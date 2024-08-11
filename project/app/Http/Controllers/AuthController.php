<?php

namespace App\Http\Controllers;

use App\Exceptions\Auth\CreateTokenException;
use App\Exceptions\Auth\InvalidEmailException;
use App\Exceptions\Auth\InvalidPasswordException;
use App\Exceptions\Auth\RegisterException;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    private AuthService $authService;

    public function __construct()
    {
        $this->authService = new AuthService();
    }

    /**
     * @param RegisterRequest $request
     * @return JsonResponse
     * @throws CreateTokenException|RegisterException
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $data = $request->validated();
        return response()->json($this->authService->register($data));
    }

    /**
     * @param LoginRequest $request
     * @return JsonResponse
     * @throws InvalidPasswordException|InvalidEmailException|CreateTokenException
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $data = $request->validated();
        return response()->json($this->authService->login($data['email'], $data['password']));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());
        return response()->json(['success' => true]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws CreateTokenException
     */
    public function refresh(Request $request): JsonResponse
    {
        return response()->json($this->authService->refresh($request->user()));
    }
}
