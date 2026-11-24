<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use App\Models\Store;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->words(3, true);
        return [
            'store_id' => Store::factory(),
            'category_id' => Category::factory(),
            'name' => ucwords($name),
            'slug' => Str::slug($name) . '-' . Str::random(5),
            'description' => fake()->paragraphs(3, true),
            'price' => fake()->numberBetween(25000, 2500000),
            'stock' => fake()->numberBetween(5, 200),
            'weight' => fake()->randomElement([200, 500, 1000, 1500]),
            'is_active' => true,
            'is_featured' => fake()->boolean(20),
        ];
    }
}
