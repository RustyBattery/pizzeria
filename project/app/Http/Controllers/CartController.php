<?php

namespace App\Http\Controllers;

use App\Exceptions\Cart\CartProductLimitException;
use App\Exceptions\Cart\CartProductOutStockException;
use App\Exceptions\Cart\DuplicateCartProductException;
use App\Http\Requests\CartChangeCountRequest;
use App\Http\Resources\CartResource;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        return response()->json(CartResource::make($user->getCart()));
    }

    /**
     * @param Request $request
     * @param Product $product
     * @return JsonResponse
     * @throws CartProductLimitException | CartProductOutStockException | DuplicateCartProductException
     */
    public function addProduct(Request $request, Product $product): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        $user->addProductToCart($product);
        return response()->json(['message' => 'Success']);
    }

    /**
     * @param Request $request
     * @param Product $product
     * @return JsonResponse
     */
    public function removeProduct(Request $request, Product $product): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        $user->removeProductFromCart($product);
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
        /** @var User $user */
        $user = $request->user();
        $count = $request->validatedAsObject()->count;
        $user->changeProductCountInCart($product, $count);
        return response()->json(['message' => 'Success']);
    }
}
