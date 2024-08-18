<?php

namespace App\Http\Resources;

use App\Models\OrderProduct;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


/**
 * @property mixed $id
 * @property mixed $user_id
 * @property mixed $phone
 * @property mixed $email
 * @property mixed $address
 * @property mixed $delivery_time
 * @property mixed $status
 * @property mixed $products
 * @method getCostInRubles()
 */
class OrderShortResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'phone' => $this->phone,
            'email' => $this->email,
            'address' => $this->address,
            'delivery_time' => $this->delivery_time,
            'cost' => $this->getCostInRubles(),
            'status' => $this->status,
        ];
    }
}
