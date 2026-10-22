<!doctype html>

<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr"
    data-theme="theme-default" data-assets-path="{{ asset('assets') }}/" data-template="vertical-menu-template"
    data-style="light">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>{{ isset($currentShop) ? $currentShop->name : 'Infotech' }} | ADMIN</title>

    <meta name="description" content="{{ isset($currentShop) ? $currentShop->name : 'Infotech' }} Admin Dashboard" />

    <!-- Favicon -->
    @php
        $shopLogo = isset($currentShop) ? $currentShop->logoUrl() : asset('assets/img/logo.webp');
        $shopName = $currentShop->name ?? 'Infotech';
    @endphp
    <link rel="icon" type="image/webp" href="{{ $shopLogo }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@600;700&family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
        rel="stylesheet" />

    <!-- Icons -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/boxicons.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/fontawesome.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/flag-icons.css') }}" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/core.css?id=2') }}"
        class="template-customizer-core-css" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/theme-default.css') }}"
        class="template-customizer-theme-css" />
    <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/admin-saas.css') }}?v=14" />
    @include('admin.partials.shop-theme')

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
    <link rel="stylesheet"
        href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />

    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css?id=1') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/bootstrap-select/bootstrap-select.css') }}" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    @stack('css')
    <!-- Helpers -->
    <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>
    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Template customizer: To hide customizer set displayCustomizer value false in config.js.  -->
    <script src="{{ asset('assets/vendor/js/template-customizer.js') }}"></script>
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="{{ asset('assets/js/config.js') }}"></script>


    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
        window.SHOP_CURRENCY = @json($shopCurrency ?? 'INR');
        window.SHOP_CURRENCY_SYMBOL = @json($shopCurrencySymbol ?? '₹');
        window.shopMoney = function (amount, decimals) {
            decimals = (typeof decimals === 'number') ? decimals : 2;
            var symbol = window.SHOP_CURRENCY_SYMBOL || '';
            var n = Number(amount);
            if (isNaN(n)) n = 0;
            return symbol + n.toFixed(decimals);
        };
        window.paymentMethodLabel = function (method) {
            var label = String(method || '').trim();
            var normalized = label.toLowerCase();
            if (['upi', 'gpay', 'google pay', 'phonepe', 'paytm'].indexOf(normalized) !== -1) return 'UPI';
            if (['card', 'credit card', 'debit card'].indexOf(normalized) !== -1) return 'Card';
            if (['cash', 'cash payment'].indexOf(normalized) !== -1) return 'Cash';
            return label ? normalized.charAt(0).toUpperCase() + normalized.slice(1) : '—';
        };
    </script>
