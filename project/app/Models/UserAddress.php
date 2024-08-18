<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
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

    private function resetDefaultAddress(): void
    {
        $user = $this->user;
        $user->addresses()->where('is_default', true)->update(['is_default' => false]);
    }

    protected static function boot(): void
    {
        parent::boot();
        static::creating(static function (UserAddress $address) {
            if ($address->is_default) {
                $address->resetDefaultAddress();
            }
        });
        static::updating(static function (UserAddress $address) {
            if ($address->is_default) {
                $address->resetDefaultAddress();
            }
        });
    }
}
