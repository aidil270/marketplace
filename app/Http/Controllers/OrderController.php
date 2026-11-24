<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class OrderController extends Controller
{
    /**
     * Tampilkan riwayat pesanan pembeli dengan tab filter status.
     */
    public function index(Request $request): View
    {
        $status = $request->input('status', 'all');

        $query = Order::where('user_id', auth()->id())
            ->with([
                'store',
                'items.product.primaryImage',
                'payment',
                'shipment',
            ])
            ->latest();

        if ($status !== 'all' && in_array($status, ['pending', 'processing', 'shipped', 'completed', 'cancelled'])) {
            $query->where('status', $status);
        }

        $orders = $query->paginate(8)->withQueryString();

        // Hitung badge kuantitas per status
        $counts = [
            'all' => Order::where('user_id', auth()->id())->count(),
            'pending' => Order::where('user_id', auth()->id())->where('status', 'pending')->count(),
            'processing' => Order::where('user_id', auth()->id())->where('status', 'processing')->count(),
            'shipped' => Order::where('user_id', auth()->id())->where('status', 'shipped')->count(),
            'completed' => Order::where('user_id', auth()->id())->where('status', 'completed')->count(),
            'cancelled' => Order::where('user_id', auth()->id())->where('status', 'cancelled')->count(),
        ];

        return view('orders.index', compact('orders', 'status', 'counts'));
    }

    /**
     * Tampilkan detail pesanan pembeli.
     */
    public function show(Order $order): View
    {
        // Otorisasi melalui Policy
        Gate::authorize('view', $order);

        $order->load([
            'store',
            'voucher',
            'items.product.primaryImage',
            'items.variant',
            'payment',
            'shipment',
            'user',
        ]);

        return view('orders.show', compact('order'));
    }

    /**
     * Batalkan pesanan (hanya jika berstatus pending).
     */
    public function cancel(Order $order): RedirectResponse
    {
        Gate::authorize('cancel', $order);

        if ($order->status !== 'pending') {
            return redirect()->back()->with('error', 'Pesanan ini tidak dapat dibatalkan karena sudah diproses atau selesai.');
        }

        // Kembalikan kuota voucher jika ada
        if ($order->voucher_id && $order->voucher) {
            $order->voucher->decrement('used_count');
        }

        $order->update(['status' => 'cancelled']);

        if ($order->payment) {
            $order->payment->update(['status' => 'failed']);
        }

        // Jika semua order dalam grup dibatalkan, tandai grup pembayaran sebagai failed
        if ($order->payment_group_id && $order->paymentGroup) {
            $hasActiveOrders = Order::where('payment_group_id', $order->payment_group_id)
                ->where('status', '!=', 'cancelled')
                ->exists();

            if (! $hasActiveOrders) {
                $order->paymentGroup->update(['status' => 'failed']);
            }
        }

        return redirect()->route('orders.show', $order->id)
            ->with('success', 'Pesanan #' . $order->invoice_number . ' telah berhasil dibatalkan.');
    }

    /**
     * Pembeli menandai pesanan telah diterima (hanya jika berstatus shipped).
     */
    public function complete(Order $order): RedirectResponse
    {
        Gate::authorize('complete', $order);

        if ($order->status !== 'shipped') {
            return redirect()->back()->with('error', 'Pesanan hanya dapat diselesaikan jika barang telah berstatus dikirim.');
        }

        $order->update(['status' => 'completed']);

        if ($order->shipment) {
            $order->shipment->update([
                'shipment_status' => 'delivered',
                'delivered_at' => now(),
            ]);
        }

        return redirect()->route('orders.show', $order->id)
            ->with('success', 'Terima kasih! Pesanan telah selesai. Kamu sekarang dapat memberikan ulasan untuk produk ini.');
    }
}
