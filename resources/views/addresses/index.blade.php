<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-xl text-stone-800 leading-tight flex items-center gap-2 font-heading">
                <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Alamat Saya
            </h2>
            <a href="{{ route('addresses.create') }}" class="min-h-[44px] px-4 py-2 bg-primary hover:bg-primary-hover text-white font-bold text-xs rounded-xl transition shadow flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Tambah Alamat Baru</span>
            </a>
        </div>
    </x-slot>

    <div class="py-8 bg-surface min-h-screen">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Navigasi Profil & Alamat -->
            <div class="flex items-center gap-4 border-b border-stone-200 mb-6 pb-2 text-xs sm:text-sm font-semibold">
                <a href="{{ route('profile.edit') }}" class="text-stone-500 hover:text-primary transition py-1">
                    Profil Akun
                </a>
                <a href="{{ route('addresses.index') }}" class="text-primary border-b-2 border-primary py-1">
                    Daftar Alamat Pengiriman
                </a>
                <a href="{{ route('orders.index') }}" class="text-stone-500 hover:text-primary transition py-1">
                    Pesanan Saya
                </a>
            </div>

            <!-- Flash Alert -->
            @if (session('success'))
                <div class="mb-6 p-4 bg-teal-50 border border-teal-200 text-teal-900 rounded-xl flex items-center gap-3">
                    <svg class="w-5 h-5 text-secondary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span class="text-xs sm:text-sm font-medium">{{ session('success') }}</span>
                </div>
            @endif

            @if ($addresses->isEmpty())
                <div class="bg-white rounded-2xl shadow-sm border border-stone-200 p-12 text-center">
                    <div class="w-16 h-16 bg-orange-50 text-primary rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-stone-900 mb-1 font-heading">Belum Ada Alamat Tersimpan</h3>
                    <p class="text-xs text-stone-500 mb-6">Tambahkan alamat pengiriman Anda untuk memudahkan proses checkout pesanan.</p>
                    <a href="{{ route('addresses.create') }}" class="min-h-[44px] inline-flex items-center gap-2 px-5 py-2.5 bg-primary hover:bg-primary-hover text-white font-bold text-xs rounded-xl transition shadow">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>Tambah Alamat Sekarang</span>
                    </a>
                </div>
            @else
                <div class="space-y-4">
                    @foreach ($addresses as $address)
                        <div class="bg-white rounded-2xl p-5 border {{ $address->is_primary ? 'border-primary shadow-sm bg-orange-50/20 ring-1 ring-primary/20' : 'border-stone-200 hover:border-stone-400' }} transition">
                            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                                <div class="space-y-1.5 flex-1">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="font-bold text-stone-900 text-sm sm:text-base">{{ $address->recipient_name }}</span>
                                        <span class="text-stone-300">|</span>
                                        <span class="text-xs text-stone-600 font-medium">{{ $address->phone_number }}</span>
                                        <span class="px-2 py-0.5 text-xs font-semibold rounded bg-stone-100 text-stone-700">
                                            {{ $address->label ?? 'Rumah' }}
                                        </span>
                                        @if ($address->is_primary)
                                            <span class="px-2 py-0.5 text-xs font-bold rounded bg-primary text-white shadow-xs">
                                                Alamat Utama
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-xs sm:text-sm text-stone-700 leading-relaxed font-normal">
                                        {{ $address->full_address }}
                                    </p>
                                    <p class="text-xs text-stone-500">
                                        {{ $address->district ? $address->district . ', ' : '' }}{{ $address->city }}, {{ $address->province }} {{ $address->postal_code }}
                                    </p>
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex items-center sm:flex-col sm:items-end gap-2 shrink-0 pt-2 sm:pt-0 border-t sm:border-t-0 border-stone-100">
                                    <div class="flex items-center gap-3">
                                        <a href="{{ route('addresses.edit', $address->id) }}" class="text-xs font-bold text-secondary hover:underline py-1">
                                            Ubah
                                        </a>

                                        @if (! $address->is_primary)
                                            <span class="text-stone-300">|</span>
                                            <form action="{{ route('addresses.destroy', $address->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus alamat ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-xs font-bold text-rose-600 hover:underline py-1">
                                                    Hapus
                                                </button>
                                            </form>
                                        @endif
                                    </div>

                                    @if (! $address->is_primary)
                                        <form action="{{ route('addresses.set-primary', $address->id) }}" method="POST" class="mt-1">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="min-h-[38px] text-xs px-3 py-1.5 bg-white hover:bg-stone-50 border border-stone-300 text-stone-700 font-semibold rounded-xl transition shadow-xs">
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
