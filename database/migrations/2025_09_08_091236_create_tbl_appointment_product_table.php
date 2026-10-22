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
        Schema::create('tbl_appointment_product', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appointment_id')->constrained('tbl_appointment')->onDelete('cascade');
            $table->unsignedBigInteger('staff_id');
            $table->unsignedBigInteger('product_id');
            $table->integer('qty')->default(1);
            $table->string('amount')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_appointment_product');
    }
};
