<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'TokoKu') }} - Marketplace Terlengkap & Terpercaya</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700|plus-jakarta-sans:600,700,800&display=swap" rel="stylesheet" />

        <!-- Styles & Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-surface text-stone-800 flex flex-col min-h-screen pb-16 md:pb-0">
        <!-- Shopee-style Gradient Navigation -->
        @include('layouts.navigation')

        <!-- Flash Session Messages -->
        @if (session('success'))
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 w-full">
                <div class="bg-emerald-50 border-l-4 border-primary text-emerald-800 p-4 rounded-r-lg shadow-sm flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-primary" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 w-full">
                <div class="bg-red-50 border-l-4 border-red-500 text-red-800 p-4 rounded-r-lg shadow-sm flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                        <span>{{ session('error') }}</span>
                    </div>
                </div>
            </div>
        @endif

        <!-- Main Content -->
        <main class="flex-grow">
            {{ $slot }}
        </main>

        <!-- Marketplace Footer (Shopee Style) -->
        <footer class="bg-white border-t border-gray-200 mt-12 text-sm text-gray-600">
            <!-- Features Highlights -->
            <div class="border-b border-gray-100 bg-emerald-50/50 py-6">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-2 md:grid-cols-4 gap-6 text-center md:text-left">
                    <div class="flex items-center space-x-3 justify-center md:justify-start">
                        <div class="w-10 h-10 rounded-full bg-emerald-100 text-primary flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-900 text-xs sm:text-sm">100% Produk Original</h4>
                            <p class="text-[11px] text-gray-500">Jaminan uang kembali jika palsu</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3 justify-center md:justify-start">
                        <div class="w-10 h-10 rounded-full bg-blue-100 text-secondary flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-900 text-xs sm:text-sm">Pengiriman Cepat</h4>
                            <p class="text-[11px] text-gray-500">Dukungan ekspedisi terpercaya</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3 justify-center md:justify-start">
                        <div class="w-10 h-10 rounded-full bg-emerald-100 text-primary flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-900 text-xs sm:text-sm">Transaksi Aman</h4>
                            <p class="text-[11px] text-gray-500">Dana ditahan hingga barang diterima</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3 justify-center md:justify-start">
                        <div class="w-10 h-10 rounded-full bg-blue-100 text-secondary flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-900 text-xs sm:text-sm">Layanan 24/7</h4>
                            <p class="text-[11px] text-gray-500">Pusat bantuan pelanggan siap membantu</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Links -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 grid grid-cols-2 md:grid-cols-4 gap-8">
                <div>
                    <h5 class="text-xs font-bold uppercase tracking-wider text-gray-900 mb-3">Layanan Pelanggan</h5>
                    <ul class="space-y-2 text-xs">
                        <li><a href="#" class="hover:text-primary transition">Pusat Bantuan</a></li>
                        <li><a href="#" class="hover:text-primary transition">Cara Membeli</a></li>
                        <li><a href="#" class="hover:text-primary transition">Pengiriman & Pelacakan</a></li>
                        <li><a href="#" class="hover:text-primary transition">Pengembalian Barang & Dana</a></li>
                        <li><a href="#" class="hover:text-primary transition">Garansi {{ config('app.name', 'TokoKu') }}</a></li>
                    </ul>
                </div>
                <div>
                    <h5 class="text-xs font-bold uppercase tracking-wider text-gray-900 mb-3">Jelajahi {{ config('app.name', 'TokoKu') }}</h5>
                    <ul class="space-y-2 text-xs">
                        <li><a href="#" class="hover:text-primary transition">Tentang Kami</a></li>
                        <li><a href="#" class="hover:text-primary transition">Karir</a></li>
                        <li><a href="#" class="hover:text-primary transition">Kebijakan Privasi</a></li>
                        <li><a href="#" class="hover:text-primary transition">Syarat & Ketentuan</a></li>
                        <li><a href="#" class="hover:text-primary transition">Mitra Penjual</a></li>
                    </ul>
                </div>
                <div>
                    <h5 class="text-xs font-bold uppercase tracking-wider text-gray-900 mb-3">Metode Pembayaran</h5>
                    <div class="grid grid-cols-3 gap-2">
                        <span class="bg-gray-100 p-2 text-center rounded text-[11px] font-bold text-gray-700">BCA</span>
                        <span class="bg-gray-100 p-2 text-center rounded text-[11px] font-bold text-gray-700">Mandiri</span>
                        <span class="bg-gray-100 p-2 text-center rounded text-[11px] font-bold text-gray-700">BRI</span>
                        <span class="bg-gray-100 p-2 text-center rounded text-[11px] font-bold text-gray-700">BNI</span>
                        <span class="bg-gray-100 p-2 text-center rounded text-[11px] font-bold text-gray-700">QRIS</span>
                        <span class="bg-gray-100 p-2 text-center rounded text-[11px] font-bold text-gray-700">COD</span>
                    </div>
                    <h5 class="text-xs font-bold uppercase tracking-wider text-gray-900 mt-4 mb-2">Jasa Pengiriman</h5>
                    <div class="flex flex-wrap gap-2 text-[11px] font-semibold text-gray-600">
                        <span class="bg-gray-100 px-2 py-1 rounded">JNE</span>
                        <span class="bg-gray-100 px-2 py-1 rounded">J&T</span>
                        <span class="bg-gray-100 px-2 py-1 rounded">SiCepat</span>
                        <span class="bg-gray-100 px-2 py-1 rounded">GoSend</span>
                    </div>
                </div>
                <div>
                    <h5 class="text-xs font-bold uppercase tracking-wider text-gray-900 mb-3">Download Aplikasi</h5>
                    <p class="text-xs text-gray-500 mb-3">Nikmati belanja lebih mudah, diskon eksklusif dan promo gratis ongkir.</p>
                    <div class="space-y-2">
                        <div class="bg-gray-900 text-white rounded-lg p-2.5 flex items-center space-x-3 cursor-pointer hover:bg-gray-800 transition">
                            <span class="text-xs font-semibold">Google Play Store</span>
                        </div>
                        <div class="bg-gray-900 text-white rounded-lg p-2.5 flex items-center space-x-3 cursor-pointer hover:bg-gray-800 transition">
                            <span class="text-xs font-semibold">Apple App Store</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Copyright -->
            <div class="border-t border-gray-100 py-4 bg-gray-50 text-center text-xs text-gray-500">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row justify-between items-center gap-2">
                    <p>&copy; {{ date('Y') }} {{ config('app.name', 'TokoKu') }}. Hak Cipta Dilindungi Undang-Undang.</p>
                    <p>Negara: <span class="font-semibold text-gray-700">Indonesia</span></p>
                </div>
            </div>
        </footer>
    </body>
</html>
