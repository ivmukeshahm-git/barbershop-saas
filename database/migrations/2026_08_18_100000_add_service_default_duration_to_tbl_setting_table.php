<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tbl_setting', function (Blueprint $table) {
            if (!Schema::hasColumn('tbl_setting', 'service_default_duration')) {
                $table->unsignedInteger('service_default_duration')->nullable()->default(30)->after('service_reminder_day');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tbl_setting', function (Blueprint $table) {
            if (Schema::hasColumn('tbl_setting', 'service_default_duration')) {
                $table->dropColumn('service_default_duration');
            }
        });
    }
};
