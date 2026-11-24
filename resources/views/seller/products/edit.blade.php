<x-seller-layout title="Edit Produk">
    <x-slot name="header">
        Edit Produk: {{ $product->name }}
    </x-slot>

    <div class="max-w-4xl mx-auto space-y-6" x-data="{
        variants: @js($product->variants->map(fn($v) => [
            'id' => $v->id,
            'name' => $v->name,
            'value' => $v->value,
            'additional_price' => (float)$v->additional_price,
            'stock' => $v->stock,
        ])),
        addVariant() {
            this.variants.push({ id: null, name: '', value: '', additional_price: 0, stock: {{ $product->stock }} });
        },
        removeVariant(index) {
            this.variants.splice(index, 1);
        }
    }">
        @if (session('success'))
            <div class="p-4 bg-emerald-50 border-l-4 border-emerald-500 rounded-r-xl text-emerald-800 text-xs font-semibold flex items-center justify-between shadow-sm">
                <span>{{ session('success') }}</span>
            </div>
        @endif

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

        <!-- Form Update Data & Variasi -->
        <form method="POST" action="{{ route('seller.products.update', $product->id) }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- 1. Informasi Utama -->
            <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm space-y-5">
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <h3 class="font-bold text-gray-900 text-sm">Informasi Utama Produk</h3>
                    <a href="{{ route('products.show', $product->slug) }}" target="_blank" class="text-xs text-secondary hover:underline font-bold flex items-center gap-1">
                        <span>Lihat di Halaman Pembeli</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    </a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- Nama Produk -->
                    <div class="md:col-span-2">
                        <label for="name" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                            Nama Produk <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" id="name" value="{{ old('name', $product->name) }}" required
                            class="w-full text-sm border-gray-300 rounded-xl focus:ring-primary focus:border-primary shadow-sm" />
                    </div>

                    <!-- Kategori -->
                    <div>
                        <label for="category_id" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                            Kategori Produk <span class="text-red-500">*</span>
                        </label>
                        <select name="category_id" id="category_id" required class="w-full text-sm border-gray-300 rounded-xl focus:ring-primary focus:border-primary shadow-sm">
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>
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
                        <input type="number" name="weight" id="weight" value="{{ old('weight', $product->weight ?? 1000) }}" min="1" required
                            class="w-full text-sm border-gray-300 rounded-xl focus:ring-primary focus:border-primary shadow-sm" />
                    </div>

                    <!-- Harga Produk -->
                    <div>
                        <label for="price" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                            Harga Produk (Rp) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="price" id="price" value="{{ old('price', (int)$product->price) }}" min="0" required
                            class="w-full text-sm border-gray-300 rounded-xl focus:ring-primary focus:border-primary shadow-sm" />
                    </div>

                    <!-- Stok Produk -->
                    <div>
                        <label for="stock" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                            Stok Utama <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="stock" id="stock" value="{{ old('stock', $product->stock) }}" min="0" required
                            class="w-full text-sm border-gray-300 rounded-xl focus:ring-primary focus:border-primary shadow-sm" />
                    </div>
                </div>

                <!-- Deskripsi Lengkap -->
                <div>
                    <label for="description" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                        Deskripsi Lengkap Produk <span class="text-red-500">*</span>
                    </label>
                    <textarea name="description" id="description" rows="5" required
                        class="w-full text-sm border-gray-300 rounded-xl focus:ring-primary focus:border-primary shadow-sm">{{ old('description', $product->description) }}</textarea>
                </div>

                <!-- Status Aktif -->
                <div class="flex items-center gap-2 pt-2">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }}
                        class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                    <label for="is_active" class="text-xs font-semibold text-gray-700">
                        Produk aktif dan dapat dibeli di katalog TokoKu
                    </label>
                </div>
            </div>

            <!-- 2. Tambah Foto Baru (Jika kuota masih tersedia < 8) -->
            <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <div>
                        <h3 class="font-bold text-gray-900 text-sm">Tambah Foto Produk Baru</h3>
                        <p class="text-xs text-gray-400">Total saat ini: {{ $product->images->count() }} / 8 foto</p>
                    </div>
                </div>

                @if ($product->images->count() < 8)
                    <div>
                        <input type="file" name="new_images[]" multiple accept="image/jpeg,image/png,image/webp"
                            class="block w-full text-xs text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20 cursor-pointer" />
                        <p class="mt-2 text-[11px] text-gray-400">Pilih foto tambahan (JPG, PNG, WEBP, maks 2MB).</p>
                    </div>
                @else
                    <div class="p-3 bg-amber-50 rounded-xl text-xs text-amber-800">
                        Batas maksimal 8 foto telah tercapai. Hapus foto lama di bagian bawah jika ingin mengunggah foto baru.
                    </div>
                @endif
            </div>

            <!-- 3. Sinkronisasi Variasi Produk -->
            <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <div>
                        <h3 class="font-bold text-gray-900 text-sm">Kelola Variasi Produk</h3>
                        <p class="text-xs text-gray-400">Ubah, tambah, atau hapus baris variasi produk</p>
                    </div>
                    <button type="button" @click="addVariant()"
                        class="px-3 py-1.5 bg-secondary hover:bg-blue-700 text-white text-xs font-bold rounded-xl transition flex items-center gap-1 shadow-sm">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>+ Tambah Variasi</span>
                    </button>
                </div>

                <div x-show="variants.length === 0" class="p-6 bg-gray-50 rounded-xl text-center text-xs text-gray-400">
                    Tidak ada variasi produk saat ini.
                </div>

                <div class="space-y-3">
                    <template x-for="(variant, index) in variants" :key="index">
                        <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 p-3 bg-gray-50 rounded-xl border border-gray-200 items-end">
                            <input type="hidden" :name="`variants[${index}][id]`" x-model="variant.id" />
                            <div>
                                <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Tipe (Nama)</label>
                                <input type="text" :name="`variants[${index}][name]`" x-model="variant.name" placeholder="Misal: Warna" required
                                    class="w-full text-xs border-gray-300 rounded-lg focus:ring-primary focus:border-primary" />
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Pilihan (Nilai)</label>
                                <input type="text" :name="`variants[${index}][value]`" x-model="variant.value" placeholder="Misal: Hitam" required
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

            <!-- Submit Button -->
            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('seller.products.index') }}" class="px-5 py-2.5 rounded-xl border border-gray-300 text-gray-700 hover:bg-gray-50 text-xs font-bold transition">
                    Kembali
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-primary hover:bg-emerald-700 text-white text-xs font-bold shadow-md shadow-emerald-700/20 transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Simpan Perubahan Produk
                </button>
            </div>
        </form>

        <!-- 4. Kelola Foto Yang Sudah Ada (Hapus / Jadikan Foto Utama) -->
        <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm space-y-4">
            <h3 class="font-bold text-gray-900 text-sm border-b border-gray-100 pb-3">Galeri Foto Produk Saat Ini</h3>

            @if ($product->images->isEmpty())
                <p class="text-xs text-gray-400">Belum ada foto yang diunggah untuk produk ini.</p>
            @else
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    @foreach ($product->images as $img)
                        <div class="relative bg-gray-50 rounded-xl border {{ $img->is_primary ? 'border-primary ring-2 ring-primary/20' : 'border-gray-200' }} p-2 flex flex-col justify-between">
                            <div class="aspect-square rounded-lg overflow-hidden bg-white mb-2">
                                <img src="{{ $img->image_path }}" alt="Foto Produk" class="w-full h-full object-cover">
                            </div>

                            @if ($img->is_primary)
                                <span class="bg-primary text-white text-[10px] font-bold py-1 px-2 rounded-md text-center block mb-2">
                                    Foto Utama
                                </span>
                            @else
                                <form method="POST" action="{{ route('seller.products.images.primary', [$product->id, $img->id]) }}" class="mb-2">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="w-full bg-gray-100 hover:bg-primary hover:text-white text-gray-700 text-[10px] font-bold py-1 px-2 rounded-md transition text-center">
                                        Jadikan Utama
                                    </button>
                                </form>
                            @endif

                            <form method="POST" action="{{ route('seller.products.images.destroy', [$product->id, $img->id]) }}" onsubmit="return confirm('Hapus foto ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full bg-red-50 hover:bg-red-500 hover:text-white text-red-600 text-[10px] font-bold py-1 px-2 rounded-md transition text-center flex items-center justify-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    <span>Hapus Foto</span>
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-seller-layout>
