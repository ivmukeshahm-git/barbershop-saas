<?php

use App\Models\Role;
use App\Models\SaasShop;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('roles') || !Schema::hasColumn('roles', 'shop_id')) {
            return;
        }

        $newPermissionNames = [
            'Appointment-View',
            'Calender-View',
            'Appointment-Confirm',
            'Appointment-Cancel',
            'Appointment-Reschedule',
        ];
        foreach ($newPermissionNames as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        $tenantPermissionNames = Permission::query()
            ->where('guard_name', 'web')
            ->get()
            ->reject(fn (Permission $permission) => str_starts_with(strtolower($permission->name), 'platform-'))
            ->pluck('name')
            ->all();

        SaasShop::query()->orderBy('id')->each(function (SaasShop $shop) use ($tenantPermissionNames) {
            $adminRole = Role::ensureForShop($shop->id, 'Admin');
            $adminRole->syncPermissions($tenantPermissionNames);
        });

        Role::query()
            ->where('name', 'Staff')
            ->each(fn (Role $role) => $role->revokePermissionTo(['Report-View', 'Testimonial-Delete']));

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        // Keep the new permissions and corrected default roles to avoid revoking active grants.
    }
};
