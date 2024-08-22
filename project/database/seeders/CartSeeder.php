<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use Exception;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CartSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * @throws Exception
     */
    public function run(): void
    {
        $users = User::where('is_admin', false)->get();

        $pizzas = Product::query()->whereHas('category', function ($query) {
            $query->where('name', 'Pizza');
        })->pluck('id');

        $drinks = Product::query()->whereHas('category', function ($query) {
            $query->where('name', 'Drink');
        })->pluck('id');

        foreach ($users as $user) {
            $userPizzas = $pizzas->random(random_int(3, 5));
            $userDrinks = $drinks->random(random_int(3, 5));
            foreach ($userPizzas as $pizza) {
                $user->cart()->attach($pizza, ['count' => random_int(1, 2)]);
            }
            foreach ($userDrinks as $drink) {
                $user->cart()->attach($drink, ['count' => random_int(2, 4)]);
            }
        }
    }
}
