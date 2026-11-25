<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Category;
use App\Models\Product;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        // 1. Banner slider aktif
        $banners = Banner::where('is_active', true)
            ->where('position', 'home_slider')
            ->orderBy('sort_order')
            ->get();

        // 2. Kategori utama dengan jumlah produk
        $categories = Category::where('is_active', true)
            ->whereNull('parent_id')
            ->withCount('products')
            ->orderBy('name')
            ->take(8)
            ->get();

        // 3. Flash sale (produk unggulan dengan stok terbatas)
        $flashSales = Product::available()
            ->where('is_featured', true)
            ->with(['store', 'primaryImage'])
            ->take(6)
            ->get();

        // 4. Produk terlaris (diurutkan berdasarkan total order_items)
        $topProducts = Product::available()
            ->with(['store', 'category', 'primaryImage'])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->withCount('orderItems')
            ->orderByDesc('order_items_count')
            ->take(6)
            ->get();

        // 5. Rekomendasi produk (paginated)
        $recommendedProducts = Product::available()
            ->with(['store', 'category', 'primaryImage'])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->latest()
            ->paginate(18);

        // Ambil ID semua produk di wishlist pengguna dalam 1 query untuk menghindari N+1
        $wishlistProductIds = auth()->check()
            ? auth()->user()->wishlists()->pluck('product_id')->toArray()
            : [];

        return view('welcome', compact(
            'banners',
            'categories',
            'flashSales',
            'topProducts',
            'recommendedProducts',
            'wishlistProductIds'
        ));
    }
}
