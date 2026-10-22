<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable,  SoftDeletes, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'shop_id',
        'created_by',
        'name',
        'email',
        'password',
        'role',
        'active_status',
        'is_platform_admin',
        'phone',
        'service_id',
        'commission',
        'is_barber',
        'profile_img',
        'morning_start_time',
        'morning_end_time',
        'evning_start_time',
        'evning_end_time',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_platform_admin' => 'boolean',
        'is_barber' => 'boolean',
    ];

    protected $appends = ['profile_image_url'];

    public function getProfileImageUrlAttribute(): string
    {
        $path = trim((string) $this->profile_img);
        if ($path === '') {
            return asset('assets/img/user.svg');
        }

        if (preg_match('#^(https?:)?//#i', $path)) {
            return $path;
        }

        $path = ltrim(str_replace('\\', '/', $path), '/');
        $path = preg_replace('#^(public/|storage/)#', '', $path);

        return asset('storage/' . $path);
    }

    public function shop()
    {
        return $this->belongsTo(SaasShop::class, 'shop_id');
    }

    /**
     * Limit users to the current tenant shop when admin context is set.
     */
    public function scopeForCurrentShop($query)
    {
        $shopId = \App\Support\CurrentShop::id();
        if ($shopId !== null) {
            $query->where($query->getModel()->getTable() . '.shop_id', $shopId);
        }

        return $query;
    }

    public function assignedServices()
    {
        return $this->hasMany(StaffService::class, 'staff_id', 'id');
    }

    public function assignedServiceList()
    {
        return $this->belongsToMany(Service::class, 'tbl_staff_service', 'staff_id', 'service_id');
    }

    public function isStaffUser(): bool
    {
        return strcasecmp((string) $this->role, 'Staff') === 0;
    }

    public function isBarber(): bool
    {
        return (int) $this->is_barber === 1;
    }

    /**
     * Staff members who take appointments (barber toggle on).
     */
    public function scopeBarbers($query)
    {
        return $query->where('role', 'Staff')->where('is_barber', 1);
    }

    public function isAccountant(): bool
    {
        return strcasecmp((string) $this->role, 'Accountant') === 0
            || $this->hasRole('Accountant');
    }

    /**
     * Best landing route after login based on permissions.
     */
    public function preferredAdminHomeRoute(): string
    {
        if ($this->canAny([
            'Dashboard-Today_Appointments',
            'Dashboard-Upcoming_Appointments',
            'Dashboard-Top_Services',
            'Dashboard-Top_Staff',
        ])) {
            return 'admin.dashboard';
        }

        if ($this->can('Report-View')) {
            return $this->isStaffUser()
                ? 'admin.staff.staff-report'
                : 'admin.reports';
        }

        if ($this->canAny(['Pos_invoice-Add', 'Pos_invoice-Edit'])) {
            return 'admin.invoice.index';
        }

        if ($this->canAny(['Customer-Add', 'Customer-Edit', 'Customer-Delete'])) {
            return 'admin.customers.index';
        }

        if ($this->isStaffUser()) {
            return 'admin.staff.staff-report';
        }

        return 'admin.profile.index';
    }
}
