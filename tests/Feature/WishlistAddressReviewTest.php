<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Review;
use App\Models\Role;
use App\Models\Shipment;
use App\Models\Store;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class WishlistAddressReviewTest extends TestCase
{
    use RefreshDatabase;

    private User $buyer;
    private User $otherBuyer;
    private Store $store;
    private Category $category;
    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Roles
        $buyerRole = Role::firstOrCreate(['slug' => 'buyer'], ['name' => 'Buyer']);
        $sellerRole = Role::firstOrCreate(['slug' => 'seller'], ['name' => 'Seller']);

        // 2. Users
        $this->buyer = User::factory()->create([
            'role_id' => $buyerRole->id,
            'email' => 'buyer_test@tokoku.com',
        ]);

        $this->otherBuyer = User::factory()->create([
            'role_id' => $buyerRole->id,
            'email' => 'other_test@tokoku.com',
        ]);

        $seller = User::factory()->create([
            'role_id' => $sellerRole->id,
            'email' => 'seller_test@tokoku.com',
        ]);

        // 3. Store, Category & Product
        $this->store = Store::create([
            'user_id' => $seller->id,
            'name' => 'Toko Mitra Uji',
            'slug' => 'toko-mitra-uji',
            'city' => 'Jakarta Selatan',
            'is_active' => true,
        ]);

        $this->category = Category::create([
            'name' => 'Gadget Uji',
            'slug' => 'gadget-uji',
            'is_active' => true,
        ]);

        $this->product = Product::create([
            'store_id' => $this->store->id,
            'category_id' => $this->category->id,
            'name' => 'Smartphone Canggih X1',
            'slug' => 'smartphone-canggih-x1',
            'description' => 'Deskripsi smartphone uji',
            'price' => 3500000,
            'stock' => 15,
            'weight' => 500,
            'is_active' => true,
        ]);
    }

    // ==========================================
    // A. WISHLIST TESTS
    // ==========================================

    public function test_guest_cannot_toggle_wishlist_and_is_redirected_to_login(): void
    {
        $response = $this->post(route('wishlist.toggle'), [
            'product_id' => $this->product->id,
        ]);

        $response->assertRedirect(route('login'));
        $this->assertDatabaseCount('wishlists', 0);
    }

    public function test_buyer_can_toggle_wishlist_add_and_remove(): void
    {
        // 1. Toggle Add (AJAX/JSON)
        $responseAdd = $this->actingAs($this->buyer)
            ->postJson(route('wishlist.toggle'), [
                'product_id' => $this->product->id,
            ]);

        $responseAdd->assertOk()
            ->assertJson([
                'status' => 'success',
                'in_wishlist' => true,
            ]);

        $this->assertDatabaseHas('wishlists', [
            'user_id' => $this->buyer->id,
            'product_id' => $this->product->id,
        ]);

        // 2. Toggle Remove
        $responseRemove = $this->actingAs($this->buyer)
            ->postJson(route('wishlist.toggle'), [
                'product_id' => $this->product->id,
            ]);

        $responseRemove->assertOk()
            ->assertJson([
                'status' => 'success',
                'in_wishlist' => false,
            ]);

        $this->assertDatabaseMissing('wishlists', [
            'user_id' => $this->buyer->id,
            'product_id' => $this->product->id,
        ]);
    }

    public function test_buyer_can_view_wishlist_page(): void
    {
        Wishlist::create([
            'user_id' => $this->buyer->id,
            'product_id' => $this->product->id,
        ]);

        $response = $this->actingAs($this->buyer)->get(route('wishlist.index'));

        $response->assertOk()
            ->assertSee('Smartphone Canggih X1')
            ->assertSee('Wishlist');
    }

    // ==========================================
    // B. ADDRESS TESTS
    // ==========================================

    public function test_buyer_can_create_address_and_first_address_is_automatically_primary(): void
    {
        $response = $this->actingAs($this->buyer)->post(route('addresses.store'), [
            'recipient_name' => 'Budi Santoso',
            'phone_number' => '081234567890',
            'label' => 'Rumah',
            'province' => 'DKI Jakarta',
            'city' => 'Jakarta Selatan',
            'district' => 'Tebet',
            'postal_code' => '12810',
            'full_address' => 'Jl. Tebet Barat Dalam No. 12',
            'is_primary' => false, // Sengaja false, sistem harus mengotomatiskan jadi true untuk alamat pertama
        ]);

        $response->assertRedirect(route('addresses.index'));

        $this->assertDatabaseHas('addresses', [
            'user_id' => $this->buyer->id,
            'recipient_name' => 'Budi Santoso',
            'is_primary' => true,
        ]);
    }

    public function test_creating_second_address_with_primary_unsets_previous_primary(): void
    {
        // Alamat pertama
        $addr1 = Address::create([
            'user_id' => $this->buyer->id,
            'recipient_name' => 'Alamat Pertama',
            'phone_number' => '0811111111',
            'label' => 'Rumah',
            'province' => 'DKI Jakarta',
            'city' => 'Jakarta Selatan',
            'postal_code' => '12000',
            'full_address' => 'Alamat 1',
            'is_primary' => true,
        ]);

        // Alamat kedua dijadikan utama
        $this->actingAs($this->buyer)->post(route('addresses.store'), [
            'recipient_name' => 'Alamat Kantor',
            'phone_number' => '0822222222',
            'label' => 'Kantor',
            'province' => 'DKI Jakarta',
            'city' => 'Jakarta Pusat',
            'postal_code' => '10110',
            'full_address' => 'Gedung Wisma 46',
            'is_primary' => '1',
        ]);

        $this->assertFalse((bool) $addr1->fresh()->is_primary);
        $this->assertDatabaseHas('addresses', [
            'recipient_name' => 'Alamat Kantor',
            'is_primary' => true,
        ]);
    }

    public function test_user_a_cannot_update_or_delete_user_b_address(): void
    {
        $addressB = Address::create([
            'user_id' => $this->otherBuyer->id,
            'recipient_name' => 'Penerima B',
            'phone_number' => '0899999999',
            'label' => 'Rumah',
            'province' => 'Jawa Barat',
            'city' => 'Bandung',
            'postal_code' => '40111',
            'full_address' => 'Jl. Dago No. 10',
            'is_primary' => true,
        ]);

        // User A mencoba mengedit alamat User B
        $responseEdit = $this->actingAs($this->buyer)->put(route('addresses.update', $addressB->id), [
            'recipient_name' => 'Hacked Name',
            'phone_number' => '0899999999',
            'label' => 'Rumah',
            'province' => 'Jawa Barat',
            'city' => 'Bandung',
            'postal_code' => '40111',
            'full_address' => 'Jl. Dago No. 10',
        ]);

        $responseEdit->assertForbidden();

        // User A mencoba menghapus alamat User B
        $responseDelete = $this->actingAs($this->buyer)->delete(route('addresses.destroy', $addressB->id));
        $responseDelete->assertForbidden();
    }

    public function test_deleting_primary_address_promotes_remaining_address_and_does_not_break_old_order(): void
    {
        $primaryAddr = Address::create([
            'user_id' => $this->buyer->id,
            'recipient_name' => 'Alamat Rumah Utama',
            'phone_number' => '0812345678',
            'label' => 'Rumah',
            'province' => 'DKI Jakarta',
            'city' => 'Jakarta Selatan',
            'postal_code' => '12810',
            'full_address' => 'Jl. Tebet Barat No. 5',
            'is_primary' => true,
        ]);

        $secondaryAddr = Address::create([
            'user_id' => $this->buyer->id,
            'recipient_name' => 'Alamat Kantor Cadangan',
            'phone_number' => '0812999999',
            'label' => 'Kantor',
            'province' => 'DKI Jakarta',
            'city' => 'Jakarta Pusat',
            'postal_code' => '10220',
            'full_address' => 'Jl. Sudirman Kav 21',
            'is_primary' => false,
        ]);

        // Buat Order masa lalu dengan snapshot alamat
        $order = Order::create([
            'invoice_number' => 'INV/20261124/TEST01',
            'user_id' => $this->buyer->id,
            'store_id' => $this->store->id,
            'total_amount' => 3500000,
            'shipping_cost' => 14000,
            'discount_amount' => 0,
            'final_amount' => 3514000,
            'status' => 'pending',
            'shipping_address' => "Alamat Rumah Utama (0812345678)\nJl. Tebet Barat No. 5, Jakarta Selatan, DKI Jakarta 12810",
        ]);

        Shipment::create([
            'order_id' => $order->id,
            'courier_name' => 'J&T Express',
            'recipient_address' => "Alamat Rumah Utama (0812345678)\nJl. Tebet Barat No. 5, Jakarta Selatan, DKI Jakarta 12810",
        ]);

        // Hapus alamat utama
        $this->actingAs($this->buyer)->delete(route('addresses.destroy', $primaryAddr->id));

        // 1. Alamat terhapus secara soft delete
        $this->assertSoftDeleted('addresses', ['id' => $primaryAddr->id]);

        // 2. Alamat cadangan otomatis dipromosikan menjadi utama
        $this->assertTrue((bool) $secondaryAddr->fresh()->is_primary);

        // 3. Detail order lama tetap dapat dibuka dan alamat tersaji utuh tanpa error
        $responseOrder = $this->actingAs($this->buyer)->get(route('orders.show', $order->id));
        $responseOrder->assertOk()
            ->assertSee('Alamat Rumah Utama (0812345678)');
    }

    // ==========================================
    // C. REVIEW & RATING TESTS
    // ==========================================

    public function test_buyer_can_review_product_after_order_is_completed(): void
    {
        Storage::fake('public');

        $order = Order::create([
            'invoice_number' => 'INV/20261124/REV001',
            'user_id' => $this->buyer->id,
            'store_id' => $this->store->id,
            'total_amount' => 3500000,
            'shipping_cost' => 14000,
            'discount_amount' => 0,
            'final_amount' => 3514000,
            'status' => 'completed',
        ]);

        $orderItem = OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'product_price' => $this->product->price,
            'quantity' => 1,
            'subtotal' => $this->product->price,
        ]);

        $imageFile = UploadedFile::fake()->image('review1.jpg', 600, 600);

        $response = $this->actingAs($this->buyer)->post(
            route('reviews.store', ['order' => $order->id, 'orderItem' => $orderItem->id]),
            [
                'rating' => 5,
                'comment' => 'Barang sangat bagus, pengiriman super cepat dan packing rapi!',
                'photos' => [$imageFile],
            ]
        );

        $response->assertRedirect(route('orders.show', $order->id));

        $this->assertDatabaseHas('reviews', [
            'user_id' => $this->buyer->id,
            'product_id' => $this->product->id,
            'order_item_id' => $orderItem->id,
            'rating' => 5,
            'comment' => 'Barang sangat bagus, pengiriman super cepat dan packing rapi!',
        ]);

        $review = Review::where('order_item_id', $orderItem->id)->first();
        $this->assertNotNull($review);
        $this->assertCount(1, $review->images);
    }

    public function test_buyer_cannot_review_if_order_is_not_completed(): void
    {
        $order = Order::create([
            'invoice_number' => 'INV/20261124/REV002',
            'user_id' => $this->buyer->id,
            'store_id' => $this->store->id,
            'total_amount' => 3500000,
            'shipping_cost' => 14000,
            'discount_amount' => 0,
            'final_amount' => 3514000,
            'status' => 'shipped', // Belum completed
        ]);

        $orderItem = OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'product_price' => $this->product->price,
            'quantity' => 1,
            'subtotal' => $this->product->price,
        ]);

        $response = $this->actingAs($this->buyer)->post(
            route('reviews.store', ['order' => $order->id, 'orderItem' => $orderItem->id]),
            [
                'rating' => 5,
                'comment' => 'Coba mengulas saat belum selesai',
            ]
        );

        $response->assertRedirect(route('orders.show', $order->id));
        $this->assertDatabaseCount('reviews', 0);
    }

    public function test_buyer_cannot_review_same_item_twice(): void
    {
        $order = Order::create([
            'invoice_number' => 'INV/20261124/REV003',
            'user_id' => $this->buyer->id,
            'store_id' => $this->store->id,
            'total_amount' => 3500000,
            'shipping_cost' => 14000,
            'discount_amount' => 0,
            'final_amount' => 3514000,
            'status' => 'completed',
        ]);

        $orderItem = OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'product_price' => $this->product->price,
            'quantity' => 1,
            'subtotal' => $this->product->price,
        ]);

        // Review pertama
        Review::create([
            'user_id' => $this->buyer->id,
            'product_id' => $this->product->id,
            'order_id' => $order->id,
            'order_item_id' => $orderItem->id,
            'rating' => 4,
            'comment' => 'Ulasan pertama',
        ]);

        // Percobaan review kedua
        $response = $this->actingAs($this->buyer)->post(
            route('reviews.store', ['order' => $order->id, 'orderItem' => $orderItem->id]),
            [
                'rating' => 5,
                'comment' => 'Mencoba ulas lagi',
            ]
        );

        $response->assertRedirect(route('orders.show', $order->id));
        $this->assertEquals(1, Review::where('order_item_id', $orderItem->id)->count());
    }

    public function test_review_validation_rejects_rating_outside_range_and_invalid_files(): void
    {
        $order = Order::create([
            'invoice_number' => 'INV/20261124/REV004',
            'user_id' => $this->buyer->id,
            'store_id' => $this->store->id,
            'total_amount' => 3500000,
            'shipping_cost' => 14000,
            'discount_amount' => 0,
            'final_amount' => 3514000,
            'status' => 'completed',
        ]);

        $orderItem = OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'product_price' => $this->product->price,
            'quantity' => 1,
            'subtotal' => $this->product->price,
        ]);

        // 1. Rating di luar range (misal 6 atau 0)
        $invalidRatingResponse = $this->actingAs($this->buyer)->post(
            route('reviews.store', ['order' => $order->id, 'orderItem' => $orderItem->id]),
            ['rating' => 6]
        );
        $invalidRatingResponse->assertSessionHasErrors('rating');

        // 2. File bukan gambar (misal file text)
        $textFile = UploadedFile::fake()->create('document.txt', 100);
        $invalidFileResponse = $this->actingAs($this->buyer)->post(
            route('reviews.store', ['order' => $order->id, 'orderItem' => $orderItem->id]),
            [
                'rating' => 5,
                'photos' => [$textFile],
            ]
        );
        $invalidFileResponse->assertSessionHasErrors('photos.0');
    }

    public function test_product_average_rating_and_masked_author(): void
    {
        Review::create([
            'user_id' => $this->buyer->id,
            'product_id' => $this->product->id,
            'rating' => 4,
            'comment' => 'Bagus',
        ]);

        Review::create([
            'user_id' => $this->otherBuyer->id,
            'product_id' => $this->product->id,
            'rating' => 5,
            'comment' => 'Sangat bagus!',
        ]);

        // Rata-rata 4.5
        $this->assertEquals(4.5, $this->product->average_rating);
        $this->assertEquals(2, $this->product->reviews_count);

        // Penyamaran nama pembeli
        $review = Review::where('user_id', $this->buyer->id)->first();
        $this->assertStringContainsString('*', $review->masked_author);
    }
}
