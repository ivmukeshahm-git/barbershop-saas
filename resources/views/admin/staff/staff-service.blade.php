@extends('admin.layout.layout')
@section('content')
    <script>
        page_active_id = "StaffService";
    </script>
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="bc-page-header">
            <div>
                <h4>Assign Services to Staff</h4>
                <p>Manage which staff members provide which services</p>
            </div>
        </div>

        <div class="card rounded-3 shadow-sm">
            <div class="card-body table-responsive p-4">
                <table id="StaffServiceDataTable" class="table table-hover">
                    <thead class="border-top">
                        <tr>
                            <th>Sr. NO.</th>
                            <th>Staff Name</th>
                            <th>Staff Email</th>
                            <th>Type</th>
                            <th>Assigned Services</th>
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

    <!-- Assign Services Modal -->
    <div class="modal fade" id="AssignServicesModal" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <form id="assign-services-form" class="modal-content" onsubmit="return false;">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bx bx-sitemap me-2"></i>Assign Services</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning d-none" id="non-barber-alert">
                        This staff member is not a barber. Only barbers can take service appointments, but you can still assign services.
                    </div>
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-12 mb-3">
                            <label class="form-label mb-2">Staff Name</label>
                            <input type="text" id="assign_staff_name" class="form-control" readonly>
                            <input type="hidden" id="assign_staff_id" name="staff_id">
                        </div>

                        <div class="col-lg-12 col-md-12 col-12 mb-3">
                            <label for="assign_service_ids" class="form-label mb-2">Services</label>
                            <select id="assign_service_ids" name="service_ids[]" class="selectpicker w-100" data-style="btn-default"
                                multiple data-actions-box="true" data-live-search="true" data-title="Select services...">
                                @foreach ($services as $row)
                                    <option value="{{ $row->id }}">{{ $row->service_name }} ({{ $row->duration }} min - {{ $shopCurrencySymbol ?? '₹' }}{{ $row->price }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="AssignSubmitBtn">Save Assignments</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            if ($.fn.selectpicker) {
                $('#assign_service_ids').selectpicker('render');
            }

            var datatable = $('#StaffServiceDataTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.staff-service.gettabledata') }}",
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
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'is_barber_label',
                        name: 'is_barber_label',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'service_names',
                        name: 'service_names'
                    },
                    {
                        data: 'staff_status',
                        name: 'staff_status',
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

            $('#StaffServiceDataTable tbody').on('click', '.assign-services-btn', function() {
                var rowData = datatable.row($(this).closest('tr')).data();

                $('#assign_staff_id').val(rowData.id);
                $('#assign_staff_name').val(rowData.name);

                $('#non-barber-alert').toggleClass('d-none', String(rowData.is_barber) === '1' || String(rowData.is_barber) === 'true');

                $('#assign_service_ids').selectpicker('deselectAll');

                $.ajax({
                    type: "POST",
                    url: "{{ route('admin.staff-service.get-services') }}",
                    data: {
                        _token: '{{ csrf_token() }}',
                        staff_id: rowData.id
                    },
                    dataType: "json",
                    success: function(response) {
                        if (response.status === 1) {
                            $('#assign_service_ids').selectpicker('val', response.service_ids);
                        } else {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Warning',
                                text: response.message,
                                showConfirmButton: false,
                                timer: 2000
                            });
                        }
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                    }
                });

                $('#AssignServicesModal').modal('show');
            });

            $("#assign-services-form").submit(function(e) {
                e.preventDefault();

                let selected = $('#assign_service_ids').val() || [];
                if (!selected.length) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Warning',
                        text: 'Please select at least one service.',
                        showConfirmButton: false,
                        timer: 2000
                    });
                    return;
                }

                if (typeof LoaderShow === "function") LoaderShow();
                $('#AssignSubmitBtn').prop('disabled', true);

                let formData = new FormData(this);

                $.ajax({
                    type: "POST",
                    url: "{{ route('admin.staff-service.save') }}",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (typeof LoaderHide === "function") LoaderHide();
                        $('#AssignSubmitBtn').prop('disabled', false);
                        $('#AssignServicesModal').modal('hide');

                        if (response.status === 1) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: response.message,
                                showConfirmButton: false,
                                timer: 2000
                            });
                            setTimeout(() => location.reload(), 1000);
                        } else {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Warning',
                                text: response.message || 'Error saving assignments.',
                                showConfirmButton: false,
                                timer: 2000
                            });
                        }
                    },
                    error: function(xhr) {
                        if (typeof LoaderHide === "function") LoaderHide();
                        $('#AssignSubmitBtn').prop('disabled', false);
                        console.error(xhr.responseText);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Error occurred while saving assignments.',
                            showConfirmButton: false,
                            timer: 2000
                        });
                    }
                });
            });
        });
    </script>
@endpush