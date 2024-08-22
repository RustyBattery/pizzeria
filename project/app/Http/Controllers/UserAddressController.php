<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserAddressRequest;
use App\Http\Resources\UserAddressResource;
use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserAddressController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        return response()->json(UserAddressResource::collection($user->addresses));
    }

    /**
     * @param UserAddressRequest $request
     * @return JsonResponse
     */
    public function create(UserAddressRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        $user->addresses()->create($request->validated());
        return response()->json(['message' => 'Success']);
    }

    /**
     * @param UserAddressRequest $request
     * @param UserAddress $address
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(UserAddressRequest $request, UserAddress $address): JsonResponse
    {
        $this->authorize('update', $address);
        $address->update($request->validated());
        return response()->json(['message' => 'Success']);
    }

    /**
     * @param UserAddress $address
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function delete(UserAddress $address): JsonResponse
    {
        $this->authorize('delete', $address);
        $address->delete();
        return response()->json(['message' => 'Success']);
    }
}
