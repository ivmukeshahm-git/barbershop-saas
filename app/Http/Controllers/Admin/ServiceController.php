<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Setting;
use App\Support\CurrentShop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

use Yajra\DataTables\DataTables;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $ServiceCategory = ServiceCategory::whereNull('deleted_at')->get();
        $defaultDuration = (int) (Setting::forCurrentShop()?->service_default_duration ?: 30);
        return view('admin.service.service', compact('ServiceCategory', 'defaultDuration'));
    }

    public function GetTableData(Request $request)
    {
        if ($request->ajax()) {
            $data = Service::select(
                'tbl_service.id',
                'tbl_service.created_by',
                'tbl_service.service_name',
                'tbl_service.price',
                'tbl_service.duration',
                'tbl_service.status',
                'tbl_service.tip_status',
                'tbl_service.service_img',
                'tbl_service.description',
                'tbl_service.category_id',
                'tbl_service.commission',
                'tbl_service.home_section',
                'tbl_service_category.category_name'
            )
                ->join('tbl_service_category', function ($join) {
                    $join->on('tbl_service.category_id', '=', 'tbl_service_category.id')
                        ->where('tbl_service_category.shop_id', CurrentShop::id());
                });

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('service_price', function ($row) {
                    // Properly return the formatted price string
                    return shop_money($row->price);
                })
                ->addColumn('service_duration', function ($row) {
                    // Properly return the formatted price string
                    return $row->duration . ' Minute';
                })
                ->addColumn('action', function ($row) {
                    $actions = '';

                    // Uncomment and use permissions if needed
                    if (auth()->user()->can('Service-Edit')) {
                        $actions .= '
                    <button type="button" class="btn view-details-btn btn-sm btn-icon btn-warning">
                        <span class="tf-icons bx bx-edit-alt bx-18px"></span>
                    </button>
                ';
                    }

                    if (auth()->user()->can('Service-Delete')) {
                        $actions .= '
                    <button type="button" class="btn btn-sm btn-icon btn-danger delete-service-btn">
                        <span class="tf-icons bx bx-trash bx-18px"></span>
                    </button>
                ';
                    }

                    $actions .= '
                    <button type="button" class="btn btn-sm btn-icon btn-primary home-section-btn" title="Website section">
                        <i class="bi bi-house-door"></i>
                    </button>
                ';

                    return $actions;
                })
                ->filterColumn('category_name', function ($query, $keyword) {
                    $query->where('tbl_service.service_name', 'like', "%{$keyword}%")
                    ->orWhere('tbl_service_category.category_name', 'like', "%{$keyword}%")
                    ->orWhere('tbl_service.duration', 'like', "%{$keyword}%")
                    ->orWhere('tbl_service.price', 'like', "%{$keyword}%");
                })
                ->rawColumns(['action', 'service_price'])
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
        $request->validate([
            'category_id' => ['required', Rule::exists('tbl_service_category', 'id')->where('shop_id', CurrentShop::id())],
            'service_nm' => 'required|string|max:255',
            'price' => 'required|numeric|min:0|max:99999999.99|decimal:0,2',
            'duration' => 'required|integer|min:1',
            'service_image' => 'required|image|mimes:jpg,jpeg,png,gif|max:2048',
            'service_commission' => 'nullable|numeric|min:0|max:100',
            'description' => 'nullable|string',
        ]);

        $service = new Service();
        $service->created_by = Auth::id();
        $service->category_id = $request->category_id;
        $service->service_name = $request->service_nm;
        $service->price = $request->price;
        $service->duration = $request->duration;
        $service->status = $request->has('active_status') ? 1 : 0;
        $service->tip_status = $request->has('eligible_for_tip') ? 1 : 0;
        $service->commission = $request->service_commission;
        $service->description = $request->description;

        $service->service_img = $request->file('service_image')->store('service_images', 'public');

        $service->save();

        return response()->json([
            'status' => 1,
            'message' => 'Service created successfully.'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($shop, $service)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($shop, $service)
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
        $request->validate([
            'category_id' => ['required', Rule::exists('tbl_service_category', 'id')->where('shop_id', CurrentShop::id())],
            'service_nm' => 'required|string|max:255',
            'price' => 'required|numeric|min:0|max:99999999.99|decimal:0,2',
            'duration' => 'required|integer|min:1',
            'service_image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'service_commission' => 'nullable|numeric|min:0|max:100',
            'description' => 'nullable|string',
        ]);

        $service = Service::findOrFail($id);
        $service->category_id = $request->category_id;
        $service->service_name = $request->service_nm;
        $service->price = $request->price;
        $service->duration = $request->duration;
        $service->status = $request->has('active_status') ? 1 : 0;
        $service->tip_status = $request->has('eligible_for_tip') ? 1 : 0;
        $service->description = $request->description;
           $service->commission = $request->service_commission;

        if ($request->hasFile('service_image')) {
            if ($service->service_img && Storage::disk('public')->exists($service->service_img)) {
                Storage::disk('public')->delete($service->service_img);
            }
            $service->service_img = $request->file('service_image')->store('service_images', 'public');
        }

        $service->save();

        return response()->json([
            'status' => 1,
            'message' => 'Service updated successfully.'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($shop, $id)
    {
        $service = Service::findOrFail($id);

        // Optionally delete image from storage
        if ($service->service_img && Storage::disk('public')->exists($service->service_img)) {
            Storage::disk('public')->delete($service->service_img);
        }

        $service->delete();

        return response()->json([
            'status' => 1,
            'message' => 'Service deleted successfully.'
        ]);
    }

    public function updateHomeSection(Request $request)
    {
        $request->merge([
            'home_section' => $request->home_section ?: null,
        ]);

        $request->validate([
            'service_id' => ['required', Rule::exists('tbl_service', 'id')->where('shop_id', CurrentShop::id())],
            'home_section' => 'nullable|in:signature_grooming',
        ]);

        $service = Service::findOrFail($request->service_id);
        $service->home_section = $request->home_section ?: null;
        $service->save();

        return response()->json([
            'status' => 1,
            'message' => 'Website section updated successfully.',
        ]);
    }
}
