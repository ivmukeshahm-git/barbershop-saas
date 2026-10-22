<?php

namespace Database\Seeders;

use App\Models\SaasPlan;
use Illuminate\Database\Seeder;

class SaasPlanSeeder extends Seeder
{
    public function run()
    {
        $plans = [
            [
                'name' => 'Free',
                'slug' => 'free',
                'tagline' => 'Start with one chair',
                'description' => 'Try the shop admin with one barber and a small monthly booking limit. Upgrade when the floor gets busier.',
                'price_monthly' => 0,
                'price_yearly' => 0,
                'max_staff' => 1,
                'max_appointments_month' => 80,
                'features' => [
                    '1 staff member',
                    'Up to 80 appointments / month',
                    'Customer records',
                    'Basic invoices',
                    'Email support',
                ],
                'is_popular' => false,
                'is_active' => true,
                'sort_order' => 0,
            ],
            [
                'name' => 'Starter',
                'slug' => 'starter',
                'tagline' => 'For new barbershops',
                'description' => 'Everything you need to run appointments and billing for a single shop.',
                'price_monthly' => 999,
                'price_yearly' => 9990,
                'max_staff' => 3,
                'max_appointments_month' => 300,
                'features' => [
                    'Admin dashboard',
                    'Up to 3 staff members',
                    'Invoices & payments',
                    'Customer records',
                    'Email support',
                ],
                'is_popular' => false,
                'is_active' => true,
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
                'features' => [
                    'Everything in Starter',
                    'Up to 10 staff members',
                    'Staff performance reports',
                    'Calendar & working hours',
                    'Priority email support',
                ],
                'is_popular' => true,
                'is_active' => true,
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
                'features' => [
                    'Everything in Growth',
                    'Unlimited staff',
                    'Unlimited appointments',
                    'Advanced reports',
                    'Onboarding assistance',
                ],
                'is_popular' => false,
                'is_active' => true,
                'sort_order' => 3,
            ],
        ];

        foreach ($plans as $plan) {
            SaasPlan::updateOrCreate(
                ['slug' => $plan['slug']],
                $plan
            );
        }
    }
}
