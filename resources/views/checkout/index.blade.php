<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6" x-data="{ submitting: false, addNewAddress: false }">
        <!-- Header -->
        <div class="flex items-center justify-between border-b border-stone-200 pb-4">
            <div class="flex items-center gap-2.5">
                <div class="w-2.5 h-6 bg-primary rounded-full"></div>
                <h1 class="text-xl font-extrabold text-stone-900 tracking-tight font-heading">Checkout Pesanan</h1>
            </div>
            <a href="{{ route('cart.index') }}" class="text-xs font-semibold text-secondary hover:text-primary transition flex items-center gap-1">
                &larr; Kembali ke Keranjang
            </a>
        </div>

        <form action="{{ route('checkout.process') }}" method="POST" id="checkoutForm" @submit="submitting = true" class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
            @csrf

            <!-- Left 2 Cols: Alamat, Produk per Toko, Pilihan Kurir -->
            <div class="lg:col-span-2 space-y-6">
                <!-- 1. Alamat Pengiriman -->
                <div class="bg-white rounded-2xl p-5 sm:p-6 border border-stone-200 shadow-sm space-y-4">
                    <div class="flex items-center justify-between border-b border-stone-100 pb-3">
                        <h2 class="text-xs sm:text-sm font-extrabold text-stone-900 uppercase tracking-wide flex items-center gap-2 font-heading">
                            <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                            Alamat Pengiriman
                        </h2>
                        @if ($addresses->isNotEmpty())
                            <button
                                type="button"
                                @click="addNewAddress = !addNewAddress"
                                class="text-xs font-semibold text-secondary hover:text-primary transition min-h-[38px] px-2 flex items-center"
                                x-text="addNewAddress ? 'Gunakan Alamat Tersimpan' : '+ Tambah Alamat Baru'"
                            ></button>
                        @endif
                    </div>

                    @if ($addresses->isNotEmpty())
                        <div x-show="!addNewAddress" x-transition class="space-y-3">
                            @foreach ($addresses as $addr)
                                <label class="block p-4 rounded-xl border cursor-pointer transition-all duration-150 {{ $selectedAddress && $selectedAddress->id === $addr->id ? 'border-primary bg-orange-50/40 ring-2 ring-primary/20 shadow-sm' : 'border-stone-200 hover:border-stone-400 bg-white' }}">
                                    <div class="flex items-start gap-3">
                                        <input
                                            type="radio"
                                            name="address_id"
                                            value="{{ $addr->id }}"
                                            {{ $selectedAddress && $selectedAddress->id === $addr->id ? 'checked' : '' }}
                                            class="mt-1 text-primary focus:ring-primary border-stone-300 h-4 w-4"
                                        >
                                        <div class="text-xs space-y-1">
                                            <div class="flex items-center gap-2 font-bold text-stone-900">
                                                <span>{{ $addr->recipient_name }}</span>
                                                <span class="text-stone-300">&bull;</span>
                                                <span class="text-stone-600 font-medium">{{ $addr->phone_number }}</span>
                                                @if ($addr->is_primary)
                                                    <span class="bg-primary text-white text-xs font-bold px-2 py-0.5 rounded shadow-xs">Utama</span>
                                                @endif
                                            </div>
                                            <p class="text-stone-700">{{ $addr->full_address }}</p>
                                            <p class="text-stone-400 text-xs">{{ $addr->district ? $addr->district . ', ' : '' }}{{ $addr->city }}, {{ $addr->province }} {{ $addr->postal_code }}</p>
                                        </div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    @endif

                    <!-- Form Tambah Alamat Cepat jika belum ada atau tombol tambah ditekan -->
                    <div x-show="addNewAddress || {{ $addresses->isEmpty() ? 'true' : 'false' }}" x-transition class="space-y-3 pt-2">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                            <div>
                                <label class="font-bold text-stone-700 block mb-1">Nama Penerima</label>
                                <input type="text" name="recipient_name" placeholder="Nama Lengkap" value="{{ old('recipient_name') }}" class="w-full text-xs rounded-xl border-stone-300 focus:border-primary focus:ring-primary py-2 px-3">
                            </div>
                            <div>
                                <label class="font-bold text-stone-700 block mb-1">Nomor Telepon</label>
                                <input type="text" name="phone_number" placeholder="08xxxxxxxxxx" value="{{ old('phone_number') }}" class="w-full text-xs rounded-xl border-stone-300 focus:border-primary focus:ring-primary py-2 px-3">
                            </div>
                            <div class="sm:col-span-2">
                                <label class="font-bold text-stone-700 block mb-1">Alamat Lengkap</label>
                                <textarea name="full_address" rows="2" placeholder="Nama jalan, gedung, nomor rumah, RT/RW" class="w-full text-xs rounded-xl border-stone-300 focus:border-primary focus:ring-primary py-2 px-3">{{ old('full_address') }}</textarea>
                            </div>
                            <div>
                                <label class="font-bold text-stone-700 block mb-1">Provinsi</label>
                                <input type="text" name="province" placeholder="Contoh: DKI Jakarta" value="{{ old('province') }}" class="w-full text-xs rounded-xl border-stone-300 focus:border-primary focus:ring-primary py-2 px-3">
                            </div>
                            <div>
                                <label class="font-bold text-stone-700 block mb-1">Kota / Kabupaten</label>
                                <input type="text" name="city" placeholder="Contoh: Jakarta Selatan" value="{{ old('city') }}" class="w-full text-xs rounded-xl border-stone-300 focus:border-primary focus:ring-primary py-2 px-3">
                            </div>
                            <div>
                                <label class="font-bold text-stone-700 block mb-1">Kode Pos</label>
                                <input type="text" name="postal_code" placeholder="12345" value="{{ old('postal_code') }}" class="w-full text-xs rounded-xl border-stone-300 focus:border-primary focus:ring-primary py-2 px-3">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. Rincian Produk Dipesan per Toko -->
                <div class="space-y-4">
                    @foreach ($itemsByStore as $storeId => $storeItems)
                        @php
                            $store = $storeItems->first()->product->store;
                            $storeSubtotal = $storeItems->sum(fn ($i) => \App\Services\CartService::getItemUnitPrice($i) * $i->quantity);
                        @endphp
                        <div class="bg-white rounded-2xl border border-stone-200 shadow-sm overflow-hidden">
                            <!-- Store Header -->
                            <div class="bg-stone-50 px-5 py-3 border-b border-stone-200 flex items-center justify-between text-xs">
                                <div class="flex items-center gap-2">
                                    <span class="bg-secondary/90 text-white text-xs font-bold px-2 py-0.5 rounded">Toko</span>
                                    <span class="font-bold text-stone-900">{{ $store->name ?? 'Toko' }}</span>
                                    <span class="text-stone-300">&bull;</span>
                                    <span class="text-stone-500">{{ $store->city ?? 'Indonesia' }}</span>
                                </div>
                            </div>

                            <!-- Items in Store -->
                            <div class="divide-y divide-stone-100 p-4 space-y-3">
                                @foreach ($storeItems as $item)
                                    @php
                                        $unitPrice = \App\Services\CartService::getItemUnitPrice($item);
                                        $itemTotal = $unitPrice * $item->quantity;
                                    @endphp
                                    <div class="flex items-center justify-between gap-4 pt-3 first:pt-0">
                                        <div class="flex items-center gap-3">
                                            <div class="w-14 h-14 rounded-xl bg-stone-50 border border-stone-200 overflow-hidden shrink-0 flex items-center justify-center">
                                                @if ($item->product->primaryImage)
                                                    <img src="{{ $item->product->primaryImage->image_path }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover">
                                                @else
                                                    <svg class="w-6 h-6 text-stone-300 stroke-current" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                                @endif
                                            </div>
                                            <div class="text-xs space-y-0.5">
                                                <h4 class="font-bold text-stone-900 line-clamp-1">{{ $item->product->name }}</h4>
                                                @if ($item->variant)
                                                    <span class="text-xs text-stone-600 bg-stone-100 px-2 py-0.5 rounded font-medium inline-block">
                                                        {{ $item->variant->name }}: {{ $item->variant->value }}
                                                    </span>
                                                @endif
                                                <div class="text-xs text-stone-400">
                                                    {{ $item->quantity }} x Rp {{ number_format($unitPrice, 0, ',', '.') }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-xs font-black text-stone-900">
                                            Rp {{ number_format($itemTotal, 0, ',', '.') }}
                                        </div>
                                    </div>
                                @endforeach

                                <!-- Pilihan Kurir Pengiriman Toko Ini -->
                                <div class="bg-orange-50/40 rounded-xl p-3.5 border border-orange-200/60 mt-3 flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 text-xs">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="font-bold text-stone-800">Opsi Pengiriman:</span>
                                        <select name="courier" class="text-xs rounded-xl border-stone-300 focus:ring-primary focus:border-primary py-1.5 px-3 bg-white">
                                            @foreach ($couriers as $key => $c)
                                                <option value="{{ $key }}" {{ request('courier', config('shipping.default_courier')) === $key ? 'selected' : '' }}>
                                                    {{ $c['name'] }} ({{ $c['service'] }}) - Rp {{ number_format($c['cost'], 0, ',', '.') }} [{{ $c['etd'] }}]
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <span class="text-xs font-extrabold text-primary">
                                        Ongkir: Rp {{ number_format($shippingCostPerStore, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Catatan Pembeli -->
                <div class="bg-white rounded-2xl p-5 border border-stone-200 shadow-sm">
                    <label class="text-xs font-bold text-stone-800 uppercase tracking-wide block mb-2">Catatan Pesanan (Opsional):</label>
                    <input type="text" name="notes" placeholder="Contoh: Titipkan di satpam, permintaan khusus, dll." class="w-full text-xs rounded-xl border-stone-300 focus:border-primary focus:ring-primary py-2 px-3">
                </div>
            </div>

            <!-- Right 1 Col: Voucher, Ringkasan Biaya, dan Tombol Bayar -->
            <div class="space-y-6">
                <!-- Voucher Box -->
                <div class="bg-white rounded-2xl p-6 border border-stone-200 shadow-sm space-y-4">
                    <h3 class="text-xs sm:text-sm font-extrabold text-stone-900 uppercase tracking-wide flex items-center gap-2 font-heading">
                        <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                        Voucher TokoKu
                    </h3>

                    @if ($voucher)
                        <div class="bg-orange-50 border border-orange-200 rounded-xl p-3 flex items-center justify-between text-xs">
                            <div>
                                <span class="font-bold text-orange-950 block">{{ $voucher->name }}</span>
                                <span class="text-xs text-primary font-mono font-bold">{{ $voucher->code }} (-Rp {{ number_format($discountAmount, 0, ',', '.') }})</span>
                            </div>
                            <!-- Form Batal Voucher -->
                            <button
                                type="button"
                                onclick="document.getElementById('removeVoucherForm').submit()"
                                class="text-xs text-rose-600 font-bold hover:underline"
                            >
                                Hapus
                            </button>
                        </div>
                    @else
                        <!-- Form Apply Voucher -->
                        <div class="space-y-2">
                            <div class="flex gap-2">
                                <input
                                    type="text"
                                    id="voucherInput"
                                    placeholder="Masukkan kode voucher"
                                    class="w-full uppercase text-xs rounded-xl border-stone-300 focus:border-primary focus:ring-primary py-2 px-3"
                                >
                                <button
                                    type="button"
                                    onclick="applyVoucherAction()"
                                    class="min-h-[44px] px-4 py-2 bg-secondary hover:bg-secondary-hover text-white font-bold text-xs rounded-xl shadow-sm transition"
                                >
                                    Pakai
                                </button>
                            </div>
                            <span class="text-xs text-stone-400 block">Contoh kode promo: DISKONHEMAT, GAJIANSERU, atau GRATISONGKIR</span>
                        </div>
                    @endif
                </div>

                <!-- Metode Pembayaran: DOKU Sandbox Info -->
                <div class="bg-white rounded-2xl p-6 border border-stone-200 shadow-sm space-y-3">
                    <h3 class="text-xs sm:text-sm font-extrabold text-stone-900 uppercase tracking-wide flex items-center gap-2 font-heading">
                        <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                        Metode Pembayaran
                    </h3>

                    <div class="p-3.5 bg-teal-50/50 border border-teal-200 rounded-xl space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-teal-950 flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-secondary animate-pulse"></span>
                                DOKU Payment Gateway
                            </span>
                            <span class="bg-secondary text-white text-xs font-bold px-1.5 py-0.5 rounded">SANDBOX</span>
                        </div>
                        <p class="text-xs text-teal-800 leading-relaxed">
                            Mendukung QRIS, Virtual Account (BCA, Mandiri, BRI, BNI), E-Wallet, dan Kartu Kredit/Debit.
                        </p>
                    </div>
                </div>

                <!-- Ringkasan Biaya & Tombol Submit -->
                <div class="bg-white rounded-2xl p-6 border border-stone-200 shadow-sm space-y-4">
                    <h3 class="text-xs sm:text-sm font-extrabold text-stone-900 uppercase tracking-wide font-heading">
                        Ringkasan Pembayaran
                    </h3>

                    <div class="space-y-2.5 text-xs text-stone-600 border-b border-stone-100 pb-4">
                        <div class="flex justify-between">
                            <span>Subtotal Produk ({{ $selectedItems->sum('quantity') }} item)</span>
                            <span class="font-semibold text-stone-900">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Total Ongkos Kirim ({{ $itemsByStore->count() }} toko)</span>
                            <span class="font-semibold text-stone-900">Rp {{ number_format($totalShippingCost, 0, ',', '.') }}</span>
                        </div>
                        @if ($discountAmount > 0)
                            <div class="flex justify-between text-secondary font-bold">
                                <span>Diskon Voucher</span>
                                <span>-Rp {{ number_format($discountAmount, 0, ',', '.') }}</span>
                            </div>
                        @endif
                    </div>

                    <div class="flex items-baseline justify-between pt-1">
                        <span class="text-xs font-bold text-stone-800">Total Pembayaran:</span>
                        <span class="text-xl font-extrabold text-primary font-heading">
                            Rp {{ number_format($finalAmount, 0, ',', '.') }}
                        </span>
                    </div>

                    <button
                        type="submit"
                        :disabled="submitting"
                        class="w-full min-h-[44px] py-3.5 bg-primary hover:bg-primary-hover text-white font-extrabold text-xs uppercase tracking-wider rounded-xl shadow-md transition flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <template x-if="submitting">
                            <span class="flex items-center gap-2">
                                <svg class="w-4 h-4 animate-spin text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path></svg>
                                <span>Memproses Pesanan...</span>
                            </span>
                        </template>
                        <template x-if="!submitting">
                            <span>Buat Pesanan Sekarang</span>
                        </template>
                    </button>

                    <p class="text-xs text-stone-400 text-center leading-relaxed">
                        Dengan menekan tombol di atas, Anda menyetujui Syarat & Ketentuan transaksi di TokoKu.
                    </p>
                </div>
            </div>
        </form>

        <!-- Form Tersembunyi untuk Voucher Apply & Remove -->
        <form id="applyVoucherForm" action="{{ route('checkout.voucher') }}" method="POST" class="hidden">
            @csrf
            <input type="hidden" name="code" id="hiddenVoucherCode">
        </form>

        <form id="removeVoucherForm" action="{{ route('checkout.remove-voucher') }}" method="POST" class="hidden">
            @csrf
            @method('DELETE')
        </form>

        <script>
            function applyVoucherAction() {
                const val = document.getElementById('voucherInput').value.trim();
                if (!val) {
                    alert('Silakan masukkan kode voucher terlebih dahulu.');
                    return;
                }
                document.getElementById('hiddenVoucherCode').value = val;
                document.getElementById('applyVoucherForm').submit();
            }
        </script>
    </div>
</x-app-layout>
