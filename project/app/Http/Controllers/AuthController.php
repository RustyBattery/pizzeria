<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    private AuthService $authService;

    public function __construct()
    {
        $this->authService = new AuthService();
    }

    public function register(RegisterRequest $request)
    {
        try {
            $response = $this->authService->register($request->validated());
        } catch (\Exception $e) {
            return response(['message' => $e->getMessage()], $e->getCode() >= 200 && $e->getCode() <= 500 ? $e->getCode() : 400);
        }
        return response($response, 200);
    }

    public function login(LoginRequest $request)
    {
        try {
            $data = $request->validated();
            $response = $this->authService->login($data['email'], $data['password']);
        } catch (\Exception $e) {
            return response(['message' => $e->getMessage()], $e->getCode() >= 200 && $e->getCode() <= 500 ? $e->getCode() : 400);
        }
        return response($response, 200);
    }

    public function logout(Request $request)
    {
        try {
            $this->authService->logout($request->user());
        } catch (\Exception $e) {
            return response(['message' => $e->getMessage()], $e->getCode() >= 200 && $e->getCode() <= 500 ? $e->getCode() : 400);
        }
        return response(['success' => true], 200);
    }

    public function refresh(Request $request)
    {
        try {
            $response = $this->authService->refresh($request->user());
        } catch (\Exception $e) {
            return response(['message' => $e->getMessage()], $e->getCode() >= 200 && $e->getCode() <= 500 ? $e->getCode() : 400);
        }
        return response($response, 200);
    }
}
