<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight flex items-center gap-2">
            <a href="{{ route('orders.show', $review->order_id) }}" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            Ubah Ulasan Produk
        </h2>
    </x-slot>

    <div class="py-8 bg-surface min-h-screen">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-sm border border-stone-200">
                <!-- Info Produk -->
                <div class="flex items-center gap-4 pb-6 mb-6 border-b border-stone-100">
                    @php
                        $image = $review->product?->primaryImage?->image_path 
                            ?? 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=500&auto=format&fit=crop&q=60';
                    @endphp
                    <img src="{{ $image }}" alt="{{ $review->product?->name }}" class="w-16 h-16 rounded-xl object-cover bg-stone-50 border border-stone-200 flex-shrink-0">
                    <div>
                        <h3 class="font-bold text-stone-900 text-sm leading-snug line-clamp-2">
                            {{ $review->product?->name }}
                        </h3>
                        <p class="text-xs text-secondary font-semibold mt-1">
                            Anda dapat mengubah ulasan dalam masa 7 hari sejak ulasan pertama dibuat.
                        </p>
                    </div>
                </div>

                <form action="{{ route('reviews.update', $review->id) }}" 
                      method="POST" 
                      enctype="multipart/form-data" 
                      class="space-y-6"
                      @submit="submitting = true"
                      x-data="{
                          submitting: false,
                          rating: {{ old('rating', $review->rating) }},
                          hoverRating: 0,
                          comment: '{{ old('comment', addslashes($review->comment ?? '')) }}',
                          ratingLabels: {
                              1: 'Sangat Buruk',
                              2: 'Kurang Memuaskan',
                              3: 'Cukup',
                              4: 'Bagus & Memuaskan',
                              5: 'Sangat Bagus & Puas!'
                          }
                      }">
                    @csrf
                    @method('PUT')

                    <!-- Rating Bintang Interaktif -->
                    <div>
                        <label class="block text-xs font-bold text-stone-700 mb-2 uppercase tracking-wider">
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
                                        :class="(hoverRating || rating) >= star ? 'text-amber-400 fill-current' : 'text-stone-200 fill-current'" 
                                        viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                </button>
                            </template>
                            <span class="ml-2 text-xs font-bold text-stone-700" x-text="ratingLabels[hoverRating || rating]"></span>
                        </div>
                        @error('rating') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Komentar Ulasan -->
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider">
                                Ulasan Anda
                            </label>
                            <span class="text-xs text-stone-400" x-text="comment.length + '/1000'"></span>
                        </div>
                        <textarea name="comment" rows="4" maxlength="1000" x-model="comment"
                            class="w-full text-xs rounded-xl border-stone-300 focus:border-primary focus:ring-primary shadow-sm">{{ old('comment', $review->comment) }}</textarea>
                        @error('comment') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Foto yang Sudah Diunggah -->
                    @if ($review->images->isNotEmpty())
                        <div>
                            <label class="block text-xs font-bold text-stone-700 mb-2 uppercase tracking-wider">
                                Foto Ulasan Terunggah
                            </label>
                            <div class="flex items-center gap-3">
                                @foreach ($review->images as $img)
                                    <img src="{{ $img->image_path }}" class="w-16 h-16 rounded-xl object-cover border border-stone-200">
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Tambah Foto Baru -->
                    @if ($review->images->count() < 3)
                        <div>
                            <label class="block text-xs font-bold text-stone-700 mb-1 uppercase tracking-wider">
                                Tambah Foto (Maksimal sisa {{ 3 - $review->images->count() }} foto)
                            </label>
                            <input type="file" name="photos[]" multiple accept="image/jpeg,image/png,image/webp"
                                class="block w-full text-xs text-stone-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-orange-50 file:text-primary hover:file:bg-orange-100 transition cursor-pointer">
                            @error('photos.*') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                    @endif

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-stone-100">
                        <a href="{{ route('orders.show', $review->order_id) }}" class="min-h-[44px] px-5 py-2.5 bg-stone-100 hover:bg-stone-200 text-stone-700 font-bold text-xs rounded-xl transition flex items-center">
                            Batal
                        </a>
                        <button type="submit" :disabled="submitting" class="min-h-[44px] px-6 py-2.5 bg-primary hover:bg-primary-hover text-white font-bold text-xs rounded-xl transition shadow flex items-center justify-center gap-2 disabled:opacity-50">
                            <template x-if="submitting">
                                <span class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4 animate-spin text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path></svg>
                                    <span>Memperbarui...</span>
                                </span>
                            </template>
                            <template x-if="!submitting">
                                <span>Perbarui Ulasan</span>
                            </template>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
