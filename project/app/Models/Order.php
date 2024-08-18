<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $id
 * @property int $user_id
 * @property int $address_id
 * @property string $phone
 * @property int $cost
 * @property string $email
 * @property string $delivery_time
 * @property string $status
 * @property string $created_at
 * @property string $updated_at
 * @property User $user
 * @property UserAddress $address
 * @property Collection|Product[] $products
 */
class Order extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'address_id', 'phone', 'cost',
        'email', 'delivery_time', 'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(UserAddress::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'order_products')
            ->withPivot(['count', 'price'])
            ->withTimestamps();
    }

    public function getCostInRubles(): string
    {
        return bcdiv($this->cost / 100, 1, 2);
    }

    public function cancel(): void
    {
        $this->status = 'canceled';
        $this->save();
    }
}
