<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopClosure extends Model
{
    use \App\Models\Concerns\BelongsToShop;

    protected $table = 'tbl_shop_closure';

    protected $fillable = [
        'shop_id',
        'created_by',
        'date',
        'closure_status',
        'start_time',
        'end_time',
        'reason',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    /**
     * Optionally, define relationship if you have a User model for created_by
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    
}
