<header class="bg-gradient-to-r from-secondary to-primary text-white shadow-md sticky top-0 z-50" x-data="{ mobileMenuOpen: false, userDropdownOpen: false }">
    <!-- Top Utility Bar (Shopee Style) -->
    <div class="border-b border-white/15 text-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center h-8">
            <div class="flex items-center space-x-4">
                <a href="#" class="hover:text-emerald-200 transition">Mulai Jual</a>
                <span class="text-white/40">|</span>
                <a href="#" class="hover:text-emerald-200 transition">Download Aplikasi</a>
                <span class="text-white/40 hidden sm:inline">|</span>
                <div class="hidden sm:flex items-center space-x-2">
                    <span>Ikuti Kami</span>
                    <a href="#" class="hover:text-emerald-200"><i class="fab fa-facebook"></i>FB</a>
                    <a href="#" class="hover:text-emerald-200"><i class="fab fa-instagram"></i>IG</a>
                </div>
            </div>
            <div class="flex items-center space-x-4">
                <a href="#" class="flex items-center space-x-1 hover:text-emerald-200 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    <span>Notifikasi</span>
                </a>
                <a href="#" class="flex items-center space-x-1 hover:text-emerald-200 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Bantuan</span>
                </a>
                @auth
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center space-x-1 font-medium hover:text-emerald-200">
                            <span class="w-5 h-5 rounded-full bg-white text-secondary font-bold text-[10px] flex items-center justify-center">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </span>
                            <span>{{ Auth::user()->name }}</span>
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" @click.outside="open = false" x-cloak class="absolute right-0 mt-2 w-48 bg-white text-gray-800 rounded-lg shadow-xl py-1 text-sm z-50">
                            <a href="{{ route('dashboard') }}" class="block px-4 py-2 hover:bg-gray-100 flex items-center gap-2">
                                <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                Dashboard
                            </a>
                            <a href="{{ route('orders.index') }}" class="block px-4 py-2 hover:bg-gray-100 flex items-center gap-2">
                                <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                Pesanan Saya
                            </a>
                            <a href="{{ route('wishlist.index') }}" class="block px-4 py-2 hover:bg-gray-100 flex items-center gap-2">
                                <svg class="w-4 h-4 text-red-500 fill-current" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/></svg>
                                Wishlist Saya
                            </a>
                            <a href="{{ route('addresses.index') }}" class="block px-4 py-2 hover:bg-gray-100 flex items-center gap-2">
                                <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                Alamat Saya
                            </a>
                            <a href="{{ route('cart.index') }}" class="block px-4 py-2 hover:bg-gray-100 flex items-center gap-2">
                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                Keranjang
                            </a>
                            <a href="{{ route('profile.edit') }}" class="block px-4 py-2 hover:bg-gray-100 flex items-center gap-2">
                                <svg class="w-4 h-4 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                Profil Saya
                            </a>
                            <div class="border-t border-gray-100 my-1"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 hover:bg-red-50 text-red-600 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                    Keluar
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="font-medium hover:text-emerald-200 transition">Masuk</a>
                    <span class="text-white/40">|</span>
                    <a href="{{ route('register') }}" class="font-medium hover:text-emerald-200 transition">Daftar</a>
                @endauth
            </div>
        </div>
    </div>

    <!-- Main Header Bar -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3.5">
        <div class="flex items-center justify-between gap-4 lg:gap-8">
            <!-- Brand Logo -->
            <a href="{{ url('/') }}" class="shrink-0 flex items-center group">
                <x-application-logo />
            </a>

            <!-- Search Bar (Shopee Style) -->
            <div class="flex-1 max-w-3xl">
                <form action="{{ route('products.index') }}" method="GET" class="relative flex items-center bg-white rounded-lg p-1 shadow-inner">
                    <input
                        type="text"
                        name="q"
                        value="{{ request('q') }}"
                        placeholder="Cari produk, toko, atau kategori pilihanmu di sini..."
                        class="w-full px-4 py-2 text-sm text-gray-800 placeholder-gray-400 border-0 focus:ring-0 focus:outline-none bg-transparent"
                    />
                    <button type="submit" class="bg-primary hover:bg-emerald-700 text-white px-6 py-2 rounded-md transition flex items-center justify-center font-medium shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </button>
                </form>
                <!-- Quick Keywords Bar -->
                <div class="hidden md:flex items-center gap-3 text-xs text-white/80 mt-1.5 px-1 overflow-hidden">
                    <span class="font-medium text-emerald-200">Populer:</span>
                    <a href="{{ route('products.index', ['q' => 'Kemeja']) }}" class="hover:text-white transition">Kemeja Pria</a>
                    <a href="{{ route('products.index', ['q' => 'Sneakers']) }}" class="hover:text-white transition">Sepatu Sneaker</a>
                    <a href="{{ route('products.index', ['q' => 'Smartwatch']) }}" class="hover:text-white transition">Smartwatch</a>
                    <a href="{{ route('products.index', ['q' => 'Ransel']) }}" class="hover:text-white transition">Tas Ransel</a>
                    <a href="{{ route('products.index', ['q' => 'Serum']) }}" class="hover:text-white transition">Serum Wajah</a>
                    <a href="{{ route('products.index', ['q' => 'Earphone']) }}" class="hover:text-white transition">TWS Earphone</a>
                </div>
            </div>

            <!-- Cart & Wishlist Actions -->
            <div class="flex items-center space-x-3 sm:space-x-5">
                <!-- Wishlist -->
                @php
                    $wishlistBadgeCount = Auth::check() ? (int) Auth::user()->wishlists()->count() : 0;
                @endphp
                <a href="{{ route('wishlist.index') }}" class="relative p-2 text-white hover:text-emerald-200 transition" title="Wishlist & Favorit Saya">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                    @if ($wishlistBadgeCount > 0)
                        <span class="absolute top-0 right-0 bg-red-500 text-white text-[10px] font-bold rounded-full h-4 w-4 flex items-center justify-center shadow">
                            {{ $wishlistBadgeCount }}
                        </span>
                    @endif
                </a>

                <!-- Cart -->
                <a href="{{ route('cart.index') }}" class="relative p-2 text-white hover:text-emerald-200 transition" title="Keranjang Belanja">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    @php
                        $cartBadgeCount = Auth::check() ? (int) (Auth::user()->cart?->items()->sum('quantity') ?? 0) : 0;
                    @endphp
                    <span class="absolute top-0 right-0 bg-emerald-300 text-emerald-950 text-[10px] font-extrabold rounded-full h-4.5 w-4.5 px-1 flex items-center justify-center shadow">
                        {{ $cartBadgeCount }}
                    </span>
                </a>
            </div>
        </div>
    </div>
</header>
