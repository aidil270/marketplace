<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">
            Status Pembayaran
        </h2>
    </x-slot>

    <div class="py-12 bg-[#F8FAFC] min-h-screen">
        <div class="max-w-xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="bg-white rounded-2xl p-8 border border-gray-100 shadow-sm space-y-6">
                @if ($paymentGroup && $paymentGroup->isPaid())
                    <div class="w-16 h-16 bg-emerald-100 text-primary rounded-full flex items-center justify-center mx-auto">
                        <svg class="w-8 h-8 fill-current" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-black text-gray-900 mb-1">Pembayaran Berhasil!</h3>
                        <p class="text-xs text-gray-500">Terima kasih, pesanan Anda telah lunas dan kini sedang diproses oleh penjual.</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-xl text-xs space-y-1 text-gray-700">
                        <div class="flex justify-between">
                            <span class="text-gray-500">No. Tagihan:</span>
                            <span class="font-mono font-bold">{{ $paymentGroup->invoice_number }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Total Dibayar:</span>
                            <span class="font-bold text-primary">Rp {{ number_format($paymentGroup->total_amount, 0, ',', '.') }}</span>
                        </div>
                    </div>
                @elseif ($paymentGroup && ($paymentGroup->isExpired() || $paymentGroup->status === 'failed'))
                    <div class="w-16 h-16 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-black text-gray-900 mb-1">Pembayaran Tidak Berhasil</h3>
                        <p class="text-xs text-gray-500">Waktu pembayaran telah berakhir atau transaksi dibatalkan.</p>
                    </div>
                @else
                    <div class="w-16 h-16 bg-blue-100 text-secondary rounded-full flex items-center justify-center mx-auto">
                        <svg class="w-8 h-8 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-black text-gray-900 mb-1">Menunggu Konfirmasi Pembayaran</h3>
                        <p class="text-xs text-gray-500">Kami sedang memeriksa status pembayaran Anda dari gateway DOKU.</p>
                    </div>
                @endif

                <div class="pt-2 flex flex-col sm:flex-row items-center justify-center gap-3">
                    <a href="{{ route('orders.index') }}" class="w-full sm:w-auto px-6 py-2.5 bg-primary hover:bg-emerald-700 text-white font-bold text-xs rounded-xl transition shadow">
                        Lihat Pesanan Saya
                    </a>
                    <a href="{{ route('home') }}" class="w-full sm:w-auto px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs rounded-xl transition">
                        Kembali ke Beranda
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
