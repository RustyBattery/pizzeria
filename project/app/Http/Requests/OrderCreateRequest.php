<?php

namespace App\Http\Requests;

use App\DTO\OrderInfoDTO;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class OrderCreateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'delivery_time' => 'nullable|date|after:now',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|regex:/^\+7\(\d{3}\)\d{3}-\d{2}-\d{2}$/',
            'address_id' => 'required|exists:user_addresses,id',
        ];
    }

    /**
     * @return OrderInfoDTO
     */
    public function validatedAsDto(): OrderInfoDTO
    {
        $data = $this->validated();
        /** @var User $user */
        $user = $this->user();
        return new OrderInfoDTO(
            $data['delivery_time'] ?? null,
            $data['email'] ?? $user->email,
            $data['phone'] ?? $user->phone,
            $data['address_id']
        );
    }
}
