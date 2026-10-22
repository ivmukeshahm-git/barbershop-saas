<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('saas_subscriptions')) {
            Schema::create('saas_subscriptions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('shop_id')->constrained('saas_shops')->cascadeOnDelete();
                $table->foreignId('plan_id')->constrained('saas_plans')->restrictOnDelete();
                $table->enum('billing_cycle', ['monthly', 'yearly'])->default('monthly');
                $table->decimal('amount', 10, 2)->default(0);
                $table->string('currency', 10)->default('INR');
                $table->enum('status', ['pending', 'active', 'expired', 'cancelled'])->default('pending');
                $table->timestamp('starts_at')->nullable();
                $table->timestamp('ends_at')->nullable();
                $table->string('payment_method')->nullable();
                $table->string('payment_reference')->nullable();
                $table->timestamp('paid_at')->nullable();
                $table->json('meta')->nullable();
                $table->timestamps();

                $table->index(['shop_id', 'status']);
            });
        }

        $shopId = DB::table('saas_shops')->where('slug', 'rosenthal')->value('id');
        $planId = DB::table('saas_plans')->where('slug', 'growth')->value('id');

        if (!$shopId || !$planId) {
            return;
        }

        $now = now();
        $starts = $now->copy();
        $ends = $now->copy()->addYear();

        $subscription = [
            'shop_id' => $shopId,
            'plan_id' => $planId,
            'billing_cycle' => 'yearly',
            'amount' => 19990,
            'currency' => 'INR',
            'status' => 'active',
            'starts_at' => $starts,
            'ends_at' => $ends,
            'payment_method' => 'manual',
            'payment_reference' => 'SEED-ROSENTHAL-GROWTH',
            'paid_at' => $starts,
            'meta' => json_encode([
                'seeded' => true,
                'note' => 'Baseline demo subscription for Rosenthal Barbershop',
            ]),
            'updated_at' => $now,
        ];

        $existing = DB::table('saas_subscriptions')
            ->where('shop_id', $shopId)
            ->where('payment_reference', 'SEED-ROSENTHAL-GROWTH')
            ->first();

        if ($existing) {
            DB::table('saas_subscriptions')->where('id', $existing->id)->update($subscription);
        } else {
            $hasActive = DB::table('saas_subscriptions')
                ->where('shop_id', $shopId)
                ->where('status', 'active')
                ->exists();

            if (!$hasActive) {
                $subscription['created_at'] = $now;
                DB::table('saas_subscriptions')->insert($subscription);
            }
        }
    }

    public function down()
    {
        Schema::dropIfExists('saas_subscriptions');
    }
};