</head>
<style>
    @media (max-width: 1199.98px) {
        .layout-menu-toggle.mobile-menu-toggle {
            display: inline-flex !important;
            align-items: center;
            justify-content: center;
            flex: 0 0 auto;
            padding: 0.5rem;
            color: #334155;
        }

        .layout-page {
            margin-left: 0 !important;
            padding-left: 0 !important;
            width: 100%;
            min-width: 0;
        }

        .content-wrapper,
        .container-xxl {
            max-width: 100%;
            min-width: 0;
        }
    }

    @media (max-width: 767.98px) {
        .layout-navbar.container-xxl {
            padding-inline: 0.75rem;
        }

        .layout-menu {
            width: min(280px, 82vw) !important;
            max-width: 82vw;
        }

        .container-p-y {
            padding-top: 1rem !important;
            padding-bottom: 1rem !important;
        }

        .card {
            padding: 1rem !important;
        }
    }

    @media (min-width: 1200px) {

        .layout-navbar-fixed body:not(.modal-open) .layout-content-navbar .layout-menu,
        .layout-menu-fixed body:not(.modal-open) .layout-content-navbar .layout-menu,
        .layout-menu-fixed-offcanvas body:not(.modal-open) .layout-content-navbar .layout-menu {
            z-index: 100;
        }
    }

    .layout-navbar-fixed body:not(.modal-open) .layout-content-navbar .layout-navbar,
    .layout-menu-fixed body:not(.modal-open) .layout-content-navbar .layout-navbar,
    .layout-menu-fixed-offcanvas body:not(.modal-open) .layout-content-navbar .layout-navbar {
        z-index: 100;
    }

    .menu .app-brand.demo {
        height: 101px;
        margin-top: 12px;
    }

    .menu-inner-shadow {
        top: 7.0rem;
    }

    .center-logo {
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .template-customizer-open-btn {
        display: none !important;
    }
</style>
<style>
    .service-image-upload {
        border: 2px dashed #6c757d;
        /* Bootstrap secondary border color */
        border-radius: 0.375rem;
        /* Rounded border */
        height: 180px;
        cursor: pointer;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        position: relative;
    }

    .service-image-upload input[type="file"] {
        opacity: 0;
        position: absolute;
        width: 100%;
        height: 100%;
        cursor: pointer;
    }

    .service-image-upload .icon {
        font-size: 48px;
        color: #868e96;
        /* Bootstrap text-muted color */
    }

    .service-image-upload .text-primary {
        font-weight: 600;
        margin-bottom: 0;
    }

    .service-image-upload .text-muted {
        font-size: 0.875rem;
        margin: 0;
    }
</style>
<style>
    .loader {
        width: 200px;
        height: 140px;
        background: #979794;
        box-sizing: border-box;
        position: relative;
        border-radius: 8px;
        perspective: 1000px;
        position: absolute;
        left: 45%;
        top: 40%;
    }

    .loader:before {
        content: '';
        position: absolute;
        left: 10px;
        right: 10px;
        top: 10px;
        bottom: 10px;
        border-radius: 8px;
        background: #f5f5f5 no-repeat;
        background-size: 60px 10px;
        background-image: linear-gradient(#ddd 100px, transparent 0),
            linear-gradient(#ddd 100px, transparent 0),
            linear-gradient(#ddd 100px, transparent 0),
            linear-gradient(#ddd 100px, transparent 0),
            linear-gradient(#ddd 100px, transparent 0),
            linear-gradient(#ddd 100px, transparent 0);

        background-position: 15px 30px, 15px 60px, 15px 90px,
            105px 30px, 105px 60px, 105px 90px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.25);
    }

    .loader:after {
        content: '';
        position: absolute;
        width: calc(50% - 10px);
        right: 10px;
        top: 10px;
        bottom: 10px;
        border-radius: 8px;
        background: #fff no-repeat;
        background-size: 60px 10px;
        background-image: linear-gradient(#ddd 100px, transparent 0),
            linear-gradient(#ddd 100px, transparent 0),
            linear-gradient(#ddd 100px, transparent 0);
        background-position: 50% 30px, 50% 60px, 50% 90px;
        transform: rotateY(0deg);
        transform-origin: left center;
        animation: paging 1s linear infinite;
    }


    @keyframes paging {
        to {
            transform: rotateY(-180deg);
        }
    }

    .preloader {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #000000c9;
        z-index: 9999;
    }

    #Loader {
        display: none;
    }

    #calendarBody tr td{
padding: 0.782rem 1rem!important;
    }
</style>
<script>
    var page_active_id = "Home";
</script>

<body>
    @php
        use Illuminate\Support\Facades\Auth;
        $user = Auth::user();
    @endphp
    @php
        $showCompleteOrderModal = request()->routeIs(
            'admin.customer.customer-profile',
            'admin.staff.staff-profile'
        );
        $servicesByCategory = collect();
        $Product = collect();

        if ($showCompleteOrderModal) {
            $shopId = \App\Support\CurrentShop::require()->id;
            $servicesByCategoryRaw = DB::table('tbl_service_category as sc')
                ->join('tbl_service as s', 'sc.id', '=', 's.category_id')
                ->select(
                    'sc.id as category_id',
                    'sc.category_name',
                    's.id as service_id',
                    's.service_name',
                    's.price',
                    's.duration',
                    's.status',
                    's.tip_status',
                    's.service_img',
                    's.description',
                )
                ->where('sc.shop_id', $shopId)
                ->where('s.shop_id', $shopId)
                ->whereNull('s.deleted_at')
                ->whereNull('sc.deleted_at')
                ->orderBy('sc.id')
                ->orderBy('s.id')
                ->get();

            $servicesByCategory = $servicesByCategoryRaw->groupBy('category_id');
            $Product = App\Models\Product::query()->get();
        }

    @endphp
    <div class="preloader" id="Loader">
        <span class="loader"></span>
    </div>


    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <!-- Menu -->

            <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
                <div class="app-brand demo center-logo">
                    <a href="{{ route(auth()->user()->preferredAdminHomeRoute()) }}" class="app-brand-link">
                        <img class="bc-brand-logo" src="{{ $shopLogo }}"
                            alt="{{ $shopName }}">
                        <span class="bc-brand-text">
                            <strong>{{ $shopName }}</strong>
                            <span>{{ isset($currentPlan) && $currentPlan ? $currentPlan->name . ' plan' : 'Admin' }}</span>
                        </span>
                    </a>
                </div>

                @if (!empty($currentPlan))
                    <div class="px-3 pb-2">
                        <a href="{{ route('admin.billing.index') }}" class="d-block small text-decoration-none rounded px-2 py-2 saas-plan-usage">
                            <strong>{{ $currentPlan->name }}</strong>
                            · staff {{ $planUsage['staff_used'] ?? 0 }}/{{ ($planUsage['staff_limit'] ?? null) === null ? '∞' : $planUsage['staff_limit'] }}
                            · appts {{ $planUsage['appointments_used'] ?? 0 }}/{{ ($planUsage['appointments_limit'] ?? null) === null ? '∞' : $planUsage['appointments_limit'] }}
                        </a>
                    </div>
                @endif

                <div class="menu-inner-shadow"></div>

                <ul class="menu-inner py-1">

                    <li class="menu-item" id="Billing">
                        <a href="{{ route('admin.billing.index') }}" class="menu-link">
                            <i class="menu-icon bi bi-credit-card-2-front-fill"></i>
                            <div class="text-truncate" data-i18n="Plan & billing">Plan &amp; billing</div>
                        </a>
                    </li>

                    @canany(['Dashboard-Today_Appointments', 'Dashboard-Upcoming_Appointments',
                    'Dashboard-Top_Services', 'Dashboard-Top_Staff'])
                    <li class="menu-item" id="Home">
                        <a href="{{ route('admin.dashboard') }}" class="menu-link ">
                            <i class="menu-icon bi bi-grid-1x2-fill"></i>
                            <div class="text-truncate" data-i18n="Dashboard">Dashboard</div>
                        </a>
                    </li>
                @endcanany

                
                    @can('Appointment-View')
                        <li class="menu-item" id="Appointment">
                            <a href="{{ route('admin.appointment.index') }}" class="menu-link ">
                                <i class="menu-icon bi bi-calendar2-check-fill"></i>
                                <div class="text-truncate" data-i18n="Appointments">Appointments</div>
                            </a>
                        </li>
                    @endcan

                    @can('Calender-View')
                        <li class="menu-item" id="Calendar">
                            <a href="{{ route('admin.calender.index') }}" class="menu-link ">
                                <i class="menu-icon bi bi-calendar3"></i>
                                <div class="text-truncate" data-i18n="Calendar">Calendar</div>
                            </a>
                        </li>
                    @endcan

                    @canany(['Customer-Add', 'Customer-Edit', 'Customer-Delete'])
                        <li class="menu-item" id="Customer">
                            <a href="{{ route('admin.customers.index') }}" class="menu-link ">
                                <i class="menu-icon bi bi-person-badge-fill"></i>
                                <div class="text-truncate" data-i18n="Customer">Customer</div>
                            </a>
                        </li>
                    @endcanany

                  

                    @canany(['Gst-Add', 'Gst-Edit', 'Gst-Delete'])
                        <li class="menu-item" id="GSTSettings">
                            <a href="{{ route('admin.gst.index') }}" class="menu-link ">
                                <i class="menu-icon bi bi-currency-dollar"></i>
                                <div class="text-truncate" data-i18n="GST Setting">GST Setting</div>
                            </a>
                        </li>
                    @endcanany

                    @canany(['Pos_invoice-Add', 'Pos_invoice-Edit'])
                        <li class="menu-item @if (request()->routeIs('admin.invoice.index')) active open @endif" id="Invoices">
                            <a href="{{ route('admin.invoice.index') }}" class="menu-link ">
                                <i class="menu-icon bi bi-receipt-cutoff"></i>
                                <div class="text-truncate" data-i18n="POS & Invoice">POS & Invoice</div>
                            </a>
                        </li>
                    @endcanany

                    @canany(['Product-Add', 'Product-Edit', 'Product-Delete'])
                        <li class="menu-item" id="Product">
                            <a href="{{ route('admin.product.index') }}" class="menu-link ">
                                <i class="menu-icon bi bi-box-seam-fill"></i>
                                <div class="text-truncate" data-i18n="Product">Product</div>
                            </a>
                        </li>
                    @endcanany

                    @can('Report-View')
                        <li class="menu-item" id="{{ $user->isStaffUser() ? 'StaffReports' : 'Reports' }}">
                            <a href="{{ $user->isStaffUser() ? route('admin.staff.staff-report') : route('admin.reports') }}" class="menu-link ">
                                <i class="menu-icon bi {{ $user->isStaffUser() ? 'bi-graph-up-arrow' : 'bi-bar-chart-line-fill' }}"></i>
                                <div class="text-truncate" data-i18n="Report">Report</div>
                            </a>
                        </li>
                    @endcan

                    @canany(['Role-Add', 'Role-Edit'])
                        <li class="menu-item" id="Role">
                            <a href="javascript:void(0);" class="menu-link menu-toggle">
                                <i class="menu-icon bi bi-shield-lock-fill"></i>
                                <div class="text-truncate" data-i18n="Roles & Permissions">Roles & Permissions</div>
                            </a>
                            <ul class="menu-sub">
                                <li class="menu-item sub-menu" id="Roles">
                                    <a href="{{ route('admin.roles.index') }}" class="menu-link">
                                        <div class="text-truncate" data-i18n="Roles">Roles</div>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @endcanany

                    @canany(['Service_Category-Add', 'Service_Category-Edit', 'Service_Category-Delete'])
                        <li class="menu-item" id="ServiceCategory">
                            <a href="{{ route('admin.service-category.index') }}" class="menu-link ">
                                <i class="menu-icon bi bi-ui-checks-grid"></i>
                                <div class="text-truncate" data-i18n="Service Category">Service Category</div>
                            </a>
                        </li>
                    @endcanany

                    @canany(['Service-Add', 'Service-Edit', 'Service-Delete'])
                        <li class="menu-item" id="Service">
                            <a href="{{ route('admin.service.index') }}" class="menu-link ">
                                <i class="menu-icon bi bi-scissors"></i>
                                <div class="text-truncate" data-i18n="Services">Services</div>
                            </a>
                        </li>
                    @endcanany

                    @can('Setting-Payment_Method')
                        <li class="menu-item" id="Settings">
                            <a href="{{ route('admin.setting.index') }}" class="menu-link ">
                                <i class="menu-icon bi bi-gear-fill"></i>
                                <div class="text-truncate" data-i18n="Setting">Setting</div>
                            </a>
                        </li>
                    @endcan

                    @canany(['Shop_Closures-Add', 'Shop_Closures-Edit', 'Shop_Closures-Delete'])
                        <li class="menu-item" id="ShopClosures">
                            <a href="{{ route('admin.shop-closures.index') }}" class="menu-link ">
                                <i class="menu-icon bi bi-shop-window"></i>
                                <div class="text-truncate" data-i18n="Shop Closures">Shop Closures</div>
                            </a>
                        </li>
                    @endcanany

                    @can('Slot-Update')
                        <li class="menu-item" id="Slot">
                            <a href="{{ route('admin.slot.index') }}" class="menu-link ">
                                <i class="menu-icon bi bi-clock-fill"></i>
                                <div class="text-truncate" data-i18n="Slots">Slots</div>
                            </a>
                        </li>
                    @endcan

                    @canany(['Staff-Add', 'Staff-Edit', 'Staff-Delete'])
                        <li class="menu-item" id="Staff">
                            <a href="{{ route('admin.staff.index') }}" class="menu-link ">
                                <i class="menu-icon bi bi-people-fill"></i>
                                <div class="text-truncate" data-i18n="Staff">Staff</div>
                            </a>
                        </li>
                    @endcanany

                    @if ($user->isStaffUser())
                        @can('Calender-View')
                            <li class="menu-item" id="WorkingHours">
                                <a href="{{ route('admin.working-hour.index') }}" class="menu-link ">
                                    <i class="menu-icon bi bi-hourglass-split"></i>
                                    <div class="text-truncate" data-i18n="Working Hours">Working Hours</div>
                                </a>
                            </li>
                        @endcan
                    @endif
                </ul>
            </aside>
            <!-- / Menu -->

            <!-- Layout container -->
            <div class="layout-page">
                <!-- Navbar -->

                <nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
                    id="layout-navbar">
                    <div class="navbar-nav-right d-flex align-items-center w-100" id="navbar-collapse">
                        <button type="button" class="layout-menu-toggle mobile-menu-toggle btn btn-link d-xl-none me-2"
                            aria-label="Open navigation menu">
                            <i class="bx bx-menu bx-sm"></i>
                        </button>
                        <div class="navbar-nav align-items-center flex-grow-1">
                            <div class="nav-item navbar-search-wrapper mb-0 d-flex align-items-center gap-3 w-100">
                                <div class="d-none d-md-block">
                                    <h4 class="mb-0" style="font-weight: 700;color: #0F172A;font-size: 1.1rem;">
                                        {{ $user->name ? 'Welcome, ' . explode(' ', $user->name)[0] : 'Dashboard' }}
                                    </h4>
                                </div>
                                <div class="bc-navbar-search ms-md-2">
                                    <i class="bi bi-search"></i>
                                    <input type="search" id="bcNavbarSearch" class="form-control"
                                        placeholder="Search…" aria-label="Search" autocomplete="off" />
                                </div>
                            </div>
                        </div>

                        <ul class="navbar-nav flex-row align-items-center ms-auto gap-2">
                            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                                <a class="nav-link dropdown-toggle hide-arrow p-0" href="javascript:void(0);"
                                    data-bs-toggle="dropdown">
                                    <img class="bc-avatar"
                                        src="{{ $user->profile_image_url }}"
                                        alt="{{ $user->name }}" />
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="javascript:void(0);">
                                            <div class="d-flex">
                                                <div class="flex-shrink-0 me-3">
                                                    <img class="bc-avatar"
                                                        src="{{ $user->profile_image_url }}"
                                                        alt="{{ $user->name }}" />
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-0">{{ $user->name }}</h6>
                                                    <small class="text-muted">{{ $user->role }}</small>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                    <li>
                                        <div class="dropdown-divider my-1"></div>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.profile.index') }}">
                                            <i class="bi bi-person me-2"></i><span>Profile</span>
                                        </a>
                                    </li>
                                    @can('Setting-Payment_Method')
                                        <li>
                                            <a class="dropdown-item" href="{{ route('admin.setting.index') }}">
                                                <i class="bi bi-gear me-2"></i><span>Settings</span>
                                            </a>
                                        </li>
                                    @endcan
                                    <li>
                                        <a class="dropdown-item" href="javascript:void(0);"
                                            data-bs-toggle="modal" data-bs-target="#BcChangePasswordModal">
                                            <i class="bi bi-key me-2"></i><span>Change Password</span>
                                        </a>
                                    </li>
                                    <li>
                                        <div class="dropdown-divider my-1"></div>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="javascript:void(0);"
                                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                            <i class="bi bi-box-arrow-right me-2"></i><span>Logout</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>


                    <form id="logout-form" action="{{ route('logout') }}" method="POST"
                        style="display: none;">
                        @csrf
                    </form>


                    <!-- Search Small Screens -->
                    <div class="navbar-search-wrapper search-input-wrapper d-none">
                        <input type="text" class="form-control search-input container-xxl border-0"
                            placeholder="Search..." aria-label="Search..." />
                        <i class="bx bx-x bx-md search-toggler cursor-pointer"></i>
                    </div>
                </nav>

                <!-- / Navbar -->

                @yield('content')



                <style>
                    #service-categories {
                        margin-bottom: 30px;
                    }

                    .category {
                        border: 1px solid #eee;
                        border-radius: 14px;
                        padding: 16px;
                        margin-bottom: 20px;
                        background: #fafafa;
                    }

                    .category-title {
                        font-weight: 600;
                        margin-bottom: 10px;
                        font-size: 1.1em;
                    }

                    .badge {
                        /* status badges styled by admin-saas.css */
                    }

                    .service-btn {
                        width: 100%;

                        color: #000;
                        font-size: 1em;
                        border: none;
                        border-radius: 6px;
                        padding: 12px;
                        margin-bottom: 10px;
                        text-align: left;
                        margin-top: 4px;
                        cursor: pointer;
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                        transition: background .2s;
                    }

                    .service-btn.active,
                    .service-btn:hover {
                        background: #333;
                        color: #fff;
                    }

                    .service-price {
                        font-weight: bold;
                        margin-left: 10px;
                    }

                    .selected-title {
                        font-weight: 500;
                        margin-bottom: 12px;
                        color: #181818;
                        font-size: 1.06em;
                    }

                    .selected-service {
                        background: #fff;
                        border-radius: 9px;
                        display: flex;
                        align-items: center;
                        justify-content: space-between;
                        margin-bottom: 8px;
                        padding: 12px;
                        font-size: 1.07em;
                        border: 1px solid #eee;
                    }

                    .selected-service span.service-name {
                        font-weight: 500;
                    }

                    .selected-service small {
                        color: #888;
                        font-size: 0.77em;
                        margin-left: 5px;
                    }

                    .selected-service .service-remove {
                        background: none;
                        border: none;
                        color: #db3535;
                        font-size: 1.4em;
                        cursor: pointer;
                        margin-left: 20px;
                    }

                    .selected-service .service-price {
                        margin-left: 30px;
                    }

                    .service-btn.active {
                        color: #fff;
                        border: 2px solid #181818;
                        /* Highlight border color */
                        background: #181818;
                        /* Glow effect */
                    }
                </style>
                
                <style>
                    .staff-card {
                        display: flex;
                        align-items: center;
                        background: #fff;
                        border: 2.5px solid #fff;
                        border-radius: 16px;
                        padding: 18px 20px 18px 12px;
                        box-shadow: 0 3px 13px rgba(20, 20, 20, 0.06);
                        cursor: pointer;
                        transition: border 0.18s, background 0.18s;
                        position: relative;
                        min-height: 85px;
                        border: 1px solid #eee;
                    }

       

                    .staff-photo {
                        width: 54px;
                        height: 54px;
                        background: #e9e9e9;
                        border-radius: 50%;
                        overflow: hidden;
                        margin-right: 18px;
                        flex-shrink: 0;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                    }

                    .staff-photo img {
                        width: 100%;
                        height: 100%;
                        object-fit: cover;
                        display: block;
                    }

                    .staff-details {
                        flex: 1;
                    }

                    .staff-name {
                        font-weight: 600;
                        font-size: 1.1em;
                        margin-bottom: 4px;
                    }

                    .staff-card .staff-qty {
                        font-size: 1em;
                        background: #f2f2f2;
                        border-radius: 7px;
                        padding: 1px 8px;
                        color: #333;
                        display: inline-block;
                        margin-top: 2px;
                    }

                    .staff-card.active .staff-qty {
                        background: #242424;
                        color: #fff;
                    }

                    .staff-status {
                        font-size: 0.97em;
                        font-weight: 500;
                        background: #f2f2f2;
                        color: #444;
                        border-radius: 9px;
                        padding: 4px 15px;
                        position: absolute;
                        top: 15px;
                        right: 17px;
                    }

                    .staff-card.active .staff-status {
                        background: #282828;
                        color: #ededed;
                    }
                </style>
                <style>
                    .card-block {
                        width: 99%;
                        max-width: 100%;
                    }

                    .card-section {
                        background: #fff;
                        border-radius: 16px;
                        box-shadow: 0 3px 13px rgba(40, 40, 40, 0.07);
                        margin-bottom: 24px;
                        border: 1px solid #eee;
                    }

                    .card-header-payment {
                        font-size: 1.18em;
                        border-bottom: 1px solid #f0f0f0;
                        padding: 18px 22px 10px 22px;
                        color: #222;
                        display: flex;
                        align-items: center;
                        font-weight: 400;
                        letter-spacing: 0.01em;
                    }

                    .tip-icon {
                        color: #18bb6a;
                        font-weight: 700;
                        font-size: 1.15em;
                        margin-right: 8px;
                    }

                    .card-body {
                        padding: 14px 22px 16px 22px;
                    }

                    .card-label {
                        font-weight: 600;
                        font-size: 1.03em;
                        margin-bottom: 6px;
                        display: block;
                        color: #252525;
                    }

                    .card-input,
                    .card-select {
                        width: 100%;
                        border: 1.5px solid #efefef;
                        padding: 8px 14px;
                        border-radius: 9px;
                        font-size: 1.07em;
                        background: #fafafa;
                        margin-top: 7px;
                        outline: none;
                        box-sizing: border-box;
                        transition: border 0.15s;
                    }

                    .card-input:focus,
                    .card-select:focus {
                        border: 1.5px solid #73c4ff;
                    }

                    .row-flex {
                        display: flex;
                        gap: 18px;
                        flex-wrap: wrap;
                    }

                    .form-group {
                        flex: 1 1 180px;
                        min-width: 160px;
                    }
                </style>

                <style>
                    #step-two {
                        display: none;
                    }

                    #step-four {
                        display: none;
                    }

                    #step-three {
                        display: none;
                    }
                </style>
                @if ($showCompleteOrderModal)
                <div class="modal fade" id="CompleteOrderModal" data-bs-backdrop="static" tabindex="-1">
                    <div class="modal-dialog">
                        <form class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="backDropModalTitle">Finalize Appointment</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div id="step-one">
                                    <div id="service-categories">
                                        <!-- Beard Trimmed Category -->
                                        @php
                                            $i = 1;
                                        @endphp
                                        @foreach ($servicesByCategory as $categoryId => $servicesGroup)
                                            @php
                                                $categoryName =
                                                    $servicesGroup->first()->category_name ?? 'Unknown Category';
                                            @endphp

                                            <div class="accordion mt-4" id="accordionExample">
                                                <div class="card accordion-item active">
                                                    <h2 class="accordion-header" id="heading{{ $i }}">
                                                        <button type="button" class="accordion-button"
                                                            data-bs-toggle="collapse" data-bs-target="#headingOne"
                                                            aria-expanded="true"
                                                            aria-controls="heading{{ $i }}">
                                                            {{ $categoryName }}
                                                        </button>
                                                    </h2>

                                                    <div id="heading{{ $i }}"
                                                        class="accordion-collapse collapse show"
                                                        data-bs-parent="#accordionExample">
                                                        <div class="accordion-body">
                                                            @foreach ($servicesGroup as $service)
                                                                <button type="button" class="service-btn"
                                                                    data-id="{{ $service->service_id }}"
                                                                    data-category="{{ $categoryName }}"
                                                                    data-name="{{ $service->service_name }}"
                                                                    data-price="{{ $service->price }}">
                                                                    {{ $service->service_name }} <span
                                                                        class="service-price">{{ $shopCurrencySymbol ?? '₹' }}{{ $service->price }}</span>
                                                                </button>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            @php
                                                $i++;
                                            @endphp
                                        @endforeach

                                    </div>

                                    <div class="selected-title">Selected Services</div>
                                    <div id="selected-services"></div>


                                    <div class="row mt-4">
                                        <div class="col-lg-6">
                                            <button type="button" class="btn btn-primary" disabled>Back</button>
                                        </div>
                                        <div class="col-lg-6" style="text-align: right;">
                                            <button type="button" class="btn btn-primary"
                                                onclick="StepNext('step-two','step-one')">Next</button>
                                        </div>
                                    </div>
                                </div>



                                <div id="step-two">
                                    <div class="category-title">Hair Care</div>
                                    <div class="product-list">
                                        <div class="row">
                                            @foreach ($Product as $row)
                                                <div class="col-lg-6 mb-3">
                                                    <div class="product-card" data-id="{{ $row->id }}"
                                                        data-name="fvdhfgh,hndf" data-price="25.00">
                                                        <div class="product-thumb">
                                                            <img src="{{ $row->image_url }}"
                                                                style="max-width: 100%;" alt="">

                                                        </div>
                                                        <div class="product-info">
                                                            <div class="product-name">{{ $row->product_name }}</div>
                                                        </div>
                                                        <div class="product-price">{{ $shopCurrencySymbol ?? '₹' }}{{ $row->price }}</div>
                                                    </div>
                                                </div>
                                            @endforeach


                                        </div>
                                    </div>


                                    <div class="row mt-4">
                                        <div class="col-lg-6">
                                            <button type="button" class="btn btn-primary"
                                                onclick="BackStep('step-two','step-one')">Back</button>
                                        </div>
                                        <div class="col-lg-6" style="text-align: right;">
                                            <button type="button" class="btn btn-primary"
                                                onclick="StepNext('step-three','step-two')">Next</button>
                                        </div>
                                    </div>
                                </div>



                                <div id="step-three">
                                    <div class="row" id="StaffListHtml">


                                    </div>


                                    <div class="row">
                                        <div class="col-lg-6">
                                            <button type="button" class="btn btn-primary"
                                                onclick="BackStep('step-three','step-two')">Back</button>
                                        </div>
                                        <div class="col-lg-6" style="text-align: right;">
                                            <button type="button" class="btn btn-primary"
                                                onclick="StepNext('step-four','step-three')">Next</button>
                                        </div>
                                    </div>
                                </div>

                                <div id="step-four">
                                    <div class="card-block">
                                        <div class="card-section">
                                            <div class="card-header-payment">
                                                <span class="tip-icon">$</span> Tip
                                            </div>
                                            <div class="card-body">
                                                <label class="card-label" for="tip-amount">Tip Amount</label>
                                                <input class="card-input" id="tip-amount" type="number"
                                                    value="0" min="0" />
                                            </div>
                                        </div>

                                        <div class="card-section">
                                            <div class="card-header-payment">Payment &amp; Discount</div>
                                            <div class="card-body row-flex">
                                                <div class="form-group">
                                                    <label class="card-label" for="payment-method">Payment
                                                        Method</label>
                                                    <select class="form-select select2" id="payment-method">
                                                        <option selected>Select method</option>
                                                        <option>Cash</option>
                                                        <option>Credit Card</option>
                                                        <option>UPI</option>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label class="card-label" for="discount">Discount</label>
                                                    <select class="form-select select2" id="discount">
                                                        <option selected>No Discount</option>
                                                        <option>10% Off</option>
                                                        <option>20% Off</option>
                                                        <option>Flat {{ $shopCurrencySymbol ?? '₹' }}100 Off</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-6">
                                            <button type="button" class="btn btn-primary"
                                                onclick="BackStep('step-four','step-three')">Back</button>
                                        </div>
                                        <div class="col-lg-6" style="text-align: right;">
                                            <button type="button" class="btn btn-primary">Submit</button>
                                        </div>
                                    </div>
                                </div>


                                <input type="hidden" id="sell_product_id" name="sell_product_id" value="">
                                <input type="hidden" name="staff_ids" id="staff_ids" value="">
                                <input type="hidden" id="service_ids" name="service_ids" value="">



                            </div>
                        </form>
                    </div>
                </div>


                @endif
                <style>
                    .toggle-tabs {
                        display: flex;
                        border-radius: 8px;
                        border: 1px solid #f1dfdf;
                        overflow: hidden;
                        background: #fff;
                        width: 100%;
                    }

                    .tab-btn {
                        flex: 1;
                        padding: 7px 0;
                        background: none;
                        border: none;
                        font-weight: 500;
                        font-size: 15px;
                        color: #222;
                        transition: background 0.2s, color 0.2s;
                        cursor: pointer;
                        outline: none;
                    }

                    .tab-btn.active {
                        background: #a61c1c;
                        color: #fff;
                        border-radius: 8px 0 0 8px;
                        font-weight: bold;
                    }

                    .tab-btn:not(.active) {
                        background: none;
                        color: #222;
                    }

                    .tab-btn:last-child.active {
                        border-radius: 0 8px 8px 0;
                    }

                    .slot-btn {
                        background: #eee;
                        color: #000;
                    }


                    .slot-btn.active {
                        background-color: #a61c1c !important;
                        /* Bootstrap primary color */
                        color: #fff !important;
                        border-color: #a61c1c !important;
                    }

                    .shop-close-error {
                        padding: 14px;
                        width: 100%;
                        text-align: center;
                        font-weight: 600;
                        font-size: 23px;
                        background: #eee;
                    }

                    #SlotSelectValidationError {
                        display: none;
                    }
                </style>

                <div class="modal fade" id="RescheduleAppointmentModal" data-bs-backdrop="static" tabindex="-1">
                    <div class="modal-dialog">
                        <form class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="backDropModalTitle">Reschedule Appointment</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row g-5">
                                    <!-- Calendar -->
                                    <div class="col-lg-12">
                                        {{-- <label class="mb-2 fw-semibold">Select Date</label> --}}
                                        <div class="d-inline-block bg-white border rounded p-3 w-100">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <button type="button" class="btn btn-link btn-sm px-2"
                                                    id="prevMonthBtn"><i class="fa fa-chevron-left"
                                                        style="color: #000;"></i></button>
                                                <span id="calendarMonth" class="fw-semibold">September 2025</span>
                                                <button type="button" class="btn btn-link btn-sm px-2"
                                                    id="nextMonthBtn"><i class="fa fa-chevron-right"
                                                        style="color: #000;"></i></button>
                                            </div>
                                            <table class="table table-borderless mb-0" id="calendarTable">
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
                                    <div class="col-lg-12">
                                        <div class="mb-2 fw-semibold">Select Time</div>
                                        <div class="row row-cols-3 g-2">
                                            <!--<div class="col-lg-12">-->
                                            <!--    <div class="toggle-tabs">-->
                                            <!--        <button type="button" class="tab-btn active" id="byMorning"-->
                                            <!--            onclick="SlotTime('Morning')">Morning</button>-->
                                            <!--        <button type="button" class="tab-btn" id="byEvening"-->
                                            <!--            onclick="SlotTime('Evening')">Evening</button>-->
                                            <!--    </div>-->
                                            <!--</div>-->

                                            <div class="col-lg-12 mt-4" id="timeSlotsMorning">
                                                <div class="d-flex flex-wrap gap-2" id="MorningSlotHtml">

                                                </div>
                                            </div>

                                            <!--<div class="col-lg-12 mt-4 d-none" id="timeSlotsEvening">-->
                                            <!--    <div class="d-flex flex-wrap gap-2" id="EveningSlotHtml">-->

                                            <!--    </div>-->
                                            <!--</div>-->
                                            <small style="color: red;font-size: 13px;"
                                                id="SlotSelectValidationError">Please Select Slot!!</small>



                                            <input type="hidden" id="reschedule_appointment_id"
                                                name="reschedule_appointment_id">
                                            <input type="hidden" id="final_staff_id" name="final_staff_id">
                                            <input type="hidden" id="final_staff_id" name="final_staff_id">
                                            <input type="hidden" id="final_service_id" name="final_service_id">
                                            <input type="hidden" id="selectedSlotStartValue"
                                                name="selectedSlotStartValue">
                                            <input type="hidden" id="selectedSlotEndValue"
                                                name="selectedSlotEndValue">




                                        </div>
                                    </div>
                                    <div class="col-lg-12" style="text-align: right;">
                                        <button type="button" class="btn btn-primary"
                                            onclick="SubmitRescheduleAppointment()">Submit</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>


   <style>
                    .modal-customer-section {
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                        margin: 18px 0 7px 0;
                    }

                    .customer-name {
                        font-weight: 700;
                        font-size: 1.05em;
                    }

                    .status-badge {
                        background: #e9ecef;
                        color: #222;
                        font-size: .96em;
                        font-weight: 600;
                        border-radius: 12px;
                        padding: 3px 15px 3px 15px;
                        margin-left: 10px;
                    }

                    .modal-section {
                        margin-top: 9px;
                        margin-bottom: 3px;
                    }

                    .icon-row {
                        display: flex;
                        align-items: flex-start;
                        margin-bottom: 14px;
                        gap: 10px;
                    }

                    .modal-icon {
                        font-size: 1.23em;
                        color: #232323;
                        min-width: 25px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        margin-top: 0.2em;
                    }

                    .item-label {
                        font-weight: 600;
                        font-size: 1.02em;
                        color: #181818;
                    }

                    .item-value {
                        font-size: 0.99em;
                        color: #444;
                        margin-bottom: 1px;
                    }

                    hr.modal-divider {
                        border: none;
                        border-top: 1.5px solid #ededed;
                        margin: 18px 0 14px 0;
                    }

                    .service-row {
                        font-size: 1.02em;
                        margin-top: 5px;
                        font-weight: 500;
                        color: #181818;
                    }

                    .service-name {
                        margin-right: 5px;
                    }

                    .service-price {
                        margin-left: 2px;
                        font-weight: 500;
                    }

                    .service-duration {
                        color: #848484;
                        margin-left: 8px;
                        font-size: 0.97em;
                    }

                    .service-qty {
                        display: inline-block;
                        margin-left: 6px;
                        padding: 1px 7px;
                        font-size: 0.82em;
                        font-weight: 600;
                        color: #444;
                        background: #f0f0f0;
                        border-radius: 10px;
                    }

                    .staff-service-list {
                        margin-top: 4px;
                        font-size: 0.92em;
                        color: #666;
                        line-height: 1.45;
                    }

                    .staff-assigned {
                        margin-top: 2px;
                        font-size: 0.9em;
                        color: #6b6b6b;
                    }

                    .service-total-row {
                        margin-top: 13px;
                        font-size: 1.05em;
                        font-weight: 700;
                        display: flex;
                        justify-content: space-between;
                        color: #181818;
                        letter-spacing: 0.01em;
                    }

                    .total-amount {
                        font-weight: 700;
                        color: #181818;
                    }

                    .modal-action {
                        margin-top: 26px;
                        display: block;
                        width: 110px;
                        margin-left: auto;
                        margin-right: auto;
                        padding: 10px 0;
                        border-radius: 9px;
                        border: 2px solid #191919;
                        background: #fff;
                        color: #191919;
                        font-weight: 600;
                        font-size: 1.02em;
                        transition: background .21s, color .21s;
                        cursor: pointer;
                    }

                    .modal-action:hover {
                        background: #181818;
                        color: #fff;
                    }
                </style>

                <div class="modal fade" id="AppointmentDetailsShowModal" data-bs-backdrop="static" tabindex="-1">
                    <div class="modal-dialog">
                        <form class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="backDropModalTitle">Appointment</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body" id="AppointmentDetailsHtml">

                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">
                                    Close
                                </button>
                            </div>
                        </form>
                    </div>
                </div>


                <!-- Footer -->
                <footer class="content-footer footer bg-footer-theme p-0">
                    <div class="container-xxl">
                        <div class="bc-footer">
                            <div>© {{ date('Y') }} Rosenthal Barbershop</div>
                            <div>Designed &amp; Developed by <a href="https://ivinfotech.com/" target="_blank"
                                    rel="noopener">IV Infotech</a></div>
                        </div>
                    </div>
                </footer>
                <!-- / Footer -->

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
    
    
     <!-- / Content -->
    <div class="modal fade" id="addAppointmentModal" tabindex="-1" aria-labelledby="addAppointmentLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header text-white">
                    <h5 class="modal-title" id="addAppointmentLabel">Add Appointment</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <!-- Modal Body -->
                <div class="modal-body">
                    {{--                    <div class="invoice-container"> --}}
                    {{--                        <!-- Header --> --}}
                    {{--                        <div class="header"> --}}
                    {{--                            <img src="{{ asset('assets/img/logo.png') }}" alt="Rosenthal Barbershop Logo"> --}}
                    {{--                            <div class="company-details"> --}}
                    {{--                                <h2>Rosenthal Barbershop</h2> --}}
                    {{--                                info@bluecrownbarbers.com<br> --}}
                    {{--                                +91 1234567890<br> --}}
                    {{--                                www.bluecrownbarbers.com --}}
                    {{--                            </div> --}}
                    {{--                        </div> --}}

                    {{--                        <!-- Customer & Invoice Info --> --}}
                    {{--                        <div class="customer-details"> --}}
                    {{--                            <div> --}}
                    {{--                                <strong>Customer:</strong><br> --}}
                    {{--                                Mukesh Suthar<br> --}}
                    {{--                                mukeshsthr690@gmail.com<br> --}}
                    {{--                                7296930519 --}}
                    {{--                            </div> --}}
                    {{--                            <div class="invoice-info"> --}}
                    {{--                                <strong>Invoice No:</strong> GEN-12345<br> --}}
                    {{--                                <strong>Date:</strong> 11 Sep 2025 --}}
                    {{--                            </div> --}}
                    {{--                        </div> --}}

                    {{--                        <!-- Service Table --> --}}
                    {{--                        <table class="invoice-table"> --}}
                    {{--                            <thead> --}}
                    {{--                            <tr> --}}
                    {{--                                <th>Service Name</th> --}}
                    {{--                                <th class="text-center">Qty</th> --}}
                    {{--                                <th class="text-center">Price</th> --}}
                    {{--                                <th class="text-center">Discount</th> --}}
                    {{--                                <th class="text-center">Tax</th> --}}
                    {{--                                <th class="text-center">Total</th> --}}
                    {{--                            </tr> --}}
                    {{--                            </thead> --}}
                    {{--                            <tbody> --}}
                    {{--                            <tr> --}}
                    {{--                                <td>Haircut & Styling</td> --}}
                    {{--                                <td class="text-center">1</td> --}}
                    {{--                                <td class="text-center">₹ 500.00</td> --}}
                    {{--                                <td class="text-center">₹ 50.00</td> --}}
                    {{--                                <td class="text-center">₹ 45.00</td> --}}
                    {{--                                <td class="text-center">₹ 495.00</td> --}}
                    {{--                            </tr> --}}
                    {{--                            <tr> --}}
                    {{--                                <td>Shampoo</td> --}}
                    {{--                                <td class="text-center">2</td> --}}
                    {{--                                <td class="text-center">₹ 150.00</td> --}}
                    {{--                                <td class="text-center">₹ 0.00</td> --}}
                    {{--                                <td class="text-center">₹ 27.00</td> --}}
                    {{--                                <td class="text-center">₹ 327.00</td> --}}
                    {{--                            </tr> --}}
                    {{--                            </tbody> --}}
                    {{--                        </table> --}}

                    {{--                        <!-- Totals --> --}}
                    {{--                        <table class="totals"> --}}
                    {{--                            <tr> --}}
                    {{--                                <td><strong>Subtotal:</strong></td> --}}
                    {{--                                <td>₹ 822.00</td> --}}
                    {{--                            </tr> --}}
                    {{--                            <tr> --}}
                    {{--                                <td><strong>Total Discount:</strong></td> --}}
                    {{--                                <td>-₹ 50.00</td> --}}
                    {{--                            </tr> --}}
                    {{--                            <tr> --}}
                    {{--                                <td><strong>Tax:</strong></td> --}}
                    {{--                                <td>₹ 72.00</td> --}}
                    {{--                            </tr> --}}
                    {{--                            <tr> --}}
                    {{--                                <td><strong>Tip:</strong></td> --}}
                    {{--                                <td>₹ 10.00</td> --}}
                    {{--                            </tr> --}}
                    {{--                            <tr> --}}
                    {{--                                <td><strong>Total Amount:</strong></td> --}}
                    {{--                                <td><strong>₹ 854.00</strong></td> --}}
                    {{--                            </tr> --}}
                    {{--                        </table> --}}

                    {{--                        <!-- Footer --> --}}
                    {{--                        <div class="footer"> --}}
                    {{--                            Rosenthal Barbershop – For Style & Elegance<br> --}}
                    {{--                            Need help? Contact us at info@bluecrownbarbers.com --}}
                    {{--                        </div> --}}
                    {{--                    </div> --}}
                    <div class="invoice-container" id="invoice">
                        <!-- Header -->
                        <div class="header">
                            <img src="{{ $shopLogo }}" alt="{{ $shopName }} Logo">
                            <div class="company-details">
                                <h2>{{ $shopName }}</h2>
                                {{ $siteSetting->contact_email ?? 'info@bluecrownbarbers.com' }}<br>
                                {{ $siteSetting->contact_phone ?? '+1 (587) 555-2489' }}<br>
                                {{ optional($siteSetting)->websiteHost() ?: 'www.bluecrownbarbers.com' }}
                            </div>
                        </div>

                        <!-- Customer & Invoice Info -->
                        <div class="customer-details">
                            <div id="customer-details"></div>
                            <div class="invoice-info" id="invoice-info"></div>
                        </div>

                        <!-- Service Table -->
                        <table class="invoice-table">
                            <thead>
                                <tr>
                                    <th>Service Name</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-center">Price</th>
                                    <th class="text-center">Total</th>
                                </tr>
                            </thead>
                            <tbody id="service-rows"></tbody>
                        </table>

                        <!-- Totals -->
                        <table class="totals" id="totals"></table>

                        <!-- Footer -->
                        <div class="page-break"></div>
                        <div class="footer">
                            Rosenthal Barbershop – For Style & Elegance<br>
                            Need help? Contact us at {{ $siteSetting->contact_email ?? 'info@bluecrownbarbers.com' }}
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                    <button type="button" href="javascript:void(0)" class="btn btn-success export-pdf"
                        onclick="generatePDF()">Export</button>
                    <button type="button" href="javascript:void(0)" class="btn btn-primary"
                        onclick="printInvoice()">Print</button>
                    {{--                    <button type="submit" form="appointmentForm" class="btn btn-primary sav-appointment">Save Appointment</button> --}}
                </div>

            </div>
        </div>
    </div>

    <style>
        /* Scoped invoice preview styles — avoid painting the whole admin shell */
        #addAppointmentModal body,
        #invoice {
            font-family: 'Inter', sans-serif;
        }

        .invoice-container {
            max-width: 800px;
            margin: 11px auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            padding: 20px 30px;
            color: #333;
        }

        .invoice-container .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #136207;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .invoice-container .header img {
            width: 60px;
        }

        .invoice-container .header .company-details {
            text-align: right;
            font-size: 12px;
            font-weight: 600;
        }

        .invoice-container .header .company-details h2 {
            margin: 0;
            color: #136207;
            font-size: 14px;
        }

        .invoice-container .customer-details {
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
        }

        .invoice-container .customer-details div {
            font-size: 14px;
        }

        .invoice-container .invoice-info {
            text-align: right;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .invoice-container .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .invoice-container .invoice-table thead tr {
            background: #136207;
            color: #fff;
        }

        .invoice-container .invoice-table th,
        .invoice-container .invoice-table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
            font-size: 14px;
        }

        .invoice-container .invoice-table td.text-center {
            text-align: center;
        }

        .invoice-container .invoice-table td.text-right {
            text-align: right;
        }

        .invoice-container .totals {
            margin-top: 10px;
            width: 100%;
        }

        .invoice-container .totals tr td {
            font-size: 14px;
            padding: 8px;
        }

        .invoice-container .totals tr td:last-child {
            text-align: right;
        }

        .invoice-container .footer {
            text-align: center;
            padding-top: 20px;
            border-top: 2px solid #136207;
            font-size: 12px;
            color: #555;
        }

        .invoice-container .page-break {
            page-break-before: auto;
        }

        @media print {
            body.printing-invoice {
                background: #fff;
            }

            .invoice-container {
                box-shadow: none;
                margin: 0;
            }

            .invoice-container .invoice-table thead tr {
                background: #136207;
                color: #fff;
            }
        }
    </style>

    <!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js -->

    <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/hammer/hammer.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/i18n/i18n.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/menu.js') }}"></script>

    <!-- endbuild -->

    <!-- Vendors JS -->

    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/bootstrap-select/bootstrap-select.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>

    <!-- Main JS -->
    <script src="{{ asset('assets/js/main.js') }}"></script>

    <!-- Page JS -->
    <script src="{{ asset('assets/js/forms-selects.js') }}"></script>


    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/admin-saas-datatables.css') }}?v=2" />
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>


    <script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.0/jquery.validate.min.js"></script>
    <script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.0/additional-methods.js"></script>
    <script src="{{ asset('assets/js/admin-saas.js') }}?v=5"></script>
    @stack('script')
    <script>
        function ArchiveAppointment(url) {
            Swal.fire({
                icon: 'warning',
                title: 'Archive appointment?',
                text: 'The appointment will be marked as cancelled. Its history will remain and it can be restored.',
                showCancelButton: true,
                confirmButtonText: 'Archive'
            }).then(function(result) {
                if (!result.isConfirmed) return;
                $.ajax({
                    url: url,
                    type: 'DELETE',
                    data: { _token: $('meta[name="csrf-token"]').attr('content') }
                }).done(function(response) {
                    Swal.fire('Archived', response.message, 'success').then(function() {
                        const table = $('#AppointmentDataTable');
                        if (table.length && $.fn.DataTable.isDataTable(table)) {
                            table.DataTable().ajax.reload();
                        } else {
                            location.reload();
                        }
                    });
                }).fail(function(xhr) {
                    const message = xhr.responseJSON && xhr.responseJSON.message
                        ? xhr.responseJSON.message
                        : 'The appointment could not be archived.';
                    Swal.fire('Action failed', message, 'error');
                });
            });
        }

        $(function() {
            $('.modal').each(function() {
                const title = this.querySelector('.modal-title');
                const submitButton = this.querySelector('button[type="submit"]');
                if (title) title.dataset.defaultHtml = title.innerHTML;
                if (submitButton) submitButton.dataset.defaultText = submitButton.textContent.trim();
            });

            $(document).on('hidden.bs.modal', '.modal', function() {
                const form = this.querySelector('form');
                if (!form || form.hasAttribute('data-preserve-values')) return;
                form.reset();
                $(form).find('select').trigger('change');
                $(form).find('.is-invalid').removeClass('is-invalid');
                $(form).find('[aria-invalid="true"]').removeAttr('aria-invalid');

                const flag = form.querySelector('[name="flag"]');
                const rowId = form.querySelector('[name="row_id"]');
                const method = form.querySelector('[name="_method"]');
                if (flag) flag.value = 'A';
                if (rowId) rowId.value = '';
                if (method) method.value = 'POST';

                const title = this.querySelector('.modal-title');
                const submitButton = form.querySelector('button[type="submit"]');
                if (title && title.dataset.defaultHtml) title.innerHTML = title.dataset.defaultHtml;
                if (submitButton && submitButton.dataset.defaultText) {
                    submitButton.textContent = submitButton.dataset.defaultText;
                }
            });
        });
    </script>

    {{-- Global Change Password modal (uses existing staff.change-password endpoint) --}}
    <div class="modal fade" id="BcChangePasswordModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form id="bc-self-change-password-form" class="modal-content" onsubmit="return false;">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Change Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3 bc-pass-wrap">
                        <label class="form-label" for="bc_current_password">Current Password</label>
                        <input type="password" class="form-control" id="bc_current_password" name="current_password"
                            autocomplete="current-password" placeholder="Enter current password" />
                        <button type="button" class="bc-toggle-pass" data-bc-toggle-pass="#bc_current_password"
                            aria-label="Show password"><i class="bi bi-eye"></i></button>
                    </div>
                    <div class="mb-3 bc-pass-wrap">
                        <label class="form-label" for="bc_new_password">New Password</label>
                        <input type="password" class="form-control" id="bc_new_password" name="new_password"
                            autocomplete="new-password" placeholder="Enter new password" />
                        <button type="button" class="bc-toggle-pass" data-bc-toggle-pass="#bc_new_password"
                            aria-label="Show password"><i class="bi bi-eye"></i></button>
                    </div>
                    <div class="mb-0 bc-pass-wrap">
                        <label class="form-label" for="bc_confirm_password">Confirm Password</label>
                        <input type="password" class="form-control" id="bc_confirm_password" name="confirm_password"
                            autocomplete="new-password" placeholder="Confirm new password" />
                        <button type="button" class="bc-toggle-pass" data-bc-toggle-pass="#bc_confirm_password"
                            aria-label="Show password"><i class="bi bi-eye"></i></button>
                    </div>
                    <input type="hidden" name="staff_row_id" value="{{ $user->id }}">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="bcChangePasswordBtn">Update Password</button>
                </div>
            </form>
        </div>
    </div>
    <script>
        $(function() {
            var $form = $("#bc-self-change-password-form");
            if (!$form.length) return;

            $form.validate({
                rules: {
                    current_password: { required: true },
                    new_password: { required: true, minlength: 6 },
                    confirm_password: { required: true, equalTo: "#bc_new_password" }
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

            $form.on("submit", function(e) {
                e.preventDefault();
                if (!$form.valid()) return;

                var $btn = $("#bcChangePasswordBtn");
                $btn.prop("disabled", true).addClass("loading");
                if (typeof LoaderShow === "function") LoaderShow();

                $.ajaxSetup({
                    headers: { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") }
                });

                $.ajax({
                    type: "POST",
                    url: "{{ route('admin.staff.change-password') }}",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr("content"),
                        new_password: $("#bc_new_password").val(),
                        confirm_password: $("#bc_confirm_password").val(),
                        staff_row_id: "{{ $user->id }}"
                    },
                    success: function(response) {
                        if (typeof LoaderHide === "function") LoaderHide();
                        $btn.prop("disabled", false).removeClass("loading");
                        if (response.status === 1) {
                            $("#BcChangePasswordModal").modal("hide");
                            $form[0].reset();
                            if (typeof Swal !== "undefined") {
                                Swal.fire({
                                    icon: "success",
                                    title: "Success",
                                    text: response.message || "Password changed successfully",
                                    showConfirmButton: false,
                                    timer: 2000
                                });
                            } else if (typeof bcToast === "function") {
                                bcToast("success", "Success", response.message || "Password changed successfully");
                            }
                        } else if (response.errors) {
                            var msg = Object.values(response.errors).flat().join("\n");
                            Swal.fire({ icon: "warning", title: "Validation", text: msg });
                        } else {
                            Swal.fire({
                                icon: "warning",
                                title: "Warning",
                                text: response.message || "Unable to change password"
                            });
                        }
                    },
                    error: function() {
                        if (typeof LoaderHide === "function") LoaderHide();
                        $btn.prop("disabled", false).removeClass("loading");
                        Swal.fire({
                            icon: "error",
                            title: "Error",
                            text: "Something went wrong. Please try again."
                        });
                    }
                });
            });
        });
    </script>
    
    <script>
    function viewInvoice(invoice) {
        // console.log(JSON.parse(invoice));
        var appointment = invoice;

        document.getElementById("customer-details").innerHTML = "";
        document.getElementById("customer-details").innerHTML = `
    <strong>Customer:</strong><br>
    ${appointment.customer.customer_name}<br>
    ${appointment.customer.customer_name == "Guest Customer" ? '' : appointment.customer.customer_email}<br>
    ${appointment.customer.customer_name == "Guest Customer" ? '' : appointment.customer.customer_phone}
`;
        document.getElementById("invoice-info").innerHTML = "";

        document.getElementById("invoice-info").innerHTML = `
    <strong>Invoice No:</strong> ${appointment.invoice_number}<br>
    <strong>Date:</strong> ${new Date(appointment.updated_at).toLocaleDateString()}
`;

        // ✅ Render services
        document.getElementById("service-rows").innerHTML = "";
        const serviceRows = document.getElementById("service-rows");
        let subtotal = 0;
        let servicesubtotal = 0;
        appointment.services.forEach(service => {
            // subtotal= parseFloat(subtotal)+parseFloat(service.total_amt);
            servicesubtotal = parseFloat(servicesubtotal) + parseFloat(service.total_amt);
            serviceRows.innerHTML += `
        <tr>
            <td>Service #${service.service.service_name}</td>
            <td class="text-center">1</td>
            <td class="text-center">${window.SHOP_CURRENCY_SYMBOL || '₹'} ${parseFloat(service.total_amt).toFixed(2)}</td>
            <td class="text-right">${window.SHOP_CURRENCY_SYMBOL || '₹'} ${parseFloat(service.total_amt).toFixed(2)}</td>
        </tr>
    `;
        });

        let serviceSubtotalWithDiscount = servicesubtotal;
        let serviceDiscount = 0;

        if (parseInt(appointment.service_discount) > 0) {
            // console.log('dsdsd');
            serviceDiscount = servicesubtotal * appointment.service_discount / 100;
            serviceSubtotalWithDiscount = servicesubtotal - (servicesubtotal * appointment.service_discount / 100);
        }

        var service_html = ``;


        if(servicesubtotal){
service_html += `<tr style="color: red">
            <td colspan="3" >Total Services Amount</td>
            <td class="text-right">${window.SHOP_CURRENCY_SYMBOL || '₹'} ${parseFloat(servicesubtotal||0).toFixed(2)} </td>
        </tr>`;
        }


        if(serviceDiscount){
service_html += `<tr style="color: red">
            <td colspan="3" >Service Discount</td>
            <td class="text-right">(${window.SHOP_CURRENCY_SYMBOL || '₹'} ${parseFloat(serviceDiscount||0).toFixed(2)}) ${parseFloat(appointment.service_discount||0).toFixed(2)}% </td>
        </tr>`;
        }


        if(serviceSubtotalWithDiscount){
service_html += `<tr style="color: red">
            <td colspan="3">Total Services Payable</td>
            <td class="text-right">${window.SHOP_CURRENCY_SYMBOL || '₹'} ${parseFloat(serviceSubtotalWithDiscount).toFixed(2)}</td>
        </tr>`;
        }

        serviceRows.innerHTML += service_html;

        let productsubtotal = 0;

        appointment.products.forEach(product => {
            productsubtotal = parseFloat(productsubtotal) + parseFloat(product.amount);
            serviceRows.innerHTML += `
        <tr>
            <td>Product #${product.product.product_name}</td>
            <td class="text-center">1</td>
            <td class="text-center">${window.SHOP_CURRENCY_SYMBOL || '₹'} ${parseFloat(product.amount).toFixed(2)}</td>
            <td class="text-right">${window.SHOP_CURRENCY_SYMBOL || '₹'} ${parseFloat(product.amount).toFixed(2)}</td>
        </tr>
    `;
        });


        let productSubtotalWithDiscount = productsubtotal;
        let productDiscount = 0;
        if (parseInt(appointment.product_discount) > 0) {
            console.log('dsdsd');
            productDiscount = (productsubtotal * appointment.product_discount / 100);
            productSubtotalWithDiscount = productsubtotal - (productsubtotal * appointment.product_discount / 100);
        }
        let html = "";

        if (productsubtotal) {
            html += `
        <tr style="color: red">
            <td colspan="3">Total Product Amount</td>
            <td class="text-right">${window.SHOP_CURRENCY_SYMBOL || '₹'} ${parseFloat(productsubtotal || 0).toFixed(2)}</td>
        </tr>
    `;
        }

        if (productDiscount) {
            html += `
    <tr style="color: red">
        <td colspan="3">Product Discount</td>
        <td class="text-right">(${window.SHOP_CURRENCY_SYMBOL || '₹'} ${parseFloat(productDiscount || 0).toFixed(2)}) ${parseFloat(appointment.product_discount || 0).toFixed(2)}%</td>
    </tr>
`;
        }


        if (productSubtotalWithDiscount) {
            html += `<tr style="color: red">
        <td colspan="3">Total Product Payable</td>
        <td class="text-right">${window.SHOP_CURRENCY_SYMBOL || '₹'} ${parseFloat(productSubtotalWithDiscount).toFixed(2)}</td>
    </tr>`
        }



        serviceRows.innerHTML += html;

        // ✅ Render totals
        const totals = document.getElementById("totals");
        const grandTotal = appointment.total_amt + appointment.total_tax + appointment.total_tip - (productDiscount +
            serviceDiscount);
        subtotal = productSubtotalWithDiscount + serviceSubtotalWithDiscount;
        totals.innerHTML = `
    <tr><td><strong>Subtotal:</strong></td><td class="text-right">${window.SHOP_CURRENCY_SYMBOL || '₹'} ${subtotal.toFixed(2)}</td></tr>
    <tr class="d-none"><td><strong>Total Discount:</strong></td><td>-${window.SHOP_CURRENCY_SYMBOL || '₹'} ${appointment.total_discount.toFixed(2)}</td></tr>
    <tr><td><strong>Tax:</strong></td><td>${window.SHOP_CURRENCY_SYMBOL || '₹'} ${appointment.total_tax.toFixed(2)}</td></tr>
    <tr><td><strong>Tip:</strong></td><td>${window.SHOP_CURRENCY_SYMBOL || '₹'} ${appointment.total_tip.toFixed(2)}</td></tr>
    <tr><td><strong>Total Amount:</strong></td><td><strong>${window.SHOP_CURRENCY_SYMBOL || '₹'} ${appointment.total_amt.toFixed(2)}</strong></td></tr>
`;

        $('#addAppointmentModal').modal('show');
    }

    function printInvoice() {
        var printContents = document.getElementById('invoice').innerHTML;

        // Open new window
        var printWindow = window.open('', '', 'height=800,width=1200');

        printWindow.document.write('<html><head><title>Invoice</title>');

        // Add all stylesheets from the main page
        document.querySelectorAll('link[rel="stylesheet"], style').forEach(function(node) {
            printWindow.document.write(node.outerHTML);
        });

        printWindow.document.write('</head><body>');
        printWindow.document.write(printContents);
        printWindow.document.write('</body></html>');

        printWindow.document.close();

        // Wait for content and styles to load before printing
        printWindow.onload = function() {
            printWindow.focus();
            printWindow.print();
            printWindow.close();
        };
    }


    function generatePDF() {
        var oldHtml = $('.export-pdf').text();
        $('.export-pdf').prop('disabled', true).html(
            `<span class="spinner-border spinner-border-sm me-2"></span>Proccessing..`)
        const element = document.getElementById('invoice');

        const opt = {
            // 0,
            filename: 'Blue-Crown-Barbers-invoice.pdf',
            image: {
                type: 'jpeg',
                quality: 1
            },
            html2canvas: {
                scale: 2,
                useCORS: true,
                scrollY: 0
            },
            pagebreak: {
                mode: ['css', 'legacy']
            },
            jsPDF: {
                unit: 'mm',
                format: 'a4',
                orientation: 'portrait'
            }
        };

        html2pdf().set(opt).from(element).save().then(() => {
                // ✅ Only reset after PDF generation completes
                $('.export-pdf').prop('disabled', false).html(oldHtml);
            })
            .catch((err) => {
                console.error("PDF generation failed", err);
                $('.export-pdf').prop('disabled', false).html(oldHtml);
            });
        // $('.export-pdf').prop('disabled',false).html(oldHtml);
    }

    function closeInvoice() {
        // In a real implementation, this would close the modal/popup
        alert("Invoice closed. In a real application, this would close the popup.");
    }
</script>


 <script>
        function ViewAppointment(appointment_id) {
            var data = {
                'appointment_id': appointment_id
            };

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: "POST",
                url: "{{ route('admin.appointment.get-appointment-details') }}",
                data: data,
                dataType: "json",
                success: function(response) {
                    LoaderHide();

                    if (response.status === 1) {
                        let appointment = response.appointment;
                        let servicesGrouped = response.services_grouped || [];
                        let staffGrouped = response.staff_grouped || [];
                        let services = response.services || [];

                        let staffHtml = '';
                        let ServiceHtml = '';
                        let subtotal = 0;

                        if (staffGrouped.length) {
                            staffGrouped.forEach(function(staff) {
                                let serviceLines = (staff.services || []).map(function(svc) {
                                    let qtyLabel = svc.qty > 1 ? ' &times; ' + svc.qty : '';
                                    return svc.service_name + qtyLabel;
                                }).join(', ');
                                let displayName = staff.is_first_available ? 'First available' : (staff.staff_name || 'Staff');
                                let assignedHtml = '';
                                if (staff.is_first_available && staff.assigned_staff_name) {
                                    assignedHtml = `<div class="staff-assigned">Assigned: ${staff.assigned_staff_name}</div>`;
                                }

                                staffHtml += `<div class="icon-row">
                        <span class="modal-icon">&#128100;</span>
                        <div>
                            <div class="item-label">Staff</div>
                            <div class="item-value">${displayName}</div>
                            ${assignedHtml}
                            ${serviceLines ? `<div class="staff-service-list">${serviceLines}</div>` : ''}
                        </div>
                    </div>`;
                            });
                        } else {
                            services.forEach(function(element) {
                                staffHtml += `<div class="icon-row">
                        <span class="modal-icon">&#128100;</span>
                        <div>
                            <div class="item-label">Staff</div>
                            <div class="item-value">${element.staff_name || 'Staff'}</div>
                        </div>
                    </div>`;
                            });
                        }

                        if (servicesGrouped.length) {
                            servicesGrouped.forEach(function(element) {
                                subtotal += parseFloat(element.line_total ?? 0);
                                let qtyBadge = element.qty > 1
                                    ? `<span class="service-qty">Qty: ${element.qty}</span>`
                                    : '';
                                let durationLabel = element.duration_total ?? element.duration ?? 0;

                                ServiceHtml += `<div class="icon-row">
                        <span class="modal-icon">&#128736;</span>
                        <div>
                            <div class="item-label">Service</div>
                            <div class="service-row">
                                <span class="service-name">${element.service_name}</span>${qtyBadge}
                                <span class="service-price">${window.SHOP_CURRENCY_SYMBOL||'₹'}${parseFloat(element.line_total).toFixed(2)}</span>
                                <span class="service-duration">&middot; ${durationLabel} min</span>
                            </div>
                        </div>
                    </div>`;
                            });
                        } else {
                            services.forEach(function(element) {
                                subtotal += parseFloat(element.price ?? 0);
                                ServiceHtml += `<div class="icon-row">
                        <span class="modal-icon">&#128736;</span>
                        <div>
                            <div class="item-label">Service</div>
                            <div class="service-row">
                                <span class="service-name">${element.service_name}</span>
                                <span class="service-price">${window.SHOP_CURRENCY_SYMBOL||'₹'}${parseFloat(element.price).toFixed(2)}</span>
                                <span class="service-duration">&middot; ${element.duration ?? 0} min</span>
                            </div>
                        </div>
                    </div>`;
                            });
                        }

                        let totalAmt = parseFloat(appointment.total_amt ?? 0);
                        if (!totalAmt || totalAmt <= 0) {
                            totalAmt = subtotal;
                        }

                        ServiceHtml += `<div class="service-total-row">
                                    <span>Total</span>
                                    <span class="total-amount">${window.SHOP_CURRENCY_SYMBOL||'₹'}${totalAmt.toFixed(2)}</span>
                                </div>`;

                        // Customer + appointment info
                        let ViewCustomerInfo = `
                    <div class="modal-customer-section">
                        <span class="customer-name">${appointment.client_name}</span>
                        ${getStatusBadge(appointment.status)}
                    </div>

                    <div class="modal-section">
                        <div class="icon-row">
                            <span class="modal-icon">&#128197;</span>
                            <div>
                                <div class="item-label">Date</div>
                                <div class="item-value">${appointment.date}</div>
                            </div>
                        </div>
                        <div class="icon-row">
                            <span class="modal-icon">&#128337;</span>
                            <div>
                                <div class="item-label">Time</div>
                                <div class="item-value">${appointment.slot_start} - ${appointment.slot_end}</div>
                            </div>
                        </div>
                        ${staffHtml}
                    </div>

                    <hr class="modal-divider" />

                    <div class="modal-section">
                        ${ServiceHtml}
                    </div>
                `;

                        $("#AppointmentDetailsHtml").html(ViewCustomerInfo);
                        $("#AppointmentDetailsShowModal").modal('show');

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
        function getStatusBadge(status) {
    const statusMap = {
        0: { label: 'Pending',    class: 'bg-warning' },
        1: { label: 'Complete',   class: 'bg-success' },
        2: { label: 'Reject',     class: 'bg-danger' },
        3: { label: 'Confirm',    class: 'bg-primary' },
        4: { label: 'Reschedule', class: 'bg-info' },
    };

    const s = statusMap[status] || { label: 'Unknown', class: 'bg-secondary' };

    return `<span class="badge ${s.class} p-2">${s.label}</span>`;
}
    </script>
    <script>
        // Service selection logic
        var selectedServices = {};

        function StepNext(nxt_step, lst_step) {

            if (nxt_step == "step-two") {
                GetStaffDetails();
            }
            $("#" + nxt_step).show();
            $("#" + lst_step).hide();
        }

        function BackStep(currect_step, back_step) {
            $("#" + back_step).show();
            $("#" + currect_step).hide();
        }

        function RescheduleAppointment(appointment_id, staff_ids, service_ids) {
            $("#reschedule_appointment_id").val(appointment_id)
            $("#final_staff_id").val(staff_ids)

            $("#final_service_id").val(service_ids)
            $("#RescheduleAppointmentModal").modal('show');

            const currentDate = new Date();
            let selectedDay = currentDate.getDate();
            selectDay(selectedDay);
        }


        function SubmitRescheduleAppointment() {
            var appointment_id = $("#reschedule_appointment_id").val();
            var staff_id = $("#final_staff_id").val();
            var service_id = $("#final_service_id").val();
            var slot_start = $("#selectedSlotStartValue").val();
            var slot_end = $("#selectedSlotEndValue").val();


            if (!slot_start) {

                $("#SlotSelectValidationError").show()
                return false;
            }

            $("#SlotSelectValidationError").hide()

            let dateObj = new Date(selectedYear, selectedMonth, selectedDay);

            // Pad month/day with leading zero
            let year = dateObj.getFullYear();
            let month = String(dateObj.getMonth() + 1).padStart(2, '0'); // add 1 because month is 0-based
            let day = String(dateObj.getDate()).padStart(2, '0');

            let selectedDate = `${year}-${month}-${day}`;


            var data = {
                'appointment_id': appointment_id,
                'staff_id': staff_id,
                'service_id': service_id,
                'slot_start': slot_start,
                'slot_end': slot_end,
                'date': selectedDate,
            }

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: "POST",
                url: "{{ route('admin.appointment.submit-reschedule-appointment') }}",
                data: data,
                dataType: "json",
                success: function(response) {
                    LoaderHide();
                    $("#RescheduleAppointmentModal").modal('hide');
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

        // Calendar rendering
        const currentDate = new Date();
        let selectedYear = currentDate.getFullYear();
        let selectedMonth = currentDate.getMonth(); // 0-based (Jan = 0)
        let selectedDay = currentDate.getDate(); // Today's date by default
        let dayName = new Intl.DateTimeFormat('en-US', {
            weekday: 'long'
        }).format(currentDate);

        function SlotTime(typ) {
            let byMorning = document.getElementById('byMorning');
            let byEvening = document.getElementById('byEvening');
            let timeSlotsMorning = document.getElementById('timeSlotsMorning');
            let timeSlotsEvening = document.getElementById('timeSlotsEvening');

            if (typ === 'Morning') {
                timeSlotsMorning.classList.remove('d-none');
                timeSlotsEvening.classList.add('d-none');
                byMorning.classList.add('active');
                byEvening.classList.remove('active');
            } else {
                timeSlotsMorning.classList.add('d-none');
                timeSlotsEvening.classList.remove('d-none');
                byMorning.classList.remove('active');
                byEvening.classList.add('active');
            }
        }

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
                td.innerHTML = `<button type="button" class="btn btn-sm px-2 py-1 ${day === selectedDay ? 'btn-danger text-white fw-semibold' : 'btn-light'}"
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
            $("#SlotSelectValidationError").hide()

            selectedDay = day; // सिर्फ selectedDay update होगा
            var final_staff_id = $("#final_staff_id").val();
            var final_service_id = $("#final_service_id").val();


            let selectedDate = new Date(selectedYear, selectedMonth, selectedDay); // month is 0-based

            // Get full day name
            let dayName = new Intl.DateTimeFormat('en-US', {
                weekday: 'long'
            }).format(selectedDate);

            var data = {
                'selectedYear': selectedYear,
                'selectedMonth': selectedMonth,
                'selectedDay': selectedDay,
                'staff_id': final_staff_id,
                'service_id': final_service_id,
                'day_name': dayName
            }


            console.log(data);

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: "POST",
                url: "{{ route('get-slot-by-staff') }}",
                data: data,
                dataType: "json",
                success: function(response) {
                    console.log(response);
                    if (response.status == 1) {


// Render morning slots
                        if (response.slots && response.slots.length) {
                            let MorningSlotHtml = '';
                            response.slots.forEach(slot => {
                                const slotText = slot.start; // only start time
                                slotTimes.push(slotText); // add to global array

                                MorningSlotHtml += `
            <div class="col-auto">
                <button type="button"
                        class="btn btn-outline-light border text-start d-flex align-items-center slot-btn"
                        onclick="selectSlot(this, '${slot.start}', '${slot.end}')">
                    <i class="bx bx-time me-1"></i> ${slotText}
                </button>
            </div>
        `;
                            });

                            $("#MorningSlotHtml").html(MorningSlotHtml); // reset container
                        }

                        selectedDay = day;
                        renderCalendar();
                    } else {
                        $("#MorningSlotHtml").html("<p class='shop-close-error'>" + response.message + "</p>");
                        $("#EveningSlotHtml").html("<p class='shop-close-error'>" + response.message + "</p>");
                    }


                }
            });
            return false;
        }

        function selectSlot(button, start, end) {
            // Remove 'active' class from all buttons
            document.querySelectorAll('.slot-btn').forEach(btn => {
                btn.classList.remove('active');
            });

            // Add 'active' class to clicked button
            button.classList.add('active');

            // Store selected value in hidden inputs
            document.getElementById('selectedSlotStartValue').value = start; // start time
            document.getElementById('selectedSlotEndValue').value = end; // end time
        }

        window.selectDay = selectDay;


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




        function UpdateAppointmentStatus(typ, appointment_id) {
            var msg = "";
            if (typ == "Confirm") {
                msg = "Are you sure you want to confirm this appointment?";
            } else if (typ == "Cancel") {
                msg = "Are you sure you want to cancel this appointment?";
            }


            Swal.fire({
                title: 'Are you sure?',
                text: msg,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, confirm!',
                customClass: {
                    confirmButton: 'btn btn-primary me-3',
                    cancelButton: 'btn btn-label-secondary'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    var status = 0;
                    if (typ == "Confirm") {
                        status = 3;
                    } else if (typ == "Cancel") {
                        status = 2;
                    }

                    var data = {
                        'appointment_id': appointment_id,
                        'status': status,
                    }

                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });

                    $.ajax({
                        type: "POST",
                        url: "{{ route('admin.appointment.confirm-reject-action') }}",
                        data: data,
                        dataType: "json",
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

        function GetStaffDetails() {
            var service_ids = $("#service_ids").val();
            if (!service_ids) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning',
                    text: "Please Select Service!",
                    showConfirmButton: false,
                    timer: 2000
                });
                return false;
            }

            var data = {
                'service_ids': service_ids
            }

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: "POST",
                url: "{{ route('admin.appointment.get-staff-by-service') }}",
                data: data,
                dataType: "json",
                success: function(response) {
                    LoaderHide();
                    if (response.status === 1) {
                        let staff = response.staff;


                        var staffCardHtml = ``;
                        staff.forEach(function(s) {
                            staffCardHtml += `
            <div class="col-lg-6 mb-3">
                <div class="staff-card" data-id="${s.staff_id}">
                    <div class="staff-photo">
                        <img src="{{ asset('assets/img/user.png') }}" style="max-width: 100%;" alt="Staff" />
                    </div>
                    <div class="staff-details">
                        <div class="staff-name">${s.staff_name}</div>
                    </div>
                </div>
            </div>
        `;

                        });

                        $("#StaffListHtml").append(staffCardHtml);
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

        function CompleteAppointment(appointment_id) {
            var data = {
                'appointment_id': appointment_id
            }

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: "POST",
                url: "{{ route('admin.appointment.get-customer-appointment-details') }}",
                data: data,
                dataType: "json",
                success: function(response) {
                    LoaderHide();
                    if (response.status === 1) {
                        let appointment = response.appointment;
                        let services = response.services;

                        console.log(services);

                        // Clear previous selection
                        $("#service-categories .service-btn").removeClass("active");

                        // Add services to selectedServices and mark active
                        services.forEach(function(s) {
                            const btn = $("#service-categories .service-btn[data-id='" + s.service_id +
                                "']");
                            btn.addClass("active");

                            selectedServices[s.service_id] = {
                                id: s.service_id,
                                name: btn.data('name'),
                                price: btn.data('price'),
                                category: btn.data('category')
                            };
                        });

                        // Update hidden input and render selected services
                        updateHiddenInput();

                        // Show modal
                        $("#CompleteOrderModal").modal('show');
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


        // Event delegation for dynamically added staff cards
        document.addEventListener('click', function(e) {
            if (e.target.closest('.staff-card')) {
                const card = e.target.closest('.staff-card');

                // Remove active from all cards
                document.querySelectorAll('.staff-card.active').forEach(el => el.classList.remove('active'));

                // Add active to the clicked card
                card.classList.add('active');

                // Store selected staff_id
                document.getElementById('staff_ids').value = card.getAttribute('data-id');
            }
        });


        // Store a single selected product ID (as in screenshot; for multi-select, adapt to allow multiple)
        let selectedProductId = null;

        document.querySelectorAll('.product-card').forEach(card => {
            card.onclick = function() {
                // Deselect all first
                document.querySelectorAll('.product-card.active').forEach(activeCard => activeCard.classList
                    .remove('active'));
                // Select this if not already active
                if (selectedProductId !== this.dataset.id) {
                    this.classList.add('active');
                    selectedProductId = this.dataset.id;
                    document.getElementById('sell_product_id').value = selectedProductId;
                } else {
                    // Deselect (optional: comment these lines if always one must be selected)
                    selectedProductId = null;
                    document.getElementById('sell_product_id').value = '';
                }
            };
        });



        function updateHiddenInput() {
            const allIds = Object.keys(selectedServices);
            document.getElementById('service_ids').value = allIds.join(',');
            renderSelected();
        }

        function renderSelected() {
            const container = document.getElementById('selected-services');
            container.innerHTML = '';
            Object.values(selectedServices).forEach(item => {
                const div = document.createElement('div');
                div.className = 'selected-service';
                div.innerHTML = `<span class="service-name">${item.name}
    <small>Services</small>
    </span>
    <span class="service-price">${window.SHOP_CURRENCY_SYMBOL||'₹'}${Number(item.price).toFixed(2)}</span>
    <button type="button" class="service-remove" data-id="${item.id}" title="Remove Service">&times;</button>
  `;
                container.appendChild(div);
            });
            // Remove handler
            document.querySelectorAll('.service-remove').forEach(btn => {
                btn.onclick = function() {
                    const id = this.getAttribute('data-id');
                    delete selectedServices[id];
                    document.querySelector(`.service-btn[data-id="${id}"]`).classList.remove('active');
                    updateHiddenInput();
                };
            });
        }

        document.querySelectorAll('.service-btn').forEach(btn => {
            btn.onclick = function() {
                const id = this.getAttribute('data-id');
                if (selectedServices[id]) {
                    delete selectedServices[id];
                    this.classList.remove('active');
                } else {
                    selectedServices[id] = {
                        id: id,
                        name: btn.getAttribute('data-name'),
                        price: btn.getAttribute('data-price'),
                        category: btn.getAttribute('data-category')
                    };
                    this.classList.add('active');
                }
                updateHiddenInput();
            }
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#TodayAppointmentsDataTable').DataTable();
            $('#UpcomingAppointmentsDataTable').DataTable();
            $('#TopServicesDataTable').DataTable();
            $('#TopStaffDataTable').DataTable();
            $('#InvoiceDatatable').DataTable();

            $('#ReportPaymentMethod').DataTable();
            $('#CategoryPerformanceTable').DataTable();
            $('#StaffPerformanceTable').DataTable();


        });

        function LoaderShow() {
            document.getElementById('Loader').style.display = 'block';
        }

        function LoaderHide() {
            document.getElementById('Loader').style.display = 'none';
        }
    </script>

    <script>
        //set active menu
        function setActive() {
            if (!page_active_id || typeof page_active_id !== 'string') return;

            let parts = page_active_id.split("-");

            // Validate parts[0] exists and is not empty
            if (!parts[0]) return;

            // Clear previous active/open classes
            $(".menu-inner .menu-item").removeClass("active open");
            $(".menu-inner .menu-item#" + parts[0]).addClass("active open");

            // Optional: Check if parts[1] exists before applying submenu highlight
            if (parts.length > 1 && parts[1]) {
                $(".menu-inner .menu-sub .sub-menu").removeClass("active open");
                $(".menu-inner .menu-sub .sub-menu#" + parts[1]).addClass("active open");
            }
        }


        setActive();
    </script>
</body>

</html>
