<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Review;
use App\Models\ReviewImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReviewController extends Controller
{
    /**
     * Tampilkan form untuk menulis ulasan produk dari pesanan selesai.
     */
    public function create(Order $order, OrderItem $orderItem): View|RedirectResponse
    {
        $user = auth()->user();

        // 1. Otorisasi kepemilikan pesanan
        if ($order->user_id !== $user->id) {
            abort(403, 'Anda tidak memiliki akses ke pesanan ini.');
        }

        // 2. Hanya boleh mengulas jika status pesanan adalah 'completed'
        if ($order->status !== 'completed') {
            return redirect()->route('orders.show', $order->id)
                ->with('error', 'Ulasan hanya dapat diberikan setelah pesanan berstatus Selesai.');
        }

        // 3. Pastikan item benar-benar milik pesanan ini
        if ($orderItem->order_id !== $order->id) {
            abort(404, 'Item pesanan tidak ditemukan pada pesanan ini.');
        }

        // 4. Pastikan belum pernah diulas
        if ($orderItem->review()->exists()) {
            return redirect()->route('orders.show', $order->id)
                ->with('error', 'Item pesanan ini sudah pernah Anda beri ulasan.');
        }

        $orderItem->load(['product.primaryImage', 'variant']);

        return view('reviews.create', compact('order', 'orderItem'));
    }

    /**
     * Simpan ulasan baru ke database beserta foto ulasan (jika ada).
     */
    public function store(Request $request, Order $order, OrderItem $orderItem): RedirectResponse
    {
        $user = auth()->user();

        if ($order->user_id !== $user->id) {
            abort(403, 'Anda tidak memiliki akses ke pesanan ini.');
        }

        if ($order->status !== 'completed') {
            return redirect()->route('orders.show', $order->id)
                ->with('error', 'Ulasan hanya dapat diberikan setelah pesanan selesai.');
        }

        if ($orderItem->order_id !== $order->id) {
            abort(404, 'Item pesanan tidak sesuai.');
        }

        if ($orderItem->review()->exists()) {
            return redirect()->route('orders.show', $order->id)
                ->with('error', 'Item pesanan ini sudah pernah Anda beri ulasan.');
        }

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1000'],
            'photos' => ['nullable', 'array', 'max:3'],
            'photos.*' => ['file', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
        ], [
            'rating.required' => 'Rating bintang wajib dipilih (1-5).',
            'rating.min' => 'Rating bintang minimal 1.',
            'rating.max' => 'Rating bintang maksimal 5.',
            'comment.max' => 'Komentar ulasan maksimal 1000 karakter.',
            'photos.max' => 'Maksimal 3 foto ulasan yang dapat diunggah.',
            'photos.*.image' => 'File harus berupa gambar.',
            'photos.*.mimes' => 'Format foto harus berupa JPG, PNG, atau WEBP.',
            'photos.*.max' => 'Ukuran foto maksimal 2 MB per file.',
        ]);

        $review = Review::create([
            'user_id' => $user->id,
            'product_id' => $orderItem->product_id,
            'order_id' => $order->id,
            'order_item_id' => $orderItem->id,
            'rating' => (int) $validated['rating'],
            'comment' => $validated['comment'] ?? null,
        ]);

        // Upload foto opsional (maks 3 file)
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('reviews', 'public');
                ReviewImage::create([
                    'review_id' => $review->id,
                    'image_path' => '/storage/' . $path,
                ]);
            }
        }

        return redirect()->route('orders.show', $order->id)
            ->with('success', 'Terima kasih! Ulasan Anda berhasil dikirim.');
    }

    /**
     * Tampilkan form untuk mengedit ulasan dalam masa 7 hari.
     */
    public function edit(Review $review): View|RedirectResponse
    {
        $user = auth()->user();

        if ($review->user_id !== $user->id) {
            abort(403, 'Anda tidak berhak mengedit ulasan ini.');
        }

        // Cek masa 7 hari
        if ($review->created_at->diffInDays(now()) > 7) {
            return redirect()->route('orders.show', $review->order_id)
                ->with('error', 'Batas waktu pengubahan ulasan (7 hari) telah berakhir.');
        }

        $review->load(['product.primaryImage', 'images', 'orderItem']);

        return view('reviews.edit', compact('review'));
    }

    /**
     * Perbarui ulasan yang sudah ada.
     */
    public function update(Request $request, Review $review): RedirectResponse
    {
        $user = auth()->user();

        if ($review->user_id !== $user->id) {
            abort(403, 'Anda tidak berhak mengedit ulasan ini.');
        }

        if ($review->created_at->diffInDays(now()) > 7) {
            return redirect()->route('orders.show', $review->order_id)
                ->with('error', 'Batas waktu pengubahan ulasan (7 hari) telah berakhir.');
        }

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1000'],
            'photos' => ['nullable', 'array', 'max:3'],
            'photos.*' => ['file', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
        ]);

        $review->update([
            'rating' => (int) $validated['rating'],
            'comment' => $validated['comment'] ?? null,
        ]);

        // Upload foto baru jika ada
        if ($request->hasFile('photos')) {
            $existingCount = $review->images()->count();
            $availableSlots = max(0, 3 - $existingCount);

            foreach (array_slice($request->file('photos'), 0, $availableSlots) as $photo) {
                $path = $photo->store('reviews', 'public');
                ReviewImage::create([
                    'review_id' => $review->id,
                    'image_path' => '/storage/' . $path,
                ]);
            }
        }

        return redirect()->route('orders.show', $review->order_id)
            ->with('success', 'Ulasan Anda berhasil diperbarui.');
    }
}
