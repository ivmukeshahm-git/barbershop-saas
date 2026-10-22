@push('css')
    <style>
        #TodayAppointmentsDataTable {
            background: #f5f5f5
        }

        #UpcomingAppointmentsDataTable {
            background: #f5f5f5
        }

        #TopServicesDataTable {
            background: #f5f5f5
        }

        #TopStaffDataTable {
            background: #f5f5f5
        }

        .nav-link {
            position: relative;
            overflow: hidden;
        }

        .nav-link:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15) !important;
        }

        .nav-link:before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: all 0.6s ease;
        }

        .nav-link:hover:before {
            left: 100%;
        }

        .nav-link.active {
            font-weight: 600;
            box-shadow: 0 4px 8px rgba(78, 84, 200, 0.3) !important;
        }
    </style>
@endpush


@extends('admin.layout.layout')
@section('content')
    <script>
        page_active_id = "Reports";
    </script>
    <!-- Content wrapper -->
    <div class="content-wrapper">
        <!-- Content -->

        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="bc-page-header">
                <div>
                    <h4>Reports</h4>
                    <p>Analyze revenue, appointments, staff performance, and sales</p>
                </div>
            </div>

            <div class="card rounded-3 shadow-sm p-4 mb-5">
                <h5 class="mb-4 fw-semibold">Reports Filter</h5>
                <form id="appointmentFilterForm">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-2">
                            <label for="startDate" class="form-label">Start Date</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" />
                        </div>

                        <!-- End Date -->
                        <div class="col-md-2">
                            <label for="endDate" class="form-label">End Date</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" />
                        </div>



                        <!-- Staff Name -->
                        <div class="col-md-2">
                            <label for="staff_id" class="form-label">Staff Name</label>
                            <select class="form-select select2" id="staff_id" name="staff_id">
                                <option value="">All Staff</option>
                                @foreach ($StaffData as $row)
                                    <option value="{{ $row->id }}">{{ $row->name }}</option>
                                @endforeach
                            </select>
                        </div>


                        <div class="col-md-1">
                            <button type="button" class="btn btn-primary px-4" id="filterBtn">
                                Filter
                            </button>

                        </div>
                        <div class="col-md-1">
                            <a type="button" class="btn btn-primary px-4" href="">
                                Clear
                            </a>

                        </div>
                    </div>


                </form>
            </div>
            <div class="row g-4 mb-4 bc-dashboard-stats">
                <div class="col-xl-3 col-sm-6">
                    <div class="bc-stat-card bc-stat-primary">
                        <div class="bc-stat-icon"><i class="bi bi-cash-stack"></i></div>
                        <div class="bc-stat-label">Total Revenue</div>
                        <p class="bc-stat-value" id="totalRevenue">{{ $shopCurrencySymbol ?? '₹' }}0.00</p>
                        <span class="bc-stat-trend"><i class="bi bi-graph-up-arrow"></i> Revenue</span>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6">
                    <div class="bc-stat-card bc-stat-info">
                        <div class="bc-stat-icon"><i class="bi bi-calendar2-check"></i></div>
                        <div class="bc-stat-label">Total Appointment</div>
                        <p class="bc-stat-value" id="totalAppointments">0</p>
                        <span class="bc-stat-trend"><i class="bi bi-calendar-event"></i> Bookings</span>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6">
                    <div class="bc-stat-card bc-stat-danger">
                        <div class="bc-stat-icon"><i class="bi bi-percent"></i></div>
                        <div class="bc-stat-label">Discount Amount</div>
                        <p class="bc-stat-value" id="totalDiscount">0</p>
                        <span class="bc-stat-trend"><i class="bi bi-tag"></i> Discount</span>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6">
                    <div class="bc-stat-card bc-stat-success">
                        <div class="bc-stat-icon"><i class="bi bi-wallet2"></i></div>
                        <div class="bc-stat-label">Total Tips</div>
                        <p class="bc-stat-value" id="totalTips">{{ $shopCurrencySymbol ?? '₹' }}0.00</p>
                        <span class="bc-stat-trend"><i class="bi bi-heart"></i> Tips</span>
                    </div>
                </div>
            </div>


            <div class="container m-0 p-0">

                <!-- Tab Content -->
                <div class="tab-content card">

                    <!-- Nav Tabs -->
                    <ul class="nav nav-tabs mb-3" role="tablist">
                        <li class="nav-item me-2">
                            <button class="nav-link active px-4 py-2 shadow-sm" data-bs-toggle="tab"
                                data-bs-target="#appointmentsTab"
                                style="background: linear-gradient(135deg, #4e54c8, #8f94fb);
                                       color: white;
                                       border: none;
                                       box-shadow: 0 4px 8px rgba(78, 84, 200, 0.3) !important;
                                       transition: all 0.3s ease;">
                                Earning
                            </button>
                        </li>
                        <li class="nav-item me-2">
                            <button class="nav-link px-4 py-2 shadow-sm" data-bs-toggle="tab"
                                data-bs-target="#paymentMethodsTab"
                                style="background: linear-gradient(135deg, #f5f7fa, #e4e7eb);
                                       color: #5a5a5a;
                                       border: none;
                                       transition: all 0.3s ease;">
                                Payment Methods
                            </button>
                        </li>

                        @if ($allowsStaffReports ?? true)
                        <li class="nav-item me-2">
                            <button class="nav-link px-4 py-2 shadow-sm" data-bs-toggle="tab" data-bs-target="#staffTab"
                                style="background: linear-gradient(135deg, #f5f7fa, #e4e7eb);
                                       color: #5a5a5a;
                                       border: none;
                                       transition: all 0.3s ease;">
                                Staff
                            </button>
                        </li>
                        @endif
                        
                        <li class="nav-item me-2">
                            <button class="nav-link px-4 py-2 shadow-sm" data-bs-toggle="tab"
                                data-bs-target="#InvoiceTab"
                                style="background: linear-gradient(135deg, #f5f7fa, #e4e7eb);
                                       color: #5a5a5a;
                                       border: none;
                                       transition: all 0.3s ease;">
                                Invoice
                            </button>
                        </li>


                        <li class="nav-item me-2">
                            <button class="nav-link px-4 py-2 shadow-sm" data-bs-toggle="tab"
                                data-bs-target="#ProductTab"
                                style="background: linear-gradient(135deg, #f5f7fa, #e4e7eb);
                                       color: #5a5a5a;
                                       border: none;
                                       transition: all 0.3s ease;">
                                Product
                            </button>
                        </li>
                        
                         @if ($allowsAdvancedReports ?? true)
                         <li class="nav-item me-2">
                            <button class="nav-link px-4 py-2 shadow-sm" data-bs-toggle="tab"
                                data-bs-target="#TopSellingProductTab"
                                style="background: linear-gradient(135deg, #f5f7fa, #e4e7eb);
                                       color: #5a5a5a;
                                       border: none;
                                       transition: all 0.3s ease;">
                                Top Selling Products
                            </button>
                        </li>
                        @endif
                    </ul>

                    @if (!($allowsStaffReports ?? true) || !($allowsAdvancedReports ?? true))
                        <div class="alert alert-info mx-3 mt-2 mb-0">
                            @if(!empty($plan))
                                Your <strong>{{ $plan->name }}</strong> plan includes limited reports.
                                @if(!($allowsStaffReports ?? true)) Staff performance reports require Growth or Business. @endif
                                @if(!($allowsAdvancedReports ?? true)) Advanced product reports require Business. @endif
                                <a href="{{ route('admin.billing.index') }}">View plan benefits</a>
                            @endif
                        </div>
                    @endif


                    <style>
                        .staff-work-earning-count-box {
                            border: 1px solid #eee;
                            padding: 10px;
                            background: #eee;
                        }

                        .earning-count-box {
                            background: #fff;
                            padding: 10px
                        }

                        #StaffEarningReports {
                            display: none;
                        }
                    </style>

                    <div class="tab-pane fade show active" id="appointmentsTab">
                        <div class="bg-white rounded">
                            <div class="table-responsive text-nowrap">

                                <div class="staff-work-earning-count-box" id="StaffEarningReports">
                                    <div class="row">
                                        <div class="col-lg-3">
                                            <div class="earning-count-box">
                                                <h5 class="mb-1">Total Earning</h5>
                                                <h4 class="mb-0" style="font-weight: 600;" id="StaffTotalEarningAmt">{{ $shopCurrencySymbol ?? '₹' }} 0.00</h4>
                                            </div>
                                        </div>

                                        <div class="col-lg-3">
                                            <div class="earning-count-box">
                                                <h5 class="mb-1">Product Commission</h5>
                                                <h4 class="mb-0" style="font-weight: 600;"
                                                    id="StaffProductCommisionEarningAmt">{{ $shopCurrencySymbol ?? '₹' }}0.00</h4>
                                            </div>
                                        </div>

                                        <div class="col-lg-3">
                                            <div class="earning-count-box">
                                                <h5 class="mb-1">Service Commission</h5>
                                                <h4 class="mb-0" style="font-weight: 600;"
                                                    id="StaffServiceCommisionEarningAmt">{{ $shopCurrencySymbol ?? '₹' }}0.00</h4>
                                            </div>
                                        </div>

                                        <div class="col-lg-3">
                                            <div class="earning-count-box">
                                                <h5 class="mb-1">Tip</h5>
                                                <h4 class="mb-0" style="font-weight: 600;" id="StaffTipEarningAmt">{{ $shopCurrencySymbol ?? '₹' }} 0.00</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <table id="ReportAppointmentsListDataTable"
                                    class="table table-hover">
                                    <thead class="border-top">
                                        <tr>
                                            <th>Sr. NO.</th>
                                            <th>Name</th>
                                            <th>Date</th>
                                            <th>Time</th>
                                            <th>Sub Total</th>
                                            <th>Discount</th>
                                            <th>Total Amount</th>
                                            <th>Total Tip</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>


                    <!-- Payment Methods Tab -->
                    <div class="tab-pane fade" id="paymentMethodsTab">
                        <div class="row g-4">
                            <!-- Table Section -->
                            <div class="col-md-12">
                                <div class=" p-3">
                                    <h5 class="card-title">Payment Method Analytics</h5>
                                    <div class="table-responsive">
                                        <table id="ReportPaymentMethodsTable" class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Method</th>
                                                    <th>Revenue</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>


                    <!-- Staff Categories Tab -->
                    <div class="tab-pane fade" id="staffTab">
                        @if ($allowsStaffReports ?? true)
                        <!-- Table Section -->
                        <div class="col-md-12">
                            <div class="p-3">
                                <h5 class="card-title">Staff Performance</h5>
                                <div class="table-responsive">
                                    <table id="StaffPerformanceDataTable"
                                        class="table table-hover align-middle">
                                        <thead>
                                            <tr>
                                                <th>Staff Name</th>
                                                <th>Appointments</th>
                                                <th>Tips</th>
                                                <th>Service Comm.</th>
                                                <th>Product Comm.</th>
                                                <th>Total Earn</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>

                                </div>
                            </div>
                        </div>
                        @endif

                    </div>


     <div class="tab-pane fade" id="InvoiceTab">
                        <!-- Table Section -->
                        <div class="col-md-12">
                            <div class="p-3">
                                <h5 class="card-title">Invoices</h5>
                                <div class="table-responsive">
                                    <table id="ReportInvoiceDataTable"
                                        class="table table-hover align-middle">
                                        <thead>
                                            <tr>
                                                <th>Sr. No.</th>
                                                <th>Invoice No.</th>
                                                <th>Customer Name</th>
                                                <th>Staff Name</th>
                                                <th>Date</th>
                                                <th>Status</th>
                                                <th>Amount</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>

                                </div>
                            </div>
                        </div>


                    </div>


                    <div class="tab-pane fade" id="ProductTab">
                        <!-- Table Section -->
                        <div class="col-md-12">
                            <div class="p-3">
                                <h5 class="card-title">Product Sell History</h5>
                                <div class="table-responsive">
                                    <table id="ProductPerformanceDataTable"
                                        class="table table-hover align-middle">
                                        <thead>
                                            <tr>
                                                <th>Sr. No.</th>
                                                <th>Product Name</th>
                                                <th>Sell By</th>
                                                <th>Customer Name</th>
                                                <th>Date</th>
                                                <th>Product Price</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>

                                </div>
                            </div>
                        </div>


                    </div>
                    
                    <div class="tab-pane fade" id="TopSellingProductTab">
                        <h5 class="card-title mb-3">Top Selling Products</h5>

                        <div class="staff-work-earning-count-box mb-4" id="StaffProductEarningReports">
                            <div class="row g-3">
                                <div class="col-xl col-md-4 col-sm-6">
                                    <div class="bc-mini-stat bc-mini-stat-primary">
                                        <div class="bc-mini-stat-icon"><i class="bi bi-box-seam"></i></div>
                                        <div class="bc-mini-stat-body">
                                            <div class="bc-mini-stat-label">
                                                Total Product Amount
                                                <button type="button" class="btn btn-sm p-0 ms-1"
                                                    data-bs-toggle="tooltip" data-bs-placement="right"
                                                    title="This is the actual total price of all products sold.">
                                                    <i class="bi bi-info-circle"></i>
                                                </button>
                                            </div>
                                            <div class="bc-mini-stat-value" id="ProductTotalAmount">{{ $shopCurrencySymbol ?? '₹' }}0.00</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xl col-md-4 col-sm-6">
                                    <div class="bc-mini-stat bc-mini-stat-warning">
                                        <div class="bc-mini-stat-icon"><i class="bi bi-tag"></i></div>
                                        <div class="bc-mini-stat-body">
                                            <div class="bc-mini-stat-label">
                                                Total Discount Paid
                                                <button type="button" class="btn btn-sm p-0 ms-1"
                                                    data-bs-toggle="tooltip" data-bs-placement="right"
                                                    title="This shows the total discount given on sold products.">
                                                    <i class="bi bi-info-circle"></i>
                                                </button>
                                            </div>
                                            <div class="bc-mini-stat-value" id="ProductTotalDiscountPaid">{{ $shopCurrencySymbol ?? '₹' }}0.00</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xl col-md-4 col-sm-6">
                                    <div class="bc-mini-stat bc-mini-stat-purple">
                                        <div class="bc-mini-stat-icon"><i class="bi bi-percent"></i></div>
                                        <div class="bc-mini-stat-body">
                                            <div class="bc-mini-stat-label">
                                                Total Commission Paid
                                                <button type="button" class="btn btn-sm p-0 ms-1"
                                                    data-bs-toggle="tooltip" data-bs-placement="right"
                                                    title="This shows the total commission paid to staff from product sales.">
                                                    <i class="bi bi-info-circle"></i>
                                                </button>
                                            </div>
                                            <div class="bc-mini-stat-value" id="ProductTotalCommissionPaid">{{ $shopCurrencySymbol ?? '₹' }}0.00</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xl col-md-4 col-sm-6">
                                    <div class="bc-mini-stat bc-mini-stat-success">
                                        <div class="bc-mini-stat-icon"><i class="bi bi-cash-stack"></i></div>
                                        <div class="bc-mini-stat-body">
                                            <div class="bc-mini-stat-label">
                                                Total Revenue
                                                <button type="button" class="btn btn-sm p-0 ms-1"
                                                    data-bs-toggle="tooltip" data-bs-placement="right"
                                                    title="This shows the final revenue after deducting discounts and commissions.">
                                                    <i class="bi bi-info-circle"></i>
                                                </button>
                                            </div>
                                            <div class="bc-mini-stat-value" id="ProductTotalRevenue">{{ $shopCurrencySymbol ?? '₹' }}0.00</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xl col-md-4 col-sm-6">
                                    <div class="bc-mini-stat bc-mini-stat-info">
                                        <div class="bc-mini-stat-icon"><i class="bi bi-bag-check"></i></div>
                                        <div class="bc-mini-stat-body">
                                            <div class="bc-mini-stat-label">
                                                Sell Unit
                                                <button type="button" class="btn btn-sm p-0 ms-1"
                                                    data-bs-toggle="tooltip" data-bs-placement="right"
                                                    title="This shows the total number of product units sold.">
                                                    <i class="bi bi-info-circle"></i>
                                                </button>
                                            </div>
                                            <div class="bc-mini-stat-value" id="ProductTotalSellUnit">0</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        
                        <div class="col-md-12">
                            <div class="p-3">
                                <div class="table-responsive">
                                    <table id="TopSellingProductDataTable"
                                        class="table table-hover align-middle">
                                        <thead>
                                            <tr>
                                                <th>Sr. No.</th>
                                                <th>Product Name</th>
                                                <th>Product Price</th>
                                                <th>Sell Unit</th>
                                                <th>Total Discount Paid</th>
                                                <th>Total Commission</th>
                                                <th>Total Revenue</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>

                                </div>
                            </div>
                        </div>


                    </div>
                </div>

            </div>
        </div>
        <!--staff modal-->
        <div class="modal fade" id="staffDetailsModal" data-bs-backdrop="static" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content shadow-lg rounded-4">

                    <div class="modal-header border-0 rounded-top">
                        <h5 class="modal-title fw-bold">Staff Performance Details</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        <div class="d-flex align-items-center mb-4">
                            <img src="{{ asset('assets/img/user.svg') }}" class="rounded-circle me-4 shadow-sm"
                                width="80" height="80" alt="Staff Image">
                            <div>
                                <h5 class="fw-bold mb-1">Staff Performance Details</h5>
                                <p class="text-muted mb-0">Actual staff performance data</p>
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-3">
                                <div class="p-3 text-center bg-light rounded shadow-sm">
                                    <div class="text-uppercase text-muted small">Appointments</div>
                                    <div class="fs-4 fw-bold text-primary">—</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="p-3 text-center bg-light rounded shadow-sm">
                                    <div class="text-uppercase text-muted small">Total Tips</div>
                                    <div class="fs-4 fw-bold text-success">—</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="p-3 text-center bg-light rounded shadow-sm">
                                    <div class="text-uppercase text-muted small">Total Discounts</div>
                                    <div class="fs-4 fw-bold text-warning">—</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="p-3 text-center bg-light rounded shadow-sm">
                                    <div class="text-uppercase text-muted small">Products Sold</div>
                                    <div class="fs-4 fw-bold text-info">—</div>
                                </div>
                            </div>
                        </div>

                        <ul class="nav nav-tabs mb-3 border-bottom">
                            <li class="nav-item">
                                <button class="nav-link active fw-semibold text-dark" data-bs-toggle="tab"
                                    data-bs-target="#tipsTab"
                                    style="background: linear-gradient(135deg, #4e54c8, #8f94fb);
                                           color: white;
                                           border: none;
                                           box-shadow: 0 4px 8px rgba(78, 84, 200, 0.3) !important;
                                           transition: all 0.3s ease;">Tips</button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link fw-semibold text-dark" data-bs-toggle="tab"
                                    data-bs-target="#discountsTab"
                                    style="background: linear-gradient(135deg, #f5f7fa, #e4e7eb);
                                           color: #5a5a5a;
                                           border: none;
                                           transition: all 0.3s ease;">Discounts</button>
                            </li>
                        </ul>

                        <div class="tab-content">

                            <div class="tab-pane fade show active" id="tipsTab">
                                <div class="table-responsive">
                                    <table class="table table-hover table-bordered align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Date</th>
                                                <th>Customer</th>
                                                <th>Tip Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($recentTipInvoices as $invoice)
                                                <tr>
                                                    <td>{{ \Carbon\Carbon::parse($invoice->date)->format('d/m/Y') }}</td>
                                                    <td>{{ $invoice->customer->customer_name ?? $invoice->name ?? 'Guest Customer' }}</td>
                                                    <td><span class="badge bg-success">{{ shop_money($invoice->total_tip) }}</span></td>
                                                </tr>
                                            @empty
                                                <tr><td colspan="3" class="text-center text-muted">No tips recorded.</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="discountsTab">
                                <div class="table-responsive">
                                    <table class="table table-hover table-bordered align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Date</th>
                                                <th>Customer</th>
                                                <th>Discount Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($recentDiscountInvoices as $invoice)
                                                <tr>
                                                    <td>{{ \Carbon\Carbon::parse($invoice->date)->format('d/m/Y') }}</td>
                                                    <td>{{ $invoice->customer->customer_name ?? $invoice->name ?? 'Guest Customer' }}</td>
                                                    <td><span class="badge bg-warning">{{ shop_money($invoice->total_discount) }}</span></td>
                                                </tr>
                                            @empty
                                                <tr><td colspan="3" class="text-center text-muted">No discounts recorded.</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        </div>

                    </div>

                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-outline-secondary rounded-pill px-4"
                            data-bs-dismiss="modal">Close</button>
                    </div>

                </div>
            </div>
        </div>

        <!--service details modAL -->
        <div class="modal fade" id="serviceDetailsModal" data-bs-backdrop="static" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content rounded-3 shadow-sm">

                    <div class="modal-header border-0">
                        <h5 class="modal-title fw-bold">Service Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        <h4 class="fw-semibold" style="color: #bc1e2d;">Service performance</h4>
                        <p class="text-muted mb-3">
                            Beard Trimmed â€¢ 30 minutes
                        </p>

                        <div class="row g-3 my-4">

                            <div class="col-md-3">
                                <div class="p-3 border rounded text-center bg-light">
                                    <div class="text-uppercase text-muted small">Bookings</div>
                                    <div class="fs-4 fw-bold">—</div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="p-3 border rounded text-center bg-light">
                                    <div class="text-uppercase text-muted small">Revenue</div>
                                    <div class="fs-4 fw-bold">—</div>
                                </div>
                            </div>


                            <div class="col-md-3">
                                <div class="p-3 border rounded text-center bg-light">
                                    <div class="text-uppercase text-muted small">Avg. Revenue</div>
                                    <div class="fs-4 fw-bold">—</div>
                                </div>
                            </div>

                        </div>

                        <!-- Simple Tabs for Tips and Discounts -->
                        <ul class="nav nav-tabs mb-3">
                            <li class="nav-item">
                                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tipsTab"
                                    style="background: linear-gradient(135deg, #4e54c8, #8f94fb);
                                           color: white;
                                           border: none;
                                           box-shadow: 0 4px 8px rgba(78, 84, 200, 0.3) !important;
                                           transition: all 0.3s ease;">Tips</button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#discountsTab"
                                    style="background: linear-gradient(135deg, #f5f7fa, #e4e7eb);
                                           color: #5a5a5a;
                                           border: none;
                                           transition: all 0.3s ease;">Discounts</button>
                            </li>
                        </ul>

                        <div class="tab-content">

                            <!-- Tips Tab -->
                            <div class="tab-pane fade show active" id="tipsTab">
                                <div class="row g-3">

                                    <div class="col-md-4">
                                        <div class="p-3 border rounded text-center bg-light">
                                            <div class="text-uppercase text-muted small">Total Tips</div>
                                            <div class="fs-4 fw-bold">{{ shop_money($totalTips) }}</div>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="p-3 border rounded text-center bg-light">
                                            <div class="text-uppercase text-muted small">Avg Tip per Invoice</div>
                                            <div class="fs-4 fw-bold">{{ shop_money($tipInvoiceCount ? $totalTips / $tipInvoiceCount : 0) }}</div>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="p-3 border rounded text-center bg-light">
                                            <div class="text-uppercase text-muted small">Invoices w/ Tip</div>
                                            <div class="fs-4 fw-bold">{{ $tipInvoiceCount }}</div>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <!-- Discounts Tab -->
                            <div class="tab-pane fade" id="discountsTab">
                                <div class="row g-3">

                                    <div class="col-md-4">
                                        <div class="p-3 border rounded text-center bg-light">
                                            <div class="text-uppercase text-muted small">Total Discounts</div>
                                            <div class="fs-4 fw-bold">{{ shop_money($totalDiscounts) }}</div>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="p-3 border rounded text-center bg-light">
                                            <div class="text-uppercase text-muted small">Invoices w/ Discount</div>
                                            <div class="fs-4 fw-bold">{{ $discountInvoiceCount }}</div>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="p-3 border rounded text-center bg-light">
                                            <div class="text-uppercase text-muted small">Invoices w/ Discount</div>
                                            <div class="fs-4 fw-bold">{{ $discountInvoiceCount }}</div>
                                        </div>
                                    </div>

                                </div>
                            </div>

                        </div>

                    </div>

                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    </div>

                </div>
            </div>
        </div>


    </div>

    <!-- / Content -->
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            
        var TopSellingProductDataTable = null;
        @if ($allowsAdvancedReports ?? true)
        TopSellingProductDataTable = $('#TopSellingProductDataTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.top-selling-product-report.gettabledata') }}",
                    type: "POST",
                    data: function(d) {
                        d._token = '{{ csrf_token() }}';
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                        d.staff_id = $('#staff_id').val();
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'product_name',
                        name: 'product_name'
                    },
                    {
                        data: 'product_price',
                        name: 'product_price'
                    },
                    {
                        data: 'sell_unit',
                        name: 'sell_unit'

                    },
                    {
                        data: 'total_discount',
                        name: 'total_discount'

                    },
                    {
                        data: 'total_commission',
                        name: 'total_commission'
                    },
                    {
                        data: 'total_revenue',
                        name: 'total_revenue'
                    }
                ]
            });
        @endif
            
            var ProductPerformanceDataTable = $('#ProductPerformanceDataTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.sell-product-report.gettabledata') }}",
                    type: "POST",
                    data: function(d) {
                        d._token = '{{ csrf_token() }}';
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                        d.staff_id = $('#staff_id').val();
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'product_name',
                        name: 'product_name'
                    },
                    {
                        data: 'staff_name',
                        name: 'staff_name'
                    },
                    {
                        data: 'customer_name',
                        name: 'customer_name'

                    },
                    {
                        data: 'sell_date',
                        name: 'sell_date'
                    },
                    {
                        data: 'amount',
                        name: 'amount'
                    }
                ]
            });



            var ReportInvoiceDataTable = $('#ReportInvoiceDataTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.customer-report-invoice.gettabledata') }}",
                    type: "POST",
                    data: function(d) {
                        d._token = '{{ csrf_token() }}';
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                        d.staff_id = $('#staff_id').val();
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'invoice_number',
                        name: 'invoice_number'
                    },
                    {
                        data: 'customer_name',
                        name: 'customer_name'
                    },
                    {
                        data: 'staff_name',
                        name: 'staff_name'
                    },
                    {
                        data: 'date',
                        name: 'date'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'amount',
                        name: 'amount'
                    }, // ✅ corrected (was total_amt before)
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            // Initialize DataTables
            var ReportAppointmentsList = $('#ReportAppointmentsListDataTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.reports.get-report-appointment') }}",
                    type: "POST",
                    data: function(d) {
                        d._token = '{{ csrf_token() }}';
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                        d.staff_id = $('#staff_id').val();
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'customer_name',
                        name: 'customer_name'
                    },
                    {
                        data: 'date',
                        name: 'date'
                    },
                    {
                        data: 'slot_time',
                        name: 'slot_time'
                    },
                    {
                        data: 'sub_total',
                        name: 'sub_total'
                    },
                    {
                        data: 'service_discount',
                        name: 'service_discount'
                    },
                    {
                        data: 'service_total_amt',
                        name: 'service_total_amt'
                    },
                    {
                        data: 'service_tip',
                        name: 'service_tip'
                    },
                    {
                        data: 'status_badge',
                        name: 'status_badge',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            var ReportPaymentList = $('#ReportPaymentMethodsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.reports.get-report-payment') }}",
                    type: "POST",
                    data: function(d) {
                        d._token = '{{ csrf_token() }}';
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                        d.staff_id = $('#staff_id').val();
                    }
                },
                columns: [{
                        data: 'method',
                        name: 'method'
                    },
                    {
                        data: 'total_revenue',
                        name: 'total_revenue'
                    }
                ]
            });

            var StaffPerformanceDataTable = $('#StaffPerformanceDataTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.reports.get-staff-performance') }}", // ðŸ‘ˆ à¤…à¤ªà¤¨à¤¾ route à¤¯à¤¹à¤¾à¤‚ à¤¡à¤¾à¤²à¥‡à¤‚
                    type: "POST",
                    data: function(d) {
                        d._token = '{{ csrf_token() }}';
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                        d.staff_id = $('#staff_id').val();
                    }
                },
                columns: [{
                        data: 'staff_name',
                        name: 'staff_name'
                    },
                    {
                        data: 'appointments',
                        name: 'appointments'
                    },

                    {
                        data: 'tips',
                        name: 'tips'
                    },
                    {
                        data: 'service_commission',
                        name: 'service_commission'
                    },
                    {
                        data: 'product_commission',
                        name: 'product_commission'
                    },
                    {
                        data: 'total_earn',
                        name: 'total_earn'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            var exportableReportTables = [
                [ProductPerformanceDataTable, 'Product sell history'],
                [ReportInvoiceDataTable, 'Invoices'],
                [ReportAppointmentsList, 'Appointments'],
                [ReportPaymentList, 'Payment methods'],
                [StaffPerformanceDataTable, 'Staff performance']
            ];
            if (TopSellingProductDataTable) {
                exportableReportTables.unshift([TopSellingProductDataTable, 'Top selling products']);
            }
            exportableReportTables.forEach(function(item) {
                const table = item[0];
                const button = $('<button type="button" class="btn btn-outline-primary btn-sm report-export-button me-2 mb-2">Export CSV</button>');
                button.attr('aria-label', 'Export ' + item[1] + ' report');
                button.data('table-id', table.table().node().id);
                $(table.table().node()).closest('.dataTables_wrapper').prepend(button);
            });

            $(document).on('click', '.report-export-button', function() {
                const table = $('#' + $(this).data('table-id')).DataTable();
                const settings = table.settings()[0];
                const columns = settings.aoColumns
                    .filter(function(column) {
                        return column.bSearchable !== false
                            && typeof column.mData === 'string'
                            && column.mData !== 'action'
                            && column.mData !== 'DT_RowIndex';
                    })
                    .map(function(column) {
                        return { key: column.mData, label: column.sTitle || column.mData };
                    });
                const currentParams = table.ajax.params();
                if (!currentParams) {
                    Swal.fire('Export unavailable', 'Load the report before exporting it.', 'warning');
                    return;
                }
                const params = $.extend(true, {}, currentParams);
                params.start = 0;
                params.length = -1;
                params.draw = 1;

                $.ajax({
                    url: table.ajax.url(),
                    type: 'POST',
                    data: params,
                    dataType: 'json'
                }).done(function(response) {
                    const rows = response.data || [];
                    if (response.recordsFiltered != null && rows.length < Number(response.recordsFiltered)) {
                        Swal.fire('Export incomplete', 'The report server returned only part of the filtered result.', 'error');
                        return;
                    }

                    const decodeCell = function(value) {
                        const text = String(value == null ? '' : value).replace(/<[^>]*>/g, '');
                        const textarea = document.createElement('textarea');
                        textarea.innerHTML = text;
                        return textarea.value;
                    };
                    const csvCell = function(value) {
                        return '"' + decodeCell(value).replace(/"/g, '""') + '"';
                    };
                    const csv = [
                        columns.map(function(column) { return csvCell(column.label); }).join(','),
                        ...rows.map(function(row) {
                            return columns.map(function(column) { return csvCell(row[column.key]); }).join(',');
                        })
                    ].join('\r\n');
                    const blob = new Blob(['\ufeff' + csv], { type: 'text/csv;charset=utf-8;' });
                    const link = document.createElement('a');
                    link.href = URL.createObjectURL(blob);
                    link.download = table.table().node().id + '.csv';
                    document.body.appendChild(link);
                    link.click();
                    link.remove();
                    setTimeout(function() { URL.revokeObjectURL(link.href); }, 1000);
                }).fail(function(xhr) {
                    const message = xhr.responseJSON && xhr.responseJSON.message
                        ? xhr.responseJSON.message
                        : 'The report could not be exported.';
                    Swal.fire('Export failed', message, 'error');
                });
            });


            // ðŸ”Ž Function to update summary counts
            function updateSummary(json) {
                if (json.summary) {
                    $('#totalRevenue').text(json.summary.total_revenue);
                    $('#totalAppointments').text(json.summary.total_appointments);
                    $('#totalDiscount').text(json.summary.total_discount);
                    $('#totalTips').text(json.summary.total_tips);
                }
            }
            
             function updateProductSummary(json) {
                if (json.summary) {
                    $('#ProductTotalRevenue').text(json.summary.total_revenue);
                     $('#ProductTotalAmount').text(json.summary.total_product_amount);
                    $('#ProductTotalDiscountPaid').text(json.summary.total_discount);
                    $('#ProductTotalCommissionPaid').text(json.summary.total_commission);
                    $('#ProductTotalSellUnit').text(json.summary.total_units_sold);
                }
            }

            // ðŸ”Ž Filter Button Click
            $('#filterBtn').on('click', function() {
                ReportAppointmentsList.ajax.reload(function(json) {
                    updateSummary(json);


                    $('#StaffTotalEarningAmt').text(json.summary.staff_total_earning);
                    $('#StaffProductCommisionEarningAmt').text(json.summary
                        .staff_total_product_commission);
                    $('#StaffServiceCommisionEarningAmt').text(json.summary
                        .staff_total_service_commission);
                    $('#StaffTipEarningAmt').text(json.summary.staff_total_service_tip);


                    document.getElementById("StaffEarningReports").style.display = "block";
                });
                ReportPaymentList.ajax.reload();
                StaffPerformanceDataTable.ajax.reload();
                 ReportInvoiceDataTable.ajax.reload();
                ProductPerformanceDataTable.ajax.reload();
                if (TopSellingProductDataTable) {
                    TopSellingProductDataTable.ajax.reload(function(json) {
                         updateProductSummary(json);
                    });
                }
            });

            // âœ… Initial load of all tables and summary
            ReportAppointmentsList.ajax.reload(function(json) {
                updateSummary(json);
            });
            
            if (TopSellingProductDataTable) {
                TopSellingProductDataTable.ajax.reload(function(json) {
                    updateProductSummary(json);
                });
            }



            ReportPaymentList.ajax.reload();
            StaffPerformanceDataTable.ajax.reload();

        });
    </script>
    <script>
        document.querySelectorAll('.nav-tabs .nav-link').forEach(function(btn) {
            btn.addEventListener('click', function() {
                // Remove active class + reset styles from all buttons
                document.querySelectorAll('.nav-tabs .nav-link').forEach(function(el) {
                    el.classList.remove('active');
                    el.style.background = "linear-gradient(135deg, #f5f7fa, #e4e7eb)";
                    el.style.color = "#5a5a5a";
                });

                // Add active style to the clicked button
                this.classList.add('active');
                this.style.background = "linear-gradient(135deg, #4e54c8, #8f94fb)";
                this.style.color = "white";
            });
        });
    </script>
@endpush
