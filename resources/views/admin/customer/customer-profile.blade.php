@extends('admin.layout.layout')
@section('content')
    <script>
        page_active_id = "Customer";
    </script>

    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="bc-page-header">
            <div>
                <h4>Customer Profile</h4>
                <p>View visit history and pending appointments</p>
            </div>
        </div>

        <input type="hidden" id="customer_id" name="customer_id" value="{{ $id }}">

        <div class="card mb-6 p-4 rounded-3 shadow-sm">
            <div class="d-flex align-items-center justify-content-between flex-wrap">
                <!-- Left: Profile Image and Info -->
                <div class="d-flex align-items-center gap-4">
                    <!-- Profile Image -->
                    <img src="{{ asset('assets/img/user.png') }}" alt="Customer Avatar" class="rounded-circle"
                        style="width:120px; height:120px; object-fit:cover;">

                    <!-- Customer Info -->
                    <div>
                        <h4 class="mb-1">{{ $CustomerData->customer_name }}</h4>
                        <p class="mb-1"><strong>Email:</strong>{{ $CustomerData->customer_email }}</p>
                        <p class="mb-1"><strong>Phone:</strong>{{ $CustomerData->customer_phone }}</p>
                        <p><strong>Member since:</strong>
                            {{ \Carbon\Carbon::parse($CustomerData->created_at)->format('F j, Y') }}</p>
                    </div>
                </div>

                <!-- Right: Stats -->
              <div class="d-flex gap-4 mt-3 mt-md-0">
                    <div class="p-5 border rounded-3 text-center bg-light">
                        <h6 class="mb-1">Total Visits</h6>
                        <strong class="fs-4">{{ $totalVisit }}</strong>
                    </div>
                    <div class="p-5 border rounded-3 text-center bg-light">
                        <h6 class="mb-1">Total Spent</h6>
                        <strong class="fs-4">{{ $shopCurrencySymbol ?? '₹' }} {{ $totalSpendAmount }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-5 rounded-3 shadow-sm">
            <div class="card-header">
                <h5 class="mb-0">Pending Appointment</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive text-nowrap">
                    <table id="PendingAppointmentDataTable" class="table table-sm table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Sr. No</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Staff Name</th>
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

    
    </div>
@endsection




@push('script')
    <script>
        $(document).ready(function() {
            var datatable = $('#PendingAppointmentDataTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.customer-pending-appointment.gettabledata') }}",
                    type: "POST",
                    data: function(d) {
                        d._token = '{{ csrf_token() }}';
                        d.customer_id = $('#customer_id').val();
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
    </script>
@endpush
