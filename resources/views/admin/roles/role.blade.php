@extends('admin.layout.layout')
@section('content')
    <script>
        page_active_id = "Role-Roles";
    </script>
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="bc-page-header">
            <div>
                <h4>Roles</h4>
                <p>Manage user roles and permissions</p>
            </div>
        </div>

        <!-- Product List Table -->
        <div class="card rounded-3 shadow-sm">
            <div class="card-header p-4">
                <div class="row">
                    <div class="col-lg-6"></div>
                    <div class="col-lg-6" style="text-align: right;">
                        <button type="button" class="btn btn-sm btn-primary" data-bs-target="#addRoleModal"
                            data-bs-toggle="modal"><i class="bi bi-plus-lg"></i> Add New Role</button>
                    </div>
                </div>
            </div>
            <div class="card-body table-responsive p-4">
                <table id="RoleDataTable" class="table table-hover">
                    <thead class="border-top">
                        <tr>
                            <th>Sr. NO.</th>
                            <th>Type Name</th>
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

    <div class="modal fade" id="addRoleModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-simple ">
            <form id="roles-form" onsubmit="return false;">
                <div class="modal-content">
                    <div class="modal-body">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        <div class="text-center mb-6">
                            <h4 class="role-title mb-2">Add New Role</h4>
                            <p>Set role permissions</p>
                        </div>
                        <!-- Add role form -->
                        <div class="row g-6">
                            <div class="col-12">
                                <label class="form-label" for="modalRoleName">Role Name</label>
                                <input type="text" id="role_nm" name="role_nm" class="form-control"
                                    placeholder="Enter a role name" tabindex="-1" />
                            </div>
                            <div class="col-12">
                                <h5 class="mb-6">Role Permissions</h5>
                                <!-- Permission table -->
                                <div class="table-responsive">
                                    <table class="table table-flush-spacing mb-0">
                                        <tbody>
                                            <tr>
                                                <td class="text-nowrap fw-medium text-heading">
                                                    Administrator Access
                                                    <i class="bi bi-info-circle" data-bs-toggle="tooltip"
                                                        data-bs-placement="top"
                                                        title="Allows a full access to the system"></i>
                                                </td>
                                                <td>
                                                    <div class="d-flex justify-content-end">
                                                        <div class="form-check mb-0">
                                                            <input class="form-check-input" type="checkbox"
                                                                id="selectAll" />
                                                            <label class="form-check-label" for="selectAll"> Select All
                                                            </label>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>

                                            @foreach ($PermissionData as $group => $permissions)
                                                <tr>
                                                    <td class="text-nowrap fw-medium text-heading">{{ $group }}</td>
                                                    <td>
                                                        <div class="d-flex flex-wrap gap-3">
                                                            @foreach ($permissions as $permission)
                                                                <div class="form-check mb-0 me-4 me-lg-12">
                                                                    <input class="form-check-input permission-checkbox"
                                                                        type="checkbox" name="permissions[]"
                                                                        value="{{ $permission->id }}"
                                                                        id="perm_{{ $permission->id }}">
                                                                    <label class="form-check-label"
                                                                        for="perm_{{ $permission->id }}">
                                                                        {{ ucfirst(explode('-', $permission->name)[1]) }}
                                                                    </label>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <!-- Permission table -->
                            </div>
                            <div class="col-12 text-center">
                                <input type="hidden" id="_method" name="_method" value="POST">
                                <input type="hidden" id="flag" name="flag" value="A">
                                <input type="hidden" id="row_id" name="row_id">
                                <button type="submit" class="btn btn-primary me-3">Submit</button>
                                <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal"
                                    aria-label="Close">
                                    Cancel
                                </button>
                            </div>
                        </div>
                        <!--/ Add role form -->
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection


