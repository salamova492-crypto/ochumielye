<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'fullName' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'role' => 'visitor',
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    public function leader(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'leader',
        ]);
    }

    public function visitor(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'visitor',
        ]);
    }
}
