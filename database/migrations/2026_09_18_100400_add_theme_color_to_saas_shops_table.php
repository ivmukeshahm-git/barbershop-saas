<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('saas_shops')) {
            return;
        }

        Schema::table('saas_shops', function (Blueprint $table) {
            if (!Schema::hasColumn('saas_shops', 'theme_color')) {
                $table->string('theme_color', 20)->default('#2D5BFF')->after('logo');
            }
        });

        // Backfill theme for any shops missing a color.
        if (Schema::hasColumn('saas_shops', 'theme_color')) {
            DB::table('saas_shops')
                ->where(function ($q) {
                    $q->whereNull('theme_color')->orWhere('theme_color', '');
                })
                ->update([
                    'theme_color' => '#2D5BFF',
                    'updated_at' => now(),
                ]);

            DB::table('saas_shops')
                ->where('slug', 'rosenthal')
                ->where(function ($q) {
                    $q->whereNull('theme_color')->orWhere('theme_color', '');
                })
                ->update([
                    'theme_color' => '#2D5BFF',
                    'updated_at' => now(),
                ]);
        }
    }

    public function down()
    {
        if (!Schema::hasTable('saas_shops')) {
            return;
        }

        Schema::table('saas_shops', function (Blueprint $table) {
            if (Schema::hasColumn('saas_shops', 'theme_color')) {
                $table->dropColumn('theme_color');
            }
        });
    }
};
