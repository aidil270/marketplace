<x-admin-layout title="Dashboard Admin">
    <x-slot name="header">
        Dashboard Administrator
    </x-slot>

    <div class="space-y-6">
        <!-- Notification Alert -->
        @if (session('success'))
            <div class="p-4 bg-emerald-50 border-l-4 border-emerald-500 rounded-r-xl text-emerald-800 text-xs font-semibold flex items-center justify-between shadow-sm">
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="p-4 bg-red-50 border-l-4 border-red-500 rounded-r-xl text-red-800 text-xs font-semibold">
                {{ session('error') }}
            </div>
        @endif

        <!-- 1. Statistik Utama (Kartu Metrik) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Total Pengguna -->
            <div class="bg-white p-5 rounded-2xl border border-stone-200 shadow-sm flex items-center gap-4 hover:-translate-y-0.5 hover:shadow-md transition-all duration-200">
                <div class="w-12 h-12 rounded-xl bg-teal-50 text-secondary flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </div>
                <div>
                    <span class="text-xs text-stone-500 font-medium block">Total Pengguna</span>
                    <span class="text-xl font-extrabold text-stone-900 font-heading">{{ number_format($totalUsers) }}</span>
                    <span class="text-xs text-stone-400">Pembeli, Penjual, Admin</span>
                </div>
            </div>

            <!-- Total Toko -->
            <div class="bg-white p-5 rounded-2xl border border-stone-200 shadow-sm flex items-center gap-4 hover:-translate-y-0.5 hover:shadow-md transition-all duration-200">
                <div class="w-12 h-12 rounded-xl bg-orange-50 text-primary flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                <div>
                    <span class="text-xs text-stone-500 font-medium block">Total Toko</span>
                    <span class="text-xl font-extrabold text-stone-900 font-heading">{{ number_format($totalStores) }}</span>
                    <span class="text-xs text-primary font-semibold">Merchant terdaftar</span>
                </div>
            </div>

            <!-- Total Produk Aktif -->
            <div class="bg-white p-5 rounded-2xl border border-stone-200 shadow-sm flex items-center gap-4 hover:-translate-y-0.5 hover:shadow-md transition-all duration-200">
                <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
                <div>
                    <span class="text-xs text-stone-500 font-medium block">Produk Aktif</span>
                    <span class="text-xl font-extrabold text-stone-900 font-heading">{{ number_format($totalActiveProducts) }}</span>
                    <span class="text-xs text-purple-600 font-semibold">Tampil di katalog</span>
                </div>
            </div>

            <!-- Total Nilai Transaksi Paid -->
            <div class="bg-white p-5 rounded-2xl border border-stone-200 shadow-sm flex items-center gap-4 hover:-translate-y-0.5 hover:shadow-md transition-all duration-200">
                <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <span class="text-xs text-stone-500 font-medium block">Nilai Transaksi (Paid)</span>
                    <span class="text-base sm:text-lg font-extrabold text-primary font-heading">Rp{{ number_format($totalPaidRevenue, 0, ',', '.') }}</span>
                    <span class="text-xs text-stone-400 font-medium block">Pembayaran sukses</span>
                </div>
            </div>
        </div>

        <!-- Kartu Pesanan Ringkas -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white p-4 rounded-xl border border-stone-200 shadow-sm flex items-center justify-between hover:shadow-md transition">
                <div>
                    <span class="text-xs text-stone-400 block font-medium">Total Pesanan Keseluruhan</span>
                    <span class="text-lg font-extrabold text-stone-900 font-heading">{{ number_format($totalOrders) }}</span>
                </div>
                <span class="p-2 bg-stone-100 rounded-lg text-stone-600 text-xs font-bold">Semua Status</span>
            </div>
            <div class="bg-white p-4 rounded-xl border border-stone-200 shadow-sm flex items-center justify-between hover:shadow-md transition">
                <div>
                    <span class="text-xs text-amber-600 block font-medium">Menunggu Pembayaran</span>
                    <span class="text-lg font-extrabold text-amber-600 font-heading">{{ number_format($pendingOrders) }}</span>
                </div>
                <span class="p-2 bg-amber-50 rounded-lg text-amber-700 text-xs font-bold">Pending</span>
            </div>
            <div class="bg-white p-4 rounded-xl border border-stone-200 shadow-sm flex items-center justify-between hover:shadow-md transition">
                <div>
                    <span class="text-xs text-secondary block font-medium">Pesanan Sedang Diproses</span>
                    <span class="text-lg font-extrabold text-secondary font-heading">{{ number_format($processingOrders) }}</span>
                </div>
                <span class="p-2 bg-blue-50 rounded-lg text-secondary text-xs font-bold">Processing</span>
            </div>
        </div>

        <!-- 2. Grafik Tren Transaksi 7 Hari Terakhir -->
        <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="font-bold text-gray-900 text-sm">Tren Transaksi Masuk (7 Hari Terakhir)</h3>
                    <p class="text-xs text-gray-400">Total nominal pembayaran sukses (paid) yang diterima gateway</p>
                </div>
                <span class="text-xs font-bold text-primary bg-emerald-50 px-2.5 py-1 rounded-full">
                    7 Hari Terakhir
                </span>
            </div>

            <!-- Bar Chart (Pure CSS/Tailwind) -->
            <div class="h-56 flex items-end justify-between gap-3 pt-6 pb-2 border-b border-gray-100">
                @foreach ($salesChart as $point)
                    <div class="flex-1 flex flex-col items-center h-full justify-end group">
                        <!-- Tooltip On Hover -->
                        <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-150 mb-2 px-2 py-1 bg-slate-900 text-white text-[10px] rounded shadow-md pointer-events-none whitespace-nowrap z-10 text-center">
                            <span class="font-bold block">Rp{{ number_format($point['revenue'], 0, ',', '.') }}</span>
                            <span class="text-slate-300">{{ $point['count'] }} transaksi</span>
                        </div>

                        <!-- Bar Column -->
                        <div class="w-full max-w-[44px] bg-emerald-100 group-hover:bg-primary transition-colors duration-200 rounded-t-lg relative"
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
        </div>

        <!-- 3. Tabel 5 Pesanan Terbaru & 5 Toko Terbaru -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- 5 Pesanan Terbaru -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden flex flex-col justify-between">
                <div>
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                        <h4 class="font-bold text-gray-900 text-xs uppercase tracking-wider">5 Pesanan Terbaru</h4>
                        <span class="text-[11px] text-gray-400">Semua Toko</span>
                    </div>
                    @if ($recentOrders->isEmpty())
                        <div class="p-8 text-center text-xs text-gray-400">Belum ada transaksi.</div>
                    @else
                        <div class="divide-y divide-gray-100 text-xs">
                            @foreach ($recentOrders as $order)
                                <div class="p-4 flex items-center justify-between hover:bg-gray-50/60 transition">
                                    <div>
                                        <span class="font-mono font-bold text-gray-800">{{ $order->invoice_number }}</span>
                                        <div class="text-[11px] text-gray-500 mt-0.5">
                                            Pembeli: <span class="font-medium text-gray-700">{{ $order->user->name ?? 'User' }}</span> &bull; Toko: <span class="font-medium text-gray-700">{{ $order->store->name ?? '-' }}</span>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="font-bold text-primary block">Rp{{ number_format($order->final_amount, 0, ',', '.') }}</span>
                                        <span class="text-[10px] text-gray-400">{{ $order->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- 5 Toko Terbaru -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden flex flex-col justify-between">
                <div>
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                        <h4 class="font-bold text-gray-900 text-xs uppercase tracking-wider">5 Toko Terbaru Terdaftar</h4>
                        <a href="{{ route('admin.stores.index') }}" class="text-[11px] text-secondary hover:underline font-bold">Lihat Semua</a>
                    </div>
                    @if ($recentStores->isEmpty())
                        <div class="p-8 text-center text-xs text-gray-400">Belum ada toko terdaftar.</div>
                    @else
                        <div class="divide-y divide-gray-100 text-xs">
                            @foreach ($recentStores as $st)
                                <div class="p-4 flex items-center justify-between hover:bg-gray-50/60 transition">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-emerald-100 text-primary font-bold text-xs flex items-center justify-center shrink-0">
                                            {{ strtoupper(substr($st->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <span class="font-bold text-gray-800 block">{{ $st->name }}</span>
                                            <span class="text-[11px] text-gray-400">Pemilik: {{ $st->user->name ?? '-' }} ({{ $st->city }})</span>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $st->is_active ? 'bg-emerald-50 text-primary' : 'bg-red-50 text-red-600' }}">
                                            {{ $st->is_active ? 'Aktif' : 'Ditangguhkan' }}
                                        </span>
                                        <span class="text-[10px] text-gray-400 block mt-0.5">{{ $st->products_count }} produk</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
