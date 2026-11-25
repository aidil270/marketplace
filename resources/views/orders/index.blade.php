<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between border-b border-stone-200 pb-4">
            <div class="flex items-center gap-2.5">
                <div class="w-2.5 h-6 bg-secondary rounded-full"></div>
                <h1 class="text-xl font-extrabold text-stone-900 tracking-tight font-heading">Pesanan Saya</h1>
            </div>
            <a href="{{ route('home') }}" class="text-xs font-semibold text-primary hover:text-primary-hover flex items-center gap-1">
                Belanja Lagi &rarr;
            </a>
        </div>

        <!-- Filter Status Tabs -->
        <div class="bg-white rounded-2xl border border-stone-200 shadow-sm overflow-x-auto">
            <div class="flex items-center min-w-max border-b border-stone-100 text-xs font-bold">
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
                        class="px-5 py-3.5 flex items-center gap-1.5 border-b-2 transition {{ $status === $k ? 'border-primary text-primary bg-orange-50/40' : 'border-transparent text-stone-500 hover:text-stone-900' }}"
                    >
                        <span>{{ $label }}</span>
                        @if (($counts[$k] ?? 0) > 0)
                            <span class="text-xs px-2 py-0.5 rounded-full {{ $status === $k ? 'bg-primary text-white' : 'bg-stone-100 text-stone-600' }}">
                                {{ $counts[$k] }}
                            </span>
                        @endif
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Orders List -->
        @if ($orders->isEmpty())
            <div class="bg-white rounded-2xl p-12 border border-stone-200 text-center space-y-4 max-w-md mx-auto shadow-sm">
                <div class="w-16 h-16 rounded-full bg-stone-100 text-stone-400 mx-auto flex items-center justify-center">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <h3 class="text-sm font-bold text-stone-800 font-heading">Belum Ada Pesanan</h3>
                <p class="text-xs text-stone-500">Kamu belum memiliki riwayat pesanan dengan status ini.</p>
                <a href="{{ route('home') }}" class="inline-block px-5 py-2.5 bg-primary hover:bg-primary-hover text-white font-bold text-xs rounded-xl shadow-sm transition min-h-[44px]">
                    Mulai Belanja
                </a>
            </div>
        @else
            <div class="space-y-4">
                @foreach ($orders as $order)
                    @php
                        $statusColors = [
                            'pending' => 'bg-amber-100 text-amber-900 border-amber-200',
                            'processing' => 'bg-blue-100 text-blue-900 border-blue-200',
                            'shipped' => 'bg-purple-100 text-purple-900 border-purple-200',
                            'completed' => 'bg-teal-100 text-secondary border-teal-200',
                            'cancelled' => 'bg-rose-100 text-rose-900 border-rose-200',
                        ];
                        $statusLabels = [
                            'pending' => 'Menunggu Pembayaran',
                            'processing' => 'Sedang Diproses',
                            'shipped' => 'Sedang Dikirim',
                            'completed' => 'Selesai',
                            'cancelled' => 'Dibatalkan',
                        ];
                    @endphp
                    <div class="bg-white rounded-2xl border border-stone-200 shadow-sm overflow-hidden hover:shadow-md transition">
                        <!-- Card Header -->
                        <div class="bg-stone-50 px-5 py-3 border-b border-stone-200 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="bg-secondary/90 text-white text-xs font-bold px-2 py-0.5 rounded">Toko</span>
                                <span class="font-bold text-stone-900">{{ $order->store->name ?? 'Toko' }}</span>
                                <span class="text-stone-300">&bull;</span>
                                <span class="font-mono text-stone-500 font-semibold">{{ $order->invoice_number }}</span>
                                <span class="text-stone-300">&bull;</span>
                                <span class="text-stone-400">{{ $order->created_at->format('d M Y, H:i') }}</span>
                            </div>

                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold border w-max {{ $statusColors[$order->status] ?? 'bg-stone-100 text-stone-800' }}">
                                {{ $statusLabels[$order->status] ?? ucfirst($order->status) }}
                            </span>
                        </div>

                        <!-- Items Preview -->
                        <div class="p-5 divide-y divide-stone-100">
                            @foreach ($order->items as $item)
                                <div class="flex items-center justify-between gap-4 py-3 first:pt-0 last:pb-0">
                                    <div class="flex items-center gap-3">
                                        <div class="w-14 h-14 rounded-xl bg-stone-50 border border-stone-200 overflow-hidden shrink-0 flex items-center justify-center">
                                            @if ($item->product && $item->product->primaryImage)
                                                <img src="{{ $item->product->primaryImage->image_path }}" alt="{{ $item->product_name }}" class="w-full h-full object-cover">
                                            @else
                                                <svg class="w-6 h-6 text-stone-300 stroke-current" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                            @endif
                                        </div>
                                        <div class="text-xs space-y-0.5">
                                            <h4 class="font-bold text-stone-900 line-clamp-1">{{ $item->product_name }}</h4>
                                            <p class="text-stone-500">
                                                {{ $item->quantity }} x Rp {{ number_format($item->product_price, 0, ',', '.') }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="text-xs font-black text-stone-900">
                                        Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Footer Total & Actions -->
                        <div class="bg-stone-50/60 px-5 py-3.5 border-t border-stone-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="text-xs text-stone-500">
                                Total Pesanan: <span class="font-bold text-sm text-primary font-heading">Rp {{ number_format($order->final_amount, 0, ',', '.') }}</span>
                            </div>

                            <div class="flex items-center gap-2.5">
                                <a
                                    href="{{ route('orders.show', $order->id) }}"
                                    class="min-h-[38px] px-4 py-2 border border-stone-300 hover:bg-stone-100 text-stone-700 text-xs font-bold rounded-xl transition flex items-center justify-center"
                                >
                                    Rincian Pesanan
                                </a>

                                @if ($order->status === 'pending')
                                    @php
                                        $pg = $order->paymentGroup;
                                    @endphp
                                    @if ($pg)
                                        <a
                                            href="{{ route('payment-groups.pay', $pg->id) }}"
                                            class="min-h-[38px] px-5 py-2 bg-primary hover:bg-primary-hover text-white text-xs font-bold rounded-xl shadow-sm transition flex items-center justify-center"
                                        >
                                            Bayar Sekarang
                                        </a>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="pt-4 border-t border-stone-200">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
