@push('css')
    <style>
        .job-type-select {
            border: 1px solid #e0e0e0;
            border-radius: 13px;
            box-shadow: 12px 12px 5px #eee;
            cursor: pointer;
        }

        .select-job {
            border: 1px solid #242a62;
        }

        .appt-stat-card {
            cursor: pointer;
        }

        .appt-stat-note {
            margin: -8px 0 20px;
            padding: 10px 14px;
            border-radius: 12px;
            background: #f3f6f3;
            border: 1px solid #e4e4e7;
            color: #52525b;
            font-size: 0.92rem;
        }

        .appt-stat-note i {
            color: #136207;
        }
    </style>
@endpush


@extends('admin.layout.layout')
@section('content')
    <script>
        page_active_id = "Appointment";
    </script>
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="bc-page-header">
            <div>
                <h4>Appointments</h4>
                <p>View and manage all customer appointments</p>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-xl col-md-4 col-sm-6">
                <div class="bc-stat-card bc-stat-purple appt-stat-card is-active" data-status="">
                    <div class="bc-stat-icon"><i class="bi bi-collection"></i></div>
                    <div class="bc-stat-label">All</div>
                    <p class="bc-stat-value">{{ $AllAppointments }}</p>
                    <span class="bc-stat-trend"><i class="bi bi-list-ul"></i> Total</span>
                </div>
            </div>
            <div class="col-xl col-md-4 col-sm-6">
                <div class="bc-stat-card bc-stat-primary appt-stat-card" data-status="3">
                    <div class="bc-stat-icon"><i class="bi bi-shield-check"></i></div>
                    <div class="bc-stat-label">Confirm</div>
                    <p class="bc-stat-value">{{ $ConfirmAppointments }}</p>
                    <span class="bc-stat-trend"><i class="bi bi-patch-check"></i> Confirmed</span>
                </div>
            </div>
            <div class="col-xl col-md-4 col-sm-6">
                <div class="bc-stat-card bc-stat-success appt-stat-card" data-status="1">
                    <div class="bc-stat-icon"><i class="bi bi-check2-circle"></i></div>
                    <div class="bc-stat-label">Complete</div>
                    <p class="bc-stat-value">{{ $CompleteAppointments }}</p>
                    <span class="bc-stat-trend"><i class="bi bi-check-lg"></i> Done</span>
                </div>
            </div>
            <div class="col-xl col-md-4 col-sm-6">
                <div class="bc-stat-card bc-stat-danger appt-stat-card" data-status="2">
                    <div class="bc-stat-icon"><i class="bi bi-x-circle"></i></div>
                    <div class="bc-stat-label">Cancel</div>
                    <p class="bc-stat-value">{{ $CancelAppointments }}</p>
                    <span class="bc-stat-trend"><i class="bi bi-slash-circle"></i> Cancelled</span>
                </div>
            </div>
            <div class="col-xl col-md-4 col-sm-6">
                <div class="bc-stat-card bc-stat-info appt-stat-card" data-status="4">
                    <div class="bc-stat-icon"><i class="bi bi-arrow-repeat"></i></div>
                    <div class="bc-stat-label">Reschedule</div>
                    <p class="bc-stat-value">{{ $RescheduleAppointments }}</p>
                    <span class="bc-stat-trend"><i class="bi bi-calendar-event"></i> Scheduled</span>
                </div>
            </div>
        </div>

        <p class="appt-stat-note">
            <i class="bi bi-info-circle me-1"></i>
            Click any box above to filter the appointments table. Use <strong>All</strong> to see every appointment.
        </p>

        <div class="card rounded-3 shadow-sm p-4 mb-4">
            <h5 class="mb-4 fw-semibold">Appointment Filter</h5>
            <form id="appointmentFilterForm">
                <div class="row g-3 align-items-end">
                    <!-- Start Date -->
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

                    <!-- Day Type -->
                    <div class="col-md-2">
                        <label for="day_type" class="form-label">Day Type</label>
                        <select class="form-select select2" id="day_type" name="day_type">
                            <option value="">All Times</option>
                            <option value="Morning">Morning</option>
                            <option value="Afternoon">Afternoon</option>
                            <option value="Evening">Evening</option>
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



        <!-- Product List Table -->
        <div class="card rounded-3 shadow-sm">

            <div class="card-body table-responsive p-4">
                <table id="AppointmentDataTable" class="table table-hover">
                    <thead class="border-top">
                        <tr>
                            <th>Sr. NO.</th>
                            <th>Name</th>
                            <th>Contact No.</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Staff / Services</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- / Content -->



    <!-- Modal -->
    <div class="modal fade" id="appointmentDetailsModal" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog">
            <form id="job-category-form" class="modal-content" onsubmit="return false;">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="JobCategoryTitle"><i class="bi bi-calendar-event me-2"></i>Appointment Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="d-flex align-items-center mb-3">
                        <span class="fw-bold fs-5">—</span>
                        <span class="badge bg-danger ms-3 px-3 py-1" style="font-size:0.92rem;">Cancelled</span>
                    </div>

                    <!-- Date -->
                    <div class="d-flex align-items-start mb-3">
                        <i class='bx bx-calendar me-3 fs-4 mt-1 text-muted'></i>
                        <div>
                            <div class="fw-semibold">Date</div>
                            <div class="text-muted small">Saturday, September 6, 2025</div>
                        </div>
                    </div>

                    <!-- Time -->
                    <div class="d-flex align-items-start mb-3">
                        <i class='bx bx-time me-3 fs-4 mt-1 text-muted'></i>
                        <div>
                            <div class="fw-semibold">Time</div>
                            <div class="text-muted small">11:30:00 - 12:00:00</div>
                        </div>
                    </div>

                    <!-- Staff -->
                    <div class="d-flex align-items-start mb-3">
                        <i class='bx bx-user me-3 fs-4 mt-1 text-muted'></i>
                        <div>
                            <div class="fw-semibold">Staff</div>
                            <div class="text-muted small">—</div>
                        </div>
                    </div>

                    <!-- Divider -->
                    <hr>

                    <!-- Services -->
                    <div class="d-flex align-items-start mb-3">
                        <i class='bx bx-scissors me-3 fs-4 mt-1 text-muted'></i>
                        <div>
                            <div class="fw-semibold">Services</div>
                            <div class="small">
                                <span>—</span>
                                <span class="text-muted ms-2">—</span>
                            </div>
                        </div>
                    </div>

                    <!-- Total Row -->
                    <div class="d-flex justify-content-between align-items-center mt-3 px-1">
                        <span class="fw-semibold">Total</span>
                        <span class="fw-bold fs-5">—</span>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">
                        Close
                    </button>

                    {{--
                    <input type="hidden" id="_method" name="_method" value="POST">
                    <input type="hidden" id="flag" name="flag" value="A">
                    <input type="hidden" id="row_id" name="row_id">
                    <button type="submit" class="btn btn-primary">Save</button> --}}
                </div>
            </form>
        </div>
    </div>
