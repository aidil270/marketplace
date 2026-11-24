<?php

namespace App\Http\Controllers;

use App\Models\PaymentGroup;
use App\Services\DokuService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PaymentGroupController extends Controller
{
    /**
     * Tampilkan halaman "Selesaikan Pembayaran" untuk suatu payment_group.
     */
    public function show(PaymentGroup $paymentGroup): View
    {
        Gate::authorize('view', $paymentGroup);

        $paymentGroup->load([
            'orders.store',
            'orders.items.product.primaryImage',
            'orders.shipment',
        ]);

        return view('payment-groups.show', compact('paymentGroup'));
    }

    /**
     * Arahkan pembeli ke URL pembayaran DOKU Checkout Hosted Page.
     */
    public function pay(PaymentGroup $paymentGroup, DokuService $dokuService): RedirectResponse
    {
        Gate::authorize('pay', $paymentGroup);

        if ($paymentGroup->isPaid()) {
            return redirect()->route('payment-groups.show', $paymentGroup->id)
                ->with('info', 'Pembayaran untuk pesanan ini sudah berhasil.');
        }

        // Jika sudah expired, buat invoice baru dan refresh sesi DOKU
        if ($paymentGroup->isExpired()) {
            $paymentGroup->update([
                'invoice_number' => 'PAY/' . date('Ymd') . '/' . strtoupper(Str::random(6)),
                'status' => 'pending',
                'expired_at' => now()->addMinutes(1440),
                'payment_url' => null,
            ]);
        }

        // Jika payment_url belum ada, buat transaksi ke DOKU
        if (empty($paymentGroup->payment_url)) {
            $result = $dokuService->createCheckout($paymentGroup);
            if (! $result['success']) {
                return redirect()->route('payment-groups.show', $paymentGroup->id)
                    ->with('error', $result['message']);
            }
        }

        if (! empty($paymentGroup->payment_url)) {
            return redirect()->away($paymentGroup->payment_url);
        }

        return redirect()->route('payment-groups.show', $paymentGroup->id)
            ->with('error', 'URL pembayaran tidak tersedia.');
    }

    /**
     * Cek status pembayaran ke DOKU API secara manual oleh pembeli.
     */
    public function check(PaymentGroup $paymentGroup, DokuService $dokuService): RedirectResponse
    {
        Gate::authorize('check', $paymentGroup);

        $result = $dokuService->checkStatus($paymentGroup);

        if ($result['success']) {
            return redirect()->route('payment-groups.show', $paymentGroup->id)
                ->with('success', $result['message']);
        }

        return redirect()->route('payment-groups.show', $paymentGroup->id)
            ->with('info', $result['message']);
    }

    /**
     * Halaman kembali (redirect callback) setelah pembeli selesai dari DOKU.
     * Status final hanya diubah oleh Webhook atau cek status API.
     */
    public function callback(Request $request, DokuService $dokuService): View
    {
        $invoice = $request->input('invoice');
        $paymentGroup = null;

        if ($invoice) {
            $paymentGroup = PaymentGroup::where('invoice_number', $invoice)->first();
            if ($paymentGroup && $paymentGroup->isPending()) {
                // Sinkronkan status ke DOKU
                $dokuService->checkStatus($paymentGroup);
                $paymentGroup->refresh();
            }
        }

        return view('payment-groups.callback', compact('paymentGroup', 'invoice'));
    }
}
