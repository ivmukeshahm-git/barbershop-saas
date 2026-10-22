@push('css')
    <style>
        .btn-light.active {
            background-color: #fff;
            border-color: #000;
            color: #000;
            box-shadow: 0 0 0 0.25rem rgb(0 0 0 / 0.25);
        }
    </style>
@endpush


@extends('admin.layout.layout')
@section('content')
    @php
        use Illuminate\Support\Facades\Auth;
        $authUser = Auth::user();
    @endphp
    <script>
        page_active_id = "Settings";
    </script>
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="bc-page-header">
            <div>
                <h4>Settings</h4>
                <p>Manage business settings, payments, and account security</p>
            </div>
        </div>

        <!-- Tabs Navigation -->
        <div class="mb-4 d-flex flex-wrap gap-2">
            <button type="button" class="btn btn-light border fw-semibold tab-btn active" id="tabGeneralSettings">
                <i class="bi bi-sliders me-1"></i> General Settings
            </button>
            <button type="button" class="btn btn-light border fw-semibold tab-btn" id="tabContactDetails">
                <i class="bi bi-geo-alt me-1"></i> Contact Details
            </button>
            <button type="button" class="btn btn-light border fw-semibold tab-btn" id="tabPaymentOptions">
                <i class="bi bi-credit-card me-1"></i> Payment Options
            </button>
            {{--
            <button type="button" class="btn btn-light border fw-semibold tab-btn" id="tabChangePassword">
                <i class="bi bi-key me-1"></i> Change Password
            </button>
            --}}
        </div>

        <!-- General Settings Section -->
        <div class="card rounded-3 shadow-sm p-4 " id="sectionGeneralSettings">
            <h5 class="mb-4 fw-semibold">General Business Settings</h5>
            <form id="general-form" onsubmit="return false;">
                <input type="hidden" name="setting_section" value="general">
                <div class="row g-4">
                    <!-- Left Column -->
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-500">Service Reminder Day</label>
                            <input type="number" class="form-control" id="service_reminder_day" name="service_reminder_day" placeholder="Enter Day" value="{{ $Setting->service_reminder_day }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-500">Service Default Duration (Minutes)</label>
                            <input type="number" class="form-control" id="service_default_duration" name="service_default_duration" min="1" placeholder="Enter Duration" value="{{ $Setting->service_default_duration ?? 30 }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-500" for="booking_code_prefix">Booking Code Prefix</label>
                            <input type="text" class="form-control" id="booking_code_prefix" name="booking_code_prefix"
                                maxlength="10" pattern="[A-Za-z0-9]{2,10}" required
                                value="{{ old('booking_code_prefix', $Setting->booking_code_prefix ?? 'MHS') }}">
                            <small class="text-muted">Use 2-10 letters or numbers. New booking codes will use this prefix.</small>
                        </div>

                        <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" id="invoice_mail_send_status" name="invoice_mail_send_status" {{ $Setting->invoice_send_sts == 1 ? 'checked' : '' }}/>
                        <label class="form-check-label" for="invoice_mail_send_status"
                          >Invoice Mail Send</label
                        >
                      </div>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-check2 me-1"></i> Update
                    </button>
                </div>
            </form>
        </div>

        <!-- Contact Details Section -->
        <div class="card rounded-3 shadow-sm p-4 d-none" id="sectionContactDetails">
            <h5 class="mb-1 fw-semibold">Contact Details</h5>
            <p class="text-muted mb-4">These details appear in the website footer, contact page, header, and booking pages.</p>
            <form id="contact-form" onsubmit="return false;">
                <input type="hidden" name="setting_section" value="contact">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label">Footer About Text</label>
                        <textarea class="form-control" name="footer_about" id="footer_about" rows="3" placeholder="Short about text for the footer">{{ old('footer_about', $Setting->footer_about) }}</textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Address <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="contact_address" id="contact_address" rows="2" required placeholder="Shop address">{{ old('contact_address', $Setting->contact_address) }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Phone <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="contact_phone" id="contact_phone" required
                            value="{{ old('contact_phone', $Setting->contact_phone) }}" placeholder="+1 (587) 555-2489">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">WhatsApp Number</label>
                        <input type="text" class="form-control" name="whatsapp" id="whatsapp"
                            value="{{ old('whatsapp', $Setting->whatsapp) }}" placeholder="15875552489 (country code + number, digits only)">
                        <small class="text-muted">Used for the floating WhatsApp button. Digits with country code, no +.</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Info Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" name="contact_email" id="contact_email" required
                            value="{{ old('contact_email', $Setting->contact_email) }}" placeholder="info@bluecrownbarbers.com">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Support Email</label>
                        <input type="email" class="form-control" name="support_email" id="support_email"
                            value="{{ old('support_email', $Setting->support_email) }}" placeholder="support@bluecrownbarbers.com">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">GSTIN</label>
                        <input type="text" class="form-control" name="gstin" id="gstin" maxlength="15"
                            value="{{ old('gstin', $Setting->gstin) }}" placeholder="15-character GSTIN">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">SAC Code</label>
                        <input type="text" class="form-control" name="sac_code" id="sac_code" maxlength="20"
                            value="{{ old('sac_code', $Setting->sac_code) }}" placeholder="Service Accounting Code">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Website URL</label>
                        <input type="url" class="form-control" name="website_url" id="website_url"
                            value="{{ old('website_url', $Setting->website_url) }}" placeholder="https://www.bluecrownbarbers.com">
                    </div>
                    <div class="col-12"><hr class="my-2"><h6 class="fw-semibold mb-0">Social Media Links</h6></div>
                    <div class="col-md-6">
                        <label class="form-label">Facebook</label>
                        <input type="url" class="form-control" name="facebook_url" id="facebook_url"
                            value="{{ old('facebook_url', $Setting->facebook_url) }}" placeholder="https://www.facebook.com/...">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Instagram</label>
                        <input type="url" class="form-control" name="instagram_url" id="instagram_url"
                            value="{{ old('instagram_url', $Setting->instagram_url) }}" placeholder="https://www.instagram.com/...">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">TikTok</label>
                        <input type="url" class="form-control" name="tiktok_url" id="tiktok_url"
                            value="{{ old('tiktok_url', $Setting->tiktok_url) }}" placeholder="https://www.tiktok.com/@...">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">YouTube</label>
                        <input type="url" class="form-control" name="youtube_url" id="youtube_url"
                            value="{{ old('youtube_url', $Setting->youtube_url) }}" placeholder="https://www.youtube.com/@...">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Twitter / X</label>
                        <input type="url" class="form-control" name="twitter_url" id="twitter_url"
                            value="{{ old('twitter_url', $Setting->twitter_url) }}" placeholder="https://twitter.com/...">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">LinkedIn</label>
                        <input type="url" class="form-control" name="linkedin_url" id="linkedin_url"
                            value="{{ old('linkedin_url', $Setting->linkedin_url) }}" placeholder="https://www.linkedin.com/company/...">
                    </div>
                </div>
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-check2 me-1"></i> Update Contact Details
                    </button>
                </div>
            </form>
        </div>

        <!-- Payment Options Section (hidden by default) -->
        <div class="card rounded-3 shadow-sm p-4 d-none" id="sectionPaymentOptions">
            <h5 class="mb-4 fw-semibold">Payment Options</h5>
            <div class="mb-3 fw-semibold ps-1" style="font-size:1.08rem;">
                Custom Payment Methods
            </div>
            <div id="paymentMethodsList">
                @foreach ($PaymentMethod as $row)
                    <div class="d-flex align-items-center border rounded-3 bg-white px-3 py-3 mb-2">
                        <span class="flex-grow-1 fw-medium">{{ $row->name }}</span>
                        <button type="button" onclick="EditPaymentMethod({{ $row->id }},'{{ $row->name }}')"
                            class="btn btn-sm btn-icon btn-warning" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button type="button" onclick="DeletePaymentMethod({{ $row->id }})"
                            class="btn btn-sm btn-icon btn-danger ms-1" title="Delete">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                @endforeach
            </div>
            <button type="button" class="btn btn-outline-primary fw-semibold mt-2" id="addPaymentMethod"
                data-bs-toggle="modal" data-bs-target="#PaymentMethod">
                <i class="bi bi-plus-lg me-1"></i>Add Payment Method
            </button>
        </div>

        {{-- Change Password Section
        <div class="card rounded-3 shadow-sm p-4 d-none" id="sectionChangePassword">
            <h5 class="mb-1 fw-semibold">Change Password</h5>
            <p class="text-muted mb-4">Update your account password. Use at least 6 characters.</p>
            <form id="settings-change-password-form" onsubmit="return false;" class="col-lg-6">
                @csrf
                <div class="mb-3 bc-pass-wrap">
                    <label class="form-label" for="settings_current_password">Current Password</label>
                    <input type="password" class="form-control" id="settings_current_password" name="current_password"
                        autocomplete="current-password" placeholder="Enter current password" />
                    <button type="button" class="bc-toggle-pass" data-bc-toggle-pass="#settings_current_password"
                        aria-label="Show password"><i class="bi bi-eye"></i></button>
                </div>
                <div class="mb-3 bc-pass-wrap">
                    <label class="form-label" for="settings_new_password">New Password</label>
                    <input type="password" class="form-control" id="settings_new_password" name="new_password"
                        autocomplete="new-password" placeholder="Enter new password" />
                    <button type="button" class="bc-toggle-pass" data-bc-toggle-pass="#settings_new_password"
                        aria-label="Show password"><i class="bi bi-eye"></i></button>
                </div>
                <div class="mb-4 bc-pass-wrap">
                    <label class="form-label" for="settings_confirm_password">Confirm Password</label>
                    <input type="password" class="form-control" id="settings_confirm_password" name="confirm_password"
                        autocomplete="new-password" placeholder="Confirm new password" />
                    <button type="button" class="bc-toggle-pass" data-bc-toggle-pass="#settings_confirm_password"
                        aria-label="Show password"><i class="bi bi-eye"></i></button>
                </div>
                <input type="hidden" name="staff_row_id" value="{{ $authUser->id }}">
                <button type="submit" class="btn btn-primary px-4" id="settingsChangePasswordBtn">
                    <i class="bi bi-shield-lock me-1"></i> Update Password
                </button>
            </form>
        </div>
        --}}

    </div>
    <!-- / Content -->


    <!-- Modal -->
    <div class="modal fade" id="PaymentMethod" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog">
            <form id="payment-method-form" class="modal-content" onsubmit="return false;">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="JobCategoryTitle">Add Payment Method</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-12 mb-3">
                            <label for="nameBackdrop" class="form-label mb-2">Payment Method</label>
                            <input type="text" id="name" name="name" class="form-control"
                                placeholder="Enter Payment Method" />
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
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $("#general-form").submit(function(e) {
            e.preventDefault();

            if (!$("#general-form").valid()) {
                return;
            }

            let form = $(this);
            let formData = form.serialize();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            LoaderShow()

            $.ajax({
                type: "PUT",
                url: "{{ url('admin/setting') }}/" + 1,
                data: formData,
                success: function(response) {
                    LoaderHide()
                    $("#PaymentMethod").modal('hide');
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
                    console.log(xhr.responseText);
                    console.log(response.message);
                }
            });
        });

        $("#contact-form").submit(function(e) {
            e.preventDefault();

            let form = $(this);
            let formData = form.serialize();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            LoaderShow();

            $.ajax({
                type: "PUT",
                url: "{{ url('admin/setting') }}/" + 1,
                data: formData,
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
                        setTimeout(function() {
                            location.reload();
                        }, 800);
                    } else {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Warning',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 2500
                        });
                    }
                },
                error: function(xhr) {
                    LoaderHide();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Could not update contact details. Please try again.',
                        showConfirmButton: false,
                        timer: 2500
                    });
                    console.log(xhr.responseText);
                }
            });
        });




        document.addEventListener('DOMContentLoaded', function() {
            const generalTab = document.getElementById('tabGeneralSettings');
            const contactTab = document.getElementById('tabContactDetails');
            const paymentTab = document.getElementById('tabPaymentOptions');
            const passwordTab = document.getElementById('tabChangePassword');

            const generalSection = document.getElementById('sectionGeneralSettings');
            const contactSection = document.getElementById('sectionContactDetails');
            const paymentSection = document.getElementById('sectionPaymentOptions');
            const passwordSection = document.getElementById('sectionChangePassword');

            function setActiveTab(activeBtn, activeSection) {
                [generalTab, contactTab, paymentTab, passwordTab].forEach(function(btn) {
                    if (btn) btn.classList.remove('active');
                });
                [generalSection, contactSection, paymentSection, passwordSection].forEach(function(sec) {
                    if (sec) sec.classList.add('d-none');
                });
                if (activeBtn) activeBtn.classList.add('active');
                if (activeSection) activeSection.classList.remove('d-none');
            }

            generalTab.addEventListener('click', function() {
                setActiveTab(generalTab, generalSection);
            });

            if (contactTab) {
                contactTab.addEventListener('click', function() {
                    setActiveTab(contactTab, contactSection);
                    if (history.replaceState) {
                        history.replaceState(null, '', '#contact-details');
                    }
                });
            }

            paymentTab.addEventListener('click', function() {
                setActiveTab(paymentTab, paymentSection);
            });

            {{-- Change Password tab hidden
            passwordTab.addEventListener('click', function() {
                setActiveTab(passwordTab, passwordSection);
                if (history.replaceState) {
                    history.replaceState(null, '', '#change-password');
                }
            });

            if (window.location.hash === '#change-password') {
                setActiveTab(passwordTab, passwordSection);
            } else --}}
            if (window.location.hash === '#contact-details' && contactTab) {
                setActiveTab(contactTab, contactSection);
            }
        });

        function DeletePaymentMethod(id) {
            Swal.fire({
                title: 'Delete Record?',
                text: "Are you sure to delete Payment Method?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Delete',
                cancelButtonText: 'Cancel',
                customClass: {
                    confirmButton: 'btn btn-danger me-3',
                    cancelButton: 'btn btn-label-secondary'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {

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
                        url: "{{ url('admin/payment-method') }}/" + id,
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
        }

        function EditPaymentMethod(id, name) {
            $("#PaymentMethod").modal("show");
            $("#name").val(name);
            $("#flag").val("U");
            $("#row_id").val(id);
            $("#_method").val("PUT");
        }

        let validator = $("#payment-method-form").validate({
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

        $("#payment-method-form").submit(function(e) {
            e.preventDefault();

            if (!$("#payment-method-form").valid()) {
                return;
            }

            let form = $(this);
            let formData = form.serialize();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });


            var flag = $("#flag").val();

            var id = $("#row_id").val();
            var url = "";
            if (flag == "A") {
                url = "{{ route('admin.payment-method.store') }}";
            } else {
                url = "{{ url('admin/payment-method') }}/" + id;
            }

            LoaderShow()

            $.ajax({
                type: "POST",
                url: url,
                data: formData,
                success: function(response) {
                    LoaderHide()
                    $("#PaymentMethod").modal('hide');
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
                    console.log(xhr.responseText);
                    console.log(response.message);
                }
            });
        });

        /* Change Password tab hidden
        $("#settings-change-password-form").validate({
            rules: {
                current_password: { required: true },
                new_password: { required: true, minlength: 6 },
                confirm_password: { required: true, equalTo: "#settings_new_password" }
            },
            messages: {
                current_password: { required: "Please enter your current password" },
                new_password: {
                    required: "Please enter a new password",
                    minlength: "Password must be at least 6 characters"
                },
                confirm_password: {
                    required: "Please confirm your password",
                    equalTo: "Passwords do not match"
                }
            },
            errorElement: "div",
            errorPlacement: function(error, element) {
                error.addClass("invalid-feedback");
                error.insertAfter(element.closest(".bc-pass-wrap").length ? element.closest(".bc-pass-wrap") : element);
            },
            highlight: function(element) {
                $(element).addClass("is-invalid").removeClass("is-valid");
            },
            unhighlight: function(element) {
                $(element).removeClass("is-invalid");
            }
        });

        $("#settings-change-password-form").submit(function(e) {
            e.preventDefault();
            if (!$(this).valid()) return;

            var $btn = $("#settingsChangePasswordBtn");
            $btn.prop("disabled", true).addClass("loading");
            LoaderShow();

            $.ajaxSetup({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
            });

            $.ajax({
                type: "POST",
                url: "{{ route('admin.staff.change-password') }}",
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    new_password: $("#settings_new_password").val(),
                    confirm_password: $("#settings_confirm_password").val(),
                    staff_row_id: "{{ $authUser->id }}"
                },
                success: function(response) {
                    LoaderHide();
                    $btn.prop("disabled", false).removeClass("loading");
                    if (response.status === 1) {
                        $("#settings-change-password-form")[0].reset();
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: response.message || 'Password changed successfully',
                            showConfirmButton: false,
                            timer: 2000
                        });
                    } else if (response.errors) {
                        var msg = Object.values(response.errors).flat().join('\n');
                        Swal.fire({ icon: 'warning', title: 'Validation', text: msg });
                    } else {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Warning',
                            text: response.message || 'Unable to change password'
                        });
                    }
                },
                error: function() {
                    LoaderHide();
                    $btn.prop("disabled", false).removeClass("loading");
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Something went wrong. Please try again.'
                    });
                }
            });
        });
        */

    </script>
@endpush
