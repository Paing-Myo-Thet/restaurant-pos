<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(2, true),
            'description' => null,
            'category_id' => Category::factory(),
            'cost' => 1000,
            'price' => 2500,
            'stock' => 50,
            'status' => true,
        ];
    }
}
