<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\Store;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $techStore = Store::where('slug', 'techofficial-store')->first();
        $fashionStore = Store::where('slug', 'fashionboutique-id')->first();

        $catElektronik = Category::where('slug', 'elektronik')->first();
        $catFashionPria = Category::where('slug', 'fashion-pria')->first();
        $catFashionWanita = Category::where('slug', 'fashion-wanita')->first();
        $catHandphone = Category::where('slug', 'handphone-aksesoris')->first();
        $catKecantikan = Category::where('slug', 'kecantikan-perawatan')->first();
        $catMakanan = Category::where('slug', 'makanan-minuman')->first();
        $catOlahraga = Category::where('slug', 'olahraga-outdoor')->first();
        $catOtomotif = Category::where('slug', 'otomotif')->first();

        $productsData = [
            // Toko Elektronik (15 produk)
            [
                'store_id' => $techStore->id,
                'category_id' => $catElektronik->id,
                'name' => 'TWS Wireless Bluetooth Earphone 5.3 Deep Bass',
                'price' => 89000,
                'stock' => 120,
                'weight' => 200,
                'is_featured' => true,
                'variants' => [
                    ['name' => 'Warna', 'value' => 'Hitam', 'sku' => 'TWS-BLK', 'additional_price' => 0, 'stock' => 70],
                    ['name' => 'Warna', 'value' => 'Putih', 'sku' => 'TWS-WHT', 'additional_price' => 0, 'stock' => 50],
                ],
            ],
            [
                'store_id' => $techStore->id,
                'category_id' => $catElektronik->id,
                'name' => 'Mechanical Keyboard RGB 87 Keys Blue Switch Gaming',
                'price' => 245000,
                'stock' => 45,
                'weight' => 850,
                'is_featured' => true,
                'variants' => [
                    ['name' => 'Switch', 'value' => 'Blue Switch', 'sku' => 'KB-BLU', 'additional_price' => 0, 'stock' => 25],
                    ['name' => 'Switch', 'value' => 'Red Switch', 'sku' => 'KB-RED', 'additional_price' => 15000, 'stock' => 20],
                ],
            ],
            [
                'store_id' => $techStore->id,
                'category_id' => $catElektronik->id,
                'name' => 'Mouse Wireless Ergonomic Silent Click 2.4GHz USB',
                'price' => 75000,
                'stock' => 80,
                'weight' => 150,
                'is_featured' => false,
                'variants' => [],
            ],
            [
                'store_id' => $techStore->id,
                'category_id' => $catHandphone->id,
                'name' => 'Smartwatch Full Touch Screen IP68 Waterproof Heart Rate',
                'price' => 149000,
                'stock' => 60,
                'weight' => 180,
                'is_featured' => true,
                'variants' => [
                    ['name' => 'Warna Strap', 'value' => 'Midnight Black', 'sku' => 'SW-BLK', 'additional_price' => 0, 'stock' => 35],
                    ['name' => 'Warna Strap', 'value' => 'Rose Pink', 'sku' => 'SW-PNK', 'additional_price' => 0, 'stock' => 25],
                ],
            ],
            [
                'store_id' => $techStore->id,
                'category_id' => $catHandphone->id,
                'name' => 'Powerbank 20000mAh Fast Charging 22.5W Dual USB Type-C',
                'price' => 135000,
                'stock' => 90,
                'weight' => 420,
                'is_featured' => true,
                'variants' => [],
            ],
            [
                'store_id' => $techStore->id,
                'category_id' => $catHandphone->id,
                'name' => 'Kabel Data Fast Charging Type-C to Type-C 65W Braided',
                'price' => 32000,
                'stock' => 200,
                'weight' => 80,
                'is_featured' => false,
                'variants' => [
                    ['name' => 'Panjang', 'value' => '1 Meter', 'sku' => 'KBL-1M', 'additional_price' => 0, 'stock' => 120],
                    ['name' => 'Panjang', 'value' => '2 Meter', 'sku' => 'KBL-2M', 'additional_price' => 10000, 'stock' => 80],
                ],
            ],
            [
                'store_id' => $techStore->id,
                'category_id' => $catHandphone->id,
                'name' => 'Tripod Handphone & Kamera Universal 1.3 Meter + Remote Bluetooth',
                'price' => 42000,
                'stock' => 110,
                'weight' => 350,
                'is_featured' => false,
                'variants' => [],
            ],
            [
                'store_id' => $techStore->id,
                'category_id' => $catElektronik->id,
                'name' => 'Lampu Meja Belajar LED Dimmable Touch Sensor Fleksibel',
                'price' => 58000,
                'stock' => 75,
                'weight' => 300,
                'is_featured' => false,
                'variants' => [],
            ],
            [
                'store_id' => $techStore->id,
                'category_id' => $catElektronik->id,
                'name' => 'Speaker Bluetooth Portable Waterproof Bass Boost',
                'price' => 120000,
                'stock' => 50,
                'weight' => 500,
                'is_featured' => true,
                'variants' => [],
            ],
            [
                'store_id' => $techStore->id,
                'category_id' => $catHandphone->id,
                'name' => 'Holder HP Mobil Dashboard Universal Otomatis Jepit',
                'price' => 29000,
                'stock' => 140,
                'weight' => 160,
                'is_featured' => false,
                'variants' => [],
            ],
            [
                'store_id' => $techStore->id,
                'category_id' => $catElektronik->id,
                'name' => 'USB Hub 4 Port USB 3.0 High Speed Transfer',
                'price' => 48000,
                'stock' => 85,
                'weight' => 100,
                'is_featured' => false,
                'variants' => [],
            ],
            [
                'store_id' => $techStore->id,
                'category_id' => $catHandphone->id,
                'name' => 'Ring Light LED 26cm + Stand Tripod Selfie Video Call',
                'price' => 68000,
                'stock' => 65,
                'weight' => 700,
                'is_featured' => false,
                'variants' => [],
            ],
            [
                'store_id' => $techStore->id,
                'category_id' => $catElektronik->id,
                'name' => 'Microphone Clip On Wireless Lavalier Type-C / Lightning',
                'price' => 95000,
                'stock' => 40,
                'weight' => 120,
                'is_featured' => false,
                'variants' => [],
            ],
            [
                'store_id' => $techStore->id,
                'category_id' => $catOtomotif->id,
                'name' => 'Pompa Ban Elektrik Portable Wireless Digital Display',
                'price' => 195000,
                'stock' => 30,
                'weight' => 600,
                'is_featured' => true,
                'variants' => [],
            ],
            [
                'store_id' => $techStore->id,
                'category_id' => $catOtomotif->id,
                'name' => 'Charger Mobil Fast Charging Quick Charge 3.0 Dual USB',
                'price' => 38000,
                'stock' => 95,
                'weight' => 80,
                'is_featured' => false,
                'variants' => [],
            ],

            // Toko Fashion & Lifestyle (15 produk)
            [
                'store_id' => $fashionStore->id,
                'category_id' => $catFashionPria->id,
                'name' => 'Jaket Hoodie Polos Premium Unisex Fleece Tebal Hangat',
                'price' => 115000,
                'stock' => 100,
                'weight' => 600,
                'is_featured' => true,
                'variants' => [
                    ['name' => 'Ukuran', 'value' => 'M', 'sku' => 'HD-M', 'additional_price' => 0, 'stock' => 30],
                    ['name' => 'Ukuran', 'value' => 'L', 'sku' => 'HD-L', 'additional_price' => 0, 'stock' => 40],
                    ['name' => 'Ukuran', 'value' => 'XL', 'sku' => 'HD-XL', 'additional_price' => 10000, 'stock' => 30],
                ],
            ],
            [
                'store_id' => $fashionStore->id,
                'category_id' => $catFashionPria->id,
                'name' => 'Kemeja Pria Polos Lengan Panjang Formal & Casual Slim Fit',
                'price' => 65000,
                'stock' => 150,
                'weight' => 300,
                'is_featured' => true,
                'variants' => [
                    ['name' => 'Warna', 'value' => 'Putih', 'sku' => 'KMJ-WHT', 'additional_price' => 0, 'stock' => 75],
                    ['name' => 'Warna', 'value' => 'Hitam', 'sku' => 'KMJ-BLK', 'additional_price' => 0, 'stock' => 75],
                ],
            ],
            [
                'store_id' => $fashionStore->id,
                'category_id' => $catFashionPria->id,
                'name' => 'Celana Chino Slim Fit Pria Panjang Bahan Katun Stretch',
                'price' => 95000,
                'stock' => 80,
                'weight' => 450,
                'is_featured' => true,
                'variants' => [
                    ['name' => 'Ukuran', 'value' => 'Size 30', 'sku' => 'CH-30', 'additional_price' => 0, 'stock' => 40],
                    ['name' => 'Ukuran', 'value' => 'Size 32', 'sku' => 'CH-32', 'additional_price' => 0, 'stock' => 40],
                ],
            ],
            [
                'store_id' => $fashionStore->id,
                'category_id' => $catFashionPria->id,
                'name' => 'Kaos Polos Cotton Combed 30s Reaktif Lembut Adem',
                'price' => 35000,
                'stock' => 300,
                'weight' => 200,
                'is_featured' => false,
                'variants' => [
                    ['name' => 'Warna', 'value' => 'Navy', 'sku' => 'TS-NVY', 'additional_price' => 0, 'stock' => 150],
                    ['name' => 'Warna', 'value' => 'Hitam', 'sku' => 'TS-BLK', 'additional_price' => 0, 'stock' => 150],
                ],
            ],
            [
                'store_id' => $fashionStore->id,
                'category_id' => $catFashionWanita->id,
                'name' => 'Blouse Wanita Korean Style Lengan Balon Casual Elegan',
                'price' => 78000,
                'stock' => 90,
                'weight' => 250,
                'is_featured' => true,
                'variants' => [
                    ['name' => 'Warna', 'value' => 'Cream', 'sku' => 'BL-CRM', 'additional_price' => 0, 'stock' => 45],
                    ['name' => 'Warna', 'value' => 'Lilac', 'sku' => 'BL-LLC', 'additional_price' => 0, 'stock' => 45],
                ],
            ],
            [
                'store_id' => $fashionStore->id,
                'category_id' => $catFashionWanita->id,
                'name' => 'Celana Kulot Highwaist Wanita Linen Adem Nyaman Seharian',
                'price' => 82000,
                'stock' => 70,
                'weight' => 320,
                'is_featured' => false,
                'variants' => [],
            ],
            [
                'store_id' => $fashionStore->id,
                'category_id' => $catFashionWanita->id,
                'name' => 'Tas Selempang Wanita Sling Bag Mini Kulit Sintetis Import',
                'price' => 85000,
                'stock' => 85,
                'weight' => 350,
                'is_featured' => true,
                'variants' => [],
            ],
            [
                'store_id' => $fashionStore->id,
                'category_id' => $catFashionPria->id,
                'name' => 'Tas Ransel Laptop Waterproof USB Port Anti Maling',
                'price' => 99000,
                'stock' => 100,
                'weight' => 650,
                'is_featured' => true,
                'variants' => [],
            ],
            [
                'store_id' => $fashionStore->id,
                'category_id' => $catOlahraga->id,
                'name' => 'Sepatu Sneakers Pria Running Sport Ringan Breathable',
                'price' => 125000,
                'stock' => 60,
                'weight' => 750,
                'is_featured' => true,
                'variants' => [
                    ['name' => 'Ukuran', 'value' => '40', 'sku' => 'SP-40', 'additional_price' => 0, 'stock' => 20],
                    ['name' => 'Ukuran', 'value' => '41', 'sku' => 'SP-41', 'additional_price' => 0, 'stock' => 20],
                    ['name' => 'Ukuran', 'value' => '42', 'sku' => 'SP-42', 'additional_price' => 0, 'stock' => 20],
                ],
            ],
            [
                'store_id' => $fashionStore->id,
                'category_id' => $catOlahraga->id,
                'name' => 'Matras Yoga Anti Slip 6mm Premium Eco Friendly Foam',
                'price' => 72000,
                'stock' => 45,
                'weight' => 900,
                'is_featured' => false,
                'variants' => [],
            ],
            [
                'store_id' => $fashionStore->id,
                'category_id' => $catKecantikan->id,
                'name' => 'Serum Wajah Vitamin C Brightening & Glowing Skin 20ml',
                'price' => 62000,
                'stock' => 130,
                'weight' => 100,
                'is_featured' => true,
                'variants' => [],
            ],
            [
                'store_id' => $fashionStore->id,
                'category_id' => $catKecantikan->id,
                'name' => 'Sunscreen SPF 50 PA++++ Light Texture No Whitecast 50ml',
                'price' => 59000,
                'stock' => 110,
                'weight' => 120,
                'is_featured' => true,
                'variants' => [],
            ],
            [
                'store_id' => $fashionStore->id,
                'category_id' => $catMakanan->id,
                'name' => 'Kopi Arabika Gayo Aceh Single Origin Roast Beans 250gr',
                'price' => 75000,
                'stock' => 60,
                'weight' => 300,
                'is_featured' => false,
                'variants' => [
                    ['name' => 'Gilingan', 'value' => 'Biji Utuh', 'sku' => 'KP-WHL', 'additional_price' => 0, 'stock' => 30],
                    ['name' => 'Gilingan', 'value' => 'Bubuk Halus', 'sku' => 'KP-GRD', 'additional_price' => 0, 'stock' => 30],
                ],
            ],
            [
                'store_id' => $fashionStore->id,
                'category_id' => $catMakanan->id,
                'name' => 'Madu Murni Hutan Asli Alami Organik 500gr Bersertifikat Halal',
                'price' => 88000,
                'stock' => 55,
                'weight' => 650,
                'is_featured' => false,
                'variants' => [],
            ],
            [
                'store_id' => $fashionStore->id,
                'category_id' => $catFashionPria->id,
                'name' => 'Dompet Kulit Pria Lipat Tiga Slot Kartu Lengkap RFID Protection',
                'price' => 49000,
                'stock' => 100,
                'weight' => 150,
                'is_featured' => false,
                'variants' => [],
            ],
        ];

        foreach ($productsData as $data) {
            $variants = $data['variants'] ?? [];
            unset($data['variants']);

            $slug = Str::slug($data['name']);
            $product = Product::firstOrCreate(
                ['slug' => $slug],
                array_merge($data, [
                    'description' => "Deskripsi lengkap untuk produk {$data['name']}.\n\nFitur Utama:\n- Kualitas premium terjamin 100% original.\n- Garansi toko resmi TokoKu.\n- Pengiriman cepat dan aman dengan bubble wrap berlapis.",
                    'is_active' => true,
                ])
            );

            // Buat foto utama produk
            ProductImage::firstOrCreate(
                ['product_id' => $product->id, 'sort_order' => 0],
                [
                    'image_path' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=600&auto=format&fit=crop&q=80',
                    'is_primary' => true,
                ]
            );

            ProductImage::firstOrCreate(
                ['product_id' => $product->id, 'sort_order' => 1],
                [
                    'image_path' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=600&auto=format&fit=crop&q=80',
                    'is_primary' => false,
                ]
            );

            // Buat varian jika ada
            foreach ($variants as $v) {
                ProductVariant::firstOrCreate(
                    ['product_id' => $product->id, 'sku' => $v['sku']],
                    $v
                );
            }
        }
    }
}
