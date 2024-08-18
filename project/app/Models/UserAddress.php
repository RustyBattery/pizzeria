<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property string $address
 * @property string|null $comment
 * @property bool $is_default
 * @property User $user
 */
class UserAddress extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'address', 'comment', 'is_default'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
