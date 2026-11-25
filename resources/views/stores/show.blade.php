<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-stone-800 leading-tight font-heading">
            {{ $store->name }}
        </h2>
    </x-slot>

    <div class="py-6 bg-surface min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <!-- Store Profile Header Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-stone-200 overflow-hidden">
                <!-- Banner Background -->
                <div class="h-36 sm:h-48 relative bg-gradient-to-r from-secondary to-primary">
                    @if ($store->banner)
                        <img src="{{ $store->banner }}" alt="{{ $store->name }}" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-stone-950/30"></div>
                    @endif
                </div>

                <!-- Store Info Bar -->
                <div class="p-6 relative">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 -mt-16 sm:-mt-20">
                        <div class="flex items-end gap-4">
                            <div class="w-24 h-24 sm:w-28 sm:h-28 rounded-2xl bg-white shadow-xl border-4 border-white overflow-hidden flex items-center justify-center font-black text-3xl text-secondary shrink-0">
                                @if ($store->logo)
                                    <img src="{{ $store->logo }}" alt="{{ $store->name }}" class="w-full h-full object-cover">
                                @else
                                    {{ strtoupper(substr($store->name, 0, 1)) }}
                                @endif
                            </div>
                            <div class="mb-1">
                                <div class="flex items-center gap-2">
                                    <h1 class="text-xl sm:text-2xl font-bold text-stone-900 font-heading">{{ $store->name }}</h1>
                                    <span class="bg-teal-100 text-secondary text-xs font-bold px-2 py-0.5 rounded-full flex items-center gap-1">
                                        <svg class="w-3 h-3 text-secondary" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                        Official Seller
                                    </span>
                                </div>
                                <p class="text-xs text-stone-500 mt-1 flex items-center gap-3">
                                    <span class="flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        {{ $store->city }}
                                    </span>
                                    <span>&bull;</span>
                                    <span>Bergabung {{ $store->created_at->translatedFormat('F Y') }}</span>
                                </p>
                            </div>
                        </div>

                        <!-- Store Quick Metrics -->
                        <div class="grid grid-cols-3 gap-4 border-t md:border-t-0 md:border-l border-stone-200 pt-4 md:pt-0 md:pl-6 text-center">
                            <div>
                                <span class="block text-lg font-extrabold text-stone-900 font-heading">{{ $totalProductsCount }}</span>
                                <span class="text-xs text-stone-500 font-medium">Produk</span>
                            </div>
                            <div>
                                <span class="block text-lg font-extrabold text-amber-500 flex items-center justify-center gap-1 font-heading">
                                    <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    {{ $averageRating > 0 ? $averageRating : '5.0' }}
                                </span>
                                <span class="text-xs text-stone-500 font-medium">Penilaian</span>
                            </div>
                            <div>
                                <span class="block text-lg font-extrabold text-secondary font-heading">{{ $reviewsCount }}</span>
                                <span class="text-xs text-stone-500 font-medium">Ulasan</span>
                            </div>
                        </div>
                    </div>

                    @if ($store->description)
                        <div class="mt-4 pt-4 border-t border-stone-200 text-xs text-stone-600 leading-relaxed">
                            {{ $store->description }}
                        </div>
                    @endif
                </div>
            </div>

            <!-- Filters & Catalog Grid -->
            <div class="bg-white rounded-2xl shadow-sm border border-stone-200 p-6 space-y-6">
                <!-- Search & Filters Toolbar -->
                <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                    <h3 class="font-bold text-stone-900 text-base flex items-center gap-2 font-heading">
                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        Semua Produk Toko ({{ $products->total() }})
                    </h3>

                    <!-- Filter Form -->
                    <form method="GET" action="{{ route('stores.show', $store->slug) }}" class="flex flex-wrap items-center gap-2 w-full md:w-auto">
                        <!-- Search in Store -->
                        <div class="relative flex-1 sm:w-60">
                            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari di toko ini..."
                                class="w-full text-xs border-stone-300 rounded-xl pl-8 pr-3 py-2 focus:ring-primary focus:border-primary shadow-sm" />
                            <svg class="w-4 h-4 text-stone-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>

                        <!-- Category Filter -->
                        @if ($categories->isNotEmpty())
                            <select name="category" onchange="this.form.submit()" class="text-xs border-stone-300 rounded-xl py-2 px-3 focus:ring-primary focus:border-primary shadow-sm">
                                <option value="">Semua Kategori</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->slug }}" {{ request('category') === $cat->slug ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                        @endif

                        <!-- Sort Order -->
                        <select name="sort" onchange="this.form.submit()" class="text-xs border-stone-300 rounded-xl py-2 px-3 focus:ring-primary focus:border-primary shadow-sm">
                            <option value="newest" {{ request('sort') === 'newest' ? 'selected' : '' }}>Terbaru</option>
                            <option value="popular" {{ request('sort') === 'popular' ? 'selected' : '' }}>Terlaris</option>
                            <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Harga Terendah</option>
                            <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Harga Tertinggi</option>
                        </select>
                    </form>
                </div>

                <!-- Products Grid -->
                @if ($products->isEmpty())
                    <div class="text-center py-12">
                        <div class="w-16 h-16 mx-auto mb-3 text-stone-300">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        </div>
                        <h4 class="text-sm font-bold text-stone-700">Belum ada produk yang sesuai</h4>
                        <p class="text-xs text-stone-500 mt-1">Coba gunakan kata kunci pencarian lain atau pilih kategori yang berbeda.</p>
                    </div>
                @else
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3 sm:gap-4">
                        @foreach ($products as $product)
                            <div class="bg-white rounded-xl border border-stone-200 hover:border-primary hover:-translate-y-1 hover:shadow-md transition-all duration-300 overflow-hidden flex flex-col group">
                                <a href="{{ route('products.show', $product->slug) }}" class="block relative aspect-square bg-stone-50 overflow-hidden">
                                    @if ($product->primaryImage)
                                        <img src="{{ $product->primaryImage->image_path }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-stone-300">
                                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        </div>
                                    @endif
                                </a>

                                <div class="p-3 flex-1 flex flex-col justify-between">
                                    <div>
                                        <span class="text-xs text-stone-400 font-medium block truncate">{{ $product->category->name ?? 'Kategori' }}</span>
                                        <a href="{{ route('products.show', $product->slug) }}" class="text-xs font-semibold text-stone-800 hover:text-primary transition line-clamp-2 mt-0.5 leading-tight">
                                            {{ $product->name }}
                                        </a>
                                    </div>

                                    <div class="mt-2.5 pt-2 border-t border-stone-100 flex items-center justify-between">
                                        <span class="text-xs sm:text-sm font-extrabold text-primary">
                                            Rp {{ number_format($product->price, 0, ',', '.') }}
                                        </span>
                                        <span class="text-xs text-stone-400">Stok {{ $product->stock }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="pt-4 border-t border-stone-200">
                        {{ $products->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
