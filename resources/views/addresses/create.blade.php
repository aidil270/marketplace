<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight flex items-center gap-2">
            <a href="{{ route('addresses.index') }}" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            Tambah Alamat Baru
        </h2>
    </x-slot>

    <div class="py-8 bg-[#F8FAFC] min-h-screen">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-sm border border-gray-100">
                <form action="{{ route('addresses.store') }}" method="POST" class="space-y-5">
                    @csrf

                    <!-- Baris 1: Nama Penerima & No Telepon -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1 uppercase tracking-wider">Nama Penerima <span class="text-red-500">*</span></label>
                            <input type="text" name="recipient_name" value="{{ old('recipient_name') }}" required
                                class="w-full text-sm rounded-xl border-gray-300 focus:border-primary focus:ring-primary shadow-sm"
                                placeholder="Contoh: Budi Santoso">
                            @error('recipient_name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1 uppercase tracking-wider">Nomor Telepon <span class="text-red-500">*</span></label>
                            <input type="text" name="phone_number" value="{{ old('phone_number') }}" required
                                class="w-full text-sm rounded-xl border-gray-300 focus:border-primary focus:ring-primary shadow-sm"
                                placeholder="08123456789">
                            @error('phone_number') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Label Alamat -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1 uppercase tracking-wider">Label Alamat <span class="text-red-500">*</span></label>
                        <div class="flex items-center gap-3">
                            <label class="inline-flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="label" value="Rumah" {{ old('label', 'Rumah') === 'Rumah' ? 'checked' : '' }}
                                    class="text-primary focus:ring-primary">
                                <span class="text-sm font-semibold text-gray-700">Rumah</span>
                            </label>
                            <label class="inline-flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="label" value="Kantor" {{ old('label') === 'Kantor' ? 'checked' : '' }}
                                    class="text-primary focus:ring-primary">
                                <span class="text-sm font-semibold text-gray-700">Kantor</span>
                            </label>
                            <label class="inline-flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="label" value="Apartemen" {{ old('label') === 'Apartemen' ? 'checked' : '' }}
                                    class="text-primary focus:ring-primary">
                                <span class="text-sm font-semibold text-gray-700">Apartemen</span>
                            </label>
                        </div>
                        @error('label') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Baris 2: Provinsi, Kota, Kecamatan -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1 uppercase tracking-wider">Provinsi <span class="text-red-500">*</span></label>
                            <input type="text" name="province" value="{{ old('province') }}" required
                                class="w-full text-sm rounded-xl border-gray-300 focus:border-primary focus:ring-primary shadow-sm"
                                placeholder="DKI Jakarta">
                            @error('province') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1 uppercase tracking-wider">Kota/Kabupaten <span class="text-red-500">*</span></label>
                            <input type="text" name="city" value="{{ old('city') }}" required
                                class="w-full text-sm rounded-xl border-gray-300 focus:border-primary focus:ring-primary shadow-sm"
                                placeholder="Jakarta Selatan">
                            @error('city') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1 uppercase tracking-wider">Kecamatan</label>
                            <input type="text" name="district" value="{{ old('district') }}"
                                class="w-full text-sm rounded-xl border-gray-300 focus:border-primary focus:ring-primary shadow-sm"
                                placeholder="Tebet">
                            @error('district') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Kode Pos & Alamat Lengkap -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1 uppercase tracking-wider">Kode Pos <span class="text-red-500">*</span></label>
                        <input type="text" name="postal_code" value="{{ old('postal_code') }}" required
                            class="w-full text-sm rounded-xl border-gray-300 focus:border-primary focus:ring-primary shadow-sm"
                            placeholder="12810">
                        @error('postal_code') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1 uppercase tracking-wider">Alamat Lengkap <span class="text-red-500">*</span></label>
                        <textarea name="full_address" rows="3" required
                            class="w-full text-sm rounded-xl border-gray-300 focus:border-primary focus:ring-primary shadow-sm"
                            placeholder="Nama jalan, nomor rumah, RT/RW, kelurahan, patokan gedung...">{{ old('full_address') }}</textarea>
                        @error('full_address') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Checkbox Jadikan Alamat Utama -->
                    <div class="pt-2">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="is_primary" value="1" {{ old('is_primary') ? 'checked' : '' }}
                                class="rounded border-gray-300 text-primary focus:ring-primary">
                            <span class="text-sm font-medium text-gray-700">Atur sebagai alamat utama</span>
                        </label>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                        <a href="{{ route('addresses.index') }}" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-sm rounded-xl transition">
                            Batal
                        </a>
                        <button type="submit" class="px-6 py-2.5 bg-primary hover:bg-emerald-700 text-white font-bold text-sm rounded-xl transition shadow">
                            Simpan Alamat
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
