<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaasSubscription extends Model
{
    use HasFactory;

    protected $table = 'saas_subscriptions';

    protected $fillable = [
        'shop_id',
        'plan_id',
        'billing_cycle',
        'amount',
        'currency',
        'status',
        'starts_at',
        'ends_at',
        'payment_method',
        'payment_reference',
        'paid_at',
        'meta',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'paid_at' => 'datetime',
        'meta' => 'array',
    ];

    public function shop()
    {
        return $this->belongsTo(SaasShop::class, 'shop_id');
    }

    public function plan()
    {
        return $this->belongsTo(SaasPlan::class, 'plan_id');
    }

    public function isActive(): bool
    {
        if ($this->status !== 'active') {
            return false;
        }

        return $this->ends_at === null || $this->ends_at->isFuture();
    }

    /**
     * Activate or renew this subscription period.
     * If $from is provided (e.g. previous ends_at still in future), period starts there.
     */
    public function activate(string $paymentMethod = 'manual', ?string $reference = null, $from = null): void
    {
        $starts = $from
            ? \Carbon\Carbon::parse($from)
            : now();

        if ($starts->isPast()) {
            $starts = now();
        }

        $ends = $this->billing_cycle === 'yearly'
            ? $starts->copy()->addYear()
            : $starts->copy()->addMonth();

        $this->update([
            'status' => 'active',
            'starts_at' => $starts,
            'ends_at' => $ends,
            'payment_method' => $paymentMethod,
            'payment_reference' => $reference ?: ('PAY-' . strtoupper(uniqid())),
            'paid_at' => now(),
        ]);
    }

    public function markExpired(): void
    {
        if ($this->status === 'active') {
            $this->update(['status' => 'expired']);
        }
    }
}
