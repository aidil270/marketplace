<?php

namespace Tests\Feature\Auth;

use App\Models\Role;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }

    public function test_redirect_after_login_according_to_role(): void
    {
        // Pastikan roles ada
        $buyerRole = Role::firstOrCreate(['slug' => 'buyer'], ['name' => 'Pembeli']);
        $sellerRole = Role::firstOrCreate(['slug' => 'seller'], ['name' => 'Penjual']);
        $adminRole = Role::firstOrCreate(['slug' => 'admin'], ['name' => 'Administrator']);

        // 1. Buyer -> /dashboard
        $buyer = User::factory()->create(['role_id' => $buyerRole->id]);
        $responseBuyer = $this->post('/login', [
            'email' => $buyer->email,
            'password' => 'password',
        ]);
        $this->assertAuthenticatedAs($buyer);
        $responseBuyer->assertRedirect(route('dashboard', absolute: false));

        $this->post('/logout');

        // 2. Seller -> /penjual (seller.dashboard)
        $seller = User::factory()->create(['role_id' => $sellerRole->id]);
        Store::factory()->create(['user_id' => $seller->id]);
        $responseSeller = $this->post('/login', [
            'email' => $seller->email,
            'password' => 'password',
        ]);
        $this->assertAuthenticatedAs($seller);
        $responseSeller->assertRedirect(route('seller.dashboard', absolute: false));

        $this->post('/logout');

        // 3. Admin -> /admin (admin.dashboard)
        $admin = User::factory()->create(['role_id' => $adminRole->id]);
        $responseAdmin = $this->post('/login', [
            'email' => $admin->email,
            'password' => 'password',
        ]);
        $this->assertAuthenticatedAs($admin);
        $responseAdmin->assertRedirect(route('admin.dashboard', absolute: false));
    }
}
