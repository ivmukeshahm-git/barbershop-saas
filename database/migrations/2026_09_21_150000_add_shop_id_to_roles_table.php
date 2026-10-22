<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Make Spatie roles shop-scoped so each tenant manages its own roles.
 * Global roles (shop_id null) stay platform-only (e.g. Super-admin).
 */
return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('roles') || Schema::hasColumn('roles', 'shop_id')) {
            return;
        }

        Schema::table('roles', function (Blueprint $table) {
            $table->unsignedBigInteger('shop_id')->nullable()->after('id')->index();
        });

        // Drop default unique(name, guard_name) so same role name can exist per shop.
        $this->dropNameUniqueIfExists();

        Schema::table('roles', function (Blueprint $table) {
            $table->unique(['shop_id', 'name', 'guard_name'], 'roles_shop_name_guard_unique');
        });

        if (!Schema::hasTable('saas_shops')) {
            return;
        }

        $shops = DB::table('saas_shops')->orderBy('id')->get(['id']);
        if ($shops->isEmpty()) {
            return;
        }

        // Keep Super-admin (and similarly named) as global templates (shop_id null).
        $globalNames = ['Super-admin', 'Super Admin', 'Admin', 'Platform Admin'];
        $templates = DB::table('roles')
            ->whereNull('shop_id')
            ->whereNotIn('name', $globalNames)
            ->get();

        // If everything is still null, treat non-global as templates to clone.
        if ($templates->isEmpty()) {
            $templates = DB::table('roles')
                ->whereNotIn('name', $globalNames)
                ->get();
        }

        foreach ($shops as $shop) {
            foreach ($templates as $template) {
                $exists = DB::table('roles')
                    ->where('shop_id', $shop->id)
                    ->where('name', $template->name)
                    ->where('guard_name', $template->guard_name)
                    ->exists();

                if ($exists) {
                    continue;
                }

                $newId = DB::table('roles')->insertGetId([
                    'shop_id' => $shop->id,
                    'name' => $template->name,
                    'guard_name' => $template->guard_name,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $permIds = DB::table('role_has_permissions')
                    ->where('role_id', $template->id)
                    ->pluck('permission_id');

                foreach ($permIds as $permissionId) {
                    DB::table('role_has_permissions')->insert([
                        'permission_id' => $permissionId,
                        'role_id' => $newId,
                    ]);
                }
            }
        }

        // Point existing shop users at their shop's role copy when possible.
        if (Schema::hasColumn('users', 'shop_id') && Schema::hasTable('model_has_roles')) {
            $userRoles = DB::table('model_has_roles as mhr')
                ->join('users as u', function ($join) {
                    $join->on('u.id', '=', 'mhr.model_id')
                        ->where('mhr.model_type', '=', 'App\\Models\\User');
                })
                ->join('roles as r', 'r.id', '=', 'mhr.role_id')
                ->whereNotNull('u.shop_id')
                ->where(function ($q) {
                    $q->whereNull('r.shop_id')->orWhereColumn('r.shop_id', '<>', 'u.shop_id');
                })
                ->whereNotIn('r.name', $globalNames)
                ->select('mhr.role_id', 'mhr.model_id', 'mhr.model_type', 'u.shop_id', 'r.name', 'r.guard_name')
                ->get();

            foreach ($userRoles as $row) {
                $shopRoleId = DB::table('roles')
                    ->where('shop_id', $row->shop_id)
                    ->where('name', $row->name)
                    ->where('guard_name', $row->guard_name)
                    ->value('id');

                if (!$shopRoleId || (int) $shopRoleId === (int) $row->role_id) {
                    continue;
                }

                // Avoid duplicate primary key if already linked.
                $already = DB::table('model_has_roles')
                    ->where('role_id', $shopRoleId)
                    ->where('model_id', $row->model_id)
                    ->where('model_type', $row->model_type)
                    ->exists();

                if (!$already) {
                    DB::table('model_has_roles')->insert([
                        'role_id' => $shopRoleId,
                        'model_type' => $row->model_type,
                        'model_id' => $row->model_id,
                    ]);
                }

                DB::table('model_has_roles')
                    ->where('role_id', $row->role_id)
                    ->where('model_id', $row->model_id)
                    ->where('model_type', $row->model_type)
                    ->delete();
            }
        }

        // Keep original roles as global templates (shop_id null) for cloning on new signups.
        // Shop UI only lists roles where shop_id = current shop.
    }

    public function down()
    {
        if (!Schema::hasTable('roles') || !Schema::hasColumn('roles', 'shop_id')) {
            return;
        }

        Schema::table('roles', function (Blueprint $table) {
            $table->dropUnique('roles_shop_name_guard_unique');
            $table->dropColumn('shop_id');
        });
    }

    protected function dropNameUniqueIfExists(): void
    {
        try {
            Schema::table('roles', function (Blueprint $table) {
                $table->dropUnique('roles_name_guard_name_unique');
            });
        } catch (\Throwable $e) {
            // Index name may differ across installs; try common alternatives.
            try {
                DB::statement('ALTER TABLE `roles` DROP INDEX `roles_name_guard_name_unique`');
            } catch (\Throwable $e2) {
                try {
                    // Some schemas use a different unique index name.
                    $indexes = DB::select('SHOW INDEX FROM `roles` WHERE Non_unique = 0 AND Column_name = ?', ['name']);
                    foreach ($indexes as $index) {
                        if ($index->Key_name === 'PRIMARY') {
                            continue;
                        }
                        try {
                            DB::statement('ALTER TABLE `roles` DROP INDEX `' . $index->Key_name . '`');
                        } catch (\Throwable $e3) {
                            // ignore
                        }
                    }
                } catch (\Throwable $e3) {
                    // ignore
                }
            }
        }
    }
};
