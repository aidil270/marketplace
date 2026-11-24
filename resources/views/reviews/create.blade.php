<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight flex items-center gap-2">
            <a href="{{ route('orders.show', $order->id) }}" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            Tulis Ulasan Produk
        </h2>
    </x-slot>

    <div class="py-8 bg-[#F8FAFC] min-h-screen">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-sm border border-gray-100">
                <!-- Info Produk yang Diulas -->
                <div class="flex items-center gap-4 pb-6 mb-6 border-b border-gray-100">
                    @php
                        $image = $orderItem->product?->primaryImage?->image_path 
                            ?? 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=500&auto=format&fit=crop&q=60';
                    @endphp
                    <img src="{{ $image }}" alt="{{ $orderItem->product_name }}" class="w-16 h-16 rounded-xl object-cover bg-gray-50 border border-gray-100 flex-shrink-0">
                    <div>
                        <h3 class="font-bold text-gray-900 text-sm leading-snug line-clamp-2">
                            {{ $orderItem->product_name }}
                        </h3>
                        <p class="text-xs text-gray-500 mt-1">
                            Pesanan: <span class="font-semibold text-gray-700">{{ $order->invoice_number }}</span>
                        </p>
                    </div>
                </div>

                <form action="{{ route('reviews.store', ['order' => $order->id, 'orderItem' => $orderItem->id]) }}" 
                      method="POST" 
                      enctype="multipart/form-data" 
                      class="space-y-6"
                      x-data="{
                          rating: {{ old('rating', 5) }},
                          hoverRating: 0,
                          comment: '{{ old('comment', '') }}',
                          ratingLabels: {
                              1: 'Sangat Buruk',
                              2: 'Kurang Memuaskan',
                              3: 'Cukup',
                              4: 'Bagus & Memuaskan',
                              5: 'Sangat Bagus & Puas!'
                          }
                      }">
                    @csrf

                    <!-- Pilihan Rating Bintang Interaktif -->
                    <div class="text-center sm:text-left">
                        <label class="block text-xs font-bold text-gray-700 mb-2 uppercase tracking-wider">
                            Kualitas Produk <span class="text-red-500">*</span>
                        </label>
                        <input type="hidden" name="rating" :value="rating">

                        <div class="flex items-center gap-2">
                            <template x-for="star in 5" :key="star">
                                <button type="button" 
                                    @click="rating = star"
                                    @mouseenter="hoverRating = star"
                                    @mouseleave="hoverRating = 0"
                                    class="p-1 focus:outline-none transition transform hover:scale-110">
                                    <svg class="w-8 h-8 transition-colors duration-150" 
                                        :class="(hoverRating || rating) >= star ? 'text-yellow-400 fill-current' : 'text-gray-200 fill-current'" 
                                        viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                </button>
                            </template>
                            <span class="ml-2 text-sm font-bold text-gray-700" x-text="ratingLabels[hoverRating || rating]"></span>
                        </div>
                        @error('rating') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Komentar Ulasan -->
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Ulasan Anda (Opsional)
                            </label>
                            <span class="text-xs text-gray-400" x-text="comment.length + '/1000'"></span>
                        </div>
                        <textarea name="comment" rows="4" maxlength="1000" x-model="comment"
                            class="w-full text-sm rounded-xl border-gray-300 focus:border-primary focus:ring-primary shadow-sm"
                            placeholder="Ceritakan kepuasan Anda mengenai kualitas barang, kecepatan pengiriman, atau pelayanan penjual..."></textarea>
                        @error('comment') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Upload Foto Ulasan -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1 uppercase tracking-wider">
                            Foto Produk (Opsional, Maks. 3 Foto)
                        </label>
                        <p class="text-xs text-gray-500 mb-3">Format JPG, PNG, atau WEBP (Maksimal 2 MB per file).</p>
                        <input type="file" name="photos[]" multiple accept="image/jpeg,image/png,image/webp"
                            class="block w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-primary hover:file:bg-emerald-100 transition cursor-pointer">
                        @error('photos') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        @error('photos.*') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                        <a href="{{ route('orders.show', $order->id) }}" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-sm rounded-xl transition">
                            Batal
                        </a>
                        <button type="submit" class="px-6 py-2.5 bg-primary hover:bg-emerald-700 text-white font-bold text-sm rounded-xl transition shadow">
                            Kirim Ulasan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
