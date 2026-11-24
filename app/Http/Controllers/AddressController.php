<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class AddressController extends Controller
{
    /**
     * Tampilkan daftar alamat milik pengguna yang sedang login.
     */
    public function index(): View
    {
        $user = auth()->user();
        $addresses = $user->addresses()
            ->orderByDesc('is_primary')
            ->latest()
            ->get();

        return view('addresses.index', compact('addresses'));
    }

    /**
     * Tampilkan form untuk menambahkan alamat baru.
     */
    public function create(): View
    {
        return view('addresses.create');
    }

    /**
     * Simpan alamat baru ke database.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'recipient_name' => ['required', 'string', 'max:255'],
            'phone_number' => ['required', 'string', 'max:30'],
            'label' => ['required', 'string', 'max:50'],
            'province' => ['required', 'string', 'max:100'],
            'city' => ['required', 'string', 'max:100'],
            'district' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['required', 'string', 'max:10'],
            'full_address' => ['required', 'string', 'max:1000'],
            'is_primary' => ['nullable', 'boolean'],
        ]);

        $user = auth()->user();

        // Alamat pertama otomatis menjadi alamat utama
        $isFirst = ! $user->addresses()->exists();
        $isPrimary = $isFirst || $request->boolean('is_primary');

        if ($isPrimary) {
            $user->addresses()->update(['is_primary' => false]);
        }

        $validated['user_id'] = $user->id;
        $validated['is_primary'] = $isPrimary;

        Address::create($validated);

        return redirect()->route('addresses.index')
            ->with('success', 'Alamat baru berhasil ditambahkan.');
    }

    /**
     * Tampilkan form untuk mengedit alamat.
     */
    public function edit(Address $address): View
    {
        Gate::authorize('update', $address);

        return view('addresses.edit', compact('address'));
    }

    /**
     * Perbarui data alamat yang sudah ada.
     */
    public function update(Request $request, Address $address): RedirectResponse
    {
        Gate::authorize('update', $address);

        $validated = $request->validate([
            'recipient_name' => ['required', 'string', 'max:255'],
            'phone_number' => ['required', 'string', 'max:30'],
            'label' => ['required', 'string', 'max:50'],
            'province' => ['required', 'string', 'max:100'],
            'city' => ['required', 'string', 'max:100'],
            'district' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['required', 'string', 'max:10'],
            'full_address' => ['required', 'string', 'max:1000'],
            'is_primary' => ['nullable', 'boolean'],
        ]);

        $user = auth()->user();

        if ($request->boolean('is_primary')) {
            // Jadikan alamat ini utama, nonaktifkan utama di alamat lain
            $user->addresses()->where('id', '!=', $address->id)->update(['is_primary' => false]);
            $validated['is_primary'] = true;
        } else {
            // Jika alamat ini sebelumnya adalah satu-satunya alamat utama, jangan biarkan menjadi false
            $otherPrimaryExists = $user->addresses()
                ->where('id', '!=', $address->id)
                ->where('is_primary', true)
                ->exists();

            $validated['is_primary'] = $address->is_primary && ! $otherPrimaryExists;
        }

        $address->update($validated);

        return redirect()->route('addresses.index')
            ->with('success', 'Data alamat berhasil diperbarui.');
    }

    /**
     * Hapus alamat (soft delete).
     * Jika alamat utama dihapus, alamat lain terbaru otomatis menjadi utama.
     */
    public function destroy(Address $address): RedirectResponse
    {
        Gate::authorize('delete', $address);

        $user = auth()->user();
        $wasPrimary = $address->is_primary;

        $address->delete();

        // Jika alamat yang dihapus adalah alamat utama, alihkan ke alamat terbaru yang tersisa
        if ($wasPrimary) {
            $latestRemaining = $user->addresses()->latest()->first();
            if ($latestRemaining) {
                $latestRemaining->update(['is_primary' => true]);
            }
        }

        return redirect()->route('addresses.index')
            ->with('success', 'Alamat berhasil dihapus.');
    }

    /**
     * Jadikan alamat ini sebagai alamat utama (primary).
     */
    public function setPrimary(Address $address): RedirectResponse
    {
        Gate::authorize('setPrimary', $address);

        $user = auth()->user();
        $user->addresses()->where('id', '!=', $address->id)->update(['is_primary' => false]);
        $address->update(['is_primary' => true]);

        return redirect()->route('addresses.index')
            ->with('success', "'{$address->recipient_name}' berhasil dijadikan alamat utama.");
    }
}
