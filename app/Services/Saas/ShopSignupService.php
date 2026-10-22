<?php

namespace App\Services\Saas;

use App\Models\Role;
use App\Models\SaasPlan;
use App\Models\SaasShop;
use App\Models\SaasSubscription;
use App\Models\User;
use App\Services\Saas\PlanEntitlementService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Permission;

class ShopSignupService
{
    /**
     * Complete SaaS purchase: create owner, shop, subscription, activate access.
     * Demo/manual payment activates immediately and creates /{shop-slug}/admin URL.
     */
    public function provision(
        array $account,
        array $shopData,
        SaasPlan $plan,
        string $billingCycle = 'monthly',
        string $paymentMethod = 'manual'
    ): array {
        return DB::transaction(function () use ($account, $shopData, $plan, $billingCycle, $paymentMethod) {
            $user = User::create([
                'name' => $account['name'],
                'email' => $account['email'],
                'phone' => $account['phone'] ?? null,
                'password' => Hash::make($account['password']),
                'role' => 'Admin',
                'active_status' => 1,
                'is_platform_admin' => 0,
            ]);

            // Keep legacy `status` column in sync when present (e.g. fineto_db).
            if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'status')) {
                DB::table('users')->where('id', $user->id)->update(['status' => 'Active']);
            }

            $slug = $shopData['slug'] ?? SaasShop::makeUniqueSlug($shopData['name']);
            $themeColor = $shopData['theme_color'] ?? SaasShop::DEFAULT_THEME_COLOR;
            if (!preg_match('/^#[0-9A-Fa-f]{6}$/', (string) $themeColor)) {
                $themeColor = SaasShop::DEFAULT_THEME_COLOR;
            }

            $logoPath = null;
            if (!empty($shopData['logo']) && $shopData['logo'] instanceof UploadedFile) {
                $logoPath = $shopData['logo']->store('shop-logos', 'public');
            }

            $shopPayload = [
                'owner_user_id' => $user->id,
                'name' => $shopData['name'],
                'slug' => $slug,
                'email' => $shopData['email'] ?? $account['email'],
                'phone' => $shopData['phone'] ?? ($account['phone'] ?? null),
                'address' => $shopData['address'] ?? null,
                'city' => $shopData['city'] ?? null,
                'state' => $shopData['state'] ?? null,
                'country' => $shopData['country'] ?? 'India',
                'pincode' => $shopData['pincode'] ?? null,
                'logo' => $logoPath,
                'theme_color' => strtoupper($themeColor),
                'status' => 'pending',
            ];

            if (Schema::hasColumn('saas_shops', 'currency')) {
                $shopPayload['currency'] = $shopData['currency']
                    ?? \App\Support\CountryCurrency::currencyForCountry($shopPayload['country']);
            }

            $shop = SaasShop::create($shopPayload);

            $user->update(['shop_id' => $shop->id]);

            // The shop Admin role and its permissions are required before assigning the owner.
            $adminRole = $this->ensureShopAdminRole($shop);

            // Seed other empty defaults for this shop only.
            $this->seedShopDefaults($shop);

            $this->assignShopOwnerRole($user, $adminRole);

            $subscription = $this->createAndActivateSubscription($shop, $plan, $billingCycle, $paymentMethod);

            $shop->update([
                'status' => 'active',
                'activated_at' => now(),
            ]);

            return [
                'user' => $user->fresh(),
                'shop' => $shop->fresh(),
                'subscription' => $subscription->fresh(['plan']),
            ];
        });
    }

    /**
     * Renew / change plan on an EXISTING shop — never creates a new user or shop.
     *
     * Same plan cannot be purchased again while still active (wait until expiry).
     * Switching to a different plan (upgrade/change) is allowed and starts immediately.
     */
    public function renew(
        SaasShop $shop,
        SaasPlan $plan,
        string $billingCycle = 'monthly',
        string $paymentMethod = 'manual'
    ): array {
        return DB::transaction(function () use ($shop, $plan, $billingCycle, $paymentMethod) {
            $current = SaasSubscription::query()
                ->where('shop_id', $shop->id)
                ->where('status', 'active')
                ->where(function ($q) {
                    $q->whereNull('ends_at')->orWhere('ends_at', '>', now());
                })
                ->latest('id')
                ->first();

            $this->assertFreePlanAvailable($shop, $plan);

            $isSamePlan = $current && (int) $current->plan_id === (int) $plan->id;

            if ($isSamePlan) {
                throw ValidationException::withMessages([
                    'plan_id' => 'Your current plan is still active until '
                        . optional($current->ends_at)->format('d M Y')
                        . '. You can renew it after it expires, or upgrade to a different plan now.',
                ]);
            }

            // Upgrade / plan change takes effect immediately (no stacking of remaining days).
            $startFrom = null;

            // Close any open/pending rows for this shop (keep history).
            SaasSubscription::query()
                ->where('shop_id', $shop->id)
                ->whereIn('status', ['active', 'pending'])
                ->update(['status' => 'expired']);

            $subscription = $this->createAndActivateSubscription(
                $shop,
                $plan,
                $billingCycle,
                $paymentMethod,
                $startFrom
            );

            $shop->update([
                'status' => 'active',
                'activated_at' => $shop->activated_at ?? now(),
            ]);

            return [
                'shop' => $shop->fresh(),
                'subscription' => $subscription->fresh(['plan']),
            ];
        });
    }

    protected function assertFreePlanAvailable(SaasShop $shop, SaasPlan $plan): void
    {
        $isFree = $plan->slug === 'free'
            || ((float) $plan->price_monthly <= 0 && (float) $plan->price_yearly <= 0);

        if (!$isFree || !$shop->hasUsedFreePlan()) {
            return;
        }

        throw ValidationException::withMessages([
            'plan_id' => 'The free plan can only be purchased once. Choose a paid plan to continue.',
        ]);
    }

    protected function createAndActivateSubscription(
        SaasShop $shop,
        SaasPlan $plan,
        string $billingCycle,
        string $paymentMethod,
        $startFrom = null
    ): SaasSubscription {
        $cycle = $billingCycle === 'yearly' ? 'yearly' : 'monthly';
        $totals = app(PlanEntitlementService::class)->calculateTotals($plan, $cycle);

        $subscription = SaasSubscription::create([
            'shop_id' => $shop->id,
            'plan_id' => $plan->id,
            'billing_cycle' => $cycle,
            'amount' => $totals['total'],
            'currency' => (Schema::hasColumn('saas_shops', 'currency') && !empty($shop->currency))
                ? $shop->currency
                : 'INR',
            'status' => 'pending',
            'meta' => array_merge($totals, [
                'renewed_at' => now()->toDateTimeString(),
                'shop_id' => $shop->id,
            ]),
        ]);

        $subscription->activate($paymentMethod, null, $startFrom);

        return $subscription;
    }

    /**
     * Shop owners get a shop-scoped Admin role with the same permissions as Super-admin.
     * Never assign the global Super-admin role to tenant users.
     */
    protected function assignShopOwnerRole(User $user, Role $adminRole): void
    {
        $user->syncRoles([$adminRole]);
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }

    /**
     * Seed empty per-shop rows so a new tenant starts clean (not sharing another shop's data).
     */
    protected function seedShopDefaults(SaasShop $shop): void
    {
        $shopId = $shop->id;

        try {
            if (Schema::hasTable('tbl_setting') && Schema::hasColumn('tbl_setting', 'shop_id')) {
                if (!DB::table('tbl_setting')->where('shop_id', $shopId)->exists()) {
                    $row = ['shop_id' => $shopId];
                    if (Schema::hasColumn('tbl_setting', 'created_at')) {
                        $row['created_at'] = now();
                        $row['updated_at'] = now();
                    }
                    DB::table('tbl_setting')->insert($row);
                }
            }

            if (Schema::hasTable('tbl_gst') && Schema::hasColumn('tbl_gst', 'shop_id')) {
                if (!DB::table('tbl_gst')->where('shop_id', $shopId)->exists()) {
                    $row = [
                        'shop_id' => $shopId,
                        'name' => 'GST 18%',
                        'rate' => 18,
                        'default_status' => 1,
                    ];
                    if (Schema::hasColumn('tbl_gst', 'created_by')) {
                        $row['created_by'] = $shop->owner_user_id ?? 0;
                    }
                    if (Schema::hasColumn('tbl_gst', 'tax_type')) {
                        $row['tax_type'] = 'include';
                    }
                    if (Schema::hasColumn('tbl_gst', 'created_at')) {
                        $row['created_at'] = now();
                        $row['updated_at'] = now();
                    }
                    DB::table('tbl_gst')->insert($row);
                }
            }

            if (Schema::hasTable('tbl_payment_method') && Schema::hasColumn('tbl_payment_method', 'shop_id')) {
                if (!DB::table('tbl_payment_method')->where('shop_id', $shopId)->exists()) {
                    foreach (['Cash', 'UPI', 'Card'] as $name) {
                        $row = ['shop_id' => $shopId, 'name' => $name];
                        if (Schema::hasColumn('tbl_payment_method', 'created_at')) {
                            $row['created_at'] = now();
                            $row['updated_at'] = now();
                        }
                        DB::table('tbl_payment_method')->insert($row);
                    }
                }
            }

            if (Schema::hasTable('tbl_slots') && Schema::hasColumn('tbl_slots', 'shop_id')) {
                foreach (['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day) {
                    $exists = DB::table('tbl_slots')
                        ->where('shop_id', $shopId)
                        ->where('staff_id', 0)
                        ->where('day_name', $day)
                        ->exists();
                    if ($exists) {
                        continue;
                    }
                    $row = [
                        'shop_id' => $shopId,
                        'day_name' => $day,
                        'staff_id' => 0,
                        'is_day_off' => $day === 'Sunday' ? 1 : 0,
                    ];
                    if (Schema::hasColumn('tbl_slots', 'created_at')) {
                        $row['created_at'] = now();
                        $row['updated_at'] = now();
                    }
                    DB::table('tbl_slots')->insert($row);
                }
            }
        } catch (\Throwable $e) {
            // Defaults are best-effort; shop/user/subscription still succeed.
        }
    }

    /**
     * Create only the shop's Admin role and grant it every tenant-facing permission.
     */
    protected function ensureShopAdminRole(SaasShop $shop): Role
    {
        if (
            !Schema::hasTable('roles')
            || !Schema::hasColumn('roles', 'shop_id')
            || !Schema::hasTable('permissions')
            || !Schema::hasTable('role_has_permissions')
        ) {
            throw new \RuntimeException('Shop roles and permissions are not available.');
        }

        $role = Role::ensureForShop((int) $shop->id, 'Admin');
        $permissionNames = Permission::query()
            ->where('guard_name', 'web')
            ->where('name', 'not like', 'platform-%')
            ->orderBy('name')
            ->pluck('name')
            ->all();

        $role->syncPermissions($permissionNames);
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        return $role;
    }
}
