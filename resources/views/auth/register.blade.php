<x-guest-layout>
    <div class="mb-6 text-center">
        <h2 class="text-2xl font-bold font-heading text-stone-900">Buat Akun Baru</h2>
        <p class="text-sm text-stone-500 mt-1">Gabung bersama ribuan pengguna TokoKu</p>
    </div>

    <form method="POST" action="{{ route('register') }}" x-data="{ role: '{{ old('role', request('role', 'buyer')) }}' }" class="space-y-4">
        @csrf

        <!-- Pilihan Peran / Role Selector -->
        <div>
            <label class="block text-sm font-semibold text-stone-700 mb-2">Pilih Jenis Akun:</label>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <!-- Kartu Pembeli (Hijau #16A34A) -->
                <label 
                    class="relative flex flex-col p-3.5 border-2 rounded-xl cursor-pointer transition-all duration-150 select-none"
                    :class="role === 'buyer' 
                        ? 'border-[#16A34A] bg-emerald-50/60 ring-2 ring-[#16A34A]/20 shadow-sm' 
                        : 'border-stone-200 hover:border-stone-300 bg-white'"
                >
                    <input type="radio" name="role" value="buyer" x-model="role" class="sr-only">
                    <div class="flex items-center justify-between mb-2">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors"
                            :class="role === 'buyer' ? 'bg-[#16A34A] text-white shadow-sm' : 'bg-stone-100 text-stone-500'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        </div>
                        <div class="w-4 h-4 rounded-full border-2 flex items-center justify-center transition-colors"
                            :class="role === 'buyer' ? 'border-[#16A34A] bg-[#16A34A]' : 'border-stone-300'">
                            <div class="w-1.5 h-1.5 bg-white rounded-full" x-show="role === 'buyer'"></div>
                        </div>
                    </div>
                    <span class="font-bold text-sm" :class="role === 'buyer' ? 'text-emerald-900' : 'text-stone-800'">Daftar sebagai Pembeli</span>
                    <span class="text-xs text-stone-500 mt-1 leading-snug">Beli aneka makanan, minuman, dan jasa favorit Anda</span>
                </label>

                <!-- Kartu Penjual (Biru #2563EB) -->
                <label 
                    class="relative flex flex-col p-3.5 border-2 rounded-xl cursor-pointer transition-all duration-150 select-none"
                    :class="role === 'seller' 
                        ? 'border-[#2563EB] bg-blue-50/60 ring-2 ring-[#2563EB]/20 shadow-sm' 
                        : 'border-stone-200 hover:border-stone-300 bg-white'"
                >
                    <input type="radio" name="role" value="seller" x-model="role" class="sr-only">
                    <div class="flex items-center justify-between mb-2">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors"
                            :class="role === 'seller' ? 'bg-[#2563EB] text-white shadow-sm' : 'bg-stone-100 text-stone-500'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </div>
                        <div class="w-4 h-4 rounded-full border-2 flex items-center justify-center transition-colors"
                            :class="role === 'seller' ? 'border-[#2563EB] bg-[#2563EB]' : 'border-stone-300'">
                            <div class="w-1.5 h-1.5 bg-white rounded-full" x-show="role === 'seller'"></div>
                        </div>
                    </div>
                    <span class="font-bold text-sm" :class="role === 'seller' ? 'text-blue-900' : 'text-stone-800'">Daftar sebagai Penjual</span>
                    <span class="text-xs text-stone-500 mt-1 leading-snug">Buka toko langsung & mulai jualan di TokoKu</span>
                </label>
            </div>
            <x-input-error :messages="$errors->get('role')" class="mt-2" />
        </div>

        <!-- Kolom Tambahan Toko (Hanya tampil jika Penjual dipilih) -->
        <div x-show="role === 'seller'" x-cloak class="space-y-4 pt-3 border-t border-dashed border-stone-200">
            <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-blue-700 bg-blue-50 px-3 py-2 rounded-lg border border-blue-100">
                <svg class="w-4 h-4 text-[#2563EB]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                <span>Informasi Toko Baru</span>
            </div>

            <!-- Nama Toko -->
            <div>
                <x-input-label for="store_name">
                    <span>Nama Toko</span> <span class="text-red-500">*</span>
                </x-input-label>
                <x-text-input id="store_name" class="block mt-1 w-full" type="text" name="store_name" :value="old('store_name')" placeholder="Contoh: Dapur Nusantara / Clean Express" />
                <x-input-error :messages="$errors->get('store_name')" class="mt-1" />
            </div>

            <!-- Kota Asal Pengiriman -->
            <div>
                <x-input-label for="city">
                    <span>Kota Asal Pengiriman</span> <span class="text-red-500">*</span>
                </x-input-label>
                <x-text-input id="city" class="block mt-1 w-full" type="text" name="city" :value="old('city')" placeholder="Contoh: Jakarta Selatan, Surabaya, Bandung" />
                <x-input-error :messages="$errors->get('city')" class="mt-1" />
            </div>

            <!-- Nomor Telepon / WhatsApp -->
            <div>
                <x-input-label for="phone">
                    <span>Nomor Telepon / WhatsApp</span> <span class="text-red-500">*</span>
                </x-input-label>
                <x-text-input id="phone" class="block mt-1 w-full" type="text" name="phone" :value="old('phone')" placeholder="Contoh: 081234567890" />
                <x-input-error :messages="$errors->get('phone')" class="mt-1" />
            </div>
        </div>

        <div class="pt-2 border-t border-stone-200">
            <span class="text-xs font-bold uppercase tracking-wider text-stone-500">Informasi Akun Pribadi</span>
        </div>

        <!-- Name -->
        <div>
            <x-input-label for="name">
                <span>Nama Lengkap</span> <span class="text-red-500">*</span>
            </x-input-label>
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Nama Anda" />
            <x-input-error :messages="$errors->get('name')" class="mt-1" />
        </div>

        <!-- Email Address -->
        <div>
            <x-input-label for="email">
                <span>Alamat Email</span> <span class="text-red-500">*</span>
            </x-input-label>
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="nama@email.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password">
                <span>Kata Sandi</span> <span class="text-red-500">*</span>
            </x-input-label>
            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" placeholder="Minimal 8 karakter" />
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        <!-- Confirm Password -->
        <div>
            <x-input-label for="password_confirmation">
                <span>Konfirmasi Kata Sandi</span> <span class="text-red-500">*</span>
            </x-input-label>
            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" placeholder="Ulangi kata sandi" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
        </div>

        <div class="pt-3">
            <button 
                type="submit" 
                class="w-full py-3 px-4 text-white font-semibold rounded-xl shadow-md transition duration-150 flex items-center justify-center gap-2 min-h-[44px]"
                :class="role === 'seller' ? 'bg-[#2563EB] hover:bg-blue-700 shadow-blue-500/20' : 'bg-[#16A34A] hover:bg-emerald-700 shadow-emerald-500/20'"
            >
                <span x-text="role === 'seller' ? 'Daftar & Buka Toko' : 'Daftar Sekarang'"></span>
            </button>
        </div>

        <div class="text-center pt-2">
            <span class="text-sm text-stone-500">Sudah punya akun?</span>
            <a class="text-sm font-semibold text-emerald-600 hover:text-emerald-700 ml-1 underline" href="{{ route('login') }}">
                Masuk di sini
            </a>
        </div>
    </form>
</x-guest-layout>
