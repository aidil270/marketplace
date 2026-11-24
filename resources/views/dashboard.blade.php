<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
        <div class="bg-gradient-to-r from-secondary/10 to-primary/10 border border-emerald-200 rounded-2xl p-6 flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex items-center space-x-4">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-secondary to-primary text-white flex items-center justify-center text-2xl font-black shadow-md">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <div>
                    <h1 class="text-xl font-black text-gray-900">Halo, {{ Auth::user()->name }}!</h1>
                    <p class="text-xs text-gray-500 mt-0.5">{{ Auth::user()->email }} &bull; Member TokoKu</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ url('/') }}" class="px-5 py-2.5 bg-primary hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-sm transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    Mulai Belanja
                </a>
                <a href="{{ route('orders.index') }}" class="px-5 py-2.5 bg-white border border-secondary text-secondary hover:bg-blue-50 font-bold text-xs rounded-xl shadow-sm transition flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    Pesanan Saya
                </a>
                <a href="{{ route('profile.edit') }}" class="px-5 py-2.5 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 font-bold text-xs rounded-xl shadow-sm transition">
                    Pengaturan Profil
                </a>
            </div>
        </div>

        <!-- Quick Status Cards Linked to Orders -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <a href="{{ route('orders.index', ['status' => 'pending']) }}" class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center space-x-3 hover:border-amber-300 transition">
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-secondary flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <div class="text-xs text-gray-500">Menunggu Bayar</div>
                    <div class="text-lg font-black text-gray-900">
                        {{ Auth::user()->orders()->where('status', 'pending')->count() }}
                    </div>
                </div>
            </a>

            <a href="{{ route('orders.index', ['status' => 'processing']) }}" class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center space-x-3 hover:border-blue-300 transition">
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                </div>
                <div>
                    <div class="text-xs text-gray-500">Sedang Diproses</div>
                    <div class="text-lg font-black text-gray-900">
                        {{ Auth::user()->orders()->where('status', 'processing')->count() }}
                    </div>
                </div>
            </a>

            <a href="{{ route('orders.index', ['status' => 'shipped']) }}" class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center space-x-3 hover:border-purple-300 transition">
                <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
                </div>
                <div>
                    <div class="text-xs text-gray-500">Dalam Pengiriman</div>
                    <div class="text-lg font-black text-gray-900">
                        {{ Auth::user()->orders()->where('status', 'shipped')->count() }}
                    </div>
                </div>
            </a>

            <a href="{{ route('orders.index', ['status' => 'completed']) }}" class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center space-x-3 hover:border-emerald-300 transition">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-primary flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <div class="text-xs text-gray-500">Pesanan Selesai</div>
                    <div class="text-lg font-black text-gray-900">
                        {{ Auth::user()->orders()->where('status', 'completed')->count() }}
                    </div>
                </div>
            </a>
        </div>
    </div>
</x-app-layout>
