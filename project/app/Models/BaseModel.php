<?php

namespace App\Models;

use App\Builders\BaseBuilder;
use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    /**
     * @param $query
     * @return BaseBuilder
     */
    public function newEloquentBuilder($query): BaseBuilder
    {
        return new BaseBuilder($query);
    }
}
