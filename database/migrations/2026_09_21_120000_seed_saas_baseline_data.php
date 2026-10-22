<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

/**
 * Re-applies SaaS baseline seed data for installs that already ran the
 * 2026_09_18_100xxx schema migrations before seed inserts were added.
 */
return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('saas_plans') || !Schema::hasTable('saas_shops')) {
            return;
        }

        $now = now();

        // --- Plans ---
        $plans = [
            [
                'name' => 'Starter',
                'slug' => 'starter',
                'tagline' => 'For new barbershops',
                'description' => 'Everything you need to run appointments and billing for a single shop.',
                'price_monthly' => 999,
                'price_yearly' => 9990,
                'max_staff' => 3,
                'max_appointments_month' => 300,
                'features' => json_encode([
                    'Admin dashboard',
                    'Up to 3 staff members',
                    'Invoices & payments',
                    'Customer records',
                    'Email support',
                ]),
                'is_popular' => 0,
                'is_active' => 1,
                'sort_order' => 1,
            ],
            [
                'name' => 'Growth',
                'slug' => 'growth',
                'tagline' => 'Most popular for busy shops',
                'description' => 'More staff capacity, reports, and tools to grow your barbershop.',
                'price_monthly' => 1999,
                'price_yearly' => 19990,
                'max_staff' => 10,
                'max_appointments_month' => 1500,
                'features' => json_encode([
                    'Everything in Starter',
                    'Up to 10 staff members',
                    'Staff performance reports',
                    'Calendar & working hours',
                    'Priority email support',
                ]),
                'is_popular' => 1,
                'is_active' => 1,
                'sort_order' => 2,
            ],
            [
                'name' => 'Business',
                'slug' => 'business',
                'tagline' => 'For multi-chair premium shops',
                'description' => 'Unlimited scale with full reporting and dedicated onboarding help.',
                'price_monthly' => 3999,
                'price_yearly' => 39990,
                'max_staff' => null,
                'max_appointments_month' => null,
                'features' => json_encode([
                    'Everything in Growth',
                    'Unlimited staff',
                    'Unlimited appointments',
                    'Advanced reports',
                    'Onboarding assistance',
                ]),
                'is_popular' => 0,
                'is_active' => 1,
                'sort_order' => 3,
            ],
        ];

        foreach ($plans as $plan) {
            $existing = DB::table('saas_plans')->where('slug', $plan['slug'])->first();
            $payload = array_merge($plan, ['updated_at' => $now]);
            if ($existing) {
                DB::table('saas_plans')->where('id', $existing->id)->update($payload);
            } else {
                $payload['created_at'] = $now;
                DB::table('saas_plans')->insert($payload);
            }
        }

        // --- Demo shop ---
        $ownerId = DB::table('users')->where('email', 'superadmin@gmail.com')->value('id')
            ?: DB::table('users')->orderBy('id')->value('id');

        $shopPayload = [
            'owner_user_id' => $ownerId,
            'name' => 'Rosenthal Barbershop',
            'slug' => 'rosenthal',
            'email' => 'hello@rosenthalbarbershop.ca',
            'phone' => '+1 416 555 0100',
            'address' => '123 King Street West',
            'city' => 'Toronto',
            'state' => 'Ontario',
            'country' => 'Canada',
            'pincode' => 'M5H 1A1',
            'logo' => null,
            'theme_color' => '#2D5BFF',
            'status' => 'active',
            'activated_at' => $now,
            'updated_at' => $now,
        ];

        $shop = DB::table('saas_shops')->where('slug', 'rosenthal')->first();
        if ($shop) {
            DB::table('saas_shops')->where('id', $shop->id)->update($shopPayload);
            $shopId = $shop->id;
        } else {
            $shopPayload['created_at'] = $now;
            $shopId = DB::table('saas_shops')->insertGetId($shopPayload);
        }

        // --- Subscription ---
        if (Schema::hasTable('saas_subscriptions')) {
            $planId = DB::table('saas_plans')->where('slug', 'growth')->value('id');
            if ($planId) {
                $subPayload = [
                    'shop_id' => $shopId,
                    'plan_id' => $planId,
                    'billing_cycle' => 'yearly',
                    'amount' => 19990,
                    'currency' => 'INR',
                    'status' => 'active',
                    'starts_at' => $now,
                    'ends_at' => $now->copy()->addYear(),
                    'payment_method' => 'manual',
                    'payment_reference' => 'SEED-ROSENTHAL-GROWTH',
                    'paid_at' => $now,
                    'meta' => json_encode([
                        'seeded' => true,
                        'note' => 'Baseline demo subscription for Rosenthal Barbershop',
                    ]),
                    'updated_at' => $now,
                ];

                $existingSub = DB::table('saas_subscriptions')
                    ->where('payment_reference', 'SEED-ROSENTHAL-GROWTH')
                    ->first();

                if ($existingSub) {
                    DB::table('saas_subscriptions')->where('id', $existingSub->id)->update($subPayload);
                } elseif (!DB::table('saas_subscriptions')->where('shop_id', $shopId)->where('status', 'active')->exists()) {
                    $subPayload['created_at'] = $now;
                    DB::table('saas_subscriptions')->insert($subPayload);
                }
            }
        }

        // --- Link users + platform admin ---
        if (Schema::hasColumn('users', 'shop_id')) {
            DB::table('users')
                ->whereNull('shop_id')
                ->where(function ($q) {
                    $q->whereNull('is_platform_admin')->orWhere('is_platform_admin', 0);
                })
                ->update(['shop_id' => $shopId]);
        }

        if (Schema::hasColumn('users', 'is_platform_admin')) {
            $email = 'platform@barbershopsaas.com';
            $existing = DB::table('users')->where('email', $email)->first();
            $payload = [
                'name' => 'Platform Admin',
                'email' => $email,
                'role' => 'Admin',
                'shop_id' => null,
                'is_platform_admin' => 1,
                'updated_at' => $now,
            ];
            if (Schema::hasColumn('users', 'active_status')) {
                $payload['active_status'] = 1;
            }
            if (Schema::hasColumn('users', 'is_barber')) {
                $payload['is_barber'] = 0;
            }

            if ($existing) {
                DB::table('users')->where('id', $existing->id)->update($payload);
            } else {
                $payload['password'] = Hash::make('Platform@123');
                $payload['created_at'] = $now;
                if (Schema::hasColumn('users', 'email_verified_at')) {
                    $payload['email_verified_at'] = $now;
                }
                DB::table('users')->insert($payload);
            }
        }

        // --- Staff defaults ---
        if (Schema::hasColumn('users', 'is_barber')) {
            DB::table('users')
                ->where('shop_id', $shopId)
                ->where('role', 'Staff')
                ->update(['is_barber' => 1, 'updated_at' => $now]);
        }

        if (Schema::hasColumn('users', 'commission')) {
            DB::table('users')
                ->where('shop_id', $shopId)
                ->whereNull('commission')
                ->update(['commission' => 0, 'updated_at' => $now]);
        }

        if (Schema::hasColumn('users', 'morning_start_time')) {
            DB::table('users')
                ->where('shop_id', $shopId)
                ->where('role', 'Staff')
                ->whereNull('morning_start_time')
                ->update([
                    'morning_start_time' => '09:00:00',
                    'morning_end_time' => '13:00:00',
                    'evning_start_time' => '14:00:00',
                    'evning_end_time' => '18:00:00',
                    'updated_at' => $now,
                ]);
        }
    }

    public function down()
    {
        DB::table('saas_subscriptions')->where('payment_reference', 'SEED-ROSENTHAL-GROWTH')->delete();
        DB::table('users')->where('email', 'platform@barbershopsaas.com')->delete();
        DB::table('saas_shops')->where('slug', 'rosenthal')->delete();
        // Keep plans — required reference data.
    }
};
