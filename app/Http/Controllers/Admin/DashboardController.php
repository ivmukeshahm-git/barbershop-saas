<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Service;
use App\Support\CurrentShop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;


class DashboardController extends Controller
{
    public function index()
    {
        $user = User::find(Auth::id());


        $StaffList = User::barbers()->forCurrentShop()->get();
        $calendarStaffList = $user->role === 'Staff'
            ? User::barbers()->forCurrentShop()->where('id', $user->id)->get()
            : $StaffList;
        $services = Service::get();
         $products = DB::table('tbl_product as p')
            ->leftJoin('tbl_appointment_product as ap', 'p.id', '=', 'ap.product_id')
            ->select(
                'p.id',
                'p.product_name',
                DB::raw('COALESCE(SUM(ap.qty), 0) as total_sold')
            )
            ->groupBy('p.id', 'p.product_name')
            ->orderByDesc('total_sold');
        CurrentShop::constrain($products, 'p.shop_id');
        $products = $products->get();
        $serviceProductCommision = 0;


        $totalAppointment = DB::table('tbl_appointment as a')
            ->join('tbl_appointment_service as s', 'a.appointment_id', '=', 's.appointment_id')
            ->when($user->role === 'Staff', function ($query) use ($user) {
                return $query->where('s.staff_id', $user->id);
            })
            ->where('a.date', '>', Carbon::today()->toDateString())
            ->where('a.is_invoiced', null)
            ->distinct();
        CurrentShop::constrain($totalAppointment, 'a.shop_id');
        $totalAppointment = $totalAppointment->count('a.appointment_id');


        $totalCustomers = CurrentShop::constrain(DB::table('customers'), 'shop_id')
            ->count('customer_id');



        $CompleteAppointments = DB::table('tbl_appointment as a')
            ->join('tbl_appointment_service as s', 'a.appointment_id', '=', 's.appointment_id')
            ->where('a.status', 1)
            ->when($user->role === 'Staff', function ($query) use ($user) {
                return $query->where('s.staff_id', $user->id);
            })
            ->distinct();
        CurrentShop::constrain($CompleteAppointments, 'a.shop_id');
        $CompleteAppointments = $CompleteAppointments->count('a.appointment_id');

        $CancelAppointments = DB::table('tbl_appointment as a')
            ->join('tbl_appointment_service as s', 'a.appointment_id', '=', 's.appointment_id')
            ->where('a.status', 2)
            ->when($user->role === 'Staff', function ($query) use ($user) {
                return $query->where('s.staff_id', $user->id);
            })
            ->distinct();
        CurrentShop::constrain($CancelAppointments, 'a.shop_id');
        $CancelAppointments = $CancelAppointments->count('a.appointment_id');

        $ConfirmAppointments = DB::table('tbl_appointment as a')
            ->join('tbl_appointment_service as s', 'a.appointment_id', '=', 's.appointment_id')
            ->where('a.status', 3)
            ->when($user->role === 'Staff', function ($query) use ($user) {
                return $query->where('s.staff_id', $user->id);
            })
            ->distinct();
        CurrentShop::constrain($ConfirmAppointments, 'a.shop_id');
        $ConfirmAppointments = $ConfirmAppointments->count('a.appointment_id');

        $RescheduleAppointment = DB::table('tbl_appointment as a')
            ->join('tbl_appointment_service as s', 'a.appointment_id', '=', 's.appointment_id')
            ->where('a.status', 4)
            ->when($user->role === 'Staff', function ($query) use ($user) {
                return $query->where('s.staff_id', $user->id);
            })
            ->distinct();
        CurrentShop::constrain($RescheduleAppointment, 'a.shop_id');
        $RescheduleAppointment = $RescheduleAppointment->count('a.appointment_id');

        if ($user->role == "Admin") {
            $totalRevenue = CurrentShop::constrain(DB::table('tbl_appointment'), 'shop_id')
                ->where('is_invoiced', 1)
                ->select(DB::raw('SUM(total_amt) as total_revenue'))
                ->first();


            $totalRevenue = $totalRevenue->total_revenue ?? 0;


            $totalTipRevenue = DB::table('tbl_appointment as a')
                ->where('a.is_invoiced', 1);
            CurrentShop::constrain($totalTipRevenue, 'a.shop_id');
            $totalTipRevenue = $totalTipRevenue->sum('a.total_tip');
        } else {
            $totalTipRevenue = DB::table('tbl_appointment_service as s')
                ->where('s.staff_id', Auth::id());  // ← correct column name
            CurrentShop::constrain($totalTipRevenue, 's.shop_id');
            $totalTipRevenue = $totalTipRevenue->sum('s.service_tip');

            $totalServiceCommision = DB::table('tbl_appointment_service as s')
                ->where('s.staff_id', Auth::id());
            CurrentShop::constrain($totalServiceCommision, 's.shop_id');
            $totalServiceCommision = $totalServiceCommision->sum('s.service_commission');

            $totalProductCommision = DB::table('tbl_appointment_product as p')
                ->where('p.staff_id', Auth::id());  // ← correct column name
            CurrentShop::constrain($totalProductCommision, 'p.shop_id');
            $totalProductCommision = $totalProductCommision->sum('p.product_commision');


            $serviceProductCommision = $totalServiceCommision + $totalProductCommision;
            // Total Revenue
            $totalRevenue = $totalTipRevenue + $totalServiceCommision + $totalProductCommision;
        }

        return view('admin.dashboard.index', compact('StaffList', 'calendarStaffList', 'services', 'totalRevenue', 'totalAppointment', 'totalCustomers', 'totalTipRevenue', 'CompleteAppointments', 'CancelAppointments', 'ConfirmAppointments', 'RescheduleAppointment', 'serviceProductCommision','products'));
    }

