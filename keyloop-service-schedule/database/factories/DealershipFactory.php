<?php

namespace Database\Factories;

use App\Models\Dealership;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Dealership>
 */
class DealershipFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'timezone' => 'Asia/Ho_Chi_Minh',
            'is_active' => true,
        ];
    }
}
