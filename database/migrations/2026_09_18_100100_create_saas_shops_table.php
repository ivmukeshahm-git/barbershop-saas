<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('saas_shops')) {
            Schema::create('saas_shops', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('owner_user_id')->nullable();
                $table->string('name');
                $table->string('slug')->unique();
                $table->string('email')->nullable();
                $table->string('phone', 30)->nullable();
                $table->string('address')->nullable();
                $table->string('city')->nullable();
                $table->string('state')->nullable();
                $table->string('country')->default('India');
                $table->string('pincode', 20)->nullable();
                $table->string('logo')->nullable();
                $table->string('theme_color', 20)->default('#2D5BFF');
                $table->enum('status', ['pending', 'active', 'suspended', 'cancelled'])->default('pending');
                $table->timestamp('activated_at')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->foreign('owner_user_id')->references('id')->on('users')->nullOnDelete();
                $table->index('status');
            });
        }

        $ownerId = null;
        if (Schema::hasTable('users')) {
            $ownerId = DB::table('users')
                ->where('email', 'superadmin@gmail.com')
                ->value('id');

            if (!$ownerId) {
                $ownerId = DB::table('users')->orderBy('id')->value('id');
            }
        }

        $now = now();
        $shop = [
            'owner_user_id' => $ownerId,
            'name' => 'Rosenthal Barbershop',
            'slug' => 'rosenthal',
            'email' => 'hello@rosenthalbarbershop.ca',
            'phone' => '+1 416 555 0100',
            'address' => '123 King Street West',
            'city' => 'Toronto',
            'state' => 'Ontario',
            'country' => 'Canada',
            'pincode' => 'M5H 1A1',
            'logo' => null,
            'theme_color' => '#2D5BFF',
            'status' => 'active',
            'activated_at' => $now,
            'updated_at' => $now,
        ];

        $existing = DB::table('saas_shops')->where('slug', 'rosenthal')->first();
        if ($existing) {
            DB::table('saas_shops')->where('id', $existing->id)->update($shop);
        } else {
            $shop['created_at'] = $now;
            DB::table('saas_shops')->insert($shop);
        }
    }

    public function down()
    {
        Schema::dropIfExists('saas_shops');
    }
};
