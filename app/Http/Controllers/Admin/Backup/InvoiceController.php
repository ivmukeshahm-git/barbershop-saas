<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\AppointmentProduct;
use App\Models\AppointmentService;
use App\Models\Customer;
use App\Models\Gst;
use App\Models\Product;
use App\Models\PaymentMethod;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use function Brick\Math\sum;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $invoices = Appointment::where('is_invoiced', 1)
            ->orderBy('appointment_id', 'DESC')
            ->with('staff') // eager load staff to avoid N+1
            ->with(['services.service']) // eager load staff to avoid N+1
            ->with(['products.product']) // eager load staff to avoid N+1
            ->get();

//        dd($invoices);


        return view('admin.invoice.index',compact('invoices'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $services = ServiceCategory::with('services')->get();
//        dd($services);
        $products = Product::all();

       $staffs= User::where('role','Staff')->forCurrentShop()->get();
        $tax = Gst::where('default_status',1)->first();

$paymentMethods = PaymentMethod::get();

        return view('admin.invoice.create-invoice',compact('services','products','staffs','tax','paymentMethods'));

        //
    }

    public function getCustomerDetails(Request $request)
    {
        $customers = Customer::withCount(['invoicedAppointments as appointments_count'])
            ->withSum(['invoicedAppointments as appointments_sum_total_amt'], 'total_amt')
            ->where('customer_phone', $request->contact)
            ->get();

        return response()->json(["status"=>200,"data"=>$customers,"msg"=>"SuccessFully Fetched user"]);
//       return view('admin.invoice.create-invoice');

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
//    public function store(Request $request)
//    {
//
//        $isCustomerExist=Customer::where('customer_phone',$request->input('phone'))->first();
//        $customerId="";
//        $name="";
//        $email="";
//        $contact="";
//        if ($isCustomerExist){
//            $customerId=$isCustomerExist->customer_id;
//            $name=$isCustomerExist->customer_name;
//            $contact=$isCustomerExist->customer_phone;
//            $email=$isCustomerExist->customer_email;
//        }else{
//            $customer= Customer::create([
//                "customer_name"=>$request->input('customer')['name']??"Guest Customer",
//                "customer_phone"=>$request->input('customer')['email']??"(555) 123-4567",
//                "customer_email"=>$request->input('customer')['contact']??"info@bluecrownbarbers.com",
//                "customer_status"=>1,
//            ]);
//
//            $name=$customer->customer_name;
//            $contact=$customer->customer_phone;
//            $email=$customer->customer_email;
//
//            $customerId =$customer->customer_id;
//        }
//
//        $datePart = Carbon::now()->format('Ymd');
//
//        // Random 4 digit number
//        $randomPart = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
//
//        // Final invoice number
//        $invoiceNumber = "INV-" . $datePart . "-" . $randomPart;
//
//        // Ensure unique invoice number
//        while (Appointment::where('invoice_number', $invoiceNumber)->exists()) {
//            $randomPart = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
//            $invoiceNumber = "INV-" . $datePart . "-" . $randomPart;
//        }
//
//        $appointMent = Appointment::create([
//            'date'       => Carbon::now(), // only date
//            'customer_id'       =>$customerId , // only date
//            'slot_start' => Carbon::now(),
//            'slot_end'   =>Carbon::now(),
//            'name'       => $name,
//            'email'      => $email,
//            'phone'      => $contact,
//            'notes'      => $request->input('notes'),
//            'status'     => 1,
//            'is_invoiced'  => 1,
//            'payment_method'  => $request->input('payment_method')??"Cash",
//            'invoice_number'  => $invoiceNumber,
//            'total_amt'  => $request->input('totals')['finalTotal'],
//            'total_discount'  => $request->input('totals')['discountAmount'],
//            'total_tax'  => $request->input('totals')['discountAmount'],
//            'total_tip'  => $request->input('totals')['tip'],
//            'comment'    => $request->input('name'),
//        ]);
//
//        $products = $request->input('products');
//        $staffIds = array_column($products, 'staffId');
//        $staffCount =count($staffIds);
//        foreach ($products as $product){
//
//            if($product['flag'] =="S"){
//                $service = Service::find($product['productId']);
//
//
//                if($staffCount==1){
//                    $finalTip =$request->input('totals')['tip'];
//                }else{
//                    $tip = $request->input('totals')['tip'];
//                    $finalTip = $tip/$staffCount;
//                }
//
//                if ($service->tip_status==0){
//                    $finalTip =0;
//                }
//
//                AppointmentService::create([
//                    'appointment_id'     => $appointMent->appointment_id,
//                    'service_id'         => $product['productId'],
//                    'staff_id'           => $product['staffId'],
//                    'product_commission' =>0,
//                    'service_commission' => 0,
//                    'service_tip'        => $finalTip,
//                    'total_amt'          =>  $product['price'],
//                    'status'             => 1,
//                    'comment'            => "Invoicing By Pos",
//                ]);
//            }else{
//
//                $service = Product::find($product['productId']);
//
//                if ($service->commission){
//                    $productCommission = ($service->price * $service->commission) / 100;
//
//                }
//
//                AppointmentProduct::create([
//                    'appointment_id'     => $appointMent->appointment_id,
//                    'product_id'         => $product['productId'],
//                    'staff_id'           => $product['staffId'],
//                    'qty' =>1,
//                    'amount' => $product['price'],
//                    'product_commision' => $productCommission??"0",
//                ]);
//            }
//
//        }
//
//        return response()->json(["status"=>200,"success"=>true,"msg"=>"SuccessFully  Created invoice"]);
//
//
//
//        //
//    }


//    public function store(Request $request)
//    {
//        $isCustomerExist = Customer::where('customer_phone', $request->input('customer')['contact'])->first();
//        $customerId = "";
//        $name = "";
//        $email = "";
//        $contact = "";
//
//        if ($isCustomerExist) {
//            $customerId = $isCustomerExist->customer_id;
//            $name = $isCustomerExist->customer_name;
//            $contact = $isCustomerExist->customer_phone;
//            $email = $isCustomerExist->customer_email;
//        } else {
//            $customer = Customer::create([
//                "customer_name"   => $request->input('customer')['name'] ?? "Guest Customer",
//                "customer_phone"  => $request->input('customer')['phone'] ?? "(555) 123-4567",
//                "customer_email"  => $request->input('customer')['email'] ?? "info@bluecrownbarbers.com",
//                "customer_status" => 1,
//            ]);
//
//            $name       = $customer->customer_name;
//            $contact    = $customer->customer_phone;
//            $email      = $customer->customer_email;
//            $customerId = $customer->customer_id;
//        }
//
//        // Generate unique invoice number
//        $datePart = Carbon::now()->format('Ymd');
//        $randomPart = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
//        $invoiceNumber = "INV-" . $datePart . "-" . $randomPart;
//
//        while (Appointment::where('invoice_number', $invoiceNumber)->exists()) {
//            $randomPart = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
//            $invoiceNumber = "INV-" . $datePart . "-" . $randomPart;
//        }
//
//        $appointMent = Appointment::create([
//            'date'           => Carbon::now(),
//            'customer_id'    => $customerId,
//            'slot_start'     => Carbon::now(),
//            'slot_end'       => Carbon::now(),
//            'name'           => $name,
//            'email'          => $email,
//            'phone'          => $contact,
//            'notes'          => $request->input('notes'),
//            'status'         => 1,
//            'is_invoiced'    => 1,
//            'payment_method' => $request->input('payment_method') ?? "Cash",
//            'invoice_number' => $invoiceNumber,
//            'total_amt'      => $request->input('totals')['finalTotal'],
//            'total_discount' => $request->input('totals')['discountAmount'],
//            'discount_type'      => $request->input('totals')['discountType'],
//            'total_tax'      => $request->input('totals')['tax'] ?? 0,
//            'total_tip'      => $request->input('totals')['tip'],
//            'comment'        => $request->input('name'),
//        ]);
//
//        $products = $request->input('products');
//        $totalTip = $request->input('totals')['tip'] ?? 0;
//
//        // ---------------- TIP DISTRIBUTION LOGIC ----------------
//        $eligibleServices = [];
//        $allStaffIds = [];
//
//        foreach ($products as $product) {
//            if ($product['flag'] == "S") {
//                $service = Service::find($product['productId']);
//                $allStaffIds[] = $product['staffId'];
//
//                if ($service && $service->tip_status == 1) {
//                    $eligibleServices[] = [
//                        'staffId'   => $product['staffId'],
//                        'serviceId' => $product['productId'],
//                    ];
//                }
//            }
//        }
//
//        $uniqueStaff = array_unique($allStaffIds);
//        $staffCount = count($uniqueStaff);
//
//        $finalTips = [];
//        $totalEligible = count($eligibleServices);
//
//        if ($staffCount == 1) {
//            // ✅ Single staff → full tip
//            $finalTips[$uniqueStaff[0]] = $totalTip;
//        } else {
//            if ($totalEligible > 0) {
//                // ✅ Eligible services exist → split per eligible service
//                $tipPerService = $totalTip / $totalEligible;
//                foreach ($eligibleServices as $srv) {
//                    if (!isset($finalTips[$srv['staffId']])) {
//                        $finalTips[$srv['staffId']] = 0;
//                    }
//                    $finalTips[$srv['staffId']] += $tipPerService;
//                }
//            } else {
//                // ✅ No eligible services → split equally among staff
//                $tipPerStaff = $staffCount > 0 ? $totalTip / $staffCount : 0;
//                foreach ($uniqueStaff as $sid) {
//                    $finalTips[$sid] = $tipPerStaff;
//                }
//            }
//        }
//        // --------------------------------------------------------
//
//        foreach ($products as $product) {
//            if ($product['flag'] == "S") {
//                $service = Service::find($product['productId']);
//                $staffTip = $finalTips[$product['staffId']] ?? 0;
//
//                $serviceCommission = 0;
//
//                if ($service && $service->commission) {
//                    $serviceCommission = ($service->price * $service->commission) / 100;
//                }
//
//                AppointmentService::create([
//                    'appointment_id'     => $appointMent->appointment_id,
//                    'service_id'         => $product['productId'],
//                    'staff_id'           => $product['staffId'],
//                    'product_commission' => 0,
//                    'service_commission' => $serviceCommission,
//                    'service_tip'        => $staffTip,
//                    'total_amt'          => $product['price'],
//                    'status'             => 1,
//                    'comment'            => "Invoicing By Pos",
//                ]);
//            } else {
//                $service = Product::find($product['productId']);
//                $productCommission = 0;
//
//                if ($service && $service->commission) {
//                    $productCommission = ($service->price * $service->commission) / 100;
//                }
//
//                AppointmentProduct::create([
//                    'appointment_id'    => $appointMent->appointment_id,
//                    'product_id'        => $product['productId'],
//                    'staff_id'          => $product['staffId'],
//                    'qty'               => 1,
//                    'amount'            => $product['price'],
//                    'product_commision' => $productCommission,
//                ]);
//            }
//        }
//
//        return response()->json([
//            "status"  => 200,
//            "success" => true,
//            "msg"     => "Successfully Created invoice"
//        ]);
//    }


    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $isCustomerExist = Customer::where('customer_phone', $request->input('customer')['contact'])->first();
            $customerId = "";
            $name = "";
            $email = "";
            $contact = "";

            if ($isCustomerExist) {
                $customerId = $isCustomerExist->customer_id;
                $name = $isCustomerExist->customer_name;
                $contact = $isCustomerExist->customer_phone;
                $email = $isCustomerExist->customer_email;
            } else {
                $customer = Customer::create([
                    "customer_name"   => $request->input('customer')['name'] ?? "Guest Customer",
                    "customer_phone"  => $request->input('customer')['phone'] ?? "(555) 123-4567",
                    "customer_email"  => $request->input('customer')['email'] ?? "info@bluecrownbarbers.com",
                    "customer_status" => 1,
                ]);

                $name       = $customer->customer_name;
                $contact    = $customer->customer_phone;
                $email      = $customer->customer_email;
                $customerId = $customer->customer_id;
            }

            // Generate unique invoice number
            $datePart = Carbon::now()->format('Ymd');
            $randomPart = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
            $invoiceNumber = "INV-" . $datePart . "-" . $randomPart;

            while (Appointment::where('invoice_number', $invoiceNumber)->exists()) {
                $randomPart = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
                $invoiceNumber = "INV-" . $datePart . "-" . $randomPart;
            }

            $appointMent = Appointment::create([
                'date'           => Carbon::now(),
                'customer_id'    => $customerId,
                'slot_start'     => Carbon::now(),
                'slot_end'       => Carbon::now(),
                'name'           => $name,
                'email'          => $email,
                'phone'          => $contact,
                'notes'          => $request->input('notes'),
                'status'         => 1,
                'is_invoiced'    => 1,
                'payment_method' => $request->input('payment_method') ?? "Cash",
                'invoice_number' => $invoiceNumber,
                'total_amt'      => $request->input('totals')['finalTotal'],
                'total_discount' => $request->input('totals')['discountAmount'],
                'discount_type'  => $request->input('totals')['discountType'],
                'total_tax'      => $request->input('totals')['tax'] ?? 0,
                'total_tip'      => $request->input('totals')['tip'],
                'comment'        => $request->input('name'),
            ]);

            $products = $request->input('products');
            $totalTip = $request->input('totals')['tip'] ?? 0;

            // ---------------- TIP DISTRIBUTION (entry-wise) ----------------
            $eligibleEntries = [];
            $serviceCount =0;
            foreach ($products as $idx => $product) {
                if ($product['flag'] == "S") {
                    $serviceCount++;
                    $service = Service::find($product['productId']);
                    if ($service && $service->tip_status == 1) {
                        $eligibleEntries[] = $idx; // store index of eligible service
                    }
                }
            }

            $perProductTips = array_fill(0, count($products), 0);

            if (count($eligibleEntries) > 0) {
                $tipPerEntry = $totalTip / count($eligibleEntries);
                foreach ($eligibleEntries as $idx) {
                    $perProductTips[$idx] = $tipPerEntry;
                }
            }
            // ---------------------------------------------------------------

            $sumOfTips=array_sum($perProductTips);

//            dd($sumOfTips);

            if ($sumOfTips==0 && $serviceCount > 0){
//                dd('ddd');
                $perProductTips=[];
                foreach ($products as $idx => $product) {
                    $tipPerEntry = $totalTip /$serviceCount;
                    if ($product['flag'] == "S") {
                        $perProductTips[$idx] = $tipPerEntry;
                    }
                }
            }

//            dd($perProductTips);

            // ---------------- SAVE SERVICES & PRODUCTS ----------------
            foreach ($products as $idx => $product) {
                if ($product['flag'] == "S") {
                    $service = Service::find($product['productId']);

                    $serviceCommission = 0;
                    if ($service && $service->commission) {
                        $serviceCommission = ($product['price'] * $service->commission) / 100;
                    }

                    AppointmentService::create([
                        'appointment_id'     => $appointMent->appointment_id,
                        'service_id'         => $product['productId'],
                        'staff_id'           => $product['staffId'],
                        'product_commission' => 0,
                        'service_commission' => $serviceCommission,
                        'service_tip'        => $perProductTips[$idx] ?? 0,
                        'total_amt'          => $product['price'],
                        'status'             => 1,
                        'comment'            => "Invoicing By POS",
                    ]);
                } else {
                    $productModel = Product::find($product['productId']);
                    $productCommission = 0;

                    if ($productModel && $productModel->commission) {
                        $productCommission = ($product['price'] * $productModel->commission) / 100;
                    }

                    AppointmentProduct::create([
                        'appointment_id'    => $appointMent->appointment_id,
                        'product_id'        => $product['productId'],
                        'staff_id'          => $product['staffId'],
                        'qty'               => 1,
                        'amount'            => $product['price'],
                        'product_commision' => $productCommission,
                    ]);
                }
            }
            // ---------------------------------------------------------

            DB::commit();

            return response()->json([
                "status"  => 200,
                "success" => true,
                "msg"     => "Successfully Created Invoice",
                "data"    => $appointMent->load('services', 'products')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                "status"  => 500,
                "success" => false,
                "msg"     => "Error: " . $e->getMessage()
            ]);
        }
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
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $services = ServiceCategory::with('services')->get();
        $appointment=Appointment::find($id);
        $customer= Customer::find($appointment->customer_id);

        $appointmentServices=AppointmentService::where('appointment_id',$id)->pluck('service_id')->countBy()->toArray();
        $appointmentProduct=AppointmentProduct::where('appointment_id',$id)->pluck('product_id')->toArray();
        $paymentMethods = PaymentMethod::all();
        $products = Product::all();

        $staffs= User::role('staff')->get();
        $tax = Gst::where('default_status',1)->first();

//        $serviceCounts = AppointmentService::where('appointment_id', $id)
//            ->pluck('service_id')
//            ->countBy()
//            ->toArray();
//
//        dd($appointmentServices);
        return view('admin.invoice.edit',compact('services','products','staffs','tax','customer','appointment','appointmentServices','appointmentProduct','paymentMethods'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
//    public function update(Request $request)
//    {
//
////        dd($request->all());
//
//        $isCustomerExist=Customer::where('customer_phone',$request->input('phone'))->first();
//        $customerId="";
//        $name="";
//        $email="";
//        $contact="";
//        if ($isCustomerExist){
//            $customerId=$isCustomerExist->customer_id;
//            $name=$isCustomerExist->customer_name;
//            $contact=$isCustomerExist->customer_phone;
//            $email=$isCustomerExist->customer_email;
//        }else{
//            $customer= Customer::create([
//                "customer_name"=>$request->input('customer')['name']??"Guest Customer",
//                "customer_phone"=>$request->input('customer')['email']??"(555) 123-4567",
//                "customer_email"=>$request->input('customer')['contact']??"info@bluecrownbarbers.com",
//                "customer_status"=>1,
//            ]);
//
//            $name=$customer->customer_name;
//            $contact=$customer->customer_phone;
//            $email=$customer->customer_email;
//
//            $customerId =$customer->customer_id;
//        }
//
//        $datePart = Carbon::now()->format('Ymd');
//
//        // Random 4 digit number
//        $randomPart = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
//
//        // Final invoice number
//        $invoiceNumber = "INV-" . $datePart . "-" . $randomPart;
//
//        // Ensure unique invoice number
//        while (Appointment::where('invoice_number', $invoiceNumber)->exists()) {
//            $randomPart = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
//            $invoiceNumber = "INV-" . $datePart . "-" . $randomPart;
//        }
//
//
//        $appointmentId=$request->input('appointment_id');
//        $appointment = Appointment::find($appointmentId); // 👈 jis appointment ko update karna hai uski ID
//
//        if ($appointment) {
//            $appointment->update([
//                'date'           => Carbon::now()->toDateString(), // only date (Y-m-d)
//                'customer_id'    => $customerId,
//                'slot_start'     => Carbon::now(),
//                'slot_end'       => Carbon::now(),
//                'name'           => $name,
//                'email'          => $email,
//                'phone'          => $contact,
//                'notes'          => $request->input('notes'),
//                'status'         => 1,
//                'is_invoiced'    => 1,
//                'payment_method' => $request->input('payment_method') ?? "Cash",
//                'invoice_number' => $invoiceNumber,
//                'total_amt'      => $request->input('totals')['finalTotal'],
//                'total_discount' => $request->input('totals')['discountAmount'],
//                'total_tax'      => $request->input('totals')['tax'],  // ✅ Fix: tax alag se lena
//                'total_tip'      => $request->input('totals')['tip'],
//                'comment'        => $request->input('name'),
//            ]);
//
//            //service and product delete
//            AppointmentProduct::where('appointment_id',$appointmentId)->delete();
//            AppointmentService::where('appointment_id',$appointmentId)->delete();
//        }
//
//
//
//        $products = $request->input('products');
//        $staffIds = array_column($products, 'staffId');
//        $staffCount =count($staffIds);
//        foreach ($products as $product){
//
//            if($product['flag'] =="S"){
//                $service = Service::find($product['productId']);
//
//
//                if($staffCount==1){
//                    $finalTip =$request->input('totals')['tip'];
//                }else{
//                    $tip = $request->input('totals')['tip'];
//                    $finalTip = $tip/$staffCount;
//                }
//
//                if ($service->tip_status==0){
//                    $finalTip =0;
//                }
//
//                AppointmentService::create([
//                    'appointment_id'     => $appointmentId,
//                    'service_id'         => $product['productId'],
//                    'staff_id'           => $product['staffId'],
//                    'product_commission' =>0,
//                    'service_commission' => 0,
//                    'service_tip'        => $finalTip,
//                    'total_amt'          =>  $product['price'],
//                    'status'             => 1,
//                    'comment'            => "Invoicing By Pos",
//                ]);
//            }else{
//
//                $service = Product::find($product['productId']);
//
//                if ($service->commission){
//                    $productCommission = ($service->price * $service->commission) / 100;
//
//                }
//
//                AppointmentProduct::create([
//                    'appointment_id'     => $appointmentId,
//                    'product_id'         => $product['productId'],
//                    'staff_id'           => $product['staffId'],
//                    'qty' =>1,
//                    'amount' => $product['price'],
//                    'product_commision' => $productCommission??"0",
//                ]);
//            }
//
//        }
//
//        return response()->json(["status"=>200,"success"=>true,"msg"=>"SuccessFully  Updated invoice"]);
//
//        //
//    }

//    public function update(Request $request)
//    {
////        dd($request->all());
//        $isCustomerExist = Customer::where('customer_phone', $request->input('customer')['contact'])->first();
//        $customerId = "";
//        $name = "";
//        $email = "";
//        $contact = "";
//
//        if ($isCustomerExist) {
//            $customerId = $isCustomerExist->customer_id;
//            $name = $isCustomerExist->customer_name;
//            $contact = $isCustomerExist->customer_phone;
//            $email = $isCustomerExist->customer_email;
//        } else {
//            $customer = Customer::create([
//                "customer_name"   => $request->input('customer')['name'] ?? "Guest Customer",
//                "customer_phone"  => $request->input('customer')['contact'] ?? "(555) 123-4567",
//                "customer_email"  => $request->input('customer')['email'] ?? "info@bluecrownbarbers.com",
//                "customer_status" => 1,
//            ]);
//
//            $name       = $customer->customer_name;
//            $contact    = $customer->customer_phone;
//            $email      = $customer->customer_email;
//            $customerId = $customer->customer_id;
//        }
//
//        // Generate unique invoice number
//        $datePart = Carbon::now()->format('Ymd');
//        $randomPart = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
//        $invoiceNumber = "INV-" . $datePart . "-" . $randomPart;
//
//        while (Appointment::where('invoice_number', $invoiceNumber)->exists()) {
//            $randomPart = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
//            $invoiceNumber = "INV-" . $datePart . "-" . $randomPart;
//        }
//
//        $appointmentId = $request->input('appointment_id');
//        $appointment = Appointment::find($appointmentId);
//
//        $inVoiceFinal = $appointment->invoice_number??$invoiceNumber;
//        if ($appointment) {
//            $appointment->update([
//                'date'           => Carbon::now()->toDateString(),
//                'customer_id'    => $customerId,
//                'slot_start'     => Carbon::now(),
//                'slot_end'       => Carbon::now(),
//                'name'           => $name,
//                'email'          => $email,
//                'phone'          => $contact,
//                'notes'          => $request->input('notes'),
//                'status'         => 1,
//                'is_invoiced'    => 1,
//                'payment_method' => $request->input('payment_method') ?? "Cash",
//                'invoice_number' => $inVoiceFinal,
//                'total_amt'      => $request->input('totals')['finalTotal'],
//                'discount_type'      => $request->input('totals')['discountType'],
//                'total_discount' => $request->input('totals')['discountAmount'],
//                'total_tax'      => $request->input('totals')['tax'],
//                'total_tip'      => $request->input('totals')['tip'],
//                'comment'        => $request->input('name'),
//            ]);
//
//            // Delete existing services/products
//            AppointmentProduct::where('appointment_id', $appointmentId)->delete();
//            AppointmentService::where('appointment_id', $appointmentId)->delete();
//        }
//
//        $products = $request->input('products');
//        $totalTip = $request->input('totals')['tip'] ?? 0;
//
//        // ---------------- TIP DISTRIBUTION LOGIC ----------------
//        $eligibleServices = [];
//        $allStaffIds = [];
//
//        foreach ($products as $product) {
//            if ($product['flag'] == "S") {
//                $service = Service::find($product['productId']);
//                $allStaffIds[] = $product['staffId'];
//
//                if ($service && $service->tip_status == 1) {
//                    $eligibleServices[] = [
//                        'staffId'   => $product['staffId'],
//                        'serviceId' => $product['productId'],
//                    ];
//                }
//            }
//        }
//
//        $uniqueStaff = array_unique($allStaffIds);
//        $staffCount = count($uniqueStaff);
//        $finalTips = [];
//        $totalEligible = count($eligibleServices);
//
//        if ($staffCount == 1) {
//            $finalTips[$uniqueStaff[0]] = $totalTip;
//        } else {
//            if ($totalEligible > 0) {
//                $tipPerService = $totalTip / $totalEligible;
//                foreach ($eligibleServices as $srv) {
//                    if (!isset($finalTips[$srv['staffId']])) $finalTips[$srv['staffId']] = 0;
//                    $finalTips[$srv['staffId']] += $tipPerService;
//                }
//            } else {
//                $tipPerStaff = $staffCount > 0 ? $totalTip / $staffCount : 0;
//                foreach ($uniqueStaff as $sid) $finalTips[$sid] = $tipPerStaff;
//            }
//        }
//        // --------------------------------------------------------
//
//        foreach ($products as $product) {
//            if ($product['flag'] == "S") {
//                $service = Service::find($product['productId']);
//                $staffTip = $finalTips[$product['staffId']] ?? 0;
//                $serviceCommission = 0;
//
//                if ($service && $service->commission) {
//                    $serviceCommission = ($service->price * $service->commission) / 100;
//                }
//                AppointmentService::create([
//                    'appointment_id'     => $appointmentId,
//                    'service_id'         => $product['productId'],
//                    'staff_id'           => $product['staffId'],
//                    'product_commission' => 0,
//                    'service_commission' => $serviceCommission,
//                    'service_tip'        => $staffTip,
//                    'total_amt'          => $product['price'],
//                    'status'             => 1,
//                    'comment'            => "Invoicing By Pos",
//                ]);
//            } else {
//                $productItem = Product::find($product['productId']);
//                $productCommission = 0;
//                if ($productItem && $productItem->commission) {
//                    $productCommission = ($productItem->price * $productItem->commission) / 100;
//                }
//
//                AppointmentProduct::create([
//                    'appointment_id'    => $appointmentId,
//                    'product_id'        => $product['productId'],
//                    'staff_id'          => $product['staffId'],
//                    'qty'               => 1,
//                    'amount'            => $product['price'],
//                    'product_commision' => $productCommission,
//                ]);
//            }
//        }
//
//        return response()->json([
//            "status"  => 200,
//            "success" => true,
//            "msg"     => "Successfully Updated invoice"
//        ]);
//    }


    public function update(Request $request)
    {
        DB::beginTransaction();
        try {
            $isCustomerExist = Customer::where('customer_phone', $request->input('customer')['contact'])->first();
            $customerId = "";
            $name = "";
            $email = "";
            $contact = "";

            if ($isCustomerExist) {
                $customerId = $isCustomerExist->customer_id;
                $name = $isCustomerExist->customer_name;
                $contact = $isCustomerExist->customer_phone;
                $email = $isCustomerExist->customer_email;
            } else {
                $customer = Customer::create([
                    "customer_name"   => $request->input('customer')['name'] ?? "Guest Customer",
                    "customer_phone"  => $request->input('customer')['contact'] ?? "(555) 123-4567",
                    "customer_email"  => $request->input('customer')['email'] ?? "info@bluecrownbarbers.com",
                    "customer_status" => 1,
                ]);

                $name       = $customer->customer_name;
                $contact    = $customer->customer_phone;
                $email      = $customer->customer_email;
                $customerId = $customer->customer_id;
            }

            // Generate invoice number if not exists
            $datePart = Carbon::now()->format('Ymd');
            $randomPart = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
            $invoiceNumber = "INV-" . $datePart . "-" . $randomPart;

            while (Appointment::where('invoice_number', $invoiceNumber)->exists()) {
                $randomPart = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
                $invoiceNumber = "INV-" . $datePart . "-" . $randomPart;
            }

            $appointmentId = $request->input('appointment_id');
            $appointment = Appointment::find($appointmentId);

            $inVoiceFinal = $appointment->invoice_number ?? $invoiceNumber;
            if ($appointment) {
                $appointment->update([
                    'date'           => Carbon::now()->toDateString(),
                    'customer_id'    => $customerId,
                    'slot_start'     => Carbon::now(),
                    'slot_end'       => Carbon::now(),
                    'name'           => $name,
                    'email'          => $email,
                    'phone'          => $contact,
                    'notes'          => $request->input('notes'),
                    'status'         => 1,
                    'is_invoiced'    => 1,
                    'payment_method' => $request->input('payment_method') ?? "Cash",
                    'invoice_number' => $inVoiceFinal,
                    'total_amt'      => $request->input('totals')['finalTotal'],
                    'discount_type'  => $request->input('totals')['discountType'],
                    'total_discount' => $request->input('totals')['discountAmount'],
                    'total_tax'      => $request->input('totals')['tax'],
                    'total_tip'      => $request->input('totals')['tip'],
                    'comment'        => $request->input('name'),
                ]);

                // Delete old services/products
                AppointmentProduct::where('appointment_id', $appointmentId)->delete();
                AppointmentService::where('appointment_id', $appointmentId)->delete();
            }

            $products = $request->input('products');
            $totalTip = $request->input('totals')['tip'] ?? 0;

            // ---------------- TIP DISTRIBUTION (entry-wise) ----------------
            $eligibleEntries = [];
            $serviceCount = 0;

            foreach ($products as $idx => $product) {
                if ($product['flag'] == "S") {
                    $serviceCount++;
                    $service = Service::find($product['productId']);
                    if ($service && $service->tip_status == 1) {
                        $eligibleEntries[] = $idx;
                    }
                }
            }

            $perProductTips = array_fill(0, count($products), 0);

            if (count($eligibleEntries) > 0) {
                $tipPerEntry = $totalTip / count($eligibleEntries);
                foreach ($eligibleEntries as $idx) {
                    $perProductTips[$idx] = $tipPerEntry;
                }
            }

            $sumOfTips = array_sum($perProductTips);

            if ($sumOfTips == 0 && $serviceCount > 0) {
                $perProductTips = [];
                foreach ($products as $idx => $product) {
                    if ($product['flag'] == "S") {
                        $perProductTips[$idx] = $totalTip / $serviceCount;
                    }
                }
            }
            // ---------------------------------------------------------------

            // ---------------- SAVE SERVICES & PRODUCTS ----------------
            foreach ($products as $idx => $product) {
                if ($product['flag'] == "S") {
                    $service = Service::find($product['productId']);

                    $serviceCommission = 0;
                    if ($service && $service->commission) {
                        $serviceCommission = ($product['price'] * $service->commission) / 100;
                    }

                    AppointmentService::create([
                        'appointment_id'     => $appointmentId,
                        'service_id'         => $product['productId'],
                        'staff_id'           => $product['staffId'],
                        'product_commission' => 0,
                        'service_commission' => $serviceCommission,
                        'service_tip'        => $perProductTips[$idx] ?? 0,
                        'total_amt'          => $product['price'],
                        'status'             => 1,
                        'comment'            => "Invoicing By POS",
                    ]);
                } else {
                    $productItem = Product::find($product['productId']);
                    $productCommission = 0;
                    if ($productItem && $productItem->commission) {
                        $productCommission = ($product['price'] * $productItem->commission) / 100;
                    }

                    AppointmentProduct::create([
                        'appointment_id'    => $appointmentId,
                        'product_id'        => $product['productId'],
                        'staff_id'          => $product['staffId'],
                        'qty'               => 1,
                        'amount'            => $product['price'],
                        'product_commision' => $productCommission,
                    ]);
                }
            }
            // ---------------------------------------------------------

            DB::commit();

            return response()->json([
                "status"  => 200,
                "success" => true,
                "msg"     => "Successfully Updated Invoice"
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                "status"  => 500,
                "success" => false,
                "msg"     => "Error: " . $e->getMessage()
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
