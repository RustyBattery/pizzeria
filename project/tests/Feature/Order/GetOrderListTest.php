<?php

namespace Tests\Feature\Order;

use App\Models\Category;
use App\Models\Image;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductInfo;
use App\Models\User;
use App\Models\UserAddress;
use App\Services\AuthService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetOrderListTest extends TestCase
{
    use RefreshDatabase;

    public function test_success_get_orders(): void
    {
        $user = $this->prepareTestData();
        $token = $this->getBearerAccessToken($user);

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/order');

        $response->assertStatus(200);
        $response->assertJsonStructure(['orders', 'count']);
    }

    public function test_get_orders_unauthenticated(): void
    {
        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->get('/api/order');

        $response->assertStatus(401);
    }

    public function test_get_orders_empty_list(): void
    {
        $user = $this->prepareTestData(false);
        $token = $this->getBearerAccessToken($user);

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/order');

        $response->assertStatus(200);
        $response->assertJsonStructure(['orders', 'count']);
    }

    private function prepareTestData(bool $shouldCreateOrders = true)
    {
        $user = User::factory()->has(UserAddress::factory(), 'addresses')->create();

        if (!$shouldCreateOrders) {
            return $user;
        }

        $categories = [
            Category::create(['name' => 'Pizza', 'limit' => 10]),
            Category::create(['name' => 'Drink', 'limit' => 20])
        ];
        foreach ($categories as $category) {
            Product::factory(random_int(15, 20))->for($category)->has(ProductInfo::factory(3), 'infos')->has(Image::factory(3), 'images')->create();
        }

        $products = Product::all();
        Order::factory(random_int(1, 5))
            ->hasAttached($products->random(random_int(1, 5)), [
                'price' => random_int(100, 1000) * 100,
                'count' => random_int(1, 5)
            ])->create([
                'user_id' => $user->id,
                'address_id' => $user->addresses()->first()->id,
            ]);

        return $user;
    }

    private function getBearerAccessToken(User $user): string
    {
        $authService = new AuthService();
        return $authService->login($user->email, 'password')->access_token;
    }
}
