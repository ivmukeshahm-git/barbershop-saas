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
        Schema::create('tbl_shop_closure', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('created_by');
            $table->date('date');
            $table->tinyInteger('closure_status')->comment('1: Close full day, 0: Half Day');
            $table->date('start_date')->nullable();  // Nullable if closure_status = 1
            $table->date('end_date')->nullable();    // Nullable if closure_status = 1
            $table->text('reason')->nullable();
            $table->timestamps();

            // Optionally, add foreign key constraint for created_by if users table exists
            // $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_shop_closure');
    }
};
