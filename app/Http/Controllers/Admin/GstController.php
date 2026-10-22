<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gst;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Yajra\DataTables\DataTables;

class GstController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.gst.index');
    }

    public function GetTableData(Request $request)
    {
        if ($request->ajax()) {
            $data = Gst::select(['id', 'name', 'rate', 'tax_type', 'default_status']);

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('tax_type_label', function ($row) {
                    $type = strtolower((string) ($row->tax_type ?? 'include')) === 'exclude' ? 'Exclude' : 'Include';
                    $class = $type === 'Exclude' ? 'bg-warning' : 'bg-success';
                    return '<span class="badge ' . $class . '">' . $type . '</span>';
                })
                ->addColumn('default_status_img', function ($row) {
                    if ($row->default_status == 1) {
                        $imgUrl = asset('assets/img/Completed.png');
                    } else {
                        $imgUrl = asset('assets/img/cross.png');
                    }

                    return '<img src="' . $imgUrl . '" style="max-width: 75px;" alt="">';
                })
                ->addColumn('action', function ($row) {
                    $actions = '';

                    if (auth()->user()->can('Gst-Edit')) {
                        $actions .= '
            <button type="button" class="btn view-details-btn btn-sm btn-icon btn-warning" title="Edit GST" aria-label="Edit GST">
                <span class="tf-icons bx bx-edit-alt bx-18px" aria-hidden="true"></span>
            </button>
        ';
                    }

                    if (auth()->user()->can('Gst-Delete')) {
                        $actions .= '
            <button type="button" class="btn btn-sm btn-icon btn-danger delete-gst-btn" title="Delete GST" aria-label="Delete GST">
                <span class="tf-icons bx bx-trash bx-18px" aria-hidden="true"></span>
            </button>
        ';
                    }

                    return $actions;
                })
                ->filterColumn('category_name', function ($query, $keyword) {
                    $query->where('category_name', 'like', "%$keyword%");
                })
                ->rawColumns(['action', 'default_status_img', 'tax_type_label'])
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
            'gst_name' => 'required|string|max:255',
            'gst_rate' => 'required|numeric|min:0|max:100',
            'tax_type' => 'required|in:include,exclude',
        ]);

        if ($request->has('Gst_defualt') && $request->Gst_defualt) {
            Gst::query()->update(['default_status' => 0]);
        }

        $gst = new Gst();
        $gst->created_by = Auth::id();
        $gst->rate = $request->gst_rate;
        $gst->name = $request->gst_name;
        $gst->tax_type = $request->tax_type;
        $gst->default_status = $request->has('Gst_defualt') && $request->Gst_defualt ? 1 : 0;

        $gst->save();



        return response()->json([
            'status' => 1,
            'message' => 'GST record created successfully.'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($shop, $gst)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($shop, $gst)
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
    public function update(Request $request, $shop, $gst)
    {
        $request->validate([
            'gst_name' => 'required|string|max:255',
            'gst_rate' => 'required|numeric|min:0|max:100',
            'tax_type' => 'required|in:include,exclude',
        ]);

        $gst = Gst::findOrFail($gst);

        if ($request->has('Gst_defualt') && $request->Gst_defualt) {
            Gst::where('id', '!=', $gst->id)->update(['default_status' => 0]);
        }

        $gst->rate = $request->gst_rate;
        $gst->name = $request->gst_name;
        $gst->tax_type = $request->tax_type;
        $gst->default_status = $request->has('Gst_defualt') && $request->Gst_defualt ? 1 : 0;
        $gst->save();


        return response()->json([
            'status' => 1,
            'message' => 'GST record updated successfully.'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($shop, $gst)
    {
        $gst = Gst::findOrFail($gst);
        $gst->delete();


        return response()->json([
            'status' => 1,
            'message' => 'GST record deleted successfully.'
        ]);
    }
}
