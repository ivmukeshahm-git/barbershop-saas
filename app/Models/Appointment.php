<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;

class Appointment extends Model
{
    use HasFactory;
    use \App\Models\Concerns\BelongsToShop;

    protected $table = 'tbl_appointment';
    protected $primaryKey = 'appointment_id';
    protected $fillable = [
        'shop_id',
        'booking_code',
        'date',
        'slot_start',
        'slot_end',
        'name',
        'email',
        'phone',
        'notes',
        'status',
        'comment',
        'customer_id',
        'payment_method',
        'is_invoiced',
        'invoice_number',
        'total_amt',
        'total_discount',
        'total_tax',
        'tax_type',
        'tax_rate',
        'total_tip',
        'discount_type',
        'service_discount',
        'product_discount',
        'inventory_deducted_at',
    ];

    protected $appends = ['staff_names'];

    protected static function booted()
    {
        static::creating(function (Appointment $appointment) {
            if (empty($appointment->booking_code)) {
                $appointment->booking_code = static::generateUniqueBookingCode();
            }
        });
    }

    /**
     * Generate a non-sequential tracking code.
     */
    public static function generateUniqueBookingCode(): string
    {
        for ($attempt = 0; $attempt < 25; $attempt++) {
            $prefix = Schema::hasColumn('tbl_setting', 'booking_code_prefix')
                ? Setting::forCurrentShop()?->booking_code_prefix
                : 'MHS';
            $prefix = strtoupper(trim((string) ($prefix ?: 'MHS')));
            if (!preg_match('/^[A-Z0-9]{2,10}$/', $prefix)) {
                throw new \RuntimeException('The configured booking-code prefix is invalid.');
            }

            $code = $prefix . '-' . strtoupper(Str::random(32));
            if (!static::where('booking_code', $code)->exists()) {
                return $code;
            }
        }

        throw new \RuntimeException('Unable to generate a unique booking code.');
    }

    public static function statusLabel(?int $status): string
    {
        return match ((int) $status) {
            0 => 'Pending',
            1 => 'Completed',
            2 => 'Cancelled',
            3 => 'Confirmed',
            4 => 'Rescheduled',
            default => 'Unknown',
        };
    }

    public static function statusBadgeClass(?int $status): string
    {
        return match ((int) $status) {
            0 => 'pending',
            1 => 'completed',
            2 => 'cancelled',
            3 => 'confirmed',
            4 => 'rescheduled',
            default => 'pending',
        };
    }

    public function services()
    {
        return $this->hasMany(AppointmentService::class, 'appointment_id', 'appointment_id');
    }

    public function products()
    {
        return $this->hasMany(AppointmentProduct::class, 'appointment_id', 'appointment_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }

    public function serviceStaff()
    {
        return $this->hasManyThrough(
            User::class,
            AppointmentService::class,
            'appointment_id',
            'id',
            'appointment_id',
            'staff_id'
        );
    }

    public function staff()
    {
        return $this->hasManyThrough(
            User::class,
            AppointmentService::class,
            'appointment_id',
            'id',
            'appointment_id',
            'staff_id'
        );
    }

    public function productStaff()
    {
        return $this->hasManyThrough(
            User::class,
            AppointmentProduct::class,
            'appointment_id',
            'id',
            'appointment_id',
            'staff_id'
        );
    }

    public function allStaff()
    {
        return $this->serviceStaff->merge($this->productStaff)->unique('id');
    }

    public function getStaffNamesAttribute()
    {
        return $this->allStaff()->pluck('name')->implode(', ');
    }
}
