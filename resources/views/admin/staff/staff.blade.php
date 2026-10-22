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

        .barber-toggle-wrap {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .barber-toggle-wrap .form-check-input {
            width: 3rem;
            height: 1.55rem;
            cursor: pointer;
            margin-top: 0;
        }

        .barber-toggle-label {
            font-weight: 600;
            min-width: 32px;
        }

        .barber-toggle-label.is-off {
            color: #71717a;
        }

        .barber-toggle-label.is-on {
            color: #136207;
        }
    </style>
@endpush


@extends('admin.layout.layout')
@section('content')
    <script>
        page_active_id = "Staff";
    </script>
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="bc-page-header">
            <div>
                <h4>Staff</h4>
                <p>Manage team members, roles, and commissions</p>
            </div>
        </div>

        <!-- Product List Table -->
        <div class="card rounded-3 shadow-sm">
            <div class="card-header p-4">
                <div class="row">
                    <div class="col-lg-6"></div>
                    @can('Staff-Add')
                        <div class="col-lg-6" style="text-align: right;">
                            <button type="button" id="AddStaffButton" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                data-bs-target="#StaffModal"><i class="bi bi-plus-lg"></i> Add</button>
                        </div>
                    @endcan

                </div>
            </div>
            <hr class="my-1">
            <div class="card-body table-responsive p-4">
                <table id="StaffDataTable" class="table table-hover">
                    <thead class="border-top">
                        <tr>
                            <th>Sr. NO.</th>
                            <th>Staff Name</th>
                            <th>Staff Email</th>
                            <th>Service Name</th>
                            <th>Product Commission (%)</th>
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



<div class="modal fade" id="ChangeStaffPassword" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog">
            <form id="change-password-form" class="modal-content" onsubmit="return false;">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="StaffModalTitle"><i class="bi bi-key me-2"></i>Change Password</h5>
                    <button type="button" class="btn-close" onclick="ClosePasswordChangeModel()"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-12 mb-3">
                            <label for="new_password" class="form-label mb-2">New Password <span style="color:red;">*</span>
                            </label>
                            <input type="text" id="new_password" name="new_password" class="form-control"
                                placeholder="Enter New Password" />
                        </div>


                        <div class="col-lg-12 col-md-12 col-12 mb-3">
                            <label for="confirm_password" class="form-label mb-2">Confirm New Password <span
                                    style="color:red;">*</span></label>
                            <input type="text" id="confirm_password" name="confirm_password" class="form-control"
                                placeholder="Confirm New Password" />
                        </div>


                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">
                        Close
                    </button>

                    <input type="hidden" id="_method" name="_method" value="POST">
                    <input type="hidden" id="staff_row_id" name="staff_row_id">
                    <button type="submit" class="btn btn-primary" id="UpdateButton">Update Password</button>
                </div>
            </form>
        </div>
    </div>


    <!-- Modal -->
    <div class="modal fade" id="StaffModal" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog">
            <form id="staff-form" class="modal-content" onsubmit="return false;">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="StaffModalTitle"><i class="bi bi-person-badge me-2"></i>Add Staff</h5>
                    <button type="button" class="btn-close" onclick="CloseModel()"></button>
                </div>
                <div class="modal-body">

                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-12 mb-3">
                            <label for="nameBackdrop" class="form-label mb-2">Staff Name <span style="color:red;">*</span>
                            </label>
                            <input type="text" id="staff_name" name="staff_name" class="form-control"
                                placeholder="Enter Staff Name" />
                        </div>

                        <div class="col-lg-12 col-md-12 col-12 mb-3">
                            <label for="nameBackdrop" class="form-label mb-2">Email <span style="color:red;">*</span>
                            </label>
                            <input type="text" id="staff_email" name="staff_email" class="form-control"
                                placeholder="Enter Staff Email" />
                        </div>

                        <div class="col-lg-12 col-md-12 col-12 mb-3">
                            <label for="nameBackdrop" class="form-label mb-2">Phone
                            </label>
                            <input type="text" id="staff_phone" name="staff_phone" class="form-control"
                                placeholder="Enter Staff Phone" />
                        </div>

                        <div class="col-lg-12 col-md-12 col-12 mb-3" id="PasswordInputFeild">
                            <label for="nameBackdrop" class="form-label mb-2">Password <span style="color:red;">*</span>
                            </label>
                            <input type="password" id="staff_password" name="staff_password" class="form-control"
                                placeholder="Enter Password" />
                        </div>


                        <div class="col-lg-12 col-md-12 col-12 mb-3">
                            <label class="form-label mb-2">Barber</label>
                            <div class="barber-toggle-wrap">
                                {{-- <span class="barber-toggle-label is-off">No</span> --}}
                                <div class="form-check form-switch mb-0">
                                    <input type="hidden" name="is_barber" value="0">
                                    <input class="form-check-input" type="checkbox" role="switch" id="is_barber"
                                        name="is_barber" value="1" checked>
                                </div>
                                {{-- <span class="barber-toggle-label is-on">Yes</span> --}}
                            </div>
                            <small class="text-muted">Turn off if this staff member is not a barber. Service assignments will be hidden.</small>
                        </div>

                        <div class="col-md-12 mb-6 barber-only-fields">
                            <label for="service_id" class="form-label">Service</label>
                            <select id="service_id" name="service_id[]" class="selectpicker w-100" data-style="btn-default"
                                multiple data-actions-box="true">
                                @foreach ($ServiceData as $row)
                                    <option value="{{ $row->id }}">{{ $row->service_name }}</option>
                                @endforeach
                            </select>
                        </div>



                        <div class="col-lg-6 col-md-12 col-12 mb-3" id="product-commission-field" style="display: none;">
                            <label for="commission" class="form-label mb-2">Product Commission (%)</label>
                            <input type="number" id="commission" name="commission" class="form-control"
                                min="0" max="100" step="0.01"
                                placeholder="Enter Product Commission" />
                        </div>

                        <div class="col-lg-6 col-md-12 col-12 mb-3">
                            <label for="nameBackdrop" class="form-label mb-2">Status <span
                                    style="color:red;">*</span></label>
                            <select name="status" class="form-select select2" id="status">
                                <option value="1" selected>Active</option>
                                <option value="0">Deactive</option>
                            </select>
                        </div>


                        <!--<div class="col-lg-6 col-md-12 col-12 mb-3">-->
                        <!--    <label for="morning_start_time" class="form-label mb-2">Morning Start Time <span-->
                        <!--            style="color: red;">*</span> </label>-->
                        <!--    <input type="time" id="morning_start_time" name="morning_start_time"-->
                        <!--        class="form-control" />-->
                        <!--</div>-->

                        <!--<div class="col-lg-6 col-md-12 col-12 mb-3">-->
                        <!--    <label for="morning_end_time" class="form-label mb-2">Morning End Time <span-->
                        <!--            style="color: red;">*</span></label>-->
                        <!--    <input type="time" id="morning_end_time" name="morning_end_time" class="form-control" />-->
                        <!--</div>-->


                        <!--<div class="col-lg-6 col-md-12 col-12 mb-3">-->
                        <!--    <label for="evening_start_time" class="form-label mb-2">Evening Start Time <span-->
                        <!--            style="color: red;">*</span></label>-->
                        <!--    <input type="time" id="evening_start_time" name="evening_start_time"-->
                        <!--        class="form-control" />-->
                        <!--</div>-->

                        <!--<div class="col-lg-6 col-md-12 col-12 mb-3">-->
                        <!--    <label for="evening_end_time" class="form-label mb-2">Evening End Time <span-->
                        <!--            style="color: red;">*</span></label>-->
                        <!--    <input type="time" id="evening_end_time" name="evening_end_time" class="form-control" />-->
                        <!--</div>-->


                        <div class="col-lg-12 col-md-12 col-12 mb-3">
                            <label for="nameBackdrop" class="form-label mb-2">Role <span
                                    style="color:red;">*</span></label>
                            <select name="role_id" class="form-select select2" id="role_id">
                                <option value="" selected>Select Role</option>
                                @foreach ($roles as $row)
                                    <option value="{{ $row->id }}">{{ $row->name }}</option>
                                @endforeach
                            </select>
                            @if ($roles->isEmpty())
                                <small class="text-warning">Create a staff role in Roles &amp; Permissions before adding team members.</small>
                            @endif
                        </div>



                        <div class="col-lg-12 col-md-12 col-12 mb-3">
                            <div class="mb-1">
                                <label for="profile_image" class="form-label fw-semibold">Staff Profile Image</label>
                                <div class="service-image-upload"
                                    onclick="document.getElementById('profile_image').click();">

                                    <div class="text-center">
                                        <img id="ProfileImgUploadView" src="{{ asset('assets/img/image-upload.png') }}"
                                            style="max-width: 30%;" alt="">
                                    </div>
                                </div>

                                <input type="file" id="profile_image" name="profile_image"
                                    accept="image/png, image/jpeg, image/gif" style="visibility: hidden"
                                    onchange="image_show(0,this, 'ProfileImgUploadView')" />
                            </div>
                        </div>



                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">
                        Close
                    </button>


                    <input type="hidden" id="staff_form_method" name="_method" value="POST">
                    <input type="hidden" id="flag" name="flag" value="A">
                    <input type="hidden" id="row_id" name="row_id">
                    <button type="submit" class="btn btn-primary" id="UpdateButtonUser">Add</button>
                </div>
            </form>
        </div>
    </div>
@endsection



@push('script')
    <script>
        $(document).ready(function() {
            $('#AddStaffButton').on('click', function() {
                $('#staff-form')[0].reset();
                $('#flag').val('A');
                $('#row_id').val('');
                $('#staff_form_method').val('POST');
                $('#UpdateButtonUser').text('Add');
                $('#PasswordInputFeild').show();
                $('#role_id').val('').trigger('change');
                $('#service_id').selectpicker('deselectAll');
                $('#ProfileImgUploadView').attr('src', "{{ asset('assets/img/image-upload.png') }}");

            });

            $('#role_id').on('change', function() {
                const roleName = $(this).find('option:selected').text().trim();
                const isStaffRole = $(this).val() && roleName !== 'Accountant';
                $('#product-commission-field').toggle(Boolean(isStaffRole));
                if (!isStaffRole) {
                    $('#commission').val('');
                }
            });

            var datatable = $('#StaffDataTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.staff.gettabledata') }}",
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
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'service_names',
                        name: 'service_names'
                    },
                    {
                        data: 'product_commission',
                        name: 'commission'
                    },
                    {
                        data: 'staff_status',
                        name: 'staff_status'
                    },

                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }

                ]
            });


            $('#StaffDataTable tbody').on('click', '.view-details-btn',
                function() { // Delegated event handler for the button
                    var $row = $(this).closest('tr'); // Get the closest row element
                    var rowData = datatable.row($row).data(); // Get the entire row data object

                    $("#StaffModal").modal('show');

                    var isBarber = String(rowData.is_barber) === '1' || String(rowData.is_barber) === 'true';
                    $("#is_barber").prop('checked', isBarber);
                    toggleBarberFields();

                    var selectedValues = (rowData.service_id || '').split(',').filter(Boolean);

                    // Remove spaces + duplicates
                    let uniqueValues = [...new Set(selectedValues.map(v => v.trim()))];

                    // Debug check

                    // First clear selection
                    $("#service_id").selectpicker('deselectAll');

                    // Set values (must exist in <option> values)
                    $("#service_id").selectpicker('val', uniqueValues);



                    // Set input values
                    $("#staff_name").val(rowData.name);
                    $("#staff_email").val(rowData.email);
                    $("#staff_phone").val(rowData.phone);
                    $("#commission").val(rowData.product_commission);


                    // $("#morning_start_time").val(rowData.morning_start_time);
                    // $("#morning_end_time").val(rowData.morning_end_time);
                    // $("#evening_start_time").val(rowData.evning_start_time);
                    // $("#evening_end_time").val(rowData.evning_end_time);





                    let imgPath = "{{ asset('storage') }}/" + rowData.profile_img;
                    
                    if(rowData.profile_img){
                                $("#ProfileImgUploadView").attr('src', imgPath);
                    }
            

                    document.getElementById("PasswordInputFeild").style.display = "none";

                    $("#status").val(rowData.active_status).trigger('change');
                    $("#role_id").val(rowData.role_ids).trigger('change');




                    $("#flag").val("U");
                    $("#row_id").val(rowData.id);
                    $("#staff_form_method").val("PUT");

                    $("#UpdateButtonUser").html('Update');
                });

            $('#StaffDataTable tbody').on('click', '.delete-staff-btn',
                function() { // Delegated event handler for the button
                    var $row = $(this).closest('tr'); // Get the closest row element
                    var rowData = datatable.row($row).data(); // Get the entire row data object


                    Swal.fire({
                        title: 'Are you sure?',
                        text: "Are you sure to delete Staff?",
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
                                url: "{{ route('admin.staff.destroy', ['shop' => request()->route('shop'), 'staff' => '__STAFF_ID__']) }}"
                                    .replace('__STAFF_ID__', encodeURIComponent(id)),
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


        $('#StaffDataTable tbody').on('click', '.change-password',
                function() { // Delegated event handler for the button
                    var $row = $(this).closest('tr'); // Get the closest row element
                    var rowData = datatable.row($row).data(); // Get the entire row data object

                    $("#ChangeStaffPassword").modal('show');

                    $("#staff_row_id").val(rowData.id);
                });




            $('#StaffModalDataTable').on('hidden.bs.modal', function() {
                $("#_method").val("POST");
            });

        });
        
        
        
        let changePassword = $("#change-password-form").validate({
            rules: {
                new_password: {
                    required: true,
                    minlength: 6
                },
                confirm_password: {
                    required: true,
                    equalTo: "#new_password" // must match new_password field
                },
            },
            messages: {
                new_password: {
                    required: "Please enter a new password",
                    minlength: "Password must be at least 6 characters long"
                },
                confirm_password: {
                    required: "Please confirm your password",
                    equalTo: "Passwords do not match"
                },
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
                $(element).removeClass("is-invalid").addClass("is-valid");
            }
        });


        $("#change-password-form").submit(function(e) {
            e.preventDefault();

            if (!$(this).valid()) {
                return;
            }

            let formData = new FormData(this);
            let id = $("#row_id").val() || "";

            if (typeof LoaderShow === "function") LoaderShow();

            $.ajax({
                type: "POST",
                url: "{{ route('admin.staff.change-password') }}",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (typeof LoaderHide === "function") LoaderHide();
                    $("#ChangeStaffPassword").modal("hide");
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
                            text: response.message,
                            showConfirmButton: false,
                            timer: 2000
                        });
                    }
                },
                error: function(xhr) {
                    if (typeof LoaderHide === "function") LoaderHide();
                    console.error(xhr.responseText);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error occurred while processing.',
                        showConfirmButton: false,
                        timer: 2000
                    });
                }
            });
        });



        $(document).ready(function() {
            // Initialize jQuery Validation
            let validator = $("#staff-form").validate({
                ignore: ':hidden:not(.selectpicker)',
                rules: {
                    staff_name: {
                        required: true
                    },
                    staff_email: {
                        required: true,
                        email: true
                    },
                    staff_phone: {
                        required: false
                    },
                    staff_password: {
                        required: function() {
                            // Required only on create (flag = 'A')
                            return $("#flag").val() === "A";
                        }
                    },
                    "service_id[]": {
                        required: function() {
                            return $("#is_barber").is(":checked");
                        }
                    },
                    commission: {
                        number: true,
                        min: 0,
                        max: 100,
                        required: false
                    },
                    status: {
                        required: true
                    },
                    // morning_start_time: {
                    //     required: true
                    // },
                    // morning_end_time: {
                    //     required: true
                    // },
                    // evening_start_time: {
                    //     required: true
                    // },
                    // evening_end_time: {
                    //     required: true
                    // },
                    role_id: {
                        required: true
                    },
                    profile_image: {
                        extension: "jpg|jpeg|png|gif"
                    }
                },
                messages: {
                    staff_name: {
                        required: "Please enter Staff Name"
                    },
                    staff_email: {
                        required: "Please enter Staff Email",
                        email: "Enter valid email"
                    },
                    staff_password: {
                        required: "Please enter Password"
                    },
                    "service_id[]": {
                        required: "Please select at least one Service"
                    },
                    commission: {
                        number: "Product commission must be a number",
                        min: "Product commission cannot be negative",
                        max: "Product commission cannot exceed 100%"
                    },
                    status: {
                        required: "Please select Status"
                    },
                    // morning_start_time: {
                    //     required: "Please Select Morning Star Timet"
                    // },
                    // morning_end_time: {
                    //     required: "Please Select Morning End Time"
                    // },
                    // evening_start_time: {
                    //     required: "Please Select Evening start Time"
                    // },
                    // evening_end_time: {
                    //     required: "Please Select Evening End Time"
                    // },
                    status: {
                        required: "Please select Status"
                    },
                    role_id: {
                        required: "Please select Role"
                    },
                    profile_image: {
                        extension: "Please upload a valid image file (jpg, jpeg, png, gif)"
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

            // AJAX form submit
            $("#staff-form").submit(function(e) {
                e.preventDefault();

                if (!$(this).valid()) {
                    return;
                }

                let formData = new FormData(this);
                let flag = $("#flag").val();
                let id = $("#row_id").val() || "";
                let url = flag === "A" ?
                    "{{ route('admin.staff.store') }}" :
                    "{{ route('admin.staff.update', ['shop' => request()->route('shop'), 'staff' => '__STAFF_ID__']) }}"
                        .replace('__STAFF_ID__', encodeURIComponent(id));

                if (typeof LoaderShow === "function") LoaderShow();

                $.ajax({
                    type: "POST",
                    url: url,
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (typeof LoaderHide === "function") LoaderHide();
                        $("#StaffModal").modal("hide");
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
                                text: response.message,
                                showConfirmButton: false,
                                timer: 2000
                            });
                        }
                    },
                    error: function(xhr) {
                        if (typeof LoaderHide === "function") LoaderHide();
                        var message = 'Error occurred while processing.';
                        if (xhr.responseJSON) {
                            if (xhr.responseJSON.errors) {
                                var first = Object.values(xhr.responseJSON.errors)[0];
                                message = Array.isArray(first) ? first[0] : first;
                            } else if (xhr.responseJSON.message) {
                                message = xhr.responseJSON.message;
                            }
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: message
                        });
                    }
                });
            });
        });


  function CloseModel() {
            location.reload();
        }

        function toggleBarberFields() {
            var isBarber = $("#is_barber").is(":checked");
            $(".barber-only-fields").toggle(isBarber);
            if (!isBarber) {
                $("#service_id").selectpicker("deselectAll");
                if ($("#staff-form").data("validator")) {
                    $("#service_id").valid();
                }
            }
            if ($.fn.selectpicker) {
                $("#service_id").selectpicker("refresh");
            }
        }

        $(document).on("change", "#is_barber", toggleBarberFields);
        $("#StaffModal").on("shown.bs.modal", function() {
            if ($("#flag").val() === "A") {
                $("#is_barber").prop("checked", true);
            }
            toggleBarberFields();
        });


        function image_show(id, input, img_id) {

            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById(img_id).src = e.target.result;

                };
                reader.readAsDataURL(input.files[0]);

            }
        }
        
        
        function ClosePasswordChangeModel() {
            location.reload();
        }
    </script>
@endpush
