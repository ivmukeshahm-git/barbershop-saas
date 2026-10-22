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


        page_active_id = "Customer";


    </script>


    <div class="container-xxl flex-grow-1 container-p-y">


        <div class="bc-page-header">


            <div>


                <h4>Customers</h4>


                <p>Manage your customer directory</p>


            </div>


        </div>





        <!-- Product List Table -->


        <div class="card rounded-3 shadow-sm">


            <div class="card-header p-4">


                <div class="row">


                    <div class="col-lg-6"></div>





                    @can('Customer-Add')


                        <div class="col-lg-6" style="text-align: right;">


                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"


                                data-bs-target="#CustomerAddModal"><i class="bi bi-plus-lg"></i> Add Customer</button>


                        </div>


                    @endcan





                </div>


            </div>


            <hr class="my-1">


            <div class="card-body table-responsive p-4">


                <table id="CustomerDataTable" class="table table-hover">


                    <thead class="border-top">


                        <tr>


                            <th>Sr. NO.</th>


                            <th>Name</th>


                            <th>Phone</th>


                            <th>Status</th>


                            {{-- <th>Last Visit</th>


                            <th>Total Spent</th>


                            <th>Visits</th> --}}


                            <th>Action</th>


                        </tr>


                    </thead>


                    <tbody>


                        {{-- <tr>


                            <td>1</td>


                            <td>Mukesh Suthar</td>


                            <td>7296930519</td>


                            <td>Sep 6, 2025</td>


                            <td>$171.00 </td>


                            <td>2</td>


                            <td></td>


                        </tr> --}}


                    </tbody>


                </table>


            </div>


        </div>


    </div>


    <!-- / Content -->














    <!-- Modal -->


    <style>
        #CustomerAddModal .swal2-container {
            z-index: 2000 !important;
        }
    </style>
    <div class="modal fade" id="CustomerAddModal" data-bs-backdrop="static" tabindex="-1">


        <div class="modal-dialog">


            <form id="customer-form" class="modal-content" onsubmit="return false;">


                @csrf


                <div class="modal-header">


                    <h5 class="modal-title" id="CustomerAddModalTitle"><i class="bi bi-person-plus me-2"></i>Add New Customer</h5>


                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>


                </div>


                <div class="modal-body">


                    <div class="row">


                        <div class="col-lg-12 col-md-12 col-12 mb-3">


                            <label for="full_name" class="form-label mb-2">Full Name <span style="color: red;">*</span>


                            </label>


                            <input type="text" id="full_name" name="full_name" class="form-control"


                                placeholder="Enter Full Name" />


                        </div>








                        <div class="col-lg-12 col-md-12 col-12 mb-3">


                            <label for="email" class="form-label mb-2">Email (Optional)</label>


                            <input type="email" id="email" name="email" class="form-control"


                                placeholder="Enter Email Address" />


                        </div>








                        <div class="col-lg-12 col-md-12 col-12 mb-3">


                            <label for="phone_number" class="form-label mb-2">Phone Number <span


                                    style="color: red;">*</span></label>


                            <input type="tel" inputmode="tel" id="phone_number" name="phone_number" class="form-control"


                                placeholder="Enter Phone No." />


                        </div>








                        <div class="col-lg-12 col-md-12 col-12 mb-3">


                            <label for="nameBackdrop" class="form-label mb-2">Status <span


                                    style="color: red;">*</span></label>


                            <select class="form-select" id="status" name="status">


                                <option value="1">Active</option>


                                <option value="0">Deactive</option>


                            </select>


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


                    <button type="submit" class="btn btn-primary" id="UpdateBtn">Add Customer</button>


                </div>


            </form>


        </div>


    </div>


@endsection





