<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsSeller
{
    /**
     * Pastikan pengguna sudah login, berstatus seller, dan memiliki toko yang aktif.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->guest(route('login'));
        }

        // Jika belum memiliki toko atau belum berstatus seller
        if (! $user->isSeller() || ! $user->store) {
            return redirect()->route('seller.register')
                ->with('info', 'Silakan daftarkan toko Anda terlebih dahulu untuk mengakses Area Penjual.');
        }

        // Jika toko dinonaktifkan
        if (! $user->store->is_active) {
            return redirect()->route('home')
                ->with('error', 'Toko Anda saat ini sedang dinonaktifkan oleh administrator.');
        }

        return $next($request);
    }
}
