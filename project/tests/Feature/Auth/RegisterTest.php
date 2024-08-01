<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_success_register(): void
    {
        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->post('/api/auth/register', [
            'name' => $this->faker->firstName(),
            'email' => $this->faker->email(),
            'phone' => '+7(999)999-99-99',
            'password' => $this->faker->password(5, 10),
        ]);
        $response->assertStatus(200);
        $response->assertJsonStructure(['access_token', 'refresh_token']);
    }

    public function test_register_without_name(): void
    {
        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->post('/api/auth/register', [
            'email' => $this->faker->email(),
            'phone' => '+7(999)999-99-99',
            'password' => $this->faker->password(5, 10),
        ]);
        $response->assertStatus(422);
    }

    public function test_register_without_email(): void
    {
        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->post('/api/auth/register', [
            'name' => $this->faker->firstName(),
            'phone' => '+7(999)999-99-99',
            'password' => $this->faker->password(5, 10),
        ]);
        $response->assertStatus(422);
    }

    public function test_register_with_invalid_email(): void
    {
        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->post('/api/auth/register', [
            'name' => $this->faker->firstName(),
            'email' => $this->faker->slug(),
            'phone' => '+7(999)999-99-99',
            'password' => $this->faker->password(5, 10),
        ]);
        $response->assertStatus(422);
    }

    public function test_register_without_phone(): void
    {
        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->post('/api/auth/register', [
            'name' => $this->faker->firstName(),
            'email' => $this->faker->email(),
            'password' => $this->faker->password(5, 10),
        ]);
        $response->assertStatus(422);
    }

    public function test_register_with_invalid_phone(): void
    {
        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->post('/api/auth/register', [
            'name' => $this->faker->firstName(),
            'email' => $this->faker->email(),
            'phone' => '+79999999999',
            'password' => $this->faker->password(5, 10),
        ]);
        $response->assertStatus(422);
    }

    public function test_register_without_password(): void
    {
        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->post('/api/auth/register', [
            'name' => $this->faker->firstName(),
            'email' => $this->faker->email(),
            'phone' => '+7(999)999-99-99',
        ]);
        $response->assertStatus(422);
    }

    public function test_register_with_password_password_shorter_min_length(): void
    {
        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->post('/api/auth/register', [
            'name' => $this->faker->firstName(),
            'email' => $this->faker->email(),
            'phone' => '+7(999)999-99-99',
            'password' => $this->faker->password(1, 4),
        ]);
        $response->assertStatus(422);
    }
}
