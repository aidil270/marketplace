<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    /**
     * Tampilkan halaman utama Dashboard Admin dengan statistik, grafik, dan daftar terbaru.
     */
    public function index(): View
    {
        // 1. Kartu Metrik Agregat
        $totalUsers = User::count();
        $totalStores = Store::count();
        $totalActiveProducts = Product::where('is_active', true)->count();
        $totalOrders = Order::count();
        $pendingOrders = Order::where('status', 'pending')->count();
        $processingOrders = Order::where('status', 'processing')->count();

        // Total nilai transaksi dari pembayaran yang paid
        // Definisi: Jumlah nominal transaksi yang tercatat sukses (status = paid) di tabel payments
        $totalPaidRevenue = (float) Payment::where('status', 'paid')->sum('amount');

        // 2. Data Tren Transaksi 7 Hari Terakhir (HTML/Tailwind Bar Chart Murni)
        $salesChart = [];
        $maxDaily = 1;
        for ($i = 6; $i >= 0; $i--) {
            $dayCarbon = now()->subDays($i);
            $date = $dayCarbon->format('Y-m-d');
            $label = $dayCarbon->translatedFormat('D, d M');

            $revenue = (float) Payment::where('status', 'paid')
                ->whereDate('paid_at', $date)
                ->sum('amount');

            $count = Payment::where('status', 'paid')
                ->whereDate('paid_at', $date)
                ->count();

            if ($revenue > $maxDaily) {
                $maxDaily = $revenue;
            }

            $salesChart[] = [
                'date' => $date,
                'label' => $label,
                'revenue' => $revenue,
                'count' => $count,
            ];
        }

        foreach ($salesChart as &$point) {
            $point['percentage'] = $maxDaily > 0 ? max(8, round(($point['revenue'] / $maxDaily) * 100)) : 8;
        }
        unset($point);

        // 3. 5 Pesanan Terbaru
        $recentOrders = Order::with(['user', 'store'])
            ->latest()
            ->take(5)
            ->get();

        // 4. 5 Toko Terbaru
        $recentStores = Store::with('user')
            ->withCount('products')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalStores',
            'totalActiveProducts',
            'totalOrders',
            'pendingOrders',
            'processingOrders',
            'totalPaidRevenue',
            'salesChart',
            'recentOrders',
            'recentStores'
        ));
    }
}
