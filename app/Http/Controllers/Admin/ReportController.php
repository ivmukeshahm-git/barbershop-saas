<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Saas\PlanEntitlementService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Appointment;
use Carbon\Carbon;
use Yajra\DataTables\DataTables;
use App\Support\CurrentShop;
class ReportController extends Controller
{
    public function index(PlanEntitlementService $entitlements)
    {
        $StaffData = User::where('role', 'Staff')->forCurrentShop()->get();
        $allowsStaffReports = $entitlements->allowsStaffReports();
        $allowsAdvancedReports = $entitlements->allowsAdvancedReports();
        $plan = $entitlements->plan();
        $invoiceQuery = Appointment::query()
            ->where('is_invoiced', 1)
            ->where('status', 1);
        $totalTips = (clone $invoiceQuery)->sum('total_tip');
        $tipInvoiceCount = (clone $invoiceQuery)->where('total_tip', '>', 0)->count();
        $totalDiscounts = (clone $invoiceQuery)->sum('total_discount');
        $discountInvoiceCount = (clone $invoiceQuery)->where('total_discount', '>', 0)->count();
        $recentTipInvoices = (clone $invoiceQuery)
            ->where('total_tip', '>', 0)
            ->with('customer')
            ->latest('date')
            ->limit(5)
            ->get();
        $recentDiscountInvoices = (clone $invoiceQuery)
            ->where('total_discount', '>', 0)
            ->with('customer')
            ->latest('date')
            ->limit(5)
            ->get();
        return view('admin.reports.index', compact(
            'StaffData',
            'allowsStaffReports',
            'allowsAdvancedReports',
            'plan',
            'totalTips',
            'tipInvoiceCount',
            'totalDiscounts',
            'discountInvoiceCount',
            'recentTipInvoices',
            'recentDiscountInvoices'
        ));
    }

