<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6" x-data="{
        basePrice: {{ (float) $product->price }},
        baseStock: {{ (int) $product->stock }},
        selectedVariant: null,
        currentPrice: {{ (float) $product->price }},
        currentStock: {{ (int) $product->stock }},
        quantity: 1,
        activeImage: '{{ $product->primaryImage?->image_path ?: ($product->images->first()?->image_path ?: '') }}',
        variants: {{ Js::from($product->variants) }},
        selectVariant(v) {
            if (this.selectedVariant && this.selectedVariant.id === v.id) {
                // unselect
                this.selectedVariant = null;
                this.currentPrice = this.basePrice;
                this.currentStock = this.baseStock;
            } else {
                this.selectedVariant = v;
                this.currentPrice = this.basePrice + parseFloat(v.additional_price || 0);
                this.currentStock = parseInt(v.stock || 0);
            }
            if (this.quantity > this.currentStock && this.currentStock > 0) {
                this.quantity = this.currentStock;
            }
        },
        incrementQty() {
            if (this.quantity < this.currentStock) {
                this.quantity++;
            }
        },
        decrementQty() {
            if (this.quantity > 1) {
                this.quantity--;
            }
        },
        formatRupiah(num) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(num);
        }
    }">
        <!-- Breadcrumbs -->
        <nav class="flex items-center space-x-2 text-xs text-gray-500">
            <a href="{{ route('home') }}" class="hover:text-primary transition font-medium">Beranda</a>
            <span>/</span>
            <a href="{{ route('products.category', $product->category->slug) }}" class="hover:text-primary transition font-medium">{{ $product->category->name }}</a>
            <span>/</span>
            <span class="text-gray-900 font-semibold truncate max-w-md">{{ $product->name }}</span>
        </nav>

        <!-- Main Product Card (Gallery + Info + Buy Box) -->
        <div class="bg-white rounded-2xl p-6 sm:p-8 border border-gray-100 shadow-sm grid grid-cols-1 md:grid-cols-2 gap-8 lg:gap-12 items-start">
            <!-- 1. Left: Product Photo Gallery -->
            <div class="space-y-4">
                <!-- Large Active Image Preview -->
                <div class="w-full aspect-square bg-gray-50 rounded-2xl overflow-hidden border border-gray-100 flex items-center justify-center relative shadow-inner">
                    <template x-if="activeImage">
                        <img :src="activeImage" alt="{{ $product->name }}" class="w-full h-full object-cover transition duration-300">
                    </template>
                    <template x-if="!activeImage">
                        <div class="w-24 h-24 rounded-2xl bg-gradient-to-tr from-secondary/15 to-primary/15 text-primary flex items-center justify-center">
                            <svg class="w-12 h-12 stroke-current" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        </div>
                    </template>
                    <span class="absolute top-3 left-3 bg-secondary text-white text-[10px] font-black px-2 py-0.5 rounded shadow">
                        Official Store
                    </span>
                </div>

                <!-- Thumbnails Strip -->
                @if ($product->images->isNotEmpty())
                    <div class="flex items-center gap-3 overflow-x-auto pb-2">
                        @foreach ($product->images as $img)
                            <button
                                type="button"
                                @click="activeImage = '{{ $img->image_path }}'"
                                :class="activeImage === '{{ $img->image_path }}' ? 'border-primary ring-2 ring-primary/30' : 'border-gray-200 hover:border-gray-400'"
                                class="w-16 h-16 rounded-xl overflow-hidden border-2 shrink-0 transition"
                            >
                                <img src="{{ $img->image_path }}" alt="Thumbnail" class="w-full h-full object-cover">
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- 2. Right: Product Details & Purchase Form -->
            <div class="space-y-6">
                <div>
                    <h1 class="text-xl sm:text-2xl font-black text-gray-900 leading-snug">
                        {{ $product->name }}
                    </h1>

                    <!-- Rating & Sold Summary Bar -->
                    <div class="flex items-center gap-4 mt-3 text-xs divide-x divide-gray-200">
                        <div class="flex items-center gap-1.5">
                            <span class="text-primary font-black underline">{{ $product->average_rating }}</span>
                            <div class="flex items-center text-amber-400">
                                @for ($i = 1; $i <= 5; $i++)
                                    <svg class="w-3.5 h-3.5 {{ $i <= round($product->average_rating) ? 'fill-current' : 'text-gray-200 fill-current' }}" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                @endfor
                            </div>
                        </div>
                        <div class="pl-4 text-gray-600">
                            <span class="font-bold text-gray-800">{{ $product->reviews_count }}</span> Penilaian
                        </div>
                        <div class="pl-4 text-gray-600">
                            <span class="font-bold text-gray-800">{{ $product->stock }}</span> Tersedia
                        </div>
                    </div>
                </div>

                <!-- Price Box (Shopee Gradient style) -->
                <div class="bg-emerald-50/60 border border-emerald-100 rounded-2xl p-4 sm:p-5 flex items-baseline gap-3">
                    <span class="text-2xl sm:text-3xl font-black text-primary" x-text="formatRupiah(currentPrice)">
                        Rp {{ number_format($product->price, 0, ',', '.') }}
                    </span>
                    <span class="text-xs text-gray-400 line-through">
                        Rp {{ number_format($product->price * 1.25, 0, ',', '.') }}
                    </span>
                    <span class="bg-red-500 text-white text-[10px] font-black px-2 py-0.5 rounded shadow-sm">
                        DISKON 20%
                    </span>
                </div>

                <!-- Product Variants (Alpine.js Dynamic Selector) -->
                @if ($product->variants->isNotEmpty())
                    <div class="space-y-3">
                        <label class="text-xs font-bold text-gray-700 uppercase tracking-wide block">
                            Pilihan Variasi / Model:
                        </label>
                        <div class="flex flex-wrap gap-2.5">
                            @foreach ($product->variants as $variant)
                                <button
                                    type="button"
                                    @click="selectVariant({{ Js::from($variant) }})"
                                    :class="selectedVariant && selectedVariant.id === {{ $variant->id }}
                                        ? 'border-primary bg-emerald-50 text-primary font-bold ring-2 ring-primary/20'
                                        : 'border-gray-200 bg-white text-gray-700 hover:border-gray-300 font-medium'"
                                    class="px-4 py-2 rounded-xl text-xs border transition flex items-center gap-1.5 shadow-sm"
                                >
                                    <span>{{ $variant->name }}: {{ $variant->value }}</span>
                                    @if ($variant->additional_price > 0)
                                        <span class="text-[10px] text-primary">(+Rp {{ number_format($variant->additional_price, 0, ',', '.') }})</span>
                                    @endif
                                </button>
                            @endforeach
                        </div>
                        <p x-show="selectedVariant" class="text-xs text-emerald-700 font-medium">
                            Dipilih: <span x-text="selectedVariant.name + ' - ' + selectedVariant.value" class="font-bold"></span>
                            (Stok Varian: <span x-text="selectedVariant.stock" class="font-bold"></span>)
                        </p>
                    </div>
                @endif

                <!-- Quantity & Stock Selection -->
                <div class="space-y-2">
                    <label class="text-xs font-bold text-gray-700 uppercase tracking-wide block">
                        Jumlah Pembelian:
                    </label>
                    <div class="flex items-center gap-4">
                        <div class="inline-flex items-center border border-gray-300 rounded-xl overflow-hidden bg-white shadow-sm">
                            <button
                                type="button"
                                @click="decrementQty()"
                                :disabled="quantity <= 1"
                                class="px-3.5 py-2 text-gray-600 hover:bg-gray-100 disabled:opacity-40 transition font-bold"
                            >-</button>
                            <input
                                type="number"
                                x-model.number="quantity"
                                min="1"
                                :max="currentStock"
                                class="w-14 py-1.5 text-center text-xs font-bold border-0 focus:ring-0 text-gray-800"
                            >
                            <button
                                type="button"
                                @click="incrementQty()"
                                :disabled="quantity >= currentStock"
                                class="px-3.5 py-2 text-gray-600 hover:bg-gray-100 disabled:opacity-40 transition font-bold"
                            >+</button>
                        </div>
                        <span class="text-xs text-gray-500">
                            Tersisa <span class="font-bold text-gray-800" x-text="currentStock"></span> buah
                        </span>
                    </div>
                </div>

                <!-- Action Buttons: Keranjang, Beli Sekarang, Wishlist -->
                <div class="pt-2 flex flex-col sm:flex-row items-center gap-3">
                    <!-- Form Tambah ke Keranjang -->
                    <form action="{{ route('cart.store') }}" method="POST" class="w-full sm:flex-1">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <input type="hidden" name="variant_id" :value="selectedVariant ? selectedVariant.id : ''">
                        <input type="hidden" name="quantity" :value="quantity">
                        <button
                            type="submit"
                            class="w-full py-3 px-5 rounded-xl border-2 border-primary text-primary hover:bg-emerald-50 font-extrabold text-xs uppercase tracking-wider transition flex items-center justify-center gap-2 shadow-sm"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            Masukkan Keranjang
                        </button>
                    </form>

                    <!-- Form Beli Sekarang (Langsung ke Checkout) -->
                    <form action="{{ route('cart.store') }}" method="POST" class="w-full sm:flex-1">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <input type="hidden" name="variant_id" :value="selectedVariant ? selectedVariant.id : ''">
                        <input type="hidden" name="quantity" :value="quantity">
                        <input type="hidden" name="is_buy_now" value="1">
                        <button
                            type="submit"
                            class="w-full py-3 px-5 rounded-xl bg-primary hover:bg-emerald-700 text-white font-extrabold text-xs uppercase tracking-wider transition shadow-md flex items-center justify-center"
                        >
                            Beli Sekarang
                        </button>
                    </form>

                    <!-- Tombol Favorit / Wishlist -->
                    <x-wishlist-button 
                        :productId="$product->id" 
                        :inWishlist="$inWishlist" 
                        buttonClass="p-3 rounded-xl border border-gray-200 text-gray-500 hover:text-red-500 hover:border-red-200 hover:bg-red-50 transition shrink-0" 
                        svgClass="w-5 h-5" />
                </div>

                <!-- Garansi TokoKu Shopee-style guarantee badge -->
                <div class="bg-gray-50 rounded-xl p-3.5 flex items-center gap-3 text-xs text-gray-600 border border-gray-100">
                    <svg class="w-6 h-6 text-primary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    <div>
                        <span class="font-bold text-gray-900">Garansi TokoKu</span>
                        <p class="text-[11px] text-gray-500">Dapatkan barang pesananmu atau uang kembali 100%.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Store Info Banner (Shopee Style) -->
        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-6">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-secondary to-primary text-white flex items-center justify-center font-black text-xl shadow-md shrink-0">
                    {{ strtoupper(substr($product->store->name, 0, 1)) }}
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="font-black text-gray-900 text-base">{{ $product->store->name }}</h3>
                        <span class="bg-secondary text-white text-[9px] font-bold px-1.5 py-0.5 rounded">Star Seller</span>
                    </div>
                    <p class="text-xs text-gray-500 mt-1 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                        {{ $product->store->city ?? 'Jakarta' }} &bull; Aktif 2 jam yang lalu
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <a href="#" class="px-5 py-2.5 rounded-xl border border-secondary text-secondary hover:bg-blue-50 font-bold text-xs transition shadow-sm">
                    Kunjungi Toko
                </a>
                <button type="button" class="px-5 py-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold text-xs transition shadow-sm">
                    Chat Penjual
                </button>
            </div>
        </div>

        <!-- 4. Product Details & Description Section -->
        <div class="bg-white rounded-2xl p-6 sm:p-8 border border-gray-100 shadow-sm space-y-6">
            <div class="border-b border-gray-100 pb-3">
                <h3 class="font-black text-base text-gray-900 uppercase tracking-wide">
                    Spesifikasi Produk
                </h3>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-3 gap-x-6 text-xs">
                <div class="flex">
                    <span class="w-32 text-gray-500">Kategori</span>
                    <a href="{{ route('products.category', $product->category->slug) }}" class="text-secondary font-bold hover:underline">
                        {{ $product->category->name }}
                    </a>
                </div>
                <div class="flex">
                    <span class="w-32 text-gray-500">Stok Utama</span>
                    <span class="text-gray-900 font-semibold">{{ $product->stock }} buah</span>
                </div>
                <div class="flex">
                    <span class="w-32 text-gray-500">Berat Pengiriman</span>
                    <span class="text-gray-900 font-semibold">{{ $product->weight }} gram</span>
                </div>
                <div class="flex">
                    <span class="w-32 text-gray-500">Dikirim Dari</span>
                    <span class="text-gray-900 font-semibold">{{ $product->store->city ?? 'Indonesia' }}</span>
                </div>
            </div>

            <div class="border-b border-gray-100 pt-6 pb-3">
                <h3 class="font-black text-base text-gray-900 uppercase tracking-wide">
                    Deskripsi Lengkap
                </h3>
            </div>

            <div class="text-xs text-gray-700 leading-relaxed space-y-3 whitespace-pre-line">
                {{ $product->description }}
            </div>
        </div>

        <!-- 5. Customer Reviews Section (Shopee Style) -->
        <div class="bg-white rounded-2xl p-6 sm:p-8 border border-gray-100 shadow-sm space-y-6" id="ulasan">
            <div class="border-b border-gray-100 pb-4">
                <h3 class="font-black text-base text-gray-900 uppercase tracking-wide">
                    Penilaian Produk ({{ $product->reviews_count }})
                </h3>
            </div>

            <!-- Rating Summary Box -->
            <div class="bg-emerald-50/50 border border-emerald-100 rounded-2xl p-6 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <!-- Average Rating Big Display -->
                <div class="text-center md:text-left space-y-1">
                    <div class="flex items-baseline justify-center md:justify-start gap-1">
                        <span class="text-4xl font-black text-primary">{{ $product->average_rating }}</span>
                        <span class="text-sm font-bold text-gray-400">/ 5</span>
                    </div>
                    <div class="flex items-center justify-center md:justify-start text-yellow-400">
                        @for ($i = 1; $i <= 5; $i++)
                            <svg class="w-5 h-5 {{ $i <= round($product->average_rating) ? 'fill-current' : 'text-gray-200 fill-current' }}" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        @endfor
                    </div>
                    <p class="text-xs text-gray-500 font-medium">Berdasarkan {{ $product->reviews_count }} ulasan pembeli</p>
                </div>

                <!-- Star Distribution Progress Bars -->
                <div class="flex-1 max-w-md space-y-1.5 text-xs">
                    @foreach ([5, 4, 3, 2, 1] as $star)
                        @php
                            $count = $ratingBreakdown[$star] ?? 0;
                            $percentage = $product->reviews_count > 0 ? round(($count / $product->reviews_count) * 100) : 0;
                        @endphp
                        <div class="flex items-center gap-2">
                            <span class="w-12 text-gray-600 font-bold flex items-center gap-0.5 justify-end">
                                {{ $star }} <span class="text-yellow-400">★</span>
                            </span>
                            <div class="flex-1 bg-gray-200 rounded-full h-2 overflow-hidden">
                                <div class="bg-primary h-2 rounded-full transition-all duration-300" style="width: {{ $percentage }}%"></div>
                            </div>
                            <span class="w-8 text-right text-gray-400 text-[11px]">{{ $count }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Reviews List -->
            <div class="space-y-5 divide-y divide-gray-100" x-data="{ enlargedImage: null }">
                @forelse ($reviews as $rev)
                    <div class="pt-5 first:pt-0 space-y-2">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-full bg-emerald-100 text-primary font-black text-xs flex items-center justify-center">
                                    {{ strtoupper(substr($rev->user->name ?? 'P', 0, 1)) }}
                                </div>
                                <div>
                                    <!-- Nama Pembeli Disamarkan (misal: Bu***o) -->
                                    <h4 class="text-xs font-bold text-gray-900">{{ $rev->masked_author }}</h4>
                                    <div class="flex items-center text-yellow-400">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <svg class="w-3 h-3 {{ $i <= $rev->rating ? 'fill-current' : 'text-gray-200 fill-current' }}" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        @endfor
                                    </div>
                                </div>
                            </div>
                            <span class="text-[11px] text-gray-400">{{ $rev->created_at->format('d M Y, H:i') }}</span>
                        </div>

                        <!-- Komentar Ulasan (Escaped dari XSS) -->
                        @if ($rev->comment)
                            <p class="text-xs text-gray-700 pl-10 leading-relaxed">{{ $rev->comment }}</p>
                        @endif

                        <!-- Foto Ulasan -->
                        @if ($rev->images->isNotEmpty())
                            <div class="flex items-center gap-2 pl-10 pt-1">
                                @foreach ($rev->images as $img)
                                    <button type="button" @click="enlargedImage = '{{ $img->image_path }}'" class="w-14 h-14 rounded-lg overflow-hidden border border-gray-200 bg-gray-50 hover:opacity-90 transition">
                                        <img src="{{ $img->image_path }}" class="w-full h-full object-cover">
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="py-8 text-center text-gray-400 text-xs">
                        Belum ada ulasan untuk produk ini. Jadilah yang pertama memberikan ulasan setelah menyelesaikan pesanan!
                    </div>
                @endforelse

                <!-- Modal Lightbox Pembesar Foto -->
                <div x-show="enlargedImage" x-cloak 
                     @click="enlargedImage = null"
                     class="fixed inset-0 z-50 bg-black/70 flex items-center justify-center p-4">
                    <div class="relative max-w-xl max-h-[80vh] bg-white rounded-2xl overflow-hidden shadow-2xl p-2" @click.stop>
                        <button type="button" @click="enlargedImage = null" class="absolute top-4 right-4 bg-gray-900/60 hover:bg-gray-900 text-white rounded-full p-1.5 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                        <img :src="enlargedImage" class="max-w-full max-h-[75vh] object-contain rounded-xl mx-auto">
                    </div>
                </div>
            </div>

            <!-- Pagination Ulasan -->
            <div class="pt-4 border-t border-gray-100">
                {{ $reviews->fragment('ulasan')->links() }}
            </div>
        </div>

        <!-- 6. Related Products (Produk Serupa) -->
        @if ($relatedProducts->isNotEmpty())
            <div class="space-y-4 pt-4">
                <div class="flex items-center space-x-2">
                    <div class="w-2 h-6 bg-secondary rounded-full"></div>
                    <h3 class="text-base font-black text-gray-900 uppercase tracking-wide">
                        Produk Serupa di Kategori Ini
                    </h3>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-4">
                    @foreach ($relatedProducts as $rel)
                        <a href="{{ route('products.show', $rel->slug) }}" class="group bg-white rounded-xl border border-gray-100 hover:border-emerald-300 hover:shadow-lg transition flex flex-col p-3">
                            <div class="w-full aspect-square bg-gray-50 rounded-lg flex items-center justify-center mb-2 overflow-hidden">
                                @if ($rel->primaryImage)
                                    <img src="{{ $rel->primaryImage->image_path }}" alt="{{ $rel->name }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                                @else
                                    <div class="w-12 h-12 rounded-xl bg-gradient-to-tr from-secondary/15 to-primary/15 text-primary flex items-center justify-center">
                                        <svg class="w-6 h-6 stroke-current" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                    </div>
                                @endif
                            </div>
                            <h4 class="text-xs font-medium text-gray-800 line-clamp-2 h-8 group-hover:text-primary transition leading-tight">
                                {{ $rel->name }}
                            </h4>
                            <div class="mt-2 text-xs font-black text-primary">
                                Rp {{ number_format($rel->price, 0, ',', '.') }}
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