@push('script')
    <script>
        $(document).ready(function() {
            var datatable = $('#RoleDataTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.roles.gettabledata') }}",
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
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }

                ]
            });


            $('#RoleDataTable tbody').on('click', '.view-details-btn',
                function() { // Delegated event handler for the button
                    var $row = $(this).closest('tr'); // Get the closest row element
                    var rowData = datatable.row($row).data(); // Get the entire row data object

                    $("#addRoleModal").modal("show");
                    $("#role_nm").val(rowData.name);
                    $("#flag").val("U");
                    $("#row_id").val(rowData.id);
                    $("#_method").val("PUT");

                    $("input[name='permissions[]']").prop("checked", false);

                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });

                    // Fetch role's permissions via AJAX
                    $.ajax({
                        url: "{{ route('admin.roles.permissions', ['id' => '__ROLE_ID__']) }}".replace(
                            '__ROLE_ID__', rowData.id),
                        type: 'POST',
                        success: function(response) {
                            if (response.status === 1) {
                                let rolePermissionIds = response
                                    .permissions.map(Number);

                                $("input[name='permissions[]']").each(function() {
                                    let permissionId = parseInt($(this).val());
                                    if (rolePermissionIds.includes(permissionId)) {
                                        $(this).prop("checked", true);
                                    } else {
                                        $(this).prop("checked",
                                            false); // Optional: uncheck if not in list
                                    }
                                });
                            }
                        },
                        error: function() {
                            console.log("Failed to fetch role permissions.");
                        }
                    });
                });

            $('#RoleDataTable tbody').on('click', '.delete-role-btn', function() {
                var rowData = datatable.row($(this).closest('tr')).data();
                if (!rowData) {
                    return;
                }

                Swal.fire({
                    icon: 'warning',
                    title: 'Delete role?',
                    text: 'Users assigned to this role will lose its permissions.',
                    showCancelButton: true,
                    confirmButtonText: 'Delete',
                    confirmButtonColor: '#d33'
                }).then(function(result) {
                    if (!result.isConfirmed) {
                        return;
                    }

                    $.ajax({
                        url: "{{ route('admin.roles.destroy', ['role' => '__ROLE_ID__']) }}"
                            .replace('__ROLE_ID__', rowData.id),
                        type: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.status === 1) {
                                Swal.fire('Deleted', response.message, 'success');
                                datatable.ajax.reload(null, false);
                            } else {
                                Swal.fire('Error', response.message || 'Failed to delete role.', 'error');
                            }
                        },
                        error: function(xhr) {
                            const message = xhr.responseJSON && xhr.responseJSON.message
                                ? xhr.responseJSON.message
                                : 'Failed to delete role. Please try again.';
                            Swal.fire('Error', message, 'error');
                        }
                    });
                });
            });

            $('#addRoleModal').on('hidden.bs.modal', function() {
                $("#_method").val("POST");
                $("#flag").val("A");
                $("#row_id").val("");
                $("#roles-form")[0].reset();
                validator.resetForm();
                $('#selectAll').prop('checked', false);
            });

        });



        let validator = $("#roles-form").validate({
            rules: {
                role_nm: {
                    required: true
                },
            },
            messages: {
                role_nm: {
                    required: "Please Enter Role Name"
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

        // ✅ Final submission with AJAX
        $("#roles-form").submit(function(e) {
            e.preventDefault();

            if (!$("#roles-form").valid()) {
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
                url = "{{ route('admin.roles.store') }}";
            } else {
                url = "{{ route('admin.roles.update', ['role' => '__ROLE_ID__']) }}".replace(
                    '__ROLE_ID__', id);
            }

            LoaderShow()

            $.ajax({
                type: "POST",
                url: url,
                data: formData,
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
                        form[0].reset();
                        validator.resetForm();

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
                },
                error: function(xhr) {
                    LoaderHide()
                    const message = xhr.responseJSON && xhr.responseJSON.message
                        ? xhr.responseJSON.message
                        : 'Failed to update role. Please try again.';
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: message
                    });
                }
            });
        });

        $(document).ready(function() {
            $('#selectAll').on('change', function() {
                $('.permission-checkbox').prop('checked', this.checked);
            });

            $('.permission-checkbox').on('change', function() {
                const allChecked = $('.permission-checkbox').length === $('.permission-checkbox:checked')
                    .length;
                $('#selectAll').prop('checked', allChecked);
            });
        });
    </script>
@endpush
