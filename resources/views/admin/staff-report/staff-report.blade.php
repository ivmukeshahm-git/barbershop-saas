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
                    <h4>Staff Report</h4>
                    <p>Review appointments, earnings, and service performance</p>
                </div>
            </div>

            <div class="card rounded-3 shadow-sm p-4 mb-5">
                <h5 class="mb-4 fw-semibold">Reports Filter</h5>
                <form id="appointmentFilterForm">

                    <input type="hidden" id="staff_id" name="staff_id" value="{{ $id }}">
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

                        <!-- Status -->
                        <div class="col-md-2">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select select2" id="status" name="status">
                                <option value="">All</option>
                                <option value="0">Pending</option>
                                <option value="3">Confirm</option>
                                <option value="2">Cancel</option>
                                <option value="1">Completed</option>
                                <option value="4">Reschedule</option>
                            </select>
                        </div>


                        <div class="col-md-1">
                            <button type="button" class="btn btn-primary px-4" id="filterBtn">
                                <i class="bi bi-funnel"></i> Filter
                            </button>

                        </div>
                        <div class="col-md-1">
                            <a type="button" class="btn btn-label-secondary px-4" href="">
                                <i class="bi bi-arrow-counterclockwise"></i> Clear
                            </a>

                        </div>
                    </div>


                </form>
            </div>


            <div class="container m-0 p-0">
                <!-- Tab Content -->
                <div class="tab-content card rounded-3 shadow-sm">

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
                                Appointments
                            </button>
                        </li>
                        <li class="nav-item me-2">
                            <button class="nav-link px-4 py-2 shadow-sm" data-bs-toggle="tab"
                                data-bs-target="#ServiceEarningTab"
                                style="background: linear-gradient(135deg, #f5f7fa, #e4e7eb);
                                       color: #5a5a5a;
                                       border: none;
                                       transition: all 0.3s ease;">
                                Service Earning
                            </button>
                        </li>
                        {{-- <li class="nav-item me-2">
                            <button class="nav-link px-4 py-2 shadow-sm" data-bs-toggle="tab"
                                data-bs-target="#serviceCategoriesTab"
                                style="background: linear-gradient(135deg, #f5f7fa, #e4e7eb);
                                       color: #5a5a5a;
                                       border: none;
                                       transition: all 0.3s ease;">
                                Service Categories
                            </button>
                        </li> --}}
                        <li class="nav-item me-2">
                            <button class="nav-link px-4 py-2 shadow-sm" data-bs-toggle="tab" data-bs-target="#DailyEarningTab"
                                style="background: linear-gradient(135deg, #f5f7fa, #e4e7eb);
                                       color: #5a5a5a;
                                       border: none;
                                       transition: all 0.3s ease;">
                                Daily Earning
                            </button>
                        </li>
                    </ul>


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
                                    class="table table-sm table-striped table-hover">
                                    <thead class="border-top">
                                        <tr>
                                            <th>Sr. NO.</th>
                                            <th>Name</th>
                                            <th>Contact No.</th>
                                            <th>Date</th>
                                            <th>Time</th>
                                            <th>Discount</th>
                                            <th>Total Tip</th>
                                            <th>Total Amount</th>
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
                    <div class="tab-pane fade" id="ServiceEarningTab">
                        <div class="row g-4">
                            <!-- Table Section -->
                            <div class="col-md-12">
                                <div class=" p-3">
                                    <h5 class="card-title">Service Earning</h5>
                                    <div class="table-responsive">
                                        <table id="StaffServiceDetailDataTable"
                                            class="table table-sm table-striped table-hover">
                                            <thead class="border-top">
                                                <tr>
                                                    <th>Sr. NO.</th>
                                                    <th>Service Name</th>
                                                    <th>Service Price</th>
                                                    <th>Service Time</th>
                                                    <th>Total Appointment</th>
                                                    <th>Total Service Commision</th>
                                                    <th>Total Service Tip</th>
                                                    <th>Total Earn</th>
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
                    <div class="tab-pane fade" id="DailyEarningTab">
                        <!-- Table Section -->
                        <div class="col-md-12">
                            <div class="p-3">
                                <h5 class="card-title">Daily Earning</h5>
                                <div class="table-responsive">
                                    <table id="StaffDailyEarningDatatable"
                                        class="table table-sm table-striped table-hover">
                                        <thead class="border-top">
                                            <tr>
                                                <th>Sr. NO.</th>
                                                <th>Date</th>
                                                <th>Product Commision</th>
                                                <th>Service Tip</th>
                                                <th>Service Commision</th>
                                                <th>Total</th>
                                            </tr>
                                        </thead>
                                        <tbody id="earningsTableBody">

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
                            <img src="{{ asset('assets/img/avatars/1.png') }}" class="rounded-circle me-4 shadow-sm"
                                width="80" height="80" alt="Staff Image">
                            <div>
                                <h5 class="fw-bold mb-1">Staff Performance</h5>
                                <p class="text-muted mb-0">Current report data</p>
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
                                            <!-- Dynamic rows here -->
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
                                            <!-- Dynamic rows here -->
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
                            Actual service metrics
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

                            <!--<div class="col-md-3">-->
                            <!--    <div class="p-3 border rounded text-center bg-light">-->
                            <!--        <div class="text-uppercase text-muted small">Growth Rate</div>-->
                            <!--        <div class="fs-4 fw-bold text-success">100.0%</div>-->
                            <!--    </div>-->
                            <!--</div>-->

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
                                            <div class="fs-4 fw-bold">—</div>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="p-3 border rounded text-center bg-light">
                                            <div class="text-uppercase text-muted small">Avg Tip %</div>
                                            <div class="fs-4 fw-bold">—</div>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="p-3 border rounded text-center bg-light">
                                            <div class="text-uppercase text-muted small">Invoices w/ Tip</div>
                                            <div class="fs-4 fw-bold">—</div>
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
                                            <div class="fs-4 fw-bold">—</div>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="p-3 border rounded text-center bg-light">
                                            <div class="text-uppercase text-muted small">Avg Discount %</div>
                                            <div class="fs-4 fw-bold">—</div>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="p-3 border rounded text-center bg-light">
                                            <div class="text-uppercase text-muted small">Invoices w/ Discount</div>
                                            <div class="fs-4 fw-bold">—</div>
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

            // Initialize DataTables
            var ReportAppointmentsList = $('#ReportAppointmentsListDataTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.staff-report-appointment.gettabledata') }}",
                    type: "POST",
                    data: function(d) {
                        d._token = '{{ csrf_token() }}';
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                        d.status = $('#status').val();
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
                        data: 'customer_phone',
                        name: 'customer_phone'
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
                        data: 'service_discount',
                        name: 'service_discount'
                    },
                    {
                        data: 'service_tip',
                        name: 'service_tip'
                    },
                    {
                        data: 'service_total_amt',
                        name: 'service_total_amt'
                    },
                    {
                        data: 'status_badge',
                        name: 'status_badge',
                        orderable: false,
                        searchable: false
                    }
                ]
            });


            var StaffDailyEarning = $('#StaffDailyEarningDatatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.staff-report-earning.gettabledata') }}",
                    type: "POST",
                    data: function(d) {
                        d.id = $("#staff_id").val();
                        d._token = '{{ csrf_token() }}';
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'appointment_date',
                        name: 'appointment_date'
                    },

                    {
                        data: 'product_commision_earn',
                        name: 'product_commision_earn'
                    },
                    {
                        data: 'service_tip_earn',
                        name: 'service_tip_earn'
                    },
                    {
                        data: 'service_commission',
                        name: 'service_commission'
                    },
                    {
                        data: 'total_earning',
                        name: 'total_earning'
                    },
                ]
            });


            var TopServicesDataTable = $('#StaffServiceDetailDataTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.staff-report-assign-service.gettabledata') }}",
                    type: "POST",
                    data: function(d) {
                        d.id = $("#staff_id").val();
                        d._token = '{{ csrf_token() }}';
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'service_name',
                        name: 'service_name'
                    },
                    {
                        data: 'service_price',
                        name: 'service_price'
                    },
                    {
                        data: 'service_duration',
                        name: 'service_duration'
                    },
                    {
                        data: 'appointmentCount',
                        name: 'appointmentCount'
                    },
                    {
                        data: 'total_service_commission',
                        name: 'total_service_commission'
                    },
                    {
                        data: 'total_service_tip',
                        name: 'total_service_tip'
                    },
                    {
                        data: 'totalRevenue',
                        name: 'totalRevenue'
                    }
                ]
            });

            // 🔎 Function to update summary counts
            function updateSummary(json) {
                if (json.summary) {
                    $('#totalRevenue').text(json.summary.total_revenue);
                    $('#totalAppointments').text(json.summary.total_appointments);
                    $('#totalVisits').text(json.summary.total_visits);
                    $('#totalTips').text(json.summary.total_tips);
                }
            }

            // 🔎 Filter Button Click
            $('#filterBtn').on('click', function() {
                ReportAppointmentsList.ajax.reload(function(json) {
                    updateSummary(json);


                    $('#StaffTotalEarningAmt').text(json.summary.staff_total_earning);
                    $('#StaffProductCommisionEarningAmt').text(json.summary
                        .staff_total_product_commission);
                    $('#StaffServiceCommisionEarningAmt').text(json.summary
                        .staff_total_service_commission);
                    $('#StaffTipEarningAmt').text(json.summary.staff_total_service_tip);
                });

            });

            // ✅ Initial load of all tables and summary
            ReportAppointmentsList.ajax.reload(function(json) {
                updateSummary(json);

                 $('#StaffTotalEarningAmt').text(json.summary.staff_total_earning);
                    $('#StaffProductCommisionEarningAmt').text(json.summary
                        .staff_total_product_commission);
                    $('#StaffServiceCommisionEarningAmt').text(json.summary
                        .staff_total_service_commission);
                    $('#StaffTipEarningAmt').text(json.summary.staff_total_service_tip);
            });


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
