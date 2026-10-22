@push('css')
    <style>
        #ServiceModal .swal2-container {
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

        .service-image-upload {
            cursor: pointer;
        }
    </style>
@endpush


@extends('admin.layout.layout')
@section('content')
    <script>
        page_active_id = "Service";
    </script>
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="bc-page-header">
            <div>
                <h4>Services</h4>
                <p>Manage service offerings, pricing, and duration</p>
            </div>
        </div>

        <!-- Product List Table -->
        <div class="card rounded-3 shadow-sm">
            <div class="card-header p-4">
                <div class="row">
                    <div class="col-lg-6"></div>
                    @can('Service-Add')
                        <div class="col-lg-6" style="text-align: right;">
                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                data-bs-target="#ServiceModal"><i class="bi bi-plus-lg"></i> Add</button>
                        </div>
                    @endcan

                </div>
            </div>
            <hr class="my-1">
            <div class="card-body table-responsive p-4">
                <table id="ServiceDataTable" class="table table-hover">
                    <thead class="border-top">
                        <tr>
                            <th>Sr. NO.</th>
                            <th>Service</th>
                            <th>Category</th>
                            <th>Duration</th>
                            <th>Price</th>
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
    <div class="modal fade" id="ServiceModal" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog">
            <form id="serice-form" class="modal-content" onsubmit="return false;">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="ServiceModalTitle"><i class="bi bi-scissors me-2"></i>Add Service</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-12 mb-3">
                            <label for="nameBackdrop" class="form-label mb-2">Category <span style="color:red;">*</span>
                            </label>
                            <select name="category_id" class="form-select select2" id="category_id">
                                <option value="" selected>-Select Category-</option>
                                @foreach ($ServiceCategory as $row)
                                    <option value="{{ $row->id }}">{{ $row->category_name }}</option>
                                @endforeach
                            </select>
                        </div>


                        <div class="col-lg-12 col-md-12 col-12 mb-3">
                            <label for="nameBackdrop" class="form-label mb-2">Service Name <span
                                    style="color:red;">*</span></label>
                            <input type="text" id="service_nm" name="service_nm" class="form-control"
                                placeholder="Enter Service Name" />
                        </div>


                        <div class="col-lg-6 col-md-12 col-12 mb-3">
                            <label for="nameBackdrop" class="form-label mb-2">Price ({{ $shopCurrencySymbol ?? '₹' }}) <span
                                    style="color:red;">*</span></label>
                            <input type="number" id="price" name="price" class="form-control"
                                min="0" max="99999999.99" step="0.01" inputmode="decimal"
                                placeholder="Enter Service Name" />
                        </div>

                        <div class="col-lg-6 col-md-12 col-12 mb-3">
                            <label for="nameBackdrop" class="form-label mb-2">Duration (Minutes) <span
                                    style="color:red;">*</span></label>
                            <input type="number" id="duration" name="duration" class="form-control"
                                min="1" step="1" inputmode="numeric"
                                placeholder="Enter Duration (Minutes)" value="{{ $defaultDuration ?? 30 }}" />
                        </div>


  <div class="col-lg-12 col-md-12 col-12 mb-3">
                            <label for="service_commission" class="form-label mb-2">Commission (%)</label>
                            <input type="number" id="service_commission" name="service_commission" class="form-control"
                                min="0" max="100" step="0.01" inputmode="decimal"
                                placeholder="Enter Commission (%)" />
                        </div>

                        <div class="col-lg-6 col-md-12 col-12 mb-3 d-flex justify-content-between">
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" id="active_status" name="active_status" />
                                <label class="form-check-label" for="flexSwitchCheckChecked1">Active</label>
                            </div>

                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" id="eligible_for_tip"
                                    name="eligible_for_tip" />
                                <label class="form-check-label" for="flexSwitchCheckChecked2">Eligible for Tip</label>
                            </div>
                        </div>

                        <div class="col-lg-12 col-md-12 col-12 mb-3">
                            <div class="mb-1">
                                <label for="service_image" class="form-label fw-semibold">Service Image <span
                                        id="service-image-required" class="text-danger">*</span></label>
                                <label for="service_image" class="service-image-upload d-block">
                                    <div class="text-center">
                                        <img id="ServiceImgUploadView" src="{{ asset('assets/img/image-upload.png') }}"
                                            style="max-width: 30%;" alt="Service image preview">
                                    </div>
                                </label>
                                <input type="file" id="service_image" name="service_image"
                                    accept="image/png,image/jpeg,image/gif" class="visually-hidden"
                                    onchange="image_show(this, 'ServiceImgUploadView')" />
                            </div>
                        </div>

                        <div class="col-lg-12 col-md-12 col-12 mb-3">
                            <label for="nameBackdrop" class="form-label mb-2">Service Description</label>
                            <textarea name="description" id="description" class="form-control" cols="30" rows="6"
                                placeholder="Enter Category Description"></textarea>
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

    <div class="modal fade" id="HomeSectionModal" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog">
            <form id="home-section-form" class="modal-content" onsubmit="return false;">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-house-door me-2"></i>Website Section</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="home_section_service_id" name="service_id">
                    <div class="mb-0">
                        <label class="form-label" for="home_section">Select Section</label>
                        <select class="form-select" id="home_section" name="home_section">
                            <option value="">-- Select --</option>
                            <option value="signature_grooming">Signature Grooming Menu</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
