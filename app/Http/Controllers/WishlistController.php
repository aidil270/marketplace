<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WishlistController extends Controller
{
    /**
     * Tampilkan halaman daftar produk favorit/wishlist pembeli.
     */
    public function index(): View
    {
        $user = auth()->user();

        $wishlists = Wishlist::where('user_id', $user->id)
            ->with([
                'product' => function ($query) {
                    $query->with(['store', 'primaryImage'])
                        ->withAvg('reviews', 'rating')
                        ->withCount('reviews')
                        ->withCount('variants');
                },
            ])
            ->latest()
            ->paginate(12);

        return view('wishlist.index', compact('wishlists'));
    }

    /**
     * Toggle status favorit produk (tambah jika belum ada, hapus jika sudah ada).
     * Mendukung fetch/AJAX dan form submit biasa.
     */
    public function toggle(Request $request): JsonResponse|RedirectResponse
    {
        $request->validate([
            'product_id' => ['required', 'exists:products,id'],
        ]);

        $user = auth()->user();
        $productId = (int) $request->input('product_id');

        $product = Product::findOrFail($productId);

        $existing = Wishlist::where('user_id', $user->id)
            ->where('product_id', $productId)
            ->first();

        if ($existing) {
            $existing->delete();
            $inWishlist = false;
            $message = "Produk '{$product->name}' dihapus dari wishlist.";
        } else {
            Wishlist::create([
                'user_id' => $user->id,
                'product_id' => $productId,
            ]);
            $inWishlist = true;
            $message = "Produk '{$product->name}' berhasil ditambahkan ke wishlist!";
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'success',
                'in_wishlist' => $inWishlist,
                'message' => $message,
                'wishlist_count' => $user->wishlists()->count(),
            ]);
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Hapus item dari wishlist langsung berdasarkan ID produk atau ID wishlist.
     */
    public function destroy(Product $product): RedirectResponse
    {
        $user = auth()->user();

        Wishlist::where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->delete();

        return redirect()->back()->with('success', "Produk '{$product->name}' telah dihapus dari wishlist.");
    }
}
