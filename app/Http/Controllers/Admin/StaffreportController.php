<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use App\Support\CurrentShop;
class StaffreportController extends Controller
{
    public function index()
    {
        $id = Auth::id();
        return view('admin.staff-report.staff-report', compact('id'));
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
                    DB::raw('SUM(DISTINCT a.service_commission) as total_service_commission'),
                    DB::raw('SUM(DISTINCT a.service_tip) as total_service_tip'),  // ✅ like your totalTip logic
                    DB::raw('SUM(DISTINCT b.total_amt) as totalRevenue'),         // one-time calculation
                    DB::raw('COUNT(DISTINCT a.appointment_id) as appointmentCount')
                )
                ->where('a.staff_id', Auth::id())
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

    public function DailyEarningGetTableData(Request $request)
    {
        if ($request->ajax()) {
            $serviceSub = DB::table('tbl_appointment_service as s')
                ->select(
                    's.appointment_id',
                    DB::raw('SUM(s.service_commission) as total_service_commission'),
                    DB::raw('SUM(s.service_tip) as total_service_tip')
                )
                ->where('s.staff_id', Auth::id())
                ->groupBy('s.appointment_id');
            CurrentShop::constrain($serviceSub, 's.shop_id');
            $productSub = DB::table('tbl_appointment_product as p')
                ->select(
                    'p.appointment_id',
                    DB::raw('SUM(p.product_commision) as total_product_commission')
                )
                ->where('p.staff_id', Auth::id())
                ->groupBy('p.appointment_id');
            CurrentShop::constrain($productSub, 'p.shop_id');
            $data = DB::table('tbl_appointment as a')
                ->where('a.is_invoiced', 1)
                ->leftJoinSub($serviceSub, 's', fn($join) => $join->on('a.appointment_id', '=', 's.appointment_id'))
                ->leftJoinSub($productSub, 'p', fn($join) => $join->on('a.appointment_id', '=', 'p.appointment_id'))
                ->leftJoin('customers as u', 'a.customer_id', '=', 'u.customer_id')
                ->select(
                    'a.date',
                    DB::raw('
            SUM(COALESCE(s.total_service_commission,0) 
              + COALESCE(s.total_service_tip,0) 
              + COALESCE(p.total_product_commission,0)) as total_earn
        '),
                    DB::raw('SUM(COALESCE(s.total_service_commission,0)) as service_commission'),
                    DB::raw('SUM(COALESCE(s.total_service_tip,0)) as service_tip'),
                    DB::raw('SUM(COALESCE(p.total_product_commission,0)) as product_commission')
                )
                ->groupBy('a.date')
                ->orderBy('a.date', 'desc')
                ;
            CurrentShop::constrain($data, 'a.shop_id');
            $data = $data->get();
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
                            $q->where('a.date', 'like', "%{$search}%")
                                ->orWhere('s.total_service_commission', 'like', "%{$search}%")
                                ->orWhere('s.total_service_tip', 'like', "%{$search}%")
                                ->orWhere('p.total_product_commission', 'like', "%{$search}%");
                        });
                    }

                })
                ->rawColumns(['action', 'service_tip_earn', 'service_commission', 'total_earning', 'product_commision_earn'])
                ->make(true);
        }

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
                    'a.total_amt',
                    'a.total_tip'
                )
                ->groupBy(
                    'a.appointment_id',
                    'a.date',
                    'a.slot_start',
                    'a.slot_end',
                    'a.status',
                    'c.customer_name',
                    'c.customer_phone',
                    'a.total_discount',
                    'a.total_amt',
                    'a.total_tip'
                );
            CurrentShop::constrain($query, 'a.shop_id');
            // ✅ Filters
            if (!empty($request->start_date)) {
                $query->whereDate('a.date', '>=', $request->start_date);
            }

            if (!empty($request->end_date)) {
                $query->whereDate('a.date', '<=', $request->end_date);
            }

            if ($request->status !== null && $request->status !== '') {
                $query->where('a.status', $request->status);
            }

            if (!empty($request->staff_id)) {
                $query->where('b.staff_id', $request->staff_id);
            }

            $serviceSub = DB::table('tbl_appointment_service')
                ->select(
                    'appointment_id',
                    DB::raw('SUM(service_tip) as total_service_tip'),
                    DB::raw('SUM(service_commission) as total_service_commission')
                )
                ->when(!empty($request->staff_id), fn($q) => $q->where('staff_id', $request->staff_id))
                ->groupBy('appointment_id');
            CurrentShop::constrain($serviceSub, 'shop_id');
            $productSub = DB::table('tbl_appointment_product')
                ->select(
                    'appointment_id',
                    DB::raw('SUM(product_commision) as total_product_commission')
                )
                ->when(!empty($request->staff_id), fn($q) => $q->where('staff_id', $request->staff_id))
                ->groupBy('appointment_id');
            CurrentShop::constrain($productSub, 'shop_id');
            $summary = DB::table('tbl_appointment as a')
                ->leftJoinSub($serviceSub, 's', fn($join) => $join->on('a.appointment_id', '=', 's.appointment_id'))
                ->leftJoinSub($productSub, 'p', fn($join) => $join->on('a.appointment_id', '=', 'p.appointment_id'))
                ->when(
                    !empty($request->start_date),
                    fn($q) =>
                    $q->whereDate('a.date', '>=', $request->start_date)
                )
                ->when(
                    !empty($request->end_date),
                    fn($q) =>
                    $q->whereDate('a.date', '<=', $request->end_date)
                )
                ->when(
                    $request->status !== null && $request->status !== '',
                    fn($q) =>
                    $q->where('a.status', $request->status)
                )
                ->where(function ($q) {
                    // Ensure at least service or product belongs to staff
                    $q->whereNotNull('s.appointment_id')
                        ->orWhereNotNull('p.appointment_id');
                })
                ->selectRaw("
        SUM(a.total_amt) as total_revenue,
        COUNT(DISTINCT a.appointment_id) as total_appointments,
        COUNT(DISTINCT a.customer_id) as total_visits,
        SUM(a.total_tip) as total_tips,
        COALESCE(SUM(s.total_service_tip), 0) as total_service_tip,
        COALESCE(SUM(s.total_service_commission), 0) as total_service_commission,
        COALESCE(SUM(p.total_product_commission), 0) as total_product_commission,
        (
            COALESCE(SUM(s.total_service_tip), 0) + 
            COALESCE(SUM(s.total_service_commission), 0) + 
            COALESCE(SUM(p.total_product_commission), 0)
        ) as total_earning
    ")
                ;
            CurrentShop::constrain($summary, 'a.shop_id');
            $summary = $summary->first();
            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('date', function ($row) {
                    return \Carbon\Carbon::parse($row->date)->format('d-m-Y');
                })
                ->editColumn('slot_time', function ($row) {
                    return \Carbon\Carbon::parse($row->slot_start)->format('h:i A') . " - " . \Carbon\Carbon::parse($row->slot_end)->format('h:i A');
                })
                ->editColumn('service_discount', function ($row) {
                    return shop_money($row->total_discount);
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
                                // 🔎 extra columns from your list
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
                    ]
                ])
                ->rawColumns(['status_badge', 'action'])
                ->make(true);
        }

    }

}

