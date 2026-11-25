<x-seller-layout title="Tambah Produk Baru">
    <x-slot name="header">
        Tambah Produk Baru
    </x-slot>

    <div class="max-w-4xl mx-auto space-y-6" x-data="{
        submitting: false,
        variants: [],
        addVariant() {
            this.variants.push({ name: '', value: '', additional_price: 0, stock: '' });
        },
        removeVariant(index) {
            this.variants.splice(index, 1);
        }
    }">
        @if ($errors->any())
            <div class="p-4 bg-red-50 border-l-4 border-red-500 rounded-r-xl text-red-800 text-xs font-semibold">
                <p class="font-bold mb-1">Terdapat kesalahan:</p>
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('seller.products.store') }}" enctype="multipart/form-data" @submit="submitting = true" class="space-y-6">
            @csrf

            <!-- 1. Informasi Utama -->
            <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm space-y-5">
                <h3 class="font-bold text-gray-900 text-sm border-b border-gray-100 pb-3">Informasi Utama Produk</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- Nama Produk -->
                    <div class="md:col-span-2">
                        <label for="name" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                            Nama Produk <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                            placeholder="Contoh: Sepatu Sneakers Pria Original Sporty"
                            class="w-full text-sm border-gray-300 rounded-xl focus:ring-primary focus:border-primary shadow-sm" />
                    </div>

                    <!-- Kategori -->
                    <div>
                        <label for="category_id" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                            Kategori Produk <span class="text-red-500">*</span>
                        </label>
                        <select name="category_id" id="category_id" required class="w-full text-sm border-gray-300 rounded-xl focus:ring-primary focus:border-primary shadow-sm">
                            <option value="">Pilih Kategori</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Berat Produk (gram) -->
                    <div>
                        <label for="weight" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                            Berat (Gram) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="weight" id="weight" value="{{ old('weight', 1000) }}" min="1" required
                            class="w-full text-sm border-gray-300 rounded-xl focus:ring-primary focus:border-primary shadow-sm" />
                        <p class="mt-1 text-[10px] text-gray-400">Default: 1000 gram (1 kg)</p>
                    </div>

                    <!-- Harga Produk -->
                    <div>
                        <label for="price" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                            Harga Produk (Rp) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="price" id="price" value="{{ old('price') }}" min="0" required
                            placeholder="Contoh: 150000"
                            class="w-full text-sm border-gray-300 rounded-xl focus:ring-primary focus:border-primary shadow-sm" />
                    </div>

                    <!-- Stok Produk -->
                    <div>
                        <label for="stock" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                            Stok Utama <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="stock" id="stock" value="{{ old('stock', 10) }}" min="0" required
                            class="w-full text-sm border-gray-300 rounded-xl focus:ring-primary focus:border-primary shadow-sm" />
                    </div>
                </div>

                <!-- Deskripsi Lengkap -->
                <div>
                    <label for="description" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                        Deskripsi Lengkap Produk <span class="text-red-500">*</span>
                    </label>
                    <textarea name="description" id="description" rows="5" required
                        placeholder="Jelaskan spesifikasi, bahan, keunggulan, dan garansi produk..."
                        class="w-full text-sm border-gray-300 rounded-xl focus:ring-primary focus:border-primary shadow-sm">{{ old('description') }}</textarea>
                </div>

                <!-- Status Aktif -->
                <div class="flex items-center gap-2 pt-2">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                        class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                    <label for="is_active" class="text-xs font-semibold text-gray-700">
                        Aktifkan produk langsung di katalog toko TokoKu
                    </label>
                </div>
            </div>

            <!-- 2. Upload Multi Foto Produk (Maks 8) -->
            <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <div>
                        <h3 class="font-bold text-gray-900 text-sm">Foto Produk (Maksimal 8 Foto)</h3>
                        <p class="text-xs text-gray-400">Foto pertama yang diunggah akan otomatis menjadi foto utama katalog</p>
                    </div>
                    <span class="text-[11px] font-bold text-secondary bg-blue-50 px-2 py-0.5 rounded-full">
                        JPG, PNG, WEBP (Maks 2MB)
                    </span>
                </div>

                <div>
                    <input type="file" name="images[]" multiple accept="image/jpeg,image/png,image/webp"
                        class="block w-full text-xs text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20 cursor-pointer" />
                    <p class="mt-2 text-[11px] text-gray-400">Pilih hingga 8 file foto sekaligus. Pastikan foto jelas dan beresolusi baik.</p>
                </div>
            </div>

            <!-- 3. Variasi Produk (Opsional - Dynamic Rows Alpine.js) -->
            <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <div>
                        <h3 class="font-bold text-gray-900 text-sm">Variasi Produk (Opsional)</h3>
                        <p class="text-xs text-gray-400">Tambahkan opsi ukuran, warna, atau model beserta harga tambahan dan stoknya</p>
                    </div>
                    <button type="button" @click="addVariant()"
                        class="px-3 py-1.5 bg-secondary hover:bg-blue-700 text-white text-xs font-bold rounded-xl transition flex items-center gap-1 shadow-sm">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>+ Tambah Variasi</span>
                    </button>
                </div>

                <div x-show="variants.length === 0" class="p-6 bg-gray-50 rounded-xl text-center text-xs text-gray-400">
                    Belum ada variasi. Jika produk memiliki ukuran atau warna berbeda, klik tombol <span class="font-bold text-secondary">+ Tambah Variasi</span> di atas.
                </div>

                <div class="space-y-3">
                    <template x-for="(variant, index) in variants" :key="index">
                        <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 p-3 bg-gray-50 rounded-xl border border-gray-200 items-end">
                            <div>
                                <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Tipe (Nama)</label>
                                <input type="text" :name="`variants[${index}][name]`" x-model="variant.name" placeholder="Misal: Warna / Ukuran" required
                                    class="w-full text-xs border-gray-300 rounded-lg focus:ring-primary focus:border-primary" />
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Pilihan (Nilai)</label>
                                <input type="text" :name="`variants[${index}][value]`" x-model="variant.value" placeholder="Misal: Hitam / XL" required
                                    class="w-full text-xs border-gray-300 rounded-lg focus:ring-primary focus:border-primary" />
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Harga Tambahan (Rp)</label>
                                <input type="number" :name="`variants[${index}][additional_price]`" x-model="variant.additional_price" min="0" placeholder="0"
                                    class="w-full text-xs border-gray-300 rounded-lg focus:ring-primary focus:border-primary" />
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="flex-1">
                                    <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Stok Variasi</label>
                                    <input type="number" :name="`variants[${index}][stock]`" x-model="variant.stock" min="0" placeholder="Stok"
                                    class="w-full text-xs border-gray-300 rounded-lg focus:ring-primary focus:border-primary" />
                                </div>
                                <button type="button" @click="removeVariant(index)" class="p-2 text-red-500 hover:bg-red-50 rounded-lg mt-5" title="Hapus Baris">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex items-center justify-end gap-3 pt-4">
                <a href="{{ route('seller.products.index') }}" class="min-h-[44px] px-5 py-2.5 rounded-xl border border-stone-300 text-stone-700 hover:bg-stone-50 text-xs font-bold transition flex items-center">
                    Batal
                </a>
                <button type="submit" :disabled="submitting" class="min-h-[44px] px-6 py-2.5 rounded-xl bg-primary hover:bg-primary-hover text-white text-xs font-bold shadow-md transition flex items-center justify-center gap-2 disabled:opacity-50">
                    <template x-if="submitting">
                        <span class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 animate-spin text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path></svg>
                            <span>Menyimpan...</span>
                        </span>
                    </template>
                    <template x-if="!submitting">
                        <span class="flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Simpan Produk</span>
                        </span>
                    </template>
                </button>
            </div>
        </form>
    </div>
</x-seller-layout>
