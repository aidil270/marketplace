<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Role;
use App\Models\Store;
use App\Models\User;
use App\Models\Voucher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartAndCheckoutTest extends TestCase
{
    use RefreshDatabase;

    protected User $buyer;
    protected User $seller;
    protected Store $store;
    protected Product $product;
    protected ProductVariant $variant;

    protected function setUp(): void
    {
        parent::setUp();

        $buyerRole = Role::firstOrCreate(['slug' => 'buyer'], ['name' => 'Pembeli', 'slug' => 'buyer']);
        $sellerRole = Role::firstOrCreate(['slug' => 'seller'], ['name' => 'Penjual', 'slug' => 'seller']);

        $this->buyer = User::factory()->create([
            'role_id' => $buyerRole->id,
            'name' => 'Test Buyer',
            'email' => 'testbuyer@tokoku.com',
        ]);

        $this->seller = User::factory()->create([
            'role_id' => $sellerRole->id,
            'name' => 'Test Seller',
            'email' => 'testseller@tokoku.com',
        ]);

        $this->store = Store::create([
            'user_id' => $this->seller->id,
            'name' => 'Toko Uji Coba',
            'slug' => 'toko-uji-coba',
            'city' => 'Jakarta Pusat',
            'is_active' => true,
        ]);

        $category = Category::create([
            'name' => 'Elektronik Test',
            'slug' => 'elektronik-test',
            'is_active' => true,
        ]);

        $this->product = Product::create([
            'store_id' => $this->store->id,
            'category_id' => $category->id,
            'name' => 'Earphone TWS Test',
            'slug' => 'earphone-tws-test',
            'price' => 100000,
            'stock' => 10,
            'weight' => 200,
            'is_active' => true,
        ]);

        $this->variant = ProductVariant::create([
            'product_id' => $this->product->id,
            'name' => 'Warna',
            'value' => 'Hitam',
            'sku' => 'TEST-BLK',
            'additional_price' => 10000,
            'stock' => 5,
        ]);

        Address::create([
            'user_id' => $this->buyer->id,
            'recipient_name' => 'Test Buyer',
            'phone_number' => '081234567890',
            'full_address' => 'Jl. Merdeka No. 10',
            'province' => 'DKI Jakarta',
            'city' => 'Jakarta Pusat',
            'postal_code' => '10110',
            'is_primary' => true,
        ]);
    }

    public function test_guest_cannot_add_to_cart_and_is_redirected_to_login(): void
    {
        $response = $this->post(route('cart.store'), [
            'product_id' => $this->product->id,
            'quantity' => 1,
        ]);

        $response->assertRedirect(route('login'));
        $this->assertDatabaseCount('cart_items', 0);
    }

    public function test_buyer_can_add_product_to_cart_successfully(): void
    {
        $response = $this->actingAs($this->buyer)->post(route('cart.store'), [
            'product_id' => $this->product->id,
            'variant_id' => $this->variant->id,
            'quantity' => 2,
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('cart_items', [
            'product_id' => $this->product->id,
            'product_variant_id' => $this->variant->id,
            'quantity' => 2,
            'is_selected' => true,
        ]);
    }

    public function test_cannot_add_to_cart_exceeding_stock(): void
    {
        // Variant stock is 5
        $response = $this->actingAs($this->buyer)->post(route('cart.store'), [
            'product_id' => $this->product->id,
            'variant_id' => $this->variant->id,
            'quantity' => 10, // melebihi stok 5
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseCount('cart_items', 0);
    }

    public function test_buyer_can_update_quantity_and_remove_item_from_cart(): void
    {
        $this->actingAs($this->buyer)->post(route('cart.store'), [
            'product_id' => $this->product->id,
            'quantity' => 1,
        ]);

        $cartItem = CartItem::first();

        // Update Qty
        $updateResp = $this->actingAs($this->buyer)->patch(route('cart.update', $cartItem->id), [
            'quantity' => 3,
        ]);
        $updateResp->assertSessionHas('success');
        $this->assertEquals(3, $cartItem->fresh()->quantity);

        // Delete Item
        $deleteResp = $this->actingAs($this->buyer)->delete(route('cart.destroy', $cartItem->id));
        $deleteResp->assertSessionHas('success');
        $this->assertDatabaseCount('cart_items', 0);
    }

    public function test_checkout_successfully_creates_order_items_and_pending_payment(): void
    {
        // Add item to cart
        $this->actingAs($this->buyer)->post(route('cart.store'), [
            'product_id' => $this->product->id,
            'variant_id' => $this->variant->id,
            'quantity' => 2,
        ]);

        $initialProductStock = $this->product->fresh()->stock;
        $initialVariantStock = $this->variant->fresh()->stock;

        $address = Address::where('user_id', $this->buyer->id)->first();

        // Process Checkout
        $response = $this->actingAs($this->buyer)->post(route('checkout.process'), [
            'address_id' => $address->id,
            'courier' => 'jnt',
            'notes' => 'Tolong kirim siang hari',
        ]);

        // Pastikan order dan payment terbentuk
        $order = Order::where('user_id', $this->buyer->id)->first();
        $this->assertNotNull($order);
        $this->assertEquals('pending', $order->status);
        $this->assertEquals($this->store->id, $order->store_id);

        // Pastikan order_items terbentuk dengan snapshot harga yang tepat (100000 + 10000 = 110000)
        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'product_id' => $this->product->id,
            'product_price' => 110000,
            'quantity' => 2,
            'subtotal' => 220000,
        ]);

        // Pastikan payment berstatus pending dan gateway doku
        $this->assertDatabaseHas('payments', [
            'order_id' => $order->id,
            'gateway' => 'doku',
            'status' => 'pending',
            'amount' => $order->final_amount,
        ]);

        // Pastikan keranjang item sudah terhapus
        $this->assertDatabaseCount('cart_items', 0);

        // Sesuai spesifikasi: Stok TIDAK berkurang sebelum konfirmasi bayar
        $this->assertEquals($initialProductStock, $this->product->fresh()->stock);
        $this->assertEquals($initialVariantStock, $this->variant->fresh()->stock);

        // Sesuai spesifikasi Bagian 4: Redirect ke halaman Selesaikan Pembayaran (payment_groups.show)
        $response->assertRedirect(route('payment-groups.show', $order->payment_group_id));
    }

    public function test_voucher_validation(): void
    {
        $validVoucher = Voucher::create([
            'code' => 'TESTDISKON',
            'name' => 'Diskon 10rb',
            'type' => 'fixed',
            'discount_amount' => 10000,
            'min_spend' => 50000,
            'quota' => 5,
            'used_count' => 0,
            'start_date' => now()->subDay(),
            'end_date' => now()->addDays(5),
            'is_active' => true,
        ]);

        $expiredVoucher = Voucher::create([
            'code' => 'EXPIREDVOUCHER',
            'name' => 'Expired',
            'type' => 'fixed',
            'discount_amount' => 10000,
            'min_spend' => 10000,
            'quota' => 5,
            'used_count' => 0,
            'start_date' => now()->subDays(10),
            'end_date' => now()->subDays(1),
            'is_active' => true,
        ]);

        $exhaustedVoucher = Voucher::create([
            'code' => 'HABISKUOTA',
            'name' => 'Habis',
            'type' => 'fixed',
            'discount_amount' => 10000,
            'min_spend' => 10000,
            'quota' => 2,
            'used_count' => 2,
            'start_date' => now()->subDay(),
            'end_date' => now()->addDays(5),
            'is_active' => true,
        ]);

        // Isi keranjang Rp 100.000
        $this->actingAs($this->buyer)->post(route('cart.store'), [
            'product_id' => $this->product->id,
            'quantity' => 1,
        ]);

        // 1. Voucher Valid
        $resp1 = $this->actingAs($this->buyer)->post(route('checkout.voucher'), ['code' => 'TESTDISKON']);
        $resp1->assertSessionHas('success');
        $this->assertEquals('TESTDISKON', session('checkout_voucher'));

        // 2. Voucher Kedaluwarsa
        $resp2 = $this->actingAs($this->buyer)->post(route('checkout.voucher'), ['code' => 'EXPIREDVOUCHER']);
        $resp2->assertSessionHas('error');

        // 3. Voucher Kuota Habis
        $resp3 = $this->actingAs($this->buyer)->post(route('checkout.voucher'), ['code' => 'HABISKUOTA']);
        $resp3->assertSessionHas('error');

        // 4. Voucher di bawah min spend
        $highMinVoucher = Voucher::create([
            'code' => 'MINSPEND500K',
            'name' => 'Min 500k',
            'type' => 'fixed',
            'discount_amount' => 50000,
            'min_spend' => 500000,
            'quota' => 10,
            'used_count' => 0,
            'start_date' => now()->subDay(),
            'end_date' => now()->addDays(5),
            'is_active' => true,
        ]);
        $resp4 = $this->actingAs($this->buyer)->post(route('checkout.voucher'), ['code' => 'MINSPEND500K']);
        $resp4->assertSessionHas('error');
    }

    public function test_buyer_can_cancel_pending_order_but_cannot_cancel_shipped_order(): void
    {
        $orderPending = Order::create([
            'invoice_number' => 'INV/TEST/001',
            'user_id' => $this->buyer->id,
            'store_id' => $this->store->id,
            'total_amount' => 100000,
            'shipping_cost' => 14000,
            'discount_amount' => 0,
            'final_amount' => 114000,
            'status' => 'pending',
        ]);

        // Cancel Pending -> Berhasil
        $cancelResp = $this->actingAs($this->buyer)->post(route('orders.cancel', $orderPending->id));
        $cancelResp->assertSessionHas('success');
        $this->assertEquals('cancelled', $orderPending->fresh()->status);

        // Order Shipped -> Gagal dibatalkan
        $orderShipped = Order::create([
            'invoice_number' => 'INV/TEST/002',
            'user_id' => $this->buyer->id,
            'store_id' => $this->store->id,
            'total_amount' => 100000,
            'shipping_cost' => 14000,
            'discount_amount' => 0,
            'final_amount' => 114000,
            'status' => 'shipped',
        ]);

        $cancelShippedResp = $this->actingAs($this->buyer)->post(route('orders.cancel', $orderShipped->id));
        $cancelShippedResp->assertStatus(403);
        $this->assertEquals('shipped', $orderShipped->fresh()->status);
    }

    public function test_user_a_cannot_view_order_of_user_b(): void
    {
        $userB = User::factory()->create([
            'email' => 'userb@tokoku.com',
        ]);

        $orderUserB = Order::create([
            'invoice_number' => 'INV/TEST/USERB',
            'user_id' => $userB->id,
            'store_id' => $this->store->id,
            'total_amount' => 50000,
            'shipping_cost' => 14000,
            'discount_amount' => 0,
            'final_amount' => 64000,
            'status' => 'pending',
        ]);

        // Buyer mencoba melihat pesanan User B -> Harus 403 Forbidden
        $response = $this->actingAs($this->buyer)->get(route('orders.show', $orderUserB->id));
        $response->assertStatus(403);
    }
}
