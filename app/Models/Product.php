<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use HasFactory, SoftDeletes;
    use \App\Models\Concerns\BelongsToShop;

    protected $table = 'tbl_product';        
    protected $primaryKey = 'id';  

    protected $fillable = [
        'shop_id',
        'created_by',
        'category_id',
        'product_name',
        'price',
        'stock',
        'commission',
        'status',
        'product_img',
        'description',
    ];

    public function getImageUrlAttribute(): string
    {
        return static::imageUrlForPath($this->product_img);
    }

    public static function imageUrlForPath(?string $imagePath): string
    {
        $path = str_replace('\\', '/', trim((string) $imagePath));
        if ($path === '') {
            return asset('assets/img/user.svg');
        }

        if (preg_match('#^(https?:)?//#i', $path)) {
            return $path;
        }

        $productImagesPosition = stripos($path, 'product_images/');
        if ($productImagesPosition !== false) {
            $path = substr($path, $productImagesPosition);
        } else {
            $path = preg_replace('#^(?:.*?/)?(?:public/)?storage/#i', '', $path);
        }

        return Storage::disk('public')->url(ltrim($path, '/'));
    }
}
