<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? 'Seller Center' }} - TokoKu</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800,900&display=swap" rel="stylesheet" />

        <!-- Scripts & Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-[#F8FAFC] text-gray-800" x-data="{ sidebarOpen: false }">
        <div class="min-h-screen flex flex-col md:flex-row">
            <!-- Sidebar Desktop & Mobile Off-canvas -->
            <aside 
                :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'"
                class="fixed md:static inset-y-0 left-0 z-40 w-64 bg-slate-900 text-white flex flex-col transition-transform duration-200 ease-in-out shrink-0 shadow-xl md:shadow-none"
            >
                <!-- Brand Header -->
                <div class="h-16 px-6 bg-slate-950 flex items-center justify-between border-b border-slate-800">
                    <a href="{{ route('seller.dashboard') }}" class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-gradient-to-tr from-secondary to-primary flex items-center justify-center font-black text-white text-sm shadow">
                            TK
                        </div>
                        <div>
                            <span class="font-black text-white text-base tracking-tight">TokoKu</span>
                            <span class="text-[10px] bg-primary text-white font-bold px-1.5 py-0.2 rounded ml-1 uppercase">Seller</span>
                        </div>
                    </a>
                    <button @click="sidebarOpen = false" class="md:hidden text-gray-400 hover:text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Info Toko Singkat -->
                @php
                    $store = auth()->user()->store;
                @endphp
                <div class="p-4 mx-3 my-3 bg-slate-800/80 rounded-xl flex items-center gap-3 border border-slate-700/60">
                    <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-emerald-500 to-teal-600 text-white font-black text-base flex items-center justify-center shrink-0 shadow overflow-hidden">
                        @if ($store && $store->logo)
                            <img src="{{ $store->logo }}" alt="{{ $store->name }}" class="w-full h-full object-cover">
                        @else
                            {{ strtoupper(substr($store->name ?? 'T', 0, 1)) }}
                        @endif
                    </div>
                    <div class="truncate">
                        <h4 class="text-xs font-bold text-white truncate">{{ $store->name ?? 'Toko Saya' }}</h4>
                        <span class="text-[10px] text-emerald-400 flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> Toko Aktif
                        </span>
                    </div>
                </div>

                <!-- Menu Navigasi Penjual -->
                <nav class="flex-1 px-3 py-2 space-y-1 text-xs font-semibold overflow-y-auto">
                    <!-- Dashboard -->
                    <a href="{{ route('seller.dashboard') }}" 
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('seller.dashboard') ? 'bg-primary text-white shadow' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        <span>Dashboard Penjual</span>
                    </a>

                    <!-- Produk Saya -->
                    <a href="{{ route('seller.products.index') }}" 
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('seller.products.index') || request()->routeIs('seller.products.edit') ? 'bg-primary text-white shadow' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        <span>Produk Saya</span>
                    </a>

                    <!-- Tambah Produk Baru -->
                    <a href="{{ route('seller.products.create') }}" 
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('seller.products.create') ? 'bg-primary text-white shadow' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>Tambah Produk Baru</span>
                    </a>

                    <!-- Pesanan Penjual -->
                    <a href="{{ route('seller.orders.index') }}" 
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('seller.orders.*') ? 'bg-primary text-white shadow' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        <span>Kelola Pesanan</span>
                    </a>

                    <!-- Profil Toko -->
                    <a href="{{ route('seller.store.edit') }}" 
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('seller.store.*') ? 'bg-primary text-white shadow' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        <span>Pengaturan Toko</span>
                    </a>

                    <div class="pt-4 mt-4 border-t border-slate-800"></div>

                    <!-- Kembali ke TokoKu (Marketplace) -->
                    <a href="{{ route('home') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition">
                        <svg class="w-4 h-4 shrink-0 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        <span>Kembali ke TokoKu</span>
                    </a>
                </nav>

                <!-- Footer User Signout -->
                <div class="p-3 border-t border-slate-800 flex items-center justify-between text-xs">
                    <div class="flex items-center gap-2 truncate">
                        <div class="w-7 h-7 rounded-full bg-slate-800 text-white font-bold flex items-center justify-center shrink-0">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <span class="text-slate-300 truncate">{{ auth()->user()->name }}</span>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-slate-400 hover:text-red-400 p-1" title="Keluar">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        </button>
                    </form>
                </div>
            </aside>

            <!-- Backdrop Mobile -->
            <div 
                x-show="sidebarOpen" 
                @click="sidebarOpen = false" 
                x-cloak 
                class="fixed inset-0 bg-black/50 z-30 md:hidden"
            ></div>

            <!-- Main Content Area -->
            <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
                <!-- Top Navbar Dashboard -->
                <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-4 sm:px-8 shadow-sm">
                    <div class="flex items-center gap-3">
                        <button @click="sidebarOpen = !sidebarOpen" class="md:hidden p-2 rounded-lg text-gray-600 hover:bg-gray-100">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        </button>
                        <h1 class="text-base sm:text-lg font-black text-gray-900 truncate">
                            {{ $header ?? 'Seller Center' }}
                        </h1>
                    </div>

                    <div class="flex items-center gap-3">
                        @if ($store && $store->slug)
                            <a href="{{ route('stores.show', $store->slug) }}" target="_blank"
                                class="hidden sm:inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border border-gray-200 hover:border-primary text-gray-600 hover:text-primary text-xs font-semibold transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                <span>Lihat Toko Publik</span>
                            </a>
                        @endif

                        <span class="text-xs text-gray-400 font-mono hidden md:inline">
                            {{ now()->translatedFormat('l, d F Y') }}
                        </span>
                    </div>
                </header>

                <!-- Page Content -->
                <main class="flex-1 overflow-y-auto p-4 sm:p-8">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
