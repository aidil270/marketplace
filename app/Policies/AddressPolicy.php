<?php

namespace App\Policies;

use App\Models\Address;
use App\Models\User;

class AddressPolicy
{
    /**
     * Tentukan apakah pengguna dapat melihat daftar alamat.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Tentukan apakah pengguna dapat melihat alamat tertentu.
     */
    public function view(User $user, Address $address): bool
    {
        return $user->id === $address->user_id;
    }

    /**
     * Tentukan apakah pengguna dapat membuat alamat baru.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Tentukan apakah pengguna dapat mengubah alamat tertentu.
     */
    public function update(User $user, Address $address): bool
    {
        return $user->id === $address->user_id;
    }

    /**
     * Tentukan apakah pengguna dapat menghapus alamat tertentu.
     */
    public function delete(User $user, Address $address): bool
    {
        return $user->id === $address->user_id;
    }

    /**
     * Tentukan apakah pengguna dapat menjadikan alamat ini sebagai alamat utama.
     */
    public function setPrimary(User $user, Address $address): bool
    {
        return $user->id === $address->user_id;
    }
}
