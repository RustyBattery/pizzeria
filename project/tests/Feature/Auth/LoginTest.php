<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_success_login(): void
    {
        User::create(['name' => 'test', 'email' => 'test@gmail.com', 'phone' => '+7(999)999-99-99', 'password' => Hash::make('password123')]);
        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->post('/api/auth/login', ['email' => 'test@gmail.com', 'password' => 'password123']);
        $response->assertStatus(200);
        $response->assertJsonStructure(['access_token', 'refresh_token']);
    }

    public function test_login_with_invalid_email(): void
    {
        User::create(['name' => 'test', 'email' => 'test@gmail.com', 'phone' => '+7(999)999-99-99', 'password' => Hash::make('password123')]);
        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->post('/api/auth/login', ['email' => 'test2@gmail.com', 'password' => 'password123']);
        $response->assertStatus(422);
    }

    public function test_login_with_invalid_password(): void
    {
        User::create(['name' => 'test', 'email' => 'test@gmail.com', 'phone' => '+7(999)999-99-99', 'password' => Hash::make('password123')]);
        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->post('/api/auth/login', ['email' => 'test@gmail.com', 'password' => 'password']);
        $response->assertStatus(422);
    }
}
