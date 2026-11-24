<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

class ProductPolicy
{
    /**
     * Tentukan apakah pengguna dapat melihat daftar produk seller.
     */
    public function viewAny(User $user): bool
    {
        return $user->isSeller() && $user->store !== null;
    }

    /**
     * Tentukan apakah pengguna dapat melihat detail produk seller.
     */
    public function view(User $user, Product $product): bool
    {
        return $user->store && $user->store->id === $product->store_id;
    }

    /**
     * Tentukan apakah pengguna dapat membuat produk baru.
     */
    public function create(User $user): bool
    {
        return $user->isSeller() && $user->store !== null;
    }

    /**
     * Tentukan apakah pengguna dapat memperbarui produk seller.
     */
    public function update(User $user, Product $product): bool
    {
        return $user->store && $user->store->id === $product->store_id;
    }

    /**
     * Tentukan apakah pengguna dapat menghapus produk seller.
     */
    public function delete(User $user, Product $product): bool
    {
        return $user->store && $user->store->id === $product->store_id;
    }
}
