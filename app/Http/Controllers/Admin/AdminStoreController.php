<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Store;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminStoreController extends Controller
{
    /**
     * Tampilkan daftar seluruh toko dengan pencarian dan filter status.
     */
    public function index(Request $request): View
    {
        $query = Store::with('user')
            ->withCount(['products', 'orders']);

        // Pencarian Nama Toko atau Nama Pemilik
        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%'.$search.'%')
                    ->orWhere('city', 'like', '%'.$search.'%')
                    ->orWhereHas('user', fn ($uq) => $uq->where('name', 'like', '%'.$search.'%'));
            });
        }

        // Filter Status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'suspended') {
                $query->where('is_active', false);
            }
        }

        $stores = $query->latest()->paginate(10)->withQueryString();

        return view('admin.stores.index', compact('stores'));
    }

    /**
     * Tampilkan detail toko.
     */
    public function show(Store $store): View
    {
        $store->load('user')
            ->loadCount(['products', 'orders']);

        $recentProducts = $store->products()->latest()->take(6)->get();
        $recentOrders = $store->orders()->with('user')->latest()->take(5)->get();

        return view('admin.stores.show', compact('store', 'recentProducts', 'recentOrders'));
    }

    /**
     * Aktifkan atau tangguhkan (suspend) toko penjual.
     */
    public function toggleStatus(Request $request, Store $store): RedirectResponse
    {
        if ($store->is_active) {
            // Tangguhkan toko
            $validated = $request->validate([
                'suspension_reason' => ['nullable', 'string', 'max:500'],
            ]);

            $store->update([
                'is_active' => false,
                'suspension_reason' => $validated['suspension_reason'] ?? 'Toko ditangguhkan oleh administrator.',
            ]);

            return redirect()->back()
                ->with('success', "Toko {$store->name} berhasil ditangguhkan.");
        } else {
            // Aktifkan kembali toko
            $store->update([
                'is_active' => true,
                'suspension_reason' => null,
            ]);

            return redirect()->back()
                ->with('success', "Toko {$store->name} berhasil diaktifkan kembali.");
        }
    }
}
