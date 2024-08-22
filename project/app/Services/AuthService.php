<?php

namespace App\Services;

use App\DTO\Auth\TokensDTO;
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
     * @param array{name:string, email:string, phone:string, password:string} $data
     * @return TokensDTO
     * @throws RegisterException
     * @throws CreateTokenException
     */
    public function register(array $data): TokensDTO
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
     * @return TokensDTO
     * @throws InvalidPasswordException
     * @throws InvalidEmailException
     * @throws CreateTokenException
     */
    public function login(string $email, string $password): TokensDTO
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
     * @return TokensDTO
     * @throws CreateTokenException
     */
    public function refresh(User $user): TokensDTO
    {
        $user->tokens()->delete();
        return $this->getTokens($user);
    }

    /**
     * @param User $user
     * @return TokensDTO
     * @throws CreateTokenException
     */
    private function getTokens(User $user): TokensDTO
    {
        try {
            $accessToken = $user->createToken('access_token', [TokenAbility::ACCESS_API->value], Carbon::now()->addMinutes(config('sanctum.ac_expiration')));
            $refreshToken = $user->createToken('refresh_token', [TokenAbility::ISSUE_ACCESS_TOKEN->value], Carbon::now()->addMinutes(config('sanctum.rt_expiration')));
            return TokensDTO::fromArray([
                'access' => $accessToken->plainTextToken,
                'refresh' => $refreshToken->plainTextToken
            ]);
        } catch (Exception $e) {
            Log::error($e->getMessage(), ['exception' => $e]);
            throw new CreateTokenException();
        }
    }
}
