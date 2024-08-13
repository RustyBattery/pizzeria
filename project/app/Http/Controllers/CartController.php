<?php

namespace App\Http\Controllers;

use App\Exceptions\Cart\CartProductLimitException;
use App\Exceptions\Cart\CartProductOutStockException;
use App\Exceptions\Cart\DuplicateCartProductException;
use App\Http\Requests\CartChangeCountRequest;
use App\Http\Resources\CartResource;
use App\Models\Product;
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

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        return response()->json(CartResource::make($this->cartService->getCart($user)));
    }

    /**
     * @param Request $request
     * @param Product $product
     * @return JsonResponse
     * @throws CartProductLimitException | CartProductOutStockException | DuplicateCartProductException
     */
    public function addProduct(Request $request, Product $product): JsonResponse
    {
        $user = $request->user();
        $this->cartService->addProductToCart($user, $product);
        return response()->json(['message' => 'Success']);
    }

    /**
     * @param Request $request
     * @param Product $product
     * @return JsonResponse
     */
    public function removeProduct(Request $request, Product $product): JsonResponse
    {
        $user = $request->user();
        $this->cartService->removeProductFromCart($user, $product);
        return response()->json(['message' => 'Success']);
    }

    /**
     * @param CartChangeCountRequest $request
     * @param Product $product
     * @return JsonResponse
     * @throws CartProductLimitException
     * @throws CartProductOutStockException
     */
    public function changeCount(CartChangeCountRequest $request, Product $product): JsonResponse
    {
        $user = $request->user();
        $count = $request->validated()->count;
        $this->cartService->changeProductCountInCart($user, $product, $count);
        return response()->json(['message' => 'Success']);
    }
}
