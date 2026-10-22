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
    </style>
@endpush


@extends('admin.layout.layout')
@section('content')
    <script>
        page_active_id = "ShopClosures";
    </script>
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="bc-page-header">
            <div>
                <h4>Shop Closures</h4>
                <p>Schedule full-day or partial shop closures</p>
            </div>
        </div>

        <!-- Product List Table -->
        <div class="card rounded-3 shadow-sm">
            <div class="card-header p-4">
                <div class="row">
                    <div class="col-lg-6"></div>
                    @can('Shop_Closures-Add')
                        <div class="col-lg-6" style="text-align: right;">
                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                data-bs-target="#ShopClosuresModal"><i class="bi bi-plus-lg"></i> Add</button>
                        </div>
                    @endcan

                </div>
            </div>
            <hr class="my-1">
            <div class="card-body table-responsive p-4">
                <table id="ShopClosuresDataTable" class="table table-hover">
                    <thead class="border-top">
                        <tr>
                            <th>Sr. NO.</th>
                            <th>Close Type</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Reason</th>
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
    <div class="modal fade" id="ShopClosuresModal" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog">
            <form id="shop-closures-form" class="modal-content" onsubmit="return false;">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="ShopClosuresModalTitle"><i class="bi bi-shop-window me-2"></i>Add Closures</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-12 mb-3">
                            <label for="close_date" class="form-label mb-2">Date <span style="color: red;">*</span> </label>
                            <input type="date" id="close_date" name="close_date" class="form-control" />
                        </div>


                        <div class="mb-3 d-flex justify-content-between align-items-center">
                            <label class="form-label mb-0">Full Day Closure</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="full_day_closure"
                                    name="full_day_closure" onchange="toggleTimeInputs()" />
                            </div>
                        </div>


                        <div id="timeInputs" class="mb-3">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="start_time" class="form-label">Start Time <span
                                            style="color: red;">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-clock"></i></span>
                                        <input type="time" class="form-control" id="start_time" name="start_time"
                                            required />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="end_time" class="form-label">End Time <span
                                            style="color: red;">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-clock"></i></span>
                                        <input type="time" class="form-control" id="end_time" name="end_time"
                                            required />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-12 col-md-12 col-12 mb-3">
                            <label for="nameBackdrop" class="form-label mb-2">Reason</label>
                            <textarea name="close_reason" id="close_reason" class="form-control" cols="30" rows="6"
                                placeholder="Enter Reason"></textarea>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">
                        Close
                    </button>


                    <input type="hidden" id="_method" name="_method" value="POST">
                    <input type="hidden" id="flag" name="flag" value="A">
                    <input type="hidden" id="row_id" name="row_id">
                    <button type="submit" class="btn btn-primary" id="UpdateButton">Save</button>
                </div>
            </form>
        </div>
    </div>
@endsection


