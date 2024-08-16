<?php

namespace App\Http\Resources;

use App\DTO\CartDTO;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Database\Eloquent\Collection;

/**
 * @method static make(CartDTO $cart)
 * @property Collection|Product[] $products
 * @property int $cost
 * @property object[] $count_info
 */
class CartResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'products' => CartProductResource::collection($this->products),
            'cost' => number_format($this->cost, 2, '.', ''),
            'count_info' => $this->count_info,
        ];
    }
}
