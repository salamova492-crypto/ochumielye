<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\CreativityType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MasterClassFactory extends Factory
{
    public function definition(): array
    {
        return [
            'creativity_type_id' => CreativityType::factory(),
            'leader_id' => User::factory()->leader(),
            'title' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'date' => fake()->dateTimeBetween('+1 day', '+1 month')->format('Y-m-d'),
            'time_slot' => fake()->randomElement(['09:00-11:00', '11:00-13:00', '13:00-15:00', '15:00-17:00']),
            'maxPeople' => fake()->numberBetween(5, 20),
            'price' => fake()->randomFloat(2, 500, 5000),
        ];
    }
}
