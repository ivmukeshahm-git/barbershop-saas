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
use App\Models\Setting;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\User;
use App\Services\Saas\PlanEntitlementService;
use App\Support\CurrentShop;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use function Brick\Math\sum;
use App\Mail\invoiceMail;
use Illuminate\Support\Facades\Mail;

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
            ->with(['serviceStaff',
                'productStaff'])
            ->with(['services.service']) // eager load staff to avoid N+1
            ->with(['products.product']) // eager load staff to avoid N+1
            ->get();
        $voidedInvoices = Appointment::query()
            ->where('is_invoiced', 0)
            ->whereNotNull('invoice_number')
            ->orderByDesc('appointment_id')
            ->with('customer')
            ->get();

        return view('admin.invoice.index', compact('invoices', 'voidedInvoices'));
    }

    public function getInvoiceDetails($shop, int $id)
    {
        $invoice = Appointment::query()
            ->where('is_invoiced', 1)
            ->with(['customer', 'services.service', 'products.product'])
            ->findOrFail($id);
        $setting = Setting::forCurrentShop();

        return response()->json([
            'invoice_number' => $invoice->invoice_number,
            'updated_at' => $invoice->updated_at,
            'customer_name' => $invoice->customer->customer_name ?? $invoice->name,
            'customer_email' => $invoice->customer->customer_email ?? $invoice->email,
            'customer_phone' => $invoice->customer->customer_phone ?? $invoice->phone,
            'services' => $invoice->services->map(fn ($row) => [
                'service_name' => optional($row->service)->service_name,
                'total_amt' => (float) $row->total_amt,
            ])->values(),
            'products' => $invoice->products->map(fn ($row) => [
                'product_name' => optional($row->product)->product_name,
                'amount' => (float) $row->amount,
            ])->values(),
            'service_discount' => (float) $invoice->service_discount,
            'product_discount' => (float) $invoice->product_discount,
            'total_amt' => (float) $invoice->total_amt,
            'total_tax' => (float) $invoice->total_tax,
            'tax_type' => $invoice->tax_type ?? 'include',
            'tax_rate' => (float) ($invoice->tax_rate ?? 0),
            'total_tip' => (float) $invoice->total_tip,
            'total_discount' => (float) $invoice->total_discount,
            'gstin' => $setting->gstin ?? null,
            'sac_code' => $setting->sac_code ?? null,
        ])->header('Cache-Control', 'private, no-store');
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

        $staffs = User::where('role', 'Staff')->forCurrentShop()->get();
        $tax = $this->resolveDefaultTax();

        $paymentMethods = PaymentMethod::get();

        return view('admin.invoice.create-invoice', compact('services', 'products', 'staffs', 'tax', 'paymentMethods'));

        //
    }

    public function createFromAppointment($shop, $id)
    {
        return redirect()->route('admin.invoice.edit', [
            'shop' => CurrentShop::slug(),
            'id' => $id,
        ]);
    }

    public function getCustomerDetails(Request $request)
    {
        $request->validate([
            'contact' => ['required', 'string', 'max:50'],
        ]);
        $isStaff = optional(auth()->user())->role === 'Staff';
        $normalizedPhone = preg_replace('/\D+/', '', (string) $request->contact);
        if ($normalizedPhone === '') {
            throw ValidationException::withMessages([
                'contact' => ['Enter a valid phone number.'],
            ]);
        }
        $customerIds = Customer::query()
            ->get(['customer_id', 'customer_phone'])
            ->filter(fn (Customer $customer) => preg_replace('/\D+/', '', (string) $customer->customer_phone) === $normalizedPhone)
            ->pluck('customer_id');

        $customers = Customer::withCount(['invoicedAppointments as appointments_count'])
            ->withSum(['invoicedAppointments as appointments_sum_total_amt'], 'total_amt')
            ->with(['invoicedAppointments' => function ($query) {
                $query->with(['services.service', 'products.product'])
                    ->orderByDesc('appointment_id');
            }])
            ->with(['appointments' => function ($query) {
                $query->with(['services.service'])
                    ->whereHas('services')
                    ->orderByDesc('appointment_id')
                    ->limit(1);
            }])
            ->whereIn('customer_id', $customerIds)
            ->get()
            ->map(function ($customer) use ($isStaff) {
                $lastAppointment = $customer->invoicedAppointments->first()
                    ?? $customer->appointments->first();
                $serviceNames = [];

                if ($lastAppointment) {
                    $serviceNames = $lastAppointment->services
                        ->map(function ($row) {
                            return optional($row->service)->service_name;
                        })
                        ->filter()
                        ->unique()
                        ->values()
                        ->all();
                }

                $customer->last_service = !empty($serviceNames)
                    ? implode(', ', $serviceNames)
                    : '—';
                $customer->last_purchase_services = $customer->last_service;

                if ($isStaff) {
                    // Hide financial totals from staff responses.
                    $customer->appointments_sum_total_amt = null;
                    $customer->makeHidden(['appointments_sum_total_amt']);
                }

                unset($customer->appointments);

                return $customer;
            });

        return response()->json([
            "status" => 200,
            "data" => $customers,
            "is_staff" => $isStaff,
            "msg" => "SuccessFully Fetched user",
        ]);
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
        $canAdd = app(PlanEntitlementService::class)->assertCanAddAppointment();
        if ($canAdd !== true) {
            return response()->json(['status' => false, 'message' => $canAdd], 403);
        }

        DB::beginTransaction();
        try {
            $prepared = $this->preparePosInvoice($request);
            $products = $prepared['products'];
            $totals = $prepared['totals'];
            $customer = $this->resolveInvoiceCustomer($request);

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
                'customer_id'    => $customer->customer_id,
                'slot_start'     => Carbon::now(),
                'slot_end'       => Carbon::now(),
                'name'           => $customer->customer_name,
                'email'          => $customer->customer_email,
                'phone'          => $customer->customer_phone,
                'notes'          => $request->input('notes'),
                'status'         => 1,
                'is_invoiced'    => 1,
                'payment_method' => $request->input('payment_method') ?? "Cash",
                'invoice_number' => $invoiceNumber,
                'total_amt'      => $totals['final_total'],
                'total_discount' => $totals['discount_amount'],
                'discount_type'  => 1,
                'total_tax'      => $totals['tax_amount'],
                'tax_type'       => $totals['tax_type'],
                'tax_rate'       => $totals['tax_rate'],
                'total_tip'      => $totals['tip'],
                'comment'        => $request->input('name'),
                'service_discount'  => $totals['service_discount'],
                'product_discount'   => $totals['product_discount'],
            ]);

            $this->adjustInvoiceProductStock($products, $appointMent);
            $totalTip = $totals['tip'];

            // ---------------- TIP DISTRIBUTION (entry-wise) ----------------
            $eligibleEntries = [];
            $serviceCount = 0;
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

            $sumOfTips = array_sum($perProductTips);

            //            dd($sumOfTips);

            if ($sumOfTips == 0 && $serviceCount > 0) {
                //                dd('ddd');
                $perProductTips = [];
                foreach ($products as $idx => $product) {
                    $tipPerEntry = $totalTip / $serviceCount;
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

                    $discountedPrice = $product['price'];

                    $serviceCommission = 0;
                    // Apply discount only if greater than 0
                    if ($totals['service_discount'] > 0) {
                        $mainDiscount = ($product['price'] * $totals['service_discount']) / 100;
                        $discountTotal = $discountedPrice - $mainDiscount;
                        $serviceCommission = ($discountTotal * $service->commission) / 100;
                    } else {
                        if ($service && $service->commission) {
                            $serviceCommission = ($product['price'] * $service->commission) / 100;
                        }
                    }

                    $serviceCommission = number_format($serviceCommission, 2, '.', '');



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
                    $staffCommission = User::forCurrentShop()->whereKey($product['staffId'])->value('commission');
                    $commissionRate = $staffCommission !== null
                        ? (float) $staffCommission
                        : (float) ($productModel->commission ?? 0);
                    //                    $productCommission = 0;

                    $discountedPrice = $product['price'];
                    $productCommission = 0;
                    // Apply discount only if greater than 0
                    if ($totals['product_discount'] > 0) {
                        $mainDiscount = ($product['price'] * $totals['product_discount']) / 100;
                        $discountTotal = $discountedPrice - $mainDiscount;
                        $productCommission = ($discountTotal * $commissionRate) / 100;
                    } else {
                        if ($commissionRate > 0) {
                            $productCommission = ($product['price'] * $commissionRate) / 100;
                        }
                    }


                    //                    if ($productModel && $productModel->commission) {
                    //                        $productCommission = ($product['price'] * $productModel->commission) / 100;
                    //                    }

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


 $Setting = Setting::forCurrentShop();
            if ($Setting->invoice_send_sts == 1) {
            $invoices = Appointment::where('is_invoiced', 1)
                ->orderBy('appointment_id', 'DESC')
                ->with([
                    'serviceStaff',
                    'productStaff'
                ])
                ->with(['services.service']) // eager load staff to avoid N+1
                ->with(['products.product']) // eager load staff to avoid N+1
                ->where('appointment_id', $appointMent->appointment_id)
                ->get();


     if ($customer->customer_email && $customer->customer_email !== 'info@bluecrownbarbers.com') {
         Mail::to($customer->customer_email)->send(new invoiceMail($invoices));
     }
}

           
            
            
            DB::commit();

            return response()->json([
                "status"  => 200,
                "success" => true,
                "msg"     => "Successfully Created Invoice",
                "data"    => $appointMent->load('services', 'products')
            ]);
        } catch (ValidationException $e) {
            DB::rollBack();

            return response()->json([
                'status' => 0,
                'message' => $e->getMessage(),
                'errors' => $e->errors(),
            ], 422);
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
    public function edit($shop, $id)
    {
        $services = ServiceCategory::with('services')->get();
        $appointment = Appointment::query()->findOrFail($id);
        $customer = Customer::find($appointment->customer_id);
        $invoiceTitle = $appointment->is_invoiced ? 'Edit Invoice' : 'Create Invoice';
        $invoiceSubtitle = $appointment->is_invoiced
            ? 'Update an existing POS invoice with the same step-by-step wizard'
            : 'Create an invoice for this appointment';

        $appointmentServices = AppointmentService::where('appointment_id', $id)->pluck('service_id')->countBy()->toArray();
        $appointmentProduct = AppointmentProduct::where('appointment_id', $id)
            ->get()
            ->groupBy('product_id')
            ->map(function ($rows) {
                return (int) $rows->sum(function ($row) {
                    return max(1, (int) ($row->qty ?? 1));
                });
            })
            ->toArray();

        $serviceStaffIds = AppointmentService::where('appointment_id', $id)->pluck('staff_id')->filter()->unique();
        $productStaffIds = AppointmentProduct::where('appointment_id', $id)->pluck('staff_id')->filter()->unique();
        $appointmentStaffIds = $serviceStaffIds->merge($productStaffIds)->unique()->values()->toArray();

        $existingAssignments = [];
        foreach (AppointmentService::where('appointment_id', $id)->get(['service_id', 'staff_id']) as $row) {
            $existingAssignments[] = [
                'productId' => (string) $row->service_id,
                'staffId' => (string) $row->staff_id,
                'flag' => 'S',
            ];
        }
        foreach (AppointmentProduct::where('appointment_id', $id)->get(['product_id', 'staff_id', 'qty']) as $row) {
            $qty = max(1, (int) ($row->qty ?? 1));
            for ($i = 0; $i < $qty; $i++) {
                $existingAssignments[] = [
                    'productId' => (string) $row->product_id,
                    'staffId' => (string) $row->staff_id,
                    'flag' => 'P',
                ];
            }
        }

        $paymentMethods = PaymentMethod::all();
        $products = Product::all();

        $staffs = User::where('role', 'Staff')->forCurrentShop()->get();
        $tax = $this->resolveDefaultTax();

        return view('admin.invoice.edit', compact(
            'services',
            'products',
            'staffs',
            'tax',
            'customer',
            'appointment',
            'appointmentServices',
            'appointmentProduct',
            'appointmentStaffIds',
            'existingAssignments',
            'paymentMethods',
            'invoiceTitle',
            'invoiceSubtitle'
        ));
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
            $request->validate([
                'appointment_id' => ['required', 'integer'],
            ]);
            $appointment = Appointment::query()
                ->lockForUpdate()
                ->findOrFail($request->input('appointment_id'));
            $prepared = $this->preparePosInvoice($request, $appointment);
            $products = $prepared['products'];
            $totals = $prepared['totals'];
            $customer = $this->resolveInvoiceCustomer($request);

            // Generate invoice number if not exists
            $datePart = Carbon::now()->format('Ymd');
            $randomPart = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
            $invoiceNumber = "INV-" . $datePart . "-" . $randomPart;

            while (Appointment::where('invoice_number', $invoiceNumber)->exists()) {
                $randomPart = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
                $invoiceNumber = "INV-" . $datePart . "-" . $randomPart;
            }

            $appointmentId = $appointment->appointment_id;

            $inVoiceFinal = $appointment->invoice_number ?? $invoiceNumber;
            if ($appointment) {
                $appointment->update([
                    'customer_id'    => $customer->customer_id,
                    'name'           => $customer->customer_name,
                    'email'          => $customer->customer_email,
                    'phone'          => $customer->customer_phone,
                    'notes'          => $request->input('notes'),
                    'status'         => 1,
                    'is_invoiced'    => 1,
                    'payment_method' => $request->input('payment_method') ?? "Cash",
                    'invoice_number' => $inVoiceFinal,
                    'total_amt'      => $totals['final_total'],
                    'discount_type'  => 1,
                    'total_discount' => $totals['discount_amount'],
                    'total_tax'      => $totals['tax_amount'],
                    'tax_type'       => $totals['tax_type'],
                    'tax_rate'       => $totals['tax_rate'],
                    'total_tip'      => $totals['tip'],
                    'comment'        => $request->input('name'),
                    'service_discount'  => $totals['service_discount'],
                    'product_discount'   => $totals['product_discount'],
                ]);

                $this->adjustInvoiceProductStock($products, $appointment);

                // Delete old services/products
                AppointmentProduct::where('appointment_id', $appointmentId)->delete();
                AppointmentService::where('appointment_id', $appointmentId)->delete();
            }

            $totalTip = $totals['tip'];

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

                    $discountedPrice = $product['price'];

                    $serviceCommission = 0;
                    // Apply discount only if greater than 0
                    if ($totals['service_discount'] > 0) {
                        $mainDiscount = ($product['price'] * $totals['service_discount']) / 100;
                        $discountTotal = $discountedPrice - $mainDiscount;
                        $serviceCommission = ($discountTotal * $service->commission) / 100;
                    } else {
                        if ($service && $service->commission) {
                            $serviceCommission = ($product['price'] * $service->commission) / 100;
                        }
                    }

                    $serviceCommission = number_format($serviceCommission, 2, '.', '');

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
                    $staffCommission = User::forCurrentShop()->whereKey($product['staffId'])->value('commission');
                    $commissionRate = $staffCommission !== null
                        ? (float) $staffCommission
                        : (float) ($productItem->commission ?? 0);
                    $discountedPrice = $product['price'];
                    $productCommission = 0;
                    // Apply discount only if greater than 0
                    if ($totals['product_discount'] > 0) {
                        $mainDiscount = ($product['price'] * $totals['product_discount']) / 100;
                        $discountTotal = $discountedPrice - $mainDiscount;
                        $productCommission = ($discountTotal * $commissionRate) / 100;
                    } else {
                        if ($commissionRate > 0) {
                            $productCommission = ($product['price'] * $commissionRate) / 100;
                        }
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


 $Setting = Setting::forCurrentShop();
            if ($Setting->invoice_send_sts == 1) {
          $invoices = Appointment::where('is_invoiced', 1)
                ->orderBy('appointment_id', 'DESC')
                ->with([
                    'serviceStaff',
                    'productStaff'
                ])
                ->with(['services.service']) // eager load staff to avoid N+1
                ->with(['products.product']) // eager load staff to avoid N+1
                ->where('appointment_id', $appointmentId)
                ->get();


            if ($customer->customer_email && $customer->customer_email !== 'info@bluecrownbarbers.com') {
                Mail::to($customer->customer_email)->send(new invoiceMail($invoices));
            }
            }
            
            DB::commit();

            return response()->json([
                "status"  => 200,
                "success" => true,
                "msg"     => "Successfully Updated Invoice"
            ]);
        } catch (ValidationException $e) {
            DB::rollBack();

            return response()->json([
                'status' => 0,
                'message' => $e->getMessage(),
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                "status"  => 500,
                "success" => false,
                "msg"     => "Error: " . $e->getMessage()
            ]);
        }
    }
    
    
    public function SendInvoiceMail(Request $request)
    {
        $request->validate([
            'appointment_id' => ['required', 'integer'],
        ]);

        $invoice = Appointment::query()
            ->where('is_invoiced', 1)
            ->with('customer')
            ->findOrFail($request->input('appointment_id'));
        $customerEmail = $invoice->customer->customer_email ?? $invoice->email;

        if (!$customerEmail || !filter_var($customerEmail, FILTER_VALIDATE_EMAIL)) {
            return response()->json([
                'status' => 0,
                'message' => 'This customer does not have a valid email address.',
            ], 422);
        }

        $invoices = Appointment::query()
            ->where('is_invoiced', 1)
            ->whereKey($invoice->getKey())
            ->with(['serviceStaff', 'productStaff', 'services.service', 'products.product'])
            ->get();

        Mail::to($customerEmail)->send(new invoiceMail($invoices));


        return response()->json([
            "status"  => 1,
            "message"     => "Mail Successfully Send"
        ]);
    }

    private function preparePosInvoice(Request $request, ?Appointment $appointment = null): array
    {
        $validated = $request->validate([
            'products' => ['required', 'array', 'min:1'],
            'products.*.productId' => ['required', 'integer', 'min:1'],
            'products.*.staffId' => ['required', 'integer', 'min:1'],
            'products.*.flag' => ['required', 'in:S,P'],
            'totals.product_discount' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'totals.service_discount' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'totals.tip' => ['nullable', 'numeric', 'min:0'],
        ]);

        $products = [];
        $serviceSubtotal = 0.0;
        $productSubtotal = 0.0;
        foreach ($validated['products'] as $item) {
            $model = $item['flag'] === 'S'
                ? Service::query()->find($item['productId'])
                : Product::query()->find($item['productId']);
            if (!$model) {
                throw ValidationException::withMessages([
                    'products' => ['A selected service or product is no longer available.'],
                ]);
            }

            $price = (float) $model->price;
            $products[] = array_merge($item, ['price' => $price]);
            if ($item['flag'] === 'S') {
                $serviceSubtotal += $price;
            } else {
                $productSubtotal += $price;
            }
        }

        $serviceDiscount = (float) ($request->input('totals.service_discount', 0));
        $productDiscount = (float) ($request->input('totals.product_discount', 0));
        $discountAmount = round(
            ($serviceSubtotal * $serviceDiscount / 100) + ($productSubtotal * $productDiscount / 100),
            2
        );
        $discountedSubtotal = round(
            $serviceSubtotal * (1 - $serviceDiscount / 100)
                + $productSubtotal * (1 - $productDiscount / 100),
            2
        );
        $tax = $appointment && $appointment->tax_rate !== null
            ? null
            : $this->resolveDefaultTax();
        $taxRate = (float) ($appointment->tax_rate ?? $tax->rate);
        $taxType = strtolower((string) ($appointment->tax_type ?? ($tax->tax_type ?? 'include'))) === 'exclude'
            ? 'exclude'
            : 'include';
        $taxAmount = $taxType === 'exclude'
            ? round($discountedSubtotal * $taxRate / 100, 2)
            : round($discountedSubtotal * $taxRate / (100 + $taxRate), 2);
        $tip = round((float) $request->input('totals.tip', 0), 2);

        return [
            'products' => $products,
            'totals' => [
                'service_discount' => $serviceDiscount,
                'product_discount' => $productDiscount,
                'discount_amount' => $discountAmount,
                'discounted_subtotal' => $discountedSubtotal,
                'tax_amount' => $taxAmount,
                'tax_type' => $taxType,
                'tax_rate' => $taxRate,
                'tip' => $tip,
                'final_total' => round(
                    $discountedSubtotal + ($taxType === 'exclude' ? $taxAmount : 0) + $tip,
                    2
                ),
            ],
        ];
    }

    private function resolveInvoiceCustomer(Request $request): Customer
    {
        $customerInput = $request->input('customer', []);
        $customerId = $request->input('customer_id') ?? ($customerInput['customer_id'] ?? null);
        if ($customerId) {
            $customer = Customer::query()->find($customerId);
            if (!$customer) {
                throw ValidationException::withMessages([
                    'customer_id' => ['The selected customer is not available.'],
                ]);
            }

            return $customer;
        }

        $name = trim((string) ($customerInput['name'] ?? ''));
        $phone = trim((string) ($customerInput['contact'] ?? ''));
        $email = trim((string) ($customerInput['email'] ?? ''));
        $normalizedPhone = preg_replace('/\D+/', '', $phone);

        if ($normalizedPhone !== '') {
            $customer = Customer::query()->get()->first(function (Customer $candidate) use ($normalizedPhone) {
                return preg_replace('/\D+/', '', (string) $candidate->customer_phone) === $normalizedPhone;
            });
            if ($customer) {
                $customer->update(array_filter([
                    'customer_name' => $name !== '' ? $name : null,
                    'customer_email' => $email !== '' ? $email : null,
                ]));

                return $customer;
            }
        }

        if ($name === '' && $phone === '' && $email === '') {
            $name = 'Guest Customer';
            $phone = '(555) 123-4567';
            $email = 'info@bluecrownbarbers.com';
        }

        return Customer::create([
            'customer_name' => $name !== '' ? $name : 'Guest Customer',
            'customer_phone' => $phone !== '' ? $phone : '(555) 123-4567',
            'customer_email' => $email !== '' ? $email : 'info@bluecrownbarbers.com',
            'customer_status' => 1,
        ]);
    }

    private function adjustInvoiceProductStock(array $items, Appointment $appointment): void
    {
        $newQuantities = collect($items)
            ->where('flag', 'P')
            ->groupBy('productId')
            ->map(fn ($rows) => (int) $rows->sum(fn ($row) => max(1, (int) ($row['qty'] ?? 1))));
        $previousQuantities = AppointmentProduct::query()
            ->where('appointment_id', $appointment->appointment_id)
            ->get()
            ->groupBy('product_id')
            ->map(fn ($rows) => (int) $rows->sum(fn ($row) => max(1, (int) ($row->qty ?? 1))));
        $wasDeducted = $appointment->inventory_deducted_at !== null;
        $productIds = $newQuantities->keys()->merge($previousQuantities->keys())->unique();

        foreach ($productIds as $productId) {
            $product = Product::query()->whereKey($productId)->lockForUpdate()->first();
            if (!$product) {
                throw ValidationException::withMessages([
                    'products' => ['A selected product is no longer available.'],
                ]);
            }

            $oldQuantity = $wasDeducted ? (int) ($previousQuantities[$productId] ?? 0) : 0;
            $newQuantity = (int) ($newQuantities[$productId] ?? 0);
            $change = $newQuantity - $oldQuantity;
            if ($change > (int) $product->stock) {
                throw ValidationException::withMessages([
                    'products' => ["Insufficient stock for {$product->product_name}."],
                ]);
            }

            if ($change !== 0) {
                $product->decrement('stock', $change);
            }
        }

        $appointment->inventory_deducted_at = now();
        $appointment->save();
    }

    /**
     * Ensure the current shop has a usable default tax row for POS/invoice screens.
     */
    protected function resolveDefaultTax(): Gst
    {
        $tax = Gst::query()->where('default_status', 1)->first()
            ?? Gst::query()->orderBy('id')->first();

        if ($tax) {
            if (!(int) $tax->default_status) {
                $tax->update(['default_status' => 1]);
            }

            return $tax;
        }

        $payload = [
            'name' => 'GST 18%',
            'rate' => 18,
            'default_status' => 1,
            'created_by' => auth()->id() ?? 0,
        ];

        if (\Illuminate\Support\Facades\Schema::hasColumn('tbl_gst', 'tax_type')) {
            $payload['tax_type'] = 'include';
        }

        return Gst::create($payload);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($shop, $id)
    {
        DB::transaction(function () use ($id) {
            $invoice = Appointment::query()
                ->where('is_invoiced', 1)
                ->lockForUpdate()
                ->findOrFail($id);

            if ($invoice->inventory_deducted_at !== null) {
                $quantities = AppointmentProduct::query()
                    ->where('appointment_id', $invoice->appointment_id)
                    ->get()
                    ->groupBy('product_id')
                    ->map(fn ($rows) => (int) $rows->sum(fn ($row) => max(1, (int) ($row->qty ?? 1))));

                foreach ($quantities as $productId => $quantity) {
                    $product = Product::query()->whereKey($productId)->lockForUpdate()->firstOrFail();
                    $product->increment('stock', $quantity);
                }
            }

            $invoice->is_invoiced = 0;
            $invoice->save();
        });

        return response()->json([
            'status' => 1,
            'message' => 'Invoice voided. Its history is retained and the invoice can be restored.',
        ]);
    }

    public function restoreInvoice($shop, $id)
    {
        DB::transaction(function () use ($id) {
            $invoice = Appointment::query()
                ->where('is_invoiced', 0)
                ->whereNotNull('invoice_number')
                ->lockForUpdate()
                ->findOrFail($id);

            if ($invoice->inventory_deducted_at !== null) {
                $items = AppointmentProduct::query()
                    ->where('appointment_id', $invoice->appointment_id)
                    ->get()
                    ->map(fn ($row) => [
                        'flag' => 'P',
                        'productId' => $row->product_id,
                        'qty' => max(1, (int) ($row->qty ?? 1)),
                    ])
                    ->all();

                $invoice->inventory_deducted_at = null;
                $this->adjustInvoiceProductStock($items, $invoice);
            }
            $invoice->is_invoiced = 1;
            $invoice->save();
        });

        return response()->json([
            'status' => 1,
            'message' => 'Invoice restored successfully.',
        ]);
    }
}
