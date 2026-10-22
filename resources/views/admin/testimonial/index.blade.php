@extends('admin.layout.layout')
@section('content')
    <script>
        page_active_id = "Testimonials";
    </script>
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="bc-page-header">
            <div>
                <h4>Testimonials</h4>
                <p>Manage homepage “What Clients Say” reviews</p>
            </div>
        </div>

        <div class="card rounded-3 shadow-sm">
            <div class="card-header p-4">
                <div class="row">
                    <div class="col-lg-6"></div>
                    @can('Testimonial-Add')
                        <div class="col-lg-6" style="text-align: right;">
                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                data-bs-target="#TestimonialModal">
                                <i class="bi bi-plus-lg"></i> Add Testimonial
                            </button>
                        </div>
                    @endcan
                </div>
            </div>
            <div class="card-body table-responsive p-4">
                <table id="TestimonialDataTable" class="table table-hover">
                    <thead>
                        <tr>
                            <th>Sr. NO.</th>
                            <th>Photo</th>
                            <th>Name</th>
                            <th>Title</th>
                            <th>Review</th>
                            <th>Rating</th>
                            <th>Order</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="TestimonialModal" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <form id="testimonial-form" class="modal-content" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="TestimonialModalTitle"><i class="bi bi-chat-quote me-2"></i>Add Testimonial</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Client Name <span class="text-danger">*</span></label>
                            <input type="text" name="client_name" id="client_name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Client Title</label>
                            <input type="text" name="client_role" id="client_role" class="form-control"
                                placeholder="Regular Client">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Review <span class="text-danger">*</span></label>
                            <textarea name="review" id="review" class="form-control" rows="4" required></textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Rating</label>
                            <select name="rating" id="rating" class="form-select">
                                <option value="5">5 Stars</option>
                                <option value="4">4 Stars</option>
                                <option value="3">3 Stars</option>
                                <option value="2">2 Stars</option>
                                <option value="1">1 Star</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Display Order</label>
                            <input type="number" name="sort_order" id="sort_order" class="form-control" min="0" value="0">
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" id="status" name="status" value="1" checked>
                                <label class="form-check-label" for="status">Show on website</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Client Photo</label>
                            <input type="file" name="client_image" id="client_image" class="form-control" accept="image/*">
                            <div id="testimonial-preview-wrap" class="text-center mt-3 d-none">
                                <img id="testimonial-preview" src="" alt="Preview" class="rounded-circle border"
                                    style="width:80px;height:80px;object-fit:cover;">
                            </div>
                        </div>
                    </div>
                    <input type="hidden" id="flag" name="flag" value="A">
                    <input type="hidden" id="row_id" name="row_id">
                    <input type="hidden" id="_method" name="_method" value="POST">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="UpdateButton">Save</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            var datatable = $('#TestimonialDataTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.testimonial.gettabledata') }}",
                    type: "POST",
                    data: { _token: '{{ csrf_token() }}' }
                },
                columns: [
                    { data: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'image', orderable: false, searchable: false },
                    { data: 'client_name', name: 'client_name' },
                    { data: 'client_role', name: 'client_role' },
                    { data: 'review_short', name: 'review' },
                    { data: 'rating_stars', name: 'rating' },
                    { data: 'sort_order', name: 'sort_order' },
                    { data: 'status_badge', name: 'status' },
                    { data: 'action', orderable: false, searchable: false }
                ]
            });

            function resetForm() {
                $('#testimonial-form')[0].reset();
                $('#flag').val('A');
                $('#row_id').val('');
                $('#_method').val('POST');
                $('#status').prop('checked', true);
                $('#rating').val('5');
                $('#sort_order').val(0);
                $('#testimonial-preview').attr('src', '');
                $('#testimonial-preview-wrap').addClass('d-none');
                $('#TestimonialModalTitle').html('<i class="bi bi-chat-quote me-2"></i>Add Testimonial');
                $('#UpdateButton').text('Save');
            }

            $('#TestimonialModal').on('show.bs.modal', function(e) {
                if ($(e.relatedTarget).hasClass('btn-primary')) {
                    resetForm();
                }
            });

            $('#client_image').on('change', function() {
                var file = this.files && this.files[0];
                if (!file) return;
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#testimonial-preview').attr('src', e.target.result);
                    $('#testimonial-preview-wrap').removeClass('d-none');
                };
                reader.readAsDataURL(file);
            });

            $('#TestimonialDataTable').on('click', '.edit-testimonial-btn', function() {
                var rowData = datatable.row($(this).closest('tr')).data();
                resetForm();
                $('#client_name').val(rowData.client_name);
                $('#client_role').val(rowData.client_role);
                $('#review').val(rowData.review);
                $('#rating').val(rowData.rating);
                $('#sort_order').val(rowData.sort_order);
                $('#status').prop('checked', rowData.status == 1);
                $('#flag').val('U');
                $('#row_id').val(rowData.id);
                $('#_method').val('PUT');
                if (rowData.image_url) {
                    $('#testimonial-preview').attr('src', rowData.image_url);
                    $('#testimonial-preview-wrap').removeClass('d-none');
                }
                $('#TestimonialModalTitle').html('<i class="bi bi-chat-quote me-2"></i>Edit Testimonial');
                $('#UpdateButton').text('Update');
                $('#TestimonialModal').modal('show');
            });

            $('#testimonial-form').submit(function(e) {
                e.preventDefault();
                var formData = new FormData(this);
                if (!$('#status').is(':checked')) {
                    formData.set('status', '0');
                }
                var flag = $('#flag').val();
                var id = $('#row_id').val();
                var url = (flag === 'A')
                    ? "{{ route('admin.testimonial.store') }}"
                    : "{{ route('admin.testimonial.update', ['shop' => request()->route('shop'), 'id' => '__TESTIMONIAL_ID__']) }}"
                        .replace('__TESTIMONIAL_ID__', encodeURIComponent(id));

                $.ajax({
                    type: 'POST',
                    url: url,
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        if (res.status === 1) {
                            $('#TestimonialModal').modal('hide');
                            datatable.ajax.reload();
                            Swal.fire('Success', res.message, 'success');
                        } else {
                            Swal.fire('Warning', res.message || 'Unable to save', 'warning');
                        }
                    },
                    error: function(xhr) {
                        var msg = 'Unable to save testimonial';
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            msg = Object.values(xhr.responseJSON.errors).flat().join('\n');
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        Swal.fire('Error', msg, 'error');
                    }
                });
            });

            $('#TestimonialDataTable').on('click', '.delete-testimonial-btn', function() {
                var rowData = datatable.row($(this).closest('tr')).data();
                Swal.fire({
                    title: 'Delete?',
                    text: 'Delete this testimonial?',
                    icon: 'warning',
                    showCancelButton: true
                }).then(function(result) {
                    if (!result.isConfirmed) return;
                    $.ajax({
                        type: 'DELETE',
                        url: "{{ route('admin.testimonial.destroy', ['shop' => request()->route('shop'), 'id' => '__TESTIMONIAL_ID__']) }}"
                            .replace('__TESTIMONIAL_ID__', encodeURIComponent(rowData.id)),
                        data: { _token: '{{ csrf_token() }}' },
                        success: function(res) {
                            datatable.ajax.reload();
                            Swal.fire('Deleted', res.message, 'success');
                        }
                    });
                });
            });
        });
    </script>
@endpush
