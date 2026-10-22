<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->time('morning_start_time')->nullable()->after('remember_token');
            $table->time('morning_end_time')->nullable()->after('morning_start_time');
            $table->time('evning_start_time')->nullable()->after('morning_end_time');
            $table->time('evning_end_time')->nullable()->after('evning_start_time');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
