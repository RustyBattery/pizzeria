<?php

namespace App\Models;

use App\DTO\Base\FilterDTO;
use App\DTO\Base\PaginationDTO;
use App\DTO\Base\SearchDTO;
use App\DTO\Base\SortDTO;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property string $description
 * @property int $limit
 * @property int $image_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Image $image
 * @property Product[] $products
 * @method static getAdvanced(FilterDTO[] $filters, SearchDTO|null $search, SortDTO|null $sort, PaginationDTO|null $pagination)
 */
class Category extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'name', 'description', 'limit', 'image_id'
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function image(): BelongsTo
    {
        return $this->belongsTo(Image::class);
    }
}
