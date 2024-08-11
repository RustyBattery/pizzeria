<?php

namespace App\Http\Resources;

class CategoryCollection extends BaseCollection
{
    public $collects = CategoryResource::class;
    public string $items_name = 'categories';
}
