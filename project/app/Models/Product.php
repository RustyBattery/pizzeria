<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'description', 'in_stock', 'category_id'
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
