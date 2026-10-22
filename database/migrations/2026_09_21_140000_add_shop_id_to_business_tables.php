<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Add shop_id to all shop-owned business tables so each tenant only sees their data.
 */
return new class extends Migration
{
    /** @var array<string, string|null> table => after-column hint */
    protected array $tables = [
        'customers' => null,
        'tbl_appointment' => null,
        'tbl_appointment_service' => null,
        'tbl_appointment_product' => null,
        'tbl_service' => null,
        'tbl_service_category' => null,
        'tbl_product' => null,
        'tbl_gst' => null,
        'tbl_payment_method' => null,
        'tbl_setting' => null,
        'tbl_shop_closure' => null,
        'tbl_slots' => null,
        'tbl_breaks' => null,
        'tbl_staff_service' => null,
        'galleries' => null,
        'tbl_testimonial' => null,
    ];

    public function up()
    {
        if (!Schema::hasTable('saas_shops')) {
            return;
        }

        $defaultShopId = DB::table('saas_shops')->orderBy('id')->value('id');

        foreach ($this->tables as $table => $after) {
            if (!Schema::hasTable($table) || Schema::hasColumn($table, 'shop_id')) {
                continue;
            }

            Schema::table($table, function (Blueprint $blueprint) use ($table, $after) {
                $col = $blueprint->unsignedBigInteger('shop_id')->nullable()->index();
                if ($after && Schema::hasColumn($table, $after)) {
                    $col->after($after);
                }
            });

            if ($defaultShopId) {
                DB::table($table)->whereNull('shop_id')->update(['shop_id' => $defaultShopId]);
            }
        }

        // Users without a shop (non-platform) → first shop
        if ($defaultShopId && Schema::hasColumn('users', 'shop_id')) {
            DB::table('users')
                ->whereNull('shop_id')
                ->where(function ($q) {
                    $q->where('is_platform_admin', 0)->orWhereNull('is_platform_admin');
                })
                ->update(['shop_id' => $defaultShopId]);
        }
    }

    public function down()
    {
        foreach (array_keys($this->tables) as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'shop_id')) {
                Schema::table($table, function (Blueprint $blueprint) {
                    $blueprint->dropColumn('shop_id');
                });
            }
        }
    }
};
