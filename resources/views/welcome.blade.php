<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-8">
        <!-- 1. Hero Section: Banner Slider & Promo Highlights -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4" x-data="{
            activeSlide: 0,
            slidesCount: {{ $banners->count() ?: 1 }},
            paused: false,
            next() {
                this.activeSlide = (this.activeSlide + 1) % this.slidesCount;
            },
            prev() {
                this.activeSlide = (this.activeSlide - 1 + this.slidesCount) % this.slidesCount;
            },
            init() {
                if (this.slidesCount > 1) {
                    setInterval(() => {
                        if (!this.paused) {
                            this.next();
                        }
                    }, 5000);
                }
            }
        }" @mouseenter="paused = true" @mouseleave="paused = false">
            <!-- Main Slider (Left 2 cols) -->
            <div class="lg:col-span-2 relative rounded-2xl overflow-hidden shadow-md h-64 sm:h-80 bg-stone-900 group">
                @forelse ($banners as $index => $banner)
                    <div
                        x-show="activeSlide === {{ $index }}"
                        x-transition:enter="transition ease-out duration-500 transform"
                        x-transition:enter-start="opacity-0 scale-98"
                        x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-300 transform"
                        x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-98"
                        class="absolute inset-0 bg-cover bg-center p-6 sm:p-10 flex flex-col justify-between text-white"
                        style="background-image: linear-gradient(to right, rgba(15, 118, 110, 0.92), rgba(234, 88, 12, 0.82)), url('{{ $banner->image_path }}');"
                    >
                        <div>
                            <span class="inline-block bg-white/20 backdrop-blur-md px-3 py-1 rounded-full text-xs font-bold tracking-wider mb-2.5 text-orange-100 border border-white/20">
                                PROMO SPESIAL
                            </span>
                            <h2 class="text-2xl sm:text-4xl font-extrabold tracking-tight leading-tight max-w-md font-heading">
                                {{ $banner->title }}
                            </h2>
                            <p class="text-white/90 text-xs sm:text-sm mt-2 max-w-md line-clamp-2">
                                Belanja kuliner lezat & pesan jasa terpercaya, bergaransi resmi hanya di TokoKu.
                            </p>
                        </div>
                        <div class="flex items-center gap-3">
                            <a href="{{ $banner->url ?: route('products.index') }}" class="inline-flex items-center px-5 py-2.5 bg-white text-stone-900 hover:text-primary hover:bg-stone-50 rounded-xl font-bold text-xs sm:text-sm shadow-md transition-all duration-200 transform hover:-translate-y-0.5 min-h-[44px]">
                                Belanja Sekarang
                                <svg class="w-4 h-4 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="absolute inset-0 bg-gradient-to-r from-secondary to-primary p-6 sm:p-10 flex flex-col justify-between text-white">
                        <div>
                            <span class="inline-block bg-white/20 px-3 py-1 rounded-full text-xs font-bold text-orange-200">MARKETPLACE PILIHAN</span>
                            <h2 class="text-2xl sm:text-4xl font-extrabold mt-2 font-heading">Selamat Datang di TokoKu</h2>
                            <p class="text-white/90 text-xs sm:text-sm mt-1">Platform kuliner & layanan jasa terbaik dari penjual terpercaya di sekitarmu.</p>
                        </div>
                        <a href="{{ route('products.index') }}" class="inline-flex items-center px-5 py-2.5 bg-white text-stone-900 font-bold text-xs sm:text-sm rounded-xl w-max hover:text-primary transition min-h-[44px]">
                            Eksplor Sekarang
                        </a>
                    </div>
                @endforelse

                <!-- Slider Arrows (Visible on Desktop Hover) -->
                @if ($banners->count() > 1)
                    <button 
                        type="button" 
                        @click="prev()" 
                        class="absolute left-3 top-1/2 -translate-y-1/2 w-9 h-9 rounded-full bg-stone-900/40 hover:bg-stone-900/70 text-white flex items-center justify-center backdrop-blur-sm opacity-0 group-hover:opacity-100 transition-opacity duration-200 focus:opacity-100 z-10"
                        aria-label="Slide Sebelumnya"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    <button 
                        type="button" 
                        @click="next()" 
                        class="absolute right-3 top-1/2 -translate-y-1/2 w-9 h-9 rounded-full bg-stone-900/40 hover:bg-stone-900/70 text-white flex items-center justify-center backdrop-blur-sm opacity-0 group-hover:opacity-100 transition-opacity duration-200 focus:opacity-100 z-10"
                        aria-label="Slide Selanjutnya"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>

                    <!-- Slider Indicators -->
                    <div class="absolute bottom-3 right-4 flex space-x-1.5 z-10">
                        @foreach ($banners as $index => $banner)
                            <button
                                @click="activeSlide = {{ $index }}"
                                :class="activeSlide === {{ $index }} ? 'w-6 bg-white' : 'w-2 bg-white/40 hover:bg-white/70'"
                                class="h-2 rounded-full transition-all duration-300"
                                aria-label="Ke slide {{ $index + 1 }}"
                            ></button>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Side Banners (Right 1 col) -->
            <div class="flex flex-col gap-3.5 h-64 sm:h-80">
                <div class="flex-1 rounded-2xl bg-gradient-to-br from-teal-700 to-secondary p-5 text-white flex flex-col justify-between shadow-sm relative overflow-hidden group hover:shadow-md transition">
                    <div class="relative z-10">
                        <span class="text-xs font-bold bg-white/20 px-2.5 py-0.5 rounded-full">VOUCHER HEMAT</span>
                        <h3 class="text-base sm:text-lg font-bold mt-1 font-heading">Klaim Promo Kuliner</h3>
                        <p class="text-xs text-white/90 mt-0.5 line-clamp-2">Potongan harga lezat untuk pesanan kuliner & catering favoritmu!</p>
                    </div>
                    <a href="{{ route('products.index') }}" class="relative z-10 text-xs font-bold text-teal-100 group-hover:text-white flex items-center gap-1 mt-2">
                        Gunakan Promo &rarr;
                    </a>
                    <div class="absolute -right-4 -bottom-4 w-28 h-28 bg-white/10 rounded-full blur-xl pointer-events-none"></div>
                </div>

                <div class="flex-1 rounded-2xl bg-gradient-to-br from-orange-600 to-amber-700 p-5 text-white flex flex-col justify-between shadow-sm relative overflow-hidden group hover:shadow-md transition">
                    <div class="relative z-10">
                        <span class="text-xs font-bold bg-white/20 px-2.5 py-0.5 rounded-full">SELLER & MITRA</span>
                        <h3 class="text-base sm:text-lg font-bold mt-1 font-heading">Buka Lapak di TokoKu</h3>
                        <p class="text-xs text-white/90 mt-0.5 line-clamp-2">Tawarkan makanan atau keahlian jasamu ke ribuan calon pelanggan.</p>
                    </div>
                    <a href="{{ route('seller.register') }}" class="relative z-10 text-xs font-bold text-orange-100 group-hover:text-white flex items-center gap-1 mt-2">
                        Daftar Jadi Penjual &rarr;
                    </a>
                    <div class="absolute -right-4 -bottom-4 w-28 h-28 bg-white/10 rounded-full blur-xl pointer-events-none"></div>
                </div>
            </div>
        </div>

        <!-- 2. Kategori Pilihan (Database Driven) -->
        <div class="bg-white rounded-2xl p-5 sm:p-6 shadow-sm border border-stone-200">
            <div class="flex items-center justify-between mb-5">
                <div class="flex items-center space-x-2">
                    <div class="w-2 h-6 bg-secondary rounded-full"></div>
                    <h2 class="text-base sm:text-lg font-bold text-stone-900 uppercase tracking-wide font-heading">Kategori Pilihan</h2>
                </div>
                <a href="{{ route('products.index') }}" class="text-xs font-semibold text-secondary hover:text-primary transition flex items-center gap-1">
                    Lihat Semua
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>

            <div class="grid grid-cols-4 sm:grid-cols-8 gap-3 sm:gap-4 text-center">
                @foreach ($categories as $cat)
                    <a href="{{ route('products.category', $cat->slug) }}" class="group flex flex-col items-center p-2.5 sm:p-3 rounded-xl hover:bg-stone-50 hover:shadow-sm transition-all duration-200">
                        <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-stone-100 text-stone-700 flex items-center justify-center mb-2 group-hover:scale-105 group-hover:bg-primary group-hover:text-white transition-all duration-200 shadow-sm">
                            @if ($cat->icon && str_starts_with($cat->icon, 'M'))
                                <svg class="w-6 h-6 sm:w-7 sm:h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $cat->icon }}"/>
                                </svg>
                            @else
                                <svg class="w-6 h-6 sm:w-7 sm:h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                </svg>
                            @endif
                        </div>
                        <span class="text-xs font-semibold text-stone-800 group-hover:text-primary transition line-clamp-1">{{ $cat->name }}</span>
                        <span class="text-xs text-stone-500 mt-0.5">{{ $cat->products_count }} item</span>
                    </a>
                @endforeach
            </div>
        </div>

        <!-- 3. Flash Sale Section (Database Driven) -->
        @if ($flashSales->isNotEmpty())
            <div class="bg-white rounded-2xl p-5 sm:p-6 shadow-sm border border-orange-200 overflow-hidden relative">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between pb-4 border-b border-stone-100 gap-3">
                    <div class="flex items-center gap-3">
                        <span class="bg-primary text-white text-xs font-black px-3 py-1.5 rounded-lg tracking-wider uppercase flex items-center gap-1.5 shadow-sm">
                            <svg class="w-4 h-4 fill-current animate-bounce" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/></svg>
                            FLASH SALE
                        </span>
                        <!-- Countdown Timer Interaktif -->
                        <div class="flex items-center gap-1.5 text-xs font-bold" x-data="{
                            hours: '04',
                            minutes: '15',
                            seconds: '30',
                            init() {
                                let totalSecs = 4 * 3600 + 15 * 60 + 30;
                                setInterval(() => {
                                    if (totalSecs > 0) {
                                        totalSecs--;
                                        this.hours = String(Math.floor(totalSecs / 3600)).padStart(2, '0');
                                        this.minutes = String(Math.floor((totalSecs % 3600) / 60)).padStart(2, '0');
                                        this.seconds = String(totalSecs % 60).padStart(2, '0');
                                    }
                                }, 1000);
                            }
                        }">
                            <span class="bg-stone-900 text-white px-2 py-1 rounded-md" x-text="hours">04</span>
                            <span class="text-stone-400">:</span>
                            <span class="bg-stone-900 text-white px-2 py-1 rounded-md" x-text="minutes">15</span>
                            <span class="text-stone-400">:</span>
                            <span class="bg-stone-900 text-white px-2 py-1 rounded-md" x-text="seconds">30</span>
                        </div>
                    </div>
                    <a href="{{ route('products.index', ['sort' => 'top_selling']) }}" class="text-xs font-semibold text-primary hover:text-primary-hover transition flex items-center gap-1">
                        Lihat Semua Flash Sale &rarr;
                    </a>
                </div>

                <!-- Flash Sale Products Grid -->
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-3 sm:gap-4 mt-5">
                    @foreach ($flashSales as $item)
                        <a href="{{ route('products.show', $item->slug) }}" class="group bg-white rounded-xl border border-stone-200 hover:border-primary hover:-translate-y-1 hover:shadow-md transition-all duration-300 flex flex-col p-3 relative overflow-hidden">
                            <span class="absolute top-2 right-2 bg-rose-600 text-white text-xs font-extrabold px-1.5 py-0.5 rounded shadow z-10">
                                PROMO
                            </span>

                            <div class="w-full aspect-square bg-stone-50 rounded-lg flex items-center justify-center mb-2.5 overflow-hidden relative">
                                @if ($item->primaryImage)
                                    <img src="{{ $item->primaryImage->image_path }}" alt="{{ $item->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                @else
                                    <div class="w-16 h-16 rounded-xl bg-orange-50 flex items-center justify-center text-primary group-hover:scale-105 transition-transform duration-300">
                                        <svg class="w-8 h-8 stroke-current" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                    </div>
                                @endif
                            </div>

                            <h3 class="text-xs font-medium text-stone-800 line-clamp-2 h-8 group-hover:text-primary transition leading-tight">
                                {{ $item->name }}
                            </h3>

                            <div class="mt-2">
                                <div class="text-sm font-extrabold text-primary">Rp {{ number_format($item->price, 0, ',', '.') }}</div>
                                <div class="text-xs text-stone-400 line-through">Rp {{ number_format($item->price * 1.3, 0, ',', '.') }}</div>
                            </div>

                            <!-- Progress Bar Terjual -->
                            <div class="mt-2">
                                <div class="w-full bg-stone-100 rounded-full h-3.5 overflow-hidden relative flex items-center justify-center">
                                    <div class="bg-gradient-to-r from-orange-400 to-primary h-full rounded-full" style="width: 75%"></div>
                                    <span class="absolute text-xs font-bold text-stone-700">Stok: {{ $item->stock }}</span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- 4. Rekomendasi Produk Utama (Database Driven) -->
        <div id="katalog" class="space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between bg-white p-4 rounded-xl shadow-sm border border-stone-200 gap-3">
                <div class="flex items-center space-x-2">
                    <div class="w-2 h-6 bg-primary rounded-full"></div>
                    <h2 class="text-base sm:text-lg font-bold text-stone-900 uppercase tracking-wide font-heading">Rekomendasi Untukmu</h2>
                </div>
                <div class="flex flex-wrap items-center gap-1.5 text-xs">
                    <span class="text-stone-500 font-medium mr-1">Urutkan:</span>
                    <a href="{{ route('products.index', ['sort' => 'latest']) }}" class="px-3 py-1.5 bg-primary text-white font-semibold rounded-lg shadow-sm">Terkait / Terbaru</a>
                    <a href="{{ route('products.index', ['sort' => 'top_selling']) }}" class="px-3 py-1.5 bg-stone-100 hover:bg-stone-200 text-stone-700 font-medium rounded-lg transition">Terlaris</a>
                    <a href="{{ route('products.index', ['sort' => 'price_low']) }}" class="px-3 py-1.5 bg-stone-100 hover:bg-stone-200 text-stone-700 font-medium rounded-lg transition">Harga Terendah</a>
                    <a href="{{ route('products.index', ['sort' => 'price_high']) }}" class="px-3 py-1.5 bg-stone-100 hover:bg-stone-200 text-stone-700 font-medium rounded-lg transition">Harga Tertinggi</a>
                </div>
            </div>

            <!-- Product Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3 sm:gap-4">
                @forelse ($recommendedProducts as $p)
                    <a href="{{ route('products.show', $p->slug) }}" class="group bg-white rounded-xl border border-stone-200 hover:border-primary hover:-translate-y-1 hover:shadow-md transition-all duration-300 flex flex-col overflow-hidden">
                        <!-- Product Thumbnail -->
                        <div class="relative w-full aspect-square bg-stone-50 flex items-center justify-center overflow-hidden">
                            @if ($p->primaryImage)
                                <img src="{{ $p->primaryImage->image_path }}" alt="{{ $p->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            @else
                                <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-orange-50 flex items-center justify-center text-primary group-hover:scale-105 transition-transform duration-300">
                                    <svg class="w-10 h-10 stroke-current" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                </div>
                            @endif
                            <!-- Store Badge & Wishlist Button -->
                            <span class="absolute top-2 left-2 bg-secondary/90 backdrop-blur-sm text-white text-xs font-semibold px-2 py-0.5 rounded-md shadow-sm z-10">
                                Official
                            </span>
                            <div class="absolute top-2 right-2 z-10">
                                <x-wishlist-button 
                                    :productId="$p->id" 
                                    :inWishlist="in_array($p->id, $wishlistProductIds)" 
                                    buttonClass="w-8 h-8 rounded-full bg-white/90 hover:bg-white text-stone-400 hover:text-rose-500 shadow-sm border border-stone-200 flex items-center justify-center transition" 
                                    svgClass="w-4 h-4" />
                            </div>
                        </div>

                        <!-- Product Info -->
                        <div class="p-3 flex flex-col justify-between flex-grow">
                            <div>
                                <h3 class="text-xs text-stone-800 line-clamp-2 font-medium group-hover:text-primary transition leading-tight">
                                    {{ $p->name }}
                                </h3>
                                <div class="mt-2 text-sm font-extrabold text-primary">
                                    Rp {{ number_format($p->price, 0, ',', '.') }}
                                </div>
                            </div>

                            <div class="mt-2 pt-2 border-t border-stone-100 flex items-center justify-between text-xs text-stone-500">
                                <div class="flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-amber-400 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    <span class="font-semibold">{{ $p->average_rating }}</span>
                                </div>
                                <span>Stok {{ $p->stock }}</span>
                            </div>

                            <div class="mt-1 text-xs text-stone-400 truncate flex items-center gap-1">
                                <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                <span class="truncate">{{ $p->store->city ?? 'Indonesia' }}</span>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="col-span-full py-12 text-center text-stone-500">
                        Belum ada produk yang tersedia.
                    </div>
                @endforelse
            </div>

            <!-- Pagination / View All -->
            <div class="pt-6">
                {{ $recommendedProducts->fragment('katalog')->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
