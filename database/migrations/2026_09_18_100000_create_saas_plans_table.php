<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('saas_plans')) {
            Schema::create('saas_plans', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->string('tagline')->nullable();
                $table->text('description')->nullable();
                $table->decimal('price_monthly', 10, 2)->default(0);
                $table->decimal('price_yearly', 10, 2)->default(0);
                $table->unsignedInteger('max_staff')->nullable();
                $table->unsignedInteger('max_appointments_month')->nullable();
                $table->json('features')->nullable();
                $table->boolean('is_popular')->default(false);
                $table->boolean('is_active')->default(true);
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();
            });
        }

        $now = now();

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
            $payload = array_merge($plan, [
                'updated_at' => $now,
            ]);

            if ($existing) {
                DB::table('saas_plans')->where('id', $existing->id)->update($payload);
            } else {
                $payload['created_at'] = $now;
                DB::table('saas_plans')->insert($payload);
            }
        }
    }

    public function down()
    {
        Schema::dropIfExists('saas_plans');
    }
};
