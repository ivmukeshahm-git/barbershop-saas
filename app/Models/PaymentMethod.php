<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentMethod extends Model
{
    use HasFactory, SoftDeletes;
    use \App\Models\Concerns\BelongsToShop;

    // Table name
    protected $table = 'tbl_payment_method';

    // Mass assignable fields
    protected $fillable = [
        'shop_id',
        'name',
    ];

    // Optional: If you want to hide deleted_at in JSON responses
    protected $dates = ['deleted_at'];
}
