<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tbl_service', function (Blueprint $table) {
            if (!Schema::hasColumn('tbl_service', 'home_section')) {
                $table->string('home_section', 50)->nullable()->after('description');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tbl_service', function (Blueprint $table) {
            if (Schema::hasColumn('tbl_service', 'home_section')) {
                $table->dropColumn('home_section');
            }
        });
    }
};
