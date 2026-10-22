<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ServiceCategory;
use App\Support\CurrentShop;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

use Yajra\DataTables\DataTables;




class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $ServiceCategory = ServiceCategory::whereNull('deleted_at')->get();
        return view('admin.product.product', compact('ServiceCategory'));
    }


    public function GetTableData(Request $request)
    {
        if ($request->ajax()) {
            $data = Product::select(
                'tbl_product.id',
                'tbl_product.product_name',
                'tbl_product.created_by',
                'tbl_product.category_id',
                'tbl_product.price',
                'tbl_product.stock',
                'tbl_product.commission',
                'tbl_product.status',
                'tbl_product.product_img',
                'tbl_product.description',
                'tbl_service_category.category_name'
            )
                ->join('tbl_service_category', function ($join) {
                    $join->on('tbl_product.category_id', '=', 'tbl_service_category.id')
                        ->where('tbl_service_category.shop_id', CurrentShop::id());
                });

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('product_price', function ($row) {
                    // Properly return the formatted price string
                    return shop_money($row->price);
                })
                ->addColumn('product_commission', function ($row) {
                    // Properly return the formatted price string
                    return $row->commission . '%';
                })
                ->addColumn('product_image_url', function ($row) {
                    return Product::imageUrlForPath($row->product_img);
                })
                ->addColumn('product_status', function ($row) {
                    if ($row->status) {
                        return '<span class="badge bg-success">Active</span>';
                    } else {
                        return '<span class="badge bg-danger">Deactive</span>';
                    }
                })
                ->addColumn('action', function ($row) {
                    $actions = '';

                    // Uncomment and use permissions if needed
                    if (auth()->user()->can('Product-Edit')) {
                        $actions .= '
                    <button type="button" class="btn view-details-btn btn-sm btn-icon btn-warning">
                        <span class="tf-icons bx bx-edit-alt bx-18px"></span>
                    </button>
                ';
                    }

                    if (auth()->user()->can('Product-Delete')) {
                        $actions .= '
                    <button type="button" class="btn btn-sm btn-icon btn-danger delete-product-btn">
                        <span class="tf-icons bx bx-trash bx-18px"></span>
                    </button>
                ';
                    }

                    return $actions;
                })
                ->filterColumn('category_name', function ($query, $keyword) {
                    $query->where('tbl_product.product_name', 'like', "%{$keyword}%")
                    ->orWhere('tbl_service_category.category_name', 'like', "%{$keyword}%")
                    ->orWhere('tbl_product.price', 'like', "%{$keyword}%");
                })
                ->rawColumns(['action', 'product_price', 'product_commission', 'product_status'])
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
            'product_nm' => 'required',
            'category_id' => ['required', Rule::exists('tbl_service_category', 'id')->where('shop_id', CurrentShop::id())],
            'price' => 'required|numeric|min:0',
            'stock_qty' => 'required|integer|min:0',
            'commission' => 'required|numeric|min:0',
            'status' => 'required|boolean',
            'product_image' => 'required|image|mimes:jpeg,jpg,png,gif|max:2048',
            'description' => 'nullable|string',
        ]);

        $product = new Product();
        $product->product_name = $request->product_nm;
        $product->created_by = Auth::id();
        $product->category_id = $request->category_id;
        $product->price = $request->price;
        $product->stock = $request->stock_qty;
        $product->commission = $request->commission;
        $product->status = $request->status;
        $product->description = $request->description;

        if ($request->hasFile('product_image')) {
            $path = $request->file('product_image')->store('product_images', 'public');
            $product->product_img = $path;
        }

        $product->save();

        return response()->json([
            'status' => 1,
            'message' => 'Product created successfully.'
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
        $request->validate([
            'product_nm' => 'required',
            'category_id' => ['required', Rule::exists('tbl_service_category', 'id')->where('shop_id', CurrentShop::id())],
            'price' => 'required|numeric|min:0',
            'stock_qty' => 'required|integer|min:0',
            'commission' => 'required|numeric|min:0',
            'status' => 'required|boolean',
            'product_image' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:2048',
            'description' => 'nullable|string',
        ]);

        $product = Product::findOrFail($id);

        $product->category_id = $request->category_id;
        $product->product_name = $request->product_nm;
        $product->price = $request->price;
        $product->stock = $request->stock_qty;
        $product->commission = $request->commission;
        $product->status = $request->status;
        $product->description = $request->description;

        if ($request->hasFile('product_image')) {
            // Delete existing image if exists
            if ($product->product_img && Storage::disk('public')->exists($product->product_img)) {
                Storage::disk('public')->delete($product->product_img);
            }
            $path = $request->file('product_image')->store('product_images', 'public');
            $product->product_img = $path;
        }

        $product->save();

        return response()->json([
            'status' => 1,
            'message' => 'Product updated successfully.'
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
        $product = Product::findOrFail($id);

        // Optionally delete image from storage
        if ($product->product_img && Storage::disk('public')->exists($product->product_img)) {
            Storage::disk('public')->delete($product->product_img);
        }

        $product->delete(); // soft-delete if SoftDeletes trait is used in model

        return response()->json([
            'status' => 1,
            'message' => 'Product deleted successfully.'
        ]);
    }
}
