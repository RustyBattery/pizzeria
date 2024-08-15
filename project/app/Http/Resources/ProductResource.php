<?php

namespace App\Http\Resources;

use App\Models\Category;
use App\Models\Image;
use App\Models\Product;
use App\Models\ProductInfo;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @method static make(Product $product)
 * @method static collection(Collection|Product[] $products)
 * @property int $id
 * @property string $name
 * @property string $description
 * @property int $price
 * @property Category $category
 * @property Image[] $images
 * @property bool $in_stock
 * @property ProductInfo[] $infos
 */
class ProductResource extends JsonResource
{
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
            'info' => ProductInfoResource::collection($this->infos),
        ];
    }
}
