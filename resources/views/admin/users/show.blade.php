<x-admin-layout title="Detail Pengguna">
    <x-slot name="header">
        Detail Pengguna: {{ $user->name }}
    </x-slot>

    <div class="max-w-4xl mx-auto space-y-6">
        <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-full bg-slate-100 text-slate-800 font-black text-xl flex items-center justify-center shrink-0">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div>
                    <h2 class="text-lg font-black text-gray-900">{{ $user->name }}</h2>
                    <p class="text-xs text-gray-500">{{ $user->email }} &bull; Telepon: {{ $user->phone ?? '-' }}</p>
                    <span class="inline-block mt-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $user->is_active ? 'bg-emerald-50 text-primary' : 'bg-red-50 text-red-600' }}">
                        Status: {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </div>
            </div>

            @if ($user->id !== auth()->id())
                <form method="POST" action="{{ route('admin.users.toggle-status', $user->id) }}" onsubmit="return confirm('Ubah status pengguna ini?');">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold transition {{ $user->is_active ? 'bg-red-50 hover:bg-red-500 hover:text-white text-red-600' : 'bg-emerald-50 hover:bg-primary hover:text-white text-primary' }}">
                        {{ $user->is_active ? 'Nonaktifkan Akun Ini' : 'Aktifkan Akun Ini' }}
                    </button>
                </form>
            @endif
        </div>

        <!-- Detail Informasi -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm space-y-3">
                <h4 class="font-bold text-gray-900 text-xs uppercase tracking-wider border-b border-gray-100 pb-2">Informasi Akun</h4>
                <div class="space-y-2 text-xs">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Role:</span>
                        <span class="font-bold uppercase text-gray-800">{{ $user->role?->name ?? 'Pembeli' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Tanggal Registrasi:</span>
                        <span class="font-medium text-gray-800">{{ $user->created_at->translatedFormat('d F Y, H:i') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Total Transaksi Pesanan:</span>
                        <span class="font-bold text-secondary">{{ $user->orders_count }} pesanan</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Total Ulasan:</span>
                        <span class="font-medium text-gray-800">{{ $user->reviews_count }} ulasan</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm space-y-3">
                <h4 class="font-bold text-gray-900 text-xs uppercase tracking-wider border-b border-gray-100 pb-2">Informasi Toko</h4>
                @if ($user->store)
                    <div class="space-y-2 text-xs">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Nama Toko:</span>
                            <span class="font-bold text-gray-800">{{ $user->store->name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Kota Toko:</span>
                            <span class="font-medium text-gray-800">{{ $user->store->city }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Status Toko:</span>
                            <span class="font-bold {{ $user->store->is_active ? 'text-primary' : 'text-red-500' }}">
                                {{ $user->store->is_active ? 'Aktif' : 'Ditangguhkan' }}
                            </span>
                        </div>
                    </div>
                @else
                    <p class="text-xs text-gray-400">Pengguna ini belum membuka toko di TokoKu.</p>
                @endif
            </div>
        </div>

        <div class="flex justify-start">
            <a href="{{ route('admin.users.index') }}" class="px-5 py-2.5 rounded-xl border border-gray-300 text-gray-700 hover:bg-gray-50 text-xs font-bold transition">
                &larr; Kembali ke Daftar Pengguna
            </a>
        </div>
    </div>
</x-admin-layout>
