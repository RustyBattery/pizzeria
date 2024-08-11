<?php

namespace App\Http\Resources;

class ProductCollection extends BaseCollection
{
    public $collects = ProductShortResource::class;
    public string $items_name = 'products';
}
