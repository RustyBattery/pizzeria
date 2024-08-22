<?php

namespace App\Http\Controllers;

use App\Exceptions\ForbiddenException;
use App\Http\Requests\OrderCreateRequest;
use App\Http\Resources\OrderCollection;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\User;
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
     * @param Request $request
     * @param Order $order
     * @return JsonResponse
     * @throws ForbiddenException
     */
    public function get(Request $request, Order $order): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        if ($order->user_id !== $user->id) {
            throw new ForbiddenException();
        }
        return response()->json(OrderResource::make($order));
    }

    /**
     * @param OrderCreateRequest $request
     * @return JsonResponse
     */
    public function create(OrderCreateRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        $user->createOrder($request->validatedAsDto());
        return response()->json(['message' => 'Success']);
    }

    /**
     * @param Request $request
     * @param Order $order
     * @return JsonResponse
     * @throws ForbiddenException
     */
    public function cancel(Request $request, Order $order): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        if ($order->user_id !== $user->id || $order->status !== 'created') {
            throw new ForbiddenException();
        }
        $order->cancel();
        return response()->json(['message' => 'Success']);
    }
}
