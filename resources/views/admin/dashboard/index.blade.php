@push('css')
    <style>
        #TodayAppointmentsDataTable,
        #UpcomingAppointmentsDataTable,
        #TopServicesDataTable,
        #TopStaffDataTable {
            background: transparent;
        }
        #dashboard-staff-calendar {
            scroll-margin-top: 90px;
        }
    </style>
    <link rel="stylesheet" href="{{ asset('assets/css/admin-staff-calendar.css') }}?v=20" />
    <link rel="stylesheet" href="{{ asset('assets/css/admin-service-picker.css') }}?v=2" />
@endpush


@extends('admin.layout.layout')
@section('content')
  @php
        use Illuminate\Support\Facades\Auth;
        $user = Auth::user();
    @endphp
    
    <script>
        page_active_id = "Home";
    </script>
    <!-- Content wrapper -->
    <div class="content-wrapper">
        <!-- Content -->

        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="bc-page-header">
                <div>
                    <h4>Dashboard</h4>
                    <p>Overview of appointments, revenue, and performance</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.invoice.create') }}" class="btn btn-success">
                        <i class="bi bi-receipt me-1"></i> Create Invoice
                    </a>
                    <button type="button" class="btn btn-primary" id="dashboardScrollToCalendarBtn">
                        <i class="bi bi-calendar3 me-1"></i> Calendar
                    </button>
                </div>
            </div>

            <div class="row g-4 g-xl-4 bc-dashboard-stats">
               @if ($user && ($user->role == 'Admin' || $user->role == 'Staff'))
                    <div class="col-xl-3 col-sm-6">
                        <div class="bc-stat-card bc-stat-primary">
                            <div class="bc-stat-icon"><i class="bi bi-cash-stack"></i></div>
                            <div class="bc-stat-label">Total Revenue</div>
                            <p class="bc-stat-value">{{ $shopCurrencySymbol ?? '₹' }}{{ number_format($totalRevenue, 2) }}</p>
                            <span class="bc-stat-trend"><i class="bi bi-graph-up-arrow"></i> Revenue</span>
                        </div>
                    </div>
                @endif

                @if ($user && ($user->role == 'Admin' || $user->role == 'Staff'))
                    <div class="col-xl-3 col-sm-6">
                        <div class="bc-stat-card bc-stat-info">
                            <div class="bc-stat-icon"><i class="bi bi-calendar2-week"></i></div>
                            <div class="bc-stat-label">Upcoming Appointments</div>
                            <p class="bc-stat-value">{{ $totalAppointment }}</p>
                            <span class="bc-stat-trend"><i class="bi bi-clock"></i> Upcoming</span>
                        </div>
                    </div>
                @endif

                @if ($user && $user->role == 'Admin')
                    <div class="col-xl-3 col-sm-6">
                        <div class="bc-stat-card bc-stat-purple">
                            <div class="bc-stat-icon"><i class="bi bi-people"></i></div>
                            <div class="bc-stat-label">Customers</div>
                            <p class="bc-stat-value">{{ $totalCustomers }}</p>
                            <span class="bc-stat-trend"><i class="bi bi-person-plus"></i> Total</span>
                        </div>
                    </div>
                @endif

                @if ($user && $user->role == 'Staff')
                    <div class="col-xl-3 col-sm-6">
                        <div class="bc-stat-card bc-stat-warning">
                            <div class="bc-stat-icon"><i class="bi bi-wallet2"></i></div>
                            <div class="bc-stat-label">Tips Received</div>
                            <p class="bc-stat-value">{{ $shopCurrencySymbol ?? '₹' }}{{ number_format($totalTipRevenue, 2) }}</p>
                            <span class="bc-stat-trend"><i class="bi bi-heart"></i> Tips</span>
                        </div>
                    </div>
                @endif

                @if ($user && ($user->role == 'Admin' || $user->role == 'Staff'))
                    <div class="col-xl-3 col-sm-6">
                        <div class="bc-stat-card bc-stat-success">
                            <div class="bc-stat-icon"><i class="bi bi-check2-circle"></i></div>
                            <div class="bc-stat-label">Completed</div>
                            <p class="bc-stat-value">{{ $CompleteAppointments }}</p>
                            <span class="bc-stat-trend"><i class="bi bi-check-lg"></i> Done</span>
                        </div>
                    </div>
                @endif

                @if ($user && $user->role == 'Staff')
                    <div class="col-xl-3 col-sm-6">
                        <div class="bc-stat-card bc-stat-danger">
                            <div class="bc-stat-icon"><i class="bi bi-x-circle"></i></div>
                            <div class="bc-stat-label">Cancelled</div>
                            <p class="bc-stat-value">{{ $CancelAppointments }}</p>
                            <span class="bc-stat-trend"><i class="bi bi-slash-circle"></i> Cancelled</span>
                        </div>
                    </div>
                @endif

                @if ($user && $user->role == 'Staff')
                    <div class="col-xl-3 col-sm-6">
                        <div class="bc-stat-card bc-stat-primary">
                            <div class="bc-stat-icon"><i class="bi bi-shield-check"></i></div>
                            <div class="bc-stat-label">Confirmed</div>
                            <p class="bc-stat-value">{{ $ConfirmAppointments }}</p>
                            <span class="bc-stat-trend"><i class="bi bi-patch-check"></i> Confirmed</span>
                        </div>
                    </div>
                @endif
                @if ($user && $user->role == 'Staff')
                    <div class="col-xl-3 col-sm-6">
                        <div class="bc-stat-card bc-stat-info">
                            <div class="bc-stat-icon"><i class="bi bi-calendar-event"></i></div>
                            <div class="bc-stat-label">Scheduled</div>
                            <p class="bc-stat-value">{{ $RescheduleAppointment }}</p>
                            <span class="bc-stat-trend"><i class="bi bi-arrow-repeat"></i> Scheduled</span>
                        </div>
                    </div>
                @endif
                
                @if ($user && $user->role == 'Staff')
                    <div class="col-xl-3 col-sm-6">
                        <div class="bc-stat-card bc-stat-purple">
                            <div class="bc-stat-icon"><i class="bi bi-percent"></i></div>
                            <div class="bc-stat-label">Commission</div>
                            <p class="bc-stat-value">{{ $shopCurrencySymbol ?? '₹' }}{{ number_format($serviceProductCommision, 2) }}</p>
                            <span class="bc-stat-trend"><i class="bi bi-pie-chart"></i> Earn</span>
                        </div>
                    </div>
                @endif


                   {{-- Staff day calendar --}}
                   <div class="col-lg-12" id="dashboard-staff-calendar">
                    <div class="card h-100">
                        <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div class="card-title mb-0">
                                <h5 class="m-0 me-2"><i class="bi bi-calendar3 me-2 text-primary"></i>Staff Calendar</h5>
                            </div>
                            <a href="{{ route('admin.calender.index') }}" class="btn btn-sm btn-outline-primary">Open Full Calendar</a>
                        </div>
                        <div class="card-body">
                            <div id="dashboardStaffCalendar"></div>
                        </div>
                    </div>
                </div>


                @can('Dashboard-Today_Appointments')
                    <div class="col-xxl-12">
                        <div class="card h-100">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <div class="card-title mb-0">
                                    <h5 class="m-0 me-2"><i class="bi bi-calendar-day me-2 text-primary"></i>Today's Appointments</h5>
                                </div>
                                <a href="{{ route('admin.appointment.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                <table id="TodayAppointmentDataTable" class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Customer Name</th>
                                            <th scope="col">Customer Phone No.</th>
                                            <th scope="col">Staff & Services</th>
                                            <th scope="col">Date </th>
                                            <th scope="col">Time</th>
                                            <th scope="col">Status</th>
                                            <th scope="col">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                                </div>
                            </div>
                        </div>
                    </div>
                @endcan

                @can('Dashboard-Upcoming_Appointments')
                    <div class="col-xxl-12 col-lg-7">
                        <div class="card h-100">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <div class="card-title mb-0">
                                    <h5 class="m-0 me-2"><i class="bi bi-calendar2-plus me-2 text-primary"></i>Upcoming Appointments</h5>
                                </div>
                                <a href="{{ route('admin.appointment.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                            </div>
                            <div class="card-body">
                                 <div class="table-responsive">
                                <table id="UpcomingAppointmentDataTable" class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Customer Name</th>
                                            <th scope="col">Customer Phone No.</th>
                                            <th scope="col">Staff & Services</th>
                                            <th scope="col">Date </th>
                                            <th scope="col">Time</th>
                                            <th scope="col">Status</th>
                                            <th scope="col">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                                 </div>
                            </div>
                        </div>
                    </div>
                @endcan

             
                @can('Dashboard-Top_Services')
                    <div class="col-xxl-6 col-lg-5">
                        <div class="card h-100">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <div class="card-title mb-0">
                                    <h5 class="m-0 me-2"><i class="bi bi-scissors me-2 text-primary"></i>Top Services</h5>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                <table id="TopServicesDataTable" class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Service</th>
                                            <th scope="col">Bookings</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                      
                                    </tbody>
                                </table>
                                 </div>
                            </div>
                        </div>
                    </div>
                @endcan

                @can('Dashboard-Top_Selling_Product')
                    <div class="col-xxl-6 col-lg-5">
                        <div class="card h-100">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <div class="card-title mb-0">
                                    <h5 class="m-0 me-2"><i class="bi bi-box-seam me-2 text-primary"></i>Top Selling Product</h5>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="TopStaffDataTable" class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th scope="col">#</th>
                                                <th scope="col">Product Name</th>
                                                <th scope="col">Total Sold</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $i = 1;
                                            @endphp
                                            @foreach ($products as $row)
                                                <tr>
                                                    <td>{{ $i++ }}</td>
                                                    <td>{{ $row->product_name }}</td>
                                                    <td>{{ $row->total_sold }}</td>
                                                   
                                                </tr>
                                            @endforeach

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                @endcan


            </div>
        </div>
        <!-- / Content -->

        {{-- Add Appointment (opened from Staff Calendar slot click) --}}
        <div class="modal fade" id="dashAddAppointmentModal" tabindex="-1" aria-labelledby="dashAddAppointmentLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header text-white">
                        <h5 class="modal-title" id="dashAddAppointmentLabel">Add Appointment</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="dashAppointmentForm">
                            @csrf
                            <div class="row g-3">
                                <input type="hidden" name="appointment_id" id="dash_appointment_id">
                                <div class="col-md-6">
                                    <label for="dash_name" class="form-label">Full Name</label>
                                    <input type="text" class="form-control" id="dash_name" name="name" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="dash_phone" class="form-label">Phone</label>
                                    <input type="tel" inputmode="tel" class="form-control" id="dash_phone" name="phone" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="dash_email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="dash_email" name="email">
                                </div>
                                <div class="col-md-6">
                                    <label for="dashServicePicker" class="form-label">Service</label>
                                    <div class="bc-svc-picker" id="dashServicePicker">
                                        <button type="button" class="bc-svc-picker-toggle">
                                            <span class="bc-svc-picker-label">Select Service</span>
                                            <span class="bc-svc-picker-caret"></span>
                                        </button>
                                        <div class="bc-svc-picker-menu">
                                            <div class="bc-svc-picker-head">
                                                <span class="bc-svc-picker-count">0 selected</span>
                                                <span>
                                                    <a href="#" data-action="all">Select all</a>
                                                    <a href="#" data-action="clear">Clear</a>
                                                </span>
                                            </div>
                                            <div class="bc-svc-picker-list">
                                                @foreach(($services ?? []) as $service)
                                                    <div class="bc-svc-item"
                                                         data-id="{{ $service->id }}"
                                                         data-duration="{{ $service->duration }}"
                                                         data-name="{{ $service->service_name }}">
                                                        <label class="bc-svc-item-label">
                                                            <input type="checkbox" class="bc-svc-check">
                                                            <span class="bc-svc-name">{{ $service->service_name }} <small>({{ $service->duration }} min)</small></span>
                                                            <span class="bc-svc-qty">
                                                                <button type="button" class="btn btn-outline-secondary bc-svc-minus">&minus;</button>
                                                                <span class="bc-svc-qty-val">1</span>
                                                                <button type="button" class="btn btn-outline-secondary bc-svc-plus">+</button>
                                                            </span>
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="dash_staff_id" class="form-label">Staff</label>
                                    <select class="form-select" id="dash_staff_id" name="staff_id" required>
                                        <option value="">Select Staff</option>
                                        @foreach(($StaffList ?? []) as $staff)
                                            <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="dash_appointment_date" class="form-label">Date</label>
                                    <input type="datetime-local" class="form-control" id="dash_appointment_date" min="{{ date('Y-m-d') }}T00:00" name="appointment_date" required>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="bi bi-x-lg"></i> Cancel</button>
                        <a href="#" id="dashMakePaymentBtn" class="btn btn-success d-none">
                            <i class="bi bi-credit-card me-1"></i> Make Payment
                        </a>
                        <button type="submit" form="dashAppointmentForm" class="btn btn-primary dash-sav-appointment"><i class="bi bi-check-lg"></i> Save Appointment</button>
                    </div>
                </div>
            </div>
        </div>
    @endsection

    @push('script')
        <script src="{{ asset('assets/js/admin-staff-calendar.js') }}?v=23"></script>
        <script src="{{ asset('assets/js/admin-service-picker.js') }}?v=1"></script>
        <script>
            @php
                $defaultStaffAvatar = asset('assets/img/user.png');
                $dashStaffResources = collect($calendarStaffList ?? $StaffList ?? [])->map(function ($s) use ($defaultStaffAvatar) {
                    $avatar = $defaultStaffAvatar;
                    if (!empty($s->profile_img)) {
                        $path = ltrim(str_replace('\\', '/', $s->profile_img), '/');
                        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
                            $avatar = asset('storage/' . $path);
                        } elseif (file_exists(public_path('storage/' . $path))) {
                            $avatar = asset('storage/' . $path);
                        }
                    }
                    return [
                        'id' => (string) $s->id,
                        'title' => trim((string) ($s->name ?? '')) ?: 'Staff',
                        'name' => trim((string) ($s->name ?? '')) ?: 'Staff',
                        'avatar' => $avatar,
                    ];
                })->values();
            @endphp
            const BC_APP_TIMEZONE = @json(config('app.timezone', 'America/Edmonton'));
            const BC_STAFF_DEFAULT_AVATAR = @json($defaultStaffAvatar);
            const BC_DASH_STAFF_RESOURCES = @json($dashStaffResources);
            const BC_DASH_IS_STAFF = @json(\Illuminate\Support\Facades\Auth::user()->role === 'Staff');
            const BC_DASH_STAFF_ID = @json(\Illuminate\Support\Facades\Auth::user()->role === 'Staff' ? (string) \Illuminate\Support\Facades\Auth::id() : '');
            var pendingDashStaffId = '';

            function bcFormatDateTimeLocal(date) {
                function pad(n) { return n < 10 ? '0' + n : String(n); }
                return date.getFullYear() + '-' + pad(date.getMonth() + 1) + '-' + pad(date.getDate())
                    + 'T' + pad(date.getHours()) + ':' + pad(date.getMinutes());
            }

            function openDashAddAppointment(slot) {
                var form = document.getElementById('dashAppointmentForm');
                if (form) form.reset();
                $('#dash_appointment_id').val('');
                var dashPicker = $('#dashServicePicker').data('picker');
                if (dashPicker) dashPicker.clear(true);

                var staffSelect = $('#dash_staff_id');
                staffSelect.empty().append('<option value="">Select Staff</option>');
                (BC_DASH_STAFF_RESOURCES || []).forEach(function (s) {
                    staffSelect.append('<option value="' + s.id + '">' + s.name + '</option>');
                });

                if (slot && slot.date) {
                    $('#dash_appointment_date').val(bcFormatDateTimeLocal(slot.date));
                }
                if (slot && slot.staffId) {
                    staffSelect.val(String(slot.staffId));
                    pendingDashStaffId = String(slot.staffId);
                } else {
                    pendingDashStaffId = '';
                }

                $('#dashMakePaymentBtn').addClass('d-none').attr('href', '#');
                $('#dashAddAppointmentModal').modal('show');
            }

            function populateDashStaffOptions(selectedStaffId, serviceIds) {
                var staffSelect = $('#dash_staff_id');
                var preferred = selectedStaffId ? String(selectedStaffId) : '';

                function fillOptions(staffRows) {
                    staffSelect.empty().append('<option value="">Select Staff</option>');
                    (staffRows || []).forEach(function (staff) {
                        var id = String(staff.id);
                        staffSelect.append('<option value="' + id + '">' + staff.name + '</option>');
                    });

                    if (preferred && staffSelect.find('option[value="' + preferred + '"]').length === 0) {
                        var matched = (BC_DASH_STAFF_RESOURCES || []).find(function (staff) {
                            return String(staff.id) === preferred;
                        });
                        if (matched) {
                            staffSelect.append('<option value="' + matched.id + '">' + matched.name + '</option>');
                        }
                    }

                    if (preferred) {
                        staffSelect.val(preferred);
                    }
                }

                if (serviceIds && serviceIds.length) {
                    $.ajax({
                        url: "{{ route('admin.staffby.services') }}",
                        type: "GET",
                        data: { service_ids: serviceIds },
                        success: function (response) {
                            fillOptions(response.data || []);
                        },
                        error: function () {
                            fillOptions(BC_DASH_STAFF_RESOURCES || []);
                        }
                    });
                } else {
                    fillOptions(BC_DASH_STAFF_RESOURCES || []);
                }
            }

            function openDashEditAppointment(eventInfo) {
                if (!eventInfo || !eventInfo.extendedProps || !eventInfo.extendedProps.appointment_id) {
                    return;
                }

                var form = document.getElementById('dashAppointmentForm');
                if (form) form.reset();

                $('#dash_appointment_id').val(eventInfo.extendedProps.appointment_id);
                $('#dash_name').val(eventInfo.extendedProps.appointment_name || '');
                $('#dash_phone').val(eventInfo.extendedProps.phone || '');
                $('#dash_email').val(eventInfo.extendedProps.email || '');
                $('#dash_appointment_date').val(eventInfo.extendedProps.start || '');

                var serviceIds = eventInfo.extendedProps.service_id || [];
                if (typeof serviceIds === 'string') {
                    serviceIds = serviceIds.split(',');
                }
                var dashRaw = serviceIds.map(function (v) { return String(v).trim(); }).filter(Boolean);
                serviceIds = [...new Set(dashRaw)];

                var dashPicker = $('#dashServicePicker').data('picker');
                if (dashRaw.length && dashPicker) {
                    var countMap = {};
                    dashRaw.forEach(function (id) { countMap[id] = (countMap[id] || 0) + 1; });
                    dashPicker.setItems(countMap, true);
                } else if (dashPicker) {
                    dashPicker.clear(true);
                }

                pendingDashStaffId = String(eventInfo.extendedProps.staffId || '');
                populateDashStaffOptions(pendingDashStaffId, serviceIds);

                var appointmentId = eventInfo.extendedProps.appointment_id;
                $('#dashMakePaymentBtn')
                    .attr('href', "{{ route('admin.invoice.edit', ['shop' => \App\Support\CurrentShop::slug(), 'id' => '__ID__']) }}".replace('__ID__', appointmentId))
                    .removeClass('d-none');

                $('#dashAddAppointmentModal').modal('show');
            }

            $(document).ready(function() {
                $('#dashServicePicker').bcServicePicker({
                    onChange: function (serviceIds, state) {
                        var preferred = pendingDashStaffId || $('#dash_staff_id').val();
                        if (serviceIds.length > 0) {
                            $.ajax({
                                url: "{{ route('admin.staffby.services') }}",
                                type: "GET",
                                data: { service_ids: serviceIds },
                                success: function (response) {
                                    $('#dash_staff_id').empty().append('<option value="">Select Staff</option>');
                                    $.each(response.data || [], function (key, staff) {
                                        $('#dash_staff_id').append('<option value="' + staff.id + '">' + staff.name + '</option>');
                                    });
                                    if (preferred && $('#dash_staff_id option[value="' + String(preferred) + '"]').length === 0) {
                                        var matched = (BC_DASH_STAFF_RESOURCES || []).find(function (s) {
                                            return String(s.id) === String(preferred);
                                        });
                                        if (matched) {
                                            $('#dash_staff_id').append('<option value="' + matched.id + '">' + matched.name + '</option>');
                                        }
                                    }
                                    if (preferred) {
                                        $('#dash_staff_id').val(String(preferred));
                                    }
                                }
                            });
                        } else {
                            $('#dash_staff_id').empty().append('<option value="">Select Staff</option>');
                            (BC_DASH_STAFF_RESOURCES || []).forEach(function (s) {
                                $('#dash_staff_id').append('<option value="' + s.id + '">' + s.name + '</option>');
                            });
                            if (preferred) {
                                $('#dash_staff_id').val(String(preferred));
                            }
                        }
                    }
                });
                var datatable = $('#TodayAppointmentDataTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('admin.get-today-appointment.gettabledata') }}",
                        type: "POST",
                        data: function(d) {
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
                            data: 'customer_name',
                            name: 'customer_name'
                        },
                        {
                            data: 'customer_phone',
                            name: 'customer_phone'
                        },
                        {
                            data: 'staff_service_lines',
                            name: 'staff_service_lines',
                            defaultContent: '-'
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
                            data: 'status_badge',
                            name: 'status_badge',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        }
                    ]
                });


                var datatable = $('#UpcomingAppointmentDataTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('admin.get-upcoming-appointment.gettabledata') }}",
                        type: "POST",
                        data: function(d) {
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
                            data: 'customer_name',
                            name: 'customer_name'
                        },
                        {
                            data: 'customer_phone',
                            name: 'customer_phone'
                        },
                        {
                            data: 'staff_service_lines',
                            name: 'staff_service_lines',
                            defaultContent: '-'
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
                            data: 'status_badge',
                            name: 'status_badge',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        }
                    ]
                });
                
                $('#TopStaffDataTable').DataTable();

                var TopServicesDataTable = $('#TopServicesDataTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('admin.top-services.gettabledata') }}",
                        type: "POST",
                        data: function(d) {
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
                            data: 'appointmentCount',
                            name: 'appointmentCount'
                        },
                    ]
                });

                // Staff calendar on dashboard
                function fetchDashEvents(info, successCallback, failureCallback) {
                    $.ajax({
                        url: "{{ route('admin.appointments.fetch') }}",
                        type: "GET",
                        data: {
                            start: info.startStr,
                            end: info.endStr,
                            staff_id: BC_DASH_IS_STAFF ? BC_DASH_STAFF_ID : ''
                        },
                        success: function (response) {
                            successCallback(response || []);
                        },
                        error: function (xhr) {
                            console.error('Dashboard calendar events error', xhr);
                            failureCallback(xhr);
                        }
                    });
                }

                function fetchDashAvailability(params, successCallback, failureCallback) {
                    $.ajax({
                        url: "{{ route('admin.staff.availability') }}",
                        type: "GET",
                        data: {
                            date: params.date,
                            staff_id: BC_DASH_IS_STAFF ? BC_DASH_STAFF_ID : ''
                        },
                        success: function (response) {
                            successCallback(response);
                        },
                        error: function () {
                            failureCallback();
                        }
                    });
                }

                var dashCalEl = document.getElementById('dashboardStaffCalendar');
                if (dashCalEl && typeof StaffDayCalendar !== 'undefined') {
                    window.bcDashboardStaffCalendar = new StaffDayCalendar(dashCalEl, {
                        staff: BC_DASH_STAFF_RESOURCES,
                        defaultAvatar: BC_STAFF_DEFAULT_AVATAR,
                        timezone: BC_APP_TIMEZONE,
                        slotMinHour: 8,
                        slotMaxHour: 20,
                        hourHeight: 68,
                        getStaffFilter: function () {
                            return BC_DASH_IS_STAFF ? BC_DASH_STAFF_ID : '';
                        },
                        fetchEvents: fetchDashEvents,
                        fetchAvailability: fetchDashAvailability,
                        onUnavailableClick: function (info) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Staff Not Available',
                                text: 'This staff member is not available at the selected time.',
                                confirmButtonText: 'Ok',
                                confirmButtonColor: '#0f5132'
                            });
                        },
                        onEventClick: function (info) {
                            openDashEditAppointment(info.event);
                        },
                        onSlotClick: function (info) {
                            openDashAddAppointment(info);
                        },
                        onBreakClick: function () {
                            window.location.href = "{{ route('admin.calender.index') }}";
                        }
                    });

                    var resizeTimer = null;
                    window.addEventListener('resize', function () {
                        clearTimeout(resizeTimer);
                        resizeTimer = setTimeout(function () {
                            if (window.bcDashboardStaffCalendar) {
                                window.bcDashboardStaffCalendar.render();
                            }
                        }, 150);
                    });
                }

                $('#dashboardScrollToCalendarBtn').on('click', function () {
                    var target = document.getElementById('dashboard-staff-calendar');
                    if (!target) return;
                    var top = target.getBoundingClientRect().top + window.pageYOffset - 80;
                    window.scrollTo({ top: top, behavior: 'smooth' });
                });

                $('#dashAppointmentForm').on('submit', function (e) {
                    e.preventDefault();
                    var totalDuration = 0;
                    var dashPickerState = (($('#dashServicePicker').data('picker')) || { getState: function () { return {}; } }).getState() || {};
                    Object.keys(dashPickerState).forEach(function (sid) {
                        var $opt = $('#dashServicePicker').find('.bc-svc-item[data-id="' + sid + '"]');
                        var dur = parseInt($opt.data('duration'), 10) || 0;
                        totalDuration += dur * (dashPickerState[sid].qty || 1);
                    });
                    if ($('#dash_total_duration').length === 0) {
                        $('<input>').attr({
                            type: 'hidden',
                            id: 'dash_total_duration',
                            name: 'total_duration',
                            value: totalDuration
                        }).appendTo('#dashAppointmentForm');
                    } else {
                        $('#dash_total_duration').val(totalDuration);
                    }

                    var oldhtml = $('.dash-sav-appointment').html();
                    $.ajax({
                        url: "{{ route('admin.calender.book.store') }}",
                        type: "POST",
                        data: $(this).serialize(),
                        beforeSend: function () {
                            $('.dash-sav-appointment').prop('disabled', true).html(
                                '<span class="spinner-border spinner-border-sm me-2"></span> Processing...'
                            );
                        },
                        success: function (response) {
                            $('#dashAddAppointmentModal').modal('hide');
                            $('.dash-sav-appointment').prop('disabled', false).html(oldhtml);
                            if (response.status == 200) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success',
                                    text: response.msg,
                                    showConfirmButton: false,
                                    timer: 2000
                                });
                                if (window.bcDashboardStaffCalendar) {
                                    window.bcDashboardStaffCalendar.refetchEvents();
                                }
                                setTimeout(function () { location.reload(); }, 1000);
                            } else {
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Notice',
                                    text: response.msg || 'Unable to save appointment'
                                });
                            }
                        },
                        error: function (xhr) {
                            $('.dash-sav-appointment').prop('disabled', false).html(oldhtml);
                            var msg = (xhr.responseJSON && (xhr.responseJSON.msg || xhr.responseJSON.message)) || 'Something went wrong';
                            Swal.fire({ icon: 'error', title: 'Error', text: msg });
                        }
                    });
                });
            });
        </script>
    @endpush
