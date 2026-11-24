<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Review>
 */
class ReviewFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->buyer(),
            'product_id' => Product::factory(),
            'order_id' => null,
            'rating' => fake()->numberBetween(4, 5),
            'comment' => fake()->sentence(),
        ];
    }
}
