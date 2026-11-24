<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">
            {{ __('Buka Toko Gratis di TokoKu') }}
        </h2>
    </x-slot>

    <div class="py-8 bg-slate-50 min-h-screen">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header Card Banner -->
            <div class="bg-gradient-to-r from-secondary to-primary rounded-2xl p-6 sm:p-8 text-white shadow-lg mb-8 relative overflow-hidden">
                <div class="relative z-10">
                    <span class="inline-block bg-white/20 backdrop-blur text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider mb-2">
                        Pusat Penjual TokoKu
                    </span>
                    <h1 class="text-2xl sm:text-3xl font-black mb-2">Mulai Langkah Sukses Bisnismu</h1>
                    <p class="text-white/80 text-sm max-w-xl">
                        Daftarkan toko Anda dalam hitungan menit dan jangkau jutaan pembeli setia di TokoKu dengan mudah, aman, dan tanpa biaya pendaftaran.
                    </p>
                </div>
                <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
            </div>

            <!-- Form Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="font-bold text-gray-900 text-base">Informasi Dasar Toko</h3>
                    <p class="text-xs text-gray-500">Lengkapi data toko Anda dengan benar agar memudahkan pembeli bertransaksi.</p>
                </div>

                @if ($errors->any())
                    <div class="m-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-lg text-red-700 text-sm">
                        <p class="font-bold mb-1">Terdapat kesalahan dalam pengisian formulir:</p>
                        <ul class="list-disc list-inside space-y-1 text-xs">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('seller.register.store') }}" enctype="multipart/form-data" class="p-6 sm:p-8 space-y-6">
                    @csrf

                    <!-- Nama Toko -->
                    <div>
                        <label for="name" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                            Nama Toko <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                            placeholder="Contoh: Toko Elektronik Jaya"
                            class="w-full text-sm border-gray-300 rounded-lg focus:ring-primary focus:border-primary shadow-sm" />
                        <p class="mt-1 text-[11px] text-gray-500">Nama toko unik yang akan tampil di halaman produk dan toko publik Anda.</p>
                    </div>

                    <!-- Kota / Domisili Toko -->
                    <div>
                        <label for="city" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                            Kota / Kabupaten Pengiriman <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="city" id="city" value="{{ old('city') }}" required
                            placeholder="Contoh: Jakarta Selatan, Surabaya, Bandung"
                            class="w-full text-sm border-gray-300 rounded-lg focus:ring-primary focus:border-primary shadow-sm" />
                        <p class="mt-1 text-[11px] text-gray-500">Lokasi asal toko untuk acuan ongkos kirim ke pembeli.</p>
                    </div>

                    <!-- Nomor Telepon / WhatsApp -->
                    <div>
                        <label for="phone" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                            Nomor WhatsApp / Telepon Toko <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone', auth()->user()->phone) }}" required
                            placeholder="Contoh: 081234567890"
                            class="w-full text-sm border-gray-300 rounded-lg focus:ring-primary focus:border-primary shadow-sm" />
                    </div>

                    <!-- Deskripsi Toko -->
                    <div>
                        <label for="description" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                            Deskripsi Singkat Toko <span class="text-gray-400 font-normal">(Opsional)</span>
                        </label>
                        <textarea name="description" id="description" rows="3"
                            placeholder="Jelaskan jenis produk yang Anda jual dan komitmen pelayanan toko Anda..."
                            class="w-full text-sm border-gray-300 rounded-lg focus:ring-primary focus:border-primary shadow-sm">{{ old('description') }}</textarea>
                    </div>

                    <!-- Upload Logo -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                            Logo Toko <span class="text-gray-400 font-normal">(Opsional)</span>
                        </label>
                        <div class="mt-1 flex items-center gap-4">
                            <input type="file" name="logo" id="logo" accept="image/png, image/jpeg, image/webp"
                                class="block w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20 cursor-pointer" />
                        </div>
                        <p class="mt-1 text-[11px] text-gray-500">Format: PNG, JPG, WEBP. Maksimal 2MB. Disarankan rasio 1:1 (persegi).</p>
                    </div>

                    <!-- Syarat & Ketentuan -->
                    <div class="pt-4 border-t border-gray-100">
                        <div class="flex items-start">
                            <input id="agree" name="agree" type="checkbox" required class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded mt-0.5">
                            <label for="agree" class="ml-2 block text-xs text-gray-600">
                                Saya menyetujui <span class="font-semibold text-secondary">Syarat & Ketentuan Penjual TokoKu</span> serta bersedia mematuhi aturan perdagangan online yang berlaku.
                            </label>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-end gap-3 pt-2">
                        <a href="{{ route('home') }}" class="px-5 py-2.5 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 text-xs font-bold transition">
                            Batal
                        </a>
                        <button type="submit" class="px-6 py-2.5 rounded-lg bg-primary hover:bg-emerald-700 text-white text-xs font-bold shadow-md shadow-emerald-700/20 transition flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Daftarkan Toko Sekarang
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
