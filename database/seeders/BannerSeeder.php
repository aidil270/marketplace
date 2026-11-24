<?php

namespace Database\Seeders;

use App\Models\Banner;
use Illuminate\Database\Seeder;

class BannerSeeder extends Seeder
{
    public function run(): void
    {
        $banners = [
            [
                'title' => 'Super Flash Sale TokoKu s/d 80%',
                'image_path' => 'https://images.unsplash.com/photo-1607082348824-0a96f2a4b9da?w=1200&auto=format&fit=crop&q=80',
                'url' => '/#katalog',
                'position' => 'home_slider',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'title' => 'Pesta Gajian Elektronik & Gadget Terlengkap',
                'image_path' => 'https://images.unsplash.com/photo-1550009158-9ebf69173e03?w=1200&auto=format&fit=crop&q=80',
                'url' => '/#katalog',
                'position' => 'home_slider',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'title' => 'Koleksi Brand Fashion Lokal Tren 2026',
                'image_path' => 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=1200&auto=format&fit=crop&q=80',
                'url' => '/#katalog',
                'position' => 'home_slider',
                'is_active' => true,
                'sort_order' => 3,
            ],
        ];

        foreach ($banners as $b) {
            Banner::firstOrCreate(['title' => $b['title']], $b);
        }
    }
}
