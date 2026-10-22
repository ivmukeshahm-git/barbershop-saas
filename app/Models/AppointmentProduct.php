<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppointmentProduct extends Model
{
    use HasFactory;
    use \App\Models\Concerns\BelongsToShop;

    protected $table = 'tbl_appointment_product';
    protected $fillable = [
        'shop_id',
        'appointment_id',
        'staff_id',
        'product_id',
        'qty',
        'amount',
        'product_commision',
    ];

    // Belongs to Appointment
    public function appointment()
    {
        return $this->belongsTo(Appointment::class, 'appointment_id', 'appointment_id');
    }

    // Belongs to Staff
    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id', 'id');
    }

    // Belongs to Product
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
}
