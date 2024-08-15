<?php

namespace App\Http\Resources;

use App\Models\Category;
use App\Models\Image;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @method static make(Product $product)
 * @method static collection(Collection|Product[] $products)
 * @method getPriceInRubles()
 * @property int $id
 * @property string $name
 * @property string $description
 * @property int $price
 * @property Category $category
 * @property Image[] $images
 * @property bool $in_stock
 */
class ProductShortResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->getPriceInRubles(),
            'category' => CategoryResource::make($this->category),
            'images' => ImageResource::collection($this->images),
            'in_stock' => (bool)$this->in_stock,
        ];
    }
}
