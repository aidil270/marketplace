<x-admin-layout title="Kelola Toko">
    <x-slot name="header">
        Kelola Toko & Penjual
    </x-slot>

    <div class="space-y-6" x-data="{
        modalOpen: false,
        activeStoreId: null,
        activeStoreName: '',
        openSuspendModal(id, name) {
            this.activeStoreId = id;
            this.activeStoreName = name;
            this.modalOpen = true;
        }
    }">
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

        <!-- Filter & Search Toolbar -->
        <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-gray-900 text-sm">Daftar Toko ({{ $stores->total() }})</h3>
                    <p class="text-xs text-gray-400">Pantau aktivitas merchant, tangguhkan atau aktifkan toko</p>
                </div>
            </div>

            <form method="GET" action="{{ route('admin.stores.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3 pt-3 border-t border-gray-100">
                <!-- Search Input -->
                <div class="sm:col-span-3 relative">
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama toko, kota, atau nama pemilik..."
                        class="w-full text-xs border-gray-300 rounded-xl pl-9 focus:ring-primary focus:border-primary shadow-sm" />
                    <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>

                <!-- Status Filter -->
                <div class="flex items-center gap-2">
                    <select name="status" onchange="this.form.submit()" class="w-full text-xs border-gray-300 rounded-xl focus:ring-primary focus:border-primary shadow-sm">
                        <option value="">Semua Status</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Ditangguhkan</option>
                    </select>
                    @if (request()->hasAny(['q', 'status']))
                        <a href="{{ route('admin.stores.index') }}" class="p-2 text-gray-400 hover:text-gray-700 text-xs font-semibold">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Stores Table -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            @if ($stores->isEmpty())
                <div class="p-12 text-center text-gray-400">
                    <h4 class="text-sm font-bold text-gray-700">Tidak ada toko ditemukan</h4>
                    <p class="text-xs text-gray-500 mt-1">Coba sesuaikan kata kunci atau filter status pencarian Anda.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-gray-50 text-gray-500 uppercase text-[10px] tracking-wider border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-3.5">Toko</th>
                                <th class="px-6 py-3.5">Pemilik</th>
                                <th class="px-6 py-3.5">Kota</th>
                                <th class="px-6 py-3.5 text-center">Produk</th>
                                <th class="px-6 py-3.5 text-center">Pesanan</th>
                                <th class="px-6 py-3.5 text-center">Status</th>
                                <th class="px-6 py-3.5 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($stores as $s)
                                <tr class="hover:bg-gray-50/70 transition">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-xl bg-gray-100 border border-gray-200 overflow-hidden shrink-0 flex items-center justify-center font-bold text-primary">
                                                @if ($s->logo)
                                                    <img src="{{ $s->logo }}" alt="{{ $s->name }}" class="w-full h-full object-cover">
                                                @else
                                                    {{ strtoupper(substr($s->name, 0, 1)) }}
                                                @endif
                                            </div>
                                            <div>
                                                <a href="{{ route('stores.show', $s->slug) }}" target="_blank" class="font-bold text-gray-900 hover:text-primary transition block">
                                                    {{ $s->name }}
                                                </a>
                                                <span class="text-[10px] text-gray-400 font-mono">/toko/{{ $s->slug }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 font-medium text-gray-800">
                                        {{ $s->user->name ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-600">
                                        {{ $s->city }}
                                    </td>
                                    <td class="px-6 py-4 text-center font-bold text-gray-700">
                                        {{ $s->products_count }}
                                    </td>
                                    <td class="px-6 py-4 text-center font-bold text-gray-700">
                                        {{ $s->orders_count }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $s->is_active ? 'bg-emerald-50 text-primary' : 'bg-red-50 text-red-600' }}">
                                            {{ $s->is_active ? 'Aktif' : 'Ditangguhkan' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('admin.stores.show', $s->id) }}" class="p-1.5 text-gray-500 hover:text-secondary hover:bg-blue-50 rounded-lg transition" title="Lihat Detail">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            </a>

                                            @if ($s->is_active)
                                                <button type="button" @click="openSuspendModal({{ $s->id }}, '{{ addslashes($s->name) }}')"
                                                    class="px-2.5 py-1 rounded-lg text-[10px] font-bold bg-red-50 text-red-600 hover:bg-red-100 transition">
                                                    Tangguhkan
                                                </button>
                                            @else
                                                <form method="POST" action="{{ route('admin.stores.toggle-status', $s->id) }}" onsubmit="return confirm('Aktifkan kembali toko ini?');">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="px-2.5 py-1 rounded-lg text-[10px] font-bold bg-emerald-50 text-primary hover:bg-emerald-100 transition">
                                                        Aktifkan
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="p-4 border-t border-gray-100">
                    {{ $stores->links() }}
                </div>
            @endif
        </div>

        <!-- Modal Penangguhan Toko -->
        <div 
            x-show="modalOpen" 
            x-cloak 
            x-transition:enter="transition-opacity ease-linear duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 overflow-y-auto bg-stone-950/60 backdrop-blur-sm flex items-center justify-center p-4"
        >
            <div 
                @click.outside="modalOpen = false" 
                x-show="modalOpen"
                x-transition:enter="transition ease-out duration-200 transform"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150 transform"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                x-data="{ suspending: false }"
                class="bg-white rounded-2xl max-w-md w-full p-6 space-y-4 shadow-2xl border border-stone-200"
            >
                <h3 class="font-bold text-stone-900 text-sm font-heading">Tangguhkan Toko: <span x-text="activeStoreName" class="text-rose-600"></span></h3>
                <p class="text-xs text-stone-500 leading-relaxed">Toko yang ditangguhkan tidak dapat mengakses area penjual dan seluruh produknya tidak akan tampil di katalog marketplace.</p>

                <form :action="`/admin/toko/${activeStoreId}/toggle-status`" method="POST" @submit="suspending = true" class="space-y-4">
                    @csrf
                    @method('PATCH')
                    <div>
                        <label class="block text-xs font-bold text-stone-700 uppercase mb-1">Alasan Penangguhan (Opsional)</label>
                        <textarea name="suspension_reason" rows="3" placeholder="Contoh: Pelanggaran ketentuan perdagangan, produk tidak sesuai..."
                            class="w-full text-xs border-stone-300 rounded-xl focus:ring-primary focus:border-primary shadow-sm"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-stone-100">
                        <button type="button" @click="modalOpen = false" class="min-h-[38px] px-4 py-2 border border-stone-300 rounded-xl text-xs font-bold text-stone-600 hover:bg-stone-50 transition">
                            Batal
                        </button>
                        <button type="submit" :disabled="suspending" class="min-h-[38px] px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold shadow-md transition flex items-center gap-1.5 disabled:opacity-50">
                            <template x-if="suspending">
                                <span class="flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 animate-spin text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path></svg>
                                    <span>Memproses...</span>
                                </span>
                            </template>
                            <template x-if="!suspending">
                                <span>Tangguhkan Toko</span>
                            </template>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-admin-layout>
