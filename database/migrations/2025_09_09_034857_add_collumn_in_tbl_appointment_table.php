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
        Schema::table('tbl_appointment', function (Blueprint $table) {
            //
            $table->string('customer_id')->nullable();
            $table->string('is_invoiced')->nullable();
            $table->string('invoice_number')->nullable();
            $table->float('total_amt')->nullable();
            $table->float('total_discount')->nullable();
            $table->float('total_tax')->nullable();
            $table->float('total_tip')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_appointment', function (Blueprint $table) {
            //
        });
    }
};
