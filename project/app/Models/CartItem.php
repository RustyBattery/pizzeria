<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $user_id
 * @property int $product_id
 * @property int $count
 * @property Product $product
 * @property User $user
 * @property int $id
 */
class CartItem extends Model
{
    use HasFactory;

    protected $table = 'carts';

    protected $fillable = [
        'user_id', 'product_id', 'count'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getCost(): int|string
    {
        return $this->product->in_stock ? bcdiv(($this->product->price * $this->count) / 100, 1, 2) : 0;
    }
}
