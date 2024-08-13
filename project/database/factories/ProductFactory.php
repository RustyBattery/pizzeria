<?php

namespace Database\Factories;

use App\Models\Product;
use Exception;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     * @throws Exception
     */
    public function definition(): array
    {
        return [
            'name' => fake()->sentence(random_int(1, 5)),
            'description' => fake()->text(),
            'price' => random_int(100, 1000) * 100,
            'in_stock' => random_int(1,100) > 30,
            'category_id' => null
        ];
    }
}
