<?php



namespace App\Http\Controllers\Admin;



use App\Http\Controllers\Controller;

use App\Models\PaymentMethod;

use Illuminate\Http\Request;





class PaymentmethodController extends Controller

{

    /**

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function index()

    {

        //

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

            'name' => 'required|string|max:255|unique:tbl_payment_method,name',

        ]);



        $paymentMethod = PaymentMethod::create([

            'name' => $request->name,

        ]);



        return response()->json([
            'status' => 1,
            'message' => 'Payment method created successfully',
        ]);

    }



    /**

     * Display the specified resource.

     *

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */

    public function show($shop, $paymentMethod)

    {

        //

    }



    /**

     * Show the form for editing the specified resource.

     *

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */

    public function edit($shop, $paymentMethod)

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

    public function update(Request $request, $shop, $paymentMethod)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:tbl_payment_method,name,' . $paymentMethod,
        ]);

        $paymentMethod = PaymentMethod::findOrFail($paymentMethod);

        $paymentMethod->update([

            'name' => $request->name,

        ]);



        return response()->json([

            'status' => 1,

            'message' => 'Payment method updated successfully',

        ]);

    }



    /**

     * Remove the specified resource from storage.

     *

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */

    public function destroy($shop, $paymentMethod)
    {
        $paymentMethod = PaymentMethod::findOrFail($paymentMethod);

        $paymentMethod->delete();



        return response()->json([

            'status' => 1,

            'message' => 'Payment method deleted successfully'

        ]);

    }

}
