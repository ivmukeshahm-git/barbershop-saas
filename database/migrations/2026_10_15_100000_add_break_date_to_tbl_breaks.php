<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('tbl_breaks', 'break_date')) {
            Schema::table('tbl_breaks', function (Blueprint $table) {
                $table->date('break_date')->nullable()->after('break_end')->index();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('tbl_breaks', 'break_date')) {
            Schema::table('tbl_breaks', function (Blueprint $table) {
                $table->dropIndex(['break_date']);
                $table->dropColumn('break_date');
            });
        }
    }
};
