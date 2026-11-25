<?php

namespace App\Services;

use App\Models\Role;
use App\Models\Store;
use App\Models\User;
use Illuminate\Support\Str;

class StoreService
{
    /**
     * Buat slug unik untuk toko berdasarkan nama toko.
     */
    public function generateUniqueSlug(string $name): string
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug ?: 'toko';
        $counter = 1;
        while (Store::where('slug', $slug)->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        return $slug;
    }

    /**
     * Buat record toko untuk user dan pastikan role pengguna menjadi seller.
     */
    public function createStoreForUser(User $user, array $data, ?string $logoUrl = null): Store
    {
        $slug = $this->generateUniqueSlug($data['name']);

        // Pastikan role seller ada dan terpasang pada user
        $sellerRole = Role::firstOrCreate(
            ['slug' => 'seller'],
            ['name' => 'Penjual']
        );

        if ($user->role_id !== $sellerRole->id) {
            $user->update(['role_id' => $sellerRole->id]);
        }

        return $user->store()->create([
            'name' => $data['name'],
            'slug' => $slug,
            'description' => $data['description'] ?? null,
            'city' => $data['city'],
            'phone' => $data['phone'],
            'logo' => $logoUrl ?? ($data['logo'] ?? null),
            'is_active' => true,
        ]);
    }
}