    public function TopServicesGetTableData(Request $request)
    {
        if ($request->ajax()) {
            $data = DB::table('tbl_appointment_service as b')
                ->join('tbl_service as c', 'b.service_id', '=', 'c.id')
                ->join('tbl_appointment as d', 'b.appointment_id', '=', 'd.appointment_id')
                ->select(
                    'c.service_name',
                    DB::raw('SUM(b.total_amt) as totalRevenue'),
                    DB::raw('COUNT(DISTINCT b.appointment_id) as appointmentCount') // count unique appointments
                )
                ->groupBy('c.service_name')
                ->orderByDesc('totalRevenue');
            CurrentShop::constrain($data, 'b.shop_id');


            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('totalRevenue', function ($row) {
                    // Format revenue as currency like $3,015.90
                    return shop_money($row->totalRevenue);
                })
                ->editColumn('appointmentCount', function ($row) {
                    return $row->appointmentCount;
                })
                ->filter(function ($query) use ($request) {
                    if ($search = $request->get('search')['value'] ?? '') {
                        $query->where(function ($sub) use ($search) {
                            $sub->where('c.service_name', 'like', "%{$search}%");
                        });
                    }
                })
                ->rawColumns(['totalRevenue', 'appointmentCount'])
                ->make(true);
        }
    }


    public function getStaffPerformance(Request $request)
    {
        if ($request->ajax()) {
            $data = DB::table('tbl_appointment as a')
                ->join('tbl_appointment_service as b', 'a.appointment_id', '=', 'b.appointment_id')
                ->join('users as s', 'b.staff_id', '=', 's.id')
                ->select(
                    's.id as staff_id',
                    's.name as staff_name',
                    DB::raw('SUM(b.total_amt) as totalRevenue'),
                    DB::raw('COALESCE(SUM(b.service_commission),0) as totalCommission'),
                    DB::raw('COUNT(DISTINCT a.appointment_id) as appointmentCount')
                )
                ->groupBy('s.id', 's.name')
                ->orderByDesc('totalRevenue');
            CurrentShop::constrain($data, 'a.shop_id');

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('totalRevenue', function ($row) {
                    return shop_money($row->totalRevenue);
                })
                ->editColumn('totalCommission', function ($row) {
                    return shop_money($row->totalCommission);
                })
                ->filter(function ($query) use ($request) {
                    if ($search = $request->get('search')['value'] ?? '') {
                        $query->where(function ($sub) use ($search) {
                            $sub->where('s.name', 'like', "%{$search}%");
                        });
                    }
                })
                ->rawColumns(['totalRevenue', 'totalCommission'])
                ->make(true);
        }
    }


