<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductInfo extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'product_id', 'name', 'value'
    ];
}
