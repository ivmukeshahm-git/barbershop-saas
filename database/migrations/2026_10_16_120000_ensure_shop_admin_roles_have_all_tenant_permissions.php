<?php

use App\Models\Role;
use App\Models\SaasShop;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    public function up(): void
    {
        if (
            !Schema::hasTable('saas_shops')
            || !Schema::hasTable('roles')
            || !Schema::hasColumn('roles', 'shop_id')
            || !Schema::hasTable('permissions')
            || !Schema::hasTable('role_has_permissions')
        ) {
            return;
        }

        $permissionNames = Permission::query()
            ->where('guard_name', 'web')
            ->where('name', 'not like', 'platform-%')
            ->orderBy('name')
            ->pluck('name')
            ->all();

        SaasShop::query()->orderBy('id')->each(function (SaasShop $shop) use ($permissionNames): void {
            $adminRole = Role::ensureForShop((int) $shop->id, 'Admin');
            $adminRole->syncPermissions($permissionNames);

            if (!Schema::hasColumn('users', 'shop_id')) {
                return;
            }

            User::query()
                ->where('shop_id', $shop->id)
                ->where(function ($query) use ($shop): void {
                    $query->where('id', $shop->owner_user_id)
                        ->orWhere('role', 'Admin');
                })
                ->each(function (User $owner) use ($adminRole): void {
                    $owner->syncRoles([$adminRole]);
                });
        });

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        // Keep shop Admin roles and grants so existing tenant access is not revoked.
    }
};
