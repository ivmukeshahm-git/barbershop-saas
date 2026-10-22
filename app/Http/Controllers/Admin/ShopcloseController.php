<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShopClosure;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Yajra\DataTables\DataTables;

class ShopcloseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.shop-closures.index');
    }

    public function GetTableData(Request $request)
    {
        if ($request->ajax()) {
            $data = ShopClosure::select(['id', 'date', 'closure_status', 'start_time', 'end_time', 'reason']);

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('closure_type', function ($row) {
                    if ($row->closure_status == 1) {
                        $sts = "Full Day";
                    } else {
                        $sts = "Partial Day";
                    }

                    return $sts;
                })

                ->addColumn('closure_day', function ($row) {
                    return Carbon::parse($row->date)->format('d M, Y'); // Example: 07 Sep, 2025
                })
                ->addColumn('closure_time_status', function ($row) {
                    if ($row->closure_status == 1) {
                        $sts = "Full Day Close";
                    } else {
                        $start = Carbon::parse($row->start_time)->format('h:i A'); // format to 12-hour time with AM/PM
                        $end = Carbon::parse($row->end_time)->format('h:i A');
                        $sts = $start . " - " . $end;
                    }

                    return $sts;
                })
                ->addColumn('action', function ($row) {
                    $actions = '';

                    // ✅ Shop_Closures-Edit permission
                    if (auth()->user()->can('Shop_Closures-Edit')) {
                        $actions .= '
            <button type="button" class="btn view-details-btn btn-sm btn-icon btn-warning" data-id="' . $row->id . '">
                <span class="tf-icons bx bx-edit-alt bx-18px"></span>
            </button>
        ';
                    }

                    // ✅ Shop_Closures-Delete permission
                    if (auth()->user()->can('Shop_Closures-Delete')) {
                        $actions .= '
            <button type="button" class="btn btn-sm btn-icon btn-danger delete-shop-closures-btn" data-id="' . $row->id . '">
                <span class="tf-icons bx bx-trash bx-18px"></span>
            </button>
        ';
                    }

                    return $actions;
                })
                ->filterColumn('category_name', function ($query, $keyword) {
                    $query->where('date', 'like', "%$keyword%")
                    ->orWhere('closure_status', 'like', "%{$keyword}%")
                    ->orWhere('start_time', 'like', "%{$keyword}%")
                    ->orWhere('end_time', 'like', "%{$keyword}%");
                })
                ->rawColumns(['action', 'closure_time_status', 'closure_type', 'closure_day'])
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
        $isFullDay = $request->boolean('full_day_closure');
        $rules = [
            'close_date' => 'required|date_format:Y-m-d',
        ];
        if (!$isFullDay) {
            $rules['start_time'] = 'required|date_format:H:i';
            $rules['end_time'] = 'required|date_format:H:i|after:start_time';
        }
        $request->validate($rules, [
            'end_time.after' => 'End must be after start.',
        ]);

        $startTime = $isFullDay ? null : $this->normalizeTimeValue($request->start_time);
        $endTime   = $isFullDay ? null : $this->normalizeTimeValue($request->end_time);

        if (!$isFullDay) {
            [$startTime, $endTime] = $this->assertPartialClosureWindow($startTime, $endTime);
        }

        $closure = new ShopClosure();
        $closure->created_by     = Auth::id();
        $closure->date           = $request->close_date;
        $closure->closure_status = $isFullDay ? 1 : 0;
        $closure->start_time     = $startTime;
        $closure->end_time       = $endTime;
        $closure->reason         = $request->close_reason;

        $closure->save();

        return response()->json([
            'status'  => 1,
            'message' => 'Shop closure created successfully.'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($shop, $shopClosure)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($shop, $shopClosure)
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
    public function update(Request $request, $shop, $shopClosure)
    {
        $isFullDay = $request->boolean('full_day_closure');
        $rules = [
            'close_date' => 'required|date_format:Y-m-d',
        ];
        if (!$isFullDay) {
            $rules['start_time'] = 'required|date_format:H:i';
            $rules['end_time'] = 'required|date_format:H:i|after:start_time';
        }
        $request->validate($rules, [
            'end_time.after' => 'End must be after start.',
        ]);

        $closure = ShopClosure::findOrFail($shopClosure);

        $startTime = $isFullDay ? null : $this->normalizeTimeValue($request->start_time);
        $endTime   = $isFullDay ? null : $this->normalizeTimeValue($request->end_time);

        if (!$isFullDay) {
            [$startTime, $endTime] = $this->assertPartialClosureWindow($startTime, $endTime);
        }

        $closure->created_by     = Auth::id();
        $closure->date           = $request->close_date;
        $closure->closure_status = $isFullDay ? 1 : 0;
        $closure->start_time     = $startTime;
        $closure->end_time       = $endTime;
        $closure->reason         = $request->close_reason;

        $closure->save();

        return response()->json([
            'status'  => 1,
            'message' => 'Shop closure updated successfully.'
        ]);
    }

    /**
     * Normalize UI/HTML time input into H:i:s for DB storage.
     */
    private function normalizeTimeValue($time): ?string
    {
        if ($time === null || trim((string) $time) === '') {
            return null;
        }

        try {
            return Carbon::parse($time)->format('H:i:s');
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Ensure partial-day end is after start and return times as H:i:s.
     */
    private function assertPartialClosureWindow(?string $startTime, ?string $endTime): array
    {
        if (!$startTime || !$endTime) {
            throw ValidationException::withMessages([
                'start_time' => ['Start time and end time are required for a partial-day closure.'],
            ]);
        }

        $start = Carbon::parse('2000-01-01 ' . $startTime);
        $end   = Carbon::parse('2000-01-01 ' . $endTime);

        if (!$end->gt($start)) {
            throw ValidationException::withMessages([
                'end_time' => ['End time must be after start time for a partial-day closure.'],
            ]);
        }

        return [$start->format('H:i:s'), $end->format('H:i:s')];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($shop, $shopClosure)
    {
        $closure = ShopClosure::find($shopClosure);

        if (!$closure) {
            return response()->json([
                'status' => 0,
                'message' => 'Shop closure not found.'
            ], 200);
        }

        $closure->delete();

        return response()->json([
            'status' => 1,
            'message' => 'Shop closure deleted successfully.'
        ]);
    }
}
