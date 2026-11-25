<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6" x-data="{ mobileFilterOpen: false }">
        <!-- Breadcrumbs & Search Info -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between text-xs text-stone-500 gap-2 border-b border-stone-200 pb-3">
            <div class="flex items-center gap-1.5 flex-wrap">
                <a href="{{ route('home') }}" class="hover:text-primary transition font-medium">Beranda</a>
                <span>/</span>
                <span class="text-stone-900 font-semibold">
                    @if ($selectedCategory)
                        {{ $selectedCategory->name }}
                    @elseif (request('q'))
                        Hasil Pencarian: "{{ request('q') }}"
                    @else
                        Semua Produk
                    @endif
                </span>
            </div>
            <div>
                Menampilkan <span class="font-bold text-stone-800">{{ $products->total() }}</span> produk ditemukan
            </div>
        </div>

        @php
            $hasActiveFilters = request()->hasAny(['category', 'min_price', 'max_price', 'min_rating']);
        @endphp

        <!-- Mobile Filter Bar (< lg) -->
        <div class="lg:hidden flex items-center justify-between gap-2 bg-white p-3 rounded-xl border border-stone-200 shadow-sm">
            <button 
                type="button" 
                @click="mobileFilterOpen = true"
                class="flex items-center gap-2 px-3.5 py-2 bg-stone-100 hover:bg-stone-200 text-stone-800 text-xs font-semibold rounded-lg transition min-h-[44px]"
            >
                <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                <span>Filter</span>
                @if ($hasActiveFilters)
                    <span class="w-2 h-2 rounded-full bg-primary"></span>
                @endif
            </button>

            @if ($hasActiveFilters)
                <a href="{{ route('products.index', array_filter(['q' => request('q'), 'sort' => request('sort')])) }}" class="text-xs text-rose-600 hover:underline font-semibold px-2 py-1">
                    Reset Filter
                </a>
            @endif

            <span class="text-xs text-stone-500 font-medium ml-auto">
                {{ $products->total() }} Item
            </span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 items-start">
            <!-- Desktop Sidebar Filter (>= lg) -->
            <div class="hidden lg:block bg-white p-5 rounded-2xl border border-stone-200 shadow-sm space-y-6">
                <div class="flex items-center justify-between border-b border-stone-100 pb-3">
                    <h3 class="font-bold text-sm text-stone-900 uppercase flex items-center gap-2 font-heading">
                        <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                        Filter Pencarian
                    </h3>
                    @if ($hasActiveFilters)
                        <a href="{{ route('products.index', array_filter(['q' => request('q'), 'sort' => request('sort')])) }}" class="text-xs text-rose-600 hover:underline font-medium">
                            Reset
                        </a>
                    @endif
                </div>

                <form action="{{ route('products.index') }}" method="GET" class="space-y-6">
                    @if (request('q'))
                        <input type="hidden" name="q" value="{{ request('q') }}">
                    @endif
                    @if (request('sort'))
                        <input type="hidden" name="sort" value="{{ request('sort') }}">
                    @endif

                    <!-- 1. Kategori Filter -->
                    <div>
                        <h4 class="text-xs font-bold text-stone-800 uppercase tracking-wide mb-3">Kategori</h4>
                        <div class="space-y-2 max-h-56 overflow-y-auto pr-1 text-xs">
                            <label class="flex items-center gap-2 cursor-pointer hover:text-primary">
                                <input
                                    type="radio"
                                    name="category"
                                    value=""
                                    {{ !request('category') ? 'checked' : '' }}
                                    class="text-primary focus:ring-primary border-stone-300"
                                    onchange="this.form.submit()"
                                >
                                <span class="{{ !request('category') ? 'font-bold text-primary' : 'text-stone-700' }}">Semua Kategori</span>
                            </label>
                            @foreach ($categories as $cat)
                                <label class="flex items-center justify-between cursor-pointer hover:text-primary text-stone-700">
                                    <div class="flex items-center gap-2">
                                        <input
                                            type="radio"
                                            name="category"
                                            value="{{ $cat->slug }}"
                                            {{ request('category') === $cat->slug ? 'checked' : '' }}
                                            class="text-primary focus:ring-primary border-stone-300"
                                            onchange="this.form.submit()"
                                        >
                                        <span class="{{ request('category') === $cat->slug ? 'font-bold text-primary' : '' }}">{{ $cat->name }}</span>
                                    </div>
                                    <span class="text-xs text-stone-400">({{ $cat->products_count }})</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- 2. Rentang Harga -->
                    <div class="border-t border-stone-100 pt-5">
                        <h4 class="text-xs font-bold text-stone-800 uppercase tracking-wide mb-3">Batas Harga (Rp)</h4>
                        <div class="space-y-2">
                            <div class="relative">
                                <span class="absolute left-3 top-2.5 text-xs text-stone-400">Rp</span>
                                <input
                                    type="number"
                                    name="min_price"
                                    value="{{ request('min_price') }}"
                                    placeholder="Harga Minimum"
                                    class="w-full pl-9 pr-3 py-2 text-xs border border-stone-200 rounded-lg focus:ring-primary focus:border-primary"
                                >
                            </div>
                            <div class="relative">
                                <span class="absolute left-3 top-2.5 text-xs text-stone-400">Rp</span>
                                <input
                                    type="number"
                                    name="max_price"
                                    value="{{ request('max_price') }}"
                                    placeholder="Harga Maksimum"
                                    class="w-full pl-9 pr-3 py-2 text-xs border border-stone-200 rounded-lg focus:ring-primary focus:border-primary"
                                >
                            </div>
                            <button type="submit" class="w-full py-2 bg-primary hover:bg-primary-hover text-white rounded-lg text-xs font-bold transition shadow-sm mt-1 min-h-[44px]">
                                Terapkan Harga
                            </button>
                        </div>
                    </div>

                    <!-- 3. Rating Minimum -->
                    <div class="border-t border-stone-100 pt-5">
                        <h4 class="text-xs font-bold text-stone-800 uppercase tracking-wide mb-3">Rating Penilaian</h4>
                        <div class="space-y-2 text-xs">
                            @foreach ([5, 4, 3] as $r)
                                <label class="flex items-center gap-2 cursor-pointer hover:text-primary">
                                    <input
                                        type="radio"
                                        name="min_rating"
                                        value="{{ $r }}"
                                        {{ request('min_rating') == $r ? 'checked' : '' }}
                                        class="text-primary focus:ring-primary border-stone-300"
                                        onchange="this.form.submit()"
                                    >
                                    <div class="flex items-center gap-1 text-amber-400">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <svg class="w-3.5 h-3.5 {{ $i <= $r ? 'fill-current' : 'text-stone-200 fill-current' }}" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                        @endfor
                                        <span class="text-stone-600 ml-1 text-xs font-medium {{ request('min_rating') == $r ? 'font-bold text-primary' : '' }}">{{ $r }} Ke Atas</span>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </form>
            </div>

            <!-- Product Listing (Main Content) -->
            <div class="lg:col-span-3 space-y-5">
                <!-- Sorting Bar -->
                <div class="bg-white p-3.5 rounded-xl border border-stone-200 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                    <div class="flex items-center gap-1.5 flex-wrap">
                        <span class="font-bold text-stone-500 mr-1">Urutkan:</span>
                        @php
                            $currentSort = request('sort', 'latest');
                            $sortUrl = fn($sortVal) => request()->fullUrlWithQuery(['sort' => $sortVal]);
                        @endphp
                        <a href="{{ $sortUrl('latest') }}" class="px-3 py-1.5 rounded-lg font-semibold transition {{ $currentSort === 'latest' ? 'bg-primary text-white shadow-sm' : 'bg-stone-100 text-stone-700 hover:bg-stone-200' }}">
                            Terbaru
                        </a>
                        <a href="{{ $sortUrl('top_selling') }}" class="px-3 py-1.5 rounded-lg font-semibold transition {{ $currentSort === 'top_selling' ? 'bg-primary text-white shadow-sm' : 'bg-stone-100 text-stone-700 hover:bg-stone-200' }}">
                            Terlaris
                        </a>
                        <a href="{{ $sortUrl('price_low') }}" class="px-3 py-1.5 rounded-lg font-semibold transition {{ $currentSort === 'price_low' ? 'bg-primary text-white shadow-sm' : 'bg-stone-100 text-stone-700 hover:bg-stone-200' }}">
                            Harga Terendah
                        </a>
                        <a href="{{ $sortUrl('price_high') }}" class="px-3 py-1.5 rounded-lg font-semibold transition {{ $currentSort === 'price_high' ? 'bg-primary text-white shadow-sm' : 'bg-stone-100 text-stone-700 hover:bg-stone-200' }}">
                            Harga Tertinggi
                        </a>
                        <a href="{{ $sortUrl('rating') }}" class="px-3 py-1.5 rounded-lg font-semibold transition {{ $currentSort === 'rating' ? 'bg-primary text-white shadow-sm' : 'bg-stone-100 text-stone-700 hover:bg-stone-200' }}">
                            Rating Tertinggi
                        </a>
                    </div>
                </div>

                <!-- Products Grid -->
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3 sm:gap-4">
                    @forelse ($products as $p)
                        <a href="{{ route('products.show', $p->slug) }}" class="group bg-white rounded-xl border border-stone-200 hover:border-primary hover:-translate-y-1 hover:shadow-md transition-all duration-300 flex flex-col overflow-hidden">
                            <!-- Thumbnail -->
                            <div class="relative w-full aspect-square bg-stone-50 flex items-center justify-center overflow-hidden">
                                @if ($p->primaryImage)
                                    <img src="{{ $p->primaryImage->image_path }}" alt="{{ $p->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                @else
                                    <div class="w-16 h-16 rounded-xl bg-orange-50 flex items-center justify-center text-primary group-hover:scale-105 transition-transform duration-300">
                                        <svg class="w-8 h-8 stroke-current" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                    </div>
                                @endif
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

                            <!-- Body -->
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
                        <div class="col-span-full py-16 bg-white rounded-2xl border border-stone-200 text-center space-y-3">
                            <div class="w-16 h-16 rounded-full bg-stone-100 text-stone-400 mx-auto flex items-center justify-center">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <h4 class="font-bold text-stone-800 text-sm">Tidak Ada Produk yang Ditemukan</h4>
                            <p class="text-xs text-stone-500 max-w-sm mx-auto">Coba gunakan kata kunci lain atau kurangi filter pencarian untuk menemukan produk yang kamu cari.</p>
                            <a href="{{ route('products.index') }}" class="inline-block px-4 py-2.5 bg-primary text-white font-bold text-xs rounded-xl shadow-sm min-h-[44px]">
                                Reset Semua Filter
                            </a>
                        </div>
                    @endforelse
                </div>

                <!-- Pagination -->
                <div class="pt-4">
                    {{ $products->links() }}
                </div>
            </div>
        </div>

        <!-- Slide-over Mobile Filter Drawer (< lg) -->
        <div 
            x-show="mobileFilterOpen" 
            x-cloak 
            class="relative z-50 lg:hidden"
            aria-modal="true"
        >
            <!-- Backdrop -->
            <div 
                x-show="mobileFilterOpen"
                x-transition:enter="transition-opacity ease-linear duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition-opacity ease-linear duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                @click="mobileFilterOpen = false" 
                class="fixed inset-0 bg-stone-950/60 backdrop-blur-sm"
            ></div>

            <!-- Drawer Container -->
            <div class="fixed inset-y-0 right-0 max-w-full flex">
                <div 
                    x-show="mobileFilterOpen"
                    x-transition:enter="transition ease-in-out duration-300 transform"
                    x-transition:enter-start="translate-x-full"
                    x-transition:enter-end="translate-x-0"
                    x-transition:leave="transition ease-in-out duration-300 transform"
                    x-transition:leave-start="translate-x-0"
                    x-transition:leave-end="translate-x-full"
                    class="w-screen max-w-xs bg-white text-stone-800 shadow-2xl flex flex-col"
                >
                    <!-- Drawer Header -->
                    <div class="p-4 bg-stone-50 border-b border-stone-200 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                            <h3 class="font-bold text-sm text-stone-900 uppercase font-heading">Filter Pencarian</h3>
                        </div>
                        <button 
                            type="button" 
                            @click="mobileFilterOpen = false" 
                            class="p-2 -mr-2 text-stone-500 hover:text-stone-700 rounded-lg transition"
                            aria-label="Tutup Filter"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <!-- Filter Form Content -->
                    <div class="flex-1 overflow-y-auto p-4 space-y-6">
                        <form id="mobileFilterForm" action="{{ route('products.index') }}" method="GET" class="space-y-6">
                            @if (request('q'))
                                <input type="hidden" name="q" value="{{ request('q') }}">
                            @endif
                            @if (request('sort'))
                                <input type="hidden" name="sort" value="{{ request('sort') }}">
                            @endif

                            <!-- Kategori -->
                            <div>
                                <h4 class="text-xs font-bold text-stone-800 uppercase tracking-wide mb-3">Kategori</h4>
                                <div class="space-y-2 max-h-52 overflow-y-auto pr-1 text-xs">
                                    <label class="flex items-center gap-2 cursor-pointer py-1">
                                        <input
                                            type="radio"
                                            name="category"
                                            value=""
                                            {{ !request('category') ? 'checked' : '' }}
                                            class="text-primary focus:ring-primary border-stone-300"
                                        >
                                        <span class="{{ !request('category') ? 'font-bold text-primary' : 'text-stone-700' }}">Semua Kategori</span>
                                    </label>
                                    @foreach ($categories as $cat)
                                        <label class="flex items-center justify-between cursor-pointer py-1 text-stone-700">
                                            <div class="flex items-center gap-2">
                                                <input
                                                    type="radio"
                                                    name="category"
                                                    value="{{ $cat->slug }}"
                                                    {{ request('category') === $cat->slug ? 'checked' : '' }}
                                                    class="text-primary focus:ring-primary border-stone-300"
                                                >
                                                <span class="{{ request('category') === $cat->slug ? 'font-bold text-primary' : '' }}">{{ $cat->name }}</span>
                                            </div>
                                            <span class="text-xs text-stone-400">({{ $cat->products_count }})</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Rentang Harga -->
                            <div class="border-t border-stone-100 pt-5">
                                <h4 class="text-xs font-bold text-stone-800 uppercase tracking-wide mb-3">Batas Harga (Rp)</h4>
                                <div class="space-y-2">
                                    <div class="relative">
                                        <span class="absolute left-3 top-2.5 text-xs text-stone-400">Rp</span>
                                        <input
                                            type="number"
                                            name="min_price"
                                            value="{{ request('min_price') }}"
                                            placeholder="Harga Minimum"
                                            class="w-full pl-9 pr-3 py-2 text-xs border border-stone-200 rounded-lg focus:ring-primary focus:border-primary"
                                        >
                                    </div>
                                    <div class="relative">
                                        <span class="absolute left-3 top-2.5 text-xs text-stone-400">Rp</span>
                                        <input
                                            type="number"
                                            name="max_price"
                                            value="{{ request('max_price') }}"
                                            placeholder="Harga Maksimum"
                                            class="w-full pl-9 pr-3 py-2 text-xs border border-stone-200 rounded-lg focus:ring-primary focus:border-primary"
                                        >
                                    </div>
                                </div>
                            </div>

                            <!-- Rating -->
                            <div class="border-t border-stone-100 pt-5">
                                <h4 class="text-xs font-bold text-stone-800 uppercase tracking-wide mb-3">Rating Penilaian</h4>
                                <div class="space-y-2 text-xs">
                                    @foreach ([5, 4, 3] as $r)
                                        <label class="flex items-center gap-2 cursor-pointer py-1">
                                            <input
                                                type="radio"
                                                name="min_rating"
                                                value="{{ $r }}"
                                                {{ request('min_rating') == $r ? 'checked' : '' }}
                                                class="text-primary focus:ring-primary border-stone-300"
                                            >
                                            <div class="flex items-center gap-1 text-amber-400">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <svg class="w-3.5 h-3.5 {{ $i <= $r ? 'fill-current' : 'text-stone-200 fill-current' }}" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                                @endfor
                                                <span class="text-stone-600 ml-1 text-xs font-medium {{ request('min_rating') == $r ? 'font-bold text-primary' : '' }}">{{ $r }} Ke Atas</span>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Drawer Footer Action -->
                    <div class="p-4 bg-stone-50 border-t border-stone-200 space-y-2">
                        <button 
                            type="button" 
                            onclick="document.getElementById('mobileFilterForm').submit()"
                            class="w-full py-2.5 bg-primary hover:bg-primary-hover text-white rounded-xl text-xs font-bold transition shadow-sm min-h-[44px]"
                        >
                            Terapkan Filter
                        </button>
                        @if ($hasActiveFilters)
                            <a 
                                href="{{ route('products.index', array_filter(['q' => request('q'), 'sort' => request('sort')])) }}" 
                                class="block w-full py-2 text-center text-xs text-rose-600 hover:underline font-semibold"
                            >
                                Reset Filter
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
