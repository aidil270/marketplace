<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">
            {{ __('Profil Akun Saya') }}
        </h2>
    </x-slot>

    <div class="py-8 bg-[#F8FAFC] min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <!-- Navigasi Profil & Alamat -->
            <div class="flex items-center gap-4 border-b border-gray-200 pb-2 text-sm font-semibold max-w-4xl">
                <a href="{{ route('profile.edit') }}" class="text-primary border-b-2 border-primary py-1">
                    Profil Akun
                </a>
                <a href="{{ route('addresses.index') }}" class="text-gray-500 hover:text-primary transition py-1">
                    Daftar Alamat Pengiriman
                </a>
                <a href="{{ route('orders.index') }}" class="text-gray-500 hover:text-primary transition py-1">
                    Pesanan Saya
                </a>
            </div>

            <div class="p-6 sm:p-8 bg-white border border-gray-100 shadow-sm rounded-2xl max-w-4xl">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-6 sm:p-8 bg-white border border-gray-100 shadow-sm rounded-2xl max-w-4xl">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-6 sm:p-8 bg-white border border-gray-100 shadow-sm rounded-2xl max-w-4xl">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
