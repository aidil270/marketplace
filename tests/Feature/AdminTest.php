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
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $seller;
    protected User $buyer;
    protected Store $store;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $adminRole = Role::firstOrCreate(['slug' => 'admin'], ['name' => 'Admin']);
        $sellerRole = Role::firstOrCreate(['slug' => 'seller'], ['name' => 'Seller']);
        $buyerRole = Role::firstOrCreate(['slug' => 'buyer'], ['name' => 'Buyer']);

        $this->admin = User::factory()->create([
            'role_id' => $adminRole->id,
            'name' => 'Super Administrator',
            'email' => 'admin@tokoku.com',
            'is_active' => true,
        ]);

        $this->seller = User::factory()->create([
            'role_id' => $sellerRole->id,
            'name' => 'Merchant Penjual',
            'email' => 'merchant@tokoku.com',
            'is_active' => true,
        ]);

        $this->store = Store::create([
            'user_id' => $this->seller->id,
            'name' => 'Toko Mitra Resmi',
            'slug' => 'toko-mitra-resmi',
            'city' => 'Jakarta Selatan',
            'phone' => '081234567890',
            'is_active' => true,
        ]);

        $this->buyer = User::factory()->create([
            'role_id' => $buyerRole->id,
            'name' => 'Pembeli Setia',
            'email' => 'pembeli@tokoku.com',
            'is_active' => true,
        ]);

        $this->category = Category::create([
            'name' => 'Elektronik Rumah',
            'slug' => 'elektronik-rumah',
            'is_active' => true,
        ]);
    }

    /**
     * 1. Akses admin: Guest diarahkan ke login, Pembeli & Penjual mendapat 403, Admin diterima.
     */
    public function test_admin_access_control(): void
    {
        // Guest
        $guestRes = $this->get(route('admin.dashboard'));
        $guestRes->assertRedirect(route('login'));

        // Pembeli
        $buyerRes = $this->actingAs($this->buyer)->get(route('admin.dashboard'));
        $buyerRes->assertStatus(403);

        // Penjual
        $sellerRes = $this->actingAs($this->seller)->get(route('admin.dashboard'));
        $sellerRes->assertStatus(403);

        // Admin
        $adminRes = $this->actingAs($this->admin)->get(route('admin.dashboard'));
        $adminRes->assertStatus(200);
        $adminRes->assertSee('Dashboard Administrator');
    }

    /**
     * 2. Dashboard admin menampilkan statistik dan data agregat.
     */
    public function test_admin_dashboard_metrics_display(): void
    {
        Product::create([
            'store_id' => $this->store->id,
            'category_id' => $this->category->id,
            'name' => 'Kipas Angin Meja',
            'slug' => 'kipas-angin-meja',
            'description' => 'Kipas angin.',
            'price' => 120000,
            'stock' => 15,
            'is_active' => true,
        ]);

        $res = $this->actingAs($this->admin)->get(route('admin.dashboard'));
        $res->assertStatus(200);
        $res->assertSee('Total Pengguna');
        $res->assertSee('Total Toko');
        $res->assertSee('Produk Aktif');
        $res->assertSee('Nilai Transaksi (Paid)');
        $res->assertSee('Toko Mitra Resmi');
    }

    /**
     * 3. Kelola Pengguna: Admin dapat melihat daftar, mencari, dan memfilter.
     */
    public function test_admin_can_view_and_filter_users(): void
    {
        $res = $this->actingAs($this->admin)->get(route('admin.users.index', ['q' => 'Merchant']));
        $res->assertStatus(200);
        $res->assertSee('Merchant Penjual');
        $res->assertDontSee('Pembeli Setia');
    }

    /**
     * 4. Kelola Pengguna: Deaktivasi memblokir login dan memutus sesi berjalan.
     */
    public function test_user_deactivation_blocks_login_and_active_session(): void
    {
        // Admin menonaktifkan pembeli
        $toggleRes = $this->actingAs($this->admin)->patch(route('admin.users.toggle-status', $this->buyer->id));
        $toggleRes->assertRedirect();
        $this->buyer->refresh();
        $this->assertFalse($this->buyer->is_active);

        // Pembeli mencoba login
        auth()->logout();
        $loginRes = $this->post(route('login'), [
            'email' => 'pembeli@tokoku.com',
            'password' => 'password',
        ]);
        $loginRes->assertSessionHasErrors('email');
        $this->assertGuest();

        // Uji sesi aktif diputus oleh middleware EnsureUserIsActive
        $sessionRes = $this->actingAs($this->buyer)->get(route('dashboard'));
        $sessionRes->assertRedirect(route('login'));
        $this->assertGuest();
    }

    /**
     * 5. Pengaman: Admin tidak bisa menonaktifkan dirinya sendiri atau admin terakhir.
     */
    public function test_admin_cannot_deactivate_self_or_last_active_admin(): void
    {
        // Admin menonaktifkan dirinya sendiri
        $selfRes = $this->actingAs($this->admin)->patch(route('admin.users.toggle-status', $this->admin->id));
        $selfRes->assertSessionHas('error');
        $this->admin->refresh();
        $this->assertTrue($this->admin->is_active);

        // Coba menonaktifkan admin satu-satunya yang aktif dari akun lain (jika ada)
        $secondAdminRole = Role::where('slug', 'admin')->first();
        $admin2 = User::factory()->create([
            'role_id' => $secondAdminRole->id,
            'is_active' => true,
        ]);

        // Nonaktifkan admin2 oleh admin1 -> Berhasil karena masih ada admin1
        $res1 = $this->actingAs($this->admin)->patch(route('admin.users.toggle-status', $admin2->id));
        $res1->assertSessionHas('success');
        $admin2->refresh();
        $this->assertFalse($admin2->is_active);

        // Sekarang admin1 adalah admin terakhir yang aktif, coba nonaktifkan admin1
        $res2 = $this->actingAs($this->admin)->patch(route('admin.users.toggle-status', $this->admin->id));
        $res2->assertSessionHas('error');
    }

    /**
     * 6. Kelola Toko: Penangguhan toko memblokir akses seller, menghapus produk dari publik, dan memblokir keranjang.
     */
    public function test_store_suspension_blocks_seller_and_hides_products(): void
    {
        $product = Product::create([
            'store_id' => $this->store->id,
            'category_id' => $this->category->id,
            'name' => 'Blender Serbaguna 3in1',
            'slug' => 'blender-serbaguna-3in1',
            'description' => 'Blender dapur.',
            'price' => 200000,
            'stock' => 10,
            'is_active' => true,
        ]);

        // Produk awalnya muncul di katalog
        $catRes1 = $this->get(route('products.index', ['q' => 'Blender']));
        $catRes1->assertSee('Blender Serbaguna 3in1');

        // Admin menangguhkan toko
        $suspendRes = $this->actingAs($this->admin)->patch(route('admin.stores.toggle-status', $this->store->id), [
            'suspension_reason' => 'Toko melanggar standar kualitas produk.',
        ]);
        $suspendRes->assertRedirect();
        $this->store->refresh();
        $this->assertFalse($this->store->is_active);
        $this->assertEquals('Toko melanggar standar kualitas produk.', $this->store->suspension_reason);

        // Penjual membuka area penjual -> Ditolak oleh EnsureUserIsSeller
        $sellerDashRes = $this->actingAs($this->seller)->get(route('seller.dashboard'));
        $sellerDashRes->assertRedirect(route('home'));
        $sellerDashRes->assertSessionHas('error');

        // Produk HILANG dari homepage & katalog
        $homeRes = $this->get(route('home'));
        $homeRes->assertDontSee('Blender Serbaguna 3in1');

        $catRes2 = $this->get(route('products.index', ['q' => 'Blender']));
        $catRes2->assertDontSee('Blender Serbaguna 3in1');

        // Halaman toko publik 404 (karena toko tidak aktif)
        $storePageRes = $this->get(route('stores.show', $this->store->slug));
        $storePageRes->assertStatus(404);

        // Coba tambahkan produk ke keranjang belanja -> Gagal
        $cartRes = $this->actingAs($this->buyer)->post(route('cart.store'), [
            'product_id' => $product->id,
            'quantity' => 1,
        ]);
        $cartRes->assertSessionHas('error');
    }

    /**
     * 7. Produk dengan stok induk 0 tetapi variasi memiliki stok > 0 tetap muncul di katalog dan bisa dibeli.
     */
    public function test_product_with_zero_parent_stock_but_available_variant_is_visible_and_purchasable(): void
    {
        $product = Product::create([
            'store_id' => $this->store->id,
            'category_id' => $this->category->id,
            'name' => 'Sepatu Lari Ultralight',
            'slug' => 'sepatu-lari-ultralight',
            'description' => 'Sepatu lari.',
            'price' => 350000,
            'stock' => 0, // Induk 0
            'is_active' => true,
        ]);

        $variant = $product->variants()->create([
            'name' => 'Ukuran',
            'value' => '42',
            'additional_price' => 0,
            'stock' => 8, // Variasi masih punya stok 8
        ]);

        // Produk HARUS muncul di katalog
        $catRes = $this->get(route('products.index', ['q' => 'Ultralight']));
        $catRes->assertStatus(200);
        $catRes->assertSee('Sepatu Lari Ultralight');

        // Produk bisa dimasukkan ke keranjang dengan variasi tersebut
        $cartRes = $this->actingAs($this->buyer)->post(route('cart.store'), [
            'product_id' => $product->id,
            'variant_id' => $variant->id,
            'quantity' => 2,
        ]);
        $cartRes->assertRedirect();
        $cartRes->assertSessionHas('success');
    }
}
