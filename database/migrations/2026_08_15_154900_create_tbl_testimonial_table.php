<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbl_testimonial', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->string('client_name');
            $table->string('client_role')->nullable();
            $table->text('review');
            $table->unsignedTinyInteger('rating')->default(5);
            $table->string('client_image')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });

        DB::table('tbl_testimonial')->insert([
            [
                'client_name' => 'James Carter',
                'client_role' => 'Regular Client',
                'review' => 'Best fade I\'ve had in years. Clean shop, amazing attention to detail, and the booking system is so easy.',
                'rating' => 5,
                'client_image' => 'frontend/images/luxury/customer.jpg',
                'sort_order' => 1,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'client_name' => 'Michael Reyes',
                'client_role' => 'Premium Package',
                'review' => 'Luxury vibe without being pretentious. Hot towel shave was incredible — I\'ll be back every month.',
                'rating' => 5,
                'client_image' => 'frontend/images/luxury/customer2.jpg',
                'sort_order' => 2,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'client_name' => 'David Nguyen',
                'client_role' => 'Beard Specialist Fan',
                'review' => 'Friendly barbers who really listen. My beard has never looked this sharp. Highly recommend.',
                'rating' => 5,
                'client_image' => 'frontend/images/luxury/customer3.jpg',
                'sort_order' => 3,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $permissions = [];
        foreach (['Testimonial-Add', 'Testimonial-Edit', 'Testimonial-Delete'] as $name) {
            $permissions[] = Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        $roles = Role::query()
            ->whereIn('name', ['Super Admin', 'Admin', 'super-admin', 'admin', 'SuperAdmin'])
            ->orWhereIn('id', [1, 2, 4])
            ->orWhereHas('permissions', function ($q) {
                $q->whereIn('name', ['Gallery-Add', 'Setting-Payment_Method']);
            })
            ->get();

        foreach ($roles as $role) {
            foreach ($permissions as $permission) {
                if (!$role->hasPermissionTo($permission)) {
                    $role->givePermissionTo($permission);
                }
            }
        }
    }

    public function down(): void
    {
        foreach (['Testimonial-Add', 'Testimonial-Edit', 'Testimonial-Delete'] as $name) {
            $permission = Permission::where('name', $name)->where('guard_name', 'web')->first();
            if ($permission) {
                DB::table('role_has_permissions')->where('permission_id', $permission->id)->delete();
                $permission->delete();
            }
        }

        Schema::dropIfExists('tbl_testimonial');
    }
};