@push('script')
    <script>
        $(document).ready(function() {
            var datatable = $('#ShopClosuresDataTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.shop-closures.gettabledata') }}",
                    type: "POST",
                    data: function(d) {
                        d._token = '{{ csrf_token() }}'; // Required for POST in Laravel
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'closure_type',
                        name: 'closure_type'
                    },
                    {
                        data: 'closure_day',
                        name: 'closure_day'
                    },
                    {
                        data: 'closure_time_status',
                        name: 'closure_time_status'
                    },
                    {
                        data: 'reason',
                        name: 'reason'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }

                ]
            });


            $('#ShopClosuresDataTable tbody').on('click', '.view-details-btn',
                function() { // Delegated event handler for the button
                    var $row = $(this).closest('tr'); // Get the closest row element
                    var rowData = datatable.row($row).data(); // Get the entire row data object

                    $("#ShopClosuresModal").modal('show');

                    var formattedDate = String(rowData.date || '').slice(0, 10);
                    $("#close_date").val(formattedDate);
                    $("#full_day_closure").prop('checked', rowData.closure_status == 1);

                    // Conditionally show/hide time inputs if toggleTimeInputs() controls that
                    toggleTimeInputs(); // call this to reflect checkbox state on time inputs visibility

                    // Set time inputs only if not full day closure
                    if (rowData.closure_status != 1) {
                        $("#start_time").val(rowData.start_time); // expects HH:mm format
                        $("#end_time").val(rowData.end_time);
                    } else {
                        $("#start_time").val('');
                        $("#end_time").val('');
                    }

                    $("#close_reason").html(rowData.reason);


                    $("#flag").val("U");
                    $("#row_id").val(rowData.id);
                    $("#_method").val("PUT");

                    $("#UpdateButton").html('Update');
                });

            $('#ShopClosuresDataTable tbody').on('click', '.delete-shop-closures-btn',
                function() { // Delegated event handler for the button
                    var $row = $(this).closest('tr'); // Get the closest row element
                    var rowData = datatable.row($row).data(); // Get the entire row data object


                    Swal.fire({
                        title: 'Are you sure?',
                        text: "Are you sure to delete Shop Closures?",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, confirm!',
                        customClass: {
                            confirmButton: 'btn btn-primary me-3',
                            cancelButton: 'btn btn-label-secondary'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {

                            var id = $(this).data('id') || rowData.id;
                            var url = "{{ route('admin.shop-closures.destroy', ['shop' => request()->route('shop'), 'shop_closure' => '__CLOSURE_ID__']) }}"
                                .replace('__CLOSURE_ID__', encodeURIComponent(id));

                            $.ajaxSetup({
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                }
                            });

                            LoaderShow()

                            $.ajax({
                                type: "DELETE",
                                url: url,
                                dataType: "json",
                                success: function(response) {
                                    LoaderHide();
                                    if (response.status === 1) {
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Success',
                                            text: response.message,
                                            showConfirmButton: false,
                                            timer: 2000
                                        });

                                        datatable.ajax.reload(null, false);
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
                                    LoaderHide();
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Unable to delete closure',
                                        text: (xhr.responseJSON && xhr.responseJSON.message) ?
                                            xhr.responseJSON.message :
                                            'The shop closure could not be deleted. Please try again.'
                                    });
                                }
                            });
                            return false;
                        }
                    });
                });




            $('#ShopClosuresModal').on('hidden.bs.modal', function() {
                $("#_method").val("POST");
            });

        });


        function toggleTimeInputs() {
            const checkbox = document.getElementById('full_day_closure');
            const timeInputs = document.getElementById('timeInputs');
            if (checkbox.checked) {
                timeInputs.style.display = 'none'; // Hide inputs when Full Day Closure is checked
            } else {
                timeInputs.style.display = 'block'; // Show inputs when unchecked
            }
        }

        // Initialize initial state on page load
        document.addEventListener('DOMContentLoaded', function() {
            toggleTimeInputs();
        });

        $(document).ready(function() {
            // jQuery Validation setup
            let validator = $("#shop-closures-form").validate({
                rules: {
                    close_date: {
                        required: true,
                        date: true
                    },
                    // Closure status is handled by checkbox - not validated here
                    start_time: {
                        required: {
                            depends: function() {
                                return !$('#full_day_closure').is(':checked');
                            }
                        }
                    },
                    end_time: {
                        required: {
                            depends: function() {
                                return !$('#full_day_closure').is(':checked');
                            }
                        }
                    },
                    close_reason: {
                        required: true
                    }
                },
                messages: {
                    close_date: {
                        required: "Please select the closure date",
                        date: "Please enter a valid date"
                    },
                    start_time: {
                        required: "Start time is required if not a full day closure"
                    },
                    end_time: {
                        required: "End time is required if not a full day closure"
                    },
                    close_reason: {
                        required: "Please enter the reason for closure"
                    }
                },
                errorElement: "div",
                errorPlacement: function(error, element) {
                    error.addClass("invalid-feedback");
                    error.insertAfter(element);
                },
                highlight: function(element) {
                    $(element).addClass("is-invalid").removeClass("is-valid");
                },
                unhighlight: function(element) {
                    $(element).removeClass("is-invalid");
                }
            });

            // AJAX form submission with validation
            $("#shop-closures-form").submit(function(e) {
                e.preventDefault();

                if (!$(this).valid()) {
                    return;
                }

                let form = $(this)[0];
                let formData = new FormData(form);

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                let flag = $("#flag").val();
                let id = $("#row_id").val();
                let url = flag === "A" ?
                    "{{ route('admin.shop-closures.store') }}" :
                    "{{ route('admin.shop-closures.update', ['shop' => request()->route('shop'), 'shop_closure' => '__CLOSURE_ID__']) }}"
                        .replace('__CLOSURE_ID__', encodeURIComponent(id));

                LoaderShow();

                $.ajax({
                    type: "POST",
                    url: url,
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        LoaderHide();
                        $("#ShopClosuresModal").modal('hide');
                        if (response.status === 1) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: response.message,
                                showConfirmButton: false,
                                timer: 2000
                            });
                            setTimeout(function() {
                                location.reload();
                            }, 1000);
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
                        LoaderHide();
                        console.log(xhr.responseText);
                        Swal.fire({
                            icon: 'warning',
                            title: 'Warning',
                            text: 'Error occurred while processing.',
                            showConfirmButton: false,
                            timer: 2000
                        });
                    }
                });
            });
        });
    </script>
@endpush
