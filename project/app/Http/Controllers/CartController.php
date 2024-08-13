<?php

namespace App\Http\Controllers;

use App\Http\Resources\CartResource;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    private CartService $cartService;

    public function __construct()
    {
        $this->cartService = new CartService();
    }

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        return response()->json(CartResource::make($this->cartService->getCart($user)));
    }
}
