<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'category' => CategoryResource::make($this->category),
            'images' => ImageResource::collection($this->images),
            'in_stock' => (bool)$this->in_stock,
            'info' => ProductInfoResource::collection($this->infos),
        ];
    }
}
