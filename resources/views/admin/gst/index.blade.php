@push('css')
    <style>
        #GstModal .swal2-container {
            z-index: 2000 !important;
        }

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
        page_active_id = "GSTSettings";
    </script>
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="bc-page-header">
            <div>
                <h4>GST Settings</h4>
                <p>Configure tax rates for services and products</p>
            </div>
        </div>

        <!-- Product List Table -->
        <div class="card rounded-3 shadow-sm">
            <div class="card-header p-4">
                <div class="row">
                    <div class="col-lg-6"></div>
                    @can('Gst-Add')
                        <div class="col-lg-6" style="text-align: right;">
                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                data-bs-target="#GstModal"><i class="bi bi-plus-lg"></i> Add</button>
                        </div>
                    @endcan

                </div>
            </div>
            <hr class="my-1">
            <div class="card-body table-responsive p-4">
                <table id="GstDataTable" class="table table-hover">
                    <thead class="border-top">
                        <tr>
                            <th>Sr. NO.</th>
                            <th>GST Name</th>
                            <th>Total Rate</th>
                            <th>Type</th>
                            <th>Default</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- <tr>
                            <td>1</td>
                            <td>GST 10%</td>
                            <td>5%</td>
                            <td><img src="{{ asset('assets/img/Completed.png') }}" style="max-width: 75px;" alt="">
                            </td>
                            <th><span class="badge rounded-pill bg-label-success">Active</span></th>
                            <td><button class="btn btn-sm btn-warning p-2 mx-2"><i class="fa fa-pencil-square-o"
                                        aria-hidden="true"></i></button><button class="btn btn-sm btn-danger p-2"><i
                                        class="fa fa-trash" aria-hidden="true"></i></button></td>
                        </tr> --}}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- / Content -->




    <!-- Modal -->
    <div class="modal fade" id="GstModal" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog">
            <form id="gst-form" class="modal-content" onsubmit="return false;">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="GstModalTitle"><i class="bi bi-percent me-2"></i>Add GST</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-12 mb-3">
                            <label for="gst_name" class="form-label mb-2">GST Name <span
                                    class="text-danger">*</span></label>
                            <input type="text" id="gst_name" name="gst_name" class="form-control"
                                placeholder="Enter Product Name" />
                        </div>



                        <div class="col-lg-12 col-md-12 col-12 mb-3">
                            <label for="gst_rate" class="form-label mb-2">Total Rate <span
                                    class="text-danger">*</span></label>
                            <input type="text" id="gst_rate" name="gst_rate" class="form-control"
                                placeholder="Enter GST Rate" />
                        </div>
                        <div class="col-lg-12 col-md-12 col-12 mb-3">
                            <label for="tax_type" class="form-label mb-2">Type <span
                                    class="text-danger">*</span></label>
                            <select id="tax_type" name="tax_type" class="form-select">
                                <option value="include">Include</option>
                                <option value="exclude">Exclude</option>
                            </select>
                        </div>
                        <div class="col-lg-12 col-md-12 col-12 mb-3">
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" id="Gst_defualt" name="Gst_defualt" />
                                <label class="form-check-label" for="flexSwitchCheckChecked1">Default</label>
                            </div>
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
            var datatable = $('#GstDataTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.gst.gettabledata') }}",
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
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'rate',
                        name: 'rate'
                    },
                    {
                        data: 'tax_type_label',
                        name: 'tax_type'
                    },
                    {
                        data: 'default_status_img',
                        name: 'default_status_img'
                    },

                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }

                ]
            });


            $('#GstDataTable tbody').on('click', '.view-details-btn',
                function() { // Delegated event handler for the button
                    var $row = $(this).closest('tr'); // Get the closest row element
                    var rowData = datatable.row($row).data(); // Get the entire row data object

                    $("#GstModal").modal('show');




                    // Set input values
                    $("#gst_name").val(rowData.name);
                    $("#gst_rate").val(rowData.rate);
                    $("#tax_type").val(rowData.tax_type || 'include');


                    // Set checkbox states (assuming status and tip_status are 1 or 0)
                    $("#Gst_defualt").prop('checked', rowData.default_status == 1);


                    $("#flag").val("U");
                    $("#row_id").val(rowData.id);
                    $("#_method").val("PUT");

                    $("#GstModalTitle").html('<i class="bi bi-percent me-2"></i>Edit GST');
                    $("#UpdateButton").html('Update');
                });

            $('#GstDataTable tbody').on('click', '.delete-gst-btn',
                function() { // Delegated event handler for the button
                    var $row = $(this).closest('tr'); // Get the closest row element
                    var rowData = datatable.row($row).data(); // Get the entire row data object


                    Swal.fire({
                        title: 'Are you sure?',
                        text: "Are you sure to delete Gst?",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, confirm!',
                        customClass: {
                            confirmButton: 'btn btn-primary me-3',
                            cancelButton: 'btn btn-label-secondary'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {

                            var id = rowData.id;
                            var data = {
                                'id': id
                            }

                            $.ajaxSetup({
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                }
                            });

                            LoaderShow()

                            $.ajax({
                                type: "DELETE",
                                url: "{{ url('admin/gst') }}/" + id,
                                data: data,
                                dataType: "json",
                                success: function(response) {
                                    LoaderHide()
                                    if (response.status === 1) {
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Success',
                                            text: response.message,
                                            showConfirmButton: false,
                                            timer: 2000
                                        });

                                        location.reload();
                                    } else {
                                        Swal.fire({
                                            icon: 'warning',
                                            title: 'Warning',
                                            text: response.message,
                                            showConfirmButton: false,
                                            timer: 2000
                                        });
                                    }
                                }
                            });
                            return false;
                        }
                    });
                });




            $('#GstModal').on('hidden.bs.modal', function() {
                $("#_method").val("POST");
                $("#flag").val("A");
                $("#row_id").val("");
                $("#gst-form")[0].reset();
                $("#tax_type").val("include");
                $("#GstModalTitle").html('<i class="bi bi-percent me-2"></i>Add GST');
                $("#UpdateButton").html("Save");
            });

        });

        let validator = $("#gst-form").validate({
            rules: {
                gst_name: {
                    required: true
                },
                gst_rate: {
                    required: true,
                    number: true,
                    min: 0
                },
                tax_type: {
                    required: true
                }
            },
            messages: {
                gst_name: {
                    required: "Please enter GST Name"
                },
                gst_rate: {
                    required: "Please enter GST Rate",
                    number: "GST Rate must be a number",
                    min: "GST Rate cannot be negative"
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

        // Ajax form submission with validation
        $("#gst-form").submit(function(e) {
            e.preventDefault();

            if (!$("#gst-form").valid()) {
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
                "{{ route('admin.gst.store') }}" :
                "{{ url('admin/gst') }}/" + id;

            LoaderShow();

            $.ajax({
                type: "POST",
                url: url,
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    LoaderHide();
                    $("#GstModal").modal('hide');
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
                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        var errorMessages = [];
                        $.each(xhr.responseJSON.errors, function(field, fieldMessages) {
                            $.each(fieldMessages, function(index, message) {
                                errorMessages.push(message);
                            });
                        });
                        Swal.fire({
                            icon: 'warning',
                            title: 'Please check the form',
                            text: errorMessages.join('\n'),
                            target: document.getElementById('GstModal'),
                            showConfirmButton: true
                        });
                        return;
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: (xhr.responseJSON && xhr.responseJSON.message) ?
                            xhr.responseJSON.message : 'Something went wrong. Please try again.',
                        target: document.getElementById('GstModal'),
                        showConfirmButton: true
                    });
                }
            });
        });
    </script>
@endpush
