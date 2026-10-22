<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('saas_shops')) {
            return;
        }

        if (!Schema::hasColumn('saas_shops', 'currency')) {
            Schema::table('saas_shops', function (Blueprint $table) {
                $table->string('currency', 10)->default('INR')->after('country');
            });
        }

        // Backfill currency from country when known.
        $map = config('countries', []);
        if (!empty($map) && Schema::hasColumn('saas_shops', 'currency')) {
            $shops = DB::table('saas_shops')->select('id', 'country', 'currency')->get();
            foreach ($shops as $shop) {
                $currency = $map[$shop->country] ?? null;
                if ($currency && $shop->currency !== $currency) {
                    // Only overwrite default INR when country maps to something else.
                    if ($shop->currency === 'INR' || empty($shop->currency)) {
                        DB::table('saas_shops')->where('id', $shop->id)->update(['currency' => $currency]);
                    }
                }
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('saas_shops') && Schema::hasColumn('saas_shops', 'currency')) {
            Schema::table('saas_shops', function (Blueprint $table) {
                $table->dropColumn('currency');
            });
        }
    }
};
