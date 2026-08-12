<?php

namespace Database\Factories;

use App\Models\ServiceType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ServiceType>
 */
class ServiceTypeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->randomElement([
                'Oil Change',
                'Brake Inspection',
                'Tire Replacement',
                'Battery Replacement',
                'General Maintenance',
            ]),
            'duration_minutes' => fake()->randomElement([
                30,
                45,
                60,
                90,
                120,
            ]),
            'is_active' => true,
        ];
    }
}