    public function GetTodayGetTableData(Request $request)
    {
        if ($request->ajax()) {
            $user = User::find(Auth::id());

            $today = Carbon::today()->toDateString(); // e.g. 2025-09-09
            $hasFirstAvailableFlag = Schema::hasColumn('tbl_appointment_service', 'is_first_available');
            $staffServiceLinesSql = $hasFirstAvailableFlag
                ? "GROUP_CONCAT(DISTINCT CONCAT(
                        CASE WHEN COALESCE(s.is_first_available, 0) = 1 THEN 'First available' ELSE u.name END,
                        ': ',
                        t.service_name
                    ) ORDER BY s.id SEPARATOR ' | ') as staff_service_lines"
                : "GROUP_CONCAT(DISTINCT CONCAT(u.name, ': ', t.service_name) ORDER BY s.id SEPARATOR ' | ') as staff_service_lines";

            // Main query
            $query = DB::table('tbl_appointment as a')
                ->leftJoin('tbl_appointment_service as s', 'a.appointment_id', '=', 's.appointment_id')
                ->leftJoin('users as u', function ($join) {
                    $join->on('s.staff_id', '=', 'u.id')
                        ->where('u.role', '=', 'Staff');
                })
                ->leftJoin('tbl_service as t', 's.service_id', '=', 't.id')
                ->leftJoin('customers as c', 'a.customer_id', '=', 'c.customer_id')
                ->select(
                    'a.appointment_id',
                    'a.date',
                    'a.slot_start',
                    'a.slot_end',
                    'a.status',
                    'c.customer_name',
                    'c.customer_phone',
                    DB::raw('GROUP_CONCAT(DISTINCT u.name SEPARATOR ", ") as staff_names'),
                    DB::raw('GROUP_CONCAT(DISTINCT s.staff_id) as staff_ids'),
                    DB::raw('GROUP_CONCAT(DISTINCT s.service_id) as service_ids'),
                    DB::raw($staffServiceLinesSql)
                )
                ->when(in_array($user->role, ['Staff']), function ($query) use ($user) {
                    return $query->where('s.staff_id', $user->id);
                })
                ->where('a.date', $today)
                ->where(function ($q) {
                    $q->where('a.is_invoiced', 0)
                        ->orWhereNull('a.is_invoiced');
                })
                ->groupBy(
                    'a.appointment_id',
                    'c.customer_name',
                    'c.customer_phone',
                    'a.date',
                    'a.slot_start',
                    'a.slot_end',
                    'a.status'
                );
            CurrentShop::constrain($query, 'a.shop_id');


            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('slot_time', function ($row) {
    $start = \Carbon\Carbon::parse($row->slot_start)->format('h:i A');
    $end = \Carbon\Carbon::parse($row->slot_end)->format('h:i A');
    return $start . ' - ' . $end;
})
                ->addColumn('status_badge', function ($row) {
                    $statusMap = [
                        0 => ['label' => 'Pending', 'class' => 'bg-warning'],
                        1 => ['label' => 'Complete', 'class' => 'bg-success'],
                        2 => ['label' => 'Reject', 'class' => 'bg-danger'],
                        3 => ['label' => 'Confirm', 'class' => 'bg-primary'],
                        4 => ['label' => 'Reschedule', 'class' => 'bg-info'],
                    ];
                    $status = $statusMap[$row->status] ?? ['label' => 'Unknown', 'class' => 'bg-secondary'];
                    return "<span class='badge {$status['class']} p-2'>{$status['label']}</span>";
                })
                ->addColumn('action', function ($row) {
                    $action  = '';
                    $canConfirm = auth()->user()->can('Appointment-Confirm');
                    $canCancel = auth()->user()->can('Appointment-Cancel');
                    $canReschedule = auth()->user()->can('Appointment-Reschedule');
                    if ($row->status == 1) {
                        $action .= ' <a class="btn btn-dark p-2" title="Invoice" href="' . route('admin.invoice.edit', ['shop' => CurrentShop::slug(), 'id' => $row->appointment_id]) . '">
                        <i class="fa fa-file"></i>
                    </a>';
                    } else if ($row->status == 2) {
                        if ($canReschedule) {
                            $action .= ' <button class="btn btn-info p-2" title="Reschedule" onclick="RescheduleAppointment(\'' . $row->appointment_id . '\', \'' . $row->staff_ids . '\', \'' . $row->service_ids . '\')">
                        <i class="fa fa-calendar-check-o"></i>
                    </button>';
                        }
                    } else if ($row->status == 3) {
                        if ($canCancel) {
                            $action .= ' <button class="btn btn-danger p-2" title="Cancel" onclick="UpdateAppointmentStatus(\'Cancel\', \'' . $row->appointment_id . '\')">
                        <i class="fa fa-times"></i>
                    </button>';
                        }
                        $action .= ' <a class="btn btn-success p-2" title="Complete" href="' . route('admin.invoice.edit', ['shop' => CurrentShop::slug(), 'id' => $row->appointment_id]) . '">
                        <i class="fa fa-check-square-o"></i>
                    </a>';
                    } else if ($row->status == 4) {
                        if ($canConfirm) {
                            $action .= ' <button class="btn btn-primary p-2" title="Confirm" onclick="UpdateAppointmentStatus(\'Confirm\', \'' . $row->appointment_id . '\')">
                        <i class="fa fa-check"></i>
                    </button>';
                        }

                        $action .= ' <a class="btn btn-success p-2" title="Complete" href="' . route('admin.invoice.edit', ['shop' => CurrentShop::slug(), 'id' => $row->appointment_id]) . '">
                        <i class="fa fa-check-square-o"></i>
                    </a>';
                        if ($canCancel) {
                            $action .= ' <button class="btn btn-danger p-2" title="Cancel" onclick="UpdateAppointmentStatus(\'Cancel\', \'' . $row->appointment_id . '\')">
                        <i class="fa fa-times"></i>
                    </button>';
                        }
                    } else {
                        if ($canConfirm) {
                            $action .= ' <button class="btn btn-primary p-2" title="Confirm" onclick="UpdateAppointmentStatus(\'Confirm\', \'' . $row->appointment_id . '\')">
                        <i class="fa fa-check"></i>
                    </button>';
                        }

                        $action .= ' <a class="btn btn-success p-2" title="Complete" href="' . route('admin.invoice.edit', ['shop' => CurrentShop::slug(), 'id' => $row->appointment_id]) . '">
                        <i class="fa fa-check-square-o"></i>
                    </a>';
                        if ($canCancel) {
                            $action .= ' <button class="btn btn-danger p-2" title="Cancel" onclick="UpdateAppointmentStatus(\'Cancel\', \'' . $row->appointment_id . '\')">
                        <i class="fa fa-times"></i>
                    </button>';
                        }
                        if ($canReschedule) {
                            $action .= ' <button class="btn btn-info p-2" title="Reschedule" onclick="RescheduleAppointment(\'' . $row->appointment_id . '\', \'' . $row->staff_ids . '\', \'' . $row->service_ids . '\')">
                        <i class="fa fa-calendar-check-o"></i>
                    </button>';
                        }
                    }

                    $action .= ' <button class="btn btn-dark p-2" title="View Appointment" onclick="ViewAppointment(' . $row->appointment_id . ')">
                        <i class="fa fa-eye"></i>
                    </button>';

                    return $action;
                })
                ->filter(function ($query) use ($request) {
                    if ($search = $request->get('search')['value'] ?? '') {
                        $query->where(function ($sub) use ($search) {
                            $sub->where('a.appointment_id', 'like', "%{$search}%")
                                ->orWhere('c.customer_name', 'like', "%{$search}%")
                                ->orWhere('c.customer_phone', 'like', "%{$search}%");
                        });
                    }
                })
                ->rawColumns(['slot_time', 'status_badge', 'action'])
                ->make(true);
        }
    }



    public function GetUpcomingGetTableData(Request $request)
    {
        if ($request->ajax()) {
            $user = User::find(Auth::id());

            $today = Carbon::today()->toDateString(); // e.g. 2025-09-09
            $hasFirstAvailableFlag = Schema::hasColumn('tbl_appointment_service', 'is_first_available');
            $staffServiceLinesSql = $hasFirstAvailableFlag
                ? "GROUP_CONCAT(DISTINCT CONCAT(
                        CASE WHEN COALESCE(s.is_first_available, 0) = 1 THEN 'First available' ELSE u.name END,
                        ': ',
                        t.service_name
                    ) ORDER BY s.id SEPARATOR ' | ') as staff_service_lines"
                : "GROUP_CONCAT(DISTINCT CONCAT(u.name, ': ', t.service_name) ORDER BY s.id SEPARATOR ' | ') as staff_service_lines";

            // Main query
            $query = DB::table('tbl_appointment as a')
                ->leftJoin('tbl_appointment_service as s', 'a.appointment_id', '=', 's.appointment_id')
                ->leftJoin('users as u', function ($join) {
                    $join->on('s.staff_id', '=', 'u.id')
                        ->where('u.role', '=', 'Staff');
                })
                ->leftJoin('tbl_service as t', 's.service_id', '=', 't.id')
                ->leftJoin('customers as c', 'a.customer_id', '=', 'c.customer_id')
                ->select(
                    'a.appointment_id',
                    'a.date',
                    'a.slot_start',
                    'a.slot_end',
                    'a.status',
                    'c.customer_name',
                    'c.customer_phone',
                    DB::raw('GROUP_CONCAT(DISTINCT u.name SEPARATOR ", ") as staff_names'),
                    DB::raw('GROUP_CONCAT(DISTINCT s.staff_id) as staff_ids'),
                    DB::raw('GROUP_CONCAT(DISTINCT s.service_id) as service_ids'),
                    DB::raw($staffServiceLinesSql)
                )
                ->when(in_array($user->role, ['Staff']), function ($query) use ($user) {
                    return $query->where('s.staff_id', $user->id);
                })
                ->where(function ($q) use ($today) {
                    $q->where('a.date', '>', $today)
                        ->where(function ($q2) {
                            $q2->where('a.is_invoiced', 0)
                                ->orWhereNull('a.is_invoiced');
                        });
                })
                ->groupBy(
                    'a.appointment_id',
                    'c.customer_name',
                    'c.customer_phone',
                    'a.date',
                    'a.slot_start',
                    'a.slot_end',
                    'a.status'
                );
            CurrentShop::constrain($query, 'a.shop_id');

            return DataTables::of($query)
                ->addIndexColumn()
                 ->addColumn('slot_time', function ($row) {
    $start = \Carbon\Carbon::parse($row->slot_start)->format('h:i A');
    $end = \Carbon\Carbon::parse($row->slot_end)->format('h:i A');
    return $start . ' - ' . $end;
})
                ->addColumn('status_badge', function ($row) {
                    $statusMap = [
                        0 => ['label' => 'Pending', 'class' => 'bg-warning'],
                        1 => ['label' => 'Complete', 'class' => 'bg-success'],
                        2 => ['label' => 'Reject', 'class' => 'bg-danger'],
                        3 => ['label' => 'Confirm', 'class' => 'bg-primary'],
                        4 => ['label' => 'Reschedule', 'class' => 'bg-info'],
                    ];
                    $status = $statusMap[$row->status] ?? ['label' => 'Unknown', 'class' => 'bg-secondary'];
                    return "<span class='badge {$status['class']} p-2'>{$status['label']}</span>";
                })
                ->addColumn('action', function ($row) {
                    $action  = '';
                    $canConfirm = auth()->user()->can('Appointment-Confirm');
                    $canCancel = auth()->user()->can('Appointment-Cancel');
                    $canReschedule = auth()->user()->can('Appointment-Reschedule');
                    if ($row->status == 1) {
                        $action .= ' <a class="btn btn-dark p-2" title="Invoice" href="' . route('admin.invoice.edit', ['shop' => CurrentShop::slug(), 'id' => $row->appointment_id]) . '">
                        <i class="fa fa-file"></i>
                    </a>';
                    } else if ($row->status == 2) {
                        if ($canReschedule) {
                            $action .= ' <button class="btn btn-info p-2" title="Reschedule" onclick="RescheduleAppointment(\'' . $row->appointment_id . '\', \'' . $row->staff_ids . '\', \'' . $row->service_ids . '\')">
                        <i class="fa fa-calendar-check-o"></i>
                    </button>';
                        }
                    } else if ($row->status == 3) {
                        if ($canCancel) {
                            $action .= ' <button class="btn btn-danger p-2" title="Cancel" onclick="UpdateAppointmentStatus(\'Cancel\', \'' . $row->appointment_id . '\')">
                        <i class="fa fa-times"></i>
                    </button>';
                        }
                        $action .= ' <a class="btn btn-success p-2" title="Complete" href="' . route('admin.invoice.edit', ['shop' => CurrentShop::slug(), 'id' => $row->appointment_id]) . '">
                        <i class="fa fa-check-square-o"></i>
                    </a>';
                    } else if ($row->status == 4) {
                        if ($canConfirm) {
                            $action .= ' <button class="btn btn-primary p-2" title="Confirm" onclick="UpdateAppointmentStatus(\'Confirm\', \'' . $row->appointment_id . '\')">
                        <i class="fa fa-check"></i>
                    </button>';
                        }

                        $action .= ' <a class="btn btn-success p-2" title="Complete" href="' . route('admin.invoice.edit', ['shop' => CurrentShop::slug(), 'id' => $row->appointment_id]) . '">
                        <i class="fa fa-check-square-o"></i>
                    </a>';
                        if ($canCancel) {
                            $action .= ' <button class="btn btn-danger p-2" title="Cancel" onclick="UpdateAppointmentStatus(\'Cancel\', \'' . $row->appointment_id . '\')">
                        <i class="fa fa-times"></i>
                    </button>';
                        }
                    } else {
                        if ($canConfirm) {
                            $action .= ' <button class="btn btn-primary p-2" title="Confirm" onclick="UpdateAppointmentStatus(\'Confirm\', \'' . $row->appointment_id . '\')">
                        <i class="fa fa-check"></i>
                    </button>';
                        }

                        $action .= ' <a class="btn btn-success p-2" title="Complete" href="' . route('admin.invoice.edit', ['shop' => CurrentShop::slug(), 'id' => $row->appointment_id]) . '">
                        <i class="fa fa-check-square-o"></i>
                    </a>';
                        if ($canCancel) {
                            $action .= ' <button class="btn btn-danger p-2" title="Cancel" onclick="UpdateAppointmentStatus(\'Cancel\', \'' . $row->appointment_id . '\')">
                        <i class="fa fa-times"></i>
                    </button>';
                        }
                        if ($canReschedule) {
                            $action .= ' <button class="btn btn-info p-2" title="Reschedule" onclick="RescheduleAppointment(\'' . $row->appointment_id . '\', \'' . $row->staff_ids . '\', \'' . $row->service_ids . '\')">
                        <i class="fa fa-calendar-check-o"></i>
                    </button>';
                        }
                    }

                    $action .= ' <button class="btn btn-dark p-2" title="View Appointment" onclick="ViewAppointment(' . $row->appointment_id . ')">
                        <i class="fa fa-eye"></i>
                    </button>';

                    return $action;
                })
                ->filter(function ($query) use ($request) {
                    if ($search = $request->get('search')['value'] ?? '') {
                        $query->where(function ($sub) use ($search) {
                            $sub->where('a.appointment_id', 'like', "%{$search}%")
                                ->orWhere('c.customer_name', 'like', "%{$search}%")
                                ->orWhere('c.customer_phone', 'like', "%{$search}%");
                        });
                    }
                })
                ->rawColumns(['slot_time', 'status_badge', 'action'])
                ->make(true);
        }
    }
}
