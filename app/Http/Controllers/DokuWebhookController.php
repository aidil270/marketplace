<?php

namespace App\Http\Controllers;

use App\Models\PaymentGroup;
use App\Services\DokuService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DokuWebhookController extends Controller
{
    /**
     * Endpoint Webhook / Notifikasi Masuk dari DOKU Payment Gateway.
     */
    public function handle(Request $request, DokuService $dokuService): JsonResponse
    {
        // 1. Verifikasi Signature SEBELUM Memproses
        if (! $dokuService->verifyNotificationSignature($request)) {
            Log::warning('DOKU Webhook Rejected: Signature tidak valid atau header autentikasi salah.');

            return response()->json([
                'status' => 'ERROR',
                'message' => 'Invalid signature.',
            ], 401);
        }

        $payload = $request->json()->all();

        // 2. Ambil Nomor Invoice dan Nominal dari Notifikasi
        $invoiceNumber = $payload['order']['invoice_number'] 
            ?? $payload['order']['id'] 
            ?? null;

        $notifAmount = isset($payload['order']['amount']) 
            ? (float) $payload['order']['amount'] 
            : null;

        if (empty($invoiceNumber)) {
            Log::warning('DOKU Webhook: Payload tidak memuat invoice_number.', ['payload' => $payload]);

            return response()->json([
                'status' => 'ERROR',
                'message' => 'Invoice number not found in payload.',
            ], 400);
        }

        // 3. Temukan PaymentGroup terkait
        $paymentGroup = PaymentGroup::where('invoice_number', $invoiceNumber)->first();

        if (! $paymentGroup) {
            Log::warning("DOKU Webhook: PaymentGroup dengan invoice '{$invoiceNumber}' tidak ditemukan.");

            return response()->json([
                'status' => 'ERROR',
                'message' => 'Payment group not found.',
            ], 404);
        }

        // 4. Validasi Nominal Pembayaran
        if ($notifAmount !== null && abs((float) $paymentGroup->total_amount - $notifAmount) > 0.01) {
            Log::error("DOKU Anomali Nominal: Tagihan Rp {$paymentGroup->total_amount} tetapi notifikasi bernilai Rp {$notifAmount}.", [
                'invoice' => $invoiceNumber,
                'expected' => $paymentGroup->total_amount,
                'received' => $notifAmount,
            ]);

            return response()->json([
                'status' => 'ERROR',
                'message' => 'Payment amount mismatch.',
            ], 400);
        }

        // 5. Cek Status Transaksi dari DOKU
        $transactionStatus = strtoupper(
            $payload['transaction']['status'] 
            ?? $payload['order']['status'] 
            ?? $payload['status'] 
            ?? ''
        );

        if (in_array($transactionStatus, ['SUCCESS', 'PAID'])) {
            // Tandai PAID secara aman, idempotent, dan kurangi stok
            $dokuService->markAsPaid($paymentGroup, $payload);
            Log::info("DOKU Webhook: Pembayaran sukses untuk invoice {$invoiceNumber}.");
        } elseif (in_array($transactionStatus, ['FAILED', 'EXPIRED'])) {
            // Tandai FAILED / EXPIRED, batalkan order pending, kembalikan kuota voucher
            $dokuService->markAsFailedOrExpired($paymentGroup, strtolower($transactionStatus), $payload);
            Log::info("DOKU Webhook: Pembayaran {$transactionStatus} untuk invoice {$invoiceNumber}.");
        } else {
            Log::info("DOKU Webhook: Menerima status {$transactionStatus} untuk invoice {$invoiceNumber}.");
        }

        // 6. Respon HTTP 200 OK sesuai yang disyaratkan DOKU
        return response()->json([
            'status' => 'OK',
            'message' => 'Notification processed successfully.',
        ], 200);
    }
}
