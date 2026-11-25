<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Services\StoreService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // Default role jika tidak disediakan dalam request
        if (! $request->has('role')) {
            $request->merge(['role' => 'buyer']);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'string', Rule::in(['buyer', 'seller'])],
            'store_name' => ['required_if:role,seller', 'nullable', 'string', 'max:100', 'unique:stores,name'],
            'city' => ['required_if:role,seller', 'nullable', 'string', 'max:100'],
            'phone' => ['required_if:role,seller', 'nullable', 'string', 'max:20'],
        ], [
            'role.in' => 'Pilihan peran tidak valid. Hanya dapat mendaftar sebagai Pembeli atau Penjual.',
            'store_name.required_if' => 'Nama toko wajib diisi untuk pendaftaran penjual.',
            'store_name.unique' => 'Nama toko ini sudah digunakan. Silakan gunakan nama toko lain.',
            'city.required_if' => 'Kota asal pengiriman wajib diisi untuk pendaftaran penjual.',
            'phone.required_if' => 'Nomor telepon/WhatsApp wajib diisi untuk pendaftaran penjual.',
        ]);

        $roleSlug = $validated['role'];
        $roleModel = Role::firstOrCreate(
            ['slug' => $roleSlug],
            ['name' => $roleSlug === 'seller' ? 'Penjual' : 'Pembeli']
        );

        $user = DB::transaction(function () use ($validated, $roleModel, $roleSlug) {
            $user = new User([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);
            $user->role_id = $roleModel->id;
            if ($roleSlug === 'seller' && ! empty($validated['phone'])) {
                $user->phone = $validated['phone'];
            }
            $user->save();

            if ($roleSlug === 'seller') {
                app(StoreService::class)->createStoreForUser($user, [
                    'name' => $validated['store_name'],
                    'city' => $validated['city'],
                    'phone' => $validated['phone'],
                ]);
            }

            return $user;
        });

        event(new Registered($user));

        Auth::login($user);

        if ($user->role?->slug === 'seller') {
            return redirect()->route('seller.dashboard');
        }

        return redirect(route('dashboard', absolute: false));
    }
}
