<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Store;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SellerRegisterController extends Controller
{
    /**
     * Tampilkan formulir pendaftaran toko penjual baru.
     */
    public function create(): View|RedirectResponse
    {
        $user = auth()->user();

        // Jika sudah memiliki toko, langsung arahkan ke dashboard penjual
        if ($user->store) {
            return redirect()->route('seller.dashboard');
        }

        return view('seller.register');
    }

    /**
     * Simpan pendaftaran toko baru dan ubah role pengguna menjadi seller.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = auth()->user();

        if ($user->store) {
            return redirect()->route('seller.dashboard');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:stores,name'],
            'description' => ['nullable', 'string', 'max:1000'],
            'city' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:20'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ], [
            'name.unique' => 'Nama toko ini sudah digunakan. Silakan gunakan nama toko lain.',
            'logo.max' => 'Ukuran logo maksimal 2MB.',
        ]);

        DB::transaction(function () use ($user, $validated, $request) {
            // Buat slug unik
            $baseSlug = Str::slug($validated['name']);
            $slug = $baseSlug;
            $counter = 1;
            while (Store::where('slug', $slug)->exists()) {
                $slug = "{$baseSlug}-{$counter}";
                $counter++;
            }

            // Upload logo jika ada
            $logoUrl = null;
            if ($request->hasFile('logo')) {
                $path = $request->file('logo')->store('stores/logos', 'public');
                $logoUrl = '/storage/'.$path;
            }

            // Pastikan role seller ada
            $sellerRole = Role::firstOrCreate(
                ['slug' => 'seller'],
                ['name' => 'Seller']
            );

            // Update role pengguna menjadi seller
            $user->update([
                'role_id' => $sellerRole->id,
            ]);

            // Buat record toko baru
            $user->store()->create([
                'name' => $validated['name'],
                'slug' => $slug,
                'description' => $validated['description'] ?? null,
                'city' => $validated['city'],
                'phone' => $validated['phone'],
                'logo' => $logoUrl,
                'is_active' => true,
            ]);
        });

        return redirect()->route('seller.dashboard')
            ->with('success', 'Selamat! Toko Anda berhasil dibuat. Selamat berjualan di TokoKu!');
    }
}
