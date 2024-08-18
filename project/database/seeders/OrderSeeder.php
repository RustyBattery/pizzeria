<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\UserAddress;
use Exception;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * @throws Exception
     */
    public function run(): void
    {
        $users = User::where('is_admin', false)->get();
        $products = Product::all();

        foreach ($users as $user) {
            UserAddress::factory(random_int(1, 2))->create(['user_id' => $user->id]);
            /** @var UserAddress $address */
            $address = UserAddress::factory()->create(['user_id' => $user->id, 'is_default' => true]);
            Order::factory(random_int(1, 5))
                ->hasAttached($products->random(random_int(1, 5)), [
                    'price' => random_int(100, 1000) * 100,
                    'count' => random_int(1, 5)
                ])->create([
                    'user_id' => $user->id,
                    'address_id' => $address->id,
                ]);
        }
    }
}
