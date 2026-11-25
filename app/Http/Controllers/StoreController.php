<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Review;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StoreController extends Controller
{
    /**
     * Tampilkan halaman publik profil toko beserta katalog produknya.
     */
    public function show(Request $request, string $slug): View
    {
        $store = Store::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        // Rating rata-rata & ulasan
        $productIds = $store->products()->pluck('id');
        $reviewsCount = Review::whereIn('product_id', $productIds)->count();
        $averageRating = $reviewsCount > 0 
            ? round((float) Review::whereIn('product_id', $productIds)->avg('rating'), 1) 
            : 0.0;

        $totalProductsCount = $store->products()->where('is_active', true)->count();

        // Kategori produk yang dimiliki toko ini
        $categories = Category::whereHas('products', function ($query) use ($store) {
            $query->where('store_id', $store->id)->where('is_active', true);
        })->get();

        // Query produk toko
        $productsQuery = $store->products()
            ->where('is_active', true)
            ->where('stock', '>', 0)
            ->with(['primaryImage', 'category']);

        // Filter kata kunci
        if ($request->filled('q')) {
            $productsQuery->where('name', 'like', '%'.$request->q.'%');
        }

        // Filter kategori
        if ($request->filled('category')) {
            $productsQuery->whereHas('category', function ($query) use ($request) {
                $query->where('slug', $request->category);
            });
        }

        // Pengurutan
        match ($request->query('sort', 'newest')) {
            'price_asc' => $productsQuery->orderBy('price', 'asc'),
            'price_desc' => $productsQuery->orderBy('price', 'desc'),
            'popular' => $productsQuery->withCount('orderItems')->orderBy('order_items_count', 'desc'),
            default => $productsQuery->latest(),
        };

        $products = $productsQuery->paginate(12)->withQueryString();

        return view('stores.show', compact('store', 'products', 'categories', 'reviewsCount', 'averageRating', 'totalProductsCount'));
    }
}
