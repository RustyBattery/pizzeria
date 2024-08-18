<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed $id
 * @property mixed $order_id
 * @property mixed $product_id
 * @property mixed $price
 * @property mixed $count
 */
class OrderProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id', 'product_id', 'price', 'count'
    ];
}
