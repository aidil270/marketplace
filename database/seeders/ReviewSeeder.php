<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        $buyers = User::whereHas('role', fn ($q) => $q->where('slug', 'buyer'))->get();
        $products = Product::take(10)->get();

        $comments = [
            'Produk sangat bagus, pengiriman sangat cepat dan packing rapi dengan bubble wrap!',
            'Barang original 100%, kualitas memuaskan, sudah dicoba dan berfungsi sempurna.',
            'Mantap betul belanja di TokoKu, penjual ramah dan responsif. Rekomended seller!',
            'Sesuai deskripsi, bahan bagus dan warnanya elegan. Pasti langganan lagi.',
            'Harga terjangkau tapi kualitas bintang lima! Pengiriman cuma butuh 1 hari sampai.',
        ];

        foreach ($products as $index => $product) {
            $buyer = $buyers[$index % $buyers->count()] ?? $buyers->first();
            if ($buyer) {
                Review::firstOrCreate(
                    [
                        'user_id' => $buyer->id,
                        'product_id' => $product->id,
                    ],
                    [
                        'rating' => 5,
                        'comment' => $comments[$index % count($comments)],
                    ]
                );
            }
        }
    }
}
