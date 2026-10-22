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
       Schema::create('tbl_service', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('created_by');
            $table->unsignedBigInteger('category_id');
            $table->string('service_name', 255);
            $table->decimal('price', 10, 2);
            $table->integer('duration'); // Duration in minutes
            $table->boolean('status')->default(1); // 1: active, 0: inactive
            $table->boolean('tip_status')->default(0); // 1: tip allowed, 0: not allowed
            $table->string('service_img')->nullable();
            $table->text('description')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();

            // Foreign key (optional, uncomment if tbl_service_category exists)
            // $table->foreign('category_id')->references('id')->on('tbl_service_category');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_service');
    }
};
