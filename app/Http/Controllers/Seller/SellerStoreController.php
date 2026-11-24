<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SellerStoreController extends Controller
{
    /**
     * Tampilkan halaman formulir edit profil toko penjual.
     */
    public function edit(): View
    {
        $store = auth()->user()->store;

        return view('seller.store.edit', compact('store'));
    }

    /**
     * Perbarui data profil toko penjual.
     */
    public function update(Request $request): RedirectResponse
    {
        $store = auth()->user()->store;

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('stores', 'name')->ignore($store->id)],
            'description' => ['nullable', 'string', 'max:1000'],
            'city' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'banner' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
        ], [
            'name.unique' => 'Nama toko sudah dipakai oleh penjual lain.',
            'logo.max' => 'Ukuran logo toko maksimal 2MB.',
            'banner.max' => 'Ukuran banner toko maksimal 4MB.',
        ]);

        $updateData = [
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'city' => $validated['city'],
            'phone' => $validated['phone'],
            'address' => $validated['address'] ?? null,
        ];

        // Upload logo baru
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('stores/logos', 'public');
            $updateData['logo'] = '/storage/'.$logoPath;
        }

        // Upload banner baru
        if ($request->hasFile('banner')) {
            $bannerPath = $request->file('banner')->store('stores/banners', 'public');
            $updateData['banner'] = '/storage/'.$bannerPath;
        }

        $store->update($updateData);

        return redirect()->route('seller.store.edit')
            ->with('success', 'Informasi profil toko Anda berhasil diperbarui!');
    }
}
