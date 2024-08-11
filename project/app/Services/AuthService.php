<?php

namespace App\Services;

use App\Enums\TokenAbility;
use App\Exceptions\Auth\CreateTokenException;
use App\Exceptions\Auth\InvalidEmailException;
use App\Exceptions\Auth\InvalidPasswordException;
use App\Exceptions\Auth\RegisterException;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Exception;
use Illuminate\Support\Facades\Log;

class AuthService
{
    /**
     * @param array $data
     * @return array{access_token: string, refresh_token: string}
     * @throws RegisterException
     * @throws CreateTokenException
     */
    public function register(array $data): array
    {
        try {
            $data['password'] = Hash::make($data['password']);
            $user = User::create($data);
        } catch (Exception $e) {
            Log::error($e->getMessage(), ['exception' => $e]);
            throw new RegisterException();
        }
        return $this->getTokens($user);
    }

    /**
     * @param string $email
     * @param string $password
     * @return array{access_token: string, refresh_token: string}
     * @throws InvalidPasswordException
     * @throws InvalidEmailException
     * @throws CreateTokenException
     */
    public function login(string $email, string $password): array
    {
        $user = User::where('email', $email)->first();
        if (!$user) {
            throw new InvalidEmailException();
        }
        if (!Hash::check($password, $user->password)) {
            throw new InvalidPasswordException();
        }

        return $this->getTokens($user);
    }

    /**
     * @param User $user
     * @return void
     */
    public function logout(User $user): void
    {
        $user->tokens()->delete();
    }

    /**
     * @param User $user
     * @return array{access_token: string, refresh_token: string}
     * @throws CreateTokenException
     */
    public function refresh(User $user): array
    {
        $user->tokens()->delete();
        return $this->getTokens($user);
    }

    /**
     * @param User $user
     * @return array{access_token: string, refresh_token: string}
     * @throws CreateTokenException
     */
    private function getTokens(User $user): array
    {
        try {
            $accessToken = $user->createToken('access_token', [TokenAbility::ACCESS_API->value], Carbon::now()->addMinutes(config('sanctum.ac_expiration')));
            $refreshToken = $user->createToken('refresh_token', [TokenAbility::ISSUE_ACCESS_TOKEN->value], Carbon::now()->addMinutes(config('sanctum.rt_expiration')));
            return [
                'access_token' => $accessToken->plainTextToken,
                'refresh_token' => $refreshToken->plainTextToken
            ];
        } catch (Exception $e) {
            Log::error($e->getMessage(), ['exception' => $e]);
            throw new CreateTokenException();
        }
    }
}
