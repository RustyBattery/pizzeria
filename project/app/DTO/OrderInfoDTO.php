<?php

namespace App\DTO;

use Illuminate\Database\Eloquent\Collection;
use App\Models\Product;
use App\Models\User;

readonly class OrderInfoDTO
{

    public function __construct(
        public ?string $delivery_time,
        public string  $email,
        public string  $phone,
        public int     $address_id,
    )
    {
    }
}
