<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'shop_id')) {
                $table->unsignedBigInteger('shop_id')->nullable()->after('id');
            }

            if (!Schema::hasColumn('users', 'is_platform_admin')) {
                $table->boolean('is_platform_admin')->default(false);
            }
        });

        // Add FK only when saas_shops exists and FK not already present.
        if (Schema::hasTable('saas_shops') && Schema::hasColumn('users', 'shop_id')) {
            try {
                Schema::table('users', function (Blueprint $table) {
                    $table->foreign('shop_id')->references('id')->on('saas_shops')->nullOnDelete();
                });
            } catch (\Throwable $e) {
                // Foreign key may already exist.
            }
        }

        $shopId = Schema::hasTable('saas_shops')
            ? DB::table('saas_shops')->where('slug', 'rosenthal')->value('id')
            : null;

        if ($shopId && Schema::hasColumn('users', 'shop_id')) {
            // Attach existing shop staff/admins to the demo tenant.
            DB::table('users')
                ->whereNull('shop_id')
                ->where(function ($q) {
                    $q->whereNull('is_platform_admin')->orWhere('is_platform_admin', 0);
                })
                ->update(['shop_id' => $shopId]);

            $ownerId = DB::table('users')
                ->where('email', 'superadmin@gmail.com')
                ->value('id');

            if ($ownerId) {
                DB::table('saas_shops')->where('id', $shopId)->update([
                    'owner_user_id' => $ownerId,
                    'updated_at' => now(),
                ]);
            }
        }

        // Platform admin account (can access any shop tenant).
        if (Schema::hasColumn('users', 'is_platform_admin')) {
            $now = now();
            $email = 'platform@barbershopsaas.com';
            $existing = DB::table('users')->where('email', $email)->first();

            $payload = [
                'name' => 'Platform Admin',
                'email' => $email,
                'password' => Hash::make('Platform@123'),
                'role' => 'Admin',
                'shop_id' => null,
                'is_platform_admin' => 1,
                'updated_at' => $now,
            ];

            if (Schema::hasColumn('users', 'active_status')) {
                $payload['active_status'] = 1;
            }

            if (Schema::hasColumn('users', 'is_barber')) {
                $payload['is_barber'] = 0;
            }

            if ($existing) {
                unset($payload['password']); // keep existing password on re-run
                DB::table('users')->where('id', $existing->id)->update($payload);
            } else {
                $payload['created_at'] = $now;
                if (Schema::hasColumn('users', 'email_verified_at')) {
                    $payload['email_verified_at'] = $now;
                }
                DB::table('users')->insert($payload);
            }
        }
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'shop_id')) {
                try {
                    $table->dropForeign(['shop_id']);
                } catch (\Throwable $e) {
                    // ignore
                }
                $table->dropColumn('shop_id');
            }

            if (Schema::hasColumn('users', 'is_platform_admin')) {
                $table->dropColumn('is_platform_admin');
            }
        });
    }
};
