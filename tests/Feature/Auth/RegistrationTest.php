<?php

namespace Tests\Feature\Auth;

use App\Models\Role;
use App\Models\Store;
use App\Models\User;
use App\Services\StoreService;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_register_as_buyer_creates_user_with_buyer_role_and_no_store(): void
    {
        $response = $this->post('/register', [
            'role' => 'buyer',
            'name' => 'Pembeli Setia',
            'email' => 'pembeli@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));

        $user = User::where('email', 'pembeli@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('buyer', $user->role->slug);
        $this->assertNull($user->store);
    }

    public function test_register_as_seller_creates_user_with_seller_role_and_store_redirects_to_seller_dashboard(): void
    {
        $response = $this->post('/register', [
            'role' => 'seller',
            'name' => 'Penjual Sukses',
            'email' => 'penjual@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'store_name' => 'Warung Makmur Jaya',
            'city' => 'Jakarta Selatan',
            'phone' => '081234567890',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('seller.dashboard'));

        $user = User::where('email', 'penjual@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('seller', $user->role->slug);
        $this->assertNotNull($user->store);
        $this->assertEquals('Warung Makmur Jaya', $user->store->name);
        $this->assertEquals('warung-makmur-jaya', $user->store->slug);
        $this->assertEquals('Jakarta Selatan', $user->store->city);
        $this->assertEquals('081234567890', $user->store->phone);
    }

    public function test_seller_registration_requires_store_name_city_and_phone_and_unique_store_name(): void
    {
        // 1. Missing required seller fields
        $response = $this->post('/register', [
            'role' => 'seller',
            'name' => 'Penjual Tanpa Toko',
            'email' => 'seller_invalid@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors(['store_name', 'city', 'phone']);
        $this->assertDatabaseMissing('users', ['email' => 'seller_invalid@example.com']);

        // 2. Duplicate store name
        $existingStore = Store::factory()->create([
            'name' => 'Toko Sudah Ada',
        ]);

        $response2 = $this->post('/register', [
            'role' => 'seller',
            'name' => 'Penjual Duplikat',
            'email' => 'seller_duplikat@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'store_name' => 'Toko Sudah Ada',
            'city' => 'Bandung',
            'phone' => '081122334455',
        ]);

        $response2->assertSessionHasErrors(['store_name']);
        $this->assertDatabaseMissing('users', ['email' => 'seller_duplikat@example.com']);
    }

    public function test_role_admin_or_invalid_role_is_rejected_and_no_user_created(): void
    {
        // Request with role = admin
        $responseAdmin = $this->post('/register', [
            'role' => 'admin',
            'name' => 'Hacker Admin',
            'email' => 'hacker@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $responseAdmin->assertSessionHasErrors(['role']);
        $this->assertDatabaseMissing('users', ['email' => 'hacker@example.com']);

        // Request with arbitrary role
        $responseInvalid = $this->post('/register', [
            'role' => 'superman',
            'name' => 'Invalid Role User',
            'email' => 'invalid@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $responseInvalid->assertSessionHasErrors(['role']);
        $this->assertDatabaseMissing('users', ['email' => 'invalid@example.com']);
    }

    public function test_manual_role_id_injection_is_ignored(): void
    {
        $adminRole = Role::firstOrCreate(['slug' => 'admin'], ['name' => 'Administrator']);

        $response = $this->post('/register', [
            'role' => 'buyer',
            'role_id' => $adminRole->id,
            'name' => 'Injected User',
            'email' => 'injected@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $user = User::where('email', 'injected@example.com')->first();
        $this->assertNotNull($user);
        $this->assertNotEquals($adminRole->id, $user->role_id);
        $this->assertEquals('buyer', $user->role->slug);
    }

    public function test_rollback_when_store_creation_fails_user_is_not_saved(): void
    {
        // Mock StoreService to throw an exception during store creation
        $mockStoreService = $this->mock(StoreService::class);
        $mockStoreService->shouldReceive('createStoreForUser')
            ->once()
            ->andThrow(new Exception('Simulated Database Store Failure'));

        try {
            $this->post('/register', [
                'role' => 'seller',
                'name' => 'Seller Rollback',
                'email' => 'rollback@example.com',
                'password' => 'password',
                'password_confirmation' => 'password',
                'store_name' => 'Toko Gagal',
                'city' => 'Semarang',
                'phone' => '089988776655',
            ]);
        } catch (Exception $e) {
            $this->assertEquals('Simulated Database Store Failure', $e->getMessage());
        }

        // Verify that the user was NOT saved (transaction rolled back)
        $this->assertDatabaseMissing('users', ['email' => 'rollback@example.com']);
        $this->assertDatabaseMissing('stores', ['name' => 'Toko Gagal']);
    }

    public function test_role_seller_query_param_and_old_input_on_validation_failure(): void
    {
        // 1. Query parameter ?role=seller
        $responseGet = $this->get('/register?role=seller');
        $responseGet->assertStatus(200);
        $responseGet->assertSee("role: 'seller'", false);

        // 2. Validation failure keeps old input
        $responsePost = $this->post('/register', [
            'role' => 'seller',
            'name' => 'Seller Old Input',
            'email' => 'seller_old@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'store_name' => 'Toko Keren Abis',
            'city' => 'Surabaya',
            // Missing phone to trigger validation error
        ]);

        $responsePost->assertSessionHasErrors(['phone']);
        $responsePost->assertSessionHasInput('role', 'seller');
        $responsePost->assertSessionHasInput('store_name', 'Toko Keren Abis');
        $responsePost->assertSessionHasInput('city', 'Surabaya');
    }
}
