<!doctype html>

<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr"
    data-theme="theme-default" data-assets-path="{{ asset('assets') }}/" data-template="vertical-menu-template"
    data-style="light">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>Demo : Wizard Numbered - Forms | sneat - Bootstrap Dashboard PRO</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="../../assets/img/favicon/favicon.ico" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
        rel="stylesheet" />

    <!-- Icons -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/boxicons.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/fontawesome.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/flag-icons.css') }}" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/core.css') }}" class="template-customizer-core-css" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/theme-default.css') }}"
        class="template-customizer-theme-css" />
    <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/typeahead-js/typeahead.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/bs-stepper/bs-stepper.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/bootstrap-select/bootstrap-select.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/@form-validation/form-validation.css') }}" />

    <!-- Page CSS -->

    <!-- Helpers -->
    <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>
    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Template customizer: To hide customizer set displayCustomizer value false in config.js.  -->
    <script src="{{ asset('assets/vendor/js/template-customizer.js') }}"></script>
    <!--? Config: Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="{{ asset('assets/js/config.js') }}"></script>

</head>
<style>
    @media (min-width: 1200px) {

        .layout-menu-fixed:not(.layout-menu-collapsed) .layout-page,
        .layout-menu-fixed-offcanvas:not(.layout-menu-collapsed) .layout-page {
            padding-left: 0 !important;
        }
    }

    @media (min-width: 1200px) {

        .layout-navbar-fixed:not(.layout-menu-collapsed) .layout-content-navbar:not(.layout-without-menu) .layout-navbar,
        .layout-menu-fixed.layout-navbar-fixed:not(.layout-menu-collapsed) .layout-content-navbar:not(.layout-without-menu) .layout-navbar,
        .layout-menu-fixed-offcanvas.layout-navbar-fixed:not(.layout-menu-collapsed) .layout-content-navbar:not(.layout-without-menu) .layout-navbar {
            left: 0 !important;
        }
    }
</style>

