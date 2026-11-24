<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\PaymentGroup;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Role;
use App\Models\Store;
use App\Models\User;
use App\Models\Voucher;
use App\Services\DokuService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class DokuPaymentTest extends TestCase
{
    use RefreshDatabase;

    private User $buyer;
    private User $otherBuyer;
    private Store $store1;
    private Store $store2;
    private Product $product1;
    private Product $product2;
    private ProductVariant $variant1;

    protected function setUp(): void
    {
        parent::setUp();

        // Kredensial Mocking DOKU Sandbox
        Config::set('doku.client_id', 'MCH-TEST-CLIENT-123');
        Config::set('doku.secret_key', 'SK-TEST-SECRET-KEY-ABC');
        Config::set('doku.base_url', 'https://api-sandbox.doku.com');

        $buyerRole = Role::firstOrCreate(['slug' => 'buyer'], ['name' => 'Buyer']);
        $sellerRole = Role::firstOrCreate(['slug' => 'seller'], ['name' => 'Seller']);

        $this->buyer = User::factory()->create([
            'role_id' => $buyerRole->id,
            'name' => 'Budi Pembeli',
            'email' => 'budi@test.com',
        ]);

        $this->otherBuyer = User::factory()->create([
            'role_id' => $buyerRole->id,
            'name' => 'Joko Pembeli Lain',
            'email' => 'joko@test.com',
        ]);

        $seller1 = User::factory()->create(['role_id' => $sellerRole->id]);
        $seller2 = User::factory()->create(['role_id' => $sellerRole->id]);

        $this->store1 = Store::create([
            'user_id' => $seller1->id,
            'name' => 'Toko Elektronik Maju',
            'slug' => 'toko-elektronik-maju',
            'city' => 'Jakarta Barat',
            'is_active' => true,
        ]);

        $this->store2 = Store::create([
            'user_id' => $seller2->id,
            'name' => 'Toko Pakaian Keren',
            'slug' => 'toko-pakaian-keren',
            'city' => 'Bandung',
            'is_active' => true,
        ]);

        $category = Category::create([
            'name' => 'Umum',
            'slug' => 'umum',
            'is_active' => true,
        ]);

        $this->product1 = Product::create([
            'store_id' => $this->store1->id,
            'category_id' => $category->id,
            'name' => 'Headset Bluetooth',
            'slug' => 'headset-bluetooth',
            'description' => 'Headset mantap',
            'price' => 200000,
            'stock' => 10,
            'weight' => 200,
            'is_active' => true,
        ]);

        $this->variant1 = ProductVariant::create([
            'product_id' => $this->product1->id,
            'name' => 'Warna',
            'value' => 'Hitam',
            'additional_price' => 20000,
            'stock' => 5,
        ]);

        $this->product2 = Product::create([
            'store_id' => $this->store2->id,
            'category_id' => $category->id,
            'name' => 'Kemeja Katun',
            'slug' => 'kemeja-katun',
            'description' => 'Kemeja katun premium',
            'price' => 150000,
            'stock' => 8,
            'weight' => 250,
            'is_active' => true,
        ]);

        Address::create([
            'user_id' => $this->buyer->id,
            'recipient_name' => 'Budi Pembeli',
            'phone_number' => '081234567890',
            'label' => 'Rumah',
            'province' => 'DKI Jakarta',
            'city' => 'Jakarta Barat',
            'postal_code' => '11530',
            'full_address' => 'Jl. Kebon Jeruk No. 5',
            'is_primary' => true,
        ]);
    }

    /**
     * 1. Test multi-store checkout menghasilkan satu payment_group dan beberapa order.
     */
    public function test_multi_store_checkout_creates_one_payment_group_and_multiple_orders(): void
    {
        Http::fake([
            'https://api-sandbox.doku.com/checkout/v1/payment' => Http::response([
                'order' => [
                    'invoice_number' => 'PAY/20261124/TEST01',
                ],
                'payment' => [
                    'url' => 'https://staging.doku.com/checkout/mock-url-123',
                ],
            ], 200),
        ]);

        $cart = Cart::create(['user_id' => $this->buyer->id]);

        // Item dari Toko 1
        CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $this->product1->id,
            'product_variant_id' => $this->variant1->id,
            'quantity' => 1,
            'is_selected' => true,
        ]);

        // Item dari Toko 2
        CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $this->product2->id,
            'product_variant_id' => null,
            'quantity' => 2,
            'is_selected' => true,
        ]);

        $address = Address::where('user_id', $this->buyer->id)->first();

        $response = $this->actingAs($this->buyer)->post(route('checkout.process'), [
            'address_id' => $address->id,
            'courier' => 'jnt',
        ]);

        // Pastikan terbentuk 1 payment_group
        $paymentGroup = PaymentGroup::where('user_id', $this->buyer->id)->latest()->first();
        $this->assertNotNull($paymentGroup);
        $this->assertEquals('pending', $paymentGroup->status);

        // Pastikan terbentuk 2 orders untuk 2 toko yang berbeda
        $orders = Order::where('payment_group_id', $paymentGroup->id)->get();
        $this->assertCount(2, $orders);

        // Redirect ke halaman selesaikan pembayaran
        $response->assertRedirect(route('payment-groups.show', $paymentGroup->id));

        // Pastikan payment_url tersimpan
        $this->assertEquals('https://staging.doku.com/checkout/mock-url-123', $paymentGroup->fresh()->payment_url);
    }

    /**
     * 2. Test membuat transaksi dari payment_group (request memuat signature dan simpan payment_url).
     */
    public function test_create_checkout_generates_valid_headers_and_updates_payment_group(): void
    {
        Http::fake([
            'https://api-sandbox.doku.com/checkout/v1/payment' => function (\Illuminate\Http\Client\Request $request) {
                // Verifikasi header DOKU terkirim lengkap
                $this->assertEquals('MCH-TEST-CLIENT-123', $request->header('Client-Id')[0]);
                $this->assertNotEmpty($request->header('Request-Id')[0]);
                $this->assertNotEmpty($request->header('Request-Timestamp')[0]);
                $this->assertStringStartsWith('HMACSHA256=', $request->header('Signature')[0]);

                return Http::response([
                    'order' => [
                        'invoice_number' => 'PAY/20261124/TEST02',
                    ],
                    'payment' => [
                        'url' => 'https://staging.doku.com/checkout/unique-session-456',
                    ],
                ], 200);
            },
        ]);

        $paymentGroup = PaymentGroup::create([
            'user_id' => $this->buyer->id,
            'invoice_number' => 'PAY/20261124/TEST02',
            'total_amount' => 500000,
            'status' => 'pending',
            'expired_at' => now()->addDay(),
        ]);

        $service = app(DokuService::class);
        $result = $service->createCheckout($paymentGroup);

        $this->assertTrue($result['success']);
        $this->assertEquals('https://staging.doku.com/checkout/unique-session-456', $result['payment_url']);
        $this->assertEquals('https://staging.doku.com/checkout/unique-session-456', $paymentGroup->fresh()->payment_url);
    }

    /**
     * 3. Test kunci kosong menghasilkan pesan ramah tanpa memanggil API DOKU.
     */
    public function test_empty_credentials_returns_friendly_error_without_calling_api(): void
    {
        // Kosongkan konfigurasi
        Config::set('doku.client_id', '');
        Config::set('doku.secret_key', '');

        Http::fake();

        $paymentGroup = PaymentGroup::create([
            'user_id' => $this->buyer->id,
            'invoice_number' => 'PAY/20261124/NOKEY',
            'total_amount' => 100000,
            'status' => 'pending',
        ]);

        $service = app(DokuService::class);
        $result = $service->createCheckout($paymentGroup);

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('Kredensial DOKU Payment Gateway belum dikonfigurasi', $result['message']);

        // Pastikan tidak ada request HTTP yang terkirim ke DOKU
        Http::assertNothingSent();
    }

    /**
     * 4. Test webhook notifikasi signature valid vs signature salah (ditolak 401).
     */
    public function test_webhook_rejects_invalid_signature_and_accepts_valid_signature(): void
    {
        $paymentGroup = PaymentGroup::create([
            'user_id' => $this->buyer->id,
            'invoice_number' => 'PAY/20261124/NOTIF01',
            'total_amount' => 250000,
            'status' => 'pending',
        ]);

        $payload = [
            'order' => [
                'invoice_number' => $paymentGroup->invoice_number,
                'amount' => 250000,
            ],
            'transaction' => [
                'status' => 'SUCCESS',
                'date' => now()->toISOString(),
            ],
        ];

        $rawBody = json_encode($payload);
        $service = app(DokuService::class);
        $timestamp = now()->setTimezone('UTC')->format('Y-m-d\TH:i:s\Z');
        $requestId = 'req-test-123';

        // 1. Signature Salah -> Ditolak 401
        $badResponse = $this->postJson(route('doku.notification'), $payload, [
            'Client-Id' => 'MCH-TEST-CLIENT-123',
            'Request-Id' => $requestId,
            'Request-Timestamp' => $timestamp,
            'Signature' => 'HMACSHA256=invalidSignatureFake',
        ]);
        $badResponse->assertStatus(401);

        // 2. Signature Benar -> Diterima 200
        $validSignature = $service->generateSignature($requestId, $timestamp, '/api/doku/notification', $rawBody);

        $goodResponse = $this->call('POST', route('doku.notification'), [], [], [], [
            'HTTP_CONTENT_TYPE' => 'application/json',
            'HTTP_CLIENT_ID' => 'MCH-TEST-CLIENT-123',
            'HTTP_REQUEST_ID' => $requestId,
            'HTTP_REQUEST_TIMESTAMP' => $timestamp,
            'HTTP_SIGNATURE' => $validSignature,
        ], $rawBody);

        $goodResponse->assertStatus(200);
        $this->assertEquals('paid', $paymentGroup->fresh()->status);
    }

    /**
     * 5. Test webhook: nominal tidak cocok ditolak 400.
     */
    public function test_webhook_rejects_amount_mismatch(): void
    {
        $paymentGroup = PaymentGroup::create([
            'user_id' => $this->buyer->id,
            'invoice_number' => 'PAY/20261124/MISMATCH',
            'total_amount' => 300000, // Tagihan asli 300rb
            'status' => 'pending',
        ]);

        $payload = [
            'order' => [
                'invoice_number' => $paymentGroup->invoice_number,
                'amount' => 150000, // Notifikasi hanya 150rb
            ],
            'transaction' => [
                'status' => 'SUCCESS',
            ],
        ];

        $rawBody = json_encode($payload);
        $service = app(DokuService::class);
        $timestamp = now()->setTimezone('UTC')->format('Y-m-d\TH:i:s\Z');
        $requestId = 'req-mismatch-123';
        $validSignature = $service->generateSignature($requestId, $timestamp, '/api/doku/notification', $rawBody);

        $response = $this->call('POST', route('doku.notification'), [], [], [], [
            'HTTP_CONTENT_TYPE' => 'application/json',
            'HTTP_CLIENT_ID' => 'MCH-TEST-CLIENT-123',
            'HTTP_REQUEST_ID' => $requestId,
            'HTTP_REQUEST_TIMESTAMP' => $timestamp,
            'HTTP_SIGNATURE' => $validSignature,
        ], $rawBody);

        $response->assertStatus(400);
        $this->assertEquals('pending', $paymentGroup->fresh()->status);
    }

    /**
     * 6. Test status paid: order processing dan stok berkurang tepat satu kali (idempotent).
     */
    public function test_paid_notification_updates_orders_to_processing_and_decrements_stock_once(): void
    {
        $initialProductStock = $this->product1->stock;
        $initialVariantStock = $this->variant1->stock;

        $paymentGroup = PaymentGroup::create([
            'user_id' => $this->buyer->id,
            'invoice_number' => 'PAY/20261124/STOCKTEST',
            'total_amount' => 220000,
            'status' => 'pending',
        ]);

        $order = Order::create([
            'invoice_number' => 'INV/20261124/ORD01',
            'user_id' => $this->buyer->id,
            'store_id' => $this->store1->id,
            'payment_group_id' => $paymentGroup->id,
            'total_amount' => 220000,
            'shipping_cost' => 0,
            'discount_amount' => 0,
            'final_amount' => 220000,
            'status' => 'pending',
        ]);

        Payment::create([
            'order_id' => $order->id,
            'gateway' => 'doku',
            'amount' => 220000,
            'status' => 'pending',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $this->product1->id,
            'product_variant_id' => $this->variant1->id,
            'product_name' => 'Headset Bluetooth',
            'product_price' => 220000,
            'quantity' => 2,
            'subtotal' => 220000,
        ]);

        $service = app(DokuService::class);

        // Eksekusi Mark Paid pertama kali
        $service->markAsPaid($paymentGroup, ['notif' => 'payload1']);

        $this->assertEquals('paid', $paymentGroup->fresh()->status);
        $this->assertEquals('processing', $order->fresh()->status);
        $this->assertEquals('paid', $order->payment->fresh()->status);

        // Stok berkurang 2
        $this->assertEquals($initialProductStock - 2, $this->product1->fresh()->stock);
        $this->assertEquals($initialVariantStock - 2, $this->variant1->fresh()->stock);

        // Eksekusi Mark Paid kedua kali (notifikasi duplikat)
        $service->markAsPaid($paymentGroup, ['notif' => 'payload2']);

        // Stok TIDAK boleh berkurang dua kali (harus tetap -2)
        $this->assertEquals($initialProductStock - 2, $this->product1->fresh()->stock);
        $this->assertEquals($initialVariantStock - 2, $this->variant1->fresh()->stock);
    }

    /**
     * 7. Test status failed/expired: order dibatalkan, kuota voucher dikembalikan, stok tidak berubah.
     */
    public function test_failed_or_expired_notification_cancels_order_and_restores_voucher_without_changing_stock(): void
    {
        $initialProductStock = $this->product1->stock;

        $voucher = Voucher::create([
            'code' => 'VOUCHERDOKU',
            'name' => 'Diskon 20rb',
            'type' => 'fixed',
            'discount_amount' => 20000,
            'min_spend' => 100000,
            'quota' => 5,
            'used_count' => 1,
            'start_date' => now()->subDay(),
            'end_date' => now()->addDays(5),
            'is_active' => true,
        ]);

        $paymentGroup = PaymentGroup::create([
            'user_id' => $this->buyer->id,
            'invoice_number' => 'PAY/20261124/EXPIRED',
            'total_amount' => 180000,
            'status' => 'pending',
        ]);

        $order = Order::create([
            'invoice_number' => 'INV/20261124/EX01',
            'user_id' => $this->buyer->id,
            'store_id' => $this->store1->id,
            'payment_group_id' => $paymentGroup->id,
            'voucher_id' => $voucher->id,
            'total_amount' => 200000,
            'shipping_cost' => 0,
            'discount_amount' => 20000,
            'final_amount' => 180000,
            'status' => 'pending',
        ]);

        $service = app(DokuService::class);
        $service->markAsFailedOrExpired($paymentGroup, 'expired', ['msg' => 'expired']);

        $this->assertEquals('expired', $paymentGroup->fresh()->status);
        $this->assertEquals('cancelled', $order->fresh()->status);
        $this->assertEquals(0, $voucher->fresh()->used_count); // Kuota dikembalikan
        $this->assertEquals($initialProductStock, $this->product1->fresh()->stock); // Stok utuh
    }

    /**
     * 8. Test otorisasi: User A tidak bisa melihat atau membayar payment_group User B.
     */
    public function test_user_a_cannot_view_or_pay_user_b_payment_group(): void
    {
        $groupUserB = PaymentGroup::create([
            'user_id' => $this->otherBuyer->id,
            'invoice_number' => 'PAY/20261124/USERB',
            'total_amount' => 500000,
            'status' => 'pending',
        ]);

        // User A mencoba melihat halaman pembayaran User B
        $respView = $this->actingAs($this->buyer)->get(route('payment-groups.show', $groupUserB->id));
        $respView->assertForbidden();

        // User A mencoba memicu pembayaran User B
        $respPay = $this->actingAs($this->buyer)->post(route('payment-groups.pay', $groupUserB->id));
        $respPay->assertForbidden();
    }

    /**
     * 9. Test cek status pembayaran memperbarui status sesuai respons DOKU (di-fake).
     */
    public function test_check_status_updates_payment_group_from_doku_api(): void
    {
        $paymentGroup = PaymentGroup::create([
            'user_id' => $this->buyer->id,
            'invoice_number' => 'PAY/20261124/STATUSCHECK',
            'total_amount' => 200000,
            'status' => 'pending',
        ]);

        $order = Order::create([
            'invoice_number' => 'INV/20261124/ST01',
            'user_id' => $this->buyer->id,
            'store_id' => $this->store1->id,
            'payment_group_id' => $paymentGroup->id,
            'total_amount' => 200000,
            'shipping_cost' => 0,
            'discount_amount' => 0,
            'final_amount' => 200000,
            'status' => 'pending',
        ]);

        Http::fake([
            'https://api-sandbox.doku.com/orders/v1/status/' . $paymentGroup->invoice_number => Http::response([
                'transaction' => [
                    'status' => 'SUCCESS',
                    'date' => now()->toISOString(),
                ],
            ], 200),
        ]);

        $response = $this->actingAs($this->buyer)->post(route('payment-groups.check', $paymentGroup->id));

        $response->assertRedirect(route('payment-groups.show', $paymentGroup->id));
        $this->assertEquals('paid', $paymentGroup->fresh()->status);
        $this->assertEquals('processing', $order->fresh()->status);
    }
}
