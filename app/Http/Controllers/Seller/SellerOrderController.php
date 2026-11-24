<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SellerOrderController extends Controller
{
    /**
     * Tampilkan daftar pesanan toko penjual dengan tab filter status.
     */
    public function index(Request $request): View
    {
        $store = auth()->user()->store;

        // Hitung total kuantitas per status untuk badge tab
        $counts = [
            'all' => $store->orders()->count(),
            'processing' => $store->orders()->where('status', 'processing')->count(),
            'shipped' => $store->orders()->where('status', 'shipped')->count(),
            'completed' => $store->orders()->where('status', 'completed')->count(),
            'cancelled' => $store->orders()->where('status', 'cancelled')->count(),
            'pending' => $store->orders()->where('status', 'pending')->count(),
        ];

        $query = $store->orders()
            ->with(['user', 'items.product.primaryImage', 'items.variant', 'payment', 'shipment']);

        // Filter status tab
        $activeStatus = $request->query('status', 'all');
        if (in_array($activeStatus, ['processing', 'shipped', 'completed', 'cancelled', 'pending'])) {
            $query->where('status', $activeStatus);
        }

        // Pencarian invoice atau nama pembeli
        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', '%'.$search.'%')
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', '%'.$search.'%');
                    });
            });
        }

        $orders = $query->latest()->paginate(10)->withQueryString();

        return view('seller.orders.index', compact('orders', 'counts', 'activeStatus'));
    }

    /**
     * Tampilkan detail pesanan toko berdasarkan nomor invoice.
     */
    public function show(string $orderNumber): View
    {
        $store = auth()->user()->store;

        $order = $store->orders()
            ->where('invoice_number', $orderNumber)
            ->with(['user', 'items.product.primaryImage', 'items.variant', 'payments', 'shipment', 'voucher'])
            ->firstOrFail();

        return view('seller.orders.show', compact('order'));
    }

    /**
     * Penjual memproses pesanan dan menginput nomor resi pengiriman kurir.
     */
    public function ship(Request $request, string $orderNumber): RedirectResponse
    {
        $store = auth()->user()->store;

        $order = $store->orders()
            ->where('invoice_number', $orderNumber)
            ->firstOrFail();

        // Hanya pesanan berstatus processing yang bisa dikirim
        if ($order->status !== 'processing') {
            return redirect()->back()
                ->with('error', 'Hanya pesanan dengan status "Perlu Diproses" yang dapat dikirim.');
        }

        $validated = $request->validate([
            'courier_name' => ['required', 'string', 'max:50'],
            'tracking_number' => ['required', 'string', 'max:100'],
        ], [
            'courier_name.required' => 'Nama kurir wajib dipilih atau diisi.',
            'tracking_number.required' => 'Nomor resi pengiriman wajib diisi.',
        ]);

        DB::transaction(function () use ($order, $validated) {
            // Update / create record pengiriman
            $order->shipment()->updateOrCreate(
                ['order_id' => $order->id],
                [
                    'courier_name' => $validated['courier_name'],
                    'tracking_number' => $validated['tracking_number'],
                    'shipment_status' => 'shipped',
                    'shipped_at' => now(),
                    'recipient_address' => $order->shipping_address ?? 'Alamat Pengiriman',
                ]
            );

            // Perbarui status pesanan menjadi shipped
            $order->update(['status' => 'shipped']);
        });

        return redirect()->route('seller.orders.show', $order->invoice_number)
            ->with('success', 'Pesanan berhasil dikirim! Nomor resi telah tersimpan.');
    }
}
