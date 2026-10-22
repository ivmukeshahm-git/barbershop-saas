<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    public function up(): void
    {
        $permission = Permission::firstOrCreate(
            ['name' => 'Report-View', 'guard_name' => 'web']
        );

        $roles = Role::query()
            ->whereIn('name', ['Super Admin', 'Admin', 'super-admin', 'admin', 'SuperAdmin'])
            ->orWhereIn('id', [1, 2, 4])
            ->orWhereHas('permissions', function ($q) {
                $q->where('name', 'Calender-View');
            })
            ->get();

        foreach ($roles as $role) {
            if (!$role->hasPermissionTo($permission)) {
                $role->givePermissionTo($permission);
            }
        }
    }

    public function down(): void
    {
        $permission = Permission::where('name', 'Report-View')->where('guard_name', 'web')->first();
        if ($permission) {
            DB::table('role_has_permissions')->where('permission_id', $permission->id)->delete();
            $permission->delete();
        }
    }
};
