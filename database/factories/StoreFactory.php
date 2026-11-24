<?php

namespace Database\Factories;

use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Store>
 */
class StoreFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->company() . ' Store';
        return [
            'user_id' => User::factory()->seller(),
            'name' => $name,
            'slug' => Str::slug($name) . '-' . Str::random(4),
            'description' => fake()->paragraph(),
            'logo' => null,
            'banner' => null,
            'city' => fake()->city(),
            'address' => fake()->address(),
            'is_active' => true,
        ];
    }
}
