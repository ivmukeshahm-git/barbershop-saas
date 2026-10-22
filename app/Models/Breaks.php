<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Breaks extends Model
{
    use HasFactory;
    use \App\Models\Concerns\BelongsToShop;

    protected $table = 'tbl_breaks';
    protected $primaryKey = 'id';

    protected $fillable = [
        'shop_id',
        'slot_id',       // foreign key to slots
        'break_start',   // start time of break
        'break_end',     // end time of break
        'break_date',
        'staff_id',
        'break_type'
    ];

    public function slot()
    {
        return $this->belongsTo(Slot::class, 'slot_id', 'id');
    }
}
