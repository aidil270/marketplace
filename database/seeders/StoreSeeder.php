<?php

namespace Database\Seeders;

use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Seeder;

class StoreSeeder extends Seeder
{
    public function run(): void
    {
        $seller1 = User::where('email', 'seller1@tokoku.com')->first();
        $seller2 = User::where('email', 'seller2@tokoku.com')->first();

        if ($seller1) {
            Store::firstOrCreate(
                ['slug' => 'techofficial-store'],
                [
                    'user_id' => $seller1->id,
                    'name' => 'TechOfficial Store',
                    'description' => 'Toko resmi produk elektronik, audio, gadget, dan aksesoris komputer terlengkap bergaransi resmi.',
                    'city' => 'Jakarta Pusat',
                    'address' => 'Mall Mangga Dua Lt. 3 No. 45, Jakarta Pusat',
                    'is_active' => true,
                ]
            );
        }

        if ($seller2) {
            Store::firstOrCreate(
                ['slug' => 'fashionboutique-id'],
                [
                    'user_id' => $seller2->id,
                    'name' => 'FashionBoutique ID',
                    'description' => 'Distributor busana pria dan wanita trendi masa kini dengan bahan premium berkualitas tinggi.',
                    'city' => 'Bandung',
                    'address' => 'Jl. Riau No. 88, Citarum, Bandung',
                    'is_active' => true,
                ]
            );
        }
    }
}
