<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;
    use \App\Models\Concerns\BelongsToShop;

    protected $table = "customers";

    protected $primaryKey = "customer_id";

    protected $fillable = [
        'shop_id',
        'customer_name',
        'customer_phone',
        'customer_email',
        'customer_status',
    ];

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'customer_id', 'customer_id');
    }

    public function invoicedAppointments()
    {
        return $this->hasMany(Appointment::class, 'customer_id', 'customer_id')
            ->where('is_invoiced', 1);
    }
}
