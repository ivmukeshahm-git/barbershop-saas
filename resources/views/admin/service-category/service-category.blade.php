@push('css')

    <style>

        #ServiceCategoryModal .swal2-container {
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


        .category-image-upload {
            cursor: pointer;
            border: 1px dashed #b8c2cc;
            border-radius: 12px;
            min-height: 150px;
            padding: 16px;
            display: flex !important;
            align-items: center;
            justify-content: center;
            background-color: #f8fafc;
            transition: border-color 0.2s ease, background-color 0.2s ease;
        }

        .category-image-upload:hover,
        .category-image-upload:focus-within {
            border-color: var(--bs-primary, #696cff);
            background-color: #f1f5f9;
        }

        .category-image-upload img {
            display: block;
            max-width: 100%;
            max-height: 180px;
            object-fit: contain;
        }

    </style>

@endpush





@extends('admin.layout.layout')

@section('content')

    <script>

        page_active_id = "ServiceCategory";

    </script>

    <div class="container-xxl flex-grow-1 container-p-y">

        <div class="bc-page-header">

            <div>

                <h4>Service Categories</h4>

                <p>Organize services into categories</p>

            </div>

        </div>



        <!-- Product List Table -->

        <div class="card rounded-3 shadow-sm">

            <div class="card-header p-4">

                <div class="row">

                    <div class="col-lg-6"></div>

                    <div class="col-lg-6" style="text-align: right;">

                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"

                            data-bs-target="#ServiceCategoryModal"><i class="bi bi-plus-lg"></i> Add</button>

                    </div>

                </div>

            </div>

            <hr class="my-1">

            <div class="card-body table-responsive p-4">

                <table id="ServiceCategoryDataTable" class="table table-hover">

                    <thead class="border-top">

                        <tr>

                            <th>Sr. NO.</th>

                            <th>Image</th>

                            <th>Category Name</th>

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

    <div class="modal fade" id="ServiceCategoryModal" data-bs-backdrop="static" tabindex="-1">

        <div class="modal-dialog">

            <form id="service-category-form" enctype="multipart/form-data" class="modal-content" onsubmit="return false;">

                @csrf

                <div class="modal-header">

                    <h5 class="modal-title" id="ServiceCategoryModalTitle"><i class="bi bi-folder-plus me-2"></i>Add Service Category</h5>

                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

                </div>

                <div class="modal-body">



                    <div class="row">

                        <div class="col-lg-12 col-md-12 col-12 mb-3">

                            <label for="category_nm" class="form-label mb-2">Category Name <span
                                    class="text-danger">*</span></label>
                            <input type="text" id="category_nm" name="category_nm" class="form-control"

                                placeholder="Enter Category Name" />

                        </div>



                        <div class="col-lg-12 col-md-12 col-12 mb-3">

                            <label for="service_image" class="form-label fw-semibold">Category Image <span
                                    id="category-image-required" class="text-danger">*</span></label>
                            <label for="service_image" class="category-image-upload">
                                <div class="text-center">
                                    <img id="CategoryImgUploadView" src="{{ asset('assets/img/image-upload.png') }}"
                                        style="max-width: 100%;" alt="Category image preview">
                                </div>
                            </label>
                            <input type="file" id="service_image" name="service_image"
                                accept="image/jpeg,image/png,image/gif,image/webp" class="visually-hidden"
                                onchange="category_image_show(this, 'CategoryImgUploadView')" />
                        </div>





                        <div class="col-lg-12 col-md-12 col-12 mb-3">

                            <label for="nameBackdrop" class="form-label mb-2">Category Description</label>

                            <textarea name="category_description" id="category_description" class="form-control" cols="30" rows="6"

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

@endsection





@push('script')

    <script>

        $(document).ready(function() {

            var datatable = $('#ServiceCategoryDataTable').DataTable({

                processing: true,

                serverSide: true,

                ajax: {

                    url: "{{ route('admin.service-category.gettabledata') }}",

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

                        data: 'service_image',

                        name: 'service_image'

                    },

                    {

                        data: 'category_name',

                        name: 'category_name'

                    },

                    {

                        data: 'action',

                        name: 'action',

                        orderable: false,

                        searchable: false

                    }



                ]

            });





            $('#ServiceCategoryDataTable tbody').on('click', '.view-details-btn',

                function() { // Delegated event handler for the button

                    var $row = $(this).closest('tr'); // Get the closest row element

                    var rowData = datatable.row($row).data(); // Get the entire row data object









                    $("#ServiceCategoryModal").modal("show");

                    $("#category_nm").val(rowData.category_name);

                    $("#category_description").html(rowData.description);

                    $("#CategoryImgUploadView").attr(
                        'src',
                        rowData.service_image_path ? "{{ asset('storage') }}/" + rowData.service_image_path :
                            "{{ asset('assets/img/image-upload.png') }}"
                    );
                    $("#flag").val("U");
                    $("#row_id").val(rowData.id);

                    $("#_method").val("PUT");

                    $("#category-image-required").hide();

                    $("#ServiceCategoryModalTitle").html('<i class="bi bi-folder-plus me-2"></i>Edit Service Category');
                    $("#UpdateButton").html('Update');
                });



            $('#ServiceCategoryDataTable tbody').on('click', '.delete-cetegory-btn',

                function() { // Delegated event handler for the button

                    var $row = $(this).closest('tr'); // Get the closest row element

                    var rowData = datatable.row($row).data(); // Get the entire row data object





                    Swal.fire({

                        title: 'Are you sure?',

                        text: "Are you sure to delete Service category?",

                        icon: 'warning',

                        showCancelButton: true,

                        confirmButtonText: 'Yes, confirm!',

                        customClass: {

                            confirmButton: 'btn btn-primary me-3',

                            cancelButton: 'btn btn-label-secondary'

                        }

                    }).then((result) => {

                        if (result.isConfirmed) {



                            var categoryId = rowData.id;

                            var data = {

                                'categoryId': categoryId

                            }



                            $.ajaxSetup({

                                headers: {

                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')

                                }

                            });



                            LoaderShow()



                            $.ajax({

                                type: "DELETE",

                                url: "{{ route('admin.service-category.destroy', ['shop' => request()->route('shop'), 'service_category' => '__CATEGORY_ID__']) }}"
                                    .replace('__CATEGORY_ID__', encodeURIComponent(categoryId)),
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





            $('#ServiceCategoryModal').on('hidden.bs.modal', function() {
                $("#_method").val("POST");

                $("#flag").val("A");
                $("#row_id").val("");
                $("#service-category-form")[0].reset();
                $("#CategoryImgUploadView").attr("src", "{{ asset('assets/img/image-upload.png') }}");
                validator.resetForm();
                $("#category-image-required").show();
                $("#service-category-form").find(".is-invalid").removeClass("is-invalid");
                $("#ServiceCategoryModalTitle").html('<i class="bi bi-folder-plus me-2"></i>Add Service Category');
                $("#UpdateButton").text("Save");
            });


        });







        let validator = $("#service-category-form").validate({
            ignore: ":hidden:not(#service_image)",
            rules: {
                category_nm: {

                    required: true

                },

                service_image: {
                    required: function() {
                        return $("#flag").val() === "A";
                    },
                    extension: "jpg|jpeg|png|gif|webp"
                },
            },
            messages: {
                category_nm: {
                    required: "Please Enter Category Name"
                },
                service_image: {
                    required: "Please upload a category image.",
                    extension: "Please upload a valid image file."
                },
            },

            errorElement: "div",

            errorPlacement: function(error, element) {

                error.addClass("invalid-feedback");

                if (element.attr("id") === "service_image") {
                    error.insertAfter(element.prev(".category-image-upload"));
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


        $("#service_image").on("change", function() {
            $(this).valid();
        });

        // ✅ Final submission with AJAX
        $("#service-category-form").submit(function(e) {

            e.preventDefault();



            if (!$("#service-category-form").valid()) {

                return;

            }



            let form = $(this)[0]; // ✅ Native DOM element

            let formData = new FormData(form); // ✅ Handles text + file inputs

            $.ajaxSetup({

                headers: {

                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')

                }

            });





            var flag = $("#flag").val();



            var id = $("#row_id").val();

            var url = "";

            if (flag == "A") {

                url = "{{ route('admin.service-category.store') }}";

            } else {

                url = "{{ route('admin.service-category.update', ['shop' => request()->route('shop'), 'service_category' => '__CATEGORY_ID__']) }}"
                    .replace('__CATEGORY_ID__', encodeURIComponent(id));
            }


            LoaderShow()



            $.ajax({

                type: "POST",

                url: url,

                data: formData,

                processData: false, // ✅ Important for FormData

                contentType: false, // ✅ Important for FormData

                success: function(response) {

                    LoaderHide()

                    if (response.status === 1) {

                        $("#ServiceCategoryModal").modal('hide');

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

                    LoaderHide()

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
                            target: document.getElementById('ServiceCategoryModal'),
                            showConfirmButton: true
                        });
                        return;
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: (xhr.responseJSON && xhr.responseJSON.message) ?
                            xhr.responseJSON.message : 'Something went wrong. Please try again.',
                        target: document.getElementById('ServiceCategoryModal'),
                        showConfirmButton: true
                    });
                }
            });

        });


        function category_image_show(input, imageId) {
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

