<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between border-b border-gray-200 pb-4">
            <div class="flex items-center gap-2">
                <div class="w-2.5 h-6 bg-secondary rounded-full"></div>
                <h1 class="text-xl font-black text-gray-900 tracking-tight">Pesanan Saya</h1>
            </div>
            <a href="{{ route('home') }}" class="text-xs font-bold text-primary hover:text-emerald-700 flex items-center gap-1">
                Belanja Lagi &rarr;
            </a>
        </div>

        <!-- Filter Status Tabs (Shopee Style) -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-x-auto">
            <div class="flex items-center min-w-max border-b border-gray-100 text-xs font-bold">
                @php
                    $tabs = [
                        'all' => 'Semua',
                        'pending' => 'Menunggu Bayar',
                        'processing' => 'Diproses',
                        'shipped' => 'Dikirim',
                        'completed' => 'Selesai',
                        'cancelled' => 'Dibatalkan',
                    ];
                @endphp
                @foreach ($tabs as $k => $label)
                    <a
                        href="{{ route('orders.index', ['status' => $k]) }}"
                        class="px-5 py-3.5 flex items-center gap-1.5 border-b-2 transition {{ $status === $k ? 'border-primary text-primary bg-emerald-50/20' : 'border-transparent text-gray-500 hover:text-gray-900' }}"
                    >
                        <span>{{ $label }}</span>
                        @if (($counts[$k] ?? 0) > 0)
                            <span class="text-[10px] px-1.5 py-0.2 rounded-full {{ $status === $k ? 'bg-primary text-white' : 'bg-gray-100 text-gray-600' }}">
                                {{ $counts[$k] }}
                            </span>
                        @endif
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Orders List -->
        @if ($orders->isEmpty())
            <div class="bg-white rounded-2xl p-12 border border-gray-100 text-center space-y-4 max-w-md mx-auto shadow-sm">
                <div class="w-16 h-16 rounded-full bg-gray-100 text-gray-400 mx-auto flex items-center justify-center">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <h3 class="text-sm font-bold text-gray-800">Belum Ada Pesanan</h3>
                <p class="text-xs text-gray-500">Kamu belum memiliki riwayat pesanan dengan status ini.</p>
                <a href="{{ route('home') }}" class="inline-block px-5 py-2 bg-primary text-white font-bold text-xs rounded-xl shadow-sm">
                    Mulai Belanja
                </a>
            </div>
        @else
            <div class="space-y-4">
                @foreach ($orders as $order)
                    @php
                        $statusColors = [
                            'pending' => 'bg-amber-100 text-amber-800 border-amber-200',
                            'processing' => 'bg-blue-100 text-blue-800 border-blue-200',
                            'shipped' => 'bg-purple-100 text-purple-800 border-purple-200',
                            'completed' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                            'cancelled' => 'bg-red-100 text-red-800 border-red-200',
                        ];
                        $statusLabels = [
                            'pending' => 'Menunggu Pembayaran',
                            'processing' => 'Sedang Diproses',
                            'shipped' => 'Sedang Dikirim',
                            'completed' => 'Selesai',
                            'cancelled' => 'Dibatalkan',
                        ];
                    @endphp
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden hover:shadow-md transition">
                        <!-- Card Header -->
                        <div class="bg-gray-50/70 px-5 py-3 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs">
                            <div class="flex items-center gap-2">
                                <span class="bg-secondary text-white text-[9px] font-black px-1.5 py-0.5 rounded">Toko</span>
                                <span class="font-extrabold text-gray-900">{{ $order->store->name ?? 'Toko' }}</span>
                                <span class="text-gray-400">&bull;</span>
                                <span class="font-mono text-gray-500 font-semibold">{{ $order->invoice_number }}</span>
                                <span class="text-gray-400">&bull;</span>
                                <span class="text-gray-400">{{ $order->created_at->format('d M Y, H:i') }}</span>
                            </div>

                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold border w-max {{ $statusColors[$order->status] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ $statusLabels[$order->status] ?? ucfirst($order->status) }}
                            </span>
                        </div>

                        <!-- Items Preview -->
                        <div class="p-5 divide-y divide-gray-100">
                            @foreach ($order->items as $item)
                                <div class="flex items-center justify-between gap-4 py-3 first:pt-0 last:pb-0">
                                    <div class="flex items-center gap-3">
                                        <div class="w-14 h-14 rounded-xl bg-gray-50 border border-gray-100 overflow-hidden shrink-0 flex items-center justify-center">
                                            @if ($item->product && $item->product->primaryImage)
                                                <img src="{{ $item->product->primaryImage->image_path }}" alt="{{ $item->product_name }}" class="w-full h-full object-cover">
                                            @else
                                                <svg class="w-6 h-6 text-gray-300 stroke-current" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                            @endif
                                        </div>
                                        <div class="text-xs space-y-0.5">
                                            <h4 class="font-bold text-gray-900 line-clamp-1">{{ $item->product_name }}</h4>
                                            <div class="text-[11px] text-gray-500">
                                                x {{ $item->quantity }} &bull; Rp {{ number_format($item->product_price, 0, ',', '.') }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-xs font-black text-gray-800">
                                        Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Card Footer -->
                        <div class="bg-gray-50/50 px-5 py-3.5 border-t border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div class="flex items-baseline gap-2 text-xs">
                                <span class="text-gray-500">Total Pesanan:</span>
                                <span class="text-base font-black text-primary">
                                    Rp {{ number_format($order->final_amount, 0, ',', '.') }}
                                </span>
                            </div>

                            <div class="flex items-center gap-2">
                                <a
                                    href="{{ route('orders.show', $order->id) }}"
                                    class="px-4 py-2 rounded-xl bg-primary hover:bg-emerald-700 text-white font-bold text-xs transition shadow-sm"
                                >
                                    Lihat Detail Pesanan
                                </a>

                                @if ($order->status === 'pending')
                                    <form action="{{ route('orders.cancel', $order->id) }}" method="POST" onsubmit="return confirm('Batalkan pesanan ini?')">
                                        @csrf
                                        <button type="submit" class="px-4 py-2 rounded-xl border border-red-200 text-red-600 hover:bg-red-50 font-bold text-xs transition">
                                            Batalkan
                                        </button>
                                    </form>
                                @endif

                                @if ($order->status === 'shipped')
                                    <form action="{{ route('orders.complete', $order->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="px-4 py-2 rounded-xl bg-secondary hover:bg-blue-700 text-white font-bold text-xs transition shadow-sm">
                                            Pesanan Diterima
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="pt-4">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
