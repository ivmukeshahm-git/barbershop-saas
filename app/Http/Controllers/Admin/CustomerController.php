<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\User;
use App\Support\CurrentShop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Support\PhoneNumber;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.customer.index');
    }

    public function customer_profile(Request $request, $shop, $id)
    {
        $CustomerData = Customer::where('customer_id', $id)->firstOrFail();

        $totalVisit = DB::table('tbl_appointment as a')
            ->where('a.customer_id', $id)
            ->where('a.is_invoiced', 1);
        CurrentShop::constrain($totalVisit, 'a.shop_id');
        $totalVisit = $totalVisit->count('a.appointment_id');

        $totalSpendAmount = DB::table('tbl_appointment as a')
            ->where('a.customer_id', $id)
            ->where('a.is_invoiced', 1);
        CurrentShop::constrain($totalSpendAmount, 'a.shop_id');
        $totalSpendAmount = $totalSpendAmount->sum('a.total_amt');
        return view('admin.customer.customer-profile', compact('CustomerData', 'id','totalVisit','totalSpendAmount'));
    }


    public function PendingAppointmentgetTableData(Request $request)
    {
        if ($request->ajax()) {

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
                )->where('a.customer_id', $request->customer_id)->groupBy('a.appointment_id', 'c.customer_name', 'c.customer_phone', 'a.date', 'a.slot_start', 'a.slot_end', 'a.status');
            CurrentShop::constrain($query, 'a.shop_id');

            // Apply filters
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereBetween('a.date', [$request->start_date, $request->end_date]);
            }

            if ($request->filled('status')) {
                $query->where('a.status',);
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

    public function GetTableData(Request $request)
    {
        if ($request->ajax()) {
            $data = Customer::select(['customer_id', 'customer_name', 'customer_phone', 'customer_email', 'customer_status']);

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('status_badge', function ($row) {
                    if ($row->customer_status == 1) {
                        return '<span class="badge bg-success">Active</span>';
                    } else {
                        return '<span class="badge bg-danger">Inactive</span>';
                    }
                })
                ->addColumn('action', function ($row) {
                    $action = '<a href="' . route('admin.customer.customer-profile', $row->customer_id) . '" class="btn btn-sm btn-secondary p-2 mx-1" title="View customer" aria-label="View customer"><i class="fa fa-eye" aria-hidden="true"></i></a>';
                    if (auth()->user()->can('Customer-Edit')) {
                        $action .= '<button type="button" class="btn btn-sm btn-warning p-2 mx-1 view-details-btn" title="Edit customer" aria-label="Edit customer"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></button>';
                    }

                    if (auth()->user()->can('Customer-Delete')) {
                        $action .= '<button type="button" class="btn btn-sm btn-danger p-2 delete-customer-btn" title="Delete customer" aria-label="Delete customer"><i class="fa fa-trash" aria-hidden="true"></i></button>';
                    }
                    return  $action;
                })
                ->rawColumns(['action', 'status_badge'])
                ->make(true);
        }
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
        abort_unless(Auth::user()->can('Customer-Add'), 403);

        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255', "regex:/^[\p{L}\p{M}\s'.-]+$/u"],
            'phone_number' => ['required', 'string', 'max:25', 'regex:/^\+?[0-9().\-\s]+$/'],
            'email' => 'nullable|email|max:255',
            'status' => 'nullable|integer'
        ]);
        if (!PhoneNumber::isValid($validated['phone_number'])) {
            throw ValidationException::withMessages(['phone_number' => ['Enter a valid phone number with 7 to 15 digits.']]);
        }
        $normalizedPhone = PhoneNumber::digits($validated['phone_number']);
        $duplicate = Customer::query()->get(['customer_id', 'customer_phone'])
            ->first(fn (Customer $customer) => PhoneNumber::digits((string) $customer->customer_phone) === $normalizedPhone);
        if ($duplicate) {
            throw ValidationException::withMessages(['phone_number' => ['A customer with this phone number already exists.']]);
        }

        $customer = new Customer();
        $customer->customer_name = $validated['full_name'];
        $customer->customer_phone = $validated['phone_number'];
        $customer->customer_email = $validated['email'] ?? null;
        $customer->customer_status = $request->status;
        $customer->save();

        return response()->json(['status' => 1, 'message' => 'Customer created successfully.']);
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
    public function update(Request $request, $shop, $id)
    {
        abort_unless(Auth::user()->can('Customer-Edit'), 403);

        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255', "regex:/^[\p{L}\p{M}\s'.-]+$/u"],
            'phone_number' => ['required', 'string', 'max:25', 'regex:/^\+?[0-9().\-\s]+$/'],
            'email' => 'nullable|email|max:255',
            'status' => 'nullable|integer'
        ]);
        if (!PhoneNumber::isValid($validated['phone_number'])) {
            throw ValidationException::withMessages(['phone_number' => ['Enter a valid phone number with 7 to 15 digits.']]);
        }

        $customer = Customer::findOrFail($id);
        $normalizedPhone = PhoneNumber::digits($validated['phone_number']);
        $duplicate = Customer::query()->where('customer_id', '!=', $customer->getKey())
            ->get(['customer_id', 'customer_phone'])
            ->first(fn (Customer $candidate) => PhoneNumber::digits((string) $candidate->customer_phone) === $normalizedPhone);
        if ($duplicate) {
            throw ValidationException::withMessages(['phone_number' => ['A customer with this phone number already exists.']]);
        }
        $customer->customer_name = $validated['full_name'];
        $customer->customer_phone = $validated['phone_number'];
        $customer->customer_email = $validated['email'] ?? null;
        $customer->customer_status = $request->status;
        $customer->update();

        return response()->json(['status' => 1, 'message' => 'Customer updated successfully.']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($shop, $id)
    {
        abort_unless(Auth::user()->can('Customer-Delete'), 403);

        $customer = Customer::findOrFail($id);
        $customer->delete();

        return response()->json(['status' => 1, 'message' => 'Customer deleted successfully.']);
    }
}
