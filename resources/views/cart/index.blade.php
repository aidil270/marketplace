<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
        <!-- Breadcrumb / Header -->
        <div class="flex items-center justify-between border-b border-gray-200 pb-4">
            <div class="flex items-center gap-2">
                <div class="w-2.5 h-6 bg-primary rounded-full"></div>
                <h1 class="text-xl font-black text-gray-900 tracking-tight">Keranjang Belanja</h1>
            </div>
            <a href="{{ route('home') }}" class="text-xs font-bold text-secondary hover:text-blue-700 flex items-center gap-1">
                &larr; Lanjut Belanja
            </a>
        </div>

        @if ($items->isEmpty())
            <!-- Empty Cart State -->
            <div class="bg-white rounded-2xl p-12 border border-gray-100 text-center space-y-4 max-w-xl mx-auto shadow-sm">
                <div class="w-20 h-20 rounded-full bg-emerald-50 text-primary mx-auto flex items-center justify-center">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <h3 class="text-lg font-bold text-gray-800">Keranjang Belanjamu Masih Kosong</h3>
                <p class="text-xs text-gray-500 max-w-sm mx-auto">Yuk, isi keranjangmu dengan beragam produk pilihan berkualitas dengan harga terbaik di TokoKu.</p>
                <div class="pt-2">
                    <a href="{{ route('home') }}" class="inline-flex items-center px-6 py-2.5 bg-primary hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-md transition">
                        Mulai Belanja Sekarang
                    </a>
                </div>
            </div>
        @else
            <!-- Table Header Bar (Shopee Style) -->
            <div class="bg-white rounded-xl p-4 border border-gray-100 shadow-sm hidden md:grid grid-cols-12 gap-4 text-xs font-bold text-gray-500 uppercase tracking-wider items-center">
                <div class="col-span-6 flex items-center gap-3">
                    <form action="{{ route('cart.select-all') }}" method="POST" id="selectAllForm">
                        @csrf
                        <input
                            type="checkbox"
                            name="select"
                            value="1"
                            {{ $items->every(fn($i) => $i->is_selected) ? 'checked' : '' }}
                            onchange="this.form.submit()"
                            class="rounded border-gray-300 text-primary focus:ring-primary h-4 w-4"
                        >
                    </form>
                    <span>Produk</span>
                </div>
                <div class="col-span-2 text-center">Harga Satuan</div>
                <div class="col-span-2 text-center">Kuantitas</div>
                <div class="col-span-1 text-center">Total</div>
                <div class="col-span-1 text-center">Aksi</div>
            </div>

            <!-- Items Grouped By Store -->
            <div class="space-y-4">
                @foreach ($groupedItems as $storeId => $storeItems)
                    @php
                        $store = $storeItems->first()->product->store;
                    @endphp
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                        <!-- Store Header -->
                        <div class="bg-gray-50/70 px-4 py-3 border-b border-gray-100 flex items-center justify-between text-xs">
                            <div class="flex items-center gap-2">
                                <span class="bg-secondary text-white text-[9px] font-black px-1.5 py-0.5 rounded">Toko</span>
                                <span class="font-extrabold text-gray-900">{{ $store->name ?? 'Toko' }}</span>
                                <span class="text-gray-400">&bull;</span>
                                <span class="text-gray-500">{{ $store->city ?? 'Indonesia' }}</span>
                            </div>
                        </div>

                        <!-- Store Product Rows -->
                        <div class="divide-y divide-gray-100">
                            @foreach ($storeItems as $item)
                                @php
                                    $unitPrice = \App\Services\CartService::getItemUnitPrice($item);
                                    $itemTotal = $unitPrice * $item->quantity;
                                @endphp
                                <div class="p-4 grid grid-cols-1 md:grid-cols-12 gap-4 items-center hover:bg-gray-50/40 transition">
                                    <!-- Checkbox & Product Info -->
                                    <div class="col-span-1 md:col-span-6 flex items-center gap-3">
                                        <form action="{{ route('cart.toggle-select', $item->id) }}" method="POST">
                                            @csrf
                                            <input
                                                type="checkbox"
                                                {{ $item->is_selected ? 'checked' : '' }}
                                                onchange="this.form.submit()"
                                                class="rounded border-gray-300 text-primary focus:ring-primary h-4 w-4"
                                            >
                                        </form>

                                        <div class="w-16 h-16 rounded-xl bg-gray-50 border border-gray-100 overflow-hidden shrink-0 flex items-center justify-center">
                                            @if ($item->product->primaryImage)
                                                <img src="{{ $item->product->primaryImage->image_path }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover">
                                            @else
                                                <svg class="w-8 h-8 text-gray-300 stroke-current" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                            @endif
                                        </div>

                                        <div class="space-y-1">
                                            <a href="{{ route('products.show', $item->product->slug) }}" class="text-xs font-bold text-gray-800 hover:text-primary transition line-clamp-2">
                                                {{ $item->product->name }}
                                            </a>
                                            @if ($item->variant)
                                                <span class="inline-block bg-gray-100 text-gray-600 text-[10px] font-semibold px-2 py-0.5 rounded">
                                                    {{ $item->variant->name }}: {{ $item->variant->value }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Unit Price -->
                                    <div class="col-span-1 md:col-span-2 text-left md:text-center text-xs font-semibold text-gray-700">
                                        <span class="md:hidden text-gray-400 font-normal">Harga: </span>
                                        Rp {{ number_format($unitPrice, 0, ',', '.') }}
                                    </div>

                                    <!-- Quantity Modifier Form -->
                                    <div class="col-span-1 md:col-span-2 flex items-center justify-start md:justify-center">
                                        <div class="inline-flex items-center border border-gray-200 rounded-lg overflow-hidden bg-white shadow-sm">
                                            <form action="{{ route('cart.update', $item->id) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="quantity" value="{{ max(1, $item->quantity - 1) }}">
                                                <button type="submit" {{ $item->quantity <= 1 ? 'disabled' : '' }} class="px-2.5 py-1 text-gray-500 hover:bg-gray-100 disabled:opacity-30 font-bold text-xs">-</button>
                                            </form>

                                            <span class="w-10 text-center text-xs font-bold text-gray-800">{{ $item->quantity }}</span>

                                            <form action="{{ route('cart.update', $item->id) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="quantity" value="{{ $item->quantity + 1 }}">
                                                <button type="submit" class="px-2.5 py-1 text-gray-500 hover:bg-gray-100 font-bold text-xs">+</button>
                                            </form>
                                        </div>
                                    </div>

                                    <!-- Subtotal -->
                                    <div class="col-span-1 md:col-span-1 text-left md:text-center text-xs font-black text-primary">
                                        <span class="md:hidden text-gray-400 font-normal">Subtotal: </span>
                                        Rp {{ number_format($itemTotal, 0, ',', '.') }}
                                    </div>

                                    <!-- Delete Action -->
                                    <div class="col-span-1 md:col-span-1 flex justify-start md:justify-center">
                                        <form action="{{ route('cart.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus produk ini dari keranjang?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs text-red-500 hover:text-red-700 font-bold flex items-center gap-1 transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                <span class="md:hidden">Hapus</span>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Sticky Bottom Checkout Bar (Shopee Style) -->
            <div class="sticky bottom-0 z-40 bg-white border border-gray-200 rounded-2xl shadow-xl p-4 sm:p-5 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-3 text-xs">
                    <form action="{{ route('cart.select-all') }}" method="POST">
                        @csrf
                        <label class="flex items-center gap-2 cursor-pointer font-bold text-gray-700">
                            <input
                                type="checkbox"
                                name="select"
                                value="1"
                                {{ $items->every(fn($i) => $i->is_selected) ? 'checked' : '' }}
                                onchange="this.form.submit()"
                                class="rounded border-gray-300 text-primary focus:ring-primary h-4 w-4"
                            >
                            <span>Pilih Semua ({{ $items->count() }})</span>
                        </label>
                    </form>
                </div>

                <div class="flex items-center gap-6 w-full sm:w-auto justify-between sm:justify-end">
                    <div class="text-right">
                        <div class="text-xs text-gray-500">
                            Total (<span class="font-bold text-gray-800">{{ $selectedCount }}</span> produk terpilih):
                        </div>
                        <div class="text-lg sm:text-2xl font-black text-primary">
                            Rp {{ number_format($subtotal, 0, ',', '.') }}
                        </div>
                    </div>

                    <a
                        href="{{ route('checkout.index') }}"
                        class="px-8 py-3 bg-primary hover:bg-emerald-700 text-white font-extrabold text-xs uppercase tracking-wider rounded-xl shadow-md transition flex items-center justify-center {{ $selectedCount == 0 ? 'pointer-events-none opacity-50' : '' }}"
                    >
                        Checkout ({{ $selectedCount }})
                    </a>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