@endsection


@push('script')
    <script>
        $(document).ready(function() {
            var datatable = $('#ServiceDataTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.service.gettabledata') }}",
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
                        data: 'service_name',
                        name: 'service_name'
                    },
                    {
                        data: 'category_name',
                        name: 'category_name'
                    },
                    {
                        data: 'service_duration',
                        name: 'service_duration'
                    },
                    {
                        data: 'service_price',
                        name: 'service_price'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }

                ]
            });


            $('#ServiceDataTable tbody').on('click', '.view-details-btn',
                function() { // Delegated event handler for the button
                    var $row = $(this).closest('tr'); // Get the closest row element
                    var rowData = datatable.row($row).data(); // Get the entire row data object

                    $("#ServiceModal").modal('show');



                    $("#category_id").val(rowData.category_id).trigger('change');

                    function decodeHtml(value) {
                        var el = document.createElement('textarea');
                        el.innerHTML = value == null ? '' : String(value);
                        return el.value;
                    }

                    $("#service_nm").val(decodeHtml(rowData.service_name));
                    $("#price").val(rowData.price);
                    $("#duration").val(rowData.duration);
                    
                     $("#service_commission").val(rowData.commission);

                    $("#active_status").prop('checked', rowData.status == 1);
                    $("#eligible_for_tip").prop('checked', rowData.tip_status == 1);

                    $("#description").val(decodeHtml(rowData.description));
                    $("#ServiceImgUploadView").attr(
                        'src',
                        rowData.service_img ? "{{ asset('storage') }}/" + rowData.service_img :
                            "{{ asset('assets/img/image-upload.png') }}"
                    );
                    $("#service-image-required").hide();

                    $("#flag").val("U");
                    $("#row_id").val(rowData.id);
                    $("#_method").val("PUT");

                    $("#ServiceModalTitle").html('<i class="bi bi-scissors me-2"></i>Edit Service');
                    $("#UpdateButton").html('Update');
                });

            $('#ServiceDataTable tbody').on('click', '.delete-service-btn',
                function() { // Delegated event handler for the button
                    var $row = $(this).closest('tr'); // Get the closest row element
                    var rowData = datatable.row($row).data(); // Get the entire row data object


                    Swal.fire({
                        title: 'Are you sure?',
                        text: "Are you sure to delete Service?",
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
                                url: "{{ route('admin.service.destroy', ['shop' => request()->route('shop'), 'service' => '__SERVICE_ID__']) }}"
                                    .replace('__SERVICE_ID__', encodeURIComponent(id)),
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

            $('#ServiceDataTable tbody').on('click', '.home-section-btn', function() {
                var $row = $(this).closest('tr');
                var rowData = datatable.row($row).data();
                $("#home_section_service_id").val(rowData.id);
                $("#home_section").val(rowData.home_section || '');
                $("#HomeSectionModal").modal('show');
            });

            $("#home-section-form").submit(function(e) {
                e.preventDefault();
                LoaderShow();
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    type: "POST",
                    url: "{{ route('admin.service.home-section') }}",
                    data: {
                        service_id: $("#home_section_service_id").val(),
                        home_section: $("#home_section").val()
                    },
                    success: function(response) {
                        LoaderHide();
                        if (response.status === 1) {
                            $("#HomeSectionModal").modal('hide');
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
                                text: response.message || 'Unable to update'
                            });
                        }
                    },
                    error: function() {
                        LoaderHide();
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Something went wrong. Please try again.'
                        });
                    }
                });
            });

            $('#JobCategoryDataTable').on('hidden.bs.modal', function() {
                $("#_method").val("POST");
            });

            var defaultDuration = "{{ $defaultDuration ?? 30 }}";
            $('#ServiceModal').on('hidden.bs.modal', function() {
                $("#serice-form")[0].reset();
                $("#_method").val("POST");
                $("#flag").val("A");
                $("#row_id").val("");
                $("#duration").val(defaultDuration);
                $("#category_id").val("").trigger("change");
                $("#service_image").val("");
                $("#ServiceImgUploadView").attr("src", "{{ asset('assets/img/image-upload.png') }}");
                $("#service-image-required").show();
                $("#UpdateButton").html("Save");
                $("#ServiceModalTitle").html('<i class="bi bi-scissors me-2"></i>Add Service');
                if (validator) {
                    validator.resetForm();
                }
                $("#serice-form").find(".is-invalid").removeClass("is-invalid");
            });

        });



        $.validator.addMethod("decimalPlaces", function(value, element, places) {
            if (this.optional(element)) {
                return true;
            }
            var decimalPart = value.split(".")[1];
            return !decimalPart || decimalPart.length <= places;
        }, "Please enter a valid number of decimal places.");

        $.validator.addMethod("serviceImageType", function(value, element) {
            return this.optional(element) || /\.(jpg|jpeg|png|gif)$/i.test(value);
        }, "Please upload a valid image (JPG, JPEG, PNG, or GIF).");

        let validator = $("#serice-form").validate({
            ignore: [],
            rules: {
                category_id: {
                    required: true
                },
                service_nm: {
                    required: true
                },
                price: {
                    required: true,
                    number: true,
                    min: 0,
                    max: 99999999.99,
                    decimalPlaces: 2
                },
                duration: {
                    required: true,
                    digits: true,
                    min: 1
                },
                service_commission: {
                    number: true,
                    min: 0,
                    max: 100
                },
                service_image: {
                    required: function() {
                        return $("#flag").val() === "A";
                    },
                    serviceImageType: true
                }
            },
            messages: {
                category_id: {
                    required: "Please select a category"
                },
                service_nm: {
                    required: "Please enter service name"
                },
                price: {
                    required: "Please enter price",
                    number: "Price must be a number",
                    min: "Price cannot be negative",
                    max: "Price cannot exceed 99,999,999.99",
                    decimalPlaces: "Price can have no more than 2 decimal places"
                },
                duration: {
                    required: "Please enter duration",
                    digits: "Duration must be a whole number",
                    min: "Duration must be at least 1 minute"
                },
                service_commission: {
                    number: "Commission must be a number",
                    min: "Commission cannot be negative",
                    max: "Commission cannot be more than 100%"
                },
                service_image: {
                    required: "Please upload a service image.",
                    serviceImageType: "Please upload a valid image (JPG, JPEG, PNG, or GIF)."
                }
            },
            errorElement: "div",
            errorPlacement: function(error, element) {
                error.addClass("invalid-feedback").css("display", "block");
                if (element.attr("name") === "category_id") {
                    var $select2 = element.next(".select2");
                    error.insertAfter($select2.length ? $select2 : element);
                } else if (element.attr("name") === "service_image") {
                    error.insertAfter(element.closest(".mb-1").find(".service-image-upload"));
                } else {
                    error.insertAfter(element);
                }
            },
            highlight: function(element) {
                $(element).addClass("is-invalid").removeClass("is-valid");
            },
            unhighlight: function(element) {
                $(element).removeClass("is-invalid");
            }
        });

        $("#category_id").on("change", function() {
            $(this).valid();
        });
        $("#service_image").on("change", function() {
            $(this).valid();
        });

        // =========================

        // Final submission with AJAX remains mostly the same
        $("#serice-form").submit(function(e) {
            e.preventDefault();

            if (!$("#serice-form").valid()) {
                return false;
            }

            let form = $(this)[0]; // Native DOM element
            let formData = new FormData(
                form); // FormData will automatically include file inputs and all form fields

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var flag = $("#flag").val();
            var id = $("#row_id").val();
            var url = "";
            if (flag == "A") {
                url = "{{ route('admin.service.store') }}";
            } else {
                url = "{{ route('admin.service.update', ['shop' => request()->route('shop'), 'service' => '__SERVICE_ID__']) }}"
                    .replace('__SERVICE_ID__', encodeURIComponent(id));
            }

            LoaderShow();

            $.ajax({
                type: "POST",
                url: url,
                data: formData,
                processData: false, // Important for FormData
                contentType: false, // Important for FormData
                success: function(response) {
                    LoaderHide();
                    if (response.status === 1) {
                        $("#ServiceModal").modal('hide');
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
                        var fieldErrors = {};
                        var errorMessages = [];
                        $.each(xhr.responseJSON.errors, function(field, msgs) {
                            fieldErrors[field] = $.isArray(msgs) ? msgs[0] : msgs;
                            $.each(msgs, function(index, message) {
                                errorMessages.push(message);
                            });
                        });
                        validator.showErrors(fieldErrors);
                        Swal.fire({
                            icon: 'warning',
                            title: 'Please check the form',
                            text: errorMessages.join('\n'),
                            target: document.getElementById('ServiceModal'),
                            showConfirmButton: true
                        });
                        return;
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Something went wrong. Please try again.',
                        target: document.getElementById('ServiceModal'),
                        showConfirmButton: true
                    });
                }
            });
        });

        function image_show(input, imageId) {
            if (!input.files || !input.files[0]) {
                return;
            }

            var reader = new FileReader();
            reader.onload = function(event) {
                document.getElementById(imageId).src = event.target.result;
            };
            reader.readAsDataURL(input.files[0]);
        }

    </script>
@endpush
