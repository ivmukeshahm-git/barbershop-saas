<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'is_barber')) {
                $table->boolean('is_barber')->default(1)->after('commission');
            }
        });

        DB::table('users')->whereNull('is_barber')->update(['is_barber' => 1]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'is_barber')) {
                $table->dropColumn('is_barber');
            }
        });
    }
};
