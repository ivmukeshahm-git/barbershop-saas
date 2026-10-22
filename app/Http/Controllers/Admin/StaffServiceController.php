<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\StaffService;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use App\Support\CurrentShop;

class StaffServiceController extends Controller
{
    public function index()
    {
        StaffService::backfillFromLegacy();

        $services = Service::orderBy('service_name')->get();
        $barbers = User::where('role', 'Staff')
            ->where('is_barber', 1)
            ->whereNull('deleted_at')
            ->forCurrentShop()
            ->orderBy('name')
            ->get();

        return view('admin.staff.staff-service', compact('services', 'barbers'));
    }

    public function GetTableData(Request $request)
    {
        if (!$request->ajax()) {
            abort(404);
        }

        StaffService::backfillFromLegacy();

        $query = DB::table('users as u')
            ->leftJoin('tbl_staff_service as ss', 'ss.staff_id', '=', 'u.id')
            ->leftJoin('tbl_service as s', function ($join) {
                $join->on('s.id', '=', 'ss.service_id')
                    ->whereNull('s.deleted_at');
            })
            ->where('u.role', 'Staff')
            ->whereNull('u.deleted_at')
            ->select(
                'u.id',
                'u.name',
                'u.email',
                'u.is_barber',
                'u.active_status',
                'u.service_id as legacy_service_id',
                DB::raw('GROUP_CONCAT(DISTINCT s.service_name ORDER BY s.service_name SEPARATOR ", ") as service_names'),
                DB::raw('GROUP_CONCAT(DISTINCT CAST(ss.service_id AS UNSIGNED) ORDER BY ss.service_id SEPARATOR ",") as service_ids')
            )
            ->groupBy('u.id', 'u.name', 'u.email', 'u.is_barber', 'u.active_status', 'u.service_id');
        CurrentShop::constrain($query, 'u.shop_id');

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('service_names', function ($row) {
                return $row->service_names ?: '—';
            })
            ->addColumn('is_barber_label', function ($row) {
                return $row->is_barber
                    ? '<span class="badge bg-success">Barber</span>'
                    : '<span class="badge bg-secondary">Non-Barber</span>';
            })
            ->addColumn('staff_status', function ($row) {
                return $row->active_status
                    ? '<span class="badge bg-success">Active</span>'
                    : '<span class="badge bg-danger">Deactive</span>';
            })
            ->addColumn('action', function ($row) {
                return '<button type="button" class="btn btn-sm btn-icon btn-warning assign-services-btn mx-1" data-id="' . $row->id . '" data-name="' . e($row->name) . '" title="Assign Services">
                    <span class="tf-icons bx bx-sitemap bx-18px"></span>
                </button>';
            })
            ->filter(function ($query) use ($request) {
                if ($search = $request->get('search')['value'] ?? null) {
                    $query->where(function ($q) use ($search) {
                        $q->where('u.name', 'like', "%{$search}%")
                            ->orWhere('u.email', 'like', "%{$search}%")
                            ->orWhere('s.service_name', 'like', "%{$search}%");
                    });
                }
            })
            ->rawColumns(['is_barber_label', 'staff_status', 'action'])
            ->make(true);
    }

    public function getAssignedServices(Request $request)
    {
        $staffId = (int) $request->get('staff_id');
        $staff = User::where('id', $staffId)->where('role', 'Staff')->whereNull('deleted_at')->forCurrentShop()->first();

        if (!$staff) {
            return response()->json(['status' => 0, 'message' => 'Staff not found']);
        }

        $assigned = StaffService::idsForStaff($staffId);

        return response()->json([
            'status' => 1,
            'staff_id' => $staff->id,
            'is_barber' => (int) $staff->is_barber,
            'service_ids' => $assigned,
        ]);
    }

    public function assignServices(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'staff_id' => 'required|exists:users,id',
        ], [
            'staff_id.required' => 'Please select a staff member',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 0,
                'errors' => $validator->errors(),
            ]);
        }

        $serviceIds = $request->input('service_ids', []);
        if (!is_array($serviceIds)) {
            $serviceIds = array_filter(array_map('trim', explode(',', $serviceIds)));
        }
        $serviceIds = array_filter($serviceIds, 'is_numeric');

        StaffService::syncForStaff((int) $request->staff_id, $serviceIds);

        return response()->json([
            'status' => 1,
            'message' => 'Services assigned successfully',
        ]);
    }
}