<?php

namespace Tests\Feature\Order;

use App\Models\Category;
use App\Models\Image;
use App\Models\Product;
use App\Models\ProductInfo;
use App\Models\User;
use App\Models\UserAddress;
use App\Services\AuthService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CreateOrderTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_success_create_order(): void
    {
        $user = $this->prepareTestData();
        $token = $this->getBearerAccessToken($user);

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token,
        ])->post('/api/order', [
            'email' => $this->faker->email(),
            'phone' => '+7(999)999-99-99',
            'delivery_time' => Carbon::now()->addDay()->format('Y-m-d H:i:s'),
            'address_id' => $user->addresses()->first()->id,
        ]);

        $response->assertStatus(200);
    }

    public function test_create_order_with_empty_cart(): void
    {
        $user = User::factory()->has(UserAddress::factory(), 'addresses')->create();
        $token = $this->getBearerAccessToken($user);

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token,
        ])->post('/api/order', [
            'email' => $this->faker->email(),
            'phone' => '+7(999)999-99-99',
            'delivery_time' => Carbon::now()->addDay()->format('Y-m-d H:i:s'),
            'address_id' => $user->addresses()->first()->id,
        ]);

        $response->assertStatus(400);
    }

    public function test_create_order_unauthenticated(): void
    {
        $user = User::factory()->has(UserAddress::factory(), 'addresses')->create();

        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->post('/api/order', [
            'email' => $this->faker->email(),
            'phone' => '+7(999)999-99-99',
            'delivery_time' => Carbon::now()->addDay()->format('Y-m-d H:i:s'),
            'address_id' => $user->addresses()->first()->id,
        ]);

        $response->assertStatus(401);
    }

    public function test_create_order_without_address():void
    {
        $user = $this->prepareTestData();
        $token = $this->getBearerAccessToken($user);

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token,
        ])->post('/api/order', [
            'email' => $this->faker->email(),
            'phone' => '+7(999)999-99-99',
            'delivery_time' => Carbon::now()->addDay()->format('Y-m-d H:i:s'),
        ]);

        $response->assertStatus(422);
    }

    public function test_create_order_with_invalid_delivery_time():void
    {
        $user = $this->prepareTestData();
        $token = $this->getBearerAccessToken($user);

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token,
        ])->post('/api/order', [
            'email' => $this->faker->email(),
            'phone' => '+7(999)999-99-99',
            'delivery_time' => 'May 10, 11:30 a.m.',
            'address_id' => $user->addresses()->first()->id,
        ]);

        $response->assertStatus(422);
    }

    public function test_create_order_with_invalid_address():void
    {
        $user = $this->prepareTestData();
        $token = $this->getBearerAccessToken($user);

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token,
        ])->post('/api/order', [
            'email' => $this->faker->email(),
            'phone' => '+7(999)999-99-99',
            'delivery_time' => Carbon::now()->addDay()->format('Y-m-d H:i:s'),
            'address_id' => $user->addresses()->first()->id + 1000,
        ]);

        $response->assertStatus(422);
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

        $pizzas = Product::query()->whereHas('category', function ($query) {
            $query->where('name', 'Pizza');
        })->pluck('id')->random(random_int(3, 5));

        $drinks = Product::query()->whereHas('category', function ($query) {
            $query->where('name', 'Drink');
        })->pluck('id')->random(random_int(3, 5));

        foreach ($pizzas as $pizza) {
            $user->cart()->attach($pizza, ['count' => random_int(1, 2)]);
        }
        foreach ($drinks as $drink) {
            $user->cart()->attach($drink, ['count' => random_int(2, 4)]);
        }

        return $user;
    }

    private function getBearerAccessToken(User $user): string
    {
        $authService = new AuthService();
        return $authService->login($user->email, 'password')->access_token;
    }
}
