<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between border-b border-gray-200 pb-4">
            <div class="flex items-center gap-2">
                <div class="w-2.5 h-6 bg-primary rounded-full"></div>
                <h1 class="text-xl font-black text-gray-900 tracking-tight">Checkout Pesanan</h1>
            </div>
            <a href="{{ route('cart.index') }}" class="text-xs font-bold text-secondary hover:text-blue-700 flex items-center gap-1">
                &larr; Kembali ke Keranjang
            </a>
        </div>

        <form action="{{ route('checkout.process') }}" method="POST" id="checkoutForm" class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
            @csrf

            <!-- Left 2 Cols: Alamat, Produk per Toko, Pilihan Kurir -->
            <div class="lg:col-span-2 space-y-6">
                <!-- 1. Alamat Pengiriman -->
                <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm space-y-4" x-data="{ addNewAddress: false }">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                        <h2 class="text-sm font-extrabold text-gray-900 uppercase tracking-wide flex items-center gap-2">
                            <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                            Alamat Pengiriman
                        </h2>
                        @if ($addresses->isNotEmpty())
                            <button
                                type="button"
                                @click="addNewAddress = !addNewAddress"
                                class="text-xs font-bold text-secondary hover:text-blue-700"
                                x-text="addNewAddress ? 'Gunakan Alamat Tersimpan' : '+ Tambah Alamat Baru'"
                            ></button>
                        @endif
                    </div>

                    @if ($addresses->isNotEmpty())
                        <div x-show="!addNewAddress" class="space-y-3">
                            @foreach ($addresses as $addr)
                                <label class="block p-4 rounded-xl border {{ $selectedAddress && $selectedAddress->id === $addr->id ? 'border-primary bg-emerald-50/30 ring-1 ring-primary/20' : 'border-gray-200 hover:border-gray-300' }} cursor-pointer transition">
                                    <div class="flex items-start gap-3">
                                        <input
                                            type="radio"
                                            name="address_id"
                                            value="{{ $addr->id }}"
                                            {{ $selectedAddress && $selectedAddress->id === $addr->id ? 'checked' : '' }}
                                            class="mt-1 text-primary focus:ring-primary border-gray-300"
                                        >
                                        <div class="text-xs space-y-1">
                                            <div class="flex items-center gap-2 font-bold text-gray-900">
                                                <span>{{ $addr->recipient_name }}</span>
                                                <span class="text-gray-400">&bull;</span>
                                                <span>{{ $addr->phone_number }}</span>
                                                @if ($addr->is_primary)
                                                    <span class="bg-primary text-white text-[9px] font-black px-1.5 py-0.5 rounded">Utama</span>
                                                @endif
                                            </div>
                                            <p class="text-gray-600">{{ $addr->full_address }}</p>
                                            <p class="text-gray-400">{{ $addr->district ? $addr->district . ', ' : '' }}{{ $addr->city }}, {{ $addr->province }} {{ $addr->postal_code }}</p>
                                        </div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    @endif

                    <!-- Form Tambah Alamat Cepat jika belum ada atau tombol tambah ditekan -->
                    <div x-show="addNewAddress || {{ $addresses->isEmpty() ? 'true' : 'false' }}" class="space-y-3 pt-2">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                            <div>
                                <label class="font-bold text-gray-700 block mb-1">Nama Penerima</label>
                                <input type="text" name="recipient_name" placeholder="Nama Lengkap" value="{{ old('recipient_name') }}" class="w-full text-xs rounded-lg border-gray-300 focus:border-primary focus:ring-primary">
                            </div>
                            <div>
                                <label class="font-bold text-gray-700 block mb-1">Nomor Telepon</label>
                                <input type="text" name="phone_number" placeholder="08xxxxxxxxxx" value="{{ old('phone_number') }}" class="w-full text-xs rounded-lg border-gray-300 focus:border-primary focus:ring-primary">
                            </div>
                            <div class="sm:col-span-2">
                                <label class="font-bold text-gray-700 block mb-1">Alamat Lengkap</label>
                                <textarea name="full_address" rows="2" placeholder="Nama jalan, gedung, nomor rumah, RT/RW" class="w-full text-xs rounded-lg border-gray-300 focus:border-primary focus:ring-primary">{{ old('full_address') }}</textarea>
                            </div>
                            <div>
                                <label class="font-bold text-gray-700 block mb-1">Provinsi</label>
                                <input type="text" name="province" placeholder="Contoh: DKI Jakarta" value="{{ old('province') }}" class="w-full text-xs rounded-lg border-gray-300 focus:border-primary focus:ring-primary">
                            </div>
                            <div>
                                <label class="font-bold text-gray-700 block mb-1">Kota / Kabupaten</label>
                                <input type="text" name="city" placeholder="Contoh: Jakarta Selatan" value="{{ old('city') }}" class="w-full text-xs rounded-lg border-gray-300 focus:border-primary focus:ring-primary">
                            </div>
                            <div>
                                <label class="font-bold text-gray-700 block mb-1">Kode Pos</label>
                                <input type="text" name="postal_code" placeholder="12345" value="{{ old('postal_code') }}" class="w-full text-xs rounded-lg border-gray-300 focus:border-primary focus:ring-primary">
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
                        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                            <!-- Store Header -->
                            <div class="bg-gray-50/70 px-5 py-3 border-b border-gray-100 flex items-center justify-between text-xs">
                                <div class="flex items-center gap-2">
                                    <span class="bg-secondary text-white text-[9px] font-black px-1.5 py-0.5 rounded">Toko</span>
                                    <span class="font-extrabold text-gray-900">{{ $store->name ?? 'Toko' }}</span>
                                    <span class="text-gray-400">&bull;</span>
                                    <span class="text-gray-500">{{ $store->city ?? 'Indonesia' }}</span>
                                </div>
                            </div>

                            <!-- Items in Store -->
                            <div class="divide-y divide-gray-100 p-4 space-y-3">
                                @foreach ($storeItems as $item)
                                    @php
                                        $unitPrice = \App\Services\CartService::getItemUnitPrice($item);
                                        $itemTotal = $unitPrice * $item->quantity;
                                    @endphp
                                    <div class="flex items-center justify-between gap-4 pt-3 first:pt-0">
                                        <div class="flex items-center gap-3">
                                            <div class="w-14 h-14 rounded-xl bg-gray-50 border border-gray-100 overflow-hidden shrink-0 flex items-center justify-center">
                                                @if ($item->product->primaryImage)
                                                    <img src="{{ $item->product->primaryImage->image_path }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover">
                                                @else
                                                    <svg class="w-6 h-6 text-gray-300 stroke-current" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                                @endif
                                            </div>
                                            <div class="text-xs space-y-0.5">
                                                <h4 class="font-bold text-gray-900 line-clamp-1">{{ $item->product->name }}</h4>
                                                @if ($item->variant)
                                                    <span class="text-[10px] text-gray-500 bg-gray-100 px-1.5 py-0.5 rounded font-medium">
                                                        {{ $item->variant->name }}: {{ $item->variant->value }}
                                                    </span>
                                                @endif
                                                <div class="text-[11px] text-gray-400">
                                                    {{ $item->quantity }} x Rp {{ number_format($unitPrice, 0, ',', '.') }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-xs font-black text-gray-900">
                                            Rp {{ number_format($itemTotal, 0, ',', '.') }}
                                        </div>
                                    </div>
                                @endforeach

                                <!-- Pilihan Kurir Pengiriman Toko Ini -->
                                <div class="bg-emerald-50/30 rounded-xl p-3 border border-emerald-100/60 mt-3 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs">
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold text-emerald-800">Opsi Pengiriman:</span>
                                        <select name="courier" class="text-xs rounded-lg border-gray-200 focus:ring-primary focus:border-primary py-1 px-2.5">
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
                <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
                    <label class="text-xs font-bold text-gray-800 uppercase tracking-wide block mb-2">Catatan Pesanan (Opsional):</label>
                    <input type="text" name="notes" placeholder="Contoh: Titipkan di satpam, warna cadangan, dll." class="w-full text-xs rounded-xl border-gray-200 focus:border-primary focus:ring-primary">
                </div>
            </div>

            <!-- Right 1 Col: Voucher, Ringkasan Biaya, dan Tombol Bayar -->
            <div class="space-y-6">
                <!-- Voucher Box -->
                <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm space-y-4">
                    <h3 class="text-sm font-extrabold text-gray-900 uppercase tracking-wide flex items-center gap-2">
                        <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                        Voucher TokoKu
                    </h3>

                    @if ($voucher)
                        <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-3 flex items-center justify-between text-xs">
                            <div>
                                <span class="font-bold text-emerald-900 block">{{ $voucher->name }}</span>
                                <span class="text-[11px] text-emerald-700 font-mono font-bold">{{ $voucher->code }} (-Rp {{ number_format($discountAmount, 0, ',', '.') }})</span>
                            </div>
                            <!-- Form Batal Voucher -->
                            <button
                                type="button"
                                onclick="document.getElementById('removeVoucherForm').submit()"
                                class="text-xs text-red-600 font-bold hover:underline"
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
                                    class="w-full uppercase text-xs rounded-xl border-gray-200 focus:border-primary focus:ring-primary"
                                >
                                <button
                                    type="button"
                                    onclick="applyVoucherAction()"
                                    class="px-4 py-2 bg-secondary hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-sm transition"
                                >
                                    Pakai
                                </button>
                            </div>
                            <span class="text-[10px] text-gray-400 block">Coba kode promo: DISKONHEMAT, GAJIANSERU, atau GRATISONGKIR</span>
                        </div>
                    @endif
                </div>

                <!-- Metode Pembayaran: DOKU Sandbox Info -->
                <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm space-y-3">
                    <h3 class="text-sm font-extrabold text-gray-900 uppercase tracking-wide flex items-center gap-2">
                        <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                        Metode Pembayaran
                    </h3>

                    <div class="p-3.5 bg-blue-50/50 border border-blue-200 rounded-xl space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-blue-900 flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-blue-600 animate-pulse"></span>
                                DOKU Payment Gateway
                            </span>
                            <span class="bg-blue-600 text-white text-[9px] font-black px-1.5 py-0.5 rounded">SANDBOX</span>
                        </div>
                        <p class="text-[11px] text-blue-700 leading-relaxed">
                            Mendukung QRIS, Virtual Account (BCA, Mandiri, BRI, BNI), E-Wallet, dan Kartu Kredit/Debit.
                        </p>
                    </div>
                </div>

                <!-- Ringkasan Biaya & Tombol Submit -->
                <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm space-y-4">
                    <h3 class="text-sm font-extrabold text-gray-900 uppercase tracking-wide">
                        Ringkasan Pembayaran
                    </h3>

                    <div class="space-y-2.5 text-xs text-gray-600 border-b border-gray-100 pb-4">
                        <div class="flex justify-between">
                            <span>Subtotal Produk ({{ $selectedItems->sum('quantity') }} item)</span>
                            <span class="font-semibold text-gray-900">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Total Ongkos Kirim ({{ $itemsByStore->count() }} toko)</span>
                            <span class="font-semibold text-gray-900">Rp {{ number_format($totalShippingCost, 0, ',', '.') }}</span>
                        </div>
                        @if ($discountAmount > 0)
                            <div class="flex justify-between text-emerald-600 font-bold">
                                <span>Diskon Voucher</span>
                                <span>-Rp {{ number_format($discountAmount, 0, ',', '.') }}</span>
                            </div>
                        @endif
                    </div>

                    <div class="flex items-baseline justify-between pt-1">
                        <span class="text-xs font-bold text-gray-800">Total Pembayaran:</span>
                        <span class="text-xl font-black text-primary">
                            Rp {{ number_format($finalAmount, 0, ',', '.') }}
                        </span>
                    </div>

                    <button
                        type="submit"
                        class="w-full py-3.5 bg-primary hover:bg-emerald-700 text-white font-extrabold text-xs uppercase tracking-wider rounded-xl shadow-md transition flex items-center justify-center gap-2"
                    >
                        Buat Pesanan Sekarang
                    </button>

                    <p class="text-[10px] text-gray-400 text-center leading-relaxed">
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
