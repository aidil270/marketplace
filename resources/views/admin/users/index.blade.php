<x-admin-layout title="Kelola Pengguna">
    <x-slot name="header">
        Kelola Pengguna Sistem
    </x-slot>

    <div class="space-y-6">
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
                    <h3 class="font-bold text-gray-900 text-sm">Daftar Pengguna ({{ $users->total() }})</h3>
                    <p class="text-xs text-gray-400">Kelola status aktif/nonaktif dan detail pengguna marketplace</p>
                </div>
            </div>

            <form method="GET" action="{{ route('admin.users.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3 pt-3 border-t border-gray-100">
                <!-- Search Input -->
                <div class="sm:col-span-2 relative">
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama, email, atau telepon..."
                        class="w-full text-xs border-gray-300 rounded-xl pl-9 focus:ring-primary focus:border-primary shadow-sm" />
                    <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>

                <!-- Role Filter -->
                <div>
                    <select name="role" onchange="this.form.submit()" class="w-full text-xs border-gray-300 rounded-xl focus:ring-primary focus:border-primary shadow-sm">
                        <option value="">Semua Role</option>
                        @foreach ($roles as $r)
                            <option value="{{ $r->slug }}" {{ request('role') === $r->slug ? 'selected' : '' }}>
                                {{ $r->name }}
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
                    @if (request()->hasAny(['q', 'role', 'status']))
                        <a href="{{ route('admin.users.index') }}" class="p-2 text-gray-400 hover:text-gray-700 text-xs font-semibold">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Users Table -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            @if ($users->isEmpty())
                <div class="p-12 text-center text-gray-400">
                    <h4 class="text-sm font-bold text-gray-700">Tidak ada pengguna ditemukan</h4>
                    <p class="text-xs text-gray-500 mt-1">Coba sesuaikan kata kunci atau filter pencarian Anda.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-gray-50 text-gray-500 uppercase text-[10px] tracking-wider border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-3.5">Pengguna</th>
                                <th class="px-6 py-3.5">Role</th>
                                <th class="px-6 py-3.5">Toko (Seller)</th>
                                <th class="px-6 py-3.5 text-center">Pesanan</th>
                                <th class="px-6 py-3.5">Terdaftar</th>
                                <th class="px-6 py-3.5 text-center">Status</th>
                                <th class="px-6 py-3.5 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($users as $u)
                                <tr class="hover:bg-gray-50/70 transition">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-700 font-bold flex items-center justify-center shrink-0">
                                                {{ strtoupper(substr($u->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <span class="font-bold text-gray-900 block">{{ $u->name }}</span>
                                                <span class="text-[11px] text-gray-400">{{ $u->email }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @switch($u->role?->slug)
                                            @case('admin')
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-purple-50 text-purple-700">Admin</span>
                                                @break
                                            @case('seller')
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-secondary">Seller</span>
                                                @break
                                            @default
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-gray-100 text-gray-700">Pembeli</span>
                                        @endswitch
                                    </td>
                                    <td class="px-6 py-4 text-gray-600">
                                        {{ $u->store ? $u->store->name : '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-center font-bold text-gray-700">
                                        {{ $u->orders_count }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-500 text-[11px]">
                                        {{ $u->created_at->translatedFormat('d M Y') }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $u->is_active ? 'bg-emerald-50 text-primary' : 'bg-red-50 text-red-600' }}">
                                            {{ $u->is_active ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('admin.users.show', $u->id) }}" class="min-h-[38px] min-w-[38px] p-2 text-stone-500 hover:text-secondary hover:bg-teal-50 rounded-xl transition flex items-center justify-center border border-stone-200" title="Lihat Detail" aria-label="Lihat Detail">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            </a>

                                            @if ($u->id !== auth()->id())
                                                <form method="POST" action="{{ route('admin.users.toggle-status', $u->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin mengubah status akun pengguna ini?');">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="min-h-[38px] px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center justify-center border {{ $u->is_active ? 'bg-rose-50 text-rose-600 hover:bg-rose-100 border-rose-200' : 'bg-teal-50 text-secondary hover:bg-teal-100 border-teal-200' }}">
                                                        {{ $u->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
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
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>
