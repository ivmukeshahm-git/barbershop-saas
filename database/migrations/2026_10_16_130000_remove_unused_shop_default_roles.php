<?php

use App\Models\Role;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    public function up(): void
    {
        if (
            !Schema::hasTable('roles')
            || !Schema::hasColumn('roles', 'shop_id')
            || !Schema::hasTable('model_has_roles')
        ) {
            return;
        }

        Role::query()
            ->whereNotNull('shop_id')
            ->where('guard_name', 'web')
            ->whereIn('name', ['Staff', 'Accountant', 'Manager', 'POS Manager'])
            ->orderBy('id')
            ->get()
            ->each(function (Role $role): void {
                $isAssigned = DB::table('model_has_roles')
                    ->where('role_id', $role->id)
                    ->exists();

                if (!$isAssigned) {
                    $role->delete();
                }
            });

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        // Removed default roles are intentionally not recreated.
    }
};
