<?php

namespace App\Http\Controllers;

use App\Exceptions\Cart\CartEmptyException;
use App\Http\Requests\OrderCreateRequest;
use App\Http\Resources\OrderCollection;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        return response()->json(new OrderCollection($user->orders));
    }

    /**
     * @param Order $order
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function get(Order $order): JsonResponse
    {
        $this->authorize('get', $order);
        return response()->json(OrderResource::make($order));
    }

    /**
     * @param OrderCreateRequest $request
     * @return JsonResponse
     * @throws CartEmptyException
     */
    public function create(OrderCreateRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        $user->createOrder($request->validatedAsDto());
        return response()->json(['message' => 'Success']);
    }

    /**
     * @param Order $order
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function cancel(Order $order): JsonResponse
    {
        $this->authorize('cancel', $order);
        $order->cancel();
        return response()->json(['message' => 'Success']);
    }
}
