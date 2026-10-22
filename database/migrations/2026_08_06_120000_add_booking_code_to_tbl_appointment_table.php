<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tbl_appointment', function (Blueprint $table) {
            if (!Schema::hasColumn('tbl_appointment', 'booking_code')) {
                $table->string('booking_code', 40)->nullable()->unique()->after('appointment_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_appointment', function (Blueprint $table) {
            if (Schema::hasColumn('tbl_appointment', 'booking_code')) {
                $table->dropUnique(['booking_code']);
                $table->dropColumn('booking_code');
            }
        });
    }
};
