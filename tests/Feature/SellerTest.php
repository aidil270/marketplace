<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Role;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SellerTest extends TestCase
{
    use RefreshDatabase;

    protected User $buyer;
    protected User $seller1;
    protected User $seller2;
    protected Store $store1;
    protected Store $store2;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $buyerRole = Role::firstOrCreate(['slug' => 'buyer'], ['name' => 'Pembeli', 'slug' => 'buyer']);
        $sellerRole = Role::firstOrCreate(['slug' => 'seller'], ['name' => 'Penjual', 'slug' => 'seller']);

        $this->buyer = User::factory()->create([
            'role_id' => $buyerRole->id,
            'name' => 'Pembeli Biasa',
            'email' => 'pembeli@tokoku.com',
        ]);

        $this->seller1 = User::factory()->create([
            'role_id' => $sellerRole->id,
            'name' => 'Penjual Satu',
            'email' => 'penjual1@tokoku.com',
        ]);

        $this->store1 = Store::create([
            'user_id' => $this->seller1->id,
            'name' => 'Toko Elektronik Satu',
            'slug' => 'toko-elektronik-satu',
            'city' => 'Jakarta Selatan',
            'phone' => '081234567891',
            'is_active' => true,
        ]);

        $this->seller2 = User::factory()->create([
            'role_id' => $sellerRole->id,
            'name' => 'Penjual Dua',
            'email' => 'penjual2@tokoku.com',
        ]);

        $this->store2 = Store::create([
            'user_id' => $this->seller2->id,
            'name' => 'Toko Fashion Dua',
            'slug' => 'toko-fashion-dua',
            'city' => 'Bandung',
            'phone' => '081234567892',
            'is_active' => true,
        ]);

        $this->category = Category::create([
            'name' => 'Gadget & Komputer',
            'slug' => 'gadget-komputer',
            'is_active' => true,
        ]);
    }

    /**
     * 1. Pendaftaran penjual: user biasa bisa mendaftar toko dan role berubah jadi seller.
     */
    public function test_buyer_can_register_store_and_role_changes_to_seller(): void
    {
        $response = $this->actingAs($this->buyer)->get(route('seller.register'));
        $response->assertStatus(200);
        $response->assertSee('Mulai Langkah Sukses Bisnismu');

        $storeData = [
            'name' => 'Toko Baru Makmur',
            'city' => 'Surabaya',
            'phone' => '089876543210',
            'description' => 'Toko peralatan rumah tangga berkualitas tinggi.',
            'agree' => '1',
        ];

        $postResponse = $this->actingAs($this->buyer)->post(route('seller.register.store'), $storeData);
        $postResponse->assertRedirect(route('seller.dashboard'));

        // Pastikan record toko dibuat di DB
        $this->assertDatabaseHas('stores', [
            'name' => 'Toko Baru Makmur',
            'slug' => 'toko-baru-makmur',
            'city' => 'Surabaya',
            'user_id' => $this->buyer->id,
        ]);

        // Pastikan role user berubah menjadi seller
        $this->buyer->refresh();
        $this->assertTrue($this->buyer->isSeller());
        $this->assertEquals('seller', $this->buyer->role->slug);
    }

    /**
     * 2. Akses middleware: pembeli biasa ditolak (redirect) saat mengakses /penjual.
     */
    public function test_buyer_cannot_access_seller_dashboard(): void
    {
        $response = $this->actingAs($this->buyer)->get(route('seller.dashboard'));
        $response->assertRedirect(route('seller.register'));
        $response->assertSessionHas('info');

        // Guest juga harus diarahkan ke login
        auth()->logout();
        $guestResponse = $this->get(route('seller.dashboard'));
        $guestResponse->assertRedirect(route('login'));
    }

    /**
     * 3. Dashboard penjual: seller bisa melihat dashboard dengan data tokonya.
     */
    public function test_seller_can_view_dashboard_with_store_metrics(): void
    {
        $product = Product::create([
            'store_id' => $this->store1->id,
            'category_id' => $this->category->id,
            'name' => 'Mouse Gaming RGB',
            'slug' => 'mouse-gaming-rgb',
            'description' => 'Mouse gaming presisi tinggi.',
            'price' => 250000,
            'stock' => 15,
            'weight' => 200,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->seller1)->get(route('seller.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Dashboard Penjual');
        $response->assertSee('Toko Elektronik Satu');
        $response->assertSee('Perlu Diproses');
        $response->assertSee('Total Pendapatan');
        $response->assertSee('Tren Penjualan 7 Hari Terakhir');
    }

    /**
     * 4. CRUD produk: seller bisa menambah, mengedit, dan menghapus produk miliknya.
     */
    public function test_seller_can_create_edit_and_delete_own_product(): void
    {
        // 4a. Tambah Produk Baru
        $createPage = $this->actingAs($this->seller1)->get(route('seller.products.create'));
        $createPage->assertStatus(200);

        $fakeImage = UploadedFile::fake()->image('keyboard.jpg');

        $storeData = [
            'name' => 'Mechanical Keyboard TKL',
            'category_id' => $this->category->id,
            'description' => 'Keyboard mekanikal switch biru.',
            'price' => 500000,
            'stock' => 20,
            'weight' => 800,
            'is_active' => '1',
            'images' => [$fakeImage],
            'variants' => [
                ['name' => 'Switch', 'value' => 'Blue Switch', 'additional_price' => 0, 'stock' => 10],
                ['name' => 'Switch', 'value' => 'Red Switch', 'additional_price' => 25000, 'stock' => 10],
            ],
        ];

        $postResponse = $this->actingAs($this->seller1)->post(route('seller.products.store'), $storeData);
        $postResponse->assertRedirect(route('seller.products.index'));

        $this->assertDatabaseHas('products', [
            'store_id' => $this->store1->id,
            'name' => 'Mechanical Keyboard TKL',
            'slug' => 'mechanical-keyboard-tkl',
            'price' => 500000,
        ]);

        $createdProduct = Product::where('slug', 'mechanical-keyboard-tkl')->first();
        $this->assertNotNull($createdProduct);
        $this->assertCount(1, $createdProduct->images);
        $this->assertCount(2, $createdProduct->variants);

        // 4b. Edit Produk
        $editPage = $this->actingAs($this->seller1)->get(route('seller.products.edit', $createdProduct->id));
        $editPage->assertStatus(200);
        $editPage->assertSee('Mechanical Keyboard TKL');

        $updateData = [
            'name' => 'Mechanical Keyboard TKL V2',
            'category_id' => $this->category->id,
            'description' => 'Keyboard mekanikal switch biru versi pembaruan.',
            'price' => 550000,
            'stock' => 25,
            'weight' => 850,
            'is_active' => '1',
        ];

        $putResponse = $this->actingAs($this->seller1)->put(route('seller.products.update', $createdProduct->id), $updateData);
        $putResponse->assertRedirect(route('seller.products.edit', $createdProduct->id));

        $this->assertDatabaseHas('products', [
            'id' => $createdProduct->id,
            'name' => 'Mechanical Keyboard TKL V2',
            'price' => 550000,
            'stock' => 25,
        ]);

        // 4c. Hapus Produk (Belum ada order -> Hard Delete)
        $deleteResponse = $this->actingAs($this->seller1)->delete(route('seller.products.destroy', $createdProduct->id));
        $deleteResponse->assertRedirect(route('seller.products.index'));

        $this->assertDatabaseMissing('products', [
            'id' => $createdProduct->id,
        ]);
    }

    /**
     * 5. Otorisasi produk: seller TIDAK BISA mengedit/menghapus produk milik toko lain (harus 403).
     */
    public function test_seller_cannot_edit_or_delete_other_store_product(): void
    {
        $otherProduct = Product::create([
            'store_id' => $this->store2->id,
            'category_id' => $this->category->id,
            'name' => 'Kemeja Katun Pria',
            'slug' => 'kemeja-katun-pria',
            'description' => 'Kemeja katun bahan adem.',
            'price' => 150000,
            'stock' => 30,
            'is_active' => true,
        ]);

        // Seller 1 mencoba membuka halaman edit produk toko 2
        $editResponse = $this->actingAs($this->seller1)->get(route('seller.products.edit', $otherProduct->id));
        $editResponse->assertStatus(403);

        // Seller 1 mencoba update produk toko 2
        $updateResponse = $this->actingAs($this->seller1)->put(route('seller.products.update', $otherProduct->id), [
            'name' => 'Hacked Product Name',
            'category_id' => $this->category->id,
            'description' => 'Deskripsi bajakan',
            'price' => 10000,
            'stock' => 1,
        ]);
        $updateResponse->assertStatus(403);

        // Seller 1 mencoba hapus produk toko 2
        $deleteResponse = $this->actingAs($this->seller1)->delete(route('seller.products.destroy', $otherProduct->id));
        $deleteResponse->assertStatus(403);

        // Pastikan data produk toko 2 tidak berubah
        $this->assertDatabaseHas('products', [
            'id' => $otherProduct->id,
            'name' => 'Kemeja Katun Pria',
        ]);
    }

    /**
     * 6. Hapus produk yang sudah dipesan: pastikan produk tidak terhapus permanen (hanya nonaktif / soft delete).
     */
    public function test_product_with_order_history_is_soft_deleted_not_permanently_deleted(): void
    {
        $product = Product::create([
            'store_id' => $this->store1->id,
            'category_id' => $this->category->id,
            'name' => 'Headset Bluetooth Bass',
            'slug' => 'headset-bluetooth-bass',
            'description' => 'Headset nirkabel suara jernih.',
            'price' => 300000,
            'stock' => 10,
            'is_active' => true,
        ]);

        $order = Order::create([
            'invoice_number' => 'INV-TEST-SOFTDELETE',
            'user_id' => $this->buyer->id,
            'store_id' => $this->store1->id,
            'total_amount' => 300000,
            'shipping_cost' => 15000,
            'discount_amount' => 0,
            'final_amount' => 315000,
            'status' => 'completed',
            'shipping_address' => 'Jl. Pengujian No. 123',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'product_price' => 300000,
            'quantity' => 1,
            'subtotal' => 300000,
        ]);

        // Coba hapus produk
        $deleteResponse = $this->actingAs($this->seller1)->delete(route('seller.products.destroy', $product->id));
        $deleteResponse->assertRedirect(route('seller.products.index'));

        // Cek bahwa record masih ada di database tapi memiliki deleted_at (Soft Deleted)
        $this->assertSoftDeleted('products', [
            'id' => $product->id,
        ]);

        // Status produk juga harus nonaktif
        $productWithTrashed = Product::withTrashed()->find($product->id);
        $this->assertNotNull($productWithTrashed);
        $this->assertFalse($productWithTrashed->is_active);
    }

    /**
     * 7. Kelola pesanan: seller hanya melihat pesanan tokonya sendiri.
     */
    public function test_seller_only_sees_orders_belonging_to_their_store(): void
    {
        // Pesanan untuk Store 1
        $order1 = Order::create([
            'invoice_number' => 'INV-SELLER1-001',
            'user_id' => $this->buyer->id,
            'store_id' => $this->store1->id,
            'total_amount' => 200000,
            'shipping_cost' => 10000,
            'discount_amount' => 0,
            'final_amount' => 210000,
            'status' => 'processing',
            'shipping_address' => 'Alamat Pembeli 1',
        ]);

        // Pesanan untuk Store 2
        $order2 = Order::create([
            'invoice_number' => 'INV-SELLER2-002',
            'user_id' => $this->buyer->id,
            'store_id' => $this->store2->id,
            'total_amount' => 100000,
            'shipping_cost' => 10000,
            'discount_amount' => 0,
            'final_amount' => 110000,
            'status' => 'processing',
            'shipping_address' => 'Alamat Pembeli 2',
        ]);

        $response = $this->actingAs($this->seller1)->get(route('seller.orders.index'));
        $response->assertStatus(200);
        $response->assertSee('INV-SELLER1-001');
        $response->assertDontSee('INV-SELLER2-002');
    }

    /**
     * 8. Input resi: seller bisa memasukkan resi dan status pesanan berubah jadi "shipped".
     */
    public function test_seller_can_input_tracking_number_and_status_changes_to_shipped(): void
    {
        $order = Order::create([
            'invoice_number' => 'INV-SHIP-TEST',
            'user_id' => $this->buyer->id,
            'store_id' => $this->store1->id,
            'total_amount' => 450000,
            'shipping_cost' => 15000,
            'discount_amount' => 0,
            'final_amount' => 465000,
            'status' => 'processing',
            'shipping_address' => 'Jl. Merdeka No. 45, Jakarta',
        ]);

        // Buka halaman detail pesanan
        $showResponse = $this->actingAs($this->seller1)->get(route('seller.orders.show', $order->invoice_number));
        $showResponse->assertStatus(200);
        $showResponse->assertSee('Input Resi Pengiriman');

        // Submit nomor resi
        $shipResponse = $this->actingAs($this->seller1)->post(route('seller.orders.ship', $order->invoice_number), [
            'courier_name' => 'JNE',
            'tracking_number' => 'JNE987654321ID',
        ]);
        $shipResponse->assertRedirect(route('seller.orders.show', $order->invoice_number));

        // Verifikasi database order status menjadi shipped
        $order->refresh();
        $this->assertEquals('shipped', $order->status);

        // Verifikasi tabel shipments
        $this->assertDatabaseHas('shipments', [
            'order_id' => $order->id,
            'courier_name' => 'JNE',
            'tracking_number' => 'JNE987654321ID',
            'shipment_status' => 'shipped',
        ]);
    }

    /**
     * 9. Otorisasi pesanan: seller TIDAK BISA mengakses detail pesanan milik toko lain (harus 404).
     */
    public function test_seller_cannot_view_or_ship_order_from_other_store(): void
    {
        $otherOrder = Order::create([
            'invoice_number' => 'INV-OTHER-STORE',
            'user_id' => $this->buyer->id,
            'store_id' => $this->store2->id,
            'total_amount' => 150000,
            'shipping_cost' => 10000,
            'discount_amount' => 0,
            'final_amount' => 160000,
            'status' => 'processing',
            'shipping_address' => 'Jl. Bandung Indah No. 10',
        ]);

        // Seller 1 mencoba melihat detail pesanan milik Toko 2
        $viewResponse = $this->actingAs($this->seller1)->get(route('seller.orders.show', $otherOrder->invoice_number));
        $viewResponse->assertStatus(404);

        // Seller 1 mencoba mengirim resi untuk pesanan milik Toko 2
        $shipResponse = $this->actingAs($this->seller1)->post(route('seller.orders.ship', $otherOrder->invoice_number), [
            'courier_name' => 'JNE',
            'tracking_number' => 'HACKED123',
        ]);
        $shipResponse->assertStatus(404);

        // Pastikan pesanan toko 2 tetap processing
        $otherOrder->refresh();
        $this->assertEquals('processing', $otherOrder->status);
    }

    /**
     * 10. Pengaturan profil toko: seller bisa mengubah data toko miliknya.
     */
    public function test_seller_can_update_store_profile(): void
    {
        $editResponse = $this->actingAs($this->seller1)->get(route('seller.store.edit'));
        $editResponse->assertStatus(200);
        $editResponse->assertSee('Toko Elektronik Satu');

        $updateResponse = $this->actingAs($this->seller1)->put(route('seller.store.update'), [
            'name' => 'Toko Elektronik Satu Official',
            'city' => 'Jakarta Barat',
            'phone' => '081299998888',
            'description' => 'Deskripsi baru toko resmi.',
        ]);

        $updateResponse->assertRedirect(route('seller.store.edit'));

        $this->assertDatabaseHas('stores', [
            'id' => $this->store1->id,
            'name' => 'Toko Elektronik Satu Official',
            'city' => 'Jakarta Barat',
            'phone' => '081299998888',
        ]);
    }

    /**
     * 11. Halaman toko publik: dapat diakses siapa saja dan menampilkan katalog toko.
     */
    public function test_public_can_view_store_page_and_products(): void
    {
        Product::create([
            'store_id' => $this->store1->id,
            'category_id' => $this->category->id,
            'name' => 'Produk Publik Unggulan',
            'slug' => 'produk-publik-unggulan',
            'description' => 'Produk untuk pengujian halaman publik.',
            'price' => 75000,
            'stock' => 12,
            'is_active' => true,
        ]);

        $response = $this->get(route('stores.show', $this->store1->slug));
        $response->assertStatus(200);
        $response->assertSee('Toko Elektronik Satu');
        $response->assertSee('Produk Publik Unggulan');
    }

    /**
     * 12. Produk nonaktif, soft-deleted, atau stok nol tidak muncul di katalog/homepage/pencarian dan tidak bisa masuk keranjang.
     */
    public function test_inactive_softdeleted_or_zero_stock_product_not_in_catalog_and_cannot_add_to_cart(): void
    {
        // a. Produk Nonaktif
        $inactiveProduct = Product::create([
            'store_id' => $this->store1->id,
            'category_id' => $this->category->id,
            'name' => 'Produk Khusus Nonaktif',
            'slug' => 'produk-khusus-nonaktif',
            'description' => 'Tidak boleh tampil.',
            'price' => 50000,
            'stock' => 10,
            'is_active' => false,
        ]);

        // b. Produk Stok Nol
        $outOfStockProduct = Product::create([
            'store_id' => $this->store1->id,
            'category_id' => $this->category->id,
            'name' => 'Produk Khusus Habis Stok',
            'slug' => 'produk-khusus-habis-stok',
            'description' => 'Stok 0.',
            'price' => 60000,
            'stock' => 0,
            'is_active' => true,
        ]);

        // c. Produk Soft-Deleted
        $softDeletedProduct = Product::create([
            'store_id' => $this->store1->id,
            'category_id' => $this->category->id,
            'name' => 'Produk Khusus Terhapus',
            'slug' => 'produk-khusus-terhapus',
            'description' => 'Sudah dihapus.',
            'price' => 70000,
            'stock' => 10,
            'is_active' => true,
        ]);
        $softDeletedProduct->delete();

        // 1. Cek Homepage
        $homeRes = $this->get(route('home'));
        $homeRes->assertDontSee('Produk Khusus Nonaktif');
        $homeRes->assertDontSee('Produk Khusus Habis Stok');
        $homeRes->assertDontSee('Produk Khusus Terhapus');

        // 2. Cek Katalog & Pencarian
        $catalogRes = $this->get(route('products.index', ['q' => 'Produk Khusus']));
        $catalogRes->assertDontSee('Produk Khusus Nonaktif');
        $catalogRes->assertDontSee('Produk Khusus Habis Stok');
        $catalogRes->assertDontSee('Produk Khusus Terhapus');

        // 3. Coba Tambah ke Keranjang
        // Inactive product
        $cart1 = $this->actingAs($this->buyer)->post(route('cart.store'), [
            'product_id' => $inactiveProduct->id,
            'quantity' => 1,
        ]);
        $cart1->assertSessionHas('error');

        // Out of stock product
        $cart2 = $this->actingAs($this->buyer)->post(route('cart.store'), [
            'product_id' => $outOfStockProduct->id,
            'quantity' => 1,
        ]);
        $cart2->assertSessionHas('error');

        // Soft deleted product (exists check should fail with 302 back with validation error)
        $cart3 = $this->actingAs($this->buyer)->post(route('cart.store'), [
            'product_id' => $softDeletedProduct->id,
            'quantity' => 1,
        ]);
        $cart3->assertSessionHasErrors('product_id');
    }

    /**
     * 13. Kirim pesanan dari status selain processing ditolak, dan nomor resi wajib.
     */
    public function test_shipping_order_from_non_processing_status_is_rejected_and_tracking_number_is_required(): void
    {
        // Pesanan masih pending (belum dibayar)
        $pendingOrder = Order::create([
            'invoice_number' => 'INV-PENDING-SHIP',
            'user_id' => $this->buyer->id,
            'store_id' => $this->store1->id,
            'total_amount' => 100000,
            'shipping_cost' => 10000,
            'discount_amount' => 0,
            'final_amount' => 110000,
            'status' => 'pending',
            'shipping_address' => 'Alamat Pembeli',
        ]);

        // Coba kirim pesanan pending
        $resPending = $this->actingAs($this->seller1)->post(route('seller.orders.ship', $pendingOrder->invoice_number), [
            'courier_name' => 'JNE',
            'tracking_number' => 'JNE123',
        ]);
        $resPending->assertRedirect();
        $resPending->assertSessionHas('error');
        $pendingOrder->refresh();
        $this->assertEquals('pending', $pendingOrder->status);

        // Pesanan processing tapi tanpa resi
        $processingOrder = Order::create([
            'invoice_number' => 'INV-PROC-SHIP',
            'user_id' => $this->buyer->id,
            'store_id' => $this->store1->id,
            'total_amount' => 100000,
            'shipping_cost' => 10000,
            'discount_amount' => 0,
            'final_amount' => 110000,
            'status' => 'processing',
            'shipping_address' => 'Alamat Pembeli',
        ]);

        $resNoResi = $this->actingAs($this->seller1)->post(route('seller.orders.ship', $processingOrder->invoice_number), [
            'courier_name' => 'JNE',
            'tracking_number' => '',
        ]);
        $resNoResi->assertSessionHasErrors('tracking_number');
        $processingOrder->refresh();
        $this->assertEquals('processing', $processingOrder->status);
    }

    /**
     * 14. Upload foto tidak valid (tipe, ukuran, jumlah lebih dari 8) ditolak.
     */
    public function test_invalid_photo_upload_is_rejected_by_validation(): void
    {
        // a. Tipe tidak valid (pdf)
        $invalidFile = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');
        $resType = $this->actingAs($this->seller1)->post(route('seller.products.store'), [
            'name' => 'Produk Invalid Tipe',
            'category_id' => $this->category->id,
            'description' => 'Deskripsi',
            'price' => 10000,
            'stock' => 5,
            'images' => [$invalidFile],
        ]);
        $resType->assertSessionHasErrors('images.0');

        // b. Ukuran melebihi 2MB (3000KB)
        $largeFile = UploadedFile::fake()->create('large.jpg', 3000, 'image/jpeg');
        $resSize = $this->actingAs($this->seller1)->post(route('seller.products.store'), [
            'name' => 'Produk Invalid Ukuran',
            'category_id' => $this->category->id,
            'description' => 'Deskripsi',
            'price' => 10000,
            'stock' => 5,
            'images' => [$largeFile],
        ]);
        $resSize->assertSessionHasErrors('images.0');

        // c. Jumlah lebih dari 8 foto
        $manyFiles = [];
        for ($i = 0; $i < 9; $i++) {
            $manyFiles[] = UploadedFile::fake()->image("img_{$i}.jpg");
        }
        $resCount = $this->actingAs($this->seller1)->post(route('seller.products.store'), [
            'name' => 'Produk Terlalu Banyak Foto',
            'category_id' => $this->category->id,
            'description' => 'Deskripsi',
            'price' => 10000,
            'stock' => 5,
            'images' => $manyFiles,
        ]);
        $resCount->assertSessionHasErrors('images');
    }

    /**
     * 15. Variasi produk: tambah, ubah, hapus.
     */
    public function test_product_variants_can_be_added_updated_and_removed(): void
    {
        $product = Product::create([
            'store_id' => $this->store1->id,
            'category_id' => $this->category->id,
            'name' => 'Kaos Polos Premium',
            'slug' => 'kaos-polos-premium',
            'description' => 'Kaos katun.',
            'price' => 80000,
            'stock' => 30,
            'is_active' => true,
        ]);

        $v1 = $product->variants()->create([
            'name' => 'Ukuran',
            'value' => 'M',
            'additional_price' => 0,
            'stock' => 15,
        ]);

        $v2 = $product->variants()->create([
            'name' => 'Ukuran',
            'value' => 'L',
            'additional_price' => 5000,
            'stock' => 15,
        ]);

        // Seller mengubah v1, menghapus v2, dan menambah v3 (XL)
        $updateData = [
            'name' => 'Kaos Polos Premium Edited',
            'category_id' => $this->category->id,
            'description' => 'Kaos katun super.',
            'price' => 85000,
            'stock' => 40,
            'variants' => [
                // update v1
                ['id' => $v1->id, 'name' => 'Ukuran', 'value' => 'M Slim', 'additional_price' => 0, 'stock' => 20],
                // add v3 (tanpa id)
                ['id' => null, 'name' => 'Ukuran', 'value' => 'XL', 'additional_price' => 10000, 'stock' => 20],
                // v2 sengaja tidak disertakan agar terhapus
            ],
        ];

        $res = $this->actingAs($this->seller1)->put(route('seller.products.update', $product->id), $updateData);
        $res->assertRedirect(route('seller.products.edit', $product->id));

        $product->refresh();
        $this->assertEquals(2, $product->variants()->count());

        // Pastikan v1 terupdate
        $this->assertDatabaseHas('product_variants', [
            'id' => $v1->id,
            'value' => 'M Slim',
            'stock' => 20,
        ]);

        // Pastikan v2 terhapus
        $this->assertDatabaseMissing('product_variants', [
            'id' => $v2->id,
        ]);

        // Pastikan v3 terbuat
        $this->assertDatabaseHas('product_variants', [
            'product_id' => $product->id,
            'value' => 'XL',
            'additional_price' => 10000,
        ]);
    }

    /**
     * 16. Dashboard menampilkan angka yang benar untuk toko sendiri saja.
     */
    public function test_seller_dashboard_calculates_correct_metrics_for_own_store_only(): void
    {
        // Buat pesanan untuk Store 1
        Order::create([
            'invoice_number' => 'INV-METRIC-S1',
            'user_id' => $this->buyer->id,
            'store_id' => $this->store1->id,
            'total_amount' => 500000,
            'shipping_cost' => 20000,
            'discount_amount' => 0,
            'final_amount' => 520000,
            'status' => 'processing',
            'shipping_address' => 'Alamat S1',
        ]);

        // Buat pesanan untuk Store 2
        Order::create([
            'invoice_number' => 'INV-METRIC-S2',
            'user_id' => $this->buyer->id,
            'store_id' => $this->store2->id,
            'total_amount' => 900000,
            'shipping_cost' => 20000,
            'discount_amount' => 0,
            'final_amount' => 920000,
            'status' => 'processing',
            'shipping_address' => 'Alamat S2',
        ]);

        $res = $this->actingAs($this->seller1)->get(route('seller.dashboard'));
        $res->assertStatus(200);

        // Hanya melihat total pendapatan toko 1 (Rp 520.000), bukan toko 2 (Rp 920.000)
        $res->assertSee('520.000');
        $res->assertDontSee('920.000');
    }

    /**
     * 17. Pembeli melihat kurir dan nomor resi di detail pesanannya setelah penjual mengirim.
     */
    public function test_buyer_can_view_courier_and_tracking_number_after_order_is_shipped(): void
    {
        $order = Order::create([
            'invoice_number' => 'INV-RESI-BUYER-VIEW',
            'user_id' => $this->buyer->id,
            'store_id' => $this->store1->id,
            'total_amount' => 300000,
            'shipping_cost' => 15000,
            'discount_amount' => 0,
            'final_amount' => 315000,
            'status' => 'processing',
            'shipping_address' => 'Jl. Kenanga No. 8, Jakarta',
        ]);

        // Penjual mengirimkan pesanan
        $this->actingAs($this->seller1)->post(route('seller.orders.ship', $order->invoice_number), [
            'courier_name' => 'SiCepat',
            'tracking_number' => 'SICEPAT998877ID',
        ]);

        // Pembeli membuka halaman detail pesanan
        $buyerRes = $this->actingAs($this->buyer)->get(route('orders.show', $order->id));
        $buyerRes->assertStatus(200);
        $buyerRes->assertSee('SiCepat');
        $buyerRes->assertSee('SICEPAT998877ID');
        $buyerRes->assertSee('Pesanan Sedang Dikirim');
    }
}
