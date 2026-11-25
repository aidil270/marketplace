<x-seller-layout title="Dashboard Penjual">
    <x-slot name="header">
        Dashboard Penjual
    </x-slot>

    <div class="space-y-6">
        <!-- Notification Alert -->
        @if (session('success'))
            <div class="p-4 bg-emerald-50 border-l-4 border-emerald-500 rounded-r-xl text-emerald-800 text-xs font-semibold flex items-center justify-between shadow-sm">
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if (session('info'))
            <div class="p-4 bg-blue-50 border-l-4 border-secondary rounded-r-xl text-blue-800 text-xs font-semibold flex items-center justify-between shadow-sm">
                <span>{{ session('info') }}</span>
            </div>
        @endif

        <!-- 1. Statistik Ringkas (5 Kartu Metrik) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <!-- Perlu Diproses -->
            <div class="bg-white p-5 rounded-2xl border border-stone-200 shadow-sm flex items-center gap-4 hover:-translate-y-0.5 hover:shadow-md transition-all duration-200">
                <div class="w-12 h-12 rounded-xl bg-orange-50 text-primary flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <span class="text-xs text-stone-500 font-medium block">Perlu Diproses</span>
                    <span class="text-xl font-extrabold text-stone-900 font-heading">{{ $processingOrdersCount }}</span>
                    <span class="text-xs text-primary font-semibold block">Siap dikirim</span>
                </div>
            </div>

            <!-- Sedang Dikirim -->
            <div class="bg-white p-5 rounded-2xl border border-stone-200 shadow-sm flex items-center gap-4 hover:-translate-y-0.5 hover:shadow-md transition-all duration-200">
                <div class="w-12 h-12 rounded-xl bg-teal-50 text-secondary flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
                </div>
                <div>
                    <span class="text-xs text-stone-500 font-medium block">Sedang Dikirim</span>
                    <span class="text-xl font-extrabold text-stone-900 font-heading">{{ $shippedOrdersCount }}</span>
                    <span class="text-xs text-secondary font-semibold block">Dalam perjalanan</span>
                </div>
            </div>

            <!-- Total Pendapatan -->
            <div class="bg-white p-5 rounded-2xl border border-stone-200 shadow-sm flex items-center gap-4 hover:-translate-y-0.5 hover:shadow-md transition-all duration-200">
                <div class="w-12 h-12 rounded-xl bg-orange-50 text-primary flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <span class="text-xs text-stone-500 font-medium block">Total Pendapatan</span>
                    <span class="text-base sm:text-lg font-extrabold text-primary font-heading">Rp{{ number_format($totalRevenue, 0, ',', '.') }}</span>
                    <span class="text-xs text-stone-400 font-medium block">Pesanan aktif & selesai</span>
                </div>
            </div>

            <!-- Produk Aktif -->
            <div class="bg-white p-5 rounded-2xl border border-stone-200 shadow-sm flex items-center gap-4 hover:-translate-y-0.5 hover:shadow-md transition-all duration-200">
                <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
                <div>
                    <span class="text-xs text-stone-500 font-medium block">Produk Aktif</span>
                    <span class="text-xl font-extrabold text-stone-900 font-heading">{{ $activeProductsCount }}</span>
                    <span class="text-xs text-purple-600 font-semibold block">Siap dijual</span>
                </div>
            </div>

            <!-- Rating Toko -->
            <div class="bg-white p-5 rounded-2xl border border-stone-200 shadow-sm flex items-center gap-4 hover:-translate-y-0.5 hover:shadow-md transition-all duration-200">
                <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-500 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                </div>
                <div>
                    <span class="text-xs text-stone-500 font-medium block">Rating Toko</span>
                    <span class="text-xl font-extrabold text-stone-900 font-heading">{{ $averageRating > 0 ? $averageRating : '5.0' }} <span class="text-xs text-amber-500 font-bold">★</span></span>
                    <span class="text-xs text-stone-400 font-medium block">{{ $reviewsCount }} ulasan</span>
                </div>
            </div>
        </div>

        <!-- 2. Tren Penjualan 7 Hari Terakhir & Peringatan Stok -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Grafik Penjualan 7 Hari (HTML/Tailwind Bar Chart Murni) -->
            <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="font-bold text-gray-900 text-sm">Tren Penjualan 7 Hari Terakhir</h3>
                        <p class="text-xs text-gray-400">Total pendapatan harian dari pesanan berhasil</p>
                    </div>
                    <span class="text-xs font-bold text-primary bg-emerald-50 px-2.5 py-1 rounded-full">
                        7 Hari Terakhir
                    </span>
                </div>

                <!-- Bar Chart Bars -->
                <div class="h-56 flex items-end justify-between gap-2 pt-6 pb-2 border-b border-gray-100">
                    @foreach ($salesChart as $point)
                        <div class="flex-1 flex flex-col items-center h-full justify-end group">
                            <!-- Tooltip On Hover -->
                            <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-150 mb-2 px-2 py-1 bg-slate-900 text-white text-[10px] rounded shadow-md pointer-events-none whitespace-nowrap z-10 text-center">
                                <span class="font-bold block">Rp{{ number_format($point['revenue'], 0, ',', '.') }}</span>
                                <span class="text-slate-300">{{ $point['orders_count'] }} pesanan</span>
                            </div>

                            <!-- Bar Column -->
                            <div class="w-full max-w-[40px] bg-emerald-100 group-hover:bg-primary transition-colors duration-200 rounded-t-lg relative"
                                style="height: {{ $point['percentage'] }}%;">
                                <div class="absolute inset-x-0 top-0 h-1 bg-primary rounded-t-lg"></div>
                            </div>

                            <!-- Day Label -->
                            <span class="text-[10px] font-semibold text-gray-500 mt-2 truncate w-full text-center">
                                {{ explode(',', $point['label'])[0] }}
                            </span>
                        </div>
                    @endforeach
                </div>
                <div class="flex items-center justify-between text-[11px] text-gray-400 mt-2">
                    <span>*Nilai berdasarkan akumulasi pesanan processing, shipped, dan completed</span>
                </div>
            </div>

            <!-- Peringatan Stok Menipis -->
            <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-red-500 animate-pulse"></span>
                            <h3 class="font-bold text-gray-900 text-sm">Peringatan Stok</h3>
                        </div>
                        <span class="text-xs bg-red-50 text-red-600 font-bold px-2 py-0.5 rounded-full">
                            Stok &le; 5
                        </span>
                    </div>
                    <p class="text-xs text-gray-500 mb-4">Segera perbarui stok produk berikut agar tidak kehabisan pesanan pembeli.</p>

                    @if ($lowStockProducts->isEmpty())
                        <div class="p-6 bg-emerald-50 rounded-xl text-center text-xs text-emerald-700">
                            <svg class="w-8 h-8 mx-auto text-emerald-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <p class="font-bold">Semua stok produk aman!</p>
                            <p class="text-[11px] text-emerald-600">Tidak ada produk dengan stok menipis saat ini.</p>
                        </div>
                    @else
                        <div class="space-y-3">
                            @foreach ($lowStockProducts as $prod)
                                <div class="flex items-center justify-between p-2.5 rounded-xl border {{ $prod->stock == 0 ? 'bg-red-50/50 border-red-200' : 'bg-amber-50/50 border-amber-200' }}">
                                    <div class="truncate mr-2">
                                        <h5 class="text-xs font-bold text-gray-800 truncate">{{ $prod->name }}</h5>
                                        <span class="text-[10px] text-gray-500">Rp{{ number_format($prod->price, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="flex items-center gap-2 shrink-0">
                                        <span class="px-2 py-0.5 rounded text-[11px] font-black {{ $prod->stock == 0 ? 'bg-red-500 text-white' : 'bg-amber-400 text-amber-950' }}">
                                            {{ $prod->stock }} unit
                                        </span>
                                        <a href="{{ route('seller.products.edit', $prod->id) }}" class="text-xs text-secondary hover:underline font-bold" title="Edit Stok">
                                            Edit
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="pt-4 mt-4 border-t border-gray-100 text-center">
                    <a href="{{ route('seller.products.index') }}" class="text-xs font-bold text-secondary hover:text-blue-700 transition">
                        Kelola Semua Stok Produk &rarr;
                    </a>
                </div>
            </div>
        </div>

        <!-- 3. Daftar 5 Pesanan Terbaru Toko -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-gray-900 text-sm">Pesanan Terbaru Toko</h3>
                    <p class="text-xs text-gray-400">5 transaksi terakhir yang masuk ke toko Anda</p>
                </div>
                <a href="{{ route('seller.orders.index') }}" class="text-xs font-bold text-secondary hover:underline flex items-center gap-1">
                    <span>Lihat Semua Pesanan</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>

            @if ($recentOrders->isEmpty())
                <div class="p-8 text-center text-gray-400">
                    <svg class="w-10 h-10 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    <p class="text-xs font-bold">Belum ada transaksi pesanan yang masuk ke toko Anda.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-gray-50 text-gray-500 uppercase text-[10px] tracking-wider border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-3">No. Invoice</th>
                                <th class="px-6 py-3">Pembeli</th>
                                <th class="px-6 py-3">Total Pesanan</th>
                                <th class="px-6 py-3">Status</th>
                                <th class="px-6 py-3">Waktu</th>
                                <th class="px-6 py-3 text-right">Aksi Cepat</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($recentOrders as $order)
                                <tr class="hover:bg-gray-50/80 transition">
                                    <td class="px-6 py-3.5 font-mono font-bold text-gray-800">
                                        {{ $order->invoice_number }}
                                    </td>
                                    <td class="px-6 py-3.5 font-medium text-gray-800">
                                        {{ $order->user->name ?? 'Pembeli' }}
                                    </td>
                                    <td class="px-6 py-3.5 font-black text-primary">
                                        Rp{{ number_format($order->final_amount, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-3.5">
                                        @switch($order->status)
                                            @case('processing')
                                                <span class="px-2.5 py-1 bg-amber-50 text-amber-700 font-bold rounded-full text-[10px]">
                                                    Perlu Diproses
                                                </span>
                                                @break
                                            @case('shipped')
                                                <span class="px-2.5 py-1 bg-blue-50 text-secondary font-bold rounded-full text-[10px]">
                                                    Sedang Dikirim
                                                </span>
                                                @break
                                            @case('completed')
                                                <span class="px-2.5 py-1 bg-emerald-50 text-primary font-bold rounded-full text-[10px]">
                                                    Selesai
                                                </span>
                                                @break
                                            @case('cancelled')
                                                <span class="px-2.5 py-1 bg-red-50 text-red-600 font-bold rounded-full text-[10px]">
                                                    Dibatalkan
                                                </span>
                                                @break
                                            @default
                                                <span class="px-2.5 py-1 bg-gray-100 text-gray-600 font-bold rounded-full text-[10px]">
                                                    Menunggu Bayar
                                                </span>
                                        @endswitch
                                    </td>
                                    <td class="px-6 py-3.5 text-gray-400 text-[11px]">
                                        {{ $order->created_at->diffForHumans() }}
                                    </td>
                                    <td class="px-6 py-3.5 text-right">
                                        <a href="{{ route('seller.orders.show', $order->invoice_number) }}" class="inline-flex items-center gap-1 px-3 py-1 bg-gray-100 hover:bg-primary hover:text-white text-gray-700 rounded-lg text-[11px] font-bold transition">
                                            Detail &rarr;
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-seller-layout>
