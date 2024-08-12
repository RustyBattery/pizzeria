<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $name
 * @property string $description
 * @property string $price
 * @property bool $in_stock
 * @property int $category_id
 * @property Category $category
 * @property Image[] $images
 * @property ProductInfo[] $infos
 */
class Product extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'name', 'description', 'price', 'in_stock', 'category_id'
    ];

    public function images(): BelongsToMany
    {
        return $this->belongsToMany(Image::class, 'product_images')->withPivot('is_main');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function infos(): HasMany
    {
        return $this->hasMany(ProductInfo::class);
    }
}
