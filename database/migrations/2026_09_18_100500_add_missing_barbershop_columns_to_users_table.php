<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ensure mixed schemas have columns the barbershop SaaS app expects.
     */
    public function up()
    {
        if (!Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'active_status')) {
                $table->boolean('active_status')->default(1);
            }

            if (!Schema::hasColumn('users', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable();
            }

            if (!Schema::hasColumn('users', 'service_id')) {
                $table->string('service_id')->nullable();
            }

            if (!Schema::hasColumn('users', 'commission')) {
                $table->decimal('commission', 8, 2)->nullable()->default(0);
            }

            if (!Schema::hasColumn('users', 'is_barber')) {
                $table->boolean('is_barber')->default(0);
            }

            if (!Schema::hasColumn('users', 'profile_img')) {
                $table->string('profile_img')->nullable();
            }

            if (!Schema::hasColumn('users', 'morning_start_time')) {
                $table->time('morning_start_time')->nullable();
            }

            if (!Schema::hasColumn('users', 'morning_end_time')) {
                $table->time('morning_end_time')->nullable();
            }

            if (!Schema::hasColumn('users', 'evning_start_time')) {
                $table->time('evning_start_time')->nullable();
            }

            if (!Schema::hasColumn('users', 'evning_end_time')) {
                $table->time('evning_end_time')->nullable();
            }

            if (!Schema::hasColumn('users', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        if (Schema::hasColumn('users', 'status') && Schema::hasColumn('users', 'active_status')) {
            // status may be string (Active) or numeric — sync safely in PHP.
            DB::table('users')->orderBy('id')->chunkById(200, function ($rows) {
                foreach ($rows as $row) {
                    $raw = strtolower(trim((string) ($row->status ?? '1')));
                    $active = in_array($raw, ['0', 'inactive', 'disabled', 'blocked'], true) ? 0 : 1;
                    DB::table('users')->where('id', $row->id)->update(['active_status' => $active]);
                }
            });
        } elseif (Schema::hasColumn('users', 'active_status')) {
            DB::table('users')->whereNull('active_status')->update(['active_status' => 1]);
        }

        // Seed sensible defaults for demo shop staff rows that are missing hours/flags.
        $shopId = Schema::hasTable('saas_shops')
            ? DB::table('saas_shops')->where('slug', 'rosenthal')->value('id')
            : null;

        if ($shopId) {
            $staffDefaults = [
                'updated_at' => now(),
            ];

            if (Schema::hasColumn('users', 'active_status')) {
                $staffDefaults['active_status'] = 1;
            }
            if (Schema::hasColumn('users', 'commission')) {
                // leave commission as-is when already set; only fill nulls below
            }
            if (Schema::hasColumn('users', 'morning_start_time')) {
                $staffDefaults['morning_start_time'] = '09:00:00';
            }
            if (Schema::hasColumn('users', 'morning_end_time')) {
                $staffDefaults['morning_end_time'] = '13:00:00';
            }
            if (Schema::hasColumn('users', 'evning_start_time')) {
                $staffDefaults['evning_start_time'] = '14:00:00';
            }
            if (Schema::hasColumn('users', 'evning_end_time')) {
                $staffDefaults['evning_end_time'] = '18:00:00';
            }

            $query = DB::table('users')->where('shop_id', $shopId)->where('role', 'Staff');

            if (Schema::hasColumn('users', 'is_barber')) {
                DB::table('users')
                    ->where('shop_id', $shopId)
                    ->where('role', 'Staff')
                    ->where(function ($q) {
                        $q->whereNull('is_barber')->orWhere('is_barber', 0);
                    })
                    ->update(['is_barber' => 1, 'updated_at' => now()]);
            }

            if (Schema::hasColumn('users', 'commission')) {
                DB::table('users')
                    ->where('shop_id', $shopId)
                    ->whereNull('commission')
                    ->update(['commission' => 0, 'updated_at' => now()]);
            }

            if (Schema::hasColumn('users', 'morning_start_time')) {
                $query->whereNull('morning_start_time')->update($staffDefaults);
            }
        }
    }

    public function down()
    {
        // Keep columns.
    }
};
