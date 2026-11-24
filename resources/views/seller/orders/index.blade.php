<x-seller-layout title="Kelola Pesanan Penjual">
    <x-slot name="header">
        Pesanan Toko
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

        <!-- 1. Tabs Status Pesanan -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-2 overflow-x-auto">
            <div class="flex items-center space-x-1 min-w-max text-xs font-bold">
                <!-- Semua -->
                <a href="{{ route('seller.orders.index', ['status' => 'all']) }}"
                    class="px-4 py-2.5 rounded-xl transition flex items-center gap-2 {{ $activeStatus === 'all' ? 'bg-primary text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100' }}">
                    <span>Semua</span>
                    <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $activeStatus === 'all' ? 'bg-white/20 text-white' : 'bg-gray-100 text-gray-600' }}">
                        {{ $counts['all'] }}
                    </span>
                </a>

                <!-- Perlu Diproses -->
                <a href="{{ route('seller.orders.index', ['status' => 'processing']) }}"
                    class="px-4 py-2.5 rounded-xl transition flex items-center gap-2 {{ $activeStatus === 'processing' ? 'bg-amber-500 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100' }}">
                    <span>Perlu Diproses</span>
                    <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $activeStatus === 'processing' ? 'bg-white/20 text-white' : 'bg-amber-100 text-amber-800' }}">
                        {{ $counts['processing'] }}
                    </span>
                </a>

                <!-- Sedang Dikirim -->
                <a href="{{ route('seller.orders.index', ['status' => 'shipped']) }}"
                    class="px-4 py-2.5 rounded-xl transition flex items-center gap-2 {{ $activeStatus === 'shipped' ? 'bg-secondary text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100' }}">
                    <span>Sedang Dikirim</span>
                    <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $activeStatus === 'shipped' ? 'bg-white/20 text-white' : 'bg-blue-100 text-blue-800' }}">
                        {{ $counts['shipped'] }}
                    </span>
                </a>

                <!-- Selesai -->
                <a href="{{ route('seller.orders.index', ['status' => 'completed']) }}"
                    class="px-4 py-2.5 rounded-xl transition flex items-center gap-2 {{ $activeStatus === 'completed' ? 'bg-emerald-600 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100' }}">
                    <span>Selesai</span>
                    <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $activeStatus === 'completed' ? 'bg-white/20 text-white' : 'bg-emerald-100 text-emerald-800' }}">
                        {{ $counts['completed'] }}
                    </span>
                </a>

                <!-- Dibatalkan -->
                <a href="{{ route('seller.orders.index', ['status' => 'cancelled']) }}"
                    class="px-4 py-2.5 rounded-xl transition flex items-center gap-2 {{ $activeStatus === 'cancelled' ? 'bg-red-500 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100' }}">
                    <span>Dibatalkan</span>
                    <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $activeStatus === 'cancelled' ? 'bg-white/20 text-white' : 'bg-red-100 text-red-800' }}">
                        {{ $counts['cancelled'] }}
                    </span>
                </a>

                <!-- Menunggu Pembayaran -->
                <a href="{{ route('seller.orders.index', ['status' => 'pending']) }}"
                    class="px-4 py-2.5 rounded-xl transition flex items-center gap-2 {{ $activeStatus === 'pending' ? 'bg-slate-700 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100' }}">
                    <span>Menunggu Bayar</span>
                    <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $activeStatus === 'pending' ? 'bg-white/20 text-white' : 'bg-gray-100 text-gray-600' }}">
                        {{ $counts['pending'] }}
                    </span>
                </a>
            </div>
        </div>

        <!-- 2. Search Toolbar -->
        <div class="bg-white rounded-2xl border border-gray-100 p-4 shadow-sm">
            <form method="GET" action="{{ route('seller.orders.index') }}" class="flex items-center gap-3">
                <input type="hidden" name="status" value="{{ $activeStatus }}">
                <div class="relative flex-1">
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nomor invoice atau nama pembeli..."
                        class="w-full text-xs border-gray-300 rounded-xl pl-9 focus:ring-primary focus:border-primary shadow-sm" />
                    <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <button type="submit" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-bold transition">
                    Cari
                </button>
                @if (request('q'))
                    <a href="{{ route('seller.orders.index', ['status' => $activeStatus]) }}" class="text-xs text-gray-400 hover:text-gray-700 font-semibold">
                        Reset
                    </a>
                @endif
            </form>
        </div>

        <!-- 3. Daftar Pesanan -->
        <div class="space-y-4">
            @if ($orders->isEmpty())
                <div class="bg-white rounded-2xl border border-gray-100 p-12 text-center text-gray-400 shadow-sm">
                    <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    <h4 class="text-sm font-bold text-gray-700">Tidak ada pesanan pada tab ini</h4>
                    <p class="text-xs text-gray-500 mt-1">Pesanan yang sesuai dengan status ini akan muncul di sini secara otomatis.</p>
                </div>
            @else
                @foreach ($orders as $order)
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                        <!-- Header Card Pesanan -->
                        <div class="px-6 py-3.5 bg-gray-50/80 border-b border-gray-100 flex flex-wrap items-center justify-between gap-3 text-xs">
                            <div class="flex items-center gap-3">
                                <span class="font-mono font-black text-gray-800">
                                    {{ $order->invoice_number }}
                                </span>
                                <span class="text-gray-300">|</span>
                                <span class="text-gray-500">{{ $order->created_at->translatedFormat('d M Y, H:i') }}</span>
                                <span class="text-gray-300">|</span>
                                <span class="font-medium text-gray-700 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    {{ $order->user->name ?? 'Pembeli' }}
                                </span>
                            </div>

                            <div>
                                @switch($order->status)
                                    @case('processing')
                                        <span class="px-2.5 py-1 bg-amber-50 text-amber-700 font-bold rounded-full text-[10px] flex items-center gap-1">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                            Perlu Diproses (Sudah Dibayar)
                                        </span>
                                        @break
                                    @case('shipped')
                                        <span class="px-2.5 py-1 bg-blue-50 text-secondary font-bold rounded-full text-[10px] flex items-center gap-1">
                                            <span class="w-1.5 h-1.5 rounded-full bg-secondary"></span>
                                            Sedang Dikirim
                                        </span>
                                        @break
                                    @case('completed')
                                        <span class="px-2.5 py-1 bg-emerald-50 text-primary font-bold rounded-full text-[10px] flex items-center gap-1">
                                            <span class="w-1.5 h-1.5 rounded-full bg-primary"></span>
                                            Pesanan Selesai
                                        </span>
                                        @break
                                    @case('cancelled')
                                        <span class="px-2.5 py-1 bg-red-50 text-red-600 font-bold rounded-full text-[10px]">
                                            Pesanan Dibatalkan
                                        </span>
                                        @break
                                    @default
                                        <span class="px-2.5 py-1 bg-gray-100 text-gray-600 font-bold rounded-full text-[10px]">
                                            Menunggu Pembayaran
                                        </span>
                                @endswitch
                            </div>
                        </div>

                        <!-- Daftar Item Pesanan -->
                        <div class="p-6 divide-y divide-gray-100">
                            @foreach ($order->items as $item)
                                <div class="py-3 first:pt-0 last:pb-0 flex items-center justify-between gap-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-12 h-12 rounded-xl bg-gray-100 border border-gray-200 overflow-hidden shrink-0">
                                            @if ($item->product?->primaryImage)
                                                <img src="{{ $item->product->primaryImage->image_path }}" alt="{{ $item->product_name }}" class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center text-gray-400">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <h5 class="text-xs font-bold text-gray-800 line-clamp-1">{{ $item->product_name }}</h5>
                                            @if ($item->variant_name)
                                                <span class="text-[10px] text-gray-500 bg-gray-100 px-1.5 py-0.5 rounded">Variasi: {{ $item->variant_name }}</span>
                                            @endif
                                            <div class="text-[11px] text-gray-500 mt-0.5">
                                                Rp{{ number_format($item->product_price, 0, ',', '.') }} &times; {{ $item->quantity }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-xs font-bold text-gray-800 block">
                                            Rp{{ number_format($item->subtotal, 0, ',', '.') }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Footer Card Pesanan -->
                        <div class="px-6 py-4 bg-gray-50/50 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                            <div class="flex items-center gap-4 text-xs">
                                <div>
                                    <span class="text-gray-500">Total Pesanan:</span>
                                    <span class="text-sm font-black text-primary ml-1">
                                        Rp{{ number_format($order->final_amount, 0, ',', '.') }}
                                    </span>
                                </div>
                                @if ($order->shipment && $order->shipment->tracking_number)
                                    <span class="text-[11px] text-gray-600 bg-blue-50 px-2 py-0.5 rounded font-mono">
                                        Resi: {{ $order->shipment->courier_name }} - {{ $order->shipment->tracking_number }}
                                    </span>
                                @endif
                            </div>

                            <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
                                <a href="{{ route('seller.orders.show', $order->invoice_number) }}" class="px-4 py-2 rounded-xl bg-white border border-gray-200 hover:border-primary text-gray-700 hover:text-primary text-xs font-bold transition">
                                    Lihat Detail & Kirim &rarr;
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach

                <!-- Pagination -->
                <div class="p-4">
                    {{ $orders->links() }}
                </div>
            @endif
        </div>
    </div>
</x-seller-layout>