<body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <!-- Menu -->

            <!-- / Menu -->

            <!-- Layout container -->
            <div class="layout-page">
                <!-- Navbar -->
                <style>
                    .service-btn-group .btn {
                        border-radius: 8px 8px 8px 8px;
                    }

                    .service-btn-group .btn.active {
                        background: #b3202c;
                        color: #fff;
                        border-color: #b3202c;
                    }

                    .service-btn-group .btn:not(.active) {
                        background: #fff;
                        color: #b3202c;
                        border-color: #b3202c;
                    }

                    .service-card {
                        border: 1.5px solid #ececec;
                        border-radius: 12px;
                        background: #fff;
                        transition: box-shadow 0.15s;
                    }

                    .service-card:hover {
                        box-shadow: 0px 2px 8px rgba(0, 0, 0, 0.06);
                    }

                    .staff-card.selected {
                        background: #b3202c !important;
                        color: #fff !important;
                        border-color: #b3202c !important;
                    }

                    .staff-card.selected .badge {
                        background: #fff !important;
                        color: #b3202c !important;
                    }

                    .staff-card {
                        border: 1.5px solid #ececec;
                        border-radius: 12px;
                        background: #fff;
                        min-height: 100px;
                        cursor: pointer;
                        transition: box-shadow 0.15s, background 0.15s;
                    }

                    .staff-card:hover {
                        box-shadow: 0px 2px 8px rgba(0, 0, 0, 0.06);
                    }
                </style>

                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <!-- Content -->

                    <div class="container-fluid flex-grow-1 container-p-y">
                        <!-- Default -->
                        <div class="row">
                            <!-- Default Wizard -->
                            <div class="col-12 mb-6">
                                <small class="text-light fw-medium">Basic</small>
                                <div class="bs-stepper wizard-numbered mt-2">
                                    <div class="bs-stepper-header">
                                        <div class="step" data-target="#account-details">
                                            <button type="button" class="step-trigger">
                                                <span class="bs-stepper-circle">1</span>
                                                <span class="bs-stepper-label">
                                                    <span class="bs-stepper-title">Account Details</span>
                                                    <span class="bs-stepper-subtitle">Setup Account Details</span>
                                                </span>
                                            </button>
                                        </div>
                                        <div class="line">
                                            <i class="bx bx-chevron-right"></i>
                                        </div>
                                        <div class="step" data-target="#personal-info">
                                            <button type="button" class="step-trigger">
                                                <span class="bs-stepper-circle">2</span>
                                                <span class="bs-stepper-label">
                                                    <span class="bs-stepper-title">Personal Info</span>
                                                    <span class="bs-stepper-subtitle">Add personal info</span>
                                                </span>
                                            </button>
                                        </div>
                                        <div class="line">
                                            <i class="bx bx-chevron-right"></i>
                                        </div>
                                        <div class="step" data-target="#social-links">
                                            <button type="button" class="step-trigger">
                                                <span class="bs-stepper-circle">3</span>
                                                <span class="bs-stepper-label">
                                                    <span class="bs-stepper-title">Social Links</span>
                                                    <span class="bs-stepper-subtitle">Add social links</span>
                                                </span>
                                            </button>
                                        </div>
                                        <div class="line">
                                            <i class="bx bx-chevron-right"></i>
                                        </div>
                                        <div class="step" data-target="#your-details-section">
                                            <button type="button" class="step-trigger">
                                                <span class="bs-stepper-circle">3</span>
                                                <span class="bs-stepper-label">
                                                    <span class="bs-stepper-title">Your Details</span>
                                                    <span class="bs-stepper-subtitle">Add social links</span>
                                                </span>
                                            </button>
                                        </div>

                                        <div class="line">
                                            <i class="bx bx-chevron-right"></i>
                                        </div>
                                        <div class="step" data-target="#social-links">
                                            <button type="button" class="step-trigger">
                                                <span class="bs-stepper-circle">3</span>
                                                <span class="bs-stepper-label">
                                                    <span class="bs-stepper-title">Confirm</span>
                                                    <span class="bs-stepper-subtitle">Add social links</span>
                                                </span>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="bs-stepper-content">
                                        <form onSubmit="return false">
                                            <!-- Account Details -->
                                            <div id="account-details" class="content py-4">
                                                <form>
                                                    <div class="container py-4">

                                                        <!-- Booking Method Tabs -->
                                                        <div class="mb-4">
                                                            <label class="form-label fs-5 fw-bold mb-2">Choose Your
                                                                Booking Method</label>
                                                            <p class="mb-3 text-muted">Select a service or choose your
                                                                preferred staff member to start</p>
                                                            <div class="service-btn-group btn-group w-100 mb-3"
                                                                role="group">
                                                                <button id="byServiceBtn" class="btn active"
                                                                    onclick="showWizardTab('service')">By
                                                                    Service</button>
                                                                <button id="byStaffBtn" class="btn"
                                                                    onclick="showWizardTab('staff')">By Staff
                                                                    (1)</button>
                                                            </div>
                                                        </div>

                                                        <!-- By Service Panel -->
                                                        <div id="byServicePanel">
                                                            <div class="mb-4">
                                                                <label class="form-label fs-5 fw-bold mb-2">Choose Your
                                                                    Services</label>
                                                                <p class="mb-3 text-muted">Select one or more services
                                                                    you'd like to book</p>
                                                                <div class="card px-4 py-3 border-0 shadow-sm">
                                                                    <div class="fw-bold fs-6 mb-2">Color <i
                                                                            class="bx bx-chevron-down align-baseline"></i>
                                                                    </div>
                                                                    <div class="row g-3">
                                                                        <div class="col-md-6 col-lg-4">
                                                                            <div
                                                                                class="service-card px-3 py-3 h-100 d-flex flex-column justify-content-between">
                                                                                <div class="fw-semibold">bread color
                                                                                </div>
                                                                                <div
                                                                                    class="d-flex justify-content-between align-items-center mt-2">
                                                                                    <div class="text-muted"><i
                                                                                            class="bx bx-time"></i> 30
                                                                                        min</div>
                                                                                    <div
                                                                                        class="fw-bold text-danger fs-6">
                                                                                        $50.00</div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6 col-lg-4">
                                                                            <div
                                                                                class="service-card px-3 py-3 h-100 d-flex flex-column justify-content-between">
                                                                                <div class="fw-semibold">bread color
                                                                                    pro</div>
                                                                                <div
                                                                                    class="d-flex justify-content-between align-items-center mt-2">
                                                                                    <div class="text-muted"><i
                                                                                            class="bx bx-time"></i> 30
                                                                                        min</div>
                                                                                    <div
                                                                                        class="fw-bold text-danger fs-6">
                                                                                        $40.00</div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6 col-lg-4">
                                                                            <div
                                                                                class="service-card px-3 py-3 h-100 d-flex flex-column justify-content-between">
                                                                                <div class="fw-semibold">hair color
                                                                                </div>
                                                                                <div
                                                                                    class="d-flex justify-content-between align-items-center mt-2">
                                                                                    <div class="text-muted"><i
                                                                                            class="bx bx-time"></i> 30
                                                                                        min</div>
                                                                                    <div
                                                                                        class="fw-bold text-danger fs-6">
                                                                                        $15.00</div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6 col-lg-4">
                                                                            <div
                                                                                class="service-card px-3 py-3 h-100 d-flex flex-column justify-content-between">
                                                                                <div class="fw-semibold">jjjjj</div>
                                                                                <div
                                                                                    class="d-flex justify-content-between align-items-center mt-2">
                                                                                    <div class="text-muted"><i
                                                                                            class="bx bx-time"></i> 60
                                                                                        min</div>
                                                                                    <div
                                                                                        class="fw-bold text-danger fs-6">
                                                                                        $15.00</div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6 col-lg-4">
                                                                            <div
                                                                                class="service-card px-3 py-3 h-100 d-flex flex-column justify-content-between">
                                                                                <div class="fw-semibold">NO tip
                                                                                    eligible</div>
                                                                                <div
                                                                                    class="d-flex justify-content-between align-items-center mt-2">
                                                                                    <div class="text-muted"><i
                                                                                            class="bx bx-time"></i> 30
                                                                                        min</div>
                                                                                    <div
                                                                                        class="fw-bold text-danger fs-6">
                                                                                        $20.00</div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6 col-lg-4">
                                                                            <div
                                                                                class="service-card px-3 py-3 h-100 d-flex flex-column justify-content-between">
                                                                                <div class="fw-semibold">primirm bread
                                                                                    color</div>
                                                                                <div
                                                                                    class="d-flex justify-content-between align-items-center mt-2">
                                                                                    <div class="text-muted"><i
                                                                                            class="bx bx-time"></i> 30
                                                                                        min</div>
                                                                                    <div
                                                                                        class="fw-bold text-danger fs-6">
                                                                                        $500.00</div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- By Staff Panel -->
                                                        <div id="byStaffPanel" class="d-none">
                                                            <div class="mb-4 mt-5">
                                                                <label class="form-label fs-5 fw-bold mb-2">Choose Your
                                                                    Barber</label>
                                                                <p class="mb-3 text-muted">Select a staff member for
                                                                    your appointment</p>
                                                                <div class="row g-3">
                                                                    <!-- Any staff -->
                                                                    <div class="col-12 col-sm-6 col-md-4">
                                                                        <div
                                                                            class="staff-card selected d-flex align-items-center p-3 justify-content-between">
                                                                            <div class="d-flex align-items-center">
                                                                                <img src="https://via.placeholder.com/48"
                                                                                    class="rounded-circle me-3"
                                                                                    width="48" height="48"
                                                                                    alt="Any staff" />
                                                                                <div>
                                                                                    <div class="fw-semibold">Any staff
                                                                                    </div>
                                                                                    <div><i class="bx bx-user"></i> 0
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <span
                                                                                class="badge bg-light text-danger fw-semibold ms-2">Available</span>
                                                                        </div>
                                                                    </div>
                                                                    <!-- staff1 -->
                                                                    <div class="col-12 col-sm-6 col-md-4">
                                                                        <div
                                                                            class="staff-card d-flex align-items-center p-3 justify-content-between">
                                                                            <div class="d-flex align-items-center">
                                                                                <img src="https://via.placeholder.com/48"
                                                                                    class="rounded-circle me-3"
                                                                                    width="48" height="48"
                                                                                    alt="" />
                                                                                <div>
                                                                                    <div class="fw-semibold">staff1
                                                                                        edit 11</div>
                                                                                    <div><i class="bx bx-user"></i> 1
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <span
                                                                                class="badge bg-light fw-semibold border border-success text-success ms-2">Available</span>
                                                                        </div>
                                                                    </div>
                                                                    <!-- Utsav Makwana -->
                                                                    <div class="col-12 col-sm-6 col-md-4">
                                                                        <div
                                                                            class="staff-card d-flex align-items-center p-3 justify-content-between">
                                                                            <div class="d-flex align-items-center">
                                                                                <img src="https://via.placeholder.com/48"
                                                                                    class="rounded-circle me-3"
                                                                                    width="48" height="48"
                                                                                    alt="" />
                                                                                <div>
                                                                                    <div class="fw-semibold">Utsav
                                                                                        Makwana</div>
                                                                                    <div><i class="bx bx-user"></i> 6
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <span
                                                                                class="badge bg-light fw-semibold border border-success text-success ms-2">Available</span>
                                                                        </div>
                                                                    </div>
                                                                    <!-- Testy -->
                                                                    <div class="col-12 col-sm-6 col-md-4">
                                                                        <div
                                                                            class="staff-card d-flex align-items-center p-3 justify-content-between">
                                                                            <div class="d-flex align-items-center">
                                                                                <img src="https://via.placeholder.com/48"
                                                                                    class="rounded-circle me-3"
                                                                                    width="48" height="48"
                                                                                    alt="" />
                                                                                <div>
                                                                                    <div class="fw-semibold">Testy
                                                                                    </div>
                                                                                    <div><i class="bx bx-user"></i> 9
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <span
                                                                                class="badge bg-light fw-semibold border border-success text-success ms-2">Available</span>
                                                                        </div>
                                                                    </div>
                                                                    <!-- ronak -->
                                                                    <div class="col-12 col-sm-6 col-md-4">
                                                                        <div
                                                                            class="staff-card d-flex align-items-center p-3 justify-content-between">
                                                                            <div class="d-flex align-items-center">
                                                                                <img src="https://via.placeholder.com/48"
                                                                                    class="rounded-circle me-3"
                                                                                    width="48" height="48"
                                                                                    alt="" />
                                                                                <div>
                                                                                    <div class="fw-semibold">ronak
                                                                                    </div>
                                                                                    <div><i class="bx bx-user"></i> 1
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <span
                                                                                class="badge bg-light fw-semibold border border-success text-success ms-2">Available</span>
                                                                        </div>
                                                                    </div>
                                                                    <!-- mahipat vaghela 121 -->
                                                                    <div class="col-12 col-sm-6 col-md-4">
                                                                        <div
                                                                            class="staff-card d-flex align-items-center p-3 justify-content-between">
                                                                            <div class="d-flex align-items-center">
                                                                                <img src="https://via.placeholder.com/48"
                                                                                    class="rounded-circle me-3"
                                                                                    width="48" height="48"
                                                                                    alt="" />
                                                                                <div>
                                                                                    <div class="fw-semibold">mahipat
                                                                                        vaghela 121</div>
                                                                                    <div><i class="bx bx-user"></i> 8
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <span
                                                                                class="badge bg-light fw-semibold border border-success text-success ms-2">Available</span>
                                                                        </div>
                                                                    </div>
                                                                    <!-- sanju -->
                                                                    <div class="col-12 col-sm-6 col-md-4">
                                                                        <div
                                                                            class="staff-card d-flex align-items-center p-3 justify-content-between">
                                                                            <div class="d-flex align-items-center">
                                                                                <img src="https://via.placeholder.com/48"
                                                                                    class="rounded-circle me-3"
                                                                                    width="48" height="48"
                                                                                    alt="" />
                                                                                <div>
                                                                                    <div class="fw-semibold">sanju
                                                                                    </div>
                                                                                    <div><i class="bx bx-user"></i> 4
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <span
                                                                                class="badge bg-light fw-semibold border border-success text-success ms-2">Available</span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </form>
                                            </div>

                                            
                                            <!-- Personal Info -->
                                            <div id="personal-info" class="content">
                                                <div class="content-header mb-4">
                                                    <h6 class="mb-0">Personal Info</h6>
                                                    <small>Enter Your Personal Info.</small>
                                                </div>
                                                <div class="row g-6">
                                                    <div class="card p-4">
                                                        <div class="mb-4">
                                                            <label class="form-label fs-5 fw-bold mb-2">Choose Your
                                                                Barber</label>
                                                            <p class="mb-3 text-muted">Select a staff member who can
                                                                perform your chosen services</p>
                                                        </div>
                                                        <div class="row gx-4 gy-3">
                                                            <!-- Any staff -->
                                                            <div class="col-12 col-md-6">
                                                                <div
                                                                    class="d-flex align-items-center p-3 rounded bg-white border h-100 justify-content-between">
                                                                    <div class="d-flex align-items-center">
                                                                        <img src="https://via.placeholder.com/48"
                                                                            class="rounded-circle me-3" width="48"
                                                                            height="48" alt="Any staff" />
                                                                        <div>
                                                                            <div class="fw-semibold">Any staff</div>
                                                                            <div>
                                                                                <span class="me-2"><i
                                                                                        class="bx bx-user"></i>
                                                                                    0</span>
                                                                                <span
                                                                                    class="badge bg-light border border-secondary text-body-secondary fw-normal">All
                                                                                    selected services</span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <span
                                                                        class="badge bg-light fw-semibold border border-success text-success ms-2">Available</span>
                                                                </div>
                                                            </div>
                                                            <!-- Utsav Makwana -->
                                                            <div class="col-12 col-md-6">
                                                                <div
                                                                    class="d-flex align-items-center p-3 rounded bg-white border h-100 justify-content-between">
                                                                    <div class="d-flex align-items-center">
                                                                        <img src="https://via.placeholder.com/48"
                                                                            class="rounded-circle me-3" width="48"
                                                                            height="48" alt="Utsav Makwana" />
                                                                        <div>
                                                                            <div class="fw-semibold">Utsav Makwana
                                                                            </div>
                                                                            <div>
                                                                                <span class="me-2"><i
                                                                                        class="bx bx-user"></i>
                                                                                    1</span>
                                                                                <span
                                                                                    class="badge bg-light border border-secondary text-body-secondary fw-normal">All
                                                                                    selected services</span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <span
                                                                        class="badge bg-light fw-semibold border border-success text-success ms-2">Available</span>
                                                                </div>
                                                            </div>
                                                            <!-- Testy -->
                                                            <div class="col-12 col-md-6">
                                                                <div
                                                                    class="d-flex align-items-center p-3 rounded bg-white border h-100 justify-content-between">
                                                                    <div class="d-flex align-items-center">
                                                                        <img src="https://via.placeholder.com/48"
                                                                            class="rounded-circle me-3" width="48"
                                                                            height="48" alt="Testy" />
                                                                        <div>
                                                                            <div class="fw-semibold">Testy</div>
                                                                            <div>
                                                                                <span class="me-2"><i
                                                                                        class="bx bx-user"></i>
                                                                                    1</span>
                                                                                <span
                                                                                    class="badge bg-light border border-secondary text-body-secondary fw-normal">All
                                                                                    selected services</span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <span
                                                                        class="badge bg-light fw-semibold border border-success text-success ms-2">Available</span>
                                                                </div>
                                                            </div>
                                                            <!-- mahipat vaghela 121 -->
                                                            <div class="col-12 col-md-6">
                                                                <div
                                                                    class="d-flex align-items-center p-3 rounded bg-white border h-100 justify-content-between">
                                                                    <div class="d-flex align-items-center">
                                                                        <img src="https://via.placeholder.com/48"
                                                                            class="rounded-circle me-3" width="48"
                                                                            height="48"
                                                                            alt="mahipat vaghela 121" />
                                                                        <div>
                                                                            <div class="fw-semibold">mahipat vaghela
                                                                                121</div>
                                                                            <div>
                                                                                <span class="me-2"><i
                                                                                        class="bx bx-user"></i>
                                                                                    1</span>
                                                                                <span
                                                                                    class="badge bg-light border border-secondary text-body-secondary fw-normal">All
                                                                                    selected services</span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <span
                                                                        class="badge bg-light fw-semibold border border-success text-success ms-2">Available</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 d-flex justify-content-between">
                                                        <button class="btn btn-primary btn-prev">
                                                            <i class="bx bx-left-arrow-alt bx-sm ms-sm-n2 me-sm-2"></i>
                                                            <span
                                                                class="align-middle d-sm-inline-block d-none">Previous</span>
                                                        </button>
                                                        <button class="btn btn-success btn-submit">Submit</button>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Social Links -->
                                            <div id="social-links" class="content">
                                                <div class="content-header mb-4">
                                                    <h6 class="mb-0">Social Links</h6>
                                                    <small>Enter Your Social Links.</small>
                                                </div>
                                                <div class="row g-6">
                                                    <div class="card p-4">
                                                        <div class="mb-3">
                                                            <h5 class="fw-bold mb-1">Choose Date &amp; Time</h5>
                                                            <div class="text-muted mb-1">Select your preferred
                                                                appointment date and time</div>
                                                            <small class="text-muted"><i class="bx bx-time"></i> Slot
                                                                duration: 30 minutes</small>
                                                        </div>
                                                        <div class="row g-5">
                                                            <!-- Calendar -->
                                                            <div class="col-lg-6">
                                                                <label class="mb-2 fw-semibold">Select Date</label>
                                                                <div
                                                                    class="d-inline-block bg-white border rounded p-3">
                                                                    <div
                                                                        class="d-flex justify-content-between align-items-center mb-2">
                                                                        <button class="btn btn-link btn-sm px-2"
                                                                            id="prevMonthBtn"><i
                                                                                class="bx bx-chevron-left"></i></button>
                                                                        <span id="calendarMonth"
                                                                            class="fw-semibold">September 2025</span>
                                                                        <button class="btn btn-link btn-sm px-2"
                                                                            id="nextMonthBtn"><i
                                                                                class="bx bx-chevron-right"></i></button>
                                                                    </div>
                                                                    <table class="table table-borderless mb-0"
                                                                        id="calendarTable">
                                                                        <thead>
                                                                            <tr class="text-center text-muted">
                                                                                <th class="fw-normal py-1">Su</th>
                                                                                <th class="fw-normal py-1">Mo</th>
                                                                                <th class="fw-normal py-1">Tu</th>
                                                                                <th class="fw-normal py-1">We</th>
                                                                                <th class="fw-normal py-1">Th</th>
                                                                                <th class="fw-normal py-1">Fr</th>
                                                                                <th class="fw-normal py-1">Sa</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody id="calendarBody">
                                                                            <!-- Calendar days filled via JS -->
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                            <!-- Time slots -->
                                                            <div class="col-lg-6">
                                                                <div class="mb-2 fw-semibold">Select Time</div>
                                                                <div class="mb-2 text-muted">Duration: 30 minutes</div>
                                                                <div class="row row-cols-3 g-2" id="timeSlots">
                                                                    <!-- Time slot buttons generated via JS -->
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>


                                                    <div class="col-12 d-flex justify-content-between">
                                                        <button class="btn btn-primary btn-prev">
                                                            <i class="bx bx-left-arrow-alt bx-sm ms-sm-n2 me-sm-2"></i>
                                                            <span
                                                                class="align-middle d-sm-inline-block d-none">Previous</span>
                                                        </button>
                                                        <button class="btn btn-success btn-submit">Submit</button>
                                                    </div>
                                                </div>
                                            </div>

                                            <div id="your-details-section" class="content">
                                                <div class="content-header mb-4">
                                                    <h6 class="mb-0">Social Links</h6>
                                                    <small>Enter Your Social Links.</small>
                                                </div>
                                                <div class="row g-6">
                                                    <div class="card p-4">
                                                        <div class="mb-3">
                                                            <h5 class="fw-bold mb-1">Your Details</h5>
                                                            <div class="text-muted mb-3">Please provide your contact
                                                                information</div>
                                                        </div>
                                                        <form autocomplete="off">
                                                            <!-- Phone Number -->
                                                            <div class="mb-3">
                                                                <label class="form-label fw-semibold"
                                                                    for="phone">Phone Number <span
                                                                        class="text-danger">*</span></label>
                                                                <input type="tel" class="form-control"
                                                                    id="phone"
                                                                    placeholder="Enter your phone number" required>
                                                                <div class="form-text text-muted">
                                                                    Enter your 10-digit phone number. We'll search our
                                                                    records and auto-fill your details if you’ve booked
                                                                    before.
                                                                </div>
                                                                <div class="form-text text-danger mt-1">
                                                                    <i class="bx bx-error-circle align-middle"></i>
                                                                    Phone number is required.
                                                                </div>
                                                            </div>

                                                            <!-- Full Name -->
                                                            <div class="mb-3">
                                                                <label class="form-label fw-semibold"
                                                                    for="name">Full Name <span
                                                                        class="text-danger">*</span></label>
                                                                <input type="text" class="form-control"
                                                                    id="name" placeholder="Enter your full name"
                                                                    required>
                                                                <div class="form-text text-danger mt-1">
                                                                    <i class="bx bx-error-circle align-middle"></i>
                                                                    Full name is required.
                                                                </div>
                                                            </div>

                                                            <!-- Email (Optional) -->
                                                            <div class="mb-3">
                                                                <label class="form-label fw-semibold"
                                                                    for="email">Email (Optional)</label>
                                                                <input type="email" class="form-control"
                                                                    id="email"
                                                                    placeholder="Enter your email address">
                                                            </div>

                                                            <!-- Notes (Optional) -->
                                                            <div class="mb-2">
                                                                <label class="form-label fw-semibold"
                                                                    for="notes">Notes (Optional)</label>
                                                                <input type="text" class="form-control"
                                                                    id="notes"
                                                                    placeholder="Any special requests or notes">
                                                            </div>
                                                        </form>
                                                    </div>

                                                    <!-- Add these in your <head> for styling and icons: -->
                                                    <!-- Bootstrap 5: -->
                                                    <link
                                                        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
                                                        rel="stylesheet">
                                                    <!-- Boxicons: -->
                                                    <link rel="stylesheet"
                                                        href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css">



                                                    <div class="col-12 d-flex justify-content-between">
                                                        <button class="btn btn-primary btn-prev">
                                                            <i class="bx bx-left-arrow-alt bx-sm ms-sm-n2 me-sm-2"></i>
                                                            <span
                                                                class="align-middle d-sm-inline-block d-none">Previous</span>
                                                        </button>
                                                        <button class="btn btn-success btn-submit">Submit</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <!-- /Default Wizard -->

                        </div>

                    </div>
                    <!-- / Content -->



                    <div class="content-backdrop fade"></div>
                </div>
                <!-- Content wrapper -->
            </div>
            <!-- / Layout page -->
        </div>

        <!-- Overlay -->
        <div class="layout-overlay layout-menu-toggle"></div>

        <!-- Drag Target Area To SlideIn Menu On Small Screens -->
        <div class="drag-target"></div>
    </div>
    <!-- / Layout wrapper -->

    <!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js -->

    <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/hammer/hammer.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/i18n/i18n.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/typeahead-js/typeahead.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/menu.js') }}"></script>

    <!-- Vendors JS -->
    <script src="{{ asset('assets/vendor/libs/bs-stepper/bs-stepper.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/bootstrap-select/bootstrap-select.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/@form-validation/popular.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/@form-validation/bootstrap5.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/@form-validation/auto-focus.js') }}"></script>

    <!-- Main JS -->
    <script src="{{ asset('assets/js/main.js') }}"></script>

    <!-- Page JS -->
    <script src="{{ asset('assets/js/form-wizard-numbered.js') }}"></script>
    <script src="{{ asset('assets/js/form-wizard-validation.js') }}"></script>
    <script>
        function showWizardTab(tab) {
            let byServicePanel = document.getElementById('byServicePanel');
            let byStaffPanel = document.getElementById('byStaffPanel');
            let byServiceBtn = document.getElementById('byServiceBtn');
            let byStaffBtn = document.getElementById('byStaffBtn');
            if (tab === 'service') {
                byServicePanel.classList.remove('d-none');
                byStaffPanel.classList.add('d-none');
                byServiceBtn.classList.add('active');
                byStaffBtn.classList.remove('active');
            } else {
                byServicePanel.classList.add('d-none');
                byStaffPanel.classList.remove('d-none');
                byServiceBtn.classList.remove('active');
                byStaffBtn.classList.add('active');
            }
        }
        // Default: show service tab
        showWizardTab('service');
    </script>
    <script>
        // Calendar rendering
        const currentDate = new Date();
        let selectedYear = 2025,
            selectedMonth = 8,
            selectedDay = 7; // September (0-based), 7 is default
        const calendarMonth = document.getElementById('calendarMonth');
        const calendarBody = document.getElementById('calendarBody');
        const prevMonthBtn = document.getElementById('prevMonthBtn');
        const nextMonthBtn = document.getElementById('nextMonthBtn');

        // Helper to get days in month:
        function daysInMonth(year, month) {
            return new Date(year, month + 1, 0).getDate();
        }

        function renderCalendar() {
            calendarMonth.textContent = new Date(selectedYear, selectedMonth).toLocaleString('default', {
                month: 'long',
                year: 'numeric'
            });
            calendarBody.innerHTML = '';
            const firstDay = new Date(selectedYear, selectedMonth, 1).getDay();
            const numDays = daysInMonth(selectedYear, selectedMonth);
            let tr = document.createElement('tr');
            // Blank days start
            for (let i = 0; i < firstDay; i++) {
                tr.appendChild(document.createElement('td'));
            }
            // Days
            for (let day = 1; day <= numDays; day++) {
                if ((tr.children.length) === 7) {
                    calendarBody.appendChild(tr);
                    tr = document.createElement('tr');
                }
                const td = document.createElement('td');
                td.className = "text-center";
                td.innerHTML = `<button class="btn btn-sm rounded-circle px-2 py-1 ${day === selectedDay ? 'btn-danger text-white fw-semibold' : 'btn-light'}" 
            onclick="selectDay(${day})">${day}</button>`;
                tr.appendChild(td);
            }
            // Fill remaining days
            while (tr.children.length < 7) {
                tr.appendChild(document.createElement('td'));
            }
            calendarBody.appendChild(tr);
        }

        function selectDay(day) {
            selectedDay = day;
            renderCalendar();
        }
        // Navigation
        prevMonthBtn.onclick = () => {
            if (selectedMonth > 0) {
                selectedMonth--;
            } else {
                selectedYear--;
                selectedMonth = 11;
            }
            renderCalendar();
        };
        nextMonthBtn.onclick = () => {
            if (selectedMonth < 11) {
                selectedMonth++;
            } else {
                selectedYear++;
                selectedMonth = 0;
            }
            renderCalendar();
        };
        // For demo, keep static month/year as per your image
        renderCalendar();

        // Time slots
        const slotTimes = [
            '9:00 AM', '9:30 AM', '10:00 AM',
            '10:30 AM', '11:00 AM', '11:30 AM',
            '12:00 PM', '12:30 PM', '1:00 PM',
            '1:30 PM', '2:00 PM', '2:30 PM',
            '3:00 PM', '3:30 PM', '4:00 PM',
            '4:30 PM', '5:00 PM', '5:30 PM'
        ];
        const timeSlotsDiv = document.getElementById('timeSlots');
        let selectedSlot = null;
        // For demo: only enable up to and including 4:30 PM
        const enabledSlots = 16;

        function renderTimeSlots() {
            timeSlotsDiv.innerHTML = '';
            slotTimes.forEach((slot, i) => {
                const col = document.createElement('div');
                let btnClass = "btn w-100 btn-outline-light border text-start d-flex align-items-center";
                if (selectedSlot === i) btnClass += " border-2 border-danger fw-semibold";
                if (i >= enabledSlots) btnClass += " disabled text-muted";
                col.innerHTML = `<button type="button" class="${btnClass}" ${i >= enabledSlots ? 'disabled' : ''} onclick="selectSlot(${i})">
            <i class="bx bx-time me-1"></i> ${slot}
        </button>`;
                timeSlotsDiv.appendChild(col);
            });
        }

        function selectSlot(i) {
            selectedSlot = i;
            renderTimeSlots();
        }
        renderTimeSlots();
        window.selectDay = selectDay;
        window.selectSlot = selectSlot;
    </script>

</body>

</html>
