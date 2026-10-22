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
        Schema::table('tbl_breaks', function (Blueprint $table) {
            //tbl_breaks
            $table->integer('staff_id')->nullable();
            $table->integer('break_type')->comment('1=configuration Break ,2 =Staff Break')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('breaks', function (Blueprint $table) {
            //
        });
    }
};
