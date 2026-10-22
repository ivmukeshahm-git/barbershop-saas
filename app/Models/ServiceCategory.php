<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceCategory extends Model
{
    use HasFactory, SoftDeletes;
    use \App\Models\Concerns\BelongsToShop;

    protected $table = 'tbl_service_category';
    protected $primaryKey = 'id';

    protected $fillable = [
        'shop_id',
        'created_by',
        'category_name',
        'service_image',
        'description'
    ];

    public function services()
    {

        return $this->hasMany(Service::class, 'category_id', 'id');
    }
}