@push('script')


    <script>


        $(document).ready(function() {


            var datatable = $('#CustomerDataTable').DataTable({


                processing: true,


                serverSide: true,


                ajax: {


                    url: "{{ route('admin.customers.gettabledata') }}",


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


                        data: 'customer_name',


                        name: 'customer_name'


                    },


                    {


                        data: 'customer_phone',


                        name: 'customer_phone'


                    },


                    {


                        data: 'status_badge',


                        name: 'status_badge'


                    },





                    {


                        data: 'action',


                        name: 'action',


                        orderable: false,


                        searchable: false


                    }





                ]


            });








            $('#CustomerDataTable tbody').on('click', '.view-details-btn',


                function() { // Delegated event handler for the button


                    var $row = $(this).closest('tr'); // Get the closest row element
                    var rowData = datatable.row($row).data(); // Get the entire row data object

                    var nameDecoder = document.createElement('textarea');
                    nameDecoder.innerHTML = rowData.customer_name || '';

                    $("#CustomerAddModal").modal("show");


                    $("#full_name").val(nameDecoder.value);


                    $("#phone_number").val(rowData.customer_phone);


                    $("#email").val(rowData.customer_email);





                    $("#flag").val("U");
                    $("#CustomerAddModalTitle").html('<i class="bi bi-person-plus me-2"></i>Edit Customer');
                    $("#row_id").val(rowData.customer_id);


                    $("#_method").val("PUT");








                    $("#UpdateBtn").html("Update Customer");


                    $("#status").val(rowData.customer_status);





                });





            $('#CustomerDataTable tbody').on('click', '.delete-customer-btn',


                function() { // Delegated event handler for the button


                    var $row = $(this).closest('tr'); // Get the closest row element


                    var rowData = datatable.row($row).data(); // Get the entire row data object








                    Swal.fire({


                        title: 'Are you sure?',


                        text: "Are you sure to delete Customer?",


                        icon: 'warning',


                        showCancelButton: true,


                        confirmButtonText: 'Yes, confirm!',


                        customClass: {


                            confirmButton: 'btn btn-primary me-3',


                            cancelButton: 'btn btn-label-secondary'


                        }


                    }).then((result) => {


                        if (result.isConfirmed) {





                            var id = rowData.customer_id;


                            var data = {


                                'id': id


                            }





                            LoaderShow()





                            $.ajaxSetup({


                                headers: {


                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')


                                }


                            });





                            $.ajax({


                                type: "DELETE",


                                url: "{{ route('admin.customers.destroy', ['shop' => request()->route('shop'), 'customer' => '__CUSTOMER_ID__']) }}"
                                    .replace('__CUSTOMER_ID__', encodeURIComponent(id)),


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














            $('#addRoleModal').on('hidden.bs.modal', function() {


                $("#_method").val("POST");


            });





        });








        $.validator.addMethod("personName", function(value, element) {
            return this.optional(element) || /^[\p{L}\p{M}\s'.-]+$/u.test(value);
        }, "Full Name can contain letters only.");

        $.validator.addMethod("phoneNumber", function(value, element) {
            if (this.optional(element)) {
                return true;
            }
            var digits = (value.match(/[0-9]/g) || []).length;
            return /^\+?[0-9().\-\s]+$/.test(value) && digits >= 7 && digits <= 15;
        }, "Enter a valid phone number with 7 to 15 digits.");

        let validator = $("#customer-form").validate({


            rules: {


                full_name: {
                    required: true,
                    personName: true


                },


                phone_number: {


                    required: true,
                    phoneNumber: true


                },

                email: {
                    email: true
                },


                status: {


                    required: true


                },


            },


            messages: {


                full_name: {
                    required: "Please Enter Full Name",
                    personName: "Full Name can contain letters only."


                },


                phone_number: {


                    required: "Please Enter Phone No.",
                    phoneNumber: "Enter a valid phone number with 7 to 15 digits."


                },

                email: {
                    email: "Please enter a valid email address."
                },


                status: {


                    required: "Please Select Status"


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


                $(element).removeClass("is-invalid");


            }


        });

        $("#customer-form").on("input change", "input, select", function() {
            if ($(this).hasClass("is-invalid")) {
                validator.element(this);
            }
        });

        $("#CustomerAddModal").on("hidden.bs.modal", function() {
            $("#customer-form")[0].reset();
            $("#flag").val("A");
            $("#row_id").val("");
            $("#_method").val("POST");
            $("#UpdateBtn").text("Add Customer");
            $("#CustomerAddModalTitle").html('<i class="bi bi-person-plus me-2"></i>Add New Customer');
            validator.resetForm();
            $("#customer-form").find(".is-invalid").removeClass("is-invalid");
        });





        // ✅ Final submission with AJAX


        $("#customer-form").submit(function(e) {


            e.preventDefault();





            if (!$("#customer-form").valid()) {


                return;


            }





            let form = $(this);


            // let url = form.attr('action');


            let formData = form.serialize(); // Use FormData for file uploads





            $.ajaxSetup({


                headers: {


                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')


                }


            });








            var flag = $("#flag").val();





            var id = $("#row_id").val();


            var url = "";


            if (flag == "A") {
                url = "{{ route('admin.customers.store') }}";
            } else {
                url = "{{ route('admin.customers.update', ['shop' => request()->route('shop'), 'customer' => '__CUSTOMER_ID__']) }}"
                    .replace('__CUSTOMER_ID__', encodeURIComponent(id));
            }





            LoaderShow()





            $.ajax({


                type: "POST",


                url: url,


                data: formData,


                success: function(response) {


                    LoaderHide()

                    if (response.status === 1) {

                        $("#CustomerAddModal").modal("hide");

                        Swal.fire({


                            icon: 'success',


                            title: 'Success',


                            text: response.message,


                            showConfirmButton: false,


                            timer: 2000


                        });


                        form[0].reset();


                        validator.resetForm();





                        location.reload();


                    } else {


                        Swal.fire({


                            icon: 'warning',

                            title: 'Warning',

                            text: window.ajaxResponseMessage(response, 'Customer could not be saved.'),


                            showConfirmButton: false,


                            timer: 2000


                        });


                    }


                },


                error: function(xhr) {


                    LoaderHide()
                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        var errorMessages = [];
                        var fieldErrors = {};
                        $.each(xhr.responseJSON.errors, function(field, fieldMessages) {
                            fieldErrors[field] = $.isArray(fieldMessages) ?
                                fieldMessages.join(' ') : fieldMessages;
                            $.each(fieldMessages, function(index, message) {
                                errorMessages.push(message);
                            });
                        });
                        validator.showErrors(fieldErrors);
                        $.each(fieldErrors, function(field) {
                            $("#customer-form").find("[name='" + field + "']")
                                .addClass("is-invalid");
                        });
                        Swal.fire({
                            icon: 'warning',
                            title: 'Please check the form',
                            text: errorMessages.join('\n'),
                            target: document.getElementById('CustomerAddModal'),
                            showConfirmButton: true
                        });
                        return;
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Update failed',
                        text: window.ajaxResponseMessage(xhr, 'Customer could not be saved. Please try again.'),
                        target: document.getElementById('CustomerAddModal'),
                        showConfirmButton: true
                    });
                }
            });
        });
    </script>


@endpush
