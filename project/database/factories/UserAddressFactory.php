<?php

namespace Database\Factories;

use App\Models\UserAddress;
use Exception;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserAddress>
 */
class UserAddressFactory extends Factory
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
            'address' => fake()->address(),
            'comment' => fake()->sentence(random_int(1, 3)),
        ];
    }
}
