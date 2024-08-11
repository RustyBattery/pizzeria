<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'path',
    ];
}
