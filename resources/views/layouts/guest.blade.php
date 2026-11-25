<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'TokoKu') }} - Masuk / Daftar</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700|plus-jakarta-sans:600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-stone-900 antialiased bg-surface">
        <!-- Top Simple Header -->
        <div class="bg-gradient-to-r from-secondary to-primary py-4 px-6 shadow-sm">
            <div class="max-w-7xl mx-auto flex items-center justify-between">
                <a href="/" class="flex items-center">
                    <x-application-logo />
                </a>
                <a href="/" class="text-white text-xs hover:text-emerald-200 transition font-medium flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Kembali ke Beranda
                </a>
            </div>
        </div>

        <div class="min-h-[calc(100vh-140px)] flex flex-col sm:justify-center items-center py-10 px-4">
            <div class="w-full sm:max-w-md bg-white shadow-lg rounded-2xl p-8 border border-gray-100">
                {{ $slot }}
            </div>
        </div>

        <footer class="text-center py-4 text-xs text-gray-500 border-t border-gray-200 bg-white">
            &copy; {{ date('Y') }} {{ config('app.name', 'TokoKu') }}. Hak Cipta Dilindungi Undang-Undang.
        </footer>
    </body>
</html>
