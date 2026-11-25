<x-seller-layout title="Detail Pesanan">
    <x-slot name="header">
        Detail Pesanan: {{ $order->invoice_number }}
    </x-slot>

    <div class="max-w-4xl mx-auto space-y-6">
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

        <!-- 1. Header Ringkasan Pesanan -->
        <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <span class="text-xs text-gray-400 font-mono">Invoice Pesanan</span>
                <h2 class="text-xl font-black text-gray-900 font-mono">{{ $order->invoice_number }}</h2>
                <p class="text-xs text-gray-500 mt-1">
                    Waktu Transaksi: {{ $order->created_at->translatedFormat('d F Y, H:i:s') }}
                </p>
            </div>

            <div>
                @switch($order->status)
                    @case('processing')
                        <div class="text-right">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-amber-50 text-amber-700 font-bold rounded-full text-xs">
                                <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                                Perlu Diproses (Siap Kirim)
                            </span>
                            <span class="block text-[10px] text-gray-400 mt-1">Pembayaran sudah diterima</span>
                        </div>
                        @break
                    @case('shipped')
                        <div class="text-right">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-blue-50 text-secondary font-bold rounded-full text-xs">
                                <span class="w-2 h-2 rounded-full bg-secondary"></span>
                                Sedang Dikirim
                            </span>
                            <span class="block text-[10px] text-gray-400 mt-1">Pesanan dalam perjalanan kurir</span>
                        </div>
                        @break
                    @case('completed')
                        <div class="text-right">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-50 text-primary font-bold rounded-full text-xs">
                                <span class="w-2 h-2 rounded-full bg-primary"></span>
                                Pesanan Selesai
                            </span>
                            <span class="block text-[10px] text-gray-400 mt-1">Barang telah diterima pembeli</span>
                        </div>
                        @break
                    @case('cancelled')
                        <div class="text-right">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-red-50 text-red-600 font-bold rounded-full text-xs">
                                Dibatalkan
                            </span>
                        </div>
                        @break
                    @default
                        <div class="text-right">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-gray-100 text-gray-600 font-bold rounded-full text-xs">
                                Menunggu Pembayaran
                            </span>
                            <span class="block text-[10px] text-gray-400 mt-1">Menunggu konfirmasi payment gateway</span>
                        </div>
                @endswitch
            </div>
        </div>

        <!-- 2. Form Input Resi Pengiriman (Khusus status processing) -->
        @if ($order->status === 'processing')
            <div class="bg-gradient-to-r from-amber-500/10 via-emerald-500/10 to-blue-500/10 border-2 border-primary/30 rounded-2xl p-6 shadow-sm">
                <div class="flex items-center gap-2 mb-3">
                    <span class="w-3 h-3 rounded-full bg-primary animate-ping"></span>
                    <h3 class="font-bold text-gray-900 text-sm">Aksi Penjual: Input Resi Pengiriman</h3>
                </div>
                <p class="text-xs text-gray-600 mb-4">
                    Pesanan ini telah lunas dibayar. Silakan kemas produk dan masukkan kurir pengiriman beserta nomor resi resmi di bawah ini.
                </p>

                <form method="POST" action="{{ route('seller.orders.ship', $order->invoice_number) }}" @submit="submitting = true" x-data="{ submitting: false }" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-stone-700 uppercase mb-1">Kurir Pengiriman <span class="text-rose-500">*</span></label>
                        <select name="courier_name" required class="w-full text-xs border-stone-300 rounded-xl focus:ring-primary focus:border-primary shadow-sm py-2">
                            <option value="JNE">JNE Express</option>
                            <option value="J&T">J&T Express</option>
                            <option value="SiCepat">SiCepat Ekspres</option>
                            <option value="AnterAja">AnterAja</option>
                            <option value="ID Express">ID Express</option>
                            <option value="Ninja Xpress">Ninja Xpress</option>
                            <option value="Pos Indonesia">Pos Indonesia</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-stone-700 uppercase mb-1">Nomor Resi Pengiriman <span class="text-rose-500">*</span></label>
                        <input type="text" name="tracking_number" required placeholder="Contoh: JNE123456789ID"
                            class="w-full text-xs font-mono uppercase border-stone-300 rounded-xl focus:ring-primary focus:border-primary shadow-sm py-2" />
                    </div>

                    <div class="flex items-end">
                        <button type="submit" :disabled="submitting" class="w-full min-h-[44px] py-2.5 px-4 bg-primary hover:bg-primary-hover text-white text-xs font-bold rounded-xl shadow-md transition flex items-center justify-center gap-2 disabled:opacity-50">
                            <template x-if="submitting">
                                <span class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4 animate-spin text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path></svg>
                                    <span>Memproses...</span>
                                </span>
                            </template>
                            <template x-if="!submitting">
                                <span class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <span>Kirim Pesanan Sekarang</span>
                                </span>
                            </template>
                        </button>
                    </div>
                </form>
            </div>
        @endif

        <!-- Informasi Pengiriman Terdata -->
        @if ($order->shipment && $order->shipment->tracking_number)
            <div class="bg-blue-50 border border-blue-200 rounded-2xl p-5 text-xs text-blue-900 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-100 text-secondary flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
                    </div>
                    <div>
                        <span class="font-bold block">Resi Pengiriman Aktif:</span>
                        <span class="font-mono text-sm font-bold text-secondary">{{ $order->shipment->courier_name }} - {{ $order->shipment->tracking_number }}</span>
                        <span class="text-[11px] text-blue-700 block mt-0.5">Dikirim pada: {{ $order->shipment->shipped_at?->translatedFormat('d F Y, H:i') ?? '-' }}</span>
                    </div>
                </div>
                <span class="px-2.5 py-1 bg-white rounded-lg text-[10px] font-bold text-secondary border border-blue-200">
                    Status: {{ ucfirst($order->shipment->shipment_status) }}
                </span>
            </div>
        @endif

        <!-- 3. Rincian Pembeli & Alamat -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Data Pembeli -->
            <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm space-y-3">
                <h4 class="font-bold text-gray-900 text-xs uppercase tracking-wider border-b border-gray-100 pb-2">Informasi Pembeli</h4>
                <div class="space-y-1 text-xs">
                    <p class="font-bold text-gray-800">{{ $order->user->name ?? 'Pembeli' }}</p>
                    <p class="text-gray-500">{{ $order->user->email ?? '-' }}</p>
                    <p class="text-gray-500">Telepon: {{ $order->user->phone ?? '-' }}</p>
                </div>
            </div>

            <!-- Alamat Pengiriman -->
            <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm space-y-3">
                <h4 class="font-bold text-gray-900 text-xs uppercase tracking-wider border-b border-gray-100 pb-2">Alamat Pengiriman</h4>
                <div class="text-xs text-gray-600 leading-relaxed">
                    {{ $order->shipping_address ?? 'Tidak ada data alamat.' }}
                </div>
            </div>
        </div>

        <!-- 4. Daftar Produk Pesanan -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h4 class="font-bold text-gray-900 text-xs uppercase tracking-wider">Produk yang Dipesan</h4>
            </div>

            <div class="divide-y divide-gray-100">
                @foreach ($order->items as $item)
                    <div class="p-6 flex items-center justify-between gap-4">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 rounded-xl bg-gray-100 border border-gray-200 overflow-hidden shrink-0">
                                @if ($item->product?->primaryImage)
                                    <img src="{{ $item->product->primaryImage->image_path }}" alt="{{ $item->product_name }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-400">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    </div>
                                @endif
                            </div>
                            <div>
                                <h5 class="text-xs sm:text-sm font-bold text-gray-900">{{ $item->product_name }}</h5>
                                @if ($item->variant_name)
                                    <span class="inline-block text-[10px] text-gray-600 bg-gray-100 px-2 py-0.5 rounded mt-1">
                                        Variasi: {{ $item->variant_name }}
                                    </span>
                                @endif
                                <div class="text-xs text-gray-500 mt-1">
                                    Rp{{ number_format($item->product_price, 0, ',', '.') }} &times; {{ $item->quantity }}
                                </div>
                            </div>
                        </div>

                        <div class="text-right">
                            <span class="text-xs sm:text-sm font-bold text-gray-900">
                                Rp{{ number_format($item->subtotal, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Rincian Biaya -->
            <div class="p-6 bg-gray-50/50 border-t border-gray-100 space-y-2 text-xs">
                <div class="flex justify-between text-gray-600">
                    <span>Subtotal Produk</span>
                    <span>Rp{{ number_format($order->total_amount, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-gray-600">
                    <span>Ongkos Kirim</span>
                    <span>Rp{{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                </div>
                @if ($order->discount_amount > 0)
                    <div class="flex justify-between text-emerald-600 font-semibold">
                        <span>Diskon Voucher</span>
                        <span>-Rp{{ number_format($order->discount_amount, 0, ',', '.') }}</span>
                    </div>
                @endif
                <div class="pt-2 border-t border-gray-200 flex justify-between items-center text-sm font-black">
                    <span class="text-gray-900">Total Pembayaran</span>
                    <span class="text-primary text-base">Rp{{ number_format($order->final_amount, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- Back Button -->
        <div class="flex justify-start">
            <a href="{{ route('seller.orders.index') }}" class="px-5 py-2.5 rounded-xl border border-gray-300 text-gray-700 hover:bg-gray-50 text-xs font-bold transition flex items-center gap-1">
                &larr; Kembali ke Daftar Pesanan
            </a>
        </div>
    </div>
</x-seller-layout>
