<x-seller-layout title="Pengaturan Toko">
    <x-slot name="header">
        Pengaturan Profil Toko
    </x-slot>

    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Notification Alert -->
        @if (session('success'))
            <div class="p-4 bg-emerald-50 border-l-4 border-emerald-500 rounded-r-xl text-emerald-800 text-xs font-semibold flex items-center justify-between shadow-sm">
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="p-4 bg-red-50 border-l-4 border-red-500 rounded-r-xl text-red-800 text-xs font-semibold">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Card Toko Overview -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="relative h-40 bg-gradient-to-r from-secondary to-primary flex items-end p-6">
                @if ($store->banner)
                    <img src="{{ $store->banner }}" alt="{{ $store->name }}" class="absolute inset-0 w-full h-full object-cover">
                    <div class="absolute inset-0 bg-black/40"></div>
                @endif
                <div class="relative z-10 flex items-center justify-between w-full">
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 rounded-2xl bg-white shadow-md border-2 border-white overflow-hidden flex items-center justify-center font-black text-secondary text-2xl">
                            @if ($store->logo)
                                <img src="{{ $store->logo }}" alt="{{ $store->name }}" class="w-full h-full object-cover">
                            @else
                                {{ strtoupper(substr($store->name, 0, 1)) }}
                            @endif
                        </div>
                        <div class="text-white drop-shadow">
                            <h2 class="text-xl font-black">{{ $store->name }}</h2>
                            <p class="text-xs text-white/90 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                {{ $store->city }} &bull; Bergabung {{ $store->created_at->format('M Y') }}
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('stores.show', $store->slug) }}" target="_blank"
                        class="bg-white/90 hover:bg-white text-gray-900 text-xs font-bold px-4 py-2 rounded-xl shadow transition flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        <span>Kunjungi Toko Publik</span>
                    </a>
                </div>
            </div>

            <!-- Edit Form -->
            <form method="POST" action="{{ route('seller.store.update') }}" enctype="multipart/form-data" class="p-6 sm:p-8 space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nama Toko -->
                    <div>
                        <label for="name" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                            Nama Toko <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" id="name" value="{{ old('name', $store->name) }}" required
                            class="w-full text-sm border-gray-300 rounded-xl focus:ring-primary focus:border-primary shadow-sm" />
                    </div>

                    <!-- Nomor Telepon -->
                    <div>
                        <label for="phone" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                            Nomor WhatsApp / Telepon <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone', $store->phone) }}" required
                            class="w-full text-sm border-gray-300 rounded-xl focus:ring-primary focus:border-primary shadow-sm" />
                    </div>

                    <!-- Kota Asal Pengiriman -->
                    <div>
                        <label for="city" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                            Kota Asal Pengiriman <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="city" id="city" value="{{ old('city', $store->city) }}" required
                            class="w-full text-sm border-gray-300 rounded-xl focus:ring-primary focus:border-primary shadow-sm" />
                    </div>

                    <!-- Slug Toko (Readonly) -->
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">
                            URL Toko Publik
                        </label>
                        <input type="text" value="{{ url('/toko/'.$store->slug) }}" readonly disabled
                            class="w-full text-sm bg-gray-50 border-gray-200 text-gray-500 rounded-xl cursor-not-allowed" />
                    </div>
                </div>

                <!-- Alamat Lengkap Toko -->
                <div>
                    <label for="address" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                        Alamat Lengkap Toko / Gudang
                    </label>
                    <textarea name="address" id="address" rows="2"
                        placeholder="Alamat fisik toko atau gudang pengiriman..."
                        class="w-full text-sm border-gray-300 rounded-xl focus:ring-primary focus:border-primary shadow-sm">{{ old('address', $store->address) }}</textarea>
                </div>

                <!-- Deskripsi Toko -->
                <div>
                    <label for="description" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                        Deskripsi Toko
                    </label>
                    <textarea name="description" id="description" rows="4"
                        placeholder="Tuliskan deskripsi lengkap profil tokomu..."
                        class="w-full text-sm border-gray-300 rounded-xl focus:ring-primary focus:border-primary shadow-sm">{{ old('description', $store->description) }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-gray-100">
                    <!-- Ganti Logo -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                            Ganti Logo Toko
                        </label>
                        <input type="file" name="logo" id="logo" accept="image/png, image/jpeg, image/webp"
                            class="block w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20 cursor-pointer" />
                        <p class="mt-1 text-[11px] text-gray-400">PNG, JPG, WEBP. Maks 2MB.</p>
                    </div>

                    <!-- Ganti Banner -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                            Ganti Banner Toko
                        </label>
                        <input type="file" name="banner" id="banner" accept="image/png, image/jpeg, image/webp"
                            class="block w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-secondary/10 file:text-secondary hover:file:bg-secondary/20 cursor-pointer" />
                        <p class="mt-1 text-[11px] text-gray-400">Rasio lebar 16:9 atau 3:1. Maks 4MB.</p>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex items-center justify-end pt-4 border-t border-gray-100">
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-primary hover:bg-emerald-700 text-white text-xs font-bold shadow-md shadow-emerald-700/20 transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Simpan Perubahan Profil
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-seller-layout>
