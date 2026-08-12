<?php

namespace Database\Factories;

use App\Models\Dealership;
use App\Models\ServiceBay;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ServiceBay>
 */
class ServiceBayFactory extends Factory
{
    public function definition(): array
    {
        return [
            'dealership_id' => Dealership::factory(),
            'name' => 'Bay '.fake()->unique()->numberBetween(1, 100),
            'is_active' => true,
        ];
    }
}
