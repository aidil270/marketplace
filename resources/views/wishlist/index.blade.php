<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-xl text-gray-800 leading-tight flex items-center gap-2">
                <svg class="w-6 h-6 text-red-500 fill-current" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" />
                </svg>
                Wishlist & Favorit Saya
            </h2>
            <span class="text-sm text-gray-500">
                Total: <strong class="text-gray-900">{{ $wishlists->total() }}</strong> Produk
            </span>
        </div>
    </x-slot>

    <div class="py-8 bg-[#F8FAFC] min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Flash Message -->
            @if (session('success'))
                <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl flex items-center gap-3">
                    <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span class="text-sm font-medium">{{ session('success') }}</span>
                </div>
            @endif

            @if ($wishlists->isEmpty())
                <!-- Empty State -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center max-w-lg mx-auto">
                    <div class="w-20 h-20 bg-red-50 text-red-500 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Wishlist Anda Masih Kosong</h3>
                    <p class="text-sm text-gray-500 mb-6 leading-relaxed">
                        Simpan produk impian Anda dengan menekan ikon hati di katalog produk agar mudah ditemukan kembali sewaktu-waktu.
                    </p>
                    <a href="{{ route('products.index') }}" class="inline-flex items-center justify-center px-6 py-2.5 bg-primary hover:bg-emerald-700 text-white font-bold text-sm rounded-xl transition shadow">
                        Mulai Belanja Sekarang
                    </a>
                </div>
            @else
                <!-- Grid Wishlist Products -->
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                    @foreach ($wishlists as $wishlist)
                        @php
                            $product = $wishlist->product;
                            if (!$product) continue;
                            $primaryImage = $product->primaryImage?->image_path 
                                ?? 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=500&auto=format&fit=crop&q=60';
                        @endphp
                        <div class="bg-white rounded-xl border border-gray-100 hover:border-emerald-300 hover:shadow-md transition flex flex-col p-3 relative group">
                            <!-- Tombol Hapus Wishlist -->
                            <form action="{{ route('wishlist.destroy', $product->id) }}" method="POST" class="absolute top-2 right-2 z-10">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                    class="w-7 h-7 bg-white/90 hover:bg-red-50 text-red-500 rounded-full flex items-center justify-center shadow-sm transition border border-gray-100"
                                    title="Hapus dari Wishlist">
                                    <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </form>

                            <!-- Gambar Produk -->
                            <a href="{{ route('products.show', $product->slug) }}" class="block aspect-square w-full rounded-lg overflow-hidden bg-gray-50 mb-2.5">
                                <img src="{{ $primaryImage }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                            </a>

                            <!-- Info Toko -->
                            @if ($product->store)
                                <div class="flex items-center gap-1 text-[11px] text-gray-500 mb-1 truncate">
                                    <svg class="w-3 h-3 text-secondary flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M4 3a2 2 0 100 4h12a2 2 0 100-4H4z" />
                                        <path fill-rule="evenodd" d="M3 8h14v7a2 2 0 01-2 2H5a2 2 0 01-2-2V8zm5 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z" clip-rule="evenodd" />
                                    </svg>
                                    <span class="truncate">{{ $product->store->name }}</span>
                                </div>
                            @endif

                            <!-- Nama Produk -->
                            <a href="{{ route('products.show', $product->slug) }}" class="text-xs font-semibold text-gray-800 line-clamp-2 hover:text-primary transition mb-1 leading-snug">
                                {{ $product->name }}
                            </a>

                            <!-- Harga -->
                            <div class="mt-auto pt-2">
                                <span class="text-primary font-black text-sm block">
                                    Rp {{ number_format($product->price, 0, ',', '.') }}
                                </span>

                                <!-- Rating & Review Count -->
                                <div class="flex items-center gap-1 mt-1 text-[10px] text-gray-500">
                                    <span class="text-yellow-400">★</span>
                                    <span class="font-bold text-gray-700">{{ $product->average_rating }}</span>
                                    <span>({{ $product->reviews_count }})</span>
                                </div>

                                <!-- Action: Masukkan Keranjang / Pilih Varian -->
                                <div class="mt-2.5">
                                    @if ($product->variants_count > 0)
                                        <a href="{{ route('products.show', $product->slug) }}" class="w-full py-1.5 px-2 bg-emerald-50 hover:bg-primary hover:text-white text-primary text-xs font-bold rounded-lg transition flex items-center justify-center gap-1 border border-emerald-200">
                                            <span>Pilih Varian</span>
                                        </a>
                                    @else
                                        <form action="{{ route('cart.store') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                                            <input type="hidden" name="quantity" value="1">
                                            <button type="submit" class="w-full py-1.5 px-2 bg-primary hover:bg-emerald-700 text-white text-xs font-bold rounded-lg transition flex items-center justify-center gap-1 shadow-sm">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                                <span>+ Keranjang</span>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $wishlists->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
