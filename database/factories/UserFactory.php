<?php

namespace Database\Factories;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'role_id' => null,
            'phone' => fake()->phoneNumber(),
            'avatar' => null,
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function withRole(string $slug): static
    {
        return $this->state(function (array $attributes) use ($slug) {
            $role = Role::where('slug', $slug)->first();
            return [
                'role_id' => $role?->id,
            ];
        });
    }

    public function admin(): static
    {
        return $this->withRole('admin');
    }

    public function seller(): static
    {
        return $this->withRole('seller');
    }

    public function buyer(): static
    {
        return $this->withRole('buyer');
    }
}
