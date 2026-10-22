<?php

use App\Models\Role;
use App\Models\SaasShop;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

/**
 * Ensure every shop has an Admin role with Super-admin permissions,
 * and reassign shop owners from the global Super-admin role onto that shop Admin.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('roles') || !Schema::hasColumn('roles', 'shop_id')) {
            return;
        }

        $template = Role::findGlobalByName('Super-admin')
            ?? Role::findGlobalByName('Super Admin')
            ?? Role::findGlobalByName('Admin');

        $permissionNames = $template
            ? $template->permissions()->pluck('name')->all()
            : Permission::pluck('name')->all();

        SaasShop::query()->orderBy('id')->each(function (SaasShop $shop) use ($template, $permissionNames) {
            $adminRole = Role::ensureForShop($shop->id, 'Admin', $template);
            if (!empty($permissionNames)) {
                $adminRole->syncPermissions($permissionNames);
            }

            $owners = User::query()
                ->where('shop_id', $shop->id)
                ->where(function ($q) use ($shop) {
                    $q->where('id', $shop->owner_user_id)
                        ->orWhere('role', 'Admin');
                })
                ->get();

            foreach ($owners as $owner) {
                $owner->syncRoles([$adminRole]);
            }
        });

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        // Keep shop Admin roles — irreversible by design.
    }
};
