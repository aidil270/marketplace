<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-xl text-gray-800 leading-tight flex items-center gap-2">
                <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Alamat Saya
            </h2>
            <a href="{{ route('addresses.create') }}" class="px-4 py-2 bg-primary hover:bg-emerald-700 text-white font-bold text-sm rounded-xl transition shadow flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Tambah Alamat Baru</span>
            </a>
        </div>
    </x-slot>

    <div class="py-8 bg-[#F8FAFC] min-h-screen">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Navigasi Profil & Alamat -->
            <div class="flex items-center gap-4 border-b border-gray-200 mb-6 pb-2 text-sm font-semibold">
                <a href="{{ route('profile.edit') }}" class="text-gray-500 hover:text-primary transition py-1">
                    Profil Akun
                </a>
                <a href="{{ route('addresses.index') }}" class="text-primary border-b-2 border-primary py-1">
                    Daftar Alamat Pengiriman
                </a>
                <a href="{{ route('orders.index') }}" class="text-gray-500 hover:text-primary transition py-1">
                    Pesanan Saya
                </a>
            </div>

            <!-- Flash Alert -->
            @if (session('success'))
                <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl flex items-center gap-3">
                    <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span class="text-sm font-medium">{{ session('success') }}</span>
                </div>
            @endif

            @if ($addresses->isEmpty())
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
                    <div class="w-16 h-16 bg-emerald-50 text-primary rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-1">Belum Ada Alamat Tersimpan</h3>
                    <p class="text-sm text-gray-500 mb-6">Tambahkan alamat pengiriman Anda untuk memudahkan proses checkout pesanan.</p>
                    <a href="{{ route('addresses.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary hover:bg-emerald-700 text-white font-bold text-sm rounded-xl transition shadow">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>Tambah Alamat Sekarang</span>
                    </a>
                </div>
            @else
                <div class="space-y-4">
                    @foreach ($addresses as $address)
                        <div class="bg-white rounded-2xl p-5 border {{ $address->is_primary ? 'border-primary shadow-sm bg-emerald-50/20' : 'border-gray-200 hover:border-gray-300' }} transition">
                            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                                <div class="space-y-1.5 flex-1">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="font-bold text-gray-900 text-base">{{ $address->recipient_name }}</span>
                                        <span class="text-gray-400">|</span>
                                        <span class="text-sm text-gray-600 font-medium">{{ $address->phone_number }}</span>
                                        <span class="px-2 py-0.5 text-xs font-semibold rounded bg-gray-100 text-gray-700">
                                            {{ $address->label ?? 'Rumah' }}
                                        </span>
                                        @if ($address->is_primary)
                                            <span class="px-2 py-0.5 text-xs font-bold rounded bg-primary text-white shadow-sm">
                                                Alamat Utama
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-sm text-gray-700 leading-relaxed font-normal">
                                        {{ $address->full_address }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        {{ $address->district ? $address->district . ', ' : '' }}{{ $address->city }}, {{ $address->province }} {{ $address->postal_code }}
                                    </p>
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex items-center sm:flex-col sm:items-end gap-2 flex-shrink-0 pt-2 sm:pt-0 border-t sm:border-t-0 border-gray-100">
                                    <div class="flex items-center gap-3">
                                        <a href="{{ route('addresses.edit', $address->id) }}" class="text-xs font-bold text-secondary hover:underline">
                                            Ubah
                                        </a>

                                        @if (! $address->is_primary)
                                            <span class="text-gray-300">|</span>
                                            <form action="{{ route('addresses.destroy', $address->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus alamat ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-xs font-bold text-red-600 hover:underline">
                                                    Hapus
                                                </button>
                                            </form>
                                        @endif
                                    </div>

                                    @if (! $address->is_primary)
                                        <form action="{{ route('addresses.set-primary', $address->id) }}" method="POST" class="mt-1">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="text-xs px-3 py-1 bg-white hover:bg-gray-50 border border-gray-300 text-gray-700 font-semibold rounded-lg transition shadow-sm">
                                                Jadikan Alamat Utama
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
