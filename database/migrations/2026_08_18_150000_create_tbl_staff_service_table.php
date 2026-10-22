<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        Schema::create('tbl_staff_service', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('staff_id');
            $table->unsignedBigInteger('service_id');
            $table->timestamps();

            $table->unique(['staff_id', 'service_id']);

            $table->foreign('staff_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('service_id')->references('id')->on('tbl_service')->onDelete('cascade');
        });

        // Backfill existing assignments from the legacy users.service_id column
        $staffUsers = DB::table('users')
            ->where('role', 'Staff')
            ->whereNotNull('service_id')
            ->where('service_id', '<>', '')
            ->get(['id', 'service_id']);

        $now = now();
        foreach ($staffUsers as $user) {
            $ids = array_values(array_unique(array_filter(array_map('trim', explode(',', (string) $user->service_id)))));
            foreach ($ids as $serviceId) {
                if (!is_numeric($serviceId)) {
                    continue;
                }
                $exists = DB::table('tbl_staff_service')
                    ->where('staff_id', $user->id)
                    ->where('service_id', $serviceId)
                    ->exists();
                if (!$exists) {
                    DB::table('tbl_staff_service')->insert([
                        'staff_id'   => $user->id,
                        'service_id' => $serviceId,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_staff_service');
    }
};