@push('css')
    <style>
        .customer-info-box {
            /* background: #fff; */
            /* border-radius: 12px; */
            /* box-shadow: 0 2px 15px rgba(44, 62, 80, .07); */
            /* border: 1px solid #eee; */
            /* max-width: 530px; */
            /* margin: 30px auto 0 auto; */
            /* padding: 28px 26px 22px 26px; */
        }

        .customer-info-header {
            font-size: 1.3rem;
            font-weight: 500;
            margin-bottom: 18px;
        }

        .customer-info-tabs {
            display: flex;
            margin-bottom: 18px;
        }

        .tab-btn {
            flex: 1;
            padding: 10px 0;
            outline: none;
            background: #f7f7f7;
            border: none;
            font-size: 1rem;
            font-weight: 500;
            color: #1a1a1a;
            cursor: pointer;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
            border: 2px solid transparent;
            transition: background .18s, border .18s;
        }

        .tab-btn.active {
            background: #fff;
            border-color: #222 #222 #fff #222;
            color: #222;
            font-weight: bold;
            z-index: 2;
        }

        .tab-btn:not(.active) {
            background: #f5f5f5;
            color: #aaa;
        }

        .customer-form {
            background: #fff;
            padding: 15px 0 0 0;
        }

        .customer-form label {
            margin-bottom: 3px;
            font-weight: 500;
            display: block;
        }

        .customer-form .form-control {
            width: 100%;
            padding: 9px 11px;
            border: 1.2px solid #dedede;
            margin-bottom: 13px;
            border-radius: 6px;
            font-size: 1rem;
        }

        .form-text {
            font-size: 0.96em;
            color: #868686;
            margin-bottom: 13px;
            margin-left: 2px;
        }

        .guest-btn {
            background: #136207;
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 10px 28px;
            font-weight: 600;
        }

        .guest-btn:hover {
            background: #0a3d04;
            color: #fff;
        }

        #previous-invoices-wrap {
            margin-top: 22px;
            text-align: left;
        }

        #previous-invoices-wrap .bc-prev-inv-title {
            font-size: 1.05rem;
            font-weight: 600;
            margin-bottom: 10px;
            color: #0F172A;
        }

        #previous-invoices-table {
            width: 100%;
            margin-bottom: 0;
        }

        #previous-invoices-table th {
            white-space: nowrap;
            font-size: .9rem;
            background: #F8FAFC;
        }

        #previous-invoices-table td {
            vertical-align: middle;
            font-size: .92rem;
        }

        .bs-stepper .step.active .bs-stepper-circle {
            background-color: #136207;
            color: #fff;
            box-shadow: 0 0.125rem 0.25rem 0 rgb(19 98 7 / 20%);
        }

        .bs-stepper .step.active .bs-stepper-icon i .bs-stepper-title,
        .bs-stepper .step.active .bs-stepper-label .bs-stepper-title {
            color: #136207;
        }

        .bs-stepper .step.crossed .step-trigger .bs-stepper-circle {
            background-color: rgb(19 98 7 / 39%) !important;
            color: #136207 !important;
        }

        .bs-stepper .bs-stepper-header .step.crossed+.line i {
            color: #136207;
        }

        .next-btn-invoice {
            background: #136207;
            border-color: #136207;
            box-shadow: 0 0.125rem 0.25rem 0 rgb(19 98 7 / 20%);
        }

        .next-btn-invoice:focus {
            background: #136207 !important;
            border-color: #136207 !important;
            box-shadow: 0 0.125rem 0.25rem 0 rgb(19 98 7 / 20%) !important;
        }

        .next-btn-invoice:hover {
            background: #136207 !important;
            border-color: #136207 !important;
            box-shadow: 0 0.125rem 0.25rem 0 rgb(19 98 7 / 20%) !important;
        }

        .invoice-step-nav {
            margin-bottom: 16px;
        }
    </style>
@endpush


@extends('admin.layout.layout')
@section('content')
    <script>
        page_active_id = "Master-Category";
    </script>


    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="bc-page-header">
            <div>
                <h4>{{ $invoiceTitle ?? 'Edit Invoice' }}</h4>
                <p>{{ $invoiceSubtitle ?? 'Update an existing POS invoice with the same step-by-step wizard' }}</p>
            </div>
        </div>

        <!-- Default -->
        <div class="row">
            <!-- Default Wizard -->
            <div class="col-12 mb-6">
                <div class="bs-stepper wizard-numbered mt-2">
                    <div class="bs-stepper-header">
                        <div class="step" data-target="#customer-details">
                            <button type="button" class="step-trigger">
                                <span class="bs-stepper-circle">1</span>
                                <span class="bs-stepper-label">
                                    <span class="bs-stepper-title">Customer</span>
                                    <span class="bs-stepper-subtitle">Add Customer Details</span>
                                </span>
                            </button>
                        </div>
                        <div class="line">
                            <i class="bx bx-chevron-right"></i>
                        </div>
                        <div class="step" data-target="#order-service">
                            <button type="button" class="step-trigger">
                                <span class="bs-stepper-circle">2</span>
                                <span class="bs-stepper-label">
                                    <span class="bs-stepper-title">Services &amp; Products</span>
                                    <span class="bs-stepper-subtitle">Select items to sell</span>
                                </span>
                            </button>
                        </div>
                        <div class="line">
                            <i class="bx bx-chevron-right"></i>
                        </div>
                        <div class="step" data-target="#staff-details">
                            <button type="button" class="step-trigger">
                                <span class="bs-stepper-circle">3</span>
                                <span class="bs-stepper-label">
                                    <span class="bs-stepper-title">Staff</span>
                                    <span class="bs-stepper-subtitle">Add Service Staff</span>
                                </span>
                            </button>
                        </div>
                        <div class="line">
                            <i class="bx bx-chevron-right"></i>
                        </div>
                        <div class="step" data-target="#order-summary">
                            <button type="button" class="step-trigger">
                                <span class="bs-stepper-circle">4</span>
                                <span class="bs-stepper-label">
                                    <span class="bs-stepper-title">Summary</span>
                                    <span class="bs-stepper-subtitle">Add Order Summary</span>
                                </span>
                            </button>
                        </div>
                    </div>
                    <div class="bs-stepper-content">
                        <form onSubmit="return false">
                            <!-- Account Details -->
                            <div id="customer-details" class="content">

                                <div class="row g-6">
                                    <div class="col-12 d-flex justify-content-between invoice-step-nav">
                                        <button class="btn btn-label-secondary btn-prev next-btn-invoice" disabled>
                                            <i class="bx bx-left-arrow-alt bx-sm ms-sm-n2 me-sm-2"></i>
                                            <span class="align-middle d-sm-inline-block d-none">Previous</span>
                                        </button>
                                        <button class="btn btn-primary btn-next next-btn-invoice">
                                            <span class="align-middle d-sm-inline-block d-none me-sm-2">Next</span>
                                            <i class="bx bx-chevron-right bx-sm me-sm-n2"></i>
                                        </button>
                                    </div>
                                    <div class="customer-info-box">
                                        <div class="customer-info-header">Customer Information</div>
                                        <div class="customer-info-tabs">
                                            <button id="tab-search" class="tab-btn active">Search Customer</button>
                                            <button id="tab-new" class="tab-btn">New Customer</button>
                                        </div>
                                        <div id="customer-search-form" class="customer-form">
                                            <label>Phone Number</label>
                                            <input type="hidden" name="customer_id" id="customer_id"
                                                value="{{ optional($customer)->customer_id }}">
                                            <input type="hidden" name="appointment_id" id="appointment_id"
                                                value="{{ optional($appointment)->appointment_id }}">
                                            <input type="text" class="form-control" id="search-contact"
                                                placeholder="Enter phone number"
                                                value="{{ optional($customer)->customer_phone ?? optional($appointment)->phone }}">
                                            <style>
                                                .customer-card {
                                                    border-radius: 16px;
                                                    border: 2px solid #4a90e2;
                                                    /* soft blue border */
                                                    padding: 16px 20px;
                                                    width: 50%;
                                                    max-width: 280px;
                                                    cursor: pointer;
                                                    /*display: grid;*/
                                                    /*gap: 6px;*/
                                                    justify-content: center;
                                                    background: #f9fbff;
                                                    /* light background */
                                                    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
                                                    transition: all 0.3s ease-in-out;
                                                }

                                                .customer-card:hover {
                                                    border-color: #357ABD;
                                                    transform: translateY(-3px);
                                                    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
                                                }

                                                .customer-card p {
                                                    margin: 0;
                                                    font-size: 15px;
                                                    color: #333;
                                                    font-weight: 500;
                                                }
                                            </style>
                                            <div id="append-customer" class="row gap-3">

                                            </div>

                                        </div>
                                        <div id="customer-new-form" class="customer-form" style="display: none;">
                                            <div class="row">
                                                <div class="col-lg-4">
                                                    <label>Phone Number</label>
                                                    <input type="text" class="form-control"
                                                        placeholder="Enter phone number" id="new-customer-phone"
                                                        value="{{ optional($customer)->customer_phone ?? optional($appointment)->phone }}">
                                                </div>
                                                <div class="col-lg-4">
                                                    <label>Full Name</label>
                                                    <input type="text" class="form-control"
                                                        placeholder="Enter customer name" id="new-customer-name"
                                                        value="{{ optional($customer)->customer_name ?? optional($appointment)->name }}">
                                                </div>

                                                <div class="col-lg-4">
                                                    <label>Email (Optional)</label>
                                                    <input type="email" class="form-control"
                                                        placeholder="Enter email address" id="new-customer-email"
                                                        value="{{ optional($customer)->customer_email ?? optional($appointment)->email }}">
                                                </div>
                                            </div>



                                        </div>

                                        <div class="row">
                                            <div class="col-lg-12" style="text-align: center;">
                                                <button class="btn guest-btn">Continue as Guest</button>
                                            </div>
                                        </div>

                                        <div id="previous-invoices-wrap" class="d-none">
                                            <div class="bc-prev-inv-title">
                                                <i class="bi bi-receipt me-1"></i> Previous Invoices
                                            </div>
                                            <div class="table-responsive border rounded-3">
                                                <table class="table table-hover align-middle mb-0" id="previous-invoices-table">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Invoice No</th>
                                                            <th>Date</th>
                                                            <th>Purchase Service</th>
                                                            <th>Payment</th>
                                                            <th class="text-end">Amount</th>
                                                            <th class="text-center">Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="previous-invoices-body"></tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="col-12 d-flex justify-content-between">
                                        <button class="btn btn-label-secondary btn-prev next-btn-invoice" disabled>
                                            <i class="bx bx-left-arrow-alt bx-sm ms-sm-n2 me-sm-2"></i>
                                            <span class="align-middle d-sm-inline-block d-none">Previous</span>
                                        </button>
                                        <button class="btn btn-primary btn-next next-btn-invoice">
                                            <span
                                                class="align-middle d-sm-inline-block d-none me-sm-2 next-tab-click-guest">Next</span>
                                            <i class="bx bx-chevron-right bx-sm me-sm-n2"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <style>
                                .services-section {
                                    margin: 28px auto;
                                }

                                .services-title {
                                    font-size: 1.2rem;
                                    margin-bottom: 10px;
                                }

                                .service-category-card {
                                    background: #fff;
                                    border: 1.5px solid #f1f1f1;
                                    border-radius: 12px;
                                    margin-bottom: 16px;
                                    box-shadow: 0 1px 7px rgba(50, 40, 10, .07);
                                }

                                .category-header {
                                    font-weight: 500;
                                    font-size: 1.09rem;
                                    padding: 14px 18px 13px 18px;
                                    border-radius: 12px 12px 0 0;
                                    background: #fff;
                                    display: flex;
                                    align-items: center;
                                    cursor: pointer;
                                    position: relative;
                                }

                                .category-badge {
                                    background: #111;
                                    color: #fff;
                                    font-weight: 600;
                                    font-size: 0.97rem;
                                    padding: 2.5px 9px;
                                    border-radius: 15px;
                                    margin-left: 13px;
                                    margin-right: 7px;
                                    display: inline-block;
                                }

                                .dropdown-arrow {
                                    margin-left: auto;
                                    font-size: 0.97em;
                                    color: #888;
                                    transition: transform 0.16s;
                                }

                                .service-category-card.collapsed .category-body {
                                    display: none;
                                }

                                .service-category-card.collapsed .dropdown-arrow {
                                    transform: rotate(-90deg);
                                }

                                .category-body {
                                    padding: 0 11px 11px 11px;
                                }

                                .service-item {
                                    background: #faf9f8;
                                    border-radius: 7px;
                                    padding: 12px 16px;
                                    font-size: 1.06em;
                                    display: flex;
                                    align-items: center;
                                    justify-content: space-between;
                                    margin-top: 9px;
                                    margin-bottom: 0;
                                    cursor: pointer;
                                    transition: background 0.18s;
                                }

                                .service-item.selected {
                                    background: #757575;
                                    color: #fff;
                                }

                                .service-item.selected .service-name {
                                    color: #fff;
                                }

                                .service-item:not(.selected):hover {
                                    background: #eee;
                                }

                                .qty-controls {
                                    display: flex;
                                    align-items: center;
                                    background: #232323;
                                    border-radius: 20px;
                                    padding: 2px 4px 2px 6px;
                                    gap: 6px;
                                }

                                .qty-btn {
                                    border: none;
                                    background: transparent;
                                    color: #fff;
                                    font-size: 20px;
                                    width: 28px;
                                    height: 28px;
                                    border-radius: 50%;
                                    cursor: pointer;
                                    font-weight: bold;
                                    transition: background 0.18s;
                                }

                                .qty-btn:hover {
                                    background: #444;
                                }

                                .qty {
                                    background: #fff;
                                    color: #111;
                                    min-width: 24px;
                                    text-align: center;
                                    border-radius: 50%;
                                    display: inline-block;
                                    font-size: 1em;
                                    font-weight: 600;
                                    padding: 2px 7px;
                                    margin: 0 1px;
                                }
                            </style>
                            <!-- Services & Products (combined) -->
                            <div id="order-service" class="content">
                                <div class="row g-6">
                                    <div class="col-12 d-flex justify-content-between invoice-step-nav">
                                        <button class="btn btn-primary btn-prev next-btn-invoice">
                                            <i class="bx bx-left-arrow-alt bx-sm ms-sm-n2 me-sm-2"></i>
                                            <span class="align-middle d-sm-inline-block d-none">Previous</span>
                                        </button>
                                        <button class="btn btn-primary btn-next next-btn-invoice">
                                            <span class="align-middle d-sm-inline-block d-none me-sm-2">Next</span>
                                            <i class="bx bx-chevron-right bx-sm me-sm-n2"></i>
                                        </button>
                                    </div>
                                    <div class="services-section">
                                        <h3 class="services-title mb-0">Select Services</h3>
                                        <p>Click on a service to select it. You can also add products below, then click Next.</p>

                                        <div class="row">
                                            @foreach ($services as $key => $service)
                                                <div class="col-lg-6">
                                                    <div class="accordion mt-4" id="accordionExample"
                                                        style="border: 1px solid #dddddd;border-radius: 12px;overflow: hidden;">
                                                        <div class="card accordion-item ">
                                                            <h2 class="accordion-header" id="heading{{ $key }}">
                                                                <button type="button" class="accordion-button"
                                                                    data-bs-toggle="collapse"
                                                                    data-bs-target="#accordion{{ $key }}"
                                                                    aria-expanded="true"
                                                                    aria-controls="accordion{{ $key }}"
                                                                    style="font-weight:600;font-size: 18px;">
                                                                    {{ $service->category_name }}
                                                                </button>
                                                            </h2>

                                                            <div id="accordion{{ $key }}"
                                                                class="accordion-collapse collapse"
                                                                data-bs-parent="#accordionExample">
                                                                <div class="accordion-body">
                                                                    @foreach ($service->services as $singleService)
                                                                        <div class="service-item @if (isset($appointmentServices[$singleService->id])) selected @endif"
                                                                            data-serviceId="{{ $singleService->id }}"
                                                                            data-price="{{ $singleService->price }}"
                                                                            data-id="{{ $singleService->id }}"
                                                                            data-category="{{ $service->category_name }}"
                                                                            data-service="{{ $singleService->service_name }}">
                                                                            <span
                                                                                class="service-name">{{ $singleService->service_name }}
                                                                                ({{ $singleService->price }})
                                                                            </span>
                                                                            @if (isset($appointmentServices[$singleService->id]))
                                                                                <div class="qty-controls">
                                                                                    <button type="button" class="qty-btn minus">&minus;</button>
                                                                                    <span class="qty">{{ $appointmentServices[$singleService->id] }}</span>
                                                                                    <button type="button" class="qty-btn plus">+</button>
                                                                                </div>
                                                                            @endif
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    <style>
                                        .products-section {
                                            margin: 30px auto 10px auto;
                                            max-height: 420px;
                                            overflow-y: auto;
                                            overflow-x: clip;
                                        }

                                        .products-title {
                                            font-size: 1.2rem;
                                            margin-bottom: 8px;
                                        }

                                        .product-card {
                                            background: #fff;
                                            border: 2px solid #e6e6e6;
                                            border-radius: 13px;
                                            width: 192px;
                                            min-height: 265px;
                                            transition: border 0.18s, box-shadow 0.18s;
                                            cursor: pointer;
                                            position: relative;
                                            display: flex;
                                            flex-direction: column;
                                            justify-content: flex-start;
                                            align-items: stretch;
                                            margin: 10px
                                        }

                                        .product-card.selected {
                                            border-color: #222;
                                            box-shadow: 0 3px 12px rgba(34, 34, 34, 0.10);
                                        }

                                        .product-image {
                                            flex-shrink: 0;
                                            height: 170px;
                                            background: #f5f5f5;
                                            color: #8596a4;
                                            display: flex;
                                            align-items: center;
                                            justify-content: center;
                                            border-radius: 13px 13px 0 0;
                                            font-size: 1.08em;
                                            font-weight: 500;
                                            margin-bottom: 1px;
                                            overflow: hidden;
                                        }

                                        .product-info {
                                            padding: 11px 0px 9px 0px;
                                            flex-grow: 1;
                                        }

                                        .product-name {
                                            font-weight: 500;
                                            font-size: 1.13em;
                                        }

                                        .product-price {
                                            font-size: 1.08em;
                                            color: #4d4d4d;
                                            font-weight: 700;
                                            margin-top: 7px;
                                        }

                                        .cart-controls {
                                            display: flex;
                                            align-items: center;
                                            justify-content: center;
                                            background: #232323;
                                            border-radius: 18px;
                                            gap: 8px;
                                            padding: 6px 10px;
                                            width: 80%;
                                            margin: 13px auto 12px auto;
                                            min-height: 36px;
                                            box-sizing: border-box;
                                        }

                                        .cart-btn {
                                            border: none;
                                            background: #111;
                                            color: #fff;
                                            font-size: 20px;
                                            width: 31px;
                                            height: 31px;
                                            border-radius: 50%;
                                            cursor: pointer;
                                            font-weight: bold;
                                            transition: background 0.18s;
                                            outline: none;
                                        }

                                        .cart-btn:hover {
                                            background: #444;
                                        }

                                        .cart-qty {
                                            background: #fff;
                                            color: #232323;
                                            min-width: 24px;
                                            text-align: center;
                                            border-radius: 50%;
                                            font-size: 1.07em;
                                            font-weight: 600;
                                            padding: 4px 11px;
                                            border: 1.2px solid #eee;
                                        }
                                    </style>

                                    <div class="products-section">
                                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
                                            <div>
                                                <h3 class="products-title mb-0">Select Products</h3>
                                                <p class="text-muted mb-0 small">Optional — add products to the invoice if needed.</p>
                                            </div>
                                            <button type="button" class="btn btn-outline-primary" id="toggle-products-btn" aria-expanded="{{ !empty($appointmentProduct) ? 'true' : 'false' }}">
                                                <i class="bi bi-box-seam me-1"></i>
                                                <span class="toggle-products-label">{{ !empty($appointmentProduct) ? 'Hide Products' : 'Show Products' }}</span>
                                            </button>
                                        </div>
                                        <div id="products-list-wrap" class="{{ empty($appointmentProduct) ? 'd-none' : '' }}">
                                            <p class="text-muted mb-2">Click a product to add it to the invoice.</p>
                                            <div class="row">
                                                @forelse ($products as $product)
                                                    @php
                                                        $productQty = (int) ($appointmentProduct[$product->id] ?? 0);
                                                    @endphp
                                                    <div class="product-card {{ $productQty > 0 ? 'selected' : '' }}" data-id="{{ $product->id }}"
                                                        data-price="{{ $product->price }}"
                                                        data-name="{{ $product->product_name }}">
                                                        <div class="product-image">
                                                            <img src="{{ $product->image_url }}"
                                                                style="max-width: 100%;" alt="">
                                                        </div>
                                                        <div class="product-info">
                                                            <div class="product-name">{{ $product->product_name }}</div>
                                                            <div class="product-price">{{ $shopCurrencySymbol ?? "$" }}{{ $product->price }}</div>
                                                        </div>
                                                        <div class="cart-controls" style="{{ $productQty > 0 ? '' : 'display: none;' }}">
                                                            <button class="cart-btn minus">−</button>
                                                            <span class="cart-qty">{{ $productQty > 0 ? $productQty : 1 }}</span>
                                                            <button class="cart-btn plus">+</button>
                                                        </div>
                                                    </div>
                                                @empty
                                                    <div class="col-12">
                                                        <p class="text-muted mb-0">No products available.</p>
                                                    </div>
                                                @endforelse
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12 d-flex justify-content-between mt-3">
                                        <button class="btn btn-primary btn-prev next-btn-invoice">
                                            <i class="bx bx-left-arrow-alt bx-sm ms-sm-n2 me-sm-2"></i>
                                            <span class="align-middle d-sm-inline-block d-none">Previous</span>
                                        </button>
                                        <button class="btn btn-primary btn-next next-btn-invoice">
                                            <span class="align-middle d-sm-inline-block d-none me-sm-2">Next</span>
                                            <i class="bx bx-chevron-right bx-sm me-sm-n2"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <style>
                                .staff-section {
                                    margin: 0 auto 0 auto;
                                }

                                .staff-title {
                                    font-size: 1.23rem;
                                    margin-bottom: 22px;
                                }

                                .staff-row {
                                    display: flex;
                                    gap: 26px;
                                }

                                .staff-card {
                                    background: #fff;
                                    border: 2.1px solid #dedede;
                                    border-radius: 13px;
                                    width: 210px;
                                    padding: 20px 12px 16px 12px;
                                    cursor: pointer;
                                    box-shadow: 0 1px 12px rgba(44, 44, 44, 0.07);
                                    transition: border 0.18s, box-shadow 0.18s;
                                    text-align: center;
                                    position: relative;
                                    display: flex;
                                    flex-direction: column;
                                    align-items: center;
                                }

                                .staff-card.selected {
                                    border-color: #222;
                                    box-shadow: 0 3px 12px rgba(34, 34, 34, 0.17);
                                }


                                .staff-avatar {
                                    background: #f7f7f7;
                                    border-radius: 50%;
                                    font-size: 1.18em;
                                    font-weight: 600;
                                    color: #222;
                                    width: 62px;
                                    height: 62px;
                                    display: flex;
                                    align-items: center;
                                    justify-content: center;
                                    margin-bottom: 13px;
                                    overflow: hidden;
                                }

                                .staff-avatar-img img {
                                    width: 100%;
                                    height: 100%;
                                    object-fit: cover;
                                    border-radius: 50%;
                                }

                                .staff-name {
                                    font-weight: 500;
                                    font-size: 1.12em;
                                    margin-bottom: 5px;
                                    color: #232323;
                                }

                                .staff-role {
                                    color: #767676;
                                    font-size: 1em;
                                    margin-bottom: 19px;
                                }

                                .select-btn {
                                    background: #181818;
                                    color: #fff;
                                    border-radius: 7px;
                                    padding: 8px 22px;
                                    font-weight: 600;
                                    font-size: 0.99em;
                                    border: none;
                                    cursor: pointer;
                                    box-shadow: 0 2px 5px rgba(30, 30, 30, 0.12);
                                    transition: background 0.18s;
                                    outline: none;
                                    display: inline-block;
                                    margin-top: auto;
                                }

                                .staff-card.selected .select-btn {
                                    display: inline-block;
                                }
                            </style>
                            <div id="staff-details" class="content">
                                <div class="row g-6">
                                    <div class="col-12 d-flex justify-content-between invoice-step-nav">
                                        <button class="btn btn-primary btn-prev next-btn-invoice">
                                            <i class="bx bx-left-arrow-alt bx-sm ms-sm-n2 me-sm-2"></i>
                                            <span class="align-middle d-sm-inline-block d-none">Previous</span>
                                        </button>
                                        <button
                                            class="btn btn-primary btn-next next-btn-invoice calculateFinalTotal check-staff-assign">
                                            <span class="align-middle d-sm-inline-block d-none me-sm-2">Next</span>
                                            <i class="bx bx-chevron-right bx-sm me-sm-n2"></i>
                                        </button>
                                    </div>
                                    <div class="staff-section">
                                        <h3 class="staff-title">Select Staff Members</h3>
                                        <p style="color: red">Step 1: Please select all barbers who have provided services
                                            or products<br>
                                            Step 2: Assign that staff member to specific services and products.</p>
                                        <div class="staff-row">
                                            @foreach ($staffs as $staff)
                                                <!-- Staff Card 1 -->
                                                <div class="staff-card @if (in_array($staff->id, $appointmentStaffIds ?? [])) selected @endif" data-id="{{ $staff->id }}"
                                                    data-name="{{ $staff->name }}">
                                                    <div class="staff-avatar">
                                                        <img src="{{ asset('assets/img/user.png') }}"
                                                            style="max-width: 100%;" alt="">
                                                    </div>
                                                    <div class="staff-name">{{ $staff->name }}</div>
                                                    <button class="select-btn" style="{{ in_array($staff->id, $appointmentStaffIds ?? []) ? '' : 'display:none;' }}">
                                                        ✔ Selected
                                                    </button>
                                                </div>
                                            @endforeach
                                        </div>
                                        <hr>
                                        <div class="staff-service-combination  mt-4  row">

                                        </div>
                                    </div>

                                    <div class="col-12 d-flex justify-content-between">
                                        <button class="btn btn-primary btn-prev next-btn-invoice">
                                            <i class="bx bx-left-arrow-alt bx-sm ms-sm-n2 me-sm-2"></i>
                                            <span class="align-middle d-sm-inline-block d-none">Previous</span>
                                        </button>
                                        <button
                                            class="btn btn-primary btn-next next-btn-invoice calculateFinalTotal check-staff-assign">
                                            <span class="align-middle d-sm-inline-block d-none me-sm-2">Next</span>
                                            <i class="bx bx-chevron-right bx-sm me-sm-n2"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <style>
                                .invoice-section {
                                    max-width: 100%;
                                    width: 100%;
                                    margin: 8px 0 20px;
                                }

                                .pos-summary-card {
                                    background: #fff;
                                    border: 1px solid #e8ebe9;
                                    border-radius: 16px;
                                    box-shadow: 0 8px 28px rgba(15, 23, 42, 0.06);
                                    overflow: hidden;
                                    margin-bottom: 16px;
                                }

                                .pos-summary-head {
                                    display: flex;
                                    justify-content: space-between;
                                    align-items: center;
                                    padding: 18px 22px;
                                    border-bottom: 1px solid #eef2f0;
                                    background: #fbfcfb;
                                }

                                .pos-summary-head h5 {
                                    margin: 0;
                                    font-size: 1.15rem;
                                    font-weight: 700;
                                    color: #111;
                                }

                                .pos-summary-head .draft-pill {
                                    font-size: 11px;
                                    font-weight: 700;
                                    letter-spacing: .06em;
                                    text-transform: uppercase;
                                    color: #136207;
                                    background: #e8f5e6;
                                    border-radius: 999px;
                                    padding: 4px 10px;
                                }

                                .pos-billto {
                                    padding: 16px 22px 8px;
                                }

                                .pos-billto .label {
                                    font-size: 12px;
                                    font-weight: 700;
                                    letter-spacing: .08em;
                                    text-transform: uppercase;
                                    color: #6b7280;
                                    margin-bottom: 4px;
                                }

                                .pos-billto .billto-customer {
                                    font-size: 1.05rem;
                                    font-weight: 700;
                                    color: #111;
                                }

                                .pos-billto .billto-meta {
                                    font-size: 13px;
                                    color: #6b7280;
                                    margin-top: 2px;
                                }

                                .invoice-table {
                                    width: 100%;
                                    border-collapse: collapse;
                                    font-size: 14px;
                                }

                                .invoice-table th {
                                    text-align: left;
                                    padding: 12px 22px;
                                    background: #f6f8f6;
                                    color: #136207;
                                    font-size: 13px;
                                    font-weight: 700;
                                    border-top: 1px solid #e8ebe9;
                                    border-bottom: 1px solid #e8ebe9;
                                }

                                .invoice-table th:last-child,
                                .invoice-table td:last-child {
                                    text-align: right;
                                }

                                .invoice-table td {
                                    padding: 11px 22px;
                                    border-bottom: 1px solid #f0f2f1;
                                    color: #1f2937;
                                    vertical-align: middle;
                                }

                                .invoice-table .item-name {
                                    font-weight: 600;
                                }

                                .invoice-table .section-sub td {
                                    background: #fafafa;
                                    color: #4b5563;
                                    font-size: 13px;
                                }

                                .invoice-table .section-payable td {
                                    background: #f0fdf4;
                                    color: #136207;
                                    font-weight: 700;
                                }

                                .pos-inv-input {
                                    width: 110px;
                                    height: 34px;
                                    border: 1px solid #d7ddd9;
                                    border-radius: 8px;
                                    padding: 4px 10px;
                                    text-align: right;
                                    font-size: 14px;
                                    background: #fff;
                                }

                                .pos-inv-input:focus {
                                    outline: none;
                                    border-color: #136207;
                                    box-shadow: 0 0 0 3px rgba(19,98,7,.12);
                                }

                                .pos-totals-card {
                                    background: #fff;
                                    border: 1px solid #e8ebe9;
                                    border-radius: 16px;
                                    box-shadow: 0 8px 28px rgba(15, 23, 42, 0.06);
                                    overflow: hidden;
                                }

                                .pos-totals-table {
                                    width: 100%;
                                    border-collapse: collapse;
                                }

                                .pos-totals-table td {
                                    padding: 12px 22px;
                                    border-bottom: 1px solid #f0f2f1;
                                    font-size: 14px;
                                    color: #4b5563;
                                }

                                .pos-totals-table td:last-child {
                                    text-align: right;
                                    color: #111;
                                    font-weight: 600;
                                }

                                .pos-totals-table .is-subtotal td {
                                    background: #f6f8f6;
                                    color: #136207;
                                    font-size: 16px;
                                    font-weight: 700;
                                }

                                .pos-totals-table .is-total td {
                                    background: #136207;
                                    color: #fff !important;
                                    font-size: 18px;
                                    font-weight: 800;
                                    border-bottom: none;
                                }

                                .pos-totals-table .pay-row td {
                                    background: #fbfcfb;
                                    vertical-align: middle;
                                }

                                .pos-totals-table select {
                                    width: 160px;
                                    height: 36px;
                                    border-radius: 8px;
                                    border: 1px solid #d7ddd9;
                                    padding: 4px 10px;
                                    font-size: 14px;
                                    display: inline-block;
                                }
                            </style>

                            <div id="order-summary" class="content">
                                <div class="row g-6">
                                    <div class="col-12 d-flex justify-content-between invoice-step-nav">
                                        <button class="btn btn-primary btn-prev next-btn-invoice">
                                            <i class="bx bx-left-arrow-alt bx-sm ms-sm-n2 me-sm-2"></i>
                                            <span class="align-middle d-sm-inline-block d-none">Previous</span>
                                        </button>
                                        <button class="btn btn-success btn-submit">Update</button>
                                    </div>
                                    <div class="invoice-section">
                                        <div class="pos-summary-card">
                                            <div class="pos-summary-head">
                                                <h5>Order Summary</h5>
                                                <span class="draft-pill">Edit</span>
                                            </div>

                                            <div class="pos-billto">
                                                <div class="label">Bill To</div>
                                                <div class="billto-customer">{{ optional($customer)->customer_name ?? optional($appointment)->name ?? 'Guest User' }}</div>
                                                <div class="billto-meta billto-phone">{{ (optional($customer)->customer_phone ?? optional($appointment)->phone) ? 'Phone: '.(optional($customer)->customer_phone ?? optional($appointment)->phone) : '' }}</div>
                                                <div class="billto-meta billto-email">{{ (optional($customer)->customer_email ?? optional($appointment)->email) ? 'Email: '.(optional($customer)->customer_email ?? optional($appointment)->email) : '' }}</div>
                                            </div>

                                            <table class="invoice-table">
                                                <thead>
                                                    <tr>
                                                        <th>Service &amp; Product</th>
                                                        <th>Price</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="selected-services-list">
                                                    <tr>
                                                        <td colspan="2" class="text-center text-muted py-4">No items selected yet.</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>

                                        <div class="pos-totals-card">
                                            <table class="pos-totals-table">
                                                <tr class="is-subtotal">
                                                    <td>Subtotal</td>
                                                    <td class="subtotal-text">{{ $shopCurrencySymbol ?? "$" }}0.00</td>
                                                </tr>
                                                <tr>
                                                    <td>Discount</td>
                                                    <td id="discount-text">{{ $shopCurrencySymbol ?? "$" }}0.00</td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        Tax ({{ $appointment->tax_rate ?? $tax->rate ?? 0 }}% <span id="tax-type-label">{{ strtolower($appointment->tax_type ?? $tax->tax_type ?? 'include') === 'exclude' ? 'Excluded' : 'Included' }}</span>)
                                                        <select id="tax-type" class="form-select form-select-sm mt-1" disabled>
                                                            <option value="include" {{ strtolower($appointment->tax_type ?? $tax->tax_type ?? 'include') !== 'exclude' ? 'selected' : '' }}>Include</option>
                                                            <option value="exclude" {{ strtolower($appointment->tax_type ?? $tax->tax_type ?? '') === 'exclude' ? 'selected' : '' }}>Exclude</option>
                                                        </select>
                                                    </td>
                                                    <td id="tax-amunt-value">{{ $shopCurrencySymbol ?? "$" }}0.00</td>
                                                </tr>
                                                <tr>
                                                    <td>Tip</td>
                                                    <td>
                                                        <input type="number" class="pos-inv-input" id="tip" name="tip" value="{{ $appointment->total_tip ?? 0 }}" min="0" step="0.01">
                                                    </td>
                                                </tr>
                                                <tr class="is-total">
                                                    <td>Total</td>
                                                    <td id="final-total">{{ $shopCurrencySymbol ?? "$" }}0.00</td>
                                                </tr>
                                                <tr class="pay-row">
                                                    <td>Payment Method</td>
                                                    <td>
                                                        <select id="payment-method" class="form-control">
                                                            @foreach ($paymentMethods as $pmethod)
                                                                <option value="{{ $pmethod->name }}" @if ($pmethod->name == ($appointment->payment_method ?? '')) selected @endif>{{ \App\Support\PaymentMethodLabel::normalize($pmethod->name) }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>

                                    <div class="col-12 d-flex justify-content-between">
                                        <button class="btn btn-primary btn-prev next-btn-invoice">
                                            <i class="bx bx-left-arrow-alt bx-sm ms-sm-n2 me-sm-2"></i>
                                            <span class="align-middle d-sm-inline-block d-none">Previous</span>
                                        </button>
                                        <button class="btn btn-success btn-submit">Update</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- /Default Wizard -->

        </div>
        <!-- / Content -->
    </div>

    {{-- Previous Invoice Preview Modal --}}
    <div class="modal fade" id="posViewInvoiceModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Invoice Preview</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="invoice-container" id="pos-invoice">
                        <div class="header" style="display:flex;justify-content:space-between;align-items:center;border-bottom:2px solid #bc1e2d;padding-bottom:10px;margin-bottom:20px;">
                            <img src="{{ ($shopLogo ?? (isset($currentShop) ? $currentShop->logoUrl() : asset('assets/img/logo.webp'))) }}" alt="Rosenthal Barbershop Logo" style="width:60px;">
                            <div class="company-details" style="text-align:right;font-size:12px;font-weight:600;">
                                <h2 style="margin:0;color:#bc1e2d;font-size:14px;">Rosenthal Barbershop</h2>
                                {{ $siteSetting->contact_email ?? 'info@bluecrownbarbers.com' }}<br>
                                {{ $siteSetting->contact_phone ?? '+1 (587) 555-2489' }}<br>
                                {{ optional($siteSetting)->websiteHost() ?: 'www.bluecrownbarbers.com' }}
                            </div>
                        </div>
                        <div class="customer-details" style="margin-bottom:20px;display:flex;justify-content:space-between;">
                            <div id="pos-invoice-customer" style="font-size:14px;"></div>
                            <div id="pos-invoice-info" style="text-align:right;font-size:14px;"></div>
                        </div>
                        <table class="invoice-table" style="width:100%;border-collapse:collapse;margin-bottom:20px;">
                            <thead>
                                <tr style="background:#bc1e2d;color:#fff;">
                                    <th style="padding:10px;text-align:left;">Service Name</th>
                                    <th class="text-center" style="padding:10px;text-align:center;">Qty</th>
                                    <th class="text-center" style="padding:10px;text-align:center;">Price</th>
                                    <th class="text-center" style="padding:10px;text-align:center;">Total</th>
                                </tr>
                            </thead>
                            <tbody id="pos-service-rows"></tbody>
                        </table>
                        <table class="totals" id="pos-totals" style="margin-top:10px;width:100%;"></table>
                        <div class="footer" style="text-align:center;padding-top:20px;border-top:2px solid #bc1e2d;font-size:12px;color:#555;">
                            Rosenthal Barbershop – For Style & Elegance<br>
                            Need help? Contact us at {{ $siteSetting->contact_email ?? 'info@bluecrownbarbers.com' }}
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-center gap-2 flex-wrap">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="printPosInvoice()">
                        <i class="bi bi-printer me-1"></i> Print
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            var CURRENCY_SYMBOL = window.SHOP_CURRENCY_SYMBOL || '₹';

            const existingAssignments = @json($existingAssignments ?? []);
            const existingServiceDiscount = "{{ $appointment->service_discount ?? 0 }}";
            const existingProductDiscount = "{{ $appointment->product_discount ?? 0 }}";

            $('#toggle-products-btn').on('click', function() {
                var $wrap = $('#products-list-wrap');
                var $btn = $(this);
                var $label = $btn.find('.toggle-products-label');
                $wrap.toggleClass('d-none');
                var isOpen = !$wrap.hasClass('d-none');
                $btn.attr('aria-expanded', isOpen ? 'true' : 'false');
                $label.text(isOpen ? 'Hide Products' : 'Show Products');
            });

            $(document).on('click', '.customer-card', function () {
                const $card = $(this);

                // alert("clicked car");
                // console.log('clicked card:', $card.data());

                $('#customer_id').val($card.data('id'));
                $('#new-customer-phone').val($card.data('contact'));
                $('#new-customer-name').val($card.data('name'));
                $('#new-customer-email').val($card.data('email'));

                $('#tab-new').click();
            });
            // 🔍 Search customer by contact
            $('#search-contact').on('keyup', function() {
                var contact = $(this).val();
                if (contact.length < 10) {
                    clearPreviousInvoicesTable();
                    return false;
                }

                $.ajax({
                    url: "{{ route('admin.invoice.customerDetials') }}",
                    type: "GET",
                    data: {
                        contact: contact
                    },
                    success: function(response) {
                        console.log(response);
                        $('#append-customer').empty();
                        clearPreviousInvoicesTable();

                        if (!response.data || response.data.length == 0) {
                            $('#tab-new').trigger('click');
                            $('#new-customer-phone').val(contact);
                            $('#customer_id').val('');
                        } else {
                            $.each(response.data, function(key, customer) {
                                $('#customer_id').val(customer.customer_id);
                                $('#new-customer-phone').val(customer.customer_phone);
                                $('#new-customer-name').val(customer.customer_name);
                                $('#new-customer-email').val(customer.customer_email);

                                var spent = customer.appointments_sum_total_amt;
                                spent = (spent === null || spent === undefined) ? '0.00' : parseFloat(spent).toFixed(2);

                                var invoices = customer.invoiced_appointments || customer.invoicedAppointments || [];
                                var lastSpend = '0.00';
                                if (invoices.length) {
                                    lastSpend = formatPosMoney(invoices[0].total_amt);
                                }

                                var isStaff = !!(response.is_staff);
                                var lastServices = customer.last_purchase_services || '—';
                                var extraRows = isStaff
                                    ? `<p>Last Purchase Services : ${lastServices}</p>`
                                    : `<p>Total Spent : ${CURRENCY_SYMBOL}${spent}</p>
                                <p>Last Spend : ${CURRENCY_SYMBOL}${lastSpend}</p>`;

                                $('#append-customer').append(`
                            <div class="customer-card col-md-3"
                                data-id="${customer.customer_id}"
                                data-name="${customer.customer_name || ''}"
                                data-email="${customer.customer_email || ''}"
                                data-contact="${customer.customer_phone || ''}">
                                <p>Customer : ${customer.customer_name || '-'}</p>
                                <p>Total Visit : ${customer.appointments_count || 0}</p>
                                ${extraRows}
                            </div>
                        `);

                                renderPreviousInvoicesTable(customer);
                            });
                        }
                    }
                });
            });

            window._posInvoiceCache = {};

            function clearPreviousInvoicesTable() {
                window._posInvoiceCache = {};
                $('#previous-invoices-body').empty();
                $('#previous-invoices-wrap').addClass('d-none');
            }

            function formatPosMoney(val) {
                var n = parseFloat(val);
                if (isNaN(n)) n = 0;
                return n.toFixed(2);
            }

            function formatPosDate(value) {
                if (!value) return '—';
                try {
                    var d = new Date(value);
                    if (isNaN(d.getTime())) return value;
                    return d.toLocaleDateString('en-IN', {
                        day: '2-digit',
                        month: 'short',
                        year: 'numeric'
                    });
                } catch (e) {
                    return value;
                }
            }

            function getInvoicePurchaseServices(inv) {
                var services = inv.services || [];
                var names = [];
                services.forEach(function(item) {
                    var name = (item.service && item.service.service_name) ? item.service.service_name : null;
                    if (name) names.push(name);
                });
                if (!names.length) return '—';
                return names.join(', ');
            }

            function renderPreviousInvoicesTable(customer) {
                var invoices = customer.invoiced_appointments || customer.invoicedAppointments || [];
                clearPreviousInvoicesTable();

                if (!invoices.length) {
                    return;
                }

                var rowsHtml = '';
                invoices.forEach(function(inv, idx) {
                    window._posInvoiceCache[inv.appointment_id] = Object.assign({}, inv, {
                        customer: {
                            customer_id: customer.customer_id,
                            customer_name: customer.customer_name,
                            customer_email: customer.customer_email,
                            customer_phone: customer.customer_phone
                        }
                    });

                    var purchaseServices = getInvoicePurchaseServices(inv);

                    rowsHtml += `
                        <tr>
                            <td>${idx + 1}</td>
                            <td><strong>${inv.invoice_number || '—'}</strong></td>
                            <td>${formatPosDate(inv.updated_at || inv.date || inv.created_at)}</td>
                            <td style="max-width:220px;white-space:normal;">${purchaseServices}</td>
                            <td>${window.paymentMethodLabel(inv.payment_method)}</td>
                            <td class="text-end">${CURRENCY_SYMBOL}${formatPosMoney(inv.total_amt)}</td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-primary"
                                    onclick="viewPreviousPosInvoice(${inv.appointment_id})">
                                    <i class="bi bi-eye me-1"></i> Invoice
                                </button>
                            </td>
                        </tr>
                    `;
                });

                $('#previous-invoices-body').html(rowsHtml);
                $('#previous-invoices-wrap').removeClass('d-none');
            }

            window.viewPreviousPosInvoice = function(appointmentId) {
                var invoice = window._posInvoiceCache[appointmentId];
                if (!invoice) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Invoice not found',
                        text: 'Please search the customer again.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    return;
                }

                var customer = invoice.customer || {};
                var customerName = customer.customer_name || invoice.name || 'Guest Customer';
                var isGuest = customerName === 'Guest Customer';

                document.getElementById('pos-invoice-customer').innerHTML = `
                    <strong>Customer:</strong><br>
                    ${customerName}<br>
                    ${isGuest ? '' : (customer.customer_email || '')}<br>
                    ${isGuest ? '' : (customer.customer_phone || invoice.phone || '')}
                `;

                document.getElementById('pos-invoice-info').innerHTML = `
                    <strong>Invoice No:</strong> ${invoice.invoice_number || '—'}<br>
                    <strong>Date:</strong> ${formatPosDate(invoice.updated_at || invoice.date || invoice.created_at)}
                    ${invoice.gstin ? `<br><strong>GSTIN:</strong> ${invoice.gstin}` : ''}
                    ${invoice.sac_code ? `<br><strong>SAC:</strong> ${invoice.sac_code}` : ''}
                `;

                var serviceRows = document.getElementById('pos-service-rows');
                serviceRows.innerHTML = '';
                var servicesubtotal = 0;
                var services = invoice.services || [];

                services.forEach(function(service) {
                    var amt = parseFloat(service.total_amt) || 0;
                    servicesubtotal += amt;
                    var sName = (service.service && service.service.service_name) ? service.service.service_name : 'Service';
                    serviceRows.innerHTML += `
                        <tr>
                            <td style="padding:10px;border-bottom:1px solid #ddd;">Service #${sName}</td>
                            <td class="text-center" style="padding:10px;border-bottom:1px solid #ddd;text-align:center;">1</td>
                            <td class="text-center" style="padding:10px;border-bottom:1px solid #ddd;text-align:center;">${CURRENCY_SYMBOL}${amt.toFixed(2)}</td>
                            <td class="text-right" style="padding:10px;border-bottom:1px solid #ddd;text-align:right;">${CURRENCY_SYMBOL}${amt.toFixed(2)}</td>
                        </tr>
                    `;
                });

                var serviceDiscountPct = parseFloat(invoice.service_discount) || 0;
                var serviceDiscount = 0;
                var serviceSubtotalWithDiscount = servicesubtotal;
                if (serviceDiscountPct > 0) {
                    serviceDiscount = servicesubtotal * serviceDiscountPct / 100;
                    serviceSubtotalWithDiscount = servicesubtotal - serviceDiscount;
                }

                if (servicesubtotal) {
                    serviceRows.innerHTML += `<tr style="color:red"><td colspan="3" style="padding:8px;">Total Services Amount</td><td class="text-right" style="padding:8px;text-align:right;">${CURRENCY_SYMBOL}${servicesubtotal.toFixed(2)}</td></tr>`;
                }
                if (serviceDiscount) {
                    serviceRows.innerHTML += `<tr style="color:red"><td colspan="3" style="padding:8px;">Service Discount</td><td class="text-right" style="padding:8px;text-align:right;">(${CURRENCY_SYMBOL}${serviceDiscount.toFixed(2)}) ${serviceDiscountPct.toFixed(2)}%</td></tr>`;
                }
                if (serviceSubtotalWithDiscount && servicesubtotal) {
                    serviceRows.innerHTML += `<tr style="color:red"><td colspan="3" style="padding:8px;">Total Services Payable</td><td class="text-right" style="padding:8px;text-align:right;">${CURRENCY_SYMBOL}${serviceSubtotalWithDiscount.toFixed(2)}</td></tr>`;
                }

                var productsubtotal = 0;
                var products = invoice.products || [];
                products.forEach(function(product) {
                    var amt = parseFloat(product.amount) || 0;
                    productsubtotal += amt;
                    var pName = (product.product && product.product.product_name) ? product.product.product_name : 'Product';
                    serviceRows.innerHTML += `
                        <tr>
                            <td style="padding:10px;border-bottom:1px solid #ddd;">Product #${pName}</td>
                            <td class="text-center" style="padding:10px;border-bottom:1px solid #ddd;text-align:center;">1</td>
                            <td class="text-center" style="padding:10px;border-bottom:1px solid #ddd;text-align:center;">${CURRENCY_SYMBOL}${amt.toFixed(2)}</td>
                            <td class="text-right" style="padding:10px;border-bottom:1px solid #ddd;text-align:right;">${CURRENCY_SYMBOL}${amt.toFixed(2)}</td>
                        </tr>
                    `;
                });

                var productDiscountPct = parseFloat(invoice.product_discount) || 0;
                var productDiscount = 0;
                var productSubtotalWithDiscount = productsubtotal;
                if (productDiscountPct > 0) {
                    productDiscount = productsubtotal * productDiscountPct / 100;
                    productSubtotalWithDiscount = productsubtotal - productDiscount;
                }

                if (productsubtotal) {
                    serviceRows.innerHTML += `<tr style="color:red"><td colspan="3" style="padding:8px;">Total Product Amount</td><td class="text-right" style="padding:8px;text-align:right;">${CURRENCY_SYMBOL}${productsubtotal.toFixed(2)}</td></tr>`;
                }
                if (productDiscount) {
                    serviceRows.innerHTML += `<tr style="color:red"><td colspan="3" style="padding:8px;">Product Discount</td><td class="text-right" style="padding:8px;text-align:right;">(${CURRENCY_SYMBOL}${productDiscount.toFixed(2)}) ${productDiscountPct.toFixed(2)}%</td></tr>`;
                }
                if (productSubtotalWithDiscount && productsubtotal) {
                    serviceRows.innerHTML += `<tr style="color:red"><td colspan="3" style="padding:8px;">Total Product Payable</td><td class="text-right" style="padding:8px;text-align:right;">${CURRENCY_SYMBOL}${productSubtotalWithDiscount.toFixed(2)}</td></tr>`;
                }

                if (!services.length && !products.length) {
                    serviceRows.innerHTML = `<tr><td colspan="4" class="text-center text-muted" style="padding:12px;">No line items found</td></tr>`;
                }

                var subtotal = productSubtotalWithDiscount + serviceSubtotalWithDiscount;
                var tax = parseFloat(invoice.total_tax) || 0;
                var tip = parseFloat(invoice.total_tip) || 0;
                var totalAmt = parseFloat(invoice.total_amt) || 0;
                var taxType = String(invoice.tax_type || 'include').toLowerCase();
                if (taxType !== 'exclude') {
                    subtotal = Math.max(0, subtotal - tax);
                }
                var taxLabel = taxType === 'exclude' ? 'GST (excluded)' : 'GST (included)';

                document.getElementById('pos-totals').innerHTML = `
                    <tr><td style="padding:8px;"><strong>Subtotal:</strong></td><td style="padding:8px;text-align:right;">${CURRENCY_SYMBOL}${subtotal.toFixed(2)}</td></tr>
                    <tr><td style="padding:8px;"><strong>${taxLabel} ${invoice.tax_rate ? `(${parseFloat(invoice.tax_rate).toFixed(2)}%)` : ''}:</strong></td><td style="padding:8px;text-align:right;">${CURRENCY_SYMBOL}${tax.toFixed(2)}</td></tr>
                    <tr><td style="padding:8px;"><strong>Tip:</strong></td><td style="padding:8px;text-align:right;">${CURRENCY_SYMBOL}${tip.toFixed(2)}</td></tr>
                    <tr><td style="padding:8px;"><strong>Total Amount:</strong></td><td style="padding:8px;text-align:right;"><strong>${CURRENCY_SYMBOL}${totalAmt.toFixed(2)}</strong></td></tr>
                `;

                var modalEl = document.getElementById('posViewInvoiceModal');
                if (modalEl && window.bootstrap) {
                    bootstrap.Modal.getOrCreateInstance(modalEl).show();
                } else {
                    $('#posViewInvoiceModal').modal('show');
                }
            };

            window.printPosInvoice = function() {
                var printContents = document.getElementById('pos-invoice').innerHTML;
                var printWindow = window.open('', '', 'height=800,width=1200');
                printWindow.document.write('<html><head><title>Invoice</title>');
                document.querySelectorAll('link[rel="stylesheet"], style').forEach(function(node) {
                    printWindow.document.write(node.outerHTML);
                });
                printWindow.document.write('</head><body>');
                printWindow.document.write(printContents);
                printWindow.document.write('</body></html>');
                printWindow.document.close();
                printWindow.onload = function() {
                    printWindow.focus();
                    printWindow.print();
                    printWindow.close();
                };
            };

            // 👤 Staff card toggle
            $(document).on("click", ".staff-card", function() {
                $(this).toggleClass("selected");
                $(this).find(".select-btn").toggle($(this).hasClass("selected"));
                getDatas()
            });

            // 🛒 Product card toggle
            $(document).on("click", ".product-card", function(e) {
                if ($(e.target).hasClass("cart-btn")) return;

                $(this).toggleClass("selected");
                const $controls = $(this).find(".cart-controls");

                if ($(this).hasClass("selected")) {
                    $controls.show();
                    let $qty = $controls.find(".cart-qty");
                    if (!$qty.text() || parseInt($qty.text()) <= 0) {
                        $qty.text("1");
                    }
                } else {
                    $controls.hide();
                }
                getDatas()
            });

            // ➕➖ Product qty buttons
            $(document).on("click", ".cart-btn.plus", function(e) {


                e.stopPropagation();
                let $qty = $(this).closest(".cart-controls").find(".cart-qty");
                console.log("PLUS CLICK", $qty.text());
                // $qty.text(parseInt($qty.text()) + 1);
            });

            $(document).on("click", ".cart-btn.minus", function(e) {
                e.stopPropagation();
                let $card = $(this).closest(".product-card");
                let $qty = $card.find(".cart-qty");
                let qty = parseInt($qty.text());
                if (qty > 1) {
                    // $qty.text(qty - 1);
                } else {
                    $card.removeClass("selected");
                    $card.find(".cart-controls").hide();
                }
            });

            // 📂 Category collapse
            $(document).on("click", ".category-toggle", function() {
                $(this).closest(".service-category-card").toggleClass("collapsed");
            });

            // 🛎 Service item select + qty controls
            $(document).on("click", ".service-item", function(e) {
                if ($(e.target).hasClass("qty-btn")) return;

                if ($(this).hasClass("selected")) {
                    $(this).removeClass("selected").find(".qty-controls").remove();
                } else {
                    $(this).addClass("selected").append(`
                <div class="qty-controls">
                    <button class="qty-btn minus">−</button>
                    <span class="qty">1</span>
                    <button class="qty-btn plus">+</button>
                </div>
            `);
                }
                getDatas();
                updateBadges();
            });

            // 🛒 Product qty buttons (only for product cards)
            $(document).on("click", ".cart-btn.plus", function(e) {
                e.stopPropagation();
                let $qty = $(this).closest(".cart-controls").find(".cart-qty");

                console.log("Before + =>", $qty.text())
                $qty.text(parseInt($qty.text()) + 1);

                console.log("AFter + =>", $qty.text())
                getDatas()
            });

            $(document).on("click", ".cart-btn.minus", function(e) {
                e.stopPropagation();
                let $card = $(this).closest(".product-card");
                let $qty = $card.find(".cart-qty");
                let qty = parseInt($qty.text());
                if (qty > 1) {
                    $qty.text(qty - 1);
                } else {
                    $card.removeClass("selected");
                    $card.find(".cart-controls").hide();
                }
                getDatas()
            });

            // 🛎 Service qty buttons (only for services)
            $(document).on("click", ".qty-btn", function(e) {
                e.stopPropagation();
                let $item = $(this).closest(".service-item");
                let $qty = $item.find(".qty");
                let qty = parseInt($qty.text());

                if ($(this).hasClass("plus")) {
                    $qty.text(qty + 1);
                } else if ($(this).hasClass("minus")) {
                    if (qty > 1) {
                        $qty.text(qty - 1);
                    } else {
                        $item.removeClass("selected").find(".qty-controls").remove();
                    }
                }
                getDatas();
                updateBadges();
            });

            // 🔢 Update badge counts for services
            function updateBadges() {
                $(".service-category-card").each(function() {
                    let count = $(this).find(".service-item.selected").length;
                    $(this).find(".category-badge").text(count);
                });
            }
            updateBadges();

            // 🔄 Tab switching
            $("#tab-search").on("click", function() {
                $(this).addClass("active");
                $("#tab-new").removeClass("active");
                $("#customer-search-form").show();
                $("#customer-new-form").hide();
            });

            $("#tab-new").on("click", function() {
                $(this).addClass("active");
                $("#tab-search").removeClass("active");
                $("#customer-search-form").hide();
                $("#customer-new-form").show();
                if (typeof clearPreviousInvoicesTable === 'function') {
                    clearPreviousInvoicesTable();
                }
            });

            // 🚀 Guest button
            $(document).on("click", ".guest-btn", function(e) {
                e.preventDefault();
                // $("#customer-search-form, #customer-new-form").hide();
                // alert("Proceed as Guest");
                $('.next-tab-click-guest').click();
            });

            //get selected options
            let ProductArray = [];
            let productStaffAssignments = []; // global array
            let restoreExistingAssignments = true;

            function applyExistingStaffAssignments() {
                var pending = restoreExistingAssignments ? (existingAssignments || []).slice() : [];

                $(".staff-service-combination .staff-select").each(function () {
                    var $select = $(this);
                    var productId = String($select.data("product-id"));
                    var flag = String($select.data("flag"));
                    var matchIndex = pending.findIndex(function (item) {
                        return String(item.productId) === productId && String(item.flag) === flag;
                    });

                    if (matchIndex !== -1 && $select.find('option[value="' + pending[matchIndex].staffId + '"]').length) {
                        $select.val(String(pending[matchIndex].staffId));
                        pending.splice(matchIndex, 1);
                    }

                    $select.trigger("change");
                });

                restoreExistingAssignments = false;
            }

            function getDatas() {
                // let services = [];
                let products = [];
                let staff = [];

                $(".service-item.selected").each(function() {
                    let Serviceid = $(this).data("id");
                    let service = $(this).data("service");
                    let price = $(this).data("price");

                    let qty = parseInt($(this).find(".qty").text(), 10) || 0;

                    for (let i = 0; i < qty; i++) {
                        products.push({
                            id: Serviceid,
                            service: service,
                            price: price,
                            flag: "S"
                        });
                    }
                });


                $(".product-card.selected").each(function() {
                    let id = $(this).data("id");
                    let pname = $(this).data("name");
                    let pprice = $(this).data("price");
                    let qty = parseInt($(this).find(".cart-qty").text(), 10) || 0;

                    for (let i = 0; i < qty; i++) {
                        products.push({
                            id: id,
                            service: pname,
                            price: pprice,
                            flag: "P"
                        });
                    }
                });

                $(".staff-card.selected").each(function() {
                    let staffid = $(this).data("id");
                    let sname = $(this).data("name");
                    // let qty = parseInt($(this).find(".cart-qty").text(), 10) || 0;

                    // for (let i = 0; i < qty; i++) {
                    staff.push({
                        id: staffid,
                        service: sname
                    });
                    // }
                });

                console.log("Product", products);
                console.log("Staff", staff);

                ProductArray = products;
                var html = "";
                products.forEach((p, index) => {
                    html += `<div class="col-md-3"><div class="assign-row">
                    <span>${p.service} (${CURRENCY_SYMBOL}${p.price})</span>
                    <select class="staff-select form-control" data-product-id="${p.id}" data-flag="${p.flag}"  data-index="${index}">
                        <option value="">-- Select Staff --</option>`;
                    staff.forEach((s, si) => {
                        html += `<option value="${s.id}"${si === 0 ? ' selected' : ''}>${s.service}</option>`;
                    });
                    html += `</select>
                 </div></div>`;
                });

                $(".staff-service-combination").html(html);

                productStaffAssignments = []; // global array

                if (staff.length > 0) {
                    applyExistingStaffAssignments();
                }

            }


            // $(document).on('click', '.check-staff-assign', function(e) {
            //     let allSelected = true;
            //
            //     // loop through all staff-select dropdowns
            //     $(".staff-service-combination .staff-select").each(function() {
            //         if ($(this).val() === "") {
            //             allSelected = false;
            //             $(this).css("border", "1px solid red"); // highlight empty
            //         } else {
            //             $(this).css("border", "");
            //         }
            //     });
            //
            //     if (!allSelected) {
            //         e.preventDefault(); // prevent stepper from moving
            //         alert("Please assign staff for all products before proceeding!");
            //         return false;
            //     }
            //
            // });

            $(document).on("change", ".staff-select", function() {
                let productId = $(this).data("product-id");
                let flag = $(this).data("flag");
                let index = $(this).data("index"); // unique for duplicate qty items
                let staffId = $(this).val();
                let staffName = $(this).find("option:selected").text();

                // 🔹 product info from products array (must be in scope)
                let product = ProductArray[index];

                // check if already assigned
                let existing = productStaffAssignments.find(a => a.index === index);
                console.log("existing=>", existing);
                if (existing) {
                    existing.staffId = staffId;
                    existing.staffName = staffName;
                } else {
                    productStaffAssignments.push({
                        productId: productId,
                        productName: product.service,
                        price: product.price, // ✅ add price
                        staffId: staffId,
                        staffName: staffName,
                        index: index,
                        flag: flag
                    });
                }

                console.log("Assignments:", productStaffAssignments);
            });


            {{-- function calculateTotal() { --}}
            {{--    // 1. Calculate subtotal --}}
            {{--    let subTotal = productStaffAssignments.reduce((sum, item) => sum + parseFloat(item.price), 0); --}}

            {{--    // 2. Get discount type and value --}}
            {{--    let discountType = $('#discount-type').val(); // 'percent' or 'fixed' (example) --}}
            {{--    let discountValue = parseFloat($('#discount-value').val()) || 0; --}}

            {{--    // 3. Apply discount --}}
            {{--    let discountedTotal = subTotal; --}}
            {{--    let discountDisplayValue = 0; --}}

            {{--    let tax = "{{ $tax->rate }}"; --}}

            {{--    if (discountType === '2') { --}}
            {{--        discountDisplayValue = (subTotal * discountValue / 100); --}}
            {{--        discountedTotal = subTotal - (subTotal * discountValue / 100); --}}
            {{--    } else if (discountType === '3') { --}}
            {{--        discountedTotal = subTotal - discountValue; --}}
            {{--        discountDisplayValue = discountValue; --}}
            {{--    } --}}


            {{--    let taxAmount = (discountedTotal * tax) / 100; --}}

            {{--    // 4. Add tip --}}
            {{--    let tip = parseFloat($('#tip').val()) || 0; --}}
            {{--    let finalTotal = discountedTotal + taxAmount + tip; --}}

            {{--    // 5. Update UI --}}
            {{--    // $('#sub-total').text(subTotal.toFixed(2)); --}}
            {{--    $('#final-total').text(CURRENCY_SYMBOL + finalTotal.toFixed(2)); --}}
            {{--    $('#tax-amunt-value').text(CURRENCY_SYMBOL + taxAmount.toFixed(2)); --}}

            {{--    $('.subtotal-text').text(CURRENCY_SYMBOL + subTotal.toFixed(2)); --}}
            {{--    $('.tip-value').text(CURRENCY_SYMBOL + tip.toFixed(2)); --}}
            {{--    $('#discount-text').text(CURRENCY_SYMBOL + discountDisplayValue.toFixed(2)); --}}

            {{--    renderProductList(); --}}
            {{--    return finalTotal; --}}
            {{-- } --}}



            // Trigger recalculation on any change
            // $('#discount-type, #discount-value, #tip').on('change keyup', function() {
            //     calculateTotal();
            //
            // });
            // $('#service-discount-value, #service-discount-type, #product-discount-value, #product-discount-type, #tip')
            //     .on('change keyup', function() {
            //         console.log('Called');
            //         calculateTotal();
            //     });


            function syncInvoiceBillTo() {
                var name = ($('#new-customer-name').val() || '').trim() || 'Guest User';
                var phone = ($('#new-customer-phone').val() || '').trim();
                var email = ($('#new-customer-email').val() || '').trim();
                $('.billto-customer').text(name);
                $('.billto-phone').text(phone ? ('Phone: ' + phone) : '');
                $('.billto-email').text(email ? ('Email: ' + email) : '');
            }

            function renderProductList() {
                let html = "";
                let serviceRows = "";
                let productRows = "";
                let serviceTotal = 0;
                let productTotal = 0;

                let productMap = {};
                productStaffAssignments.forEach(item => {
                    let key = item.flag + "_" + item.productId + "_" + item.staffId;
                    if (productMap[key]) {
                        productMap[key].qty += 1;
                    } else {
                        productMap[key] = {
                            flag: item.flag,
                            name: item.productName,
                            price: parseFloat(item.price),
                            qty: 1
                        };
                    }
                });

                Object.values(productMap).forEach(p => {
                    let row = `<tr>
            <td><span class="item-name">${p.name}</span> <span class="text-muted">x${p.qty}</span></td>
            <td>${CURRENCY_SYMBOL}${(p.price * p.qty).toFixed(2)}</td>
        </tr>`;

                    if (p.flag === "S") {
                        serviceRows += row;
                        serviceTotal += p.price * p.qty;
                    } else if (p.flag === "P") {
                        productRows += row;
                        productTotal += p.price * p.qty;
                    }
                });

                // get existing discount values (prevent reset on rerender)
                let currentServiceType = $("#service-discount-type").val() || "percentage";
                let currentServiceValue = $("#service-discount-value").val();
                if (currentServiceValue === undefined || currentServiceValue === null || currentServiceValue === '') {
                    currentServiceValue = existingServiceDiscount || 0;
                }
                let currentProductType = $("#product-discount-type").val() || "percentage";
                let currentProductValue = $("#product-discount-value").val();
                if (currentProductValue === undefined || currentProductValue === null || currentProductValue === '') {
                    currentProductValue = existingProductDiscount || 0;
                }

                if (!serviceRows && !productRows) {
                    html = `<tr><td colspan="2" class="text-center text-muted py-4">No items selected yet.</td></tr>`;
                }

                if (serviceRows) {
                    html += serviceRows;
                    html += `
        <tr class="section-sub">
            <td><strong>Total Service Amount</strong></td>
            <td class="total-service-net-amount">${CURRENCY_SYMBOL}${serviceTotal.toFixed(2)}</td>
        </tr>
        <tr class="section-sub">
            <td><strong>Service Discount (%)</strong></td>
            <td>
                <select id="service-discount-type" class="d-none">
                    <option value="flat">Flat</option>
                    <option value="percentage" ${currentServiceType === 'percentage' ? 'selected' : ''}>Percentage</option>
                </select>
                <input type="number" id="service-discount-value" class="pos-inv-input" value="${currentServiceValue}" min="0" step="0.01">
            </td>
        </tr>
        <tr class="section-payable">
            <td>Total Payable Services</td>
            <td class="service-total-with-discount">${CURRENCY_SYMBOL}${serviceTotal.toFixed(2)}</td>
        </tr>
        `;
                }

                if (productRows) {
                    html += productRows;
                    html += `
        <tr class="section-sub">
            <td><strong>Total Product Amount</strong></td>
            <td class="total-product-net-amount">${CURRENCY_SYMBOL}${productTotal.toFixed(2)}</td>
        </tr>
        <tr class="section-sub">
            <td><strong>Product Discount (%)</strong></td>
            <td>
                <select id="product-discount-type" class="d-none">
                    <option value="flat">Flat</option>
                    <option value="percentage" ${currentProductType === 'percentage' ? 'selected' : ''}>Percentage</option>
                </select>
                <input type="number" id="product-discount-value" class="pos-inv-input" value="${currentProductValue}" min="0" step="0.01">
            </td>
        </tr>
        <tr class="section-payable">
            <td>Total Payable Products</td>
            <td class="products-total-with-discount">${CURRENCY_SYMBOL}${productTotal.toFixed(2)}</td>
        </tr>
        `;
                }

                $("#selected-services-list").html(html);
                syncInvoiceBillTo();
            }

            function posTax(discountedTotal) {
                let rate = parseFloat("{{ $tax->rate ?? 0 }}") || 0;
                let type = String($('#tax-type').val() || 'include').toLowerCase();
                let excluded = type === 'exclude';
                let taxAmount = 0;
                let payable = discountedTotal;

                if (rate > 0 && excluded) {
                    taxAmount = discountedTotal * rate / 100;
                    payable = discountedTotal + taxAmount;
                } else if (rate > 0) {
                    taxAmount = discountedTotal * rate / (100 + rate);
                }

                $('#tax-type-label').text(excluded ? 'Excluded' : 'Included');
                return { taxAmount: taxAmount, payable: payable };
            }

            function calculateTotal() {
                let serviceTotal = 0;
                let productTotal = 0;

                productStaffAssignments.forEach(item => {
                    if (item.flag === "S") serviceTotal += parseFloat(item.price);
                    if (item.flag === "P") productTotal += parseFloat(item.price);
                });


                $('.total-service-net-amount').html(CURRENCY_SYMBOL + serviceTotal.toFixed(2));
                $('.total-product-net-amount').html(CURRENCY_SYMBOL + productTotal.toFixed(2));

                // service discount
                let serviceDiscountType = $('#service-discount-type').val();
                let serviceDiscountValue = parseFloat($('#service-discount-value').val()) || 0;
                let serviceDiscountAmount = 0;
                if (serviceDiscountType === 'percentage') {
                    serviceDiscountAmount = (serviceTotal * serviceDiscountValue / 100);
                } else if (serviceDiscountType === 'flat') {
                    serviceDiscountAmount = serviceDiscountValue;
                }
                let serviceFinal = serviceTotal - serviceDiscountAmount;

                // product discount
                let productDiscountType = $('#product-discount-type').val();
                let productDiscountValue = parseFloat($('#product-discount-value').val()) || 0;
                let productDiscountAmount = 0;
                if (productDiscountType === 'percentage') {
                    productDiscountAmount = (productTotal * productDiscountValue / 100);
                } else if (productDiscountType === 'flat') {
                    productDiscountAmount = productDiscountValue;
                }
                let productFinal = productTotal - productDiscountAmount;

                $('.products-total-with-discount').text(CURRENCY_SYMBOL + productFinal.toFixed(2));
                $('.service-total-with-discount').text(CURRENCY_SYMBOL + serviceFinal.toFixed(2));

                let subTotal = serviceTotal + productTotal;
                let discountedTotal = serviceFinal + productFinal;

                let taxParts = posTax(discountedTotal);
                let taxAmount = taxParts.taxAmount;

                let tip = parseFloat($('#tip').val()) || 0;
                let finalTotal = taxParts.payable + tip;

                // $('.subtotal-text').text(CURRENCY_SYMBOL + subTotal.toFixed(2));
                $('.subtotal-text').text(CURRENCY_SYMBOL + discountedTotal.toFixed(2));
                $('#discount-text').text(CURRENCY_SYMBOL + (serviceDiscountAmount + productDiscountAmount).toFixed(2));
                $('#tax-amunt-value').text(CURRENCY_SYMBOL + taxAmount.toFixed(2));
                // $('.tip-value').text(CURRENCY_SYMBOL + tip.toFixed(2));
                $('#final-total').text(CURRENCY_SYMBOL + finalTotal.toFixed(2));

                // $('#tip').val(tip.toFixed(2));
                return finalTotal;
            }

            // event binding
            // initial render + calculation
            renderProductList();
            calculateTotal();

            $(document).on('change keyup',
                '#service-discount-value, #service-discount-type, #product-discount-value, #product-discount-type, #tip, #tax-type',
                function() {
                    calculateTotal();
                });
            $('.calculateFinalTotal').on('click', function() {
                syncInvoiceBillTo();
                renderProductList();
                calculateTotal();
            });

            $('#new-customer-name, #new-customer-phone, #new-customer-email').on('change keyup', function () {
                syncInvoiceBillTo();
            });



            // function renderProductList() {
            //     let html = "";
            //
            //     // Count quantities for the same product + staff
            //     let productMap = {};
            //
            //     productStaffAssignments.forEach(item => {
            //         let key = item.flag + "_" + item.productId + "_" + item
            //         .staffId; // unique key for product+staff
            //         if (productMap[key]) {
            //             productMap[key].qty += 1;
            //         } else {
            //             productMap[key] = {
            //                 name: item.productName,
            //                 price: parseFloat(item.price),
            //                 qty: 1
            //             };
            //         }
            //     });
            //
            //     // Build table rows
            //     Object.values(productMap).forEach(p => {
            //         html += `<tr>
        //         <td>${p.name} x${p.qty}</td>
        //         <td style="text-align:right;">${CURRENCY_SYMBOL}${(p.price * p.qty).toFixed(2)}</td>
        //      </tr>`;
            //     });
            //
            //     $("#selected-services-list").html(html);
            // }





            $('.btn-submit').on('click', function() {

                let oldHtml = $(this).html(); // sto
                $(this).prop('disabled', true).html(`
                    <span class="spinner-border spinner-border-sm me-2"></span> Processing...
                `);

                let serviceTotal = 0;
                let productTotal = 0;

                productStaffAssignments.forEach(item => {
                    if (item.flag === "S") serviceTotal += parseFloat(item.price);
                    if (item.flag === "P") productTotal += parseFloat(item.price);
                });

                // service discount
                let serviceDiscountType = $('#service-discount-type').val();
                let serviceDiscountValue = parseFloat($('#service-discount-value').val()) || 0;
                let serviceDiscountAmount = 0;
                if (serviceDiscountType === 'percentage') {
                    serviceDiscountAmount = (serviceTotal * serviceDiscountValue / 100);
                } else if (serviceDiscountType === 'flat') {
                    serviceDiscountAmount = serviceDiscountValue;
                }
                let serviceFinal = serviceTotal - serviceDiscountAmount;

                // product discount
                let productDiscountType = $('#product-discount-type').val();
                let productDiscountValue = parseFloat($('#product-discount-value').val()) || 0;
                let productDiscountAmount = 0;
                if (productDiscountType === 'percentage') {
                    productDiscountAmount = (productTotal * productDiscountValue / 100);
                } else if (productDiscountType === 'flat') {
                    productDiscountAmount = productDiscountValue;
                }
                let productFinal = productTotal - productDiscountAmount;

                $('.products-total-with-discount').text(CURRENCY_SYMBOL + productFinal.toFixed(2));
                $('.service-total-with-discount').text(CURRENCY_SYMBOL + serviceFinal.toFixed(2));

                let subTotal = serviceTotal + productTotal;
                let discountedTotal = serviceFinal + productFinal;

                let taxParts = posTax(discountedTotal);
                let taxAmount = taxParts.taxAmount;

                let tip = parseFloat($('#tip').val()) || 0;
                let finalTotal = taxParts.payable + tip;

                // $('.subtotal-text').text(CURRENCY_SYMBOL + subTotal.toFixed(2));

                var FinalDiscount = (
                    parseFloat(serviceDiscountAmount.toFixed(2)) +
                    parseFloat(productDiscountAmount.toFixed(2))
                ).toFixed(2);


                // 🔹 Prepare full data
                let payload = {
                    _token: $('meta[name="csrf-token"]').attr("content"),
                    products: productStaffAssignments,
                    totals: {
                        subTotal: discountedTotal.toFixed(2),
                        discountType: 1,
                        discountAmount: FinalDiscount,
                        tip: tip.toFixed(2),
                        tax: taxAmount.toFixed(2),
                        finalTotal: finalTotal.toFixed(2),
                        product_discount: $('#product-discount-value').val() ?? 0,
                        service_discount: $('#service-discount-value').val() ?? 0
                    },
                    customer: {
                        customer_id: $('#customer_id').val(),
                        contact: $('#new-customer-phone').val(),
                        name: $('#new-customer-name').val(),
                        email: $('#new-customer-email').val(),
                    },
                    payment_method: $('#payment-method').val(),
                    appointment_id: $('#appointment_id').val() || "{{ $appointment->appointment_id ?? '' }}",
                };





                if (productStaffAssignments.length === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'warning',
                        text: "Form Invalid! Please check the form again.",
                        showConfirmButton: true
                    });
                    $(this).prop('disabled', false).html(oldHtml);
                    return false;
                }


                // 🔹 Send AJAX
                $.ajax({
                    url: "{{ route('admin.invoice.update') }}",
                    type: "POST",
                    contentType: "application/json",
                    data: JSON.stringify(payload),
                    success: function(response) {
                        $(this).prop('disabled', false).html(oldHtml);
                        if (response.status == 200) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: response.msg,
                                showConfirmButton: false,
                                timer: 2000
                            });
                            setTimeout(() => {
                                window.location.href =
                                    "{{ route('admin.invoice.index') }}";
                            }, 1500);
                        } else {
                            $(this).prop('disabled', false).html(oldHtml);
                            Swal.fire({
                                icon: 'warning',
                                title: 'warning',
                                text: "Something went wrong!",
                                showConfirmButton: true
                            });
                        }
                        // console.log("Saved successfully:", response);
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON?.msg || xhr.responseText ||
                                "Something went wrong!",
                            showConfirmButton: true
                        });

                        $(this).prop('disabled', false).html(oldHtml);
                        console.error("Error:", xhr.responseText);
                    }
                });
            });



            // Initial calculation
            getDatas();
            renderProductList();
            calculateTotal();

            @if(!empty(optional($customer)->customer_name) || !empty(optional($appointment)->name))
                $('#tab-new').trigger('click');
            @endif

            @if(!empty(optional($customer)->customer_phone) || !empty(optional($appointment)->phone))
                var existingPhone = "{{ optional($customer)->customer_phone ?? optional($appointment)->phone }}";
                if (existingPhone.length >= 10) {
                    $('#search-contact').trigger('keyup');
                }
            @endif


        });
    </script>
@endpush
