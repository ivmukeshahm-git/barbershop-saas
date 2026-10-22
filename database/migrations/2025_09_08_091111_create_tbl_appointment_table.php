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
        Schema::create('tbl_appointment', function (Blueprint $table) {
            $table->id('appointment_id');
            $table->dateTime('date');
            $table->time('slot_start');
            $table->time('slot_end');
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('notes')->nullable();
            $table->tinyInteger('status')->default(0)->comment('1:Complete, 0:Pending, 2:Reject, 3:Confirm, 4:Reschedule');
            $table->text('comment')->nullable(); // added comment field
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
        Schema::dropIfExists('tbl_appointment');
    }
};
