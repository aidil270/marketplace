<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    /**
     * Tentukan apakah user bisa melihat detail pesanan (pembeli atau penjual pemilik toko).
     */
    public function view(User $user, Order $order): bool
    {
        return $user->id === $order->user_id 
            || ($user->store && $user->store->id === $order->store_id) 
            || $user->isAdmin();
    }

    /**
     * Tentukan apakah penjual dapat memproses dan mengirim pesanan ini.
     */
    public function ship(User $user, Order $order): bool
    {
        return $user->store 
            && $user->store->id === $order->store_id 
            && $order->status === 'processing';
    }

    /**
     * Tentukan apakah user bisa membatalkan pesanan (hanya jika pending).
     */
    public function cancel(User $user, Order $order): bool
    {
        return $user->id === $order->user_id && $order->status === 'pending';
    }

    /**
     * Tentukan apakah user bisa menyelesaikan pesanan (hanya jika shipped).
     */
    public function complete(User $user, Order $order): bool
    {
        return $user->id === $order->user_id && $order->status === 'shipped';
    }
}
