<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    use \App\Models\Concerns\BelongsToShop;

    protected $fillable = [
        'shop_id',
        'image'
    ];
}
