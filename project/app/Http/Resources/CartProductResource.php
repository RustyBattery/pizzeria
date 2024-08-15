<?php

namespace App\Http\Resources;

use App\Models\CartItem;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @method static make(Product $product)
 * @method static collection(Collection|Product[] $products)
 * @property int $id
 * @property string $name
 * @property string $description
 * @property Category $category
 * @property bool $in_stock
 * @property CartItem $pivot
 * @property int $price
 * @property mixed $images
 */
class CartProductResource extends JsonResource
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
            'name' => $this->name,
            'description' => $this->description,
            'price' => bcdiv($this->price / 100, 1, 2),
            'category' => CategoryResource::make($this->category),
            'images' => ImageResource::collection($this->images),
            'in_stock' => (bool)$this->in_stock,
            'count' => $this->pivot->count,
            'cost' => $this->in_stock ? bcdiv(($this->price * $this->pivot->count) / 100, 1, 2) : 0
        ];
    }
}
