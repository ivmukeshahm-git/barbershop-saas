<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use HasFactory, SoftDeletes;
    use \App\Models\Concerns\BelongsToShop;

    protected $table = 'tbl_service';
    protected $primaryKey = 'id';

    protected $fillable = [
        'shop_id',
        'created_by',
        'category_id',
        'service_name',
        'price',
        'duration',
        'commission',
        'status',
        'tip_status',
        'service_img',
        'description',
        'home_section',
    ];

    public function getImageUrlAttribute(): string
    {
        $placeholder = asset('frontend/images/luxury/cut.jpg');
        $img = trim((string) $this->service_img);

        if ($img === '') {
            return $placeholder;
        }

        if (preg_match('#^(https?:)?//#i', $img)) {
            return $img;
        }

        $img = ltrim(str_replace('\\', '/', $img), '/');
        $img = preg_replace('#^(public/|storage/)#', '', $img);

        $publicFile = public_path('storage/' . $img);
        $version = is_file($publicFile) ? filemtime($publicFile) : time();
        $base = rtrim(request()->getBasePath(), '/');

        return $base . '/storage/' . $img . '?v=' . $version;
    }
}
