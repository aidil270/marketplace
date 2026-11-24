<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::where('slug', 'admin')->first();
        $sellerRole = Role::where('slug', 'seller')->first();
        $buyerRole = Role::where('slug', 'buyer')->first();

        // 1 Admin
        User::firstOrCreate(
            ['email' => 'admin@tokoku.com'],
            [
                'name' => 'Admin TokoKu',
                'password' => Hash::make('password'),
                'role_id' => $adminRole?->id,
                'phone' => '081234567890',
                'email_verified_at' => now(),
            ]
        );

        // 2 Penjual (Sellers)
        $seller1 = User::firstOrCreate(
            ['email' => 'seller1@tokoku.com'],
            [
                'name' => 'Budi Santoso',
                'password' => Hash::make('password'),
                'role_id' => $sellerRole?->id,
                'phone' => '081298765431',
                'email_verified_at' => now(),
            ]
        );

        $seller2 = User::firstOrCreate(
            ['email' => 'seller2@tokoku.com'],
            [
                'name' => 'Siti Rahma',
                'password' => Hash::make('password'),
                'role_id' => $sellerRole?->id,
                'phone' => '081298765432',
                'email_verified_at' => now(),
            ]
        );

        // 3 Pembeli (Buyers)
        $buyer1 = User::firstOrCreate(
            ['email' => 'buyer1@tokoku.com'],
            [
                'name' => 'Ahmad Rizky',
                'password' => Hash::make('password'),
                'role_id' => $buyerRole?->id,
                'phone' => '081311223344',
                'email_verified_at' => now(),
            ]
        );

        $buyer2 = User::firstOrCreate(
            ['email' => 'buyer2@tokoku.com'],
            [
                'name' => 'Dewi Lestari',
                'password' => Hash::make('password'),
                'role_id' => $buyerRole?->id,
                'phone' => '081322334455',
                'email_verified_at' => now(),
            ]
        );

        $buyer3 = User::firstOrCreate(
            ['email' => 'buyer3@tokoku.com'],
            [
                'name' => 'Doni Pratama',
                'password' => Hash::make('password'),
                'role_id' => $buyerRole?->id,
                'phone' => '081333445566',
                'email_verified_at' => now(),
            ]
        );

        // Alamat untuk Pembeli
        Address::firstOrCreate(
            ['user_id' => $buyer1->id, 'recipient_name' => 'Ahmad Rizky'],
            [
                'phone_number' => '081311223344',
                'full_address' => 'Jl. Sudirman No. 45, RT 02/RW 04, Kebayoran Baru',
                'province' => 'DKI Jakarta',
                'city' => 'Jakarta Selatan',
                'district' => 'Kebayoran Baru',
                'postal_code' => '12180',
                'is_primary' => true,
            ]
        );

        Address::firstOrCreate(
            ['user_id' => $buyer2->id, 'recipient_name' => 'Dewi Lestari'],
            [
                'phone_number' => '081322334455',
                'full_address' => 'Jl. Dago No. 120, Coblong',
                'province' => 'Jawa Barat',
                'city' => 'Bandung',
                'district' => 'Coblong',
                'postal_code' => '40135',
                'is_primary' => true,
            ]
        );
    }
}
