<x-app-layout>
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6" x-data="{ cancelling: false, completing: false }">
        <!-- Breadcrumb / Back button -->
        <div class="flex items-center justify-between border-b border-stone-200 pb-4">
            <div class="flex items-center gap-2">
                <a href="{{ route('orders.index') }}" class="text-xs font-semibold text-stone-500 hover:text-stone-900 flex items-center gap-1">
                    &larr; Riwayat Pesanan
                </a>
                <span class="text-stone-300">/</span>
                <span class="text-xs font-bold text-stone-900 font-mono">Detail #{{ $order->invoice_number }}</span>
            </div>
        </div>

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
                'processing' => 'Pesanan Sedang Diproses',
                'shipped' => 'Pesanan Sedang Dikirim',
                'completed' => 'Pesanan Selesai',
                'cancelled' => 'Pesanan Dibatalkan',
            ];
        @endphp

        <!-- 1. Status Bar Card -->
        <div class="bg-white rounded-2xl p-6 border border-stone-200 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="space-y-1">
                <span class="text-xs font-mono text-stone-400 font-bold uppercase tracking-wider">No. Pesanan: {{ $order->invoice_number }}</span>
                <div class="flex items-center gap-3">
                    <h2 class="text-lg font-extrabold text-stone-900 font-heading">
                        {{ $statusLabels[$order->status] ?? ucfirst($order->status) }}
                    </h2>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $statusColors[$order->status] ?? 'bg-stone-100' }}">
                        {{ strtoupper($order->status) }}
                    </span>
                </div>
                <p class="text-xs text-stone-500">Dibuat pada {{ $order->created_at->format('d F Y, H:i') }} WIB</p>
            </div>

            <!-- Action Buttons for Order Status -->
            <div class="flex items-center gap-2">
                @if ($order->status === 'pending')
                    <form action="{{ route('orders.cancel', $order->id) }}" method="POST" @submit="cancelling = true" onsubmit="return confirm('Apakah kamu yakin ingin membatalkan pesanan ini?')">
                        @csrf
                        <button type="submit" :disabled="cancelling" class="min-h-[44px] px-4 py-2 border border-rose-300 text-rose-600 hover:bg-rose-50 text-xs font-bold rounded-xl transition flex items-center justify-center gap-2 disabled:opacity-50">
                            <template x-if="cancelling">
                                <span class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4 animate-spin text-rose-600" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path></svg>
                                    <span>Membatalkan...</span>
                                </span>
                            </template>
                            <template x-if="!cancelling">
                                <span>Batalkan Pesanan</span>
                            </template>
                        </button>
                    </form>
                @endif

                @if ($order->status === 'shipped')
                    <form action="{{ route('orders.complete', $order->id) }}" method="POST" @submit="completing = true">
                        @csrf
                        <button type="submit" :disabled="completing" class="min-h-[44px] px-5 py-2.5 bg-secondary hover:bg-secondary-hover text-white text-xs font-bold rounded-xl shadow-md transition flex items-center justify-center gap-2 disabled:opacity-50">
                            <template x-if="completing">
                                <span class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4 animate-spin text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path></svg>
                                    <span>Memproses...</span>
                                </span>
                            </template>
                            <template x-if="!completing">
                                <span>Pesanan Diterima</span>
                            </template>
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <!-- 2. Alamat & Ekspedisi Pengiriman -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Alamat Penerima -->
            <div class="bg-white rounded-2xl p-6 border border-stone-200 shadow-sm space-y-2">
                <h3 class="text-xs font-bold text-stone-800 uppercase tracking-wide flex items-center gap-2 border-b border-stone-100 pb-2 font-heading">
                    <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                    Alamat Pengiriman
                </h3>
                <div class="text-xs space-y-1 text-stone-700 pt-1 leading-relaxed">
                    <p class="whitespace-pre-line">{{ $order->shipping_address ?? $order->shipment->recipient_address ?? 'Alamat tidak ditemukan' }}</p>
                </div>
            </div>

            <!-- Kurir & Pelacakan -->
            <div class="bg-white rounded-2xl p-6 border border-stone-200 shadow-sm space-y-2">
                <h3 class="text-xs font-bold text-stone-800 uppercase tracking-wide flex items-center gap-2 border-b border-stone-100 pb-2 font-heading">
                    <svg class="w-4 h-4 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
                    Informasi Pengiriman
                </h3>
                <div class="text-xs space-y-2 text-stone-700 pt-1">
                    <div class="flex justify-between">
                        <span class="text-stone-500">Kurir:</span>
                        <span class="font-bold text-stone-900">{{ $order->shipment->courier_name ?? 'Kurir' }} ({{ $order->shipment->service_type ?? 'REG' }})</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-stone-500">Nomor Resi:</span>
                        <span class="font-mono font-bold text-secondary">{{ $order->shipment->tracking_number ?? 'Belum tersedia' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-stone-500">Status Kirim:</span>
                        <span class="font-semibold text-stone-800">{{ ucfirst($order->shipment->shipment_status ?? 'pending') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Rincian Produk Dipesan -->
        <div class="bg-white rounded-2xl border border-stone-200 shadow-sm overflow-hidden">
            <div class="bg-stone-50 px-6 py-3 border-b border-stone-200 flex items-center justify-between text-xs">
                <div class="flex items-center gap-2">
                    <span class="bg-secondary/90 text-white text-xs font-bold px-2 py-0.5 rounded">Toko</span>
                    <span class="font-bold text-stone-900">{{ $order->store->name ?? 'Toko' }}</span>
                </div>
                <span class="text-stone-500">{{ $order->store->city ?? 'Indonesia' }}</span>
            </div>

            <div class="divide-y divide-stone-100 p-6 space-y-4">
                @foreach ($order->items as $item)
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pt-4 first:pt-0">
                        <div class="flex items-center gap-3">
                            <div class="w-16 h-16 rounded-xl bg-stone-50 border border-stone-200 overflow-hidden shrink-0 flex items-center justify-center">
                                @if ($item->product && $item->product->primaryImage)
                                    <img src="{{ $item->product->primaryImage->image_path }}" alt="{{ $item->product_name }}" class="w-full h-full object-cover">
                                @else
                                    <svg class="w-8 h-8 text-stone-300 stroke-current" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                @endif
                            </div>
                            <div class="text-xs space-y-1">
                                <h4 class="font-bold text-stone-900">{{ $item->product_name }}</h4>
                                <div class="text-stone-500">
                                    {{ $item->quantity }} x Rp {{ number_format($item->product_price, 0, ',', '.') }}
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between sm:justify-end gap-4 border-t sm:border-t-0 pt-2 sm:pt-0 border-stone-100">
                            <div class="text-sm font-black text-stone-900">
                                Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                            </div>

                            @if ($order->status === 'completed')
                                <div>
                                    @if ($item->review)
                                        <div class="flex items-center gap-2">
                                            <span class="inline-flex items-center gap-1 text-xs font-bold text-amber-700 bg-amber-50 px-2.5 py-1 rounded-lg border border-amber-200">
                                                <span>★</span> {{ $item->review->rating }}
                                            </span>
                                            @if ($item->review->created_at->diffInDays(now()) <= 7)
                                                <a href="{{ route('reviews.edit', $item->review->id) }}" class="text-xs text-secondary font-bold hover:underline">
                                                    Ubah
                                                </a>
                                            @endif
                                        </div>
                                    @else
                                        <a href="{{ route('reviews.create', ['order' => $order->id, 'orderItem' => $item->id]) }}" 
                                            class="inline-flex items-center gap-1.5 text-xs font-bold px-3 py-2 bg-primary hover:bg-primary-hover text-white rounded-xl transition shadow-sm min-h-[38px]">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                                            <span>Beri Ulasan</span>
                                        </a>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- 4. Rincian Pembayaran & Status Gateway -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
            <!-- Box Pembayaran DOKU Gateway -->
            <div class="bg-white rounded-2xl p-6 border border-stone-200 shadow-sm space-y-4">
                <h3 class="text-xs font-bold text-stone-800 uppercase tracking-wide flex items-center gap-2 border-b border-stone-100 pb-2 font-heading">
                    <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    Instruksi Pembayaran
                </h3>

                <div class="bg-teal-50/50 border border-teal-200/80 rounded-xl p-4 text-xs space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-teal-950">Gateway: DOKU Checkout</span>
                        <span class="bg-secondary text-white text-xs font-bold px-1.5 py-0.5 rounded">SANDBOX</span>
                    </div>
                    <div class="flex justify-between text-teal-900">
                        <span>Status Pembayaran:</span>
                        <span class="font-bold uppercase">{{ $order->payment->status ?? 'pending' }}</span>
                    </div>

                    @if ($order->status === 'pending')
                        <div class="pt-2 border-t border-teal-200/60 space-y-2.5">
                            @php
                                $pg = $order->paymentGroup;
                                $isExpired = $pg ? $pg->isExpired() : false;
                            @endphp

                            @if ($isExpired)
                                <div class="p-2.5 bg-rose-50 border border-rose-200 text-rose-800 rounded-lg text-xs">
                                    Waktu pembayaran telah kedaluwarsa. Silakan klik tombol di bawah untuk membuat sesi pembayaran baru.
                                </div>
                                @if ($pg)
                                    <form action="{{ route('payment-groups.pay', $pg->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="w-full min-h-[44px] py-2.5 bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs uppercase tracking-wider rounded-xl transition shadow">
                                            Bayar Ulang Sekarang
                                        </button>
                                    </form>
                                @endif
                            @else
                                <div class="flex flex-col sm:flex-row items-center gap-2">
                                    @if ($pg)
                                        <a href="{{ route('payment-groups.pay', $pg->id) }}" class="w-full min-h-[44px] py-2.5 bg-primary hover:bg-primary-hover text-white font-bold text-xs uppercase tracking-wider rounded-xl transition text-center shadow-md flex items-center justify-center gap-1.5">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                            <span>Bayar Sekarang</span>
                                        </a>

                                        <form action="{{ route('payment-groups.check', $pg->id) }}" method="POST" class="w-full sm:w-auto">
                                            @csrf
                                            <button type="submit" class="w-full sm:w-auto min-h-[44px] px-4 py-2.5 bg-white hover:bg-stone-50 border border-stone-300 text-stone-700 font-bold text-xs rounded-xl transition shadow-sm whitespace-nowrap">
                                                Cek Status
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-xs text-stone-500">Menunggu pembayaran</span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @elseif ($order->status === 'processing' || $order->status === 'shipped' || $order->status === 'completed' || $order->payment?->status === 'paid')
                        <div class="pt-2 border-t border-teal-200/60 text-secondary font-bold flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-secondary fill-current" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            Pembayaran Berhasil / Lunas
                        </div>
                    @endif
                </div>
            </div>

            <!-- Box Ringkasan Total Tagihan -->
            <div class="bg-white rounded-2xl p-6 border border-stone-200 shadow-sm space-y-3">
                <h3 class="text-xs font-bold text-stone-800 uppercase tracking-wide border-b border-stone-100 pb-2 font-heading">
                    Rincian Tagihan
                </h3>

                <div class="space-y-2 text-xs text-stone-600">
                    <div class="flex justify-between">
                        <span>Subtotal Produk:</span>
                        <span class="font-semibold text-stone-900">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Biaya Pengiriman (Ongkir):</span>
                        <span class="font-semibold text-stone-900">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                    </div>
                    @if ($order->discount_amount > 0)
                        <div class="flex justify-between text-secondary font-bold">
                            <span>Diskon Voucher ({{ $order->voucher->code ?? 'VOUCHER' }}):</span>
                            <span>-Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</span>
                        </div>
                    @endif
                    <div class="border-t border-stone-200 pt-2 flex items-baseline justify-between">
                        <span class="font-bold text-stone-900">Total Akhir:</span>
                        <span class="text-lg font-extrabold text-primary font-heading">
                            Rp {{ number_format($order->final_amount, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
