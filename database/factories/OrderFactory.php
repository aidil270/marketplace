<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    public function definition(): array
    {
        $total = fake()->numberBetween(50000, 1000000);
        $shipping = 15000;
        $discount = 0;
        $final = $total + $shipping - $discount;

        return [
            'invoice_number' => 'INV-' . date('Ymd') . '-' . strtoupper(Str::random(6)),
            'user_id' => User::factory()->buyer(),
            'store_id' => Store::factory(),
            'voucher_id' => null,
            'total_amount' => $total,
            'shipping_cost' => $shipping,
            'discount_amount' => $discount,
            'final_amount' => $final,
            'status' => fake()->randomElement(['pending', 'processing', 'shipped', 'completed', 'cancelled']),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
