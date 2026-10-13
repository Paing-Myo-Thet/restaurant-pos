<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Table>
 */
class TableFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => 'Table '.fake()->unique()->numberBetween(1, 999),
            'capacity' => 4,
            'status' => 'available',
        ];
    }
}
