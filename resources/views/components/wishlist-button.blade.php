@props([
    'productId',
    'inWishlist' => false,
    'buttonClass' => 'w-8 h-8 rounded-full bg-white/90 hover:bg-white text-gray-400 hover:text-red-500 shadow-sm border border-gray-100 flex items-center justify-center transition',
    'svgClass' => 'w-4 h-4',
])

@php
    $isLoggedIn = auth()->check();
    $loginUrl = route('login');
@endphp

<div x-data="{
    inWishlist: {{ $inWishlist ? 'true' : 'false' }},
    loading: false,
    toggleWishlist() {
        if (!{{ $isLoggedIn ? 'true' : 'false' }}) {
            window.location.href = '{{ $loginUrl }}';
            return;
        }

        if (this.loading) return;
        this.loading = true;

        fetch('{{ route('wishlist.toggle') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                product_id: {{ $productId }}
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                this.inWishlist = data.in_wishlist;
            }
        })
        .catch(err => {
            console.error('Gagal memperbarui wishlist:', err);
        })
        .finally(() => {
            this.loading = false;
        });
    }
}" class="inline-block">
    <button 
        type="button"
        @click.stop.prevent="toggleWishlist()"
        :class="inWishlist ? 'text-red-500 bg-red-50 hover:bg-red-100 border-red-200' : 'text-gray-400 hover:text-red-500'"
        class="{{ $buttonClass }}"
        title="Simpan ke Wishlist">
        <svg 
            :class="inWishlist ? 'fill-current' : 'fill-none'" 
            class="{{ $svgClass }} transition-colors duration-200" 
            stroke="currentColor" 
            stroke-width="2" 
            viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
        </svg>
    </button>
</div>
