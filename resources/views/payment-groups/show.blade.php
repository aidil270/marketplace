<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-xl text-gray-800 leading-tight flex items-center gap-2">
                <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                Selesaikan Pembayaran
            </h2>
            <a href="{{ route('orders.index') }}" class="text-xs font-bold text-secondary hover:underline">
                &larr; Riwayat Pesanan
            </a>
        </div>
    </x-slot>

    <div class="py-8 bg-[#F8FAFC] min-h-screen">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <!-- Flash Message -->
            @if (session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl flex items-center gap-3">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span class="text-sm font-medium">{{ session('success') }}</span>
                </div>
            @endif

            @if (session('info'))
                <div class="p-4 bg-blue-50 border border-blue-200 text-blue-800 rounded-2xl flex items-center gap-3">
                    <svg class="w-5 h-5 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="text-sm font-medium">{{ session('info') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="p-4 bg-red-50 border border-red-200 text-red-800 rounded-2xl flex items-center gap-3">
                    <svg class="w-5 h-5 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="text-sm font-medium">{{ session('error') }}</span>
                </div>
            @endif

            <!-- 1. Header Ringkasan Tagihan Utama -->
            <div class="bg-white rounded-2xl p-6 sm:p-8 border border-gray-100 shadow-sm text-center space-y-4">
                <span class="inline-block px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider
                    @if ($paymentGroup->status === 'paid') bg-emerald-100 text-emerald-800
                    @elseif ($paymentGroup->isExpired() || $paymentGroup->status === 'failed') bg-red-100 text-red-800
                    @else bg-amber-100 text-amber-800 @endif">
                    @if ($paymentGroup->status === 'paid')
                        Pembayaran Lunas
                    @elseif ($paymentGroup->isExpired())
                        Pembayaran Kedaluwarsa
                    @elseif ($paymentGroup->status === 'failed')
                        Pembayaran Dibatalkan
                    @else
                        Menunggu Pembayaran
                    @endif
                </span>

                <div>
                    <span class="text-xs text-gray-500 uppercase tracking-wider font-semibold block mb-1">Total yang Harus Dibayar</span>
                    <div class="text-3xl sm:text-4xl font-black text-primary">
                        Rp {{ number_format($paymentGroup->total_amount, 0, ',', '.') }}
                    </div>
                    <span class="text-xs text-gray-400 font-mono mt-1 block">
                        No. Transaksi: {{ $paymentGroup->invoice_number }}
                    </span>
                </div>

                <!-- Batas Waktu Pembayaran (Jika Pending) -->
                @if ($paymentGroup->isPending() && ! $paymentGroup->isExpired())
                    <div class="bg-amber-50/80 border border-amber-200/80 rounded-xl p-3 max-w-md mx-auto text-xs text-amber-800">
                        <span class="font-bold">Batas Waktu Pembayaran:</span>
                        <div class="font-mono text-sm font-bold text-amber-900 mt-0.5">
                            {{ $paymentGroup->expired_at?->format('d M Y, H:i') }} WIB
                        </div>
                    </div>
                @endif

                <!-- Action Buttons -->
                <div class="pt-2 flex flex-col sm:flex-row items-center justify-center gap-3">
                    @if ($paymentGroup->isPending() && ! $paymentGroup->isExpired())
                        @if ($paymentGroup->payment_url)
                            <a href="{{ $paymentGroup->payment_url }}" target="_blank"
                                class="w-full sm:w-auto px-8 py-3 bg-primary hover:bg-emerald-700 text-white font-extrabold text-sm rounded-xl transition shadow flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                <span>Bayar Sekarang (DOKU Checkout)</span>
                            </a>
                        @else
                            <form action="{{ route('payment-groups.pay', $paymentGroup->id) }}" method="POST" class="w-full sm:w-auto">
                                @csrf
                                <button type="submit" class="w-full sm:w-auto px-8 py-3 bg-primary hover:bg-emerald-700 text-white font-extrabold text-sm rounded-xl transition shadow">
                                    Buka Halaman Pembayaran
                                </button>
                            </form>
                        @endif

                        <form action="{{ route('payment-groups.check', $paymentGroup->id) }}" method="POST" class="w-full sm:w-auto">
                            @csrf
                            <button type="submit" class="w-full sm:w-auto px-5 py-3 bg-white hover:bg-gray-50 border border-gray-300 text-gray-700 font-bold text-sm rounded-xl transition shadow-sm">
                                Cek Status Pembayaran
                            </button>
                        </form>
                    @elseif ($paymentGroup->isExpired() || $paymentGroup->status === 'failed')
                        <form action="{{ route('payment-groups.pay', $paymentGroup->id) }}" method="POST" class="w-full sm:w-auto">
                            @csrf
                            <button type="submit" class="w-full sm:w-auto px-8 py-3 bg-amber-500 hover:bg-amber-600 text-white font-bold text-sm rounded-xl transition shadow">
                                Bayar Ulang (Buat Sesi Baru)
                            </button>
                        </form>
                    @else
                        <a href="{{ route('orders.index') }}" class="w-full sm:w-auto px-8 py-3 bg-primary hover:bg-emerald-700 text-white font-bold text-sm rounded-xl transition shadow">
                            Lihat Status Pesanan Saya
                        </a>
                    @endif
                </div>

                <!-- Petunjuk Sandbox -->
                <div class="text-[11px] text-gray-400 pt-2 border-t border-gray-100 flex items-center justify-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></span>
                    <span>Mode DOKU Sandbox &bull; Mendukung Simulasi Virtual Account, QRIS, & Kartu Uji</span>
                </div>
            </div>

            <!-- 2. Rincian Pesanan dalam Grup Pembayaran Ini -->
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm space-y-4">
                <h3 class="font-bold text-sm text-gray-900 uppercase tracking-wide border-b border-gray-100 pb-2">
                    Pesanan yang Dicakup ({{ $paymentGroup->orders->count() }} Toko)
                </h3>

                <div class="divide-y divide-gray-100 space-y-4">
                    @foreach ($paymentGroup->orders as $order)
                        <div class="pt-4 first:pt-0 space-y-3">
                            <div class="flex items-center justify-between text-xs">
                                <div class="flex items-center gap-2">
                                    <span class="bg-secondary text-white text-[9px] font-black px-1.5 py-0.5 rounded">Toko</span>
                                    <span class="font-bold text-gray-800">{{ $order->store->name ?? 'Toko' }}</span>
                                    <span class="text-gray-400 font-mono text-[11px]">({{ $order->invoice_number }})</span>
                                </div>
                                <span class="font-black text-gray-900">
                                    Rp {{ number_format($order->final_amount, 0, ',', '.') }}
                                </span>
                            </div>

                            <div class="space-y-2 pl-4 border-l-2 border-gray-100">
                                @foreach ($order->items as $item)
                                    <div class="flex items-center justify-between text-xs text-gray-600">
                                        <div class="truncate max-w-sm">
                                            <span>{{ $item->product_name }}</span>
                                            <span class="text-gray-400"> x {{ $item->quantity }}</span>
                                        </div>
                                        <span class="font-semibold text-gray-800">
                                            Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                        </span>
                                    </div>
                                @endforeach

                                <div class="flex items-center justify-between text-[11px] text-gray-400 pt-1">
                                    <span>Ongkir ({{ $order->shipment->courier_name ?? 'Kurir' }}): Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                                    @if ($order->discount_amount > 0)
                                        <span class="text-emerald-600 font-medium">Diskon: -Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
