<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Testimonial extends Model
{
    use SoftDeletes;
    use \App\Models\Concerns\BelongsToShop;

    protected $table = 'tbl_testimonial';

    protected $fillable = [
        'shop_id',
        'created_by',
        'client_name',
        'client_role',
        'review',
        'rating',
        'client_image',
        'sort_order',
        'status',
    ];

    protected $appends = ['image_url'];

    public function getReviewAttribute($value): string
    {
        return str_ireplace(
            ['&amp;#039;', '&amp;#39;', '&#039;', '&#39;', '&apos;'],
            "'",
            (string) $value
        );
    }

    public function getImageUrlAttribute(): string
    {
        if (empty($this->client_image)) {
            return asset('frontend/images/luxury/customer.jpg');
        }

        if (str_starts_with($this->client_image, 'frontend/')) {
            return asset($this->client_image);
        }

        return asset('storage/' . ltrim($this->client_image, '/'));
    }
}
