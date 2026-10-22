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
        page_active_id = "Product";
    </script>
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="bc-page-header">
            <div>
                <h4>Products</h4>
                <p>Manage retail products, pricing, and inventory</p>
            </div>
        </div>

        <!-- Product List Table -->
        <div class="card rounded-3 shadow-sm">
            <div class="card-header p-4">
                <div class="row">
                    <div class="col-lg-6"></div>
                    @can('Product-Add')
                        <div class="col-lg-6" style="text-align: right;">
                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                data-bs-target="#ProductModal"><i class="bi bi-plus-lg"></i> Add</button>
                        </div>
                    @endcan
                </div>
            </div>
            <hr class="my-1">
            <div class="card-body table-responsive p-4">
                <table id="ProductDataTable" class="table table-hover">
                    <thead class="border-top">
                        <tr>
                            <th>Sr. NO.</th>
                            <th>Product</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Commission</th>
                            <th>Stock</th>
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
    <div class="modal fade" id="ProductModal" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog">
            <form id="product-form" class="modal-content" onsubmit="return false;">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="ProductModalTitle"><i class="bi bi-box-seam me-2"></i>Add Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-12 mb-3">
                            <label for="nameBackdrop" class="form-label mb-2">Product Name <span style="color:red;">*</span>
                            </label>
                            <input type="text" id="product_nm" name="product_nm" class="form-control"
                                placeholder="Enter Product Name" />
                        </div>


                        <div class="col-lg-12 col-md-12 col-12 mb-3">
                            <label for="nameBackdrop" class="form-label mb-2">Category <span
                                    style="color:red;">*</span></label>
                            <select name="category_id" class="form-select select2" id="category_id">
                                <option value="" selected>-Select Category-</option>
                                @foreach ($ServiceCategory as $row)
                                    <option value="{{ $row->id }}">{{ $row->category_name }}</option>
                                @endforeach
                            </select>
                        </div>



                        <div class="col-lg-6 col-md-12 col-12 mb-3">
                            <label for="nameBackdrop" class="form-label mb-2">Price ({{ $shopCurrencySymbol ?? '₹' }}) <span
                                    style="color:red;">*</span></label>
                            <input type="text" id="price" name="price" class="form-control"
                                placeholder="Enter Service Name" />
                        </div>

                        <div class="col-lg-6 col-md-12 col-12 mb-3">
                            <label for="nameBackdrop" class="form-label mb-2">Stock <span
                                    style="color:red;">*</span></label>
                            <input type="text" id="stock_qty" name="stock_qty" class="form-control"
                                placeholder="Enter Stock" />
                        </div>

                        <div class="col-lg-6 col-md-12 col-12 mb-3">
                            <label for="nameBackdrop" class="form-label mb-2">Commission (%)</label>
                            <input type="text" id="commission" name="commission" class="form-control"
                                placeholder="Enter Commission" />
                        </div>

                        <div class="col-lg-6 col-md-12 col-12 mb-3">
                            <label for="nameBackdrop" class="form-label mb-2">Status <span
                                    style="color:red;">*</span></label>
                            <select name="status" class="form-select" id="status">
                                <option value="1" selected>Active</option>
                                <option value="0">Deactive</option>
                            </select>
                        </div>

                        <div class="col-lg-12 col-md-12 col-12 mb-3">
                            <div class="mb-1">
                                <label for="product_image" class="form-label fw-semibold">Service Image <span
                                        style="color:red;">*</span><span style="color:red;">*</span></label>
                                <div class="service-image-upload"
                                    onclick="document.getElementById('product_image').click();">

                                    <div class="text-center">
                                        <img id="ProductImgUploadView" src="{{ asset('assets/img/image-upload.png') }}"
                                            style="max-width: 30%;" alt="">
                                    </div>
                                </div>

                                <input type="file" id="product_image" name="product_image"
                                    accept="image/png, image/jpeg, image/gif" style="visibility: hidden"
                                    onchange="image_show(0,this, 'ProductImgUploadView')" />
                            </div>
                        </div>

                        <div class="col-lg-12 col-md-12 col-12 mb-3">
                            <label for="nameBackdrop" class="form-label mb-2">Product Description</label>
                            <textarea name="description" id="description" class="form-control" cols="30" rows="6"
                                placeholder="Enter Product Description"></textarea>
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
            var datatable = $('#ProductDataTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.product.gettabledata') }}",
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
                        data: 'product_name',
                        name: 'product_name'
                    },
                    {
                        data: 'category_name',
                        name: 'category_name'
                    },
                    {
                        data: 'product_price',
                        name: 'product_price'
                    },
                    {
                        data: 'product_commission',
                        name: 'product_commission'
                    },
                    {
                        data: 'stock',
                        name: 'stock'
                    },
                    {
                        data: 'product_status',
                        name: 'product_status'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }

                ]
            });


            $('#ProductDataTable tbody').on('click', '.view-details-btn',
                function() { // Delegated event handler for the button
                    var $row = $(this).closest('tr'); // Get the closest row element
                    var rowData = datatable.row($row).data(); // Get the entire row data object

                    $("#ProductModal").modal('show');



                    $("#category_id").val(rowData.category_id).trigger('change');

                    // Set input values
                    $("#product_nm").val(rowData.product_name);
                    $("#price").val(rowData.price);
                    $("#commission").val(rowData.commission);
                    $("#stock_qty").val(rowData.stock);


                    // Set checkbox states (assuming status and tip_status are 1 or 0)
                    $("#active_status").prop('checked', rowData.status == 1);
                    $("#eligible_for_tip").prop('checked', rowData.tip_status == 1);

                    // Set textarea value
                    $("#description").val(rowData.description);

                    let imgPath = rowData.product_image_url;
                    // Reset file input (cannot set value for file inputs due to security)
                    $("#ProductImgUploadView").attr('src', imgPath);

                    $("#status").val(rowData.status).trigger('change');


                    $("#flag").val("U");
                    $("#row_id").val(rowData.id);
                    $("#_method").val("PUT");

                    $("#UpdateButton").html('Update');
                });

            $('#ProductDataTable tbody').on('click', '.delete-product-btn',
                function() { // Delegated event handler for the button
                    var $row = $(this).closest('tr'); // Get the closest row element
                    var rowData = datatable.row($row).data(); // Get the entire row data object


                    Swal.fire({
                        title: 'Are you sure?',
                        text: "Are you sure to delete Product?",
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
                                url: "{{ url('admin/product') }}/" + id,
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




            $('#ProductModalDataTable').on('hidden.bs.modal', function() {
                $("#_method").val("POST");
            });

        });



        let validator = $("#product-form").validate({
            rules: {
                product_nm: {
                    required: true
                },
                category_id: {
                    required: true
                },
                price: {
                    required: true,
                    number: true,
                    min: 0
                },
                stock_qty: {
                    required: true,
                    digits: true,
                    min: 0
                },
                commission: {
                    required: false,
                    number: true,
                    min: 0
                },
                status: {
                    required: true
                },
                product_image: {
                    required: false, // set true if mandatory
                    extension: "jpg|jpeg|png|gif"
                },
                description: {
                    required: false
                }
            },
            messages: {
                product_nm: {
                    required: "Please enter Product Name"
                },
                category_id: {
                    required: "Please select a Category"
                },
                price: {
                    required: "Please enter Price",
                    number: "Price must be a number",
                    min: "Price cannot be negative"
                },
                stock_qty: {
                    required: "Please enter Stock quantity",
                    digits: "Stock must be an integer",
                    min: "Stock cannot be negative"
                },
                commission: {
                    number: "Commission must be a number",
                    min: "Commission cannot be negative"
                },
                status: {
                    required: "Please select Status"
                },
                product_image: {
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

        // Ajax form submission with validation

        $("#product-form").submit(function(e) {
            e.preventDefault();

            if (!$("#product-form").valid()) {
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
                "{{ route('admin.product.store') }}" :
                "{{ url('admin/product') }}/" + id;

            LoaderShow();

            $.ajax({
                type: "POST",
                url: url,
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    LoaderHide();
                    $("#ProductModal").modal('hide');
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



        function image_show(id, input, img_id) {

            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById(img_id).src = e.target.result;

                };
                reader.readAsDataURL(input.files[0]);

            }
        }
    </script>
@endpush
