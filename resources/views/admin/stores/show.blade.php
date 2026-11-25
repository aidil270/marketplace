<x-admin-layout title="Detail Toko">
    <x-slot name="header">
        Detail Toko: {{ $store->name }}
    </x-slot>

    <div class="max-w-4xl mx-auto space-y-6">
        @if (session('success'))
            <div class="p-4 bg-emerald-50 border-l-4 border-emerald-500 rounded-r-xl text-emerald-800 text-xs font-semibold flex items-center justify-between shadow-sm">
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-gray-100 border border-gray-200 overflow-hidden shrink-0 flex items-center justify-center font-black text-2xl text-primary">
                    @if ($store->logo)
                        <img src="{{ $store->logo }}" alt="{{ $store->name }}" class="w-full h-full object-cover">
                    @else
                        {{ strtoupper(substr($store->name, 0, 1)) }}
                    @endif
                </div>
                <div>
                    <h2 class="text-lg font-black text-gray-900">{{ $store->name }}</h2>
                    <p class="text-xs text-gray-500">{{ $store->city }} &bull; Telepon: {{ $store->phone }}</p>
                    <span class="inline-block mt-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $store->is_active ? 'bg-emerald-50 text-primary' : 'bg-red-50 text-red-600' }}">
                        Status Toko: {{ $store->is_active ? 'Aktif' : 'Ditangguhkan' }}
                    </span>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('stores.show', $store->slug) }}" target="_blank" class="px-4 py-2 border border-gray-300 hover:border-primary text-gray-700 hover:text-primary rounded-xl text-xs font-bold transition">
                    Lihat Toko Publik
                </a>

                <form method="POST" action="{{ route('admin.stores.toggle-status', $store->id) }}" onsubmit="return confirm('Ubah status operasional toko ini?');">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold transition {{ $store->is_active ? 'bg-red-50 text-red-600 hover:bg-red-100' : 'bg-emerald-50 text-primary hover:bg-emerald-100' }}">
                        {{ $store->is_active ? 'Tangguhkan Toko' : 'Aktifkan Toko' }}
                    </button>
                </form>
            </div>
        </div>

        @if (!$store->is_active && $store->suspension_reason)
            <div class="p-4 bg-red-50 border-l-4 border-red-500 rounded-r-xl text-xs text-red-800">
                <span class="font-bold block mb-1">Alasan Penangguhan Toko:</span>
                <p>{{ $store->suspension_reason }}</p>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm space-y-3">
                <h4 class="font-bold text-gray-900 text-xs uppercase tracking-wider border-b border-gray-100 pb-2">Informasi Merchant</h4>
                <div class="space-y-2 text-xs">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Pemilik Akun:</span>
                        <span class="font-bold text-gray-800">{{ $store->user->name ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Email Pemilik:</span>
                        <span class="font-medium text-gray-800">{{ $store->user->email ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Bergabung Sejak:</span>
                        <span class="font-medium text-gray-800">{{ $store->created_at->translatedFormat('d F Y') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Alamat Toko / Gudang:</span>
                        <span class="font-medium text-gray-800 text-right max-w-xs">{{ $store->address ?? '-' }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm space-y-3">
                <h4 class="font-bold text-gray-900 text-xs uppercase tracking-wider border-b border-gray-100 pb-2">Aktivitas Toko</h4>
                <div class="space-y-2 text-xs">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Total Produk Terdaftar:</span>
                        <span class="font-bold text-primary">{{ $store->products_count }} produk</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Total Transaksi Diterima:</span>
                        <span class="font-bold text-secondary">{{ $store->orders_count }} pesanan</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-start">
            <a href="{{ route('admin.stores.index') }}" class="px-5 py-2.5 rounded-xl border border-gray-300 text-gray-700 hover:bg-gray-50 text-xs font-bold transition">
                &larr; Kembali ke Daftar Toko
            </a>
        </div>
    </div>
</x-admin-layout>
