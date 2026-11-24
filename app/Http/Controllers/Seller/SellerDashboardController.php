<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\View\View;

class SellerDashboardController extends Controller
{
    /**
     * Tampilkan halaman utama Dashboard Penjual dengan metrik, grafik, dan peringatan stok.
     */
    public function index(): View
    {
        $store = auth()->user()->store;

        // 1. Metrik Ringkasan
        $processingOrdersCount = $store->orders()->where('status', 'processing')->count();
        $shippedOrdersCount = $store->orders()->where('status', 'shipped')->count();
        $totalRevenue = (float) $store->orders()
            ->whereIn('status', ['processing', 'shipped', 'completed'])
            ->sum('final_amount');
        $activeProductsCount = $store->products()->where('is_active', true)->count();

        // Rating rata-rata toko
        $productIds = $store->products()->pluck('id');
        $reviewsCount = Review::whereIn('product_id', $productIds)->count();
        $averageRating = $reviewsCount > 0 
            ? round((float) Review::whereIn('product_id', $productIds)->avg('rating'), 1) 
            : 0.0;

        // 2. 5 Pesanan Terbaru
        $recentOrders = $store->orders()
            ->with(['user', 'items.product'])
            ->latest()
            ->take(5)
            ->get();

        // 3. Peringatan Stok Menipis (stok <= 5)
        $lowStockProducts = $store->products()
            ->where('is_active', true)
            ->where('stock', '<=', 5)
            ->orderBy('stock', 'asc')
            ->take(8)
            ->get();

        // 4. Data Tren Penjualan 7 Hari Terakhir
        $salesChart = [];
        $maxDaily = 1;
        for ($i = 6; $i >= 0; $i--) {
            $dayCarbon = now()->subDays($i);
            $date = $dayCarbon->format('Y-m-d');
            $label = $dayCarbon->translatedFormat('D, d M');

            $revenue = (float) $store->orders()
                ->whereIn('status', ['processing', 'shipped', 'completed'])
                ->whereDate('created_at', $date)
                ->sum('final_amount');

            $count = $store->orders()
                ->whereIn('status', ['processing', 'shipped', 'completed'])
                ->whereDate('created_at', $date)
                ->count();

            if ($revenue > $maxDaily) {
                $maxDaily = $revenue;
            }

            $salesChart[] = [
                'date' => $date,
                'label' => $label,
                'revenue' => $revenue,
                'orders_count' => $count,
            ];
        }

        foreach ($salesChart as &$point) {
            $point['percentage'] = $maxDaily > 0 ? max(8, round(($point['revenue'] / $maxDaily) * 100)) : 8;
        }
        unset($point);

        return view('seller.dashboard', compact(
            'store',
            'processingOrdersCount',
            'shippedOrdersCount',
            'totalRevenue',
            'activeProductsCount',
            'reviewsCount',
            'averageRating',
            'recentOrders',
            'lowStockProducts',
            'salesChart'
        ));
    }
}
