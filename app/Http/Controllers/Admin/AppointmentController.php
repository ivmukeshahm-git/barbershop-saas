<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\AppointmentService;
use App\Models\Breaks;
use App\Models\Service;
use App\Models\StaffService;
use App\Models\User;
use App\Models\Customer;
use App\Services\Saas\PlanEntitlementService;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Yajra\DataTables\DataTables;
use App\Support\CurrentShop;
use App\Support\PhoneNumber;


class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = User::find(Auth::id());
        $StaffData = User::where('role', 'Staff')->forCurrentShop()->get();

        $AllAppointments = $this->countAppointmentsByStatus(null, $user);
        $ConfirmAppointments = $this->countAppointmentsByStatus(3, $user);
        $CompleteAppointments = $this->countAppointmentsByStatus(1, $user);
        $CancelAppointments = $this->countAppointmentsByStatus(2, $user);
        $RescheduleAppointments = $this->countAppointmentsByStatus(4, $user);

        return view('admin.appointment.index', compact(
            'StaffData',
            'AllAppointments',
            'ConfirmAppointments',
            'CompleteAppointments',
            'CancelAppointments',
            'RescheduleAppointments'
        ));
    }

    private function countAppointmentsByStatus($status, $user)
    {
        $query = DB::table('tbl_appointment as a')
            ->leftJoin('tbl_appointment_service as s', 'a.appointment_id', '=', 's.appointment_id')
            ->when($status !== null, function ($query) use ($status) {
                return $query->where('a.status', $status);
            })
            ->when($user && $user->role === 'Staff', function ($query) use ($user) {
                return $query->where('s.staff_id', $user->id);
            })
            ->distinct();
        CurrentShop::constrain($query, 'a.shop_id');
        return $query->count('a.appointment_id');
    }

