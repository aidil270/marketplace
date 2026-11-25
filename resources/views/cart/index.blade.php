<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6 pb-24 md:pb-8">
        <!-- Breadcrumb / Header -->
        <div class="flex items-center justify-between border-b border-stone-200 pb-4">
            <div class="flex items-center gap-2.5">
                <div class="w-2.5 h-6 bg-primary rounded-full"></div>
                <h1 class="text-xl font-extrabold text-stone-900 tracking-tight font-heading">Keranjang Belanja</h1>
            </div>
            <a href="{{ route('home') }}" class="text-xs font-semibold text-secondary hover:text-primary transition flex items-center gap-1">
                &larr; Lanjut Belanja
            </a>
        </div>

        @if ($items->isEmpty())
            <!-- Empty Cart State -->
            <div class="bg-white rounded-2xl p-12 border border-stone-200 text-center space-y-4 max-w-xl mx-auto shadow-sm">
                <div class="w-20 h-20 rounded-full bg-orange-50 text-primary mx-auto flex items-center justify-center">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <h3 class="text-lg font-bold text-stone-800 font-heading">Keranjang Belanjamu Masih Kosong</h3>
                <p class="text-xs text-stone-500 max-w-sm mx-auto">Yuk, isi keranjangmu dengan beragam produk kuliner dan jasa pilihan terbaik di TokoKu.</p>
                <div class="pt-2">
                    <a href="{{ route('home') }}" class="inline-flex items-center px-6 py-3 bg-primary hover:bg-primary-hover text-white font-bold text-xs rounded-xl shadow-md transition min-h-[44px]">
                        Mulai Belanja Sekarang
                    </a>
                </div>
            </div>
        @else
            <!-- Desktop Table Header Bar (>= md) -->
            <div class="bg-white rounded-xl p-4 border border-stone-200 shadow-sm hidden md:grid grid-cols-12 gap-4 text-xs font-bold text-stone-500 uppercase tracking-wider items-center">
                <div class="col-span-6 flex items-center gap-3">
                    <form action="{{ route('cart.select-all') }}" method="POST" id="selectAllFormDesktop">
                        @csrf
                        <input
                            type="checkbox"
                            name="select"
                            value="1"
                            {{ $items->every(fn($i) => $i->is_selected) ? 'checked' : '' }}
                            onchange="this.form.submit()"
                            class="rounded border-stone-300 text-primary focus:ring-primary h-4.5 w-4.5 cursor-pointer"
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
                    <div class="bg-white rounded-2xl border border-stone-200 shadow-sm overflow-hidden">
                        <!-- Store Header -->
                        <div class="bg-stone-50 px-4 py-3 border-b border-stone-200 flex items-center justify-between text-xs">
                            <div class="flex items-center gap-2">
                                <span class="bg-secondary/90 text-white text-xs font-bold px-2 py-0.5 rounded">Toko</span>
                                <span class="font-bold text-stone-900">{{ $store->name ?? 'Toko' }}</span>
                                <span class="text-stone-300">&bull;</span>
                                <span class="text-stone-500">{{ $store->city ?? 'Indonesia' }}</span>
                            </div>
                        </div>

                        <!-- Store Product Rows / Mobile Cards -->
                        <div class="divide-y divide-stone-100">
                            @foreach ($storeItems as $item)
                                @php
                                    $unitPrice = \App\Services\CartService::getItemUnitPrice($item);
                                    $itemTotal = $unitPrice * $item->quantity;
                                @endphp
                                <!-- Desktop Row View (>= md) -->
                                <div class="hidden md:grid md:grid-cols-12 gap-4 items-center p-4 hover:bg-stone-50/60 transition">
                                    <!-- Checkbox & Product Info -->
                                    <div class="col-span-6 flex items-center gap-3">
                                        <form action="{{ route('cart.toggle-select', $item->id) }}" method="POST">
                                            @csrf
                                            <input
                                                type="checkbox"
                                                {{ $item->is_selected ? 'checked' : '' }}
                                                onchange="this.form.submit()"
                                                class="rounded border-stone-300 text-primary focus:ring-primary h-4.5 w-4.5 cursor-pointer"
                                            >
                                        </form>

                                        <div class="w-16 h-16 rounded-xl bg-stone-50 border border-stone-200 overflow-hidden shrink-0 flex items-center justify-center">
                                            @if ($item->product->primaryImage)
                                                <img src="{{ $item->product->primaryImage->image_path }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover">
                                            @else
                                                <svg class="w-8 h-8 text-stone-300 stroke-current" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                            @endif
                                        </div>

                                        <div class="space-y-1">
                                            <a href="{{ route('products.show', $item->product->slug) }}" class="text-xs font-bold text-stone-800 hover:text-primary transition line-clamp-2">
                                                {{ $item->product->name }}
                                            </a>
                                            @if ($item->variant)
                                                <span class="inline-block bg-stone-100 text-stone-600 text-xs font-semibold px-2 py-0.5 rounded">
                                                    {{ $item->variant->name }}: {{ $item->variant->value }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Unit Price -->
                                    <div class="col-span-2 text-center text-xs font-semibold text-stone-700">
                                        Rp {{ number_format($unitPrice, 0, ',', '.') }}
                                    </div>

                                    <!-- Quantity Modifier Form -->
                                    <div class="col-span-2 flex items-center justify-center">
                                        <div class="inline-flex items-center border border-stone-300 rounded-lg overflow-hidden bg-white shadow-sm min-h-[38px]">
                                            <form action="{{ route('cart.update', $item->id) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="quantity" value="{{ max(1, $item->quantity - 1) }}">
                                                <button type="submit" {{ $item->quantity <= 1 ? 'disabled' : '' }} class="w-8 h-8 flex items-center justify-center text-stone-500 hover:bg-stone-100 disabled:opacity-30 font-bold text-sm" aria-label="Kurang">-</button>
                                            </form>

                                            <span class="w-10 text-center text-xs font-bold text-stone-800">{{ $item->quantity }}</span>

                                            <form action="{{ route('cart.update', $item->id) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="quantity" value="{{ $item->quantity + 1 }}">
                                                <button type="submit" class="w-8 h-8 flex items-center justify-center text-stone-500 hover:bg-stone-100 font-bold text-sm" aria-label="Tambah">+</button>
                                            </form>
                                        </div>
                                    </div>

                                    <!-- Subtotal -->
                                    <div class="col-span-1 text-center text-xs font-extrabold text-primary">
                                        Rp {{ number_format($itemTotal, 0, ',', '.') }}
                                    </div>

                                    <!-- Delete Action -->
                                    <div class="col-span-1 flex justify-center">
                                        <form action="{{ route('cart.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus produk ini dari keranjang?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 text-stone-400 hover:text-rose-600 transition" title="Hapus produk" aria-label="Hapus produk">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                <!-- Mobile Card View (< md) -->
                                <div class="md:hidden p-4 space-y-3">
                                    <div class="flex items-start gap-3">
                                        <!-- Checkbox with 44px tap target -->
                                        <form action="{{ route('cart.toggle-select', $item->id) }}" method="POST" class="shrink-0 pt-1">
                                            @csrf
                                            <label class="flex items-center justify-center w-8 h-8 cursor-pointer">
                                                <input
                                                    type="checkbox"
                                                    {{ $item->is_selected ? 'checked' : '' }}
                                                    onchange="this.form.submit()"
                                                    class="rounded border-stone-300 text-primary focus:ring-primary h-5 w-5"
                                                >
                                            </label>
                                        </form>

                                        <!-- Image -->
                                        <div class="w-16 h-16 rounded-xl bg-stone-50 border border-stone-200 overflow-hidden shrink-0 flex items-center justify-center">
                                            @if ($item->product->primaryImage)
                                                <img src="{{ $item->product->primaryImage->image_path }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover">
                                            @else
                                                <svg class="w-7 h-7 text-stone-300 stroke-current" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                            @endif
                                        </div>

                                        <!-- Info -->
                                        <div class="flex-1 min-w-0 space-y-1">
                                            <a href="{{ route('products.show', $item->product->slug) }}" class="text-xs font-bold text-stone-900 hover:text-primary transition line-clamp-2">
                                                {{ $item->product->name }}
                                            </a>
                                            @if ($item->variant)
                                                <span class="inline-block bg-stone-100 text-stone-600 text-xs font-semibold px-2 py-0.5 rounded">
                                                    {{ $item->variant->name }}: {{ $item->variant->value }}
                                                </span>
                                            @endif
                                            <div class="text-xs font-extrabold text-primary pt-0.5">
                                                Rp {{ number_format($unitPrice, 0, ',', '.') }}
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Bottom Row: Quantity + Subtotal + Delete -->
                                    <div class="flex items-center justify-between pt-2 border-t border-stone-100">
                                        <!-- Quantity Form -->
                                        <div class="inline-flex items-center border border-stone-300 rounded-lg overflow-hidden bg-white shadow-sm min-h-[38px]">
                                            <form action="{{ route('cart.update', $item->id) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="quantity" value="{{ max(1, $item->quantity - 1) }}">
                                                <button type="submit" {{ $item->quantity <= 1 ? 'disabled' : '' }} class="w-9 h-9 flex items-center justify-center text-stone-600 hover:bg-stone-100 disabled:opacity-30 font-bold text-base" aria-label="Kurang">-</button>
                                            </form>

                                            <span class="w-10 text-center text-xs font-bold text-stone-800">{{ $item->quantity }}</span>

                                            <form action="{{ route('cart.update', $item->id) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="quantity" value="{{ $item->quantity + 1 }}">
                                                <button type="submit" class="w-9 h-9 flex items-center justify-center text-stone-600 hover:bg-stone-100 font-bold text-base" aria-label="Tambah">+</button>
                                            </form>
                                        </div>

                                        <div class="flex items-center gap-3">
                                            <span class="text-xs font-black text-stone-900">
                                                Rp {{ number_format($itemTotal, 0, ',', '.') }}
                                            </span>

                                            <form action="{{ route('cart.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus produk ini dari keranjang?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-2 text-rose-500 hover:text-rose-700 min-h-[44px] min-w-[44px] flex items-center justify-center transition" aria-label="Hapus">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Sticky Bottom Checkout Bar (Mobile & Desktop) -->
            <div class="sticky bottom-16 md:bottom-0 z-40 bg-white/95 backdrop-blur border border-stone-200 rounded-2xl shadow-xl p-4 sm:p-5 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-3 text-xs w-full sm:w-auto justify-between sm:justify-start">
                    <form action="{{ route('cart.select-all') }}" method="POST">
                        @csrf
                        <label class="flex items-center gap-2.5 cursor-pointer font-bold text-stone-700 min-h-[44px]">
                            <input
                                type="checkbox"
                                name="select"
                                value="1"
                                {{ $items->every(fn($i) => $i->is_selected) ? 'checked' : '' }}
                                onchange="this.form.submit()"
                                class="rounded border-stone-300 text-primary focus:ring-primary h-5 w-5"
                            >
                            <span>Pilih Semua ({{ $items->count() }})</span>
                        </label>
                    </form>
                </div>

                <div class="flex items-center gap-4 sm:gap-6 w-full sm:w-auto justify-between sm:justify-end">
                    <div class="text-right">
                        <div class="text-xs text-stone-500">
                            Total (<span class="font-bold text-stone-800">{{ $selectedCount }}</span> produk):
                        </div>
                        <div class="text-lg sm:text-2xl font-black text-primary font-heading">
                            Rp {{ number_format($subtotal, 0, ',', '.') }}
                        </div>
                    </div>

                    <a
                        href="{{ route('checkout.index') }}"
                        class="min-h-[44px] px-6 sm:px-8 py-3 bg-primary hover:bg-primary-hover text-white font-extrabold text-xs uppercase tracking-wider rounded-xl shadow-md transition flex items-center justify-center {{ $selectedCount == 0 ? 'pointer-events-none opacity-50' : '' }}"
                    >
                        Checkout ({{ $selectedCount }})
                    </a>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
