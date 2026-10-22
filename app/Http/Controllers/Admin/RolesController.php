<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Support\CurrentShop;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Yajra\DataTables\DataTables;

class RolesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        abort_unless(auth()->user()->canAny(['Role-Add', 'Role-Edit']), 403);

        $PermissionData = Permission::query()
            ->where('guard_name', 'web')
            ->where('name', 'not like', 'platform-%')
            ->orderBy('name')
            ->get()
            ->groupBy(function ($item) {
                return explode('-', $item->name)[0];
            });

        return view('admin.roles.role', compact('PermissionData'));
    }

    public function GetTableData(Request $request)
    {
        abort_unless(auth()->user()->canAny(['Role-Add', 'Role-Edit']), 403);

        if (!$request->ajax()) {
            abort(404);
        }

        $shopId = CurrentShop::require()->id;
        $data = Role::query()
            ->select(['id', 'name', 'guard_name', 'created_at'])
            ->where('shop_id', $shopId)
            ->assignableForShop();

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $action = '';

                if (auth()->user()->can('Role-Edit')) {
                    $action .= '<button type="button" class="btn view-details-btn btn-sm rounded-pill btn-icon btn-warning" data-id="' . $row->id . '">
                        <span class="tf-icons bx bx-edit-alt bx-18px"></span>
                    </button>';
                }

                if (strcasecmp($row->name, 'Admin') !== 0 && auth()->user()->can('Role-Delete')) {
                    $action .= ' <button type="button" class="btn delete-role-btn btn-sm rounded-pill btn-icon btn-danger" data-id="' . $row->id . '" aria-label="Delete role">
                        <span class="tf-icons bx bx-trash bx-18px"></span>
                    </button>';
                }

                return $action;
            })
            ->filter(function ($query) use ($request) {
                if ($search = $request->get('search')['value'] ?? '') {
                    $query->where(function ($sub) use ($search) {
                        $sub->where('name', 'like', "%{$search}%");
                    });
                }
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function getRolePermissions(Request $request)
    {
        abort_unless(auth()->user()->can('Role-Edit'), 403);

        try {
            $role = $this->findShopRoleOrFail($request->route('id'));
            $permissions = $role->permissions->pluck('id');

            return response()->json([
                'status' => 1,
                'permissions' => $permissions,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => 'Failed to fetch role permissions.',
            ]);
        }
    }

    public function create()
    {
        abort_unless(auth()->user()->can('Role-Add'), 403);
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()->can('Role-Add'), 403);

        $shopId = CurrentShop::id();
        if (!$shopId) {
            return response()->json(['status' => 0, 'message' => 'Shop context missing.'], 422);
        }

        $request->validate([
            'role_nm' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles', 'name')->where(fn ($q) => $q->where('shop_id', $shopId)->where('guard_name', 'web')),
            ],
            'permissions' => 'nullable|array',
            'permissions.*' => [
                Rule::exists('permissions', 'id')->where(function ($query) {
                    $query->where('guard_name', 'web')->where('name', 'not like', 'platform-%');
                }),
            ],
        ]);

        try {
            $role = Role::create([
                'shop_id' => $shopId,
                'name' => $request->input('role_nm'),
                'guard_name' => 'web',
            ]);

            if ($request->has('permissions')) {
                $permissionNames = Permission::where('guard_name', 'web')
                    ->where('name', 'not like', 'platform-%')
                    ->whereIn('id', $request->permissions)
                    ->pluck('name');
                $role->syncPermissions($permissionNames);
            }

            app(PermissionRegistrar::class)->forgetCachedPermissions();
            return response()->json(['status' => 1, 'message' => 'Role created successfully.']);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => 'Failed to create role.',
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function show($id)
    {
        abort_unless(auth()->user()->can('Role-Edit'), 403);
    }

    public function edit($id)
    {
        abort_unless(auth()->user()->can('Role-Edit'), 403);
    }

    public function update(Request $request)
    {
        abort_unless(auth()->user()->can('Role-Edit'), 403);

        $shopId = CurrentShop::id();
        $role = $this->findShopRoleOrFail($request->route('role'));

        $protectedRole = in_array($role->name, ['Staff', 'Accountant', 'Admin', 'Super-admin', 'Super Admin'], true);

        $request->validate([
            'role_nm' => [
                'required',
                'string',
                'max:255',
                $protectedRole
                    ? Rule::in([$role->name])
                    : Rule::unique('roles', 'name')
                        ->ignore($role->id)
                        ->where(fn ($q) => $q->where('shop_id', $shopId)->where('guard_name', 'web')),
            ],
            'permissions' => 'nullable|array',
            'permissions.*' => [
                Rule::exists('permissions', 'id')->where(function ($query) {
                    $query->where('guard_name', 'web')->where('name', 'not like', 'platform-%');
                }),
            ],
        ]);

        try {
            if (!$protectedRole) {
                $role->name = $request->input('role_nm');
                $role->save();
            }

            if ($request->has('permissions')) {
                $permissionNames = Permission::where('guard_name', 'web')
                    ->where('name', 'not like', 'platform-%')
                    ->whereIn('id', $request->permissions)
                    ->pluck('name');
                $role->syncPermissions($permissionNames);
            } else {
                $role->syncPermissions([]);
            }

            app(PermissionRegistrar::class)->forgetCachedPermissions();
            return response()->json(['status' => 1, 'message' => 'Role updated successfully.']);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => 'Failed to update role.',
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function destroy(Request $request)
    {
        abort_unless(auth()->user()->can('Role-Delete'), 403);

        $role = $this->findShopRoleOrFail($request->route('role'));
        abort_if(strcasecmp($role->name, 'Admin') === 0, 403, 'The Admin role cannot be deleted.');

        $role->delete();
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return response()->json([
            'status' => 1,
            'message' => 'Role deleted successfully.',
        ]);
    }

    protected function findShopRoleOrFail($id): Role
    {
        $shopId = CurrentShop::require()->id;

        return Role::query()
            ->where('shop_id', $shopId)
            ->assignableForShop()
            ->findOrFail($id);
    }
}
