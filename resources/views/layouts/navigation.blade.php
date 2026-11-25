<header class="bg-gradient-to-r from-secondary to-primary text-white shadow-md sticky top-0 z-50" x-data="{ mobileMenuOpen: false, userDropdownOpen: false }">
    <!-- Top Utility Bar -->
    <div class="border-b border-white/15 text-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center h-8">
            <div class="flex items-center space-x-4">
                @auth
                    @if(Auth::user()->isSeller() && Auth::user()->store)
                        <a href="{{ route('seller.dashboard') }}" class="hover:text-orange-200 transition font-medium">Toko Saya</a>
                    @else
                        <a href="{{ route('seller.register') }}" class="hover:text-orange-200 transition">Mulai Jual</a>
                    @endif
                @else
                    <a href="{{ route('register', ['role' => 'seller']) }}" class="hover:text-orange-200 transition">Mulai Jual</a>
                @endauth
                <span class="text-white/40">|</span>
                <a href="#" class="hover:text-orange-200 transition">Download Aplikasi</a>
                <span class="text-white/40 hidden sm:inline">|</span>
                <div class="hidden sm:flex items-center space-x-2">
                    <span>Ikuti Kami</span>
                    <a href="#" class="hover:text-orange-200 transition p-0.5" title="Facebook" aria-label="Facebook">
                        <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                    </a>
                    <a href="#" class="hover:text-orange-200 transition p-0.5" title="Instagram" aria-label="Instagram">
                        <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                    </a>
                </div>
            </div>
            <div class="flex items-center space-x-4">
                <a href="#" class="flex items-center space-x-1 hover:text-orange-200 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    <span class="hidden sm:inline">Notifikasi</span>
                </a>
                <a href="#" class="flex items-center space-x-1 hover:text-orange-200 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="hidden sm:inline">Bantuan</span>
                </a>
                @auth
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center space-x-1.5 font-medium hover:text-orange-200 transition py-1">
                            <span class="w-5 h-5 rounded-full bg-white text-secondary font-bold text-[11px] flex items-center justify-center shadow-sm">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </span>
                            <span class="max-w-[100px] truncate sm:max-w-none">{{ Auth::user()->name }}</span>
                            <svg class="w-3 h-3 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div 
                            x-show="open" 
                            @click.outside="open = false" 
                            x-cloak 
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                            x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                            class="absolute right-0 mt-2 w-52 bg-white text-stone-800 rounded-xl shadow-xl py-1.5 text-sm z-50 border border-stone-100"
                        >
                            <div class="px-4 py-2 border-b border-stone-100">
                                <p class="text-xs text-stone-500">Masuk sebagai</p>
                                <p class="text-xs font-semibold text-stone-900 truncate">{{ Auth::user()->email }}</p>
                            </div>
                            @if(Auth::user()->isAdmin())
                                <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 hover:bg-purple-50 text-purple-700 font-bold flex items-center gap-2">
                                    <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    Panel Admin
                                </a>
                            @endif
                            <a href="{{ route('dashboard') }}" class="px-4 py-2 hover:bg-stone-50 flex items-center gap-2">
                                <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                Dashboard
                            </a>
                            @if(Auth::user()->isSeller() && Auth::user()->store)
                                <a href="{{ route('seller.dashboard') }}" class="px-4 py-2 hover:bg-teal-50 text-secondary font-medium flex items-center gap-2">
                                    <svg class="w-4 h-4 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                    Toko Saya
                                </a>
                            @else
                                <a href="{{ route('seller.register') }}" class="px-4 py-2 hover:bg-orange-50 text-primary font-medium flex items-center gap-2">
                                    <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                    Mulai Jual
                                </a>
                            @endif
                            <a href="{{ route('orders.index') }}" class="px-4 py-2 hover:bg-stone-50 flex items-center gap-2">
                                <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                Pesanan Saya
                            </a>
                            <a href="{{ route('wishlist.index') }}" class="px-4 py-2 hover:bg-stone-50 flex items-center gap-2">
                                <svg class="w-4 h-4 text-rose-500 fill-current" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/></svg>
                                Wishlist Saya
                            </a>
                            <a href="{{ route('addresses.index') }}" class="px-4 py-2 hover:bg-stone-50 flex items-center gap-2">
                                <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                Alamat Saya
                            </a>
                            <a href="{{ route('cart.index') }}" class="px-4 py-2 hover:bg-stone-50 flex items-center gap-2">
                                <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                Keranjang
                            </a>
                            <a href="{{ route('profile.edit') }}" class="px-4 py-2 hover:bg-stone-50 flex items-center gap-2">
                                <svg class="w-4 h-4 text-stone-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                Profil Saya
                            </a>
                            <div class="border-t border-stone-100 my-1"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 hover:bg-red-50 text-red-600 flex items-center gap-2 font-medium">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                    Keluar
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="font-medium hover:text-orange-200 transition">Masuk</a>
                    <span class="text-white/40">|</span>
                    <a href="{{ route('register') }}" class="font-medium hover:text-orange-200 transition">Daftar</a>
                @endauth
            </div>
        </div>
    </div>

    <!-- Main Header Bar -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3">
        <div class="flex items-center justify-between gap-3 md:gap-8">
            <!-- Mobile Hamburger Toggle + Logo -->
            <div class="flex items-center gap-2">
                <button 
                    type="button" 
                    @click="mobileMenuOpen = true" 
                    class="md:hidden p-2 -ml-2 text-white hover:text-orange-200 focus:outline-none rounded-lg hover:bg-white/10 transition"
                    aria-label="Buka Menu Navigasi"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <a href="{{ url('/') }}" class="shrink-0 flex items-center group">
                    <x-application-logo />
                </a>
            </div>

            <!-- Search Bar -->
            <div class="flex-1 max-w-3xl">
                <form action="{{ route('products.index') }}" method="GET" class="relative flex items-center bg-white rounded-xl p-1 shadow-inner focus-within:ring-2 focus-within:ring-white">
                    <input
                        type="text"
                        name="q"
                        value="{{ request('q') }}"
                        placeholder="Cari makanan lezat, layanan jasa, toko..."
                        class="w-full px-3.5 py-1.5 md:py-2 text-xs md:text-sm text-stone-900 placeholder-stone-400 border-0 focus:ring-0 focus:outline-none bg-transparent"
                    />
                    <button type="submit" class="bg-primary hover:bg-primary-hover text-white px-4 md:px-6 py-1.5 md:py-2 rounded-lg transition-colors flex items-center justify-center font-medium shadow-sm shrink-0" aria-label="Cari">
                        <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </button>
                </form>
                <!-- Quick Keywords Bar (Desktop) -->
                <div class="hidden md:flex items-center gap-2.5 text-xs text-white/85 mt-1.5 px-1 overflow-hidden">
                    <span class="font-semibold text-orange-200">Populer:</span>
                    <a href="{{ route('products.index', ['q' => 'Nasi']) }}" class="hover:text-white transition">Nasi Kotak</a>
                    <a href="{{ route('products.index', ['q' => 'Kue']) }}" class="hover:text-white transition">Kue & Bakery</a>
                    <a href="{{ route('products.index', ['q' => 'Kopi']) }}" class="hover:text-white transition">Kopi & Minuman</a>
                    <a href="{{ route('products.index', ['q' => 'Desain']) }}" class="hover:text-white transition">Jasa Desain</a>
                    <a href="{{ route('products.index', ['q' => 'Catering']) }}" class="hover:text-white transition">Catering Harian</a>
                </div>
            </div>

            <!-- Header Actions: Wishlist & Cart -->
            @php
                $wishlistBadgeCount = Auth::check() ? (int) Auth::user()->wishlists()->count() : 0;
                $cartBadgeCount = Auth::check() ? (int) (Auth::user()->cart?->items()->sum('quantity') ?? 0) : 0;
            @endphp
            <div class="flex items-center space-x-1 sm:space-x-3 shrink-0">
                <!-- Wishlist -->
                <a href="{{ route('wishlist.index') }}" class="relative p-2 text-white hover:text-orange-200 transition rounded-lg hover:bg-white/10" title="Wishlist & Favorit Saya" aria-label="Wishlist">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                    @if ($wishlistBadgeCount > 0)
                        <span class="absolute top-1 right-1 bg-rose-500 text-white text-[11px] font-bold rounded-full h-4 min-w-[1rem] px-1 flex items-center justify-center shadow">
                            {{ $wishlistBadgeCount }}
                        </span>
                    @endif
                </a>

                <!-- Cart -->
                <a href="{{ route('cart.index') }}" class="relative p-2 text-white hover:text-orange-200 transition rounded-lg hover:bg-white/10" title="Keranjang Belanja" aria-label="Keranjang">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    @if ($cartBadgeCount > 0)
                        <span class="absolute top-1 right-1 bg-amber-300 text-stone-950 text-[11px] font-black rounded-full h-4 min-w-[1rem] px-1 flex items-center justify-center shadow">
                            {{ $cartBadgeCount }}
                        </span>
                    @endif
                </a>
            </div>
        </div>
    </div>

    <!-- Mobile Slide-over Drawer -->
    <div 
        x-show="mobileMenuOpen" 
        x-cloak 
        class="relative z-50 md:hidden"
        aria-modal="true"
    >
        <!-- Backdrop Blur Overlay -->
        <div 
            x-show="mobileMenuOpen"
            x-transition:enter="transition-opacity ease-linear duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="mobileMenuOpen = false" 
            class="fixed inset-0 bg-stone-950/60 backdrop-blur-sm"
        ></div>

        <!-- Drawer Panel -->
        <div class="fixed inset-y-0 left-0 max-w-full flex">
            <div 
                x-show="mobileMenuOpen"
                x-transition:enter="transition ease-in-out duration-300 transform"
                x-transition:enter-start="-translate-x-full"
                x-transition:enter-end="translate-x-0"
                x-transition:leave="transition ease-in-out duration-300 transform"
                x-transition:leave-start="translate-x-0"
                x-transition:leave-end="-translate-x-full"
                class="w-screen max-w-xs bg-white text-stone-800 shadow-2xl flex flex-col"
            >
                <!-- Drawer Header -->
                <div class="p-4 bg-gradient-to-r from-secondary to-primary text-white flex items-center justify-between shadow-sm">
                    <a href="{{ url('/') }}" @click="mobileMenuOpen = false" class="flex items-center">
                        <x-application-logo />
                    </a>
                    <button 
                        type="button" 
                        @click="mobileMenuOpen = false" 
                        class="p-2 -mr-2 text-white/80 hover:text-white rounded-lg hover:bg-white/10 transition"
                        aria-label="Tutup Menu"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- User Section in Drawer -->
                <div class="p-4 bg-stone-50 border-b border-stone-200">
                    @auth
                        <div class="flex items-center space-x-3">
                            <span class="w-10 h-10 rounded-full bg-secondary text-white font-bold text-base flex items-center justify-center shrink-0 shadow">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-bold text-stone-900 truncate">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-stone-500 truncate">{{ Auth::user()->email }}</p>
                                <span class="inline-block mt-1 text-[11px] font-semibold px-2 py-0.5 rounded-full {{ Auth::user()->isAdmin() ? 'bg-purple-100 text-purple-700' : (Auth::user()->isSeller() ? 'bg-teal-100 text-teal-800' : 'bg-stone-200 text-stone-700') }}">
                                    {{ ucfirst(Auth::user()->role) }}
                                </span>
                            </div>
                        </div>
                    @else
                        <div class="space-y-2">
                            <p class="text-xs text-stone-600">Selamat datang di TokoKu! Masuk untuk memesan.</p>
                            <div class="grid grid-cols-2 gap-2">
                                <a href="{{ route('login') }}" class="w-full text-center py-2 px-3 bg-white border border-stone-300 rounded-lg text-xs font-semibold text-stone-700 hover:bg-stone-50 shadow-sm transition">
                                    Masuk
                                </a>
                                <a href="{{ route('register') }}" class="w-full text-center py-2 px-3 bg-primary rounded-lg text-xs font-semibold text-white hover:bg-primary-hover shadow-sm transition">
                                    Daftar
                                </a>
                            </div>
                        </div>
                    @endauth
                </div>

                <!-- Navigation Links List -->
                <div class="flex-1 overflow-y-auto px-3 py-4 space-y-1 text-sm">
                    <a href="{{ url('/') }}" @click="mobileMenuOpen = false" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-stone-700 hover:bg-stone-100 transition {{ request()->is('/') ? 'bg-orange-50 text-primary font-semibold' : '' }}">
                        <svg class="w-5 h-5 text-stone-400 group-hover:text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        <span>Beranda</span>
                    </a>
                    <a href="{{ route('products.index') }}" @click="mobileMenuOpen = false" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-stone-700 hover:bg-stone-100 transition {{ request()->is('produk*') || request()->is('kategori*') ? 'bg-orange-50 text-primary font-semibold' : '' }}">
                        <svg class="w-5 h-5 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                        <span>Semua Produk & Jasa</span>
                    </a>
                    <a href="{{ route('cart.index') }}" @click="mobileMenuOpen = false" class="flex items-center justify-between px-3 py-2.5 rounded-lg text-stone-700 hover:bg-stone-100 transition {{ request()->is('keranjang*') ? 'bg-orange-50 text-primary font-semibold' : '' }}">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            <span>Keranjang Belanja</span>
                        </div>
                        @if($cartBadgeCount > 0)
                            <span class="bg-primary text-white text-xs font-bold px-2 py-0.5 rounded-full">{{ $cartBadgeCount }}</span>
                        @endif
                    </a>
                    <a href="{{ route('wishlist.index') }}" @click="mobileMenuOpen = false" class="flex items-center justify-between px-3 py-2.5 rounded-lg text-stone-700 hover:bg-stone-100 transition {{ request()->is('wishlist*') ? 'bg-orange-50 text-primary font-semibold' : '' }}">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                            <span>Wishlist & Favorit</span>
                        </div>
                        @if($wishlistBadgeCount > 0)
                            <span class="bg-rose-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">{{ $wishlistBadgeCount }}</span>
                        @endif
                    </a>
                    @auth
                        <a href="{{ route('orders.index') }}" @click="mobileMenuOpen = false" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-stone-700 hover:bg-stone-100 transition {{ request()->is('pesanan*') ? 'bg-orange-50 text-primary font-semibold' : '' }}">
                            <svg class="w-5 h-5 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            <span>Pesanan Saya</span>
                        </a>
                        <a href="{{ route('addresses.index') }}" @click="mobileMenuOpen = false" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-stone-700 hover:bg-stone-100 transition {{ request()->is('alamat*') ? 'bg-orange-50 text-primary font-semibold' : '' }}">
                            <svg class="w-5 h-5 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span>Alamat Saya</span>
                        </a>
                        <a href="{{ route('profile.edit') }}" @click="mobileMenuOpen = false" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-stone-700 hover:bg-stone-100 transition {{ request()->is('profile*') ? 'bg-orange-50 text-primary font-semibold' : '' }}">
                            <svg class="w-5 h-5 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            <span>Profil Akun</span>
                        </a>

                        <div class="pt-2 pb-1 border-t border-stone-200">
                            <span class="px-3 text-[11px] font-bold uppercase tracking-wider text-stone-400">Merchant & Kelola</span>
                        </div>
                        @if(Auth::user()->isSeller() && Auth::user()->store)
                            <a href="{{ route('seller.dashboard') }}" @click="mobileMenuOpen = false" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-secondary font-semibold hover:bg-teal-50 transition">
                                <svg class="w-5 h-5 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                <span>Dashboard Penjual</span>
                            </a>
                        @else
                            <a href="{{ route('seller.register') }}" @click="mobileMenuOpen = false" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-primary font-semibold hover:bg-orange-50 transition">
                                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                <span>Mulai Buka Toko</span>
                            </a>
                        @endif
                        @if(Auth::user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" @click="mobileMenuOpen = false" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-purple-700 font-bold hover:bg-purple-50 transition">
                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <span>Panel Admin</span>
                            </a>
                        @endif
                    @endauth
                </div>

                <!-- Drawer Footer / Logout -->
                @auth
                    <div class="p-3 border-t border-stone-200">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full flex items-center justify-center gap-2 py-2.5 px-4 text-sm font-semibold text-red-600 bg-red-50 hover:bg-red-100 rounded-lg transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                Keluar dari Akun
                            </button>
                        </form>
                    </div>
                @endauth
            </div>
        </div>
    </div>

    <!-- Mobile Sticky Bottom Navigation Bar (md:hidden) -->
    <nav class="md:hidden fixed bottom-0 inset-x-0 bg-white/95 backdrop-blur border-t border-stone-200 z-50 shadow-lg px-2 py-1">
        <div class="grid grid-cols-5 items-center">
            <!-- 1. Beranda -->
            <a href="{{ url('/') }}" class="flex flex-col items-center justify-center py-1 min-h-[48px] {{ request()->is('/') ? 'text-primary' : 'text-stone-500 hover:text-stone-700' }} transition">
                <svg class="w-5 h-5 {{ request()->is('/') ? 'stroke-[2.5]' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span class="text-xs {{ request()->is('/') ? 'font-bold' : 'font-medium' }} mt-0.5">Beranda</span>
            </a>

            <!-- 2. Kategori / Katalog -->
            <a href="{{ route('products.index') }}" class="flex flex-col items-center justify-center py-1 min-h-[48px] {{ request()->is('produk*') || request()->is('kategori*') ? 'text-primary' : 'text-stone-500 hover:text-stone-700' }} transition">
                <svg class="w-5 h-5 {{ request()->is('produk*') || request()->is('kategori*') ? 'stroke-[2.5]' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                </svg>
                <span class="text-xs {{ request()->is('produk*') || request()->is('kategori*') ? 'font-bold' : 'font-medium' }} mt-0.5">Kategori</span>
            </a>

            <!-- 3. Keranjang -->
            <a href="{{ route('cart.index') }}" class="flex flex-col items-center justify-center py-1 min-h-[48px] relative {{ request()->is('keranjang*') ? 'text-primary' : 'text-stone-500 hover:text-stone-700' }} transition">
                <div class="relative">
                    <svg class="w-5 h-5 {{ request()->is('keranjang*') ? 'stroke-[2.5]' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    @if($cartBadgeCount > 0)
                        <span class="absolute -top-1.5 -right-2.5 bg-primary text-white text-[10px] font-black rounded-full h-4 min-w-[1rem] px-1 flex items-center justify-center shadow">
                            {{ $cartBadgeCount }}
                        </span>
                    @endif
                </div>
                <span class="text-xs {{ request()->is('keranjang*') ? 'font-bold' : 'font-medium' }} mt-0.5">Keranjang</span>
            </a>

            <!-- 4. Pesanan -->
            <a href="{{ Auth::check() ? route('orders.index') : route('login') }}" class="flex flex-col items-center justify-center py-1 min-h-[48px] {{ request()->is('pesanan*') ? 'text-primary' : 'text-stone-500 hover:text-stone-700' }} transition">
                <svg class="w-5 h-5 {{ request()->is('pesanan*') ? 'stroke-[2.5]' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <span class="text-xs {{ request()->is('pesanan*') ? 'font-bold' : 'font-medium' }} mt-0.5">Pesanan</span>
            </a>

            <!-- 5. Akun -->
            <a href="{{ Auth::check() ? route('dashboard') : route('login') }}" class="flex flex-col items-center justify-center py-1 min-h-[48px] {{ request()->is('profile*') || request()->is('dashboard') || request()->is('login') ? 'text-primary' : 'text-stone-500 hover:text-stone-700' }} transition">
                <svg class="w-5 h-5 {{ request()->is('profile*') || request()->is('dashboard') ? 'stroke-[2.5]' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <span class="text-xs {{ request()->is('profile*') || request()->is('dashboard') ? 'font-bold' : 'font-medium' }} mt-0.5">Akun</span>
            </a>
        </div>
    </nav>
</header>
