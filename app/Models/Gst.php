<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gst extends Model
{
    use HasFactory;
    use \App\Models\Concerns\BelongsToShop;

    protected $table = 'tbl_gst';        
    protected $primaryKey = 'id';  

    protected $fillable = [
        'shop_id',
        'created_by',
        'name',
        'rate',
        'tax_type',
        'default_status',
    ];

    public function isTaxExcluded(): bool
    {
        return strtolower((string) $this->tax_type) === 'exclude';
    }

    public function formattedRate(): string
    {
        return rtrim(rtrim(number_format((float) $this->rate, 2), '0'), '.');
    }

    public function typeLabel(): string
    {
        return $this->isTaxExcluded() ? 'excluded' : 'included';
    }

    public function websiteNote(): string
    {
        if ($this->isTaxExcluded()) {
            return 'All service prices exclude GST. Applicable GST will be added to the final bill at checkout.';
        }

        return 'All service prices include GST. No additional GST will be charged at checkout.';
    }
}