    public function TopSellingProductReport(Request $request)
    {
        if (!app(PlanEntitlementService::class)->allowsAdvancedReports()) {
            return response()->json(['message' => 'Advanced product reports are not included in your plan.'], 403);
        }

        if ($request->ajax()) {
            $query = DB::table('tbl_appointment_product as ap')
                ->join('tbl_product as p', 'ap.product_id', '=', 'p.id')
                ->join('tbl_appointment as a', 'ap.appointment_id', '=', 'a.appointment_id')
                ->join('customers as c', 'a.customer_id', '=', 'c.customer_id')
                ->leftJoin('users as u', 'ap.staff_id', '=', 'u.id')
                ->select(
                    'p.id as product_id',
                    'p.product_name',
                    'p.price as product_price',
                    DB::raw('SUM(ap.qty) as sell_unit'),
                    DB::raw('SUM(ap.product_commision) as total_commission'),
                    DB::raw('
            (
                (SUM(ap.amount) - SUM(ap.product_commision))
                - ((SUM(ap.amount) - SUM(ap.product_commision)) * (a.product_discount / 100))
            ) as total_revenue
        '),
                    DB::raw('((SUM(ap.amount) - SUM(ap.product_commision)) * (a.product_discount / 100)) as total_discount'),
                )
                ->groupBy('p.id', 'p.product_name', 'p.price', 'a.product_discount')
                ->orderByDesc('total_revenue');
            CurrentShop::constrain($query, 'ap.shop_id');
            if ($request->filled('start_date')) {
                $query->whereDate('ap.created_at', '>=', Carbon::parse($request->start_date)->toDateString());
            }

            if ($request->filled('end_date')) {
                $query->whereDate('ap.created_at', '<=', Carbon::parse($request->end_date)->toDateString());
            }

            if ($request->filled('staff_id') && (int)$request->staff_id !== 0) {
                $query->where('ap.staff_id', $request->staff_id);
            }

             $summary = DB::table('tbl_appointment_product as ap')
                ->join('tbl_product as p', 'ap.product_id', '=', 'p.id')
                ->join('tbl_appointment as a', 'ap.appointment_id', '=', 'a.appointment_id')
                ->when(!empty($request->staff_id), fn($q) => $q->where('ap.staff_id', $request->staff_id))
                ->when(!empty($request->start_date), fn($q) => $q->whereDate('ap.created_at', '>=', $request->start_date))
                ->when(!empty($request->end_date), fn($q) => $q->whereDate('ap.created_at', '<=', $request->end_date))
                ->selectRaw("
        SUM(ap.qty) as total_units_sold,
               SUM(ap.amount) as total_product_amount,
        SUM(ap.product_commision) as total_product_commission,
        SUM(
            (ap.amount - ap.product_commision) 
            - ((ap.amount - ap.product_commision) * (a.product_discount / 100))
        ) as total_revenue,
        SUM(
            ((ap.amount - ap.product_commision) * (a.product_discount / 100))
        ) as total_discount
    ")
                ;
            CurrentShop::constrain($summary, 'ap.shop_id');
            $summary = $summary->first();
            return DataTables::of($query)
                ->addIndexColumn() // Sr. No.
                ->editColumn('product_price', function ($row) {
                    return shop_money($row->product_price); // nicely formatted
                })
                ->editColumn('total_discount', function ($row) {
                    return shop_money($row->total_discount); // nicely formatted
                })
                ->editColumn('total_revenue', function ($row) {
                    return shop_money($row->total_revenue);
                })
                ->editColumn('total_commission', function ($row) {
                    return shop_money($row->total_commission);
                })
                ->with([
                    'summary' => [
                        'total_revenue' => shop_money($summary->total_revenue ?? 0),
                        'total_discount' => shop_money($summary->total_discount ?? 0),
                        'total_commission' => shop_money($summary->total_product_commission ?? 0),
                        'total_units_sold' => $summary->total_units_sold ?? 0,
                        'total_product_amount' => shop_money($summary->total_product_amount ?? 0),
                    ]
                ])
                ->make(true);
        }

    }

    public function ProductSellReport(Request $request)
    {
        if ($request->ajax()) {
            $query = DB::table('tbl_appointment_product as ap')
                ->join('tbl_product as p', 'ap.product_id', '=', 'p.id')
                ->join('tbl_appointment as a', 'ap.appointment_id', '=', 'a.appointment_id') // join appointment
                ->join('customers as c', 'a.customer_id', '=', 'c.customer_id') // join customer
                ->leftJoin('users as u', 'ap.staff_id', '=', 'u.id') // staff who sold the product
                ->select(
                    'ap.id',
                    'p.product_name as product_name',
                    'c.customer_name as customer_name', // customer name
                    'u.name as staff_name',
                    'ap.created_at as sell_date',
                    'ap.amount'
                )
                ->orderByDesc('ap.id');
            CurrentShop::constrain($query, 'ap.shop_id');
            // ✅ Filters
            if ($request->filled('start_date')) {
                $query->whereDate('ap.created_at', '>=', Carbon::parse($request->start_date)->toDateString());
            }

            if ($request->filled('end_date')) {
                $query->whereDate('ap.created_at', '<=', Carbon::parse($request->end_date)->toDateString());
            }

            if ($request->filled('staff_id') && (int)$request->staff_id !== 0) {
                $query->where('ap.staff_id', $request->staff_id);
            }

            return DataTables::of($query)
                ->addIndexColumn() // Sr. No.
                ->editColumn('sell_date', function ($row) {
                    return Carbon::parse($row->sell_date)->format('d-m-Y');
                })
                ->make(true);
        }

    }

    public function ReportCustomerInvoices(Request $request)
    {
        if ($request->ajax()) {
            $query = Appointment::where('is_invoiced', 1)
                ->orderBy('appointment_id', 'DESC')
                ->with('staff')
                ->with(['services.service'])
                ->with(['products.product'])
                ->with(['services.staff:id,name'])
                ->with(['products.staff:id,name']);
            if (!empty($request->start_date)) {
                $query->whereDate('date', '>=', $request->start_date);
            }

            if (!empty($request->end_date)) {
                $query->whereDate('date', '<=', $request->end_date);
            }

            if (!empty($request->staff_id)) {
                $query->where(function ($q) use ($request) {
                    $q->whereHas('services', function ($q2) use ($request) {
                        $q2->where('staff_id', $request->staff_id);
                    })
                        ->orWhereHas('products', function ($q3) use ($request) {
                            $q3->where('staff_id', $request->staff_id);
                        });
                });
            }

            return DataTables::of($query)
                ->addIndexColumn()
                // ✅ Customer Name
                ->addColumn('customer_name', function ($row) {
                    return $row->customer?->customer_name ?? '-';
                })
                // ✅ Staff Names
                ->addColumn('staff_name', function ($row) {
                    $staffNames = collect($row->services)->pluck('staff.name')
                        ->merge(collect($row->products)->pluck('staff.name'))
                        ->filter()
                        ->unique()
                        ->implode(', ');
                    return $staffNames ?: '-';
                })
                // ✅ Amount
                ->addColumn('amount', function ($row) {
                    return shop_money($row->total_amt);
                })
                // ✅ Status (always Paid in your case)
                ->addColumn('status', function ($row) {
                    return 'Paid';
                })
                // ✅ Action buttons
                ->addColumn('action', function ($row) {
                    $editBtn = '';
                    if (auth()->user()->can('Pos_invoice-Edit')) {
                        $editBtn = '<a class="btn btn-sm btn-warning p-2 mx-2"
                                    href="' . route('admin.invoice.edit', ['shop' => CurrentShop::slug(), 'id' => $row->appointment_id]) . '">
                                    <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                                </a>';
                    }

                    $viewBtn = '<a class="btn btn-sm btn-danger p-2" href="javascript:void(0)"
                onclick=\'viewInvoice(' . json_encode($row) . ')\'>
                <i class="fa fa-file" aria-hidden="true"></i>
            </a>';
                    return $editBtn . $viewBtn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        // ✅ Initial load just returns the Blade view
        return view('admin.reports.customer_invoices');
    }

    public function getAppointmentsTableData(Request $request)
    {
        if ($request->ajax()) {
            $query = DB::table('tbl_appointment as a')
                ->leftJoin('customers as c', 'a.customer_id', '=', 'c.customer_id')
                ->leftJoin('tbl_appointment_service as b', 'a.appointment_id', '=', 'b.appointment_id')
                ->leftJoin('users as u', 'b.staff_id', '=', 'u.id')
                ->select(
                    'a.appointment_id',
                    'a.date',
                    'a.slot_start',
                    'a.slot_end',
                    'a.status',
                    'c.customer_name',
                    'c.customer_phone',
                    DB::raw('GROUP_CONCAT(DISTINCT u.name SEPARATOR ", ") as staff_names'),
                    'a.total_discount',
                    'a.total_tax',
                    'a.total_amt',
                    'a.total_tip'
                )->where('a.is_invoiced', 1)->where('a.status', 1) // тЬЕ only invoiced and not pending
                ->groupBy(
                    'a.appointment_id',
                    'a.date',
                    'a.slot_start',
                    'a.slot_end',
                    'a.status',
                    'c.customer_name',
                    'c.customer_phone',
                    'a.total_discount',
                    'a.total_tax',
                    'a.total_amt',
                    'a.total_tip'
                );
            CurrentShop::constrain($query, 'a.shop_id');
            // тЬЕ Filters
            if (!empty($request->start_date)) {
                $query->whereDate('a.date', '>=', $request->start_date);
            }

            if (!empty($request->end_date)) {
                $query->whereDate('a.date', '<=', $request->end_date);
            }

            // if ($request->status !== null && $request->status !== '') {
            //     $query->where('a.status', $request->status);
            // }
            if (!empty($request->staff_id)) {
                $query->where('b.staff_id', $request->staff_id);
            }

            $serviceSub = DB::table('tbl_appointment_service as s')
                ->select(
                    's.appointment_id',
                    DB::raw('SUM(s.total_amt) as total_service_amount'),
                    DB::raw('SUM(s.service_commission) as total_service_commission'),
                    DB::raw('SUM(s.service_tip) as total_service_tip')
                )
                ->when(!empty($request->staff_id), fn($q) => $q->where('s.staff_id', $request->staff_id))
                ->groupBy('s.appointment_id');
            CurrentShop::constrain($serviceSub, 's.shop_id');
            $productSub = DB::table('tbl_appointment_product as p')
                ->select(
                    'p.appointment_id',
                    DB::raw('SUM(p.amount) as total_product_amount'),
                    DB::raw('SUM(p.product_commision) as total_product_commission')
                )
                ->when(!empty($request->staff_id), fn($q) => $q->where('p.staff_id', $request->staff_id))
                ->groupBy('p.appointment_id');
            CurrentShop::constrain($productSub, 'p.shop_id');
            $summary = DB::table('tbl_appointment as a')
                ->leftJoinSub($serviceSub, 's', fn($join) => $join->on('a.appointment_id', '=', 's.appointment_id'))
                ->leftJoinSub($productSub, 'p', fn($join) => $join->on('a.appointment_id', '=', 'p.appointment_id'))
                ->when(!empty($request->start_date), fn($q) => $q->whereDate('a.date', '>=', $request->start_date))
                ->when(!empty($request->end_date), fn($q) => $q->whereDate('a.date', '<=', $request->end_date))
                ->where('a.status', 1) // тЬЕ Only confirmed appointments
                ->selectRaw("
                    SUM(a.total_amt) as total_revenue,
                    COUNT(DISTINCT a.appointment_id) as total_appointments,
                    COUNT(DISTINCT a.customer_id) as total_visits,
                    SUM(a.total_tip) as total_tips,
                            SUM(a.total_discount) as total_discount,
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
            CurrentShop::constrain($summary, 'a.shop_id');
            $summary = $summary->first();
            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('date', function ($row) {
                    return Carbon::parse($row->date)->format('d-m-Y');
                })
                ->editColumn('slot_time', function ($row) {
                    return Carbon::parse($row->slot_start)->format('h:i A') . " - " . Carbon::parse($row->slot_end)->format('h:i A');
                })
                ->editColumn('service_discount', function ($row) {
                    return shop_money($row->total_discount);
                })
                ->editColumn('sub_total', function ($row) {
                    return shop_money($row->total_amt);
                })
                ->editColumn('service_tip', function ($row) {
                    return shop_money($row->total_tip);
                })
                ->editColumn('service_total_amt', function ($row) {
                    return shop_money($row->total_amt);
                })
                ->editColumn('status_badge', function ($row) {
                    switch ($row->status) {
                        case 0:
                            return '<span class="badge bg-warning">Pending</span>';
                        case 1:
                            return '<span class="badge bg-success">Completed</span>';
                        case 2:
                            return '<span class="badge bg-danger">Cancelled</span>';
                        case 3:
                            return '<span class="badge bg-info">Confirmed</span>';
                        case 4:
                            return '<span class="badge bg-primary">Rescheduled</span>';
                        default:
                            return '<span class="badge bg-secondary">Unknown</span>';
                    }

                })
                ->addColumn('action', function ($row) {
                    return '<a href="' . route('admin.appointment.show', $row->appointment_id) . '" class="btn btn-sm btn-primary">View</a>';
                })
                ->filter(function ($query) use ($request) {
                    if ($search = $request->get('search')['value'] ?? null) {
                        $query->where(function ($q) use ($search) {
                            $q->where('u.name', 'like', "%{$search}%")
                                ->orWhere('u.email', 'like', "%{$search}%")
                                ->orWhere('u.phone', 'like', "%{$search}%")
                                // ЁЯФО extra columns from your list
                                ->orWhere('u.name', 'like', "%{$search}%")
                                ->orWhere('c.customer_name', 'like', "%{$search}%")
                                ->orWhere('a.date', 'like', "%{$search}%")
                                ->orWhere('a.slot_start', 'like', "%{$search}%")
                                ->orWhere('a.slot_end', 'like', "%{$search}%")
                                ->orWhere('a.status', 'like', "%{$search}%")
                                ->orWhere('a.total_discount', 'like', "%{$search}%")
                                ->orWhere('a.total_tax', 'like', "%{$search}%")
                                ->orWhere('a.total_amt', 'like', "%{$search}%")
                                ->orWhere('a.total_tip', 'like', "%{$search}%");
                        });
                    }

                })
                ->with([
                    'summary' => [
                        'total_revenue' => shop_money($summary->total_revenue ?? 0),
                        'total_appointments' => $summary->total_appointments ?? 0,
                        'total_visits' => $summary->total_visits ?? 0,
                        'total_tips' => shop_money($summary->total_tips ?? 0),
                        'staff_total_service_tip' => shop_money($summary->total_service_tip ?? 0),
                        'staff_total_service_commission' => shop_money($summary->total_service_commission ?? 0),
                        'staff_total_product_commission' => shop_money($summary->total_product_commission ?? 0),
                        'staff_total_earning' => shop_money($summary->total_earning ?? 0),
                        'total_discount' => shop_money($summary->total_discount ?? 0),
                    ]
                ])
                ->rawColumns(['status_badge', 'action', 'slot_time', 'service_discount', 'service_tip', 'service_total_amt'])
                ->make(true);
        }

    }

    public function getPaymentMethodsTableData(Request $request)
    {
        if (!$request->ajax()) return;
        $query =  DB::table('tbl_payment_method as pm')
            ->leftJoin('tbl_appointment as a', function ($join) {
                $join->on('pm.name', '=', 'a.payment_method')
                    ->whereColumn('pm.shop_id', 'a.shop_id');
            })
            ->when(!empty($request->start_date), fn($q) => $q->whereDate('a.date', '>=', $request->start_date))
            ->when(!empty($request->end_date), fn($q) => $q->whereDate('a.date', '<=', $request->end_date))
            ->where('a.status', 1)
            ->selectRaw('LOWER(TRIM(pm.name)) as method, IFNULL(SUM(a.total_amt), 0) as total_revenue')
            ->groupByRaw('LOWER(TRIM(pm.name))');
            CurrentShop::constrain($query, 'pm.shop_id');
        $serviceSub = DB::table('tbl_appointment_service as s')
            ->select(
                's.appointment_id',
                DB::raw('SUM(s.total_amt) as total_service_amount'),
                DB::raw('SUM(s.service_commission) as total_service_commission'),
                DB::raw('SUM(s.service_tip) as total_service_tip')
            )
            ->when(!empty($request->staff_id), fn($q) => $q->where('s.staff_id', $request->staff_id))
            ->groupBy('s.appointment_id');
            CurrentShop::constrain($serviceSub, 's.shop_id');
        $productSub = DB::table('tbl_appointment_product as p')
            ->select(
                'p.appointment_id',
                DB::raw('SUM(p.amount) as total_product_amount'),
                DB::raw('SUM(p.product_commision) as total_product_commission')
            )
            ->when(!empty($request->staff_id), fn($q) => $q->where('p.staff_id', $request->staff_id))
            ->groupBy('p.appointment_id');
            CurrentShop::constrain($productSub, 'p.shop_id');
        $summary = DB::table('tbl_appointment as a')
            ->leftJoinSub($serviceSub, 's', fn($join) => $join->on('a.appointment_id', '=', 's.appointment_id'))
            ->leftJoinSub($productSub, 'p', fn($join) => $join->on('a.appointment_id', '=', 'p.appointment_id'))
            ->when(!empty($request->start_date), fn($q) => $q->whereDate('a.date', '>=', $request->start_date))
            ->when(!empty($request->end_date), fn($q) => $q->whereDate('a.date', '<=', $request->end_date))
            ->where('a.status', 1) // тЬЕ Only confirmed appointments
            ->selectRaw("
        SUM(a.total_amt) as total_revenue,
        COUNT(DISTINCT a.appointment_id) as total_appointments,
        COUNT(DISTINCT a.customer_id) as total_visits,
        SUM(a.total_tip) as total_tips,
        SUM(a.total_discount) as total_discount,
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
            CurrentShop::constrain($summary, 'a.shop_id');
            $summary = $summary->first();
        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('method', fn($row) => \App\Support\PaymentMethodLabel::normalize($row->method))
            ->editColumn('total_revenue', fn($row) => shop_money($row->total_revenue ?? 0))
            ->filter(function ($query) use ($request) {
                if (!empty($request->search['value'])) {
                    $search = $request->search['value'];
                    $query->where(function ($q) use ($search) {
                        $q->where('pm.name', 'like', "%{$search}%");
                    });
                }

            })
            ->with([
                'summary' => [
                    'total_revenue' => shop_money($summary->total_revenue ?? 0),
                    'total_discount' => shop_money($summary->total_discount ?? 0)
                ]
            ])
            ->make(true);
    }

    public function GetStaffEarnGetTableData(Request $request)
    {
        if (!app(PlanEntitlementService::class)->allowsStaffReports()) {
            return response()->json(['message' => 'Staff performance reports are not included in your plan.'], 403);
        }

        if ($request->ajax()) {
            $applyFilters = function ($query, string $appointmentAlias, string $staffColumn) use ($request) {
                $query->where("{$appointmentAlias}.is_invoiced", 1)
                    ->where("{$appointmentAlias}.status", 1)
                    ->when($request->filled('start_date'), fn($q) => $q->whereDate("{$appointmentAlias}.date", '>=', $request->start_date))
                    ->when($request->filled('end_date'), fn($q) => $q->whereDate("{$appointmentAlias}.date", '<=', $request->end_date))
                    ->when($request->filled('staff_id'), fn($q) => $q->where($staffColumn, $request->staff_id));
            };

            $serviceActivity = DB::table('tbl_appointment_service as s')
                ->join('tbl_appointment as a', 's.appointment_id', '=', 'a.appointment_id')
                ->selectRaw('s.staff_id, s.appointment_id, SUM(s.total_amt) as revenue, SUM(s.service_tip) as tips, SUM(s.service_commission) as service_commission, 0 as product_commission');
            $applyFilters($serviceActivity, 'a', 's.staff_id');
            CurrentShop::constrain($serviceActivity, 'a.shop_id');
            $serviceActivity->groupBy('s.staff_id', 's.appointment_id');

            $productActivity = DB::table('tbl_appointment_product as p')
                ->join('tbl_appointment as a', 'p.appointment_id', '=', 'a.appointment_id')
                ->selectRaw('p.staff_id, p.appointment_id, SUM(p.amount) as revenue, 0 as tips, 0 as service_commission, SUM(p.product_commision) as product_commission');
            $applyFilters($productActivity, 'a', 'p.staff_id');
            CurrentShop::constrain($productActivity, 'a.shop_id');
            $productActivity->groupBy('p.staff_id', 'p.appointment_id');

            $activity = $serviceActivity->unionAll($productActivity);
            $staffActivity = DB::query()
                ->fromSub($activity, 'activity')
                ->selectRaw('staff_id, COUNT(DISTINCT appointment_id) as appointments, SUM(revenue) as revenue, SUM(tips) as tips, SUM(service_commission) as service_commission, SUM(product_commission) as product_commission')
                ->groupBy('staff_id');
            $query = DB::table('users as st')
                ->leftJoinSub($staffActivity, 'earnings', 'st.id', '=', 'earnings.staff_id')
                ->where('st.role', 'Staff')
                ->when($request->filled('staff_id'), fn($q) => $q->where('st.id', $request->staff_id))
                ->selectRaw('st.id as staff_id, st.name as staff_name, COALESCE(earnings.appointments, 0) as appointments, COALESCE(earnings.revenue, 0) as revenue, COALESCE(earnings.tips, 0) as tips, COALESCE(earnings.service_commission, 0) as service_commission, COALESCE(earnings.product_commission, 0) as product_commission');
            CurrentShop::constrain($query, 'st.shop_id');

            $summary = DB::query()
                ->fromSub($activity, 'activity')
                ->selectRaw('COUNT(DISTINCT appointment_id) as appointmentCount, SUM(revenue) as totalRevenue, SUM(tips) as total_service_tip, SUM(service_commission) as total_service_commission, SUM(product_commission) as total_product_commission')
                ->first();
            $discountQuery = Appointment::query()->where('is_invoiced', 1)->where('status', 1)
                ->when($request->filled('start_date'), fn($q) => $q->whereDate('date', '>=', $request->start_date))
                ->when($request->filled('end_date'), fn($q) => $q->whereDate('date', '<=', $request->end_date))
                ->when($request->filled('staff_id'), function ($q) use ($request) {
                    $q->where(function ($sub) use ($request) {
                        $sub->whereHas('services', fn($service) => $service->where('staff_id', $request->staff_id))
                            ->orWhereHas('products', fn($product) => $product->where('staff_id', $request->staff_id));
                    });
                });
            $totalDiscount = $discountQuery->sum('total_discount');

            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('service_commission', fn($row) => shop_money($row->service_commission))
                ->editColumn('tips', fn($row) => shop_money($row->tips))
                ->editColumn('product_commission', fn($row) => shop_money($row->product_commission))
                ->editColumn('revenue', fn($row) => shop_money($row->revenue))
                ->editColumn('total_earn', fn($row) => shop_money($row->tips + $row->service_commission + $row->product_commission))
                ->addColumn('action', function ($row) {
                    //                    dd($row);
                    if ($row->staff_id) {
                        return '<a href="' . route('admin.staff.staff-profile', $row->staff_id) . '" class="btn btn-sm btn-primary">View</a>';
                    } else {
                        return $row->appointment_id ?? "-";
                    }

                })
                ->filter(function ($query) use ($request) {
                    if (!empty($request->search['value'])) {
                        $search = $request->search['value'];
                        $query->where(function ($q) use ($search) {
                            $q->where('st.name', 'like', "%{$search}%");
                        });
                    }

                })
                ->with([
                    'summary' => [
                        'total_service_commission' => shop_money($summary->total_service_commission ?? 0),
                        'total_service_tip' => shop_money($summary->total_service_tip ?? 0),
                        'total_product_commission' => shop_money($summary->total_product_commission ?? 0),
                        'totalRevenue' => shop_money($summary->totalRevenue ?? 0),
                        'appointmentCount' => $summary->appointmentCount ?? 0,
                        'total_discount' => shop_money($totalDiscount)
                    ]
                ])
                ->rawColumns(['service_commission', 'tips', 'product_commission', 'revenue', 'discounts', 'total_earn', 'action'])
                ->make(true);
        }

    }

}
