<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\MasterClass;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class EnrollmentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'master_class_id' => MasterClass::factory(),
            'user_id' => User::factory(),
        ];
    }
}
