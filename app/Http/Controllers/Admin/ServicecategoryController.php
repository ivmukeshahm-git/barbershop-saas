<?php



namespace App\Http\Controllers\Admin;



use App\Http\Controllers\Controller;

use App\Models\ServiceCategory;
use App\Support\CurrentShop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;



use Illuminate\Support\Facades\Storage;

use Yajra\DataTables\DataTables;



class ServicecategoryController extends Controller

{

    /**

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function index()
    {

        return view('admin.service-category.service-category');

    }





    public function GetTableData(Request $request)

    {

        if ($request->ajax()) {

            $data = ServiceCategory::select([
                'id',
                'category_name',
                'description',
                'service_image',
                'service_image as service_image_path',
            ]);



            return DataTables::of($data)

                ->addIndexColumn()

                ->addColumn('service_image', function ($row) {
                    $imagePath = $row->service_image
                        ? asset('storage/' . $row->service_image)
                        : asset('assets/img/image-upload.png');

                    return '<img src="' . $imagePath . '" 

                alt="Service Image" 

                width="50" height="50">';

                })

                ->addColumn('action', function ($row) {

                    $actions = '';



                    if (auth()->user()->can('Service_Category-Edit')) {

                        $actions .= '

            <button type="button" class="btn view-details-btn btn-sm btn-icon btn-warning" title="Edit category" aria-label="Edit category">

                <span class="tf-icons bx bx-edit-alt bx-18px" aria-hidden="true"></span>

            </button>

        ';

                    }



                    if (auth()->user()->can('Service_Category-Delete')) {

                        $actions .= '

            <button type="button" class="btn btn-sm btn-icon btn-danger delete-cetegory-btn" title="Delete category" aria-label="Delete category">

                <span class="tf-icons bx bx-trash bx-18px" aria-hidden="true"></span>

            </button>

        ';

                    }



                    return $actions;

                })

                ->filterColumn('category_name', function ($query, $keyword) {

                    $query->where('category_name', 'like', "%$keyword%");

                })

                ->rawColumns(['action','service_image'])

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

        $shopId = CurrentShop::id();

        $request->validate([

            'category_nm' => [
                'required',
                'string',
                'max:255',
                Rule::unique('tbl_service_category', 'category_name')
                    ->where('shop_id', $shopId)
                    ->whereNull('deleted_at'),
            ],

            'service_image' => 'required|image|mimes:jpg,jpeg,png,gif,webp|max:2048',

            'category_description' => 'nullable|string',

        ]);



        $category = new ServiceCategory();

        $category->created_by = Auth::id(); // currently logged-in user

        $category->category_name = $request->input('category_nm');

        $category->description = $request->input('category_description');



        if ($request->hasFile('service_image')) {

            // Delete old image if exists

            if ($category->service_img && Storage::disk('public')->exists($category->service_img)) {

                Storage::disk('public')->delete($category->service_img);

            }

            $path = $request->file('service_image')->store('service_images', 'public');

            $category->service_image = $path;

        }



        $category->save();





        return response()->json([

            'status' => 1,

            'message' => 'Category created successfully.'

        ]);

    }



    /**

     * Display the specified resource.

     *

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */

    public function show($shop, $service_category)

    {

        //

    }



    /**

     * Show the form for editing the specified resource.

     *

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */

    public function edit($shop, $service_category)

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

    public function update(Request $request, $shop, $service_category)

    {
        $category = ServiceCategory::findOrFail($service_category);
        $shopId = CurrentShop::id();

        $request->validate([

            'category_nm' => [
                'required',
                'string',
                'max:255',
                Rule::unique('tbl_service_category', 'category_name')
                    ->where('shop_id', $shopId)
                    ->whereNull('deleted_at')
                    ->ignore($category->id),
            ],

            'service_image' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',

            'category_description' => 'nullable|string',

        ]);



        $category->category_name = $request->input('category_nm');

        $category->description = $request->input('category_description');

        if ($request->hasFile('service_image')) {

            // Delete old image if exists

            if ($category->service_image && Storage::disk('public')->exists($category->service_image)) {

                Storage::disk('public')->delete($category->service_image);

            }

            $path = $request->file('service_image')->store('service_images', 'public');

            $category->service_image = $path;

        }

        $category->save();



        return response()->json([

            'status' => 1,

            'message' => 'Category updated successfully.'

        ]);

    }



    /**

     * Remove the specified resource from storage.

     *

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */

    public function destroy($shop, $service_category)

    {

        $category = ServiceCategory::findOrFail($service_category);

        $category->delete(); // soft delete if using SoftDeletes trait



        return response()->json([

            'status' => 1,

            'message' => 'Category deleted successfully.'

        ]);

    }

}
