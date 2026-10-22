@extends('admin.layout.layout')
@section('content')
    <script>
        page_active_id = "Gallary";
    </script>
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="bc-page-header">
            <div>
                <h4>Gallery</h4>
                <p>Manage website gallery images</p>
            </div>
        </div>

        <div class="card rounded-3 shadow-sm">
            <div class="card-header p-4">
                <div class="row">
                    <div class="col-lg-6"></div>
                    @can('Gallery-Add')
                    <div class="col-lg-6" style="text-align: right;">
                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#GalleryModal">
                            <i class="bi bi-plus-lg"></i> Add Image
                        </button>
                    </div>
                    @endcan
                </div>
            </div>
            <div class="card-body table-responsive p-4">
                <table id="GalleryDataTable" class="table table-hover">
                    <thead>
                    <tr>
                        <th>Sr. NO.</th>
                        <th>Image</th>
{{--                        <th>Description</th>--}}
                        <th>Action</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="GalleryModal" tabindex="-1">
        <div class="modal-dialog">
            <form id="gallery-form" class="modal-content" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-image me-2"></i>Upload Gallery Image</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label class="form-label" for="gallery_image">Select Images (up to 20)</label>
                    <input type="file" id="gallery_image" name="gallery_images[]" class="form-control mb-3" accept="image/*" multiple required>
                    <div id="gallery-preview-wrap" class="d-none d-flex flex-wrap gap-2">
                    </div>
                    <input type="hidden" id="flag" name="flag" value="A">
                    <input type="hidden" id="row_id" name="row_id">
                    <input type="hidden" id="_method" name="_method" value="POST">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="UpdateButton">
                        <i class="bi bi-upload me-1"></i> Upload
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            var datatable = $('#GalleryDataTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.gallery.gettabledata') }}",
                    type: "POST",
                    data: {_token: '{{ csrf_token() }}'}
                },
                columns: [
                    { data: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'image' },
                    { data: 'action', orderable: false, searchable: false }
                ]
            });

            function resetGalleryForm() {
                $('#gallery-form')[0].reset();
                $('#flag').val('A');
                $('#row_id').val('');
                $('#_method').val('POST');
                $('#gallery-preview-wrap').empty().addClass('d-none');
            }

            $('#GalleryModal').on('show.bs.modal', function() {
                resetGalleryForm();
            });

            $('#gallery_image').on('change', function() {
                var files = Array.from(this.files || []);
                var preview = $('#gallery-preview-wrap').empty();
                if (!files.length) {
                    preview.addClass('d-none');
                    return;
                }
                if (files.length > 20) {
                    this.value = '';
                    preview.addClass('d-none');
                    Swal.fire('Too many images', 'Select no more than 20 images at a time.', 'warning');
                    return;
                }
                preview.removeClass('d-none');
                files.forEach(function(file) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        $('<img>', {
                            src: e.target.result,
                            alt: 'Image preview',
                            class: 'rounded border',
                            css: { width: '100px', height: '100px', objectFit: 'cover' }
                        }).appendTo(preview);
                    };
                    reader.readAsDataURL(file);
                });
            });

            // Submit form
            $("#gallery-form").submit(function(e){
                e.preventDefault();
                let formData = new FormData(this);
                let flag = $("#flag").val();
                let id = $("#row_id").val();
                let url = (flag == "A") ? "{{ route('admin.gallery.store') }}" : "{{ url('admin/gallery') }}/" + id;

                $.ajax({
                    type: "POST",
                    url: url,
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(res){
                        if(res.status === 1){
                            $('#GalleryModal').modal('hide');
                            datatable.ajax.reload();
                            Swal.fire('Success', res.message, 'success');
                        } else {
                            Swal.fire('Warning', res.message || 'Unable to upload image', 'warning');
                        }
                    },
                    error: function(xhr) {
                        var msg = 'Unable to upload image';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                            msg = Object.values(xhr.responseJSON.errors).flat().join('\n');
                        }
                        Swal.fire('Error', msg, 'error');
                    }
                });
            });

            // Delete
            $('#GalleryDataTable').on('click', '.delete-gallery-btn', function(){
                let rowData = datatable.row($(this).closest('tr')).data();
                Swal.fire({
                    title: 'Delete?',
                    text: "Delete this image?",
                    icon: 'warning',
                    showCancelButton: true,
                }).then((result)=>{
                    if(result.isConfirmed){
                        $.ajax({
                            type: "DELETE",
                            url: "{{ url('admin/gallery') }}/" + rowData.id,
                            data: {_token: '{{ csrf_token() }}'},
                            success: function(res){
                                datatable.ajax.reload();
                                Swal.fire('Deleted', res.message, 'success');
                            }
                        });
                    }
                });
            });
        });
    </script>

@endpush
