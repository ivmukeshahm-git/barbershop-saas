<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('tbl_setting', 'booking_code_prefix')) {
            Schema::table('tbl_setting', function (Blueprint $table) {
                $table->string('booking_code_prefix', 10)->default('MHS');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('tbl_setting', 'booking_code_prefix')) {
            Schema::table('tbl_setting', function (Blueprint $table) {
                $table->dropColumn('booking_code_prefix');
            });
        }
    }
};
