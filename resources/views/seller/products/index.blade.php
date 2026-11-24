<x-seller-layout title="Kelola Produk Toko">
    <x-slot name="header">
        Daftar Produk Toko
    </x-slot>

    <div class="space-y-6">
        <!-- Notification Alert -->
        @if (session('success'))
            <div class="p-4 bg-emerald-50 border-l-4 border-emerald-500 rounded-r-xl text-emerald-800 text-xs font-semibold flex items-center justify-between shadow-sm">
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if (session('info'))
            <div class="p-4 bg-blue-50 border-l-4 border-secondary rounded-r-xl text-blue-800 text-xs font-semibold flex items-center justify-between shadow-sm">
                <span>{{ session('info') }}</span>
            </div>
        @endif

        <!-- Header Actions & Filter Toolbar -->
        <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm space-y-4">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <div>
                    <h3 class="font-bold text-gray-900 text-sm">Semua Produk ({{ $products->total() }})</h3>
                    <p class="text-xs text-gray-400">Kelola informasi harga, stok, dan status produk jualanmu</p>
                </div>
                <a href="{{ route('seller.products.create') }}" class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-primary hover:bg-emerald-700 text-white text-xs font-bold shadow-md shadow-emerald-700/20 transition flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Tambah Produk Baru</span>
                </a>
            </div>

            <!-- Filter & Search Form -->
            <form method="GET" action="{{ route('seller.products.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3 pt-3 border-t border-gray-100">
                <!-- Search Input -->
                <div class="sm:col-span-2 relative">
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama produk..."
                        class="w-full text-xs border-gray-300 rounded-xl pl-9 focus:ring-primary focus:border-primary shadow-sm" />
                    <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>

                <!-- Kategori Dropdown -->
                <div>
                    <select name="category_id" onchange="this.form.submit()" class="w-full text-xs border-gray-300 rounded-xl focus:ring-primary focus:border-primary shadow-sm">
                        <option value="">Semua Kategori</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Status Filter -->
                <div class="flex items-center gap-2">
                    <select name="status" onchange="this.form.submit()" class="w-full text-xs border-gray-300 rounded-xl focus:ring-primary focus:border-primary shadow-sm">
                        <option value="">Semua Status</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                    @if (request()->hasAny(['q', 'category_id', 'status']))
                        <a href="{{ route('seller.products.index') }}" class="p-2 text-gray-400 hover:text-gray-700 text-xs font-semibold" title="Reset Filter">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Products Table -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            @if ($products->isEmpty())
                <div class="p-12 text-center text-gray-400">
                    <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    <h4 class="text-sm font-bold text-gray-700">Tidak ada produk ditemukan</h4>
                    <p class="text-xs text-gray-500 mt-1">Coba ubah kata kunci atau filter pencarian Anda, atau tambahkan produk baru.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-gray-50 text-gray-500 uppercase text-[10px] tracking-wider border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-3.5">Produk</th>
                                <th class="px-6 py-3.5">Kategori</th>
                                <th class="px-6 py-3.5">Harga</th>
                                <th class="px-6 py-3.5">Stok</th>
                                <th class="px-6 py-3.5 text-center">Terjual</th>
                                <th class="px-6 py-3.5 text-center">Status</th>
                                <th class="px-6 py-3.5 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($products as $prod)
                                <tr class="hover:bg-gray-50/70 transition">
                                    <!-- Foto & Nama Produk -->
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-12 h-12 rounded-xl bg-gray-100 border border-gray-200 overflow-hidden shrink-0">
                                                @if ($prod->primaryImage)
                                                    <img src="{{ $prod->primaryImage->image_path }}" alt="{{ $prod->name }}" class="w-full h-full object-cover">
                                                @else
                                                    <div class="w-full h-full flex items-center justify-center text-gray-400">
                                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="max-w-xs truncate">
                                                <a href="{{ route('products.show', $prod->slug) }}" target="_blank" class="font-bold text-gray-900 hover:text-primary transition truncate block" title="{{ $prod->name }}">
                                                    {{ $prod->name }}
                                                </a>
                                                <span class="text-[10px] text-gray-400 font-mono">SKU / ID: #{{ $prod->id }}</span>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Kategori -->
                                    <td class="px-6 py-4 font-medium text-gray-600">
                                        {{ $prod->category->name ?? '-' }}
                                    </td>

                                    <!-- Harga -->
                                    <td class="px-6 py-4 font-black text-primary">
                                        Rp{{ number_format($prod->price, 0, ',', '.') }}
                                    </td>

                                    <!-- Stok -->
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-0.5 rounded text-[11px] font-black {{ $prod->stock <= 5 ? ($prod->stock == 0 ? 'bg-red-500 text-white' : 'bg-amber-100 text-amber-800') : 'bg-gray-100 text-gray-700' }}">
                                            {{ $prod->stock }} unit
                                        </span>
                                    </td>

                                    <!-- Terjual -->
                                    <td class="px-6 py-4 text-center font-bold text-gray-700">
                                        {{ $prod->order_items_count }}
                                    </td>

                                    <!-- Status (Aktif / Nonaktif Toggle) -->
                                    <td class="px-6 py-4 text-center">
                                        <form method="POST" action="{{ route('seller.products.toggle-status', $prod->id) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold transition {{ $prod->is_active ? 'bg-emerald-50 text-primary hover:bg-emerald-100' : 'bg-gray-100 text-gray-500 hover:bg-gray-200' }}" title="Klik untuk mengubah status produk">
                                                <span class="w-1.5 h-1.5 rounded-full {{ $prod->is_active ? 'bg-primary' : 'bg-gray-400' }}"></span>
                                                <span>{{ $prod->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                                            </button>
                                        </form>
                                    </td>

                                    <!-- Aksi -->
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('seller.products.edit', $prod->id) }}" class="p-1.5 text-secondary hover:bg-blue-50 rounded-lg transition" title="Edit Produk">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            </a>

                                            <form method="POST" action="{{ route('seller.products.destroy', $prod->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 text-red-500 hover:bg-red-50 rounded-lg transition" title="Hapus Produk">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="p-4 border-t border-gray-100">
                    {{ $products->links() }}
                </div>
            @endif
        </div>
    </div>
</x-seller-layout>
