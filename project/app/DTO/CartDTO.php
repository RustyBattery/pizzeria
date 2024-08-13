<?php

namespace App\DTO;

use Illuminate\Database\Eloquent\Collection;
use App\Models\Product;
use App\Models\User;

class CartDTO
{
    /**
     * @param User $user
     * @param Product[] $products
     * @param int $cost
     * @param object{category: object{id: int, name: string, limit: int}, in_stock: int, out_stock: int, total: int}[] $count_info
     */
    public function __construct(
        public readonly User             $user,
        public readonly array|Collection $products,
        public readonly int              $cost,
        public readonly array            $count_info
    )
    {
    }

}
