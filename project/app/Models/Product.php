<?php

namespace App\Models;

use App\DTO\Base\FilterDTO;
use App\DTO\Base\PaginationDTO;
use App\DTO\Base\SearchDTO;
use App\DTO\Base\SortDTO;
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
 * @method static getAdvanced(FilterDTO[] $filters, SearchDTO|null $search, SortDTO|null $sort, PaginationDTO|null $pagination)
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

    public function getPriceInRubles(): string
    {
        return bcdiv($this->price / 100, 1, 2);
    }
}
