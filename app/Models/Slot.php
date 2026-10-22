<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Slot extends Model
{
    use HasFactory;
    use \App\Models\Concerns\BelongsToShop;

    protected $table = 'tbl_slots';
    protected $primaryKey = 'id';

    protected $fillable = [
        'shop_id',
        'day_name',
        'is_day_off',
        'staff_id',
    ];

      public function breaks()
    {
        // Use the correct model class name; assuming Break model is named Break (singular)
        return $this->hasMany(Breaks::class, 'slot_id', 'id');
    }

    
}
