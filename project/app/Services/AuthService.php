<?php

namespace App\Services;

use App\Enums\TokenAbility;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Mockery\Exception;

class AuthService
{
    public function register(array $data): array
    {
        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);
        return $this->getTokens($user);
    }

    public function login(string $email, string $password): array
    {
        $user = User::where('email', $email)->first();
        if (!Hash::check($password, $user->password)) {
            throw new Exception('Invalid password', 422);
        }

        return $this->getTokens($user);
    }

    public function logout(User $user): void
    {
        $user->tokens()->delete();
    }

    public function refresh(User $user): array
    {
        $user->tokens()->delete();
        return $this->getTokens($user);
    }

    private function getTokens($user): array
    {
        $accessToken = $user->createToken('access_token', [TokenAbility::ACCESS_API->value], Carbon::now()->addMinutes(config('sanctum.ac_expiration')));
        $refreshToken = $user->createToken('refresh_token', [TokenAbility::ISSUE_ACCESS_TOKEN->value], Carbon::now()->addMinutes(config('sanctum.rt_expiration')));
        return [
            'access_token' => $accessToken->plainTextToken,
            'refresh_token' => $refreshToken->plainTextToken
        ];
    }
}
