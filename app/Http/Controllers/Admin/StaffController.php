<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Service;
use App\Models\StaffService;
use App\Models\User;
use App\Services\Saas\PlanEntitlementService;
use App\Support\CurrentShop;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Validator;

class StaffController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles = Role::assignableForShop()
            ->where('name', '!=', 'Admin')
            ->orderBy('name')
            ->get();
        $ServiceData = Service::get();
        return view('admin.staff.staff', compact('ServiceData', 'roles'));
    }

    private function shopWeeklyHours(): array
    {
        $shopSlots = DB::table('tbl_slots')->where('staff_id', 0);
        CurrentShop::constrain($shopSlots, 'shop_id');
        $shopSlots = $shopSlots->get()->keyBy('day_name');

        $schedule = [];
        foreach (['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day) {
            $slot = $shopSlots->get($day);
            $schedule[$day] = [
                'is_day_off' => !$slot || (bool) $slot->is_day_off,
                'periods' => $slot && $slot->open_time && $slot->close_time
                    ? [['start' => substr($slot->open_time, 0, 5), 'end' => substr($slot->close_time, 0, 5)]]
                    : [],
            ];
        }

        return $schedule;
    }

    private function saveStaffWeeklyHours(int $staffId, array $weeklyHours): void
    {
        $shopId = CurrentShop::columnValue();
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

        DB::transaction(function () use ($staffId, $shopId, $days, $weeklyHours) {
            foreach ($days as $day) {
                $dayHours = $weeklyHours[$day];
                $periods = collect($dayHours['periods'] ?? [])
                    ->filter(fn ($period) => !empty($period['start']) && !empty($period['end']))
                    ->sortBy('start')
                    ->values();
                $isDayOff = !empty($dayHours['is_day_off']);
                $firstPeriod = $periods->first();
                $lastPeriod = $periods->last();

                $slotQuery = DB::table('tbl_slots')
                    ->where('staff_id', $staffId)
                    ->where('day_name', $day);
                CurrentShop::constrain($slotQuery, 'shop_id');
                $slot = $slotQuery->first();

                $slotValues = [
                    'shop_id' => $shopId,
                    'staff_id' => $staffId,
                    'day_name' => $day,
                    'is_day_off' => $isDayOff ? 1 : 0,
                    'open_time' => $isDayOff ? null : $firstPeriod['start'],
                    'close_time' => $isDayOff ? null : $lastPeriod['end'],
                    'updated_at' => now(),
                ];

                if ($slot) {
                    DB::table('tbl_slots')->where('id', $slot->id)->update($slotValues);
                    $slotId = $slot->id;
                } else {
                    $slotValues['created_at'] = now();
                    $slotId = DB::table('tbl_slots')->insertGetId($slotValues);
                }

                DB::table('tbl_breaks')
                    ->where('slot_id', $slotId)
                    ->where('staff_id', $staffId)
                    ->where(function ($query) {
                        $query->whereIn('break_type', [1, 2])->orWhereNull('break_type');
                    })
                    ->delete();

                if (!$isDayOff) {
                    foreach ($periods as $period) {
                        DB::table('tbl_breaks')->insert([
                            'shop_id' => $shopId,
                            'slot_id' => $slotId,
                            'staff_id' => $staffId,
                            'break_start' => $period['start'],
                            'break_end' => $period['end'],
                            'break_type' => 1,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }
        });
    }

    public function staff_profile(Request $request, $id)
    {
        $auth = Auth::user();
        if ((int) $id !== (int) $auth->id && !$auth->can('Staff-Edit') && !$auth->can('Staff-Add')) {
            abort(403);
        }

        $StaffData = User::forCurrentShop()->where('id', $id)->where('role', 'Staff')->first();
        $totalAppointments = DB::table('tbl_appointment as a')
            ->join('tbl_appointment_service as b', 'a.appointment_id', '=', 'b.appointment_id')
            ->where('b.staff_id', $id)
            ->where('is_invoiced', 1);
        CurrentShop::constrain($totalAppointments, 'a.shop_id');
        $totalAppointments = $totalAppointments->count('b.appointment_id');


        $serviceSub = DB::table('tbl_appointment_service as s')
            ->select(
                's.appointment_id',
                DB::raw('SUM(s.total_amt) as total_service_amount'),
                DB::raw('SUM(s.service_commission) as total_service_commission'),
                DB::raw('SUM(s.service_tip) as total_service_tip')
            )
            ->when(!empty($id), fn($q) => $q->where('s.staff_id', $id))
            ->groupBy('s.appointment_id');
        CurrentShop::constrain($serviceSub, 's.shop_id');

        $productSub = DB::table('tbl_appointment_product as p')
            ->select(
                'p.appointment_id',
                DB::raw('SUM(p.amount) as total_product_amount'),
                DB::raw('SUM(p.product_commision) as total_product_commission')
            )
            ->when(!empty($id), fn($q) => $q->where('p.staff_id', $id))
            ->groupBy('p.appointment_id');
        CurrentShop::constrain($productSub, 'p.shop_id');

        $EarningData = DB::table('tbl_appointment as a')
            ->leftJoinSub($serviceSub, 's', fn($join) => $join->on('a.appointment_id', '=', 's.appointment_id'))
            ->leftJoinSub($productSub, 'p', fn($join) => $join->on('a.appointment_id', '=', 'p.appointment_id'))
            ->when(!empty($request->start_date), fn($q) => $q->whereDate('a.date', '>=', $request->start_date))
            ->when(!empty($request->end_date), fn($q) => $q->whereDate('a.date', '<=', $request->end_date))
            ->where('a.status', 1) // ✅ Only confirmed appointments
            ->selectRaw("
        SUM(a.total_amt) as total_revenue,
        COUNT(DISTINCT a.appointment_id) as total_appointments,
        COUNT(DISTINCT a.customer_id) as total_visits,
        SUM(a.total_tip) as total_tips,

        COALESCE(SUM(s.total_service_tip),0) as total_service_tip,
        COALESCE(SUM(s.total_service_commission),0) as total_service_commission,
        COALESCE(SUM(p.total_product_commission),0) as total_product_commission,

        (
            COALESCE(SUM(s.total_service_tip),0) + 
            COALESCE(SUM(s.total_service_commission),0) + 
            COALESCE(SUM(p.total_product_commission),0)
        ) as total_earning
    ")
            ;
        CurrentShop::constrain($EarningData, 'a.shop_id');
        $EarningData = $EarningData->first();





        $totalVisit = DB::table('tbl_appointment_service as s')
            ->join('tbl_appointment as a', 's.appointment_id', '=', 'a.appointment_id')
            ->where('s.staff_id', $id)
            ->where('a.is_invoiced', 1)
            ->distinct('a.appointment_id');
        CurrentShop::constrain($totalVisit, 'a.shop_id');
        $totalVisit = $totalVisit->count('a.customer_id'); // Use staff-specific service amount



        return view('admin.staff.staff-profile', compact('id', 'EarningData', 'totalAppointments', 'StaffData', 'totalVisit'));
    }


    public function DailyEarningGetTableData(Request $request)
    {
        if ($request->ajax()) {
    // 🔹 Service commissions grouped by appointment + staff
$serviceSub = DB::table('tbl_appointment_service as s')
    ->select(
        's.appointment_id',
        's.staff_id',
        DB::raw('SUM(s.service_commission) as service_commission'),
        DB::raw('SUM(s.service_tip) as service_tip')
    )
    ->groupBy('s.appointment_id', 's.staff_id');
  CurrentShop::constrain($serviceSub, 's.shop_id');

// 🔹 Product commissions grouped by staff_id but validated by appointment_id
$productSub = DB::table('tbl_appointment_product as p')
    ->select(
        'p.staff_id',
        DB::raw('SUM(p.product_commision) as product_commission')
    )
    ->whereIn('p.appointment_id', function ($q) {
        $q->select('appointment_id')
          ->from('tbl_appointment')
          ->where('is_invoiced', 1); // ✅ only invoiced appointments
        CurrentShop::constrain($q, 'shop_id');
    })
    ->groupBy('p.staff_id');
        CurrentShop::constrain($productSub, 'p.shop_id');

// 🔹 Main aggregation
$data = DB::table('tbl_appointment as a')
    ->where('a.is_invoiced', 1)
    ->joinSub($serviceSub, 's', function ($join) {
        $join->on('a.appointment_id', '=', 's.appointment_id');
    })
    ->leftJoinSub($productSub, 'p', function ($join) {
        $join->on('s.staff_id', '=', 'p.staff_id'); // ✅ match only staff_id
    })
    ->leftJoin('customers as u', 'a.customer_id', '=', 'u.customer_id')
    ->select(
        'a.date',
        's.staff_id',
        DB::raw('SUM(IFNULL(s.service_commission,0)) as service_commission'),
        DB::raw('SUM(IFNULL(s.service_tip,0)) as service_tip'),
        DB::raw('IFNULL(p.product_commission,0) as product_commission'),
        DB::raw('(SUM(IFNULL(s.service_commission,0)) + SUM(IFNULL(s.service_tip,0)) + IFNULL(p.product_commission,0)) as total_earn')
    )
    ->where('s.staff_id', $request->id)
    ->groupBy('a.date', 's.staff_id', 'p.product_commission')
    ->orderBy('a.date', 'desc');
        CurrentShop::constrain($data, 'a.shop_id');





            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('appointment_date', function ($row) {
                    return date('d-m-Y', strtotime($row->date));
                })
                ->editColumn('product_commision_earn', function ($row) {
                    return shop_money($row->product_commission);
                })
                ->editColumn('service_tip_earn', function ($row) {
                    return shop_money($row->service_tip);
                })
                ->editColumn('service_commission', function ($row) {
                    return shop_money($row->service_commission);
                })
                ->editColumn('total_earning', function ($row) {
                    return shop_money($row->total_earn); // total = total_amt + tip + commission
                })
                ->addColumn('action', function ($row) {
                    return '<a href="#" class="btn btn-sm btn-primary">View</a>';
                })
                ->filter(function ($query) use ($request) {
                    if ($search = $request->get('search')['value'] ?? null) {
                        $query->where(function ($q) use ($search) {
                            $q->where('c.service_name', 'like', "%{$search}%")
                                ->orWhere('c.duration', 'like', "%{$search}%")
                                ->orWhere('c.price', 'like', "%{$search}%");
                        });
                    }
                })
                ->rawColumns(['action', 'service_tip_earn', 'service_commission', 'total_earning', 'product_commision_earn'])
                ->make(true);
        }
    }



 public function AssignServicesGetTableData(Request $request)
    {
        if ($request->ajax()) {
            $data = DB::table('tbl_appointment_service as a')
                ->leftJoin('tbl_appointment as b', 'a.appointment_id', '=', 'b.appointment_id')
                ->leftJoin('tbl_service as c', 'a.service_id', '=', 'c.id')
                ->select(
                    'c.service_name',
                    'c.duration',
                    'c.price',
                    DB::raw('SUM(a.service_commission) as total_service_commission'),
                    DB::raw('SUM(a.service_tip) as total_service_tip'),  // ✅ like your totalTip logic
                    DB::raw('SUM(b.total_amt) as totalRevenue'),         // one-time calculation
                    DB::raw('COUNT(DISTINCT a.appointment_id) as appointmentCount')
                )
                ->where('a.staff_id', $request->id)
                ->where('b.is_invoiced', 1)
                ->groupBy('a.service_id', 'c.service_name', 'c.duration', 'c.price')
                ->orderByDesc('totalRevenue')
                ->orderBy('c.duration');
            CurrentShop::constrain($data, 'a.shop_id');



            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('service_duration', function ($row) {
                    return $row->duration . " Minute"; // Corrected spelling
                })
                ->editColumn('service_price', function ($row) {
                    return shop_money($row->price);
                })
                ->editColumn('total_service_tip', function ($row) {
                    return shop_money($row->total_service_tip);
                })
                ->editColumn('total_service_commission', function ($row) {
                    return shop_money($row->total_service_commission);
                })
                ->editColumn('totalRevenue', function ($row) {
                    return shop_money($row->total_service_commission + $row->total_service_tip);
                })
                ->editColumn('appointmentCount', function ($row) {
                    return $row->appointmentCount;
                })
                ->filter(function ($query) use ($request) {
                    if ($search = $request->get('search')['value'] ?? null) {
                        $query->where(function ($q) use ($search) {
                            $q->where('c.service_name', 'like', "%{$search}%")
                                ->orWhere('c.duration', 'like', "%{$search}%")
                                ->orWhere('c.price', 'like', "%{$search}%");
                        });
                    }
                })
                ->rawColumns(['totalRevenue', 'appointmentCount', 'total_service_tip', 'total_service_commission'])
                ->make(true);
        }
    }


    public function AppointmentGetTableData(Request $request)
    {
        if ($request->ajax()) {
            // Main query
            $user = User::find(Auth::id());

            $query = DB::table('tbl_appointment as a')
                ->leftJoin('tbl_appointment_service as s', 'a.appointment_id', '=', 's.appointment_id')
                ->leftJoin('users as u', function ($join) {
                    $join->on('s.staff_id', '=', 'u.id')
                        ->where('u.role', '=', 'Staff');
                })
                ->leftJoin('customers as c', function ($join) {
                    $join->on('a.customer_id', '=', 'c.customer_id');
                })
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
                    DB::raw('GROUP_CONCAT(DISTINCT s.service_id) as service_ids')
                )->where('s.staff_id', $request->id)->where('is_invoiced', 1)
                ->groupBy('a.appointment_id', 'c.customer_name', 'c.customer_phone', 'a.date', 'a.slot_start', 'a.slot_end', 'a.status');
            CurrentShop::constrain($query, 'a.shop_id');

            // Apply filters
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereBetween('a.date', [$request->start_date, $request->end_date]);
            }


            if ($request->filled('day_type')) {
                if ($request->day_type === 'Morning') {
                    $query->whereBetween('a.slot_start', ['06:00:00', '11:59:59']);
                } elseif ($request->day_type === 'Afternoon') {
                    $query->whereBetween('a.slot_start', ['12:00:00', '16:59:59']);
                } elseif ($request->day_type === 'Evening') {
                    $query->whereBetween('a.slot_start', ['17:00:00', '22:00:00']);
                }
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('slot_time', function ($row) {
                    return $row->slot_start . " - " . $row->slot_end;
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
                        $action .= ' <button class="btn btn-dark p-2" title="Invoice">
                        <i class="fa fa-file"></i>
                    </button>';
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
                    } else if ($row->status == 4) {
                        if ($canConfirm) {
                            $action .= ' <button class="btn btn-primary p-2" title="Confirm" onclick="UpdateAppointmentStatus(\'Confirm\', \'' . $row->appointment_id . '\')">
                        <i class="fa fa-check"></i>
                    </button>';
                        }

                        $action .= ' <button class="btn btn-success p-2" title="Complete" onclick="CompleteAppointment(' . $row->appointment_id . ')">
                        <i class="fa fa-check-square-o"></i>
                    </button>';
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

                        $action .= ' <button class="btn btn-success p-2" title="Complete" onclick="CompleteAppointment(' . $row->appointment_id . ')">
                        <i class="fa fa-check-square-o"></i>
                    </button>';
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



                    return $action;
                })
                ->filter(function ($query) use ($request) {
                    if ($search = $request->get('search')['value'] ?? '') {
                        $query->where(function ($sub) use ($search) {
                            $sub->where('a.appointment_id', 'like', "%{$search}%")
                                ->orWhere('a.name', 'like', "%{$search}%")
                                ->orWhere('a.phone', 'like', "%{$search}%")
                                ->orWhere('u.name', 'like', "%{$search}%");
                        });
                    }
                })
                ->rawColumns(['slot_time', 'status_badge', 'action'])
                ->make(true);
        }
    }


    public function getStaffByService(Request $request)
    {
        $serviceIds = $request->service_ids; // array of IDs
        $staffs = User::forCurrentShop()
            ->where('role', 'Staff')
            ->where('is_barber', 1)
            ->whereNull('deleted_at')
            ->get()
            ->filter(function ($staff) use ($serviceIds) {
                if (preg_match('/^first\s+available/i', trim((string) $staff->name))) {
                    return true;
                }
                foreach ($serviceIds as $sid) {
                    if (StaffService::offersService($staff->id, $sid)) {
                        return true;
                    }
                }
                return false;
            })
            ->values();
        return response()->json(['data' => $staffs]);
    }

    public function GetTableData(Request $request)
    {
        if (! $request->ajax()) {
            abort(404);
        }
        $query = User::select(
            'users.id',
            'users.name',
            'users.email',
            'users.phone',
            'users.service_id',
            'users.commission as product_commission',
            'users.is_barber',
            'users.profile_img',
            'users.active_status',
            DB::raw('GROUP_CONCAT(DISTINCT tbl_service.service_name SEPARATOR ", ") as service_names'),
            DB::raw('GROUP_CONCAT(DISTINCT model_has_roles.role_id SEPARATOR ",") as role_ids') // ✅ Only role IDs
        )
            ->forCurrentShop()
            ->leftJoin('tbl_staff_service', 'tbl_staff_service.staff_id', '=', 'users.id')
            ->leftJoin('tbl_service', function ($join) {
                $join->on('tbl_service.id', '=', 'tbl_staff_service.service_id')
                    ->whereNull('tbl_service.deleted_at')
                    ->where('tbl_service.shop_id', CurrentShop::id());
            })
            ->leftJoin('model_has_roles', 'model_has_roles.model_id', '=', 'users.id') // ✅ left join (if no role still works)
            ->whereIn('users.role', ['Staff', 'Accountant'])
            ->groupBy(
                'users.id',
                'users.name',
                'users.email',
                'users.phone',
                'users.service_id',
                'users.commission',
                'users.is_barber',
                'users.profile_img',
                'users.active_status'
            );


        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('staff_status', function ($row) {
                return $row->active_status
                    ? '<span class="badge bg-success">Active</span>'
                    : '<span class="badge bg-danger">Deactive</span>';
            })
            ->addColumn('action', function ($row) {
                $buttons = '<a href="' . route('admin.staff.staff-profile', $row->id) . '" class="btn btn-sm btn-secondary p-2 mx-1"><i class="fa fa-eye"></i></a>';

                // ✅ Staff-Edit permission
                if (auth()->user()->can('Staff-Edit')) {
                    $buttons .= '
            <button type="button" class="btn view-details-btn btn-sm btn-icon btn-warning" data-id="' . $row->id . '">
                <span class="tf-icons bx bx-edit-alt bx-18px"></span>
            </button>
        ';
                }

                // ✅ Staff-Delete permission
                if (auth()->user()->can('Staff-Delete')) {
                    $buttons .= '
            <button type="button" class="btn btn-sm btn-icon btn-danger delete-staff-btn" data-id="' . $row->id . '">
                <span class="tf-icons bx bx-trash bx-18px"></span>
            </button>
        ';
                }


                $buttons .= '
             <button type="button" class="btn btn-sm btn-icon btn-info change-password" data-id="' . $row->id . '">
                <span class="tf-icons bx bx-lock bx-18px"></span>
            </button>
        ';

                return $buttons;
            })
            ->filter(function ($query) use ($request) {
                if ($search = $request->get('search')['value'] ?? null) {
                    $query->where(function ($q) use ($search) {
                        $q->where('users.name', 'like', "%{$search}%")
                            ->orWhere('users.email', 'like', "%{$search}%")
                            ->orWhere('users.phone', 'like', "%{$search}%")
                            ->orWhere('tbl_service.service_name', 'like', "%{$search}%");
                    });
                }
            })
            ->rawColumns(['action', 'staff_status'])
            ->make(true);
    }


    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'new_password' => ['required', 'string', 'min:6'],
            'confirm_password' => ['required', 'same:new_password'],
        ], [
            'new_password.required' => 'Please enter a new password',
            'new_password.min' => 'Password must be at least 6 characters long',
            'confirm_password.required' => 'Please confirm your password',
            'confirm_password.same' => 'Passwords do not match',
        ]);

        // ✅ If validation fails, return JSON response
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 200);
        }

        $confirm_password = $request->confirm_password;
        $new_password = $request->new_password;
        $id = $request->staff_row_id;

        // ✅ Get logged-in user
        if ((int) $request->staff_row_id !== (int) Auth::id() && !Auth::user()->can('Staff-Edit')) {
            abort(403);
        }

        $user = User::forCurrentShop()->findOrFail($request->staff_row_id);

        // ✅ Update password
        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'status' => 1,
            'message' => 'Password changed successfully'
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    private function requestIsBarber(Request $request): bool
    {
        $value = $request->input('is_barber');
        if (is_array($value)) {
            $value = end($value);
        }

        return (string) $value === '1';
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $role = Role::assignableForShop()
            ->where('name', '!=', 'Admin')
            ->findOrFail($request->role_id);
        $panelRole = $role->name === 'Accountant' ? 'Accountant' : 'Staff';
        $isBarber = $panelRole === 'Staff' && $this->requestIsBarber($request);

        if ($panelRole === 'Staff') {
            $canAdd = app(PlanEntitlementService::class)->assertCanAddStaff();
            if ($canAdd !== true) {
                return back()->with('error', $canAdd)->withInput();
            }
        }

        $validator = Validator::make($request->all(), [
            'staff_name' => 'required|string|max:255',
            'staff_email' => 'required|email|unique:users,email',
            'staff_phone' => 'nullable|string|max:20',
            'staff_password' => 'required|string|min:6',
            'service_id' => $isBarber ? 'required|array|min:1' : 'nullable|array',
            'service_id.*' => Rule::exists('tbl_service', 'id')->where('shop_id', CurrentShop::id()),
            'commission' => 'nullable|numeric|min:0|max:100',
            'status' => 'required',
            'role_id' => 'required',
            'profile_image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ], [
            'staff_email.required' => 'Email is required.',
            'staff_email.email' => 'Enter a valid email address.',
            'staff_email.unique' => 'This email is already added. You cannot use the same email again.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 0,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $validated = $validator->validated();

        $user = new User();
        $user->created_by = Auth::id();
        $user->shop_id = CurrentShop::id() ?? Auth::user()->shop_id;
        $user->name = $validated['staff_name'];
        $user->email = $validated['staff_email'];
        $user->phone = $validated['staff_phone'] ?? null;
        $user->password = Hash::make($validated['staff_password']);
        $asBarber = $isBarber;

        $user->commission = $panelRole === 'Staff' ? ($validated['commission'] ?? 0) : 0;
        $user->active_status = $validated['status'];
        $user->role = $panelRole;
        $user->is_barber = $asBarber ? 1 : 0;
        $user->service_id = $asBarber ? implode(',', $validated['service_id'] ?? []) : null;

        if ($request->hasFile('profile_image')) {
            $path = $request->file('profile_image')->store('profile_images', 'public');
            $user->profile_img = $path;
        }

        $user->assignRole($role);
        $user->save();

        StaffService::syncForStaff($user->id, $asBarber ? ($validated['service_id'] ?? []) : []);

        if ($panelRole === 'Staff') {
            $this->saveStaffWeeklyHours($user->id, $this->shopWeeklyHours());
        }

        return response()->json([
            'status' => 1,
            'message' => ($panelRole === 'Accountant' ? 'Accountant' : 'Staff') . ' added successfully',
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $role = Role::assignableForShop()
            ->where('name', '!=', 'Admin')
            ->findOrFail($request->role_id);
        $panelRole = $role->name === 'Accountant' ? 'Accountant' : 'Staff';
        $isBarber = $panelRole === 'Staff' && $this->requestIsBarber($request);

        $validator = Validator::make($request->all(), [
            'staff_name' => 'required|string|max:255',
            'staff_email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($id),
            ],
            'staff_phone' => 'nullable|string|max:20',
            'staff_password' => 'nullable|string|min:6',
            'service_id' => $isBarber ? 'required|array|min:1' : 'nullable|array',
            'service_id.*' => Rule::exists('tbl_service', 'id')->where('shop_id', CurrentShop::id()),
            'commission' => 'nullable|numeric|min:0|max:100',
            'status' => 'required',
            'role_id' => 'required',
            'profile_image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ], [
            'staff_email.required' => 'Email is required.',
            'staff_email.email' => 'Enter a valid email address.',
            'staff_email.unique' => 'This email is already added. You cannot use the same email again.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 0,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $validated = $validator->validated();

        $user = User::forCurrentShop()->findOrFail($id);
        $user->name = $validated['staff_name'];
        $user->email = $validated['staff_email'];
        $user->phone = $validated['staff_phone'] ?? null;

        if (!empty($validated['staff_password'])) {
            $user->password = Hash::make($validated['staff_password']);
        }

        $asBarber = $isBarber;

        $user->commission = $panelRole === 'Staff' ? ($validated['commission'] ?? 0) : 0;
        $user->active_status = $validated['status'];
        $user->role = $panelRole;
        $user->is_barber = $asBarber ? 1 : 0;
        $user->service_id = $asBarber ? implode(',', $validated['service_id'] ?? []) : null;

        if ($request->hasFile('profile_image')) {
            if ($user->profile_img && Storage::disk('public')->exists($user->profile_img)) {
                Storage::disk('public')->delete($user->profile_img);
            }
            $path = $request->file('profile_image')->store('profile_images', 'public');
            $user->profile_img = $path;
        }

        $user->syncRoles([$role]);
        $user->save();

        StaffService::syncForStaff($user->id, $asBarber ? ($validated['service_id'] ?? []) : []);
        return response()->json([
            'status' => 1,
            'message' => ($panelRole === 'Accountant' ? 'Accountant' : 'Staff') . ' updated successfully',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::forCurrentShop()->findOrFail($id);

        // Delete profile image
        if ($user->profile_img && Storage::disk('public')->exists($user->profile_img)) {
            Storage::disk('public')->delete($user->profile_img);
        }

        $user->delete();

        return response()->json(['status' => 1, 'message' => 'Staff deleted successfully']);
    }
}