@endsection


@push('script')
    <script>
        $(document).ready(function() {
            var datatable = $('#AppointmentDataTable').DataTable({
                processing: true,
                serverSide: true,
                order: [[3, 'desc']],
                ajax: {
                    url: "{{ route('admin.appointment.gettabledata') }}",
                    type: "POST",
                    data: function(d) {
                        d._token = '{{ csrf_token() }}';
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                        d.status = $('#status').val();
                        d.staff_id = $('#staff_id').val();
                        d.day_type = $('#day_type').val();
                        console.log(d);
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
                        name: 'a.date'
                    },
                    {
                        data: 'slot_time',
                        name: 'slot_time',
                    },
                    {
                        data: 'staff_service_lines',
                        name: 'staff_service_lines',
                        defaultContent: '-'
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

            // 🔎 Filter Button Click
            $('#filterBtn').on('click', function() {
                var status = String($('#status').val() || '');
                $('.appt-stat-card').removeClass('is-active');
                $('.appt-stat-card[data-status="' + status + '"]').addClass('is-active');
                datatable.ajax.reload();
            });

            $(document).on('click', '.appt-stat-card', function() {
                var status = String($(this).attr('data-status') || '');
                $('.appt-stat-card').removeClass('is-active');
                $(this).addClass('is-active');
                $('#status').val(status).trigger('change');
                datatable.ajax.reload();
            });
        });
    </script>
@endpush
