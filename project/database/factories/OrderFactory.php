<?php

namespace Database\Factories;

use Exception;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
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
            'user_id' => null,
            'address_id' => null,
            'phone' => '+7(999)999-99-99',
            'email' => fake()->email(),
            'cost' => random_int(500, 10000) * 100,
            'delivery_time' => fake()->dateTime(),
            'status' => fake()->randomElement(['created', 'in_process', 'done', 'canceled']),
        ];
    }
}
