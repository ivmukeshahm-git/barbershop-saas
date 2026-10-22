@extends('admin.layout.layout')
@section('content')
    <style>
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
    <script>
        page_active_id = "Staff";
    </script>
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="bc-page-header">
            <div>
                <h4>Staff Profile</h4>
                <p>Track appointments, services, and earnings</p>
            </div>
        </div>

        <!-- Staff Profile Card -->
        <div class="card mb-4 p-4 rounded-3 shadow-sm">
            <input type="hidden" id="staff_id" name="staff_id" value="{{ $id }}">
            <div class="d-flex align-items-center justify-content-between flex-wrap">
                <div class="d-flex align-items-center gap-4">
                    <img src="{{ asset('assets/img/user.png') }}" alt="Staff Avatar" class="rounded-circle"
                        style="width:120px; height:120px; object-fit:cover;">
                    <div>
                        <h4 class="mb-1">{{ $StaffData->name }}</h4>
                        <p class="mb-1"><strong>Email:</strong> {{ $StaffData->email }}</p>
                        <p class="mb-1"><strong>Phone:</strong> {{ $StaffData->phone }}</p>
                        <p><strong>Staff since:</strong> {{ \Carbon\Carbon::parse($StaffData->created_at)->format('F j, Y') }}</p>
                    </div>
                </div>

                <div class="d-flex gap-4 mt-3 mt-md-0">
                   <div class="p-4 border rounded-3 text-center bg-light">
                        <h4 class="mb-1">{{ $totalVisit }}</h4>
                        <p>Total Visits</p>
                    </div>
                    <!--<div class="p-4 border rounded text-center bg-light">-->
                    <!--    <h4 class="mb-1">$24.20</h4>-->
                    <!--    <p>Total Spent</p>-->
                    <!--</div>-->
                </div>
            </div>
        </div>

       <div class="row g-6 mb-3">
            <!-- Card Border Shadow -->
            <div class="col-lg-3 col-sm-6">
                <div class="card card-border-shadow-primary h-100 rounded-3 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar me-4">
                                <span class="avatar-initial rounded bg-label-primary"><i
                                        class="bx bx-calendar bx-lg"></i></span>
                            </div>
                            <h4 class="mb-0">{{ $totalAppointments }}</h4>
                        </div>
                        <p class="mb-2">Complete Services</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div class="card card-border-shadow-warning h-100 rounded-3 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar me-4">
                                <span class="avatar-initial rounded bg-label-warning"><i
                                        class="bx bx-bulb bx-lg"></i></span>
                            </div>
                            <h4 class="mb-0">{{ $shopCurrencySymbol ?? '₹' }}{{ number_format($EarningData->total_service_tip, 2) }}</h4>
                        </div>
                        <p class="mb-2">Total Earn Tip</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div class="card card-border-shadow-danger h-100 rounded-3 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar me-4">
                                <span class="avatar-initial rounded bg-label-danger"><i
                                        class="bx bx-dollar-circle bx-lg"></i></span>
                            </div>
                            <h4 class="mb-0">{{ $shopCurrencySymbol ?? '₹' }}{{ number_format($EarningData->total_product_commission, 2) }}</h4>
                        </div>
                        <p class="mb-2">Total Product Commission</p>
                    </div>
                </div>
            </div>
            
             <div class="col-lg-3 col-sm-6">
                <div class="card card-border-shadow-info h-100 rounded-3 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar me-4">
                                <span class="avatar-initial rounded bg-label-info"><i
                                        class="bx bx-line-chart bx-lg"></i></span>
                            </div>
                            <h4 class="mb-0">{{ $shopCurrencySymbol ?? '₹' }}{{ number_format($EarningData->total_service_commission, 2) }}</h4>
                        </div>
                        <p class="mb-2">Total Service Commission</p>
                    </div>
                </div>
            </div>
            
            
            <div class="col-lg-3 col-sm-6">
                <div class="card card-border-shadow-info h-100 rounded-3 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar me-4">
                                <span class="avatar-initial rounded bg-label-info"><i
                                        class="bx bx-line-chart bx-lg"></i></span>
                            </div>
                            <h4 class="mb-0">{{ $shopCurrencySymbol ?? '₹' }}{{ number_format($EarningData->total_earning, 2) }}</h4>
                        </div>
                        <p class="mb-2">Total Earning</p>
                    </div>
                </div>
            </div>
            
            
           
            <!--/ Card Border Shadow -->
        </div>


        <div class="tab-content card rounded-3 shadow-sm">
            <!-- Tabs -->
            <ul class="nav nav-tabs mb-3 justify-content-start" role="tablist">
                <li class="nav-item me-2">
                    <button class="nav-link active px-4 py-2 shadow-sm" data-bs-toggle="tab"
                        data-bs-target="#appointmentsTab"
                        style="background: linear-gradient(135deg, #4e54c8, #8f94fb);
                                       color: white;
                                       border: none;
                                       box-shadow: 0 4px 8px rgba(78, 84, 200, 0.3) !important;
                                       transition: all 0.3s ease;">
                        <i class="bx bx-calendar me-1"></i> Appointments
                    </button>
                </li>
                <li class="nav-item me-2">
                    <button class="nav-link px-4 py-2 shadow-sm" data-bs-toggle="tab" data-bs-target="#servicesTab"
                        style="background: linear-gradient(135deg, #f5f7fa, #e4e7eb);
                                       color: #5a5a5a;
                                       border: none;
                                       transition: all 0.3s ease;">
                        <i class="bx bx-briefcase-alt-2 me-1"></i> Assign Services
                    </button>
                </li>
                <li class="nav-item me-2">
                    <button class="nav-link px-4 py-2 shadow-sm" data-bs-toggle="tab" data-bs-target="#earningsTab"
                        style="background: linear-gradient(135deg, #f5f7fa, #e4e7eb);
                                       color: #5a5a5a;
                                       border: none;
                                       transition: all 0.3s ease;">
                        <i class="bx bx-line-chart me-1"></i> Earnings
                    </button>
                </li>
            </ul>

            <!-- Appointments Tab -->
            <div class="tab-pane fade show active" id="appointmentsTab">
                <div class="bg-white rounded">
                    <div class="table-responsive text-nowrap">
                        <div class="d-flex justify-content-end mb-3 gap-2 align-items-center">
                            <label for="startDate" class="mb-0">Start Date:</label>
                            <input type="date" id="start_date" name="start_date" class="form-control w-auto">

                            <label for="endDate" class="mb-0">End Date:</label>
                            <input type="date" id="end_date" name="end_date" class="form-control w-auto">

                            <button class="btn btn-primary" id="filterBtn"><i class="bi bi-funnel"></i> Apply</button>
                        </div>


                        <table id="StaffAppointmentsListDataTable" class="table table-sm table-striped table-hover">
                            <thead class="border-top">
                                <tr>
                                    <th>Sr. NO.</th>
                                    <th>Name</th>
                                    <th>Contact No.</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Assign To</th>
                                    <th>Status</th>
                                    <!--<th>Action</th>-->
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Services Tab -->
            <div class="tab-pane fade" id="servicesTab">
                <div class="bg-white rounded">
                    <div class="table-responsive text-nowrap">
                        <table id="StaffServiceDetailDataTable" class="table table-sm table-striped table-hover">
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

            <!-- Earnings Tab -->
            <div class="tab-pane fade" id="earningsTab">

                <!-- Calendar Filter -->


                <!-- Earnings Table -->
                <div class="bg-white rounded">
                    <div class="table-responsive text-nowrap">
                         <table id="StaffDailyEarningDatatable" class="table table-sm table-striped table-hover">
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


    <!-- Modal -->
    <div class="modal fade" id="earningModal" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Earning Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">

                    <!-- Customer Name -->
                    <div class="d-flex align-items-center mb-3">
                        <span class="fw-bold fs-5">Earning details</span>
                        <span class="badge bg-success ms-3 px-3 py-1" style="font-size:0.9rem;">Paid</span>
                    </div>

                    <!-- Tip -->
                    <div class="d-flex align-items-start mb-2">
                        <i class='bx bx-money me-3 fs-4 mt-1 text-muted'></i>
                        <div>
                            <div class="fw-semibold">Tip</div>
                            <div class="text-muted small">—</div>
                        </div>
                    </div>

                    <!-- Commission -->
                    <div class="d-flex align-items-start mb-2">
                        <i class='bx bx-coin-stack me-3 fs-4 mt-1 text-muted'></i>
                        <div>
                            <div class="fw-semibold">Commission</div>
                            <div class="text-muted small">—</div>
                        </div>
                    </div>

                    <!-- Products Sold -->
                    <div class="d-flex align-items-start mb-2">
                        <i class='bx bx-shopping-bag me-3 fs-4 mt-1 text-muted'></i>
                        <div>
                            <div class="fw-semibold">Products Sold</div>
                            <div class="text-muted small">
                                <span>—</span>
                                <span class="text-muted ms-2">—</span>
                            </div>
                            <div class="text-muted small">
                                <span>—</span>
                                <span class="text-muted ms-2">—</span>
                            </div>
                        </div>
                    </div>

                    <!-- Total -->
                    <div class="d-flex justify-content-between align-items-center mt-3 px-1">
                        <span class="fw-semibold">Total</span>
                        <span class="fw-bold fs-5">—</span>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        document.querySelectorAll('.nav-tabs .nav-link').forEach(function (btn) {
    btn.addEventListener('click', function () {
        // Remove active class + reset styles from all buttons
        document.querySelectorAll('.nav-tabs .nav-link').forEach(function (el) {
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
 <script>
        $(document).ready(function() {
            var datatable = $('#StaffAppointmentsListDataTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.staff-profile-appointment.gettabledata') }}",
                    type: "POST",
                    data: function(d) {
                        d._token = '{{ csrf_token() }}';
                        d.id = $('#staff_id').val();
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
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
                        name: 'slot_time',
                    },
                    {
                        data: 'staff_names',
                        name: 'staff_names'
                    },
                    {
                        data: 'status_badge',
                        name: 'status_badge',
                        orderable: false,
                        searchable: false
                    },
                    // {
                    //     data: 'action',
                    //     name: 'action',
                    //     orderable: false,
                    //     searchable: false
                    // }
                ]
            });

            // 🔎 Filter Button Click
            $('#filterBtn').on('click', function() {
                datatable.ajax.reload();
            });
        });
        
        
         $(document).ready(function() {
            var StaffDailyEarning = $('#StaffDailyEarningDatatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.staff-profile-daily-earning.gettabledata') }}",
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
                    url: "{{ route('admin.staff-profile-service-assign.gettabledata') }}",
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
        });
    </script>
@endpush
