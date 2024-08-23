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

class GetOrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_success_get_order(): void
    {
        $order = $this->prepareTestData();
        $user = $order->user;
        $token = $this->getBearerAccessToken($user);

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/order/' . $order->id);

        $response->assertStatus(200);
    }

    public function test_get_order_unauthenticated(): void
    {
        $order = $this->prepareTestData();

        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->get('/api/order/' . $order->id);

        $response->assertStatus(401);
    }

    public function test_get_nonexistent_order(): void
    {
        $order = $this->prepareTestData();
        $user = $order->user;
        $token = $this->getBearerAccessToken($user);

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/order/' . ($order->id + 1000));

        $response->assertStatus(404);
    }

    public function test_get_order_forbidden(): void
    {
        $order = $this->prepareTestData();
        $user = User::factory()->create();
        $token = $this->getBearerAccessToken($user);

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/order/' . $order->id);

        $response->assertStatus(403);
    }

    private function prepareTestData()
    {
        $user = User::factory()->has(UserAddress::factory(), 'addresses')->create();

        $categories = [
            Category::create(['name' => 'Pizza', 'limit' => 10]),
            Category::create(['name' => 'Drink', 'limit' => 20])
        ];
        foreach ($categories as $category) {
            Product::factory(random_int(15, 20))->for($category)->has(ProductInfo::factory(3), 'infos')->has(Image::factory(3), 'images')->create();
        }

        $products = Product::all();
        $order = Order::factory()
            ->hasAttached($products->random(random_int(1, 5)), [
                'price' => random_int(100, 1000) * 100,
                'count' => random_int(1, 5)
            ])->create([
                'user_id' => $user->id,
                'address_id' => $user->addresses()->first()->id,
            ]);

        return $order;
    }

    private function getBearerAccessToken(User $user): string
    {
        $authService = new AuthService();
        return $authService->login($user->email, 'password')->access_token;
    }
}
