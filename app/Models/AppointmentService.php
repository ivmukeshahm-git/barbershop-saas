<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppointmentService extends Model
{
    use HasFactory;
    use \App\Models\Concerns\BelongsToShop;

    protected $table = 'tbl_appointment_service';
    protected $fillable = [
        'shop_id',
        'appointment_id',
        'service_id',
        'staff_id',
        'requested_staff_id',
        'is_first_available',
        'product_commission',
        'service_commission',
        'service_tip',
        'total_amt',
        'status',
        'comment',
    ];

    // Belongs to Appointment
    public function appointment()
    {
        return $this->belongsTo(Appointment::class, 'appointment_id', 'appointment_id');
    }

    // Belongs to Staff (assuming users table)
    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id', 'id');
    }

    public function requestedStaff()
    {
        return $this->belongsTo(User::class, 'requested_staff_id', 'id');
    }

    // Belongs to Service
    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id', 'id');
    }

}
