<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tbl_appointment', function (Blueprint $table) {
            if (!Schema::hasColumn('tbl_appointment', 'tax_type')) {
                $table->string('tax_type', 20)->nullable();
            }

            if (!Schema::hasColumn('tbl_appointment', 'tax_rate')) {
                $table->decimal('tax_rate', 5, 2)->nullable();
            }

            if (!Schema::hasColumn('tbl_appointment', 'inventory_deducted_at')) {
                $table->timestamp('inventory_deducted_at')->nullable();
            }
        });

        Schema::table('tbl_setting', function (Blueprint $table) {
            if (!Schema::hasColumn('tbl_setting', 'gstin')) {
                $table->string('gstin', 15)->nullable();
            }

            if (!Schema::hasColumn('tbl_setting', 'sac_code')) {
                $table->string('sac_code', 20)->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('tbl_appointment', function (Blueprint $table) {
            foreach (['tax_type', 'tax_rate', 'inventory_deducted_at'] as $column) {
                if (Schema::hasColumn('tbl_appointment', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('tbl_setting', function (Blueprint $table) {
            foreach (['gstin', 'sac_code'] as $column) {
                if (Schema::hasColumn('tbl_setting', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
