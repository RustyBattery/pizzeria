<?php

namespace Tests\Feature\Auth;

use App\Enums\TokenAbility;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;
    public function test_success_logout(): void
    {
        $user = User::create(['name' => 'test', 'email' => 'test@gmail.com', 'phone' => '+7(999)999-99-99', 'password' => Hash::make('password123')]);
        $tokens = $this->generateValidTokens($user);
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $tokens['access'],
        ])->post('/api/auth/logout');
        $response->assertStatus(200);
        $response->assertJsonStructure(['success']);
    }

    public function test_logout_with_invalid_token(): void
    {
        $user = User::create(['name' => 'test', 'email' => 'test@gmail.com', 'phone' => '+7(999)999-99-99', 'password' => Hash::make('password123')]);
        $tokens = $this->generateValidTokens($user);
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $tokens['refresh'],
        ])->post('/api/auth/logout');
        $response->assertStatus(401);
    }

    public function test_logout_without_token(): void
    {
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => '',
        ])->post('/api/auth/logout');
        $response->assertStatus(401);
    }

    public function test_logout_with_expired_token(): void
    {
        $user = User::create(['name' => 'test', 'email' => 'test@gmail.com', 'phone' => '+7(999)999-99-99', 'password' => Hash::make('password123')]);
        $tokens = $this->generateValidTokens($user, 1);
        sleep(2);
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $tokens['access'],
        ])->post('/api/auth/logout');
        $response->assertStatus(401);
    }

    private function generateValidTokens(User $user, int $access_expires_time = 60, int $refresh_expires_time = 60): array
    {
        $accessToken = $user->createToken('access_token', [TokenAbility::ACCESS_API->value], Carbon::now()->addSeconds($access_expires_time));
        $refreshToken = $user->createToken('refresh_token', [TokenAbility::ISSUE_ACCESS_TOKEN->value], Carbon::now()->addSeconds($refresh_expires_time));
        return [
            'access' => $accessToken->plainTextToken,
            'refresh' => $refreshToken->plainTextToken
        ];
    }
}
