<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $categories = Category::where('is_active', true)
            ->whereNull('parent_id')
            ->withCount('products')
            ->get();

        $query = Product::available()
            ->with(['store', 'category', 'primaryImage'])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews');

        // 1. Pencarian Kata Kunci
        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('store', fn ($sq) => $sq->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('category', fn ($cq) => $cq->where('name', 'like', "%{$search}%"));
            });
        }

        // 2. Filter Kategori
        $selectedCategory = null;
        if ($catSlug = $request->input('category')) {
            $selectedCategory = Category::where('slug', $catSlug)->first();
            if ($selectedCategory) {
                $categoryIds = Category::where('parent_id', $selectedCategory->id)
                    ->pluck('id')
                    ->push($selectedCategory->id);
                $query->whereIn('category_id', $categoryIds);
            }
        }

        // 3. Filter Rentang Harga
        if ($minPrice = $request->input('min_price')) {
            $query->where('price', '>=', (float) $minPrice);
        }
        if ($maxPrice = $request->input('max_price')) {
            $query->where('price', '<=', (float) $maxPrice);
        }

        // 4. Filter Rating Minimum (1-5)
        if ($minRating = $request->input('min_rating')) {
            $query->whereHas('reviews', function ($rq) use ($minRating) {
                $rq->havingRaw('AVG(rating) >= ?', [(float) $minRating]);
            });
        }

        // 5. Urutkan (Sorting)
        $sort = $request->input('sort', 'latest');
        switch ($sort) {
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'top_selling':
                $query->withCount('orderItems')->orderByDesc('order_items_count');
                break;
            case 'rating':
                $query->orderByDesc('reviews_avg_rating');
                break;
            case 'latest':
            default:
                $query->latest();
                break;
        }

        $products = $query->paginate(16)->withQueryString();

        // Ambil wishlist product id dalam 1 query (mencegah N+1)
        $wishlistProductIds = auth()->check()
            ? auth()->user()->wishlists()->pluck('product_id')->toArray()
            : [];

        return view('products.index', compact('products', 'categories', 'selectedCategory', 'wishlistProductIds'));
    }

    public function category(string $slug): View
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        request()->merge(['category' => $category->slug]);

        return $this->index(request());
    }

    public function show(string $slug): View
    {
        $product = Product::available()
            ->where('slug', $slug)
            ->with([
                'store',
                'category',
                'images',
                'variants',
            ])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->firstOrFail();

        // Ulasan Produk dengan Pagination
        $reviews = $product->reviews()
            ->with(['user', 'images'])
            ->latest()
            ->paginate(5, ['*'], 'review_page');

        // Distribusi Bintang 1-5
        $ratingBreakdown = [
            5 => $product->reviews()->where('rating', 5)->count(),
            4 => $product->reviews()->where('rating', 4)->count(),
            3 => $product->reviews()->where('rating', 3)->count(),
            2 => $product->reviews()->where('rating', 2)->count(),
            1 => $product->reviews()->where('rating', 1)->count(),
        ];

        // Status Wishlist Pengguna
        $inWishlist = auth()->check()
            ? auth()->user()->wishlists()->where('product_id', $product->id)->exists()
            : false;

        // Produk Serupa dari kategori yang sama
        $relatedProducts = Product::available()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->with(['store', 'primaryImage'])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->take(6)
            ->get();

        return view('products.show', compact(
            'product',
            'relatedProducts',
            'reviews',
            'ratingBreakdown',
            'inWishlist'
        ));
    }
}
