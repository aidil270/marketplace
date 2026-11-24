<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymentGroup;
use App\Models\Product;
use App\Models\ProductVariant;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class DokuService
{
    private string $clientId;
    private string $secretKey;
    private string $baseUrl;

    public function __construct()
    {
        $this->clientId = (string) config('doku.client_id', '');
        $this->secretKey = (string) config('doku.secret_key', '');
        $rawBaseUrl = config('doku.base_url');
        $this->baseUrl = rtrim(! empty($rawBaseUrl) ? (string) $rawBaseUrl : 'https://api-sandbox.doku.com', '/');
    }

    /**
     * Periksa apakah kredensial DOKU telah terkonfigurasi.
     */
    public function isConfigured(): bool
    {
        return ! empty($this->clientId) && ! empty($this->secretKey);
    }

    /**
     * Buat request transaksi Checkout Hosted Payment Page ke API DOKU.
     *
     * @return array{success: bool, payment_url: ?string, message: string}
     */
    public function createCheckout(PaymentGroup $paymentGroup): array
    {
        // 1. Validasi Kredensial
        if (! $this->isConfigured()) {
            Log::warning('DOKU Payment Gateway: Client ID atau Secret Key belum dikonfigurasi di file .env.');

            return [
                'success' => false,
                'payment_url' => null,
                'message' => 'Kredensial DOKU Payment Gateway belum dikonfigurasi pada server (.env). Silakan hubungi admin toko.',
            ];
        }

        $paymentGroup->load(['user', 'orders.items.product']);
        $user = $paymentGroup->user;

        // Ambil alamat pengiriman dari salah satu order di grup
        $firstOrder = $paymentGroup->orders->first();
        $shippingText = $firstOrder?->shipping_address ?? '';

        // Kumpulkan line items dari semua order di grup
        $lineItems = [];
        foreach ($paymentGroup->orders as $order) {
            foreach ($order->items as $item) {
                $lineItems[] = [
                    'name' => Str::limit($item->product_name, 50, ''),
                    'price' => (int) round($item->product_price),
                    'quantity' => (int) $item->quantity,
                ];
            }
            if ($order->shipping_cost > 0) {
                $lineItems[] = [
                    'name' => 'Ongkos Kirim (' . ($order->store->name ?? 'Toko') . ')',
                    'price' => (int) round($order->shipping_cost),
                    'quantity' => 1,
                ];
            }
            if ($order->discount_amount > 0) {
                $lineItems[] = [
                    'name' => 'Diskon Voucher (' . ($order->store->name ?? 'Toko') . ')',
                    'price' => -1 * (int) round($order->discount_amount),
                    'quantity' => 1,
                ];
            }
        }

        // Siapkan Payload Request Checkout
        $requestTarget = '/checkout/v1/payment';
        $requestId = (string) Str::uuid();
        $timestamp = now()->setTimezone('UTC')->format('Y-m-d\TH:i:s\Z');

        $payload = [
            'order' => [
                'invoice_number' => $paymentGroup->invoice_number,
                'amount' => (int) round($paymentGroup->total_amount),
                'callback_url' => route('payment-groups.callback', ['invoice' => $paymentGroup->invoice_number]),
                'auto_redirect' => true,
                'line_items' => $lineItems,
            ],
            'payment' => [
                'payment_due_date' => 1440, // 24 jam (dalam satuan menit)
            ],
            'customer' => [
                'id' => (string) $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'address' => Str::limit($shippingText, 200, ''),
            ],
        ];

        $jsonBody = json_encode($payload, JSON_UNESCAPED_SLASHES);
        $signature = $this->generateSignature($requestId, $timestamp, $requestTarget, $jsonBody);

        try {
            $response = Http::withHeaders([
                'Client-Id' => $this->clientId,
                'Request-Id' => $requestId,
                'Request-Timestamp' => $timestamp,
                'Signature' => $signature,
                'Content-Type' => 'application/json',
            ])->timeout(15)->post($this->baseUrl . $requestTarget, $payload);

            $responseData = $response->json();

            if ($response->successful()) {
                $paymentUrl = $responseData['payment']['url'] 
                    ?? $responseData['response']['payment_url'] 
                    ?? null;
                $referenceNumber = $responseData['order']['invoice_number'] 
                    ?? $responseData['response']['reference_number'] 
                    ?? $paymentGroup->invoice_number;

                // Simpan URL dan referensi ke payment group
                $paymentGroup->update([
                    'payment_url' => $paymentUrl,
                    'reference_number' => $referenceNumber,
                    'expired_at' => now()->addMinutes(1440),
                    'gateway_response' => $responseData,
                ]);

                // Selaraskan juga payment records di setiap order
                foreach ($paymentGroup->orders as $order) {
                    $order->payment()?->update([
                        'payment_url' => $paymentUrl,
                        'reference_number' => $referenceNumber ? "{$referenceNumber}-{$order->id}" : null,
                        'expired_at' => now()->addMinutes(1440),
                        'gateway_response' => $responseData,
                    ]);
                }

                return [
                    'success' => true,
                    'payment_url' => $paymentUrl,
                    'message' => 'Sesi pembayaran DOKU berhasil dibuat.',
                ];
            }

            $errorMessage = $responseData['error']['message'] 
                ?? $responseData['message'] 
                ?? ('Gagal membuat transaksi di DOKU: HTTP ' . $response->status());

            Log::error('DOKU Checkout Error: ' . $errorMessage, [
                'status' => $response->status(),
                'response' => $responseData,
            ]);

            return [
                'success' => false,
                'payment_url' => null,
                'message' => $errorMessage,
            ];
        } catch (Exception $e) {
            Log::error('DOKU Checkout Exception: ' . $e->getMessage());

            return [
                'success' => false,
                'payment_url' => null,
                'message' => 'Koneksi ke gateway pembayaran DOKU mengalami gangguan. Silakan coba sesaat lagi.',
            ];
        }
    }

    /**
     * Periksa status transaksi ke API DOKU (GET /orders/v1/status/{invoice_number}).
     */
    public function checkStatus(PaymentGroup $paymentGroup): array
    {
        if (! $this->isConfigured()) {
            return [
                'success' => false,
                'status' => $paymentGroup->status,
                'message' => 'Kredensial DOKU belum dikonfigurasi.',
            ];
        }

        $requestTarget = '/orders/v1/status/' . $paymentGroup->invoice_number;
        $requestId = (string) Str::uuid();
        $timestamp = now()->setTimezone('UTC')->format('Y-m-d\TH:i:s\Z');

        $signature = $this->generateSignature($requestId, $timestamp, $requestTarget, null);

        try {
            $response = Http::withHeaders([
                'Client-Id' => $this->clientId,
                'Request-Id' => $requestId,
                'Request-Timestamp' => $timestamp,
                'Signature' => $signature,
            ])->timeout(15)->get($this->baseUrl . $requestTarget);

            if ($response->successful()) {
                $data = $response->json();
                $transactionStatus = strtoupper(
                    $data['transaction']['status'] 
                    ?? $data['order']['status'] 
                    ?? $data['status'] 
                    ?? ''
                );

                if (in_array($transactionStatus, ['SUCCESS', 'PAID'])) {
                    $this->markAsPaid($paymentGroup, $data);
                    return ['success' => true, 'status' => 'paid', 'message' => 'Pembayaran telah berhasil dikonfirmasi.'];
                }

                if (in_array($transactionStatus, ['FAILED', 'EXPIRED'])) {
                    $this->markAsFailedOrExpired($paymentGroup, strtolower($transactionStatus), $data);
                    return ['success' => true, 'status' => strtolower($transactionStatus), 'message' => 'Status pembayaran: ' . $transactionStatus];
                }

                return ['success' => true, 'status' => $paymentGroup->status, 'message' => 'Menunggu pembayaran pembeli.'];
            }

            return [
                'success' => false,
                'status' => $paymentGroup->status,
                'message' => 'Gagal memeriksa status dari DOKU (HTTP ' . $response->status() . ').',
            ];
        } catch (Exception $e) {
            Log::error('DOKU Check Status Exception: ' . $e->getMessage());

            return [
                'success' => false,
                'status' => $paymentGroup->status,
                'message' => 'Gangguan jaringan saat memeriksa status ke DOKU.',
            ];
        }
    }

    /**
     * Verifikasi signature notifikasi webhook masuk dari DOKU.
     */
    public function verifyNotificationSignature(Request $request): bool
    {
        if (! $this->isConfigured()) {
            Log::warning('DOKU Webhook: Kredensial tidak ditemukan pada .env saat menerima notifikasi.');
            return false;
        }

        $clientId = (string) $request->header('Client-Id');
        $requestId = (string) $request->header('Request-Id');
        $timestamp = (string) $request->header('Request-Timestamp');
        $signature = (string) $request->header('Signature');

        if (empty($clientId) || empty($requestId) || empty($timestamp) || empty($signature)) {
            Log::warning('DOKU Webhook: Header autentikasi tidak lengkap.');
            return false;
        }

        // Pastikan Client ID cocok
        if ($clientId !== $this->clientId) {
            Log::warning('DOKU Webhook: Client ID tidak cocok.');
            return false;
        }

        // Cek timestamp tidak terlalu usang (maks 15 menit)
        try {
            $parsedTime = Carbon::parse($timestamp);
            if (abs(now()->diffInMinutes($parsedTime)) > 15) {
                Log::warning('DOKU Webhook: Timestamp kedaluwarsa (' . $timestamp . ').');
                return false;
            }
        } catch (Exception $e) {
            Log::warning('DOKU Webhook: Format timestamp tidak valid.');
            return false;
        }

        // Target URL persis sesuai yang didaftarkan (misal /api/doku/notification)
        $requestTarget = '/' . ltrim($request->path(), '/');
        $rawBody = $request->getContent();

        $expectedSignature = $this->generateSignature($requestId, $timestamp, $requestTarget, $rawBody);

        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Generate Signature HMAC-SHA256 sesuai standar resmi DOKU / Jokul.
     */
    public function generateSignature(string $requestId, string $timestamp, string $requestTarget, ?string $rawBody = null): string
    {
        $signatureComponent = "Client-Id:" . $this->clientId . "\n"
            . "Request-Id:" . $requestId . "\n"
            . "Request-Timestamp:" . $timestamp . "\n"
            . "Request-Target:" . $requestTarget;

        if ($rawBody !== null && $rawBody !== '') {
            $digest = base64_encode(hash('sha256', $rawBody, true));
            $signatureComponent .= "\nDigest:" . $digest;
        }

        $hashHmac = hash_hmac('sha256', $signatureComponent, $this->secretKey, true);

        return "HMACSHA256=" . base64_encode($hashHmac);
    }

    /**
     * Tandai payment_group dan orders terkait sebagai PAID secara idempotent & kurangi stok produk.
     */
    public function markAsPaid(PaymentGroup $paymentGroup, array $gatewayPayload = []): void
    {
        DB::transaction(function () use ($paymentGroup, $gatewayPayload) {
            // Lock payment_group untuk mencegah race condition / duplicate processing
            $lockedGroup = PaymentGroup::where('id', $paymentGroup->id)->lockForUpdate()->first();

            if (! $lockedGroup || $lockedGroup->status === 'paid') {
                return; // Sudah diproses sebelumnya
            }

            $lockedGroup->update([
                'status' => 'paid',
                'paid_at' => now(),
                'gateway_response' => $gatewayPayload ?: $lockedGroup->gateway_response,
            ]);

            // Ambil semua order dalam grup dan lock
            $orders = Order::where('payment_group_id', $lockedGroup->id)->lockForUpdate()->get();

            foreach ($orders as $order) {
                // Update status pesanan ke processing
                $order->update(['status' => 'processing']);

                // Update status pembayaran terkait
                $order->payment()?->update([
                    'status' => 'paid',
                    'paid_at' => now(),
                    'gateway_response' => $gatewayPayload ?: null,
                ]);

                // Kurangi stok untuk tiap order item dengan aman (lockForUpdate)
                $orderItems = OrderItem::where('order_id', $order->id)->get();
                foreach ($orderItems as $item) {
                    if ($item->product_variant_id) {
                        $variant = ProductVariant::where('id', $item->product_variant_id)->lockForUpdate()->first();
                        if ($variant) {
                            if ($variant->stock < $item->quantity) {
                                Log::warning("Peringatan Stok Kurang (Varian): Varian #{$variant->id} tersisa {$variant->stock}, dipesan {$item->quantity}. Stok tetap disesuaikan.");
                            }
                            $variant->decrement('stock', min($variant->stock, $item->quantity));
                        }
                    }

                    $product = Product::where('id', $item->product_id)->lockForUpdate()->first();
                    if ($product) {
                        if ($product->stock < $item->quantity) {
                            Log::warning("Peringatan Stok Kurang (Produk): Produk #{$product->id} tersisa {$product->stock}, dipesan {$item->quantity}. Stok tetap disesuaikan.");
                        }
                        $product->decrement('stock', min($product->stock, $item->quantity));
                    }
                }
            }
        });
    }

    /**
     * Tandai payment_group dan order terkait sebagai FAILED / EXPIRED & kembalikan kuota voucher.
     */
    public function markAsFailedOrExpired(PaymentGroup $paymentGroup, string $status = 'failed', array $gatewayPayload = []): void
    {
        DB::transaction(function () use ($paymentGroup, $status, $gatewayPayload) {
            $lockedGroup = PaymentGroup::where('id', $paymentGroup->id)->lockForUpdate()->first();

            if (! $lockedGroup || $lockedGroup->status === 'paid') {
                return;
            }

            $lockedGroup->update([
                'status' => $status,
                'gateway_response' => $gatewayPayload ?: $lockedGroup->gateway_response,
            ]);

            $orders = Order::where('payment_group_id', $lockedGroup->id)->lockForUpdate()->get();

            foreach ($orders as $order) {
                if ($order->status === 'pending') {
                    $order->update(['status' => 'cancelled']);

                    $order->payment()?->update([
                        'status' => $status,
                        'gateway_response' => $gatewayPayload ?: null,
                    ]);

                    // Kembalikan kuota voucher jika voucher digunakan
                    if ($order->voucher_id && $order->voucher) {
                        $order->voucher->decrement('used_count');
                    }
                }
            }
        });
    }
}
