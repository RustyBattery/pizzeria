<?php

namespace App\Http\Resources;

use App\Models\UserAddress;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @method static make(UserAddress $address)
 * @method static collection(Collection|UserAddress[] $addresses)
 * @property int $id
 * @property string $address
 * @property string|null $comment
 * @property bool $is_default
 */
class UserAddressResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'address' => $this->address,
            'comment' => $this->comment,
            'is_default' => $this->is_default,
        ];
    }
}
