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
        Schema::table('tbl_setting', function (Blueprint $table) {
            $table->boolean('invoice_send_sts')
                ->default(0)
                ->comment('0 = Deactive, 1 = Active')
                ->after('service_reminder_day'); // place after service_reminder_day if exists
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_setting', function (Blueprint $table) {
            //
        });
    }
};