public function getTableData(Request $request)
    {
        if ($request->ajax()) {
            // Main query
            $user = User::find(Auth::id());
            $staffServiceLinesSql = "GROUP_CONCAT(DISTINCT CONCAT(
                    COALESCE(u.name, 'Staff'),
                    ': ',
                    t.service_name
                ) ORDER BY s.id SEPARATOR ' | ') as staff_service_lines";

            $query = DB::table('tbl_appointment as a')
                ->leftJoin('tbl_appointment_service as s', 'a.appointment_id', '=', 's.appointment_id')
                ->leftJoin('users as u', function ($join) {
                    $join->on('s.staff_id', '=', 'u.id')
                        ->where('u.role', '=', 'Staff');
                })
                ->leftJoin('tbl_service as t', 's.service_id', '=', 't.id')
                ->leftJoin('customers as c', function ($join) {
                    $join->on('a.customer_id', '=', 'c.customer_id');
                })
                ->select(
                    'a.appointment_id',
                    'a.date',
                    'a.slot_start',
                    'a.slot_end',
                    'a.status',
                    'a.is_invoiced',
                    'c.customer_name',
                    'c.customer_phone',
                    DB::raw('GROUP_CONCAT(DISTINCT u.name SEPARATOR ", ") as staff_names'),
                    DB::raw('GROUP_CONCAT(DISTINCT s.staff_id) as staff_ids'),
                    DB::raw('GROUP_CONCAT(DISTINCT s.service_id) as service_ids'),
                    DB::raw($staffServiceLinesSql)
                )->when($user->role === 'Staff', function ($query) use ($user) {
                    return $query->where('s.staff_id', $user->id);
                })
                ->groupBy('a.appointment_id', 'c.customer_name', 'c.customer_phone', 'a.date', 'a.slot_start', 'a.slot_end', 'a.status', 'a.is_invoiced')
                ->orderBy('a.date', 'desc')
                ->orderBy('a.slot_start', 'desc');
            CurrentShop::constrain($query, 'a.shop_id');

            // Apply filters
            if ($request->filled('start_date')) {
                $query->whereDate('a.date', '>=', $request->start_date);
            }
            if ($request->filled('end_date')) {
                $query->whereDate('a.date', '<=', $request->end_date);
            }

            if ($request->filled('status')) {
                $query->where('a.status', $request->status);
            }

            if ($request->filled('staff_id')) {
                $query->where('s.staff_id', $request->staff_id);
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
                ->orderColumn('date', 'a.date $1, a.slot_start $1')
                ->order(function ($query) {
                    $query->orderBy('a.date', 'desc')->orderBy('a.slot_start', 'desc');
                })
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
                    //     $action .= ' <button class="btn btn-dark p-2" title="Invoice">
                    //     <i class="fa fa-file"></i>
                    // </button>';
                    } else if ($row->status == 2) {
                        if ($canConfirm) {
                            $action .= ' <button class="btn btn-success p-2" title="Restore appointment" onclick="UpdateAppointmentStatus(\'Confirm\', \'' . $row->appointment_id . '\')">
                        <i class="fa fa-undo"></i>
                    </button>';
                        }
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
                        $action .= ' <a class="btn btn-success p-2" title="Complete" href="'.route('admin.invoice.create-from-appointment', ['shop' => CurrentShop::slug(), 'id' => $row->appointment_id]).'">
                        <i class="fa fa-check-square-o"></i>
                    </a>';
                    } else if ($row->status == 4) {
                        if ($canConfirm) {
                            $action .= ' <button class="btn btn-primary p-2" title="Confirm" onclick="UpdateAppointmentStatus(\'Confirm\', \'' . $row->appointment_id . '\')">
                        <i class="fa fa-check"></i>
                    </button>';
                        }

                        $action .= ' <a class="btn btn-success p-2" title="Complete" href="'.route('admin.invoice.create-from-appointment', ['shop' => CurrentShop::slug(), 'id' => $row->appointment_id]).'">
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

                        $action .= ' <a class="btn btn-success p-2" title="Complete" href="'.route('admin.invoice.create-from-appointment', ['shop' => CurrentShop::slug(), 'id' => $row->appointment_id]).'">
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

                     if ((int) $row->status === 3 && (int) $row->is_invoiced !== 1) {
                         $action .= ' <button class="btn btn-warning p-2" title="Archive appointment" onclick="ArchiveAppointment(\'' . route('admin.appointment.destroy', $row->appointment_id) . '\')">
                         <i class="fa fa-archive"></i>
                     </button>';
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



    public function get_appointment_details(Request $request)
    {
        $appointment_id = $request->input('appointment_id');
        // 🟢 Get appointment details
        $appointment = DB::table('tbl_appointment as a')
            ->join('customers as c', 'a.customer_id', '=', 'c.customer_id') // join with customers table
            ->select(
                'a.appointment_id',
                'c.customer_name as client_name',
                'c.customer_phone as client_phone',
                'c.customer_email as client_email',
                'a.date',
                'a.slot_start',
                'a.slot_end',
                'a.status',
                'a.total_amt',
                'a.created_at'
            )
            ->where('a.appointment_id', $appointment_id);
        CurrentShop::constrain($appointment, 'a.shop_id');
        $appointment = $appointment->first();

        if (!$appointment) {
            return response()->json([
                'status' => false,
                'message' => 'Appointment not found'
            ], 404);
        }

        $services = $this->fetchAppointmentServiceRows($appointment_id);

        $grouped = $this->groupAppointmentDetailRows($services);

        return response()->json([
            'status' => 1,
            'appointment' => $appointment,
            'services' => $services,
            'services_grouped' => $grouped['services_grouped'],
            'staff_grouped' => $grouped['staff_grouped'],
        ]);
    }


    public function get_customer_appointment_details(Request $request)
    {
        $appointment_id = $request->input('appointment_id');
        // ðŸŸ¢ Get appointment details
        $appointment = DB::table('tbl_appointment as a')
            ->select(
                'a.appointment_id',
                'a.name as client_name',
                'a.phone as client_phone',
                'a.email as client_email',
                'a.date',
                'a.slot_start',
                'a.slot_end',
                'a.status',
                'a.total_amt',
                'a.created_at'
            )
            ->where('a.appointment_id', $appointment_id);
        CurrentShop::constrain($appointment, 'a.shop_id');
        $appointment = $appointment->first();

        if (!$appointment) {
            return response()->json([
                'status' => false,
                'message' => 'Appointment not found'
            ], 404);
        }

        $services = $this->fetchAppointmentServiceRows($appointment_id);

        $grouped = $this->groupAppointmentDetailRows($services);

        return response()->json([
            'status' => 1,
            'appointment' => $appointment,
            'services' => $services,
            'services_grouped' => $grouped['services_grouped'],
            'staff_grouped' => $grouped['staff_grouped'],
        ]);
    }

    private function fetchAppointmentServiceRows($appointment_id)
    {
        $query = DB::table('tbl_appointment_service as s')
            ->leftJoin('users as u', function ($join) {
                $join->on('s.staff_id', '=', 'u.id')
                    ->where('u.role', '=', 'Staff');
            })
            ->leftJoin('tbl_service as t', 's.service_id', '=', 't.id');
        CurrentShop::constrain($query, 's.shop_id');

        // Staff login: only show their own service rows
        if (Auth::check() && Auth::user()->role === 'Staff') {
            $query->where('s.staff_id', Auth::id());
        }

        $select = [
            's.service_id',
            't.service_name',
            't.price',
            't.duration',
            's.staff_id',
            'u.name as staff_name',
            's.product_commission',
            's.service_commission',
            's.service_tip',
            's.total_amt',
        ];

        if (Schema::hasColumn('tbl_appointment_service', 'requested_staff_id')) {
            $query->leftJoin('users as ru', 's.requested_staff_id', '=', 'ru.id');
            $select[] = 's.requested_staff_id';
            $select[] = 'ru.name as requested_staff_name';
        }

        if (Schema::hasColumn('tbl_appointment_service', 'is_first_available')) {
            $select[] = 's.is_first_available';
        }

        return $query->select($select)
            ->where('s.appointment_id', $appointment_id)
            ->orderBy('s.id')
            ->get();
    }

    /**
     * Group appointment lines for admin detail modal (qty-aware).
     */
    private function groupAppointmentDetailRows($rows): array
    {
        $serviceGroups = [];
        $staffGroups = [];

        foreach ($rows as $row) {
            $serviceId = (string) ($row->service_id ?? '');
            $staffId = (string) ($row->staff_id ?? '');
            $price = (float) ($row->price ?? 0);
            $duration = (int) ($row->duration ?? 0);
            $serviceName = $row->service_name ?? 'Service';
            $assignedName = $row->staff_name ?? 'Staff';
            $isFirstAvailable = $this->rowIsFirstAvailable($row);
            $displayName = $isFirstAvailable ? 'First available' : $assignedName;
            $groupKey = $isFirstAvailable
                ? ('flex-' . ($staffId !== '' ? $staffId : $displayName))
                : ($staffId !== '' ? $staffId : $displayName);

            if ($serviceId !== '') {
                if (!isset($serviceGroups[$serviceId])) {
                    $serviceGroups[$serviceId] = [
                        'service_id' => $row->service_id,
                        'service_name' => $serviceName,
                        'price' => $price,
                        'duration' => $duration,
                        'qty' => 0,
                        'line_total' => 0,
                        'duration_total' => 0,
                        'staff_names' => [],
                    ];
                }
                $serviceGroups[$serviceId]['qty']++;
                $serviceGroups[$serviceId]['line_total'] += $price;
                $serviceGroups[$serviceId]['duration_total'] += $duration;
                $labelForService = $isFirstAvailable
                    ? 'First available' . ($this->isFirstAvailableName($assignedName) ? '' : ' (' . $assignedName . ')')
                    : $assignedName;
                if ($labelForService && !in_array($labelForService, $serviceGroups[$serviceId]['staff_names'], true)) {
                    $serviceGroups[$serviceId]['staff_names'][] = $labelForService;
                }
            }

            if ($groupKey !== '') {
                if (!isset($staffGroups[$groupKey])) {
                    $staffGroups[$groupKey] = [
                        'staff_id' => $row->staff_id,
                        'staff_name' => $displayName,
                        'is_first_available' => $isFirstAvailable,
                        'assigned_staff_name' => ($isFirstAvailable && !$this->isFirstAvailableName($assignedName))
                            ? $assignedName
                            : null,
                        'services' => [],
                    ];
                }
                $svcKey = $serviceId !== '' ? $serviceId : $serviceName;
                if (!isset($staffGroups[$groupKey]['services'][$svcKey])) {
                    $staffGroups[$groupKey]['services'][$svcKey] = [
                        'service_name' => $serviceName,
                        'qty' => 0,
                    ];
                }
                $staffGroups[$groupKey]['services'][$svcKey]['qty']++;
            }
        }

        $servicesGrouped = array_values(array_map(function ($group) {
            $group['staff_names'] = array_values($group['staff_names']);
            return $group;
        }, $serviceGroups));

        $staffGrouped = array_values(array_map(function ($group) {
            $group['services'] = array_values($group['services']);
            return $group;
        }, $staffGroups));

        return [
            'services_grouped' => $servicesGrouped,
            'staff_grouped' => $staffGrouped,
        ];
    }

    private function rowIsFirstAvailable($row): bool
    {
        if (!empty($row->is_first_available)) {
            return true;
        }

        return $this->isFirstAvailableName($row->requested_staff_name ?? null)
            || $this->isFirstAvailableName($row->staff_name ?? null);
    }

    private function isFirstAvailableName($name): bool
    {
        $value = strtolower(trim((string) $name));
        return $value === 'first available' || strpos($value, 'first available') === 0;
    }




    public function get_staff_by_service(Request $request)
    {
        $serviceIds = $request->input('service_ids'); // Correct variable name

        if (empty($serviceIds)) {
            return response()->json([
                'status' => 0,
                'message' => 'No service IDs provided',
                'staff' => []
            ]);
        }

        // Convert comma-separated string to array if needed
        if (is_string($serviceIds)) {
            $serviceIds = explode(',', $serviceIds);
        }

        // Fetch staff linked to these service IDs from the staff-service assignment table
        // (with a fallback to the legacy users.service_id column)
        $barbers = User::where('role', 'Staff')
            ->where('is_barber', 1)
            ->whereNull('deleted_at')
            ->forCurrentShop()
            ->get();

        $rows = [];
        foreach ($barbers as $barber) {
            if (preg_match('/^first\s+available/i', trim((string) $barber->name))) {
                foreach ($serviceIds as $sid) {
                    $rows[] = [
                        'staff_id'   => $barber->id,
                        'staff_name' => $barber->name,
                        'service_id' => $sid,
                    ];
                }
                continue;
            }
            foreach ($serviceIds as $sid) {
                if (StaffService::offersService($barber->id, $sid)) {
                    $rows[] = [
                        'staff_id'   => $barber->id,
                        'staff_name' => $barber->name,
                        'service_id' => $sid,
                    ];
                }
            }
        }

        $staff = collect($rows)->unique('staff_id')->values();

        return response()->json([
            'status' => 1,
            'staff' => $staff
        ]);
    }



    public function confirm_reject_action(Request $request)
    {
        $validated = $request->validate([
            'appointment_id' => ['required', 'integer'],
            'status' => ['required', 'in:2,3'],
        ]);
        $ability = (int) $validated['status'] === 3
            ? 'Appointment-Confirm'
            : 'Appointment-Cancel';
        abort_unless(auth()->user()->can($ability), 403);

        // Update the appointment status
        $updated = DB::table('tbl_appointment')
            ->where('appointment_id', $validated['appointment_id']);
        CurrentShop::constrain($updated, 'shop_id');
        $updated = $updated->update(['status' => $status]);

        if ($updated) {
            return response()->json([
                'status' => 1,
                'message' => 'Appointment status updated successfully.'
            ]);
        } else {
            return response()->json([
                'status' => 0,
                'message' => 'Failed to update appointment status.'
            ]);
        }
    }


    public function submit_reschedule_appointment(Request $request)
    {
        abort_unless(auth()->user()->can('Appointment-Reschedule'), 403);

        $appointment_id = $request->input('appointment_id');
        $slot_start = $request->input('slot_start');
        $slot_end = $request->input('slot_end');
        $date = $request->input('date');

        // Update appointment
        $reschedule = DB::table('tbl_appointment')
            ->where('appointment_id', $appointment_id);
        CurrentShop::constrain($reschedule, 'shop_id');
        $reschedule->update([
                'date' => $date,
                'slot_start' => $slot_start,
                'slot_end' => $slot_end,
                'status' => 4, // Reschedule
                'updated_at' => now(),
            ]);

        return response()->json([
            'status' => 1,
            'message' => 'Appointment rescheduled successfully.'
        ]);
    }




    public function calenderIndex()
    {
        $user = User::find(Auth::id());
        $staffs = [];
        if ($user->role == "Admin" || (method_exists($user, 'hasRole') && $user->hasRole('Super-admin'))) {
            $staffs = User::barbers()->forCurrentShop()->get(['id', 'name', 'profile_img', 'role']);
        } elseif (
            $user->role === 'Staff'
            || (method_exists($user, 'hasRole') && $user->hasRole('Staff'))
        ) {
            $staffs = User::barbers()
                ->forCurrentShop()
                ->where('id', Auth::id())
                ->get(['id', 'name', 'profile_img', 'role']);
        } else {
            $staffs = collect();
        }

        $services = Service::get();
        $customers = Customer::query()
            ->orderBy('customer_name')
            ->get(['customer_id', 'customer_name']);

        return view('admin.calender.index', compact('services', 'staffs', 'customers'));
    }



    public function calenderAppointMentStore(Request $request)
    {
        $validated = $request->validate([
            'appointment_date' => ['required', 'date'],
            'staff_id' => ['required', 'string', 'max:30'],
            'appointment_id' => ['nullable', 'integer'],
            'total_duration' => ['required', 'integer', 'min:1', 'max:1440'],
            'name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'string', 'max:25', 'regex:/^\+?[0-9().\-\s]+$/'],
            'email' => ['nullable', 'email', 'max:150'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'service_id' => ['required', 'array', 'min:1'],
            'service_id.*' => ['required', 'integer', 'min:1'],
        ]);
        if (!PhoneNumber::isValid($validated['phone'])) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'phone' => ['Enter a valid phone number with 7 to 15 digits.'],
            ]);
        }

        $totalDuration = (int) $request->input('total_duration', 0); // minutes

        $start = \Carbon\Carbon::parse($request->input('appointment_date')); // datetime (Y-m-d H:i)
        $end   = $start->copy()->addMinutes($totalDuration);
        $date  = $start->format('Y-m-d');

        $staffId = $request->input('staff_id');
        $appointMentID = $request->input('appointment_id') ?? "";

        if ($request->filled('appointment_id')) {
            abort_unless(auth()->user()->can('Appointment-Reschedule'), 403);
        }

        if (!$request->filled('appointment_id')) {
            $canAdd = app(PlanEntitlementService::class)->assertCanAddAppointment();
            if ($canAdd !== true) {
                return response()->json([
                    'msg' => $canAdd,
                    'status' => 403,
                ]);
            }
        }

        // âœ… Check overlapping appointment for same staff
        $isSlotBooked = Appointment::join('tbl_appointment_service as aps', 'aps.appointment_id', '=', 'tbl_appointment.appointment_id')
            ->where('aps.staff_id', $staffId)
            ->whereDate('tbl_appointment.date', $date)
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('tbl_appointment.slot_start', [$start, $end])
                    ->orWhereBetween('tbl_appointment.slot_end', [$start, $end])
                    ->orWhere(function ($query) use ($start, $end) {
                        $query->where('tbl_appointment.slot_start', '<=', $start)
                            ->where('tbl_appointment.slot_end', '>=', $end);
                    });
            })->when($request->filled('appointment_id'), function ($query, $appointMentID) {
                $query->where('tbl_appointment.appointment_id', '!=', $appointMentID);
            })
            ->exists();

        if ($isSlotBooked) {
            return response()->json([
                "msg" => "This staff already has an appointment in this time slot.",
                "status" => 409
            ]);
        }


        if ($request->filled('appointment_id')) {

            $appointMent = Appointment::findOrFail($request->appointment_id);

            $appointMent->update([
                'date'       => $date,
                'slot_start' => $start->format('H:i:s'),
                'slot_end'   => $end->format('H:i:s'),
                'name'       => $request->input('name'),
                'email'      => $request->input('email'),
                'phone'      => $request->input('phone'),
                'notes'      => $request->input('notes'),
                'status'     => 0,
                'comment'    => $request->input('name'),
            ]);

            // 🧹 Delete only THIS staff's old service rows, so other staff lanes stay untouched
            $staffDeletion = AppointmentService::where('appointment_id', $appointMent->appointment_id);
            if ($this->isFirstAvailableStaffId($staffId)) {
                $staffDeletion->where(function ($q) use ($staffId) {
                    $q->where('staff_id', $staffId)
                        ->orWhere('requested_staff_id', $staffId)
                        ->orWhere('is_first_available', 1);
                });
            } else {
                $staffDeletion->where(function ($q) use ($staffId) {
                    $q->where('staff_id', $staffId);
                    if (Schema::hasColumn('tbl_appointment_service', 'requested_staff_id')) {
                        $q->orWhere('requested_staff_id', $staffId);
                    }
                });
            }
            $staffDeletion->delete();
        } else {
            // âœ… Create Appointment

            $normalizedPhone = PhoneNumber::digits($request->input('phone'));
            $isCustomerExist = Customer::query()->get(['customer_id', 'customer_phone'])
                ->first(fn (Customer $customer) => PhoneNumber::digits((string) $customer->customer_phone) === $normalizedPhone);

            if ($isCustomerExist) {

                $appointMent = Appointment::create([
                    'date'       => $date, // only date
                    'customer_id'  => $isCustomerExist->customer_id, // only date
                    'slot_start' => $start->format('H:i:s'),
                    'slot_end'   => $end->format('H:i:s'),
                    'name'       => $request->input('name'),
                    'email'      => $request->input('email'),
                    'phone'      => $request->input('phone'),
                    'notes'      => $request->input('notes'),
                    'status'     => 0,
                    'comment'    => $request->input('name'),
                ]);
            } else {

                $customer = Customer::create([
                    "customer_name" => $request->input('name'),
                    "customer_phone" => $request->input('phone'),
                    "customer_email" => $request->input('email'),
                    "customer_status" => 1,
                ]);

                $appointMent = Appointment::create([
                    'date'       => $date, // only date
                    'customer_id'       => $customer->customer_id, // only date
                    'slot_start' => $start->format('H:i:s'),
                    'slot_end'   => $end->format('H:i:s'),
                    'name'       => $request->input('name'),
                    'email'      => $request->input('email'),
                    'phone'      => $request->input('phone'),
                    'notes'      => $request->input('notes'),
                    'status'     => 0,
                    'comment'    => $request->input('name'),
                ]);
            }
        }



        $isFlexStaff = $this->isFirstAvailableStaffId($staffId);
        $serviceIds = $request->input('service_id') ?? [];

        if (empty($serviceIds)) {
            return response()->json(["msg" => "Please select at least one service.", "status" => 422]);
        }

        foreach ($serviceIds as $value) {
            $row = [
                'appointment_id'     => $appointMent->appointment_id,
                'service_id'         => $value,
                'staff_id'           => $staffId,
                'product_commission' => 0,
                'service_commission' => 0,
                'service_tip'        => 0,
                'total_amt'          => 0,
                'status'             => 0,
                'comment'            => "Appointment from calendar",
            ];

            if (Schema::hasColumn('tbl_appointment_service', 'requested_staff_id')) {
                $flexId = $this->getFirstAvailableResourceId();
                $row['requested_staff_id'] = ($isFlexStaff && $flexId)
                    ? (int) $flexId
                    : $staffId;
            }
            if (Schema::hasColumn('tbl_appointment_service', 'is_first_available')) {
                $row['is_first_available'] = $isFlexStaff ? 1 : 0;
            }

            AppointmentService::create($row);
        }

        return response()->json(["msg" => "Successfully Created Appointment", "status" => 200]);
    }


    public function fetch(Request $request)
    {
        $events = [];
        $staffId = $request->staff_id;
        if (empty($staffId) && Auth::check() && Auth::user()->role === 'Staff') {
            $staffId = Auth::id();
        }
        $appointments = $this->fetchCalendarAppointmentRows($staffId);
        $singleStaffView = !empty($staffId);

        foreach ($appointments as $appt) {
            $apptDate = Carbon::parse($appt->date)->format('Y-m-d');
            $dateStart = $apptDate . ' ' . $appt->slot_start;
            $apptStart = Carbon::parse($dateStart);
            $apptEnd = Carbon::parse($apptDate . ' ' . $appt->slot_end);
            $fullDuration = $apptEnd->diffInMinutes($apptStart);
            $customerName = $appt->name ?: 'Walk-In';
            $grouped = $this->groupCalendarServicesByLane(
                $appt->services,
                $singleStaffView ? (string) $staffId : null
            );

            foreach ($grouped['assigned'] as $staffGroup) {
                $staffDuration = (int) ($staffGroup['total_duration'] ?? 0);
                if ($staffDuration <= 0) {
                    $staffDuration = $fullDuration;
                }
                $staffEnd = $apptStart->copy()->addMinutes($staffDuration);

                $events[] = $this->makeCalendarAppointmentEvent(
                    'appt_' . $appt->appointment_id . '_staff_' . $staffGroup['staff_id'],
                    (string) $staffGroup['staff_id'],
                    $staffGroup['staff_name'],
                    $appt,
                    $customerName,
                    $dateStart,
                    $staffEnd->format('Y-m-d H:i:s'),
                    $staffDuration,
                    $staffGroup['service_ids'],
                    $staffGroup['service_names'],
                    (string) $staffGroup['staff_id']
                );
            }

            foreach ($grouped['first_available'] as $firstGroup) {
                $faDuration = (int) ($firstGroup['total_duration'] ?? 0);
                if ($faDuration <= 0) {
                    $faDuration = $fullDuration;
                }
                $faEnd = $apptStart->copy()->addMinutes($faDuration);

                $events[] = $this->makeCalendarAppointmentEvent(
                    'appt_' . $appt->appointment_id . '_first_' . $firstGroup['resource_id'],
                    (string) $firstGroup['resource_id'],
                    'First available',
                    $appt,
                    $customerName,
                    $dateStart,
                    $faEnd->format('Y-m-d H:i:s'),
                    $faDuration,
                    $firstGroup['service_ids'],
                    $firstGroup['service_names'],
                    (string) ($firstGroup['modal_staff_id'] ?? $firstGroup['resource_id']),
                    !empty($firstGroup['assigned_staff_names'])
                        ? implode(', ', $firstGroup['assigned_staff_names'])
                        : null
                );
            }
        }

        $breaks = Breaks::where('break_type', 2)->when($staffId, function ($query, $staffId) {
            $query->where('staff_id', $staffId);
        })->get();
        foreach ($breaks as $break) {
            $createdDate = $break->break_date
                ? Carbon::parse($break->break_date)->format('Y-m-d')
                : Carbon::parse($break->created_at)->format('Y-m-d');

            $start = \Carbon\Carbon::parse($createdDate . ' ' . \Carbon\Carbon::parse($break->break_start)->format('H:i:s'));
            $end   = \Carbon\Carbon::parse($createdDate . ' ' . \Carbon\Carbon::parse($break->break_end)->format('H:i:s'));

            $events[] = [
                'id'         => 'break_' . $break->id, // avoid clashing with appointments
                'title'      => 'Break',
                'start'      => $start->format('Y-m-d H:i:s'),
                'end'        => $end->format('Y-m-d H:i:s'),
                'resourceId' => (string) $break->staff_id,
                'extendedProps' => [
                    'calendar'       => 'Personal',
                    'description'    => 'Break Time',
                    'location'       => 'Office',
                    'appointment_id' => null,
                    'staffName'      => Helper::getStaffName($break->staff_id),
                    'staffId'        => $break->staff_id,
                    'service_names'  => 'Break',
                ]
            ];
        }

        return response()->json($events);
    }

    private function fetchCalendarAppointmentRows($staffId = null)
    {
        return Appointment::query()
            ->select(
                'tbl_appointment.appointment_id',
                'tbl_appointment.date',
                'tbl_appointment.is_invoiced',
                'tbl_appointment.email',
                'tbl_appointment.slot_start',
                'tbl_appointment.slot_end',
                'tbl_appointment.name',
                'tbl_appointment.phone',
                'tbl_appointment.notes',
                'tbl_appointment.comment',
                'tbl_appointment.status'
            )
            ->when($staffId, function ($query) use ($staffId) {
                $query->whereHas('services', function ($serviceQuery) use ($staffId) {
                    $serviceQuery->where('staff_id', $staffId);
                });
            })
            ->where(function ($query) {
                $query->orWhereNull('tbl_appointment.is_invoiced');
            })
            ->where('tbl_appointment.status', '!=', 2)
            ->with([
                'services' => function ($query) use ($staffId) {
                    $query->with(['service', 'staff'])
                        ->when($staffId, function ($subQuery) use ($staffId) {
                            $subQuery->where('staff_id', $staffId);
                        })
                        ->when(Schema::hasColumn('tbl_appointment_service', 'requested_staff_id'), function ($subQuery) {
                            $subQuery->with('requestedStaff');
                        })
                        ->orderBy('id');
                }
            ])
            ->get()
            ->filter(function ($appointment) {
                return $appointment->services->isNotEmpty();
            })
            ->values();
    }

    private function groupCalendarServicesByLane($serviceRows, ?string $viewStaffId = null): array
    {
        $assigned = [];
        $firstAvailable = [];
        $isSingleStaffView = !empty($viewStaffId);
        $faResourceId = $this->getFirstAvailableResourceId() ?: 'first-available';

        foreach ($serviceRows as $row) {
            $serviceName = optional($row->service)->service_name;
            if (!$serviceName) {
                continue;
            }

            $serviceId = (string) $row->service_id;
            $assignedStaffId = (string) $row->staff_id;
            $assignedStaffName = optional($row->staff)->name ?: Helper::getStaffName($row->staff_id);
            $serviceDuration = (int) (optional($row->service)->duration ?? 0);

            if ($isSingleStaffView && $assignedStaffId !== (string) $viewStaffId) {
                continue;
            }

            $isFirstAvailable = $this->calendarRowIsFirstAvailable($row);

            if ($isSingleStaffView || !$isFirstAvailable) {
                if ($assignedStaffId === '') {
                    continue;
                }
                if (!isset($assigned[$assignedStaffId])) {
                    $assigned[$assignedStaffId] = [
                        'staff_id' => $row->staff_id,
                        'staff_name' => $assignedStaffName ?: 'Staff',
                        'service_ids' => [],
                        'service_names' => [],
                        'total_duration' => 0,
                    ];
                }
                $assigned[$assignedStaffId]['service_ids'][] = $serviceId;
                $assigned[$assignedStaffId]['service_names'][] = $serviceName;
                $assigned[$assignedStaffId]['total_duration'] += $serviceDuration;
            }

            if (!$isSingleStaffView && $isFirstAvailable) {
                $resourceId = (string) $faResourceId;
                if (!isset($firstAvailable[$resourceId])) {
                    $firstAvailable[$resourceId] = [
                        'resource_id' => $resourceId,
                        'service_ids' => [],
                        'service_names' => [],
                        // For "First available" lane, do NOT auto-fill real staff.
                        // Keep staff selection as the pseudo "First available" resource.
                        'modal_staff_id' => $resourceId,
                        'assigned_staff_names' => [],
                        'total_duration' => 0,
                    ];
                }
                $firstAvailable[$resourceId]['service_ids'][] = $serviceId;
                $firstAvailable[$resourceId]['service_names'][] = $serviceName;
                if ($assignedStaffName && !in_array($assignedStaffName, $firstAvailable[$resourceId]['assigned_staff_names'], true)) {
                    $firstAvailable[$resourceId]['assigned_staff_names'][] = $assignedStaffName;
                }
                $firstAvailable[$resourceId]['total_duration'] += $serviceDuration;
            }
        }

        return [
            'assigned' => array_values(array_map(function ($group) {
                $group['service_names'] = $this->formatCalendarServiceNames($group['service_names']);
                $group['service_ids'] = implode(',', array_values($group['service_ids']));
                return $group;
            }, $assigned)),
            'first_available' => array_values(array_map(function ($group) {
                $group['service_names'] = $this->formatCalendarServiceNames($group['service_names']);
                $group['service_ids'] = implode(',', array_values($group['service_ids']));
                return $group;
            }, $firstAvailable)),
        ];
    }

    private function formatCalendarServiceNames(array $serviceNames): string
    {
        if (empty($serviceNames)) {
            return '';
        }

        $counts = array_count_values(array_values(array_filter($serviceNames)));
        $parts = [];

        foreach ($counts as $name => $count) {
            $qty = max(1, (int) $count);
            $parts[] = $name . ' ×' . $qty;
        }

        return implode(', ', $parts);
    }

    private function makeCalendarAppointmentEvent(
        string $eventId,
        string $resourceId,
        string $staffName,
        $appointment,
        string $customerName,
        string $dateStart,
        string $dateEnd,
        int $durationMinutes,
        string $serviceIds,
        string $serviceNames,
        string $modalStaffId,
        ?string $assignedStaffName = null
    ): array {
        return [
            'id' => $eventId,
            'title' => $customerName,
            'start' => $dateStart,
            'end' => $dateEnd,
            'resourceId' => $resourceId,
            'extendedProps' => [
                'calendar' => 'Business',
                'description' => $appointment->notes,
                'location' => $appointment->comment,
                'staffName' => $staffName,
                'staffId' => $modalStaffId,
                'appointment_id' => $appointment->appointment_id,
                'appointment_name' => $customerName,
                'phone' => $appointment->phone,
                'email' => $appointment->email,
                'service_id' => $serviceIds,
                'service_names' => $serviceNames,
                'duration' => $durationMinutes,
                'start' => $dateStart,
                'assigned_staff_name' => $assignedStaffName,
            ]
        ];
    }

    private function calendarRowIsFirstAvailable($row): bool
    {
        $flexStaffId = $this->getFirstAvailableResourceId();

        if (!empty($row->is_first_available)) {
            return true;
        }

        if ($flexStaffId && !empty($row->requested_staff_id) && (string) $row->requested_staff_id === (string) $flexStaffId) {
            return true;
        }

        if ($this->isFirstAvailableName(optional($row->requestedStaff)->name)) {
            return true;
        }

        if ($flexStaffId && (string) ($row->staff_id ?? '') === (string) $flexStaffId) {
            return true;
        }

        return false;
    }

    private function getFirstAvailableResourceId(): ?string
    {
        static $firstAvailableId = false;

        if ($firstAvailableId !== false) {
            return $firstAvailableId;
        }

        $firstAvailableId = User::query()
            ->forCurrentShop()
            ->where('role', 'Staff')
            ->where(function ($query) {
                $query->whereRaw('LOWER(name) = ?', ['first available'])
                    ->orWhereRaw('LOWER(name) LIKE ?', ['first available%']);
            })
            ->value('id');

        return $firstAvailableId ? (string) $firstAvailableId : null;
    }

    private function isFirstAvailableStaffId($staffId): bool
    {
        if (empty($staffId)) {
            return false;
        }

        $flexStaffId = $this->getFirstAvailableResourceId();

        if ($flexStaffId && (string) $staffId === (string) $flexStaffId) {
            return true;
        }

        $name = User::where('id', $staffId)->value('name');

        return $this->isFirstAvailableName($name);
    }

    public function fetchStaffAvailability(Request $request)
    {
        $date = $request->input('date', Carbon::now()->format('Y-m-d'));
        $staffId = $request->input('staff_id');
        $dayName = Carbon::parse($date)->format('l');

        $shopSlot = DB::table('tbl_slots')
            ->where('staff_id', 0)
            ->where('day_name', $dayName);
        CurrentShop::constrain($shopSlot, 'shop_id');
        $shopSlot = $shopSlot->first();

        $shopOpen  = null;
        $shopClose = null;
        $shopIsOff = false;

        if ($shopSlot) {
            $shopIsOff = (bool) $shopSlot->is_day_off;
            if (!$shopIsOff && !empty($shopSlot->open_time) && !empty($shopSlot->close_time)) {
                $shopOpen  = Carbon::parse($shopSlot->open_time)->format('H:i');
                $shopClose = Carbon::parse($shopSlot->close_time)->format('H:i');
            }
        }

        if (!$shopOpen || !$shopClose) {
            $shopOpen  = '09:00';
            $shopClose = '21:00';
        }

        $staffQuery = User::barbers()->forCurrentShop();
        if ($staffId) {
            $staffQuery->where('id', $staffId);
        }
        $staffMembers = $staffQuery->get();

        $availability = [];

        foreach ($staffMembers as $staff) {
            $staffSlot = DB::table('tbl_slots')
                ->where('staff_id', $staff->id)
                ->where('day_name', $dayName);
            CurrentShop::constrain($staffSlot, 'shop_id');
            $staffSlot = $staffSlot->first();

            $isDayOff = $staffSlot ? (bool) $staffSlot->is_day_off : false;

            $workingHours = [];
            $breaks = [];

            if (!$isDayOff && $staffSlot) {
                $workingHours = DB::table('tbl_breaks')
                    ->where('slot_id', $staffSlot->id)
                    ->where('staff_id', $staff->id)
                    ->where('break_type', 1)
                    ->orderBy('break_start');
                CurrentShop::constrain($workingHours, 'shop_id');
                $workingHours = $workingHours->get()
                    ->map(function ($b) {
                        return [
                            'start' => Carbon::parse($b->break_start)->format('H:i'),
                            'end'   => Carbon::parse($b->break_end)->format('H:i'),
                        ];
                    })
                    ->toArray();

                if (empty($workingHours) && !empty($staffSlot->open_time) && !empty($staffSlot->close_time)) {
                    $workingHours = [[
                        'start' => Carbon::parse($staffSlot->open_time)->format('H:i'),
                        'end'   => Carbon::parse($staffSlot->close_time)->format('H:i'),
                    ]];
                }
            }

            $breaks = DB::table('tbl_breaks')
                ->where('staff_id', $staff->id)
                ->where('break_type', 2)
                ->where(function ($query) use ($date) {
                    $query->whereDate('break_date', $date)
                        ->orWhere(function ($legacy) use ($date) {
                            $legacy->whereNull('break_date')->whereDate('created_at', $date);
                        });
                });
            CurrentShop::constrain($breaks, 'shop_id');
            $breaks = $breaks->get()
                ->map(function ($b) {
                    return [
                        'start' => Carbon::parse($b->break_start)->format('H:i'),
                        'end'   => Carbon::parse($b->break_end)->format('H:i'),
                    ];
                })
                ->toArray();

            $availability[(string) $staff->id] = [
                'is_day_off'     => $isDayOff,
                'working_hours'  => $workingHours,
                'breaks'         => $breaks,
            ];
        }

        return response()->json([
            'shop_open'  => $shopOpen,
            'shop_close' => $shopClose,
            'shop_day_off' => $shopIsOff,
            'staff'      => $availability,
        ]);
    }

    public function breakFormSubmit(Request $request)
    {
        $validated = $request->validate([
            'break_date' => ['required', 'date', 'after_or_equal:today'],
            'break_start' => ['required_without:restoftheday', 'nullable', 'date_format:H:i'],
            'break_end' => ['required_without:restoftheday', 'nullable', 'date_format:H:i', 'after:break_start'],
            'restoftheday' => ['nullable', 'boolean'],
            'break_staff_id' => ['required', 'integer', 'exists:users,id'],
        ]);

        $staff = User::query()->forCurrentShop()->where('role', 'Staff')->findOrFail($validated['break_staff_id']);
        $allDay = $request->boolean('restoftheday');

        Breaks::create([
            'slot_id' => 0,
            'break_date' => $validated['break_date'],
            'break_start' => $allDay ? '00:00:00' : $validated['break_start'],
            'break_end' => $allDay ? '23:59:59' : $validated['break_end'],
            'staff_id' => $staff->id,
            'break_type' => 2,
        ]);

        return response()->json(["msg" => "SuccessFully Created Break", "status" => 200]);
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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $appointment = Appointment::query()
            ->where('status', 3)
            ->where('is_invoiced', 0)
            ->findOrFail($id);
        $appointment->status = 2;
        $appointment->save();

        return response()->json([
            'status' => 1,
            'message' => 'Appointment archived as cancelled. Its history is retained and it can be restored.',
        ]);
    }
}
