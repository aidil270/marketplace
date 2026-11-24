<?php

namespace App\Policies;

use App\Models\PaymentGroup;
use App\Models\User;

class PaymentGroupPolicy
{
    /**
     * Pengguna hanya dapat melihat grup pembayaran miliknya sendiri.
     */
    public function view(User $user, PaymentGroup $paymentGroup): bool
    {
        return $user->id === $paymentGroup->user_id;
    }

    /**
     * Pengguna hanya dapat membayar grup pembayaran miliknya sendiri.
     */
    public function pay(User $user, PaymentGroup $paymentGroup): bool
    {
        return $user->id === $paymentGroup->user_id;
    }

    /**
     * Pengguna hanya dapat memeriksa status grup pembayaran miliknya sendiri.
     */
    public function check(User $user, PaymentGroup $paymentGroup): bool
    {
        return $user->id === $paymentGroup->user_id;
    }
}
