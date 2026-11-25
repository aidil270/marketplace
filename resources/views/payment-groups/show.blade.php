<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-xl text-stone-800 leading-tight flex items-center gap-2 font-heading">
                <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                Selesaikan Pembayaran
            </h2>
            <a href="{{ route('orders.index') }}" class="text-xs font-semibold text-secondary hover:underline">
                &larr; Riwayat Pesanan
            </a>
        </div>
    </x-slot>

    <div class="py-8 bg-surface min-h-screen" x-data="{ submitting: false }">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <!-- Flash Message -->
            @if (session('success'))
                <div class="p-4 bg-teal-50 border border-teal-200 text-teal-900 rounded-2xl flex items-center gap-3">
                    <svg class="w-5 h-5 text-secondary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span class="text-xs sm:text-sm font-medium">{{ session('success') }}</span>
                </div>
            @endif

            @if (session('info'))
                <div class="p-4 bg-blue-50 border border-blue-200 text-blue-900 rounded-2xl flex items-center gap-3">
                    <svg class="w-5 h-5 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="text-xs sm:text-sm font-medium">{{ session('info') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="p-4 bg-rose-50 border border-rose-200 text-rose-900 rounded-2xl flex items-center gap-3">
                    <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="text-xs sm:text-sm font-medium">{{ session('error') }}</span>
                </div>
            @endif

            <!-- 1. Header Ringkasan Tagihan Utama -->
            <div class="bg-white rounded-2xl p-6 sm:p-8 border border-stone-200 shadow-sm text-center space-y-4">
                <span class="inline-block px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider
                    @if ($paymentGroup->status === 'paid') bg-teal-100 text-secondary
                    @elseif ($paymentGroup->isExpired() || $paymentGroup->status === 'failed') bg-rose-100 text-rose-800
                    @else bg-amber-100 text-amber-900 @endif">
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
                    <span class="text-xs text-stone-500 uppercase tracking-wider font-semibold block mb-1">Total yang Harus Dibayar</span>
                    <div class="text-3xl sm:text-4xl font-extrabold text-primary font-heading">
                        Rp {{ number_format($paymentGroup->total_amount, 0, ',', '.') }}
                    </div>
                    <span class="text-xs text-stone-400 font-mono mt-1 block">
                        No. Transaksi: {{ $paymentGroup->invoice_number }}
                    </span>
                </div>

                <!-- Batas Waktu Pembayaran (Jika Pending) -->
                @if ($paymentGroup->isPending() && ! $paymentGroup->isExpired())
                    <div class="bg-amber-50/80 border border-amber-200/80 rounded-xl p-3 max-w-md mx-auto text-xs text-amber-900">
                        <span class="font-bold">Batas Waktu Pembayaran:</span>
                        <div class="font-mono text-sm font-bold text-amber-950 mt-0.5">
                            {{ $paymentGroup->expired_at?->format('d M Y, H:i') }} WIB
                        </div>
                    </div>
                @endif

                <!-- Action Buttons -->
                <div class="pt-2 flex flex-col sm:flex-row items-center justify-center gap-3">
                    @if ($paymentGroup->isPending() && ! $paymentGroup->isExpired())
                        @if ($paymentGroup->payment_url)
                            <a href="{{ $paymentGroup->payment_url }}" target="_blank"
                                class="w-full sm:w-auto min-h-[44px] px-8 py-3 bg-primary hover:bg-primary-hover text-white font-extrabold text-xs uppercase tracking-wider rounded-xl transition shadow-md flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                <span>Bayar Sekarang (DOKU Checkout)</span>
                            </a>
                        @else
                            <form action="{{ route('payment-groups.pay', $paymentGroup->id) }}" method="POST" @submit="submitting = true" class="w-full sm:w-auto">
                                @csrf
                                <button type="submit" :disabled="submitting" class="w-full sm:w-auto min-h-[44px] px-8 py-3 bg-primary hover:bg-primary-hover text-white font-extrabold text-xs uppercase tracking-wider rounded-xl transition shadow-md flex items-center justify-center gap-2 disabled:opacity-50">
                                    <template x-if="submitting">
                                        <span class="flex items-center gap-2">
                                            <svg class="w-4 h-4 animate-spin text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path></svg>
                                            <span>Membuka Pembayaran...</span>
                                        </span>
                                    </template>
                                    <template x-if="!submitting">
                                        <span>Buka Halaman Pembayaran</span>
                                    </template>
                                </button>
                            </form>
                        @endif

                        <form action="{{ route('payment-groups.check', $paymentGroup->id) }}" method="POST" @submit="submitting = true" class="w-full sm:w-auto">
                            @csrf
                            <button type="submit" :disabled="submitting" class="w-full sm:w-auto min-h-[44px] px-5 py-3 bg-white hover:bg-stone-50 border border-stone-300 text-stone-700 font-bold text-xs uppercase tracking-wider rounded-xl transition shadow-sm flex items-center justify-center gap-2 disabled:opacity-50">
                                <template x-if="submitting">
                                    <span class="flex items-center gap-2">
                                        <svg class="w-4 h-4 animate-spin text-stone-700" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path></svg>
                                        <span>Memeriksa Status...</span>
                                    </span>
                                </template>
                                <template x-if="!submitting">
                                    <span>Cek Status Pembayaran</span>
                                </template>
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <!-- 2. Rincian Pesanan dalam Grup Pembayaran Ini -->
            <div class="bg-white rounded-2xl p-6 border border-stone-200 shadow-sm space-y-4">
                <h3 class="text-xs sm:text-sm font-extrabold text-stone-900 uppercase tracking-wide border-b border-stone-100 pb-3 font-heading">
                    Pesanan Terkait ({{ $paymentGroup->orders->count() }} Toko)
                </h3>

                <div class="space-y-4">
                    @foreach ($paymentGroup->orders as $order)
                        <div class="border border-stone-200 rounded-xl p-4 bg-stone-50/50 space-y-3">
                            <div class="flex items-center justify-between text-xs">
                                <div class="flex items-center gap-2">
                                    <span class="bg-secondary/90 text-white text-xs font-bold px-2 py-0.5 rounded">Toko</span>
                                    <span class="font-bold text-stone-900">{{ $order->store->name }}</span>
                                    <span class="text-stone-300">&bull;</span>
                                    <span class="font-mono text-stone-500 font-semibold">{{ $order->invoice_number }}</span>
                                </div>
                                <span class="font-bold text-primary">
                                    Rp {{ number_format($order->final_amount, 0, ',', '.') }}
                                </span>
                            </div>

                            <div class="text-xs text-stone-600 divide-y divide-stone-100 pt-1">
                                @foreach ($order->items as $item)
                                    <div class="flex justify-between py-1.5 first:pt-0 last:pb-0">
                                        <span class="line-clamp-1 flex-1 pr-2">{{ $item->product_name }} (x{{ $item->quantity }})</span>
                                        <span class="text-stone-800 font-semibold shrink-0">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
