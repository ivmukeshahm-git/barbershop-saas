<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tbl_appointment_service', function (Blueprint $table) {
            if (!Schema::hasColumn('tbl_appointment_service', 'requested_staff_id')) {
                $table->unsignedBigInteger('requested_staff_id')->nullable()->after('staff_id');
            }
            if (!Schema::hasColumn('tbl_appointment_service', 'is_first_available')) {
                $table->boolean('is_first_available')->default(false)->after('requested_staff_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tbl_appointment_service', function (Blueprint $table) {
            if (Schema::hasColumn('tbl_appointment_service', 'is_first_available')) {
                $table->dropColumn('is_first_available');
            }
            if (Schema::hasColumn('tbl_appointment_service', 'requested_staff_id')) {
                $table->dropColumn('requested_staff_id');
            }
        });
    }
};
