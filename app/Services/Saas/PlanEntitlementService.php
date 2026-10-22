<?php

namespace App\Services\Saas;

use App\Models\Appointment;
use App\Models\SaasPlan;
use App\Models\SaasShop;
use App\Models\SaasSubscription;
use App\Models\User;
use App\Support\CurrentShop;
use Illuminate\Support\Facades\Schema;

class PlanEntitlementService
{
    public const GST_RATE = 18.0;

    public function shop(?SaasShop $shop = null): ?SaasShop
    {
        return $shop ?: CurrentShop::get();
    }

    public function subscription(?SaasShop $shop = null): ?SaasSubscription
    {
        $shop = $this->shop($shop);
        if (!$shop) {
            return null;
        }

        return $shop->activeSubscription()->with('plan')->first();
    }

    public function plan(?SaasShop $shop = null): ?SaasPlan
    {
        return $this->subscription($shop)?->plan;
    }

    /**
     * Build pricing breakdown for checkout UI / subscription meta.
     */
    public function calculateTotals(SaasPlan $plan, string $billingCycle): array
    {
        $cycle = $billingCycle === 'yearly' ? 'yearly' : 'monthly';
        $subtotal = $plan->priceFor($cycle);
        $monthlyEquivalent = (float) $plan->price_monthly * ($cycle === 'yearly' ? 12 : 1);
        $discount = max(0, round($monthlyEquivalent - $subtotal, 2));
        $gst = round($subtotal * (self::GST_RATE / 100), 2);
        $total = round($subtotal + $gst, 2);

        return [
            'billing_cycle' => $cycle,
            'currency' => 'INR',
            'plan_name' => $plan->name,
            'plan_slug' => $plan->slug,
            'list_price' => $monthlyEquivalent,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'gst_rate' => self::GST_RATE,
            'gst_amount' => $gst,
            'total' => $total,
            'max_staff' => $plan->max_staff,
            'max_appointments_month' => $plan->max_appointments_month,
            'features' => $plan->features ?? [],
        ];
    }

    public function staffCount(?SaasShop $shop = null): int
    {
        $shop = $this->shop($shop);
        if (!$shop) {
            return 0;
        }

        $query = User::query()
            ->where('role', 'Staff')
            ->whereNull('deleted_at');

        if (Schema::hasColumn('users', 'shop_id')) {
            $query->where('shop_id', $shop->id);
        }

        return (int) $query->count();
    }

    public function appointmentsThisMonth(?SaasShop $shop = null): int
    {
        $shop = $this->shop($shop);
        $query = Appointment::query()
            ->whereYear('date', now()->year)
            ->whereMonth('date', now()->month);

        // When appointments gain shop_id later, scope here.
        if ($shop && Schema::hasColumn('tbl_appointment', 'shop_id')) {
            $query->where('shop_id', $shop->id);
        }

        return (int) $query->count();
    }

    /**
     * @return true|string true when allowed, otherwise error message
     */
    public function assertCanAddStaff(?SaasShop $shop = null)
    {
        $plan = $this->plan($shop);
        if (!$plan) {
            return 'No active plan found. Please renew your subscription.';
        }

        if ($plan->max_staff === null) {
            return true;
        }

        $used = $this->staffCount($shop);
        if ($used >= (int) $plan->max_staff) {
            return "Your {$plan->name} plan allows up to {$plan->max_staff} staff. Upgrade to add more.";
        }

        return true;
    }

    /**
     * @return true|string
     */
    public function assertCanAddAppointment(?SaasShop $shop = null)
    {
        $plan = $this->plan($shop);
        if (!$plan) {
            return 'No active plan found. Please renew your subscription.';
        }

        if ($plan->max_appointments_month === null) {
            return true;
        }

        $used = $this->appointmentsThisMonth($shop);
        if ($used >= (int) $plan->max_appointments_month) {
            return "Your {$plan->name} plan allows {$plan->max_appointments_month} appointments this month. Limit reached — upgrade to continue.";
        }

        return true;
    }

    public function allowsStaffReports(?SaasShop $shop = null): bool
    {
        $plan = $this->plan($shop);
        if (!$plan) {
            return false;
        }

        if (in_array($plan->slug, ['growth', 'business'], true)) {
            return true;
        }

        foreach ($plan->features ?? [] as $feature) {
            if (stripos((string) $feature, 'staff') !== false && stripos((string) $feature, 'report') !== false) {
                return true;
            }
        }

        return false;
    }

    public function allowsAdvancedReports(?SaasShop $shop = null): bool
    {
        $plan = $this->plan($shop);
        if (!$plan) {
            return false;
        }

        if ($plan->slug === 'business') {
            return true;
        }

        foreach ($plan->features ?? [] as $feature) {
            if (stripos((string) $feature, 'advanced report') !== false) {
                return true;
            }
        }

        return false;
    }

    public function usageSummary(?SaasShop $shop = null): array
    {
        $plan = $this->plan($shop);

        return [
            'plan' => $plan,
            'staff_used' => $this->staffCount($shop),
            'staff_limit' => $plan?->max_staff,
            'appointments_used' => $this->appointmentsThisMonth($shop),
            'appointments_limit' => $plan?->max_appointments_month,
            'features' => $plan?->features ?? [],
            'allows_staff_reports' => $this->allowsStaffReports($shop),
            'allows_advanced_reports' => $this->allowsAdvancedReports($shop),
        ];
    }
}
