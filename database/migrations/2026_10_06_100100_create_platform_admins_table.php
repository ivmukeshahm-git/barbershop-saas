<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('platform_admins', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->boolean('is_active')->default(true);
            $table->rememberToken();
            $table->timestamps();
        });

        if (Schema::hasTable('users') && Schema::hasColumn('users', 'is_platform_admin')) {
            $admins = DB::table('users')
                ->where('is_platform_admin', true)
                ->get(['name', 'email', 'password', 'active_status', 'created_at', 'updated_at']);

            foreach ($admins as $admin) {
                DB::table('platform_admins')->updateOrInsert(
                    ['email' => $admin->email],
                    [
                        'name' => $admin->name,
                        'password' => $admin->password,
                        'is_active' => (bool) $admin->active_status,
                        'created_at' => $admin->created_at ?: now(),
                        'updated_at' => $admin->updated_at ?: now(),
                    ]
                );
            }
        }
    }

    public function down()
    {
        Schema::dropIfExists('platform_admins');
    }
};
