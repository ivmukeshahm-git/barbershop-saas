<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    /**
     * Account-related permissions for the Accountant role.
     */
    private array $accountantPermissions = [
        'Report-View',
        'Pos_invoice-Add',
        'Pos_invoice-Edit',
        'Gst-Add',
        'Gst-Edit',
        'Gst-Delete',
        'Setting-Payment_Method',
        'Customer-Add',
        'Customer-Edit',
    ];

    public function up(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        foreach ($this->accountantPermissions as $name) {
            Permission::firstOrCreate([
                'name' => $name,
                'guard_name' => 'web',
            ]);
        }

        $role = Role::firstOrCreate([
            'name' => 'Accountant',
            'guard_name' => 'web',
        ]);

        $role->syncPermissions($this->accountantPermissions);

        $email = 'accountant@rosenthalbarbershop.ca';

        $user = User::withTrashed()->where('email', $email)->first();

        if (!$user) {
            $user = User::create([
                'name' => 'Accountant',
                'email' => $email,
                'password' => Hash::make('Accountant@123'),
                'role' => 'Accountant',
                'active_status' => 1,
                'is_barber' => 0,
                'commission' => 0,
            ]);
        } else {
            if ($user->trashed()) {
                $user->restore();
            }

            $user->forceFill([
                'name' => $user->name ?: 'Accountant',
                'role' => 'Accountant',
                'active_status' => 1,
                'is_barber' => 0,
            ])->save();
        }

        if (!$user->hasRole('Accountant')) {
            $user->syncRoles(['Accountant']);
        }
    }

    public function down(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $email = 'accountant@rosenthalbarbershop.ca';
        $user = User::where('email', $email)->first();

        if ($user) {
            $user->syncRoles([]);
            $user->delete();
        }

        $role = Role::where('name', 'Accountant')->where('guard_name', 'web')->first();
        if ($role) {
            DB::table('role_has_permissions')->where('role_id', $role->id)->delete();
            $role->delete();
        }
    }
};
