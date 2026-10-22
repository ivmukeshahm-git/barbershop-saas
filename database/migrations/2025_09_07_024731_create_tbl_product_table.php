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
        Schema::create('tbl_product', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('created_by');
            $table->unsignedBigInteger('category_id');
            $table->decimal('price', 10, 2);
            $table->integer('stock')->default(0);
            $table->decimal('commission', 10, 2)->default(0.00);
            $table->boolean('status')->default(1); // 1: active, 0: inactive
            $table->string('product_img')->nullable();
            $table->text('description')->nullable();
            $table->softDeletes(); // Adds deleted_at column
            $table->timestamps();

            // Foreign key (optional)
            // $table->foreign('category_id')->references('id')->on('tbl_product_category');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_product');
    }
};
