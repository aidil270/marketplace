<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    /**
     * Tampilkan daftar seluruh pengguna dengan pencarian dan filter role.
     */
    public function index(Request $request): View
    {
        $roles = Role::orderBy('name')->get();

        $query = User::with(['role', 'store'])->withCount('orders');

        // Pencarian Nama / Email
        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%'.$search.'%')
                    ->orWhere('email', 'like', '%'.$search.'%')
                    ->orWhere('phone', 'like', '%'.$search.'%');
            });
        }

        // Filter Role
        if ($request->filled('role')) {
            $query->whereHas('role', function ($rq) use ($request) {
                $rq->where('slug', $request->role);
            });
        }

        // Filter Status Akun
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $users = $query->latest()->paginate(10)->withQueryString();

        return view('admin.users.index', compact('users', 'roles'));
    }

    /**
     * Tampilkan detail pengguna.
     */
    public function show(User $user): View
    {
        $user->load(['role', 'store', 'addresses'])
            ->loadCount(['orders', 'reviews', 'wishlists']);

        $recentOrders = $user->orders()->with('store')->latest()->take(5)->get();

        return view('admin.users.show', compact('user', 'recentOrders'));
    }

    /**
     * Aktifkan atau nonaktifkan akun pengguna dengan pengaman ketat.
     */
    public function toggleStatus(User $user): RedirectResponse
    {
        // Pengaman 1: Admin tidak bisa menonaktifkan akunnya sendiri
        if ($user->id === auth()->id()) {
            return redirect()->back()
                ->with('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri demi alasan keamanan.');
        }

        // Pengaman 2: Admin tidak bisa menonaktifkan admin terakhir yang aktif
        if ($user->isAdmin() && $user->is_active) {
            $activeAdminCount = User::whereHas('role', fn ($q) => $q->where('slug', 'admin'))
                ->where('is_active', true)
                ->count();

            if ($activeAdminCount <= 1) {
                return redirect()->back()
                    ->with('error', 'Aksi ditolak: Tidak dapat menonaktifkan satu-satunya administrator yang aktif di sistem.');
            }
        }

        $newStatus = ! $user->is_active;
        $user->update(['is_active' => $newStatus]);

        $message = $newStatus 
            ? "Akun pengguna {$user->name} berhasil diaktifkan kembali." 
            : "Akun pengguna {$user->name} berhasil dinonaktifkan.";

        return redirect()->back()->with('success', $message);
    }
}
