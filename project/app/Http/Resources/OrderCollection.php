<?php

namespace App\Http\Resources;

class OrderCollection extends BaseCollection
{
    public $collects = OrderShortResource::class;
    public string $items_name = 'orders';
}
