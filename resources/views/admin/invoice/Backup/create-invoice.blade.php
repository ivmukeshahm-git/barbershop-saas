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
            margin-top: 7px;
            padding: 10px 22px;
            background: #a61c1c;
            /* Strong red background */
            border: none;
            /* Remove border for solid color */
            border-radius: 6px;
            color: #fff;
            /* White text */
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(166, 28, 28, 0.4);
            transition: background 0.3s ease, box-shadow 0.3s ease;
        }

        .guest-btn:hover {
            background: #811616;
            color: #fff;
            /* Darker red on hover */
            box-shadow: 0 6px 10px rgba(131, 22, 22, 0.5);
        }

        .bs-stepper .step.active .bs-stepper-circle {
            background-color: #811616;
            color: #fff;
            box-shadow: 0 0.125rem 0.25rem 0 rgb(129 22 22 / 20%);
        }

        .bs-stepper .step.active .bs-stepper-icon i .bs-stepper-title,
        .bs-stepper .step.active .bs-stepper-label .bs-stepper-title {
            color: #811616;
        }

        .bs-stepper .step.crossed .step-trigger .bs-stepper-circle {
            background-color: rgb(129 22 22 / 39%) !important;
            color: #811616 !important;
        }

        .bs-stepper .bs-stepper-header .step.crossed+.line i {
            color: #811616;
        }

        .next-btn-invoice {
            background: #811616;
            border-color: #811616;
            box-shadow: 0 0.125rem 0.25rem 0 rgb(129 22 22 / 20%);
        }

        .next-btn-invoice:focus {
            background: #811616 !important;
            border-color: #811616 !important;
            box-shadow: 0 0.125rem 0.25rem 0 rgb(129 22 22 / 20%) !important;
        }

        .next-btn-invoice:hover {
            background: #811616 !important;
            border-color: #811616 !important;
            box-shadow: 0 0.125rem 0.25rem 0 rgb(129 22 22 / 20%) !important;
        }
    </style>
@endpush


@extends('admin.layout.layout')
@section('content')
    <script>
        page_active_id = "Master-Category";
    </script>


    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Default -->
        <div class="row">
            <div class="col-12">
                <h5>POS & Invoice</h5>
            </div>

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
                                    <span class="bs-stepper-title">Services</span>
                                    <span class="bs-stepper-subtitle">Select Serve Services</span>
                                </span>
                            </button>
                        </div>
                        <div class="line">
                            <i class="bx bx-chevron-right"></i>
                        </div>
                        <div class="step" data-target="#product-sell">
                            <button type="button" class="step-trigger">
                                <span class="bs-stepper-circle">3</span>
                                <span class="bs-stepper-label">
                                    <span class="bs-stepper-title">Products</span>
                                    <span class="bs-stepper-subtitle">Add Sell Product</span>
                                </span>
                            </button>
                        </div>
                        <div class="line">
                            <i class="bx bx-chevron-right"></i>
                        </div>
                        <div class="step" data-target="#staff-details">
                            <button type="button" class="step-trigger">
                                <span class="bs-stepper-circle">4</span>
                                <span class="bs-stepper-label">
                                    <span class="bs-stepper-title">Staff</span>
                                    <span class="bs-stepper-subtitle">Add Service Staff</span>
                                </span>
                            </button>
                        </div>

                        <div class="line">
                            <i class="bx bx-chevron-right"></i>
                        </div>
                        <div class="step" data-target="#payment-details">
                            <button type="button" class="step-trigger">
                                <span class="bs-stepper-circle">5</span>
                                <span class="bs-stepper-label">
                                    <span class="bs-stepper-title">Payment</span>
                                    <span class="bs-stepper-subtitle">Add Payment</span>
                                </span>
                            </button>
                        </div>

                        <div class="line">
                            <i class="bx bx-chevron-right"></i>
                        </div>
                        <div class="step" data-target="#order-summary">
                            <button type="button" class="step-trigger">
                                <span class="bs-stepper-circle">6</span>
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
                                    <div class="customer-info-box">
                                        <div class="customer-info-header">Customer Information</div>
                                        <div class="customer-info-tabs">
                                            <button id="tab-search" class="tab-btn active">Search Customer</button>
                                            <button id="tab-new" class="tab-btn">New Customer</button>
                                        </div>
                                        <div id="customer-search-form" class="customer-form">
                                            <label>Phone Number</label>
                                            <input type="hidden" name="customer_id" id="customer_id">
                                            <input type="text" class="form-control" id="search-contact"
                                                placeholder="Enter phone number">
                                            <style>
                                                .customer-card {
                                                    border-radius: 16px;
                                                    border: 2px solid #4a90e2;
                                                    /* soft blue border */
                                                    padding: 16px 20px;
                                                    width: 50%;
                                                    max-width: 400px;
                                                    display: grid;
                                                    gap: 6px;
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
                                            <div id="append-customer">

                                            </div>

                                        </div>
                                        <div id="customer-new-form" class="customer-form" style="display: none;">
                                            <div class="row">
                                                <div class="col-lg-4">
                                                    <label>Phone Number</label>
                                                    <input type="text" class="form-control"
                                                        placeholder="Enter phone number" id="new-customer-phone">
                                                </div>
                                                <div class="col-lg-4">
                                                    <label>Full Name</label>
                                                    <input type="text" class="form-control"
                                                        placeholder="Enter customer name" id="new-customer-name">
                                                </div>

                                                <div class="col-lg-4">
                                                    <label>Email (Optional)</label>
                                                    <input type="email" class="form-control"
                                                        placeholder="Enter email address" id="new-customer-email">
                                                </div>
                                            </div>



                                        </div>

                                        <div class="row">
                                            <div class="col-lg-12" style="text-align: center;">
                                                <button class="btn guest-btn">Continue as Guest</button>
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
                            <!-- Personal Info -->
                            <div id="order-service" class="content">
                                <div class="row g-6">
                                    <div class="services-section">
                                        <h3 class="services-title mb-0">Select Services</h3>
                                        <p>Click on a service to select it, then click the Next button to proceed and
                                            complete the invoice.</p>

                                        <!-- CATEGORY 1 -->
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
                                                                        <div class="service-item"
                                                                            data-serviceId="{{ $singleService->id }}"
                                                                            data-price="{{ $singleService->price }}"
                                                                            data-id="{{ $singleService->id }}"
                                                                            data-category="{{ $service->category_name }}"
                                                                            data-service="{{ $singleService->service_name }}">
                                                                            <span
                                                                                class="service-name">{{ $singleService->service_name }}
                                                                                ({{ $singleService->price }})</span>
                                                                            {{--                                                                    <div class="qty-controls"> --}}
                                                                            {{--                                                                        <button class="qty-btn minus">−</button> --}}
                                                                            {{--                                                                        <span class="qty" --}}
                                                                            {{--                                                                            id="beard-breadcolor-qty">1</span> --}}
                                                                            {{--                                                                        <button class="qty-btn plus">+</button> --}}
                                                                            {{--                                                                    </div> --}}
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



                                    <div class="col-12 d-flex justify-content-between">
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
                                .products-section {
                                    margin: 30px auto;
                                    height: 400px;
                                    overflow-y: scroll;
                                    overflow-x: clip;
                                }

                                .products-title {
                                    font-size: 1.2rem;
                                    margin-bottom: 18px;
                                }

                                .products-row {
                                    display: flex;
                                    gap: 22px;
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
                            <div id="product-sell" class="content">
                                <div class="row g-6">
                                    <div class="products-section">
                                        <h3 class="products-title">Select Products</h3>
                                        <div class="row">
                                            <!-- Product Card 1 -->
                                            @foreach ($products as $product)
                                                <div class="product-card" data-id="{{ $product->id }}"
                                                    data-price="{{ $product->price }}"
                                                    data-name="{{ $product->product_name }}">
                                                    <div class="product-image">
                                                        <img src="{{ asset('storage/' . $product->product_img) }}"
                                                            style="max-width: 100%;" alt="">
                                                    </div>
                                                    <div class="product-info">
                                                        <div class="product-name">{{ $product->product_name }}</div>
                                                        <div class="product-price">${{ $product->price }}</div>
                                                    </div>
                                                    <div class="cart-controls" style="display: none;">
                                                        <button class="cart-btn minus">−</button>
                                                        <span class="cart-qty">1</span>
                                                        <button class="cart-btn plus">+</button>
                                                    </div>
                                                </div>
                                            @endforeach

                                        </div>
                                    </div>



                                    <div class="col-12 d-flex justify-content-between">
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
                                    <div class="staff-section">
                                        <h3 class="staff-title">Select Staff Members</h3>
                                        <p style="color: red">Step 1: Please select all barbers who have provided services
                                            or products<br>
                                            Step 2: Assign that staff member to specific services and products.</p>
                                        <div class="staff-row">
                                            @foreach ($staffs as $staff)
                                                <!-- Staff Card 1 -->
                                                <div class="staff-card" data-id="{{ $staff->id }}"
                                                    data-name="{{ $staff->name }}">
                                                    <div class="staff-avatar">
                                                        <img src="{{ asset('assets/img/user.png') }}"
                                                            style="max-width: 100%;" alt="">
                                                    </div>
                                                    <div class="staff-name">{{ $staff->name }}</div>
                                                    <button class="select-btn" style="display:none;">
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
                                        <button class="btn btn-primary btn-next next-btn-invoice  check-staff-assign">
                                            <span class="align-middle d-sm-inline-block d-none me-sm-2">Next</span>
                                            <i class="bx bx-chevron-right bx-sm me-sm-n2"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <style>
                                /* .payment-details-card {
                                                            padding: 30px 28px 22px 28px;
                                                        } */

                                .details-title {
                                    font-size: 1.25rem;
                                    font-weight: 500;
                                    margin-bottom: 29px;
                                }

                                .form-group label {
                                    display: block;
                                    font-weight: 500;
                                    margin-bottom: 7px;
                                    font-size: 1.04em;
                                    color: #242424;
                                }

                                .form-control {
                                    width: 100%;
                                    border-radius: 8px;
                                    font-size: 1.05em;
                                    border: 1.3px solid #ececec;
                                    padding: 11px 12px;
                                    margin-bottom: 6px;
                                    background: #f7f7f7;
                                    box-sizing: border-box;
                                    outline: none;
                                    transition: border .18s;
                                }

                                .form-control:focus {
                                    border-color: #999abc;
                                    background: #fff;
                                }

                                .details-divider {
                                    border: none;
                                    border-top: 1.5px solid #ebebeb;
                                    margin: 25px 0 19px 0;
                                }

                                .discount-block {
                                    margin-top: 2px;
                                }

                                .discount-title {
                                    font-weight: 500;
                                    font-size: 1.1rem;
                                    color: #444;
                                    margin-bottom: 3px;
                                    display: flex;
                                    align-items: center;
                                    gap: 4px;
                                }

                                .discount-icon {
                                    color: #2672da;
                                    font-weight: bold;
                                    font-size: 1.1em;
                                    margin-right: 6px;
                                }

                                .discount-subtext {
                                    font-size: 0.99em;
                                    color: #868686;
                                    margin-left: 1px;
                                }

                                .mt-2 {
                                    margin-top: 13px;
                                }

                                .mt-3 {
                                    margin-top: 20px;
                                }
                            </style>
                            <div id="payment-details" class="content">

                                <div class="row g-6">
                                    <div class="payment-details-card">
                                        <div class="details-title">Payment Details</div>

                                        <div class="form-group">
                                            <label for="payment-method">Payment Method</label>
                                            <select id="payment-method" class="form-control">
                                                @foreach ($paymentMethods as $pmethod)
                                                    <option value="{{ $pmethod->name }}">{{ $pmethod->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <hr class="details-divider">

                                        <div class="discount-block">
                                            <div class="discount-title"><span class="discount-icon">%</span> Discount
                                            </div>
                                            <div class="discount-subtext">Apply a discount to this invoice</div>
                                            <div class="form-group mt-3">
                                                <label for="discount-type">Discount Type</label>
                                                <select id="discount-type" class="form-control">
                                                    <option value="">No Discount</option>
                                                    <option value="2">Percentage</option>
                                                    <option value="3">Fixed Amount</option>
                                                </select>
                                            </div>
                                            <div class="form-group mt-2">
                                                <label for="discount-value">Discount Value</label>
                                                <input type="number" id="discount-value" class="form-control"
                                                    value="0" min="0" />
                                            </div>




                                            <div class="form-group mt-2">
                                                <label for="discount-value">Add Tip</label>
                                                <input type="number" id="tip" name="tip" class="form-control"
                                                    value="0" min="0" />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12 d-flex justify-content-between">
                                        <button class="btn btn-primary btn-prev next-btn-invoice">
                                            <i class="bx bx-left-arrow-alt bx-sm ms-sm-n2 me-sm-2"></i>
                                            <span class="align-middle d-sm-inline-block d-none">Previous</span>
                                        </button>
                                        <button class="btn btn-primary btn-next next-btn-invoice calculateFinalTotal">
                                            <span class="align-middle d-sm-inline-block d-none  me-sm-2">Next</span>
                                            <i class="bx bx-chevron-right bx-sm me-sm-n2"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <style>
                                .invoice-section {
                                    margin: 26px auto;
                                }

                                .invoice-header {
                                    display: flex;
                                    justify-content: space-between;
                                    align-items: flex-start;
                                }

                                .invoice-title {
                                    font-size: 1.6rem;
                                    font-weight: 640;
                                    margin-bottom: -2px;
                                    margin-top: 4px;
                                }

                                .invoice-status {
                                    color: #868686;
                                    font-size: 1.07em;
                                    font-weight: 500;
                                    margin-top: 1px;
                                }

                                .invoice-meta {
                                    text-align: right;
                                    font-size: 1.01em;
                                }

                                .invoice-date {
                                    color: #212121;
                                    font-size: 1.02em;
                                    font-weight: 500;
                                }

                                .invoice-customer {
                                    color: #212121;
                                    font-size: 1.03em;
                                    font-weight: 500;
                                }

                                .invoice-card {
                                    margin-top: 25px;
                                    background: #fff;
                                    border-radius: 13px;
                                    border: 1.4px solid #eeeeee;
                                    box-shadow: 0 2px 16px rgba(44, 62, 80, 0.07);
                                    padding: 0;
                                    overflow: hidden;
                                }

                                .billto-title {
                                    padding: 18px 22px 2px 22px;
                                    font-size: 1.09em;
                                    font-weight: 500;
                                }

                                .billto-customer {
                                    padding: 0 22px 11px 22px;
                                    font-size: 1.09em;
                                    color: #181818;
                                    font-weight: 600;
                                }

                                .invoice-table {
                                    width: 100%;
                                    border-collapse: collapse;
                                    font-size: 1.05em;
                                }

                                .invoice-table th {
                                    font-weight: 600;
                                    text-align: left;
                                    padding: 7px 22px;
                                    border-bottom: 1.5px solid #ebebeb;
                                    background: #fafaf9;
                                }

                                .invoice-table td {
                                    padding: 7px 22px;
                                    border-bottom: 1.1px solid #f2f2f2;
                                }

                                .invoice-table tr:last-child td {
                                    border-bottom: none;
                                }

                                .sum-label {
                                    color: #767676;
                                }

                                .sum-value {
                                    color: #767676;
                                    text-align: right;
                                }

                                .tip-value {
                                    color: #767676;
                                }

                                .add-tip {
                                    color: #3170f7;
                                    font-weight: 500;
                                    margin-left: 4px;
                                    cursor: pointer;
                                }

                                .total-row td {
                                    font-size: 1.13em;
                                    font-weight: 600;
                                    border-top: 1.7px solid #ededed;
                                    padding-top: 11px;
                                }

                                .total-label {
                                    color: #232323;
                                }

                                .total-value {
                                    text-align: right;
                                    color: #111;
                                    font-weight: bold;
                                }
                            </style>
                            <!-- Social Links -->
                            <div id="order-summary" class="content">

                                <div class="row g-6">
                                    <div class="invoice-section">
                                        <div class="invoice-header">
                                            <div>
                                                <div class="invoice-title">Invoice</div>
                                                <div class="invoice-status">#DRAFT</div>
                                            </div>
                                            <div class="invoice-meta">
                                                <div class="invoice-date"></div>
                                                <div class="invoice-customer"></div>
                                            </div>
                                        </div>

                                        <div class="invoice-card">
                                            <div class="billto-title">Bill To</div>
                                            <div class="billto-customer">Guest User</div>
                                            <table class="invoice-table">
                                                <thead>
                                                    <tr>
                                                        <th>Item</th>
                                                        <th style="text-align:right;">Price</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="selected-services-list">

                                                    {{--                                                    <tr> --}}
                                                    {{--                                                        <td>bread color x2</td> --}}
                                                    {{--                                                        <td style="text-align:right;">$20.00</td> --}}
                                                    {{--                                                    </tr> --}}
                                                    {{--                                                    <tr> --}}
                                                    {{--                                                        <td>fvdhfgh,hndf x1</td> --}}
                                                    {{--                                                        <td style="text-align:right;">$25.00</td> --}}
                                                    {{--                                                    </tr> --}}
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td class="sum-label"style="
        font-weight: bold;
        color: green;
        font-size: 18px;
    ">Subtotal</td>
                                                        <td class="sum-value subtotal-text"
                                                            style="
    font-weight: bold;
    color: green;
    font-size: 18px;
">
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="sum-label">Discount</td>
                                                        <td class="sum-value" id="discount-text"></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="sum-label">Tax ({{ $tax->rate }}%)</td>
                                                        <td class="sum-value" id="tax-amunt-value"></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="sum-label"
                                                            style="
    font-weight: bold;
    color: #303030;
    font-size: 18px;
">
                                                            Tip</td>
                                                        <td class="sum-value tip-value"
                                                            style="
    font-weight: bold;
    color: #303030;
    font-size: 18px;
">
                                                        </td>
                                                    </tr>
                                                    <tr class="total-row">
                                                        <td class="total-label"style="
        font-weight: bold;
        color: green;
        font-size: 18px;
    ">Total</td>
                                                        <td class="total-value" id="final-total"
                                                            style="
    font-weight: bold;
    color: green;
    font-size: 18px;
">
                                                        </td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>

                                    <div class="col-12 d-flex justify-content-between">
                                        <button class="btn btn-primary btn-prev next-btn-invoice">
                                            <i class="bx bx-left-arrow-alt bx-sm ms-sm-n2 me-sm-2"></i>
                                            <span class="align-middle d-sm-inline-block d-none">Previous</span>
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
        <!-- / Content -->
    </div>
@endsection

@push('script')
    <script>
        $(document).ready(function() {

            // 🔍 Search customer by contact
            $('#search-contact').on('keyup', function() {
                var contact = $(this).val();
                if (contact.length < 10) return false;

                $.ajax({
                    url: "{{ route('admin.invoice.customerDetials') }}",
                    type: "GET",
                    data: {
                        contact: contact
                    },
                    success: function(response) {
                        console.log(response);
                        $('#append-customer').empty(); // reset before append
                        if (response.data.length == 0) {
                            $('#tab-new').trigger('click');
                            $('#new-customer-phone').val(contact);
                            $('#customer_id').val('');

                        } else {
                            $.each(response.data, function(key, customer) {
                                $('#customer_id').val(customer.customer_id);
                                $('#new-customer-phone').val(customer.customer_phone);
                                $('#new-customer-name').val(customer.customer_name);
                                $('#new-customer-email').val(customer.customer_email);

                                $('#append-customer').append(`
                            <div class="customer-card">
                                <p>Customer : ${customer.customer_name}</p>
                                <p>Total Visit : ${customer.appointments_count}</p>
                                <p>Total Spent : ${customer.appointments_sum_total_amt}</p>
                            </div>
                        `);
                            });
                        }
                    }
                });
            });

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
                    <span>${p.service} ($ ${p.price})</span>
                    <select class="staff-select form-control" data-product-id="${p.id}" data-flag="${p.flag}"  data-index="${index}">
                        <option value="">-- Select Staff --</option>`;
                    staff.forEach(s => {
                        html += `<option value="${s.id}">${s.service}</option>`;
                    });
                    html += `</select>
                 </div></div>`;
                });

                $(".staff-service-combination").html(html);

                productStaffAssignments = []; // global array

            }


            $(document).on('click', '.check-staff-assign', function(e) {
                let allSelected = true;

                // loop through all staff-select dropdowns
                $(".staff-service-combination .staff-select").each(function() {
                    if ($(this).val() === "") {
                        allSelected = false;
                        $(this).css("border", "1px solid red"); // highlight empty
                    } else {
                        $(this).css("border", "");
                    }
                });

                if (!allSelected) {
                    e.preventDefault(); // prevent stepper from moving
                    alert("Please assign staff for all products before proceeding!");
                    return false;
                }

            });

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


            function calculateTotal() {
                // 1. Calculate subtotal
                let subTotal = productStaffAssignments.reduce((sum, item) => sum + parseFloat(item.price), 0);

                // 2. Get discount type and value
                let discountType = $('#discount-type').val(); // 'percent' or 'fixed' (example)
                let discountValue = parseFloat($('#discount-value').val()) || 0;

                // 3. Apply discount
                let discountedTotal = subTotal;
                let discountDisplayValue = 0;

                let tax = "{{ $tax->rate }}";

                if (discountType === '2') {
                    discountDisplayValue = (subTotal * discountValue / 100);
                    discountedTotal = subTotal - (subTotal * discountValue / 100);
                } else if (discountType === '3') {
                    discountedTotal = subTotal - discountValue;
                    discountDisplayValue = discountValue;
                }


                let taxAmount = (discountedTotal * tax) / 100;

                // 4. Add tip
                let tip = parseFloat($('#tip').val()) || 0;
                let finalTotal = discountedTotal + taxAmount + tip;

                // 5. Update UI
                // $('#sub-total').text(subTotal.toFixed(2));
                $('#final-total').text('$' + finalTotal.toFixed(2));
                $('#tax-amunt-value').text('$' + taxAmount.toFixed(2));

                $('.subtotal-text').text('$' + subTotal.toFixed(2));
                $('.tip-value').text('$' + tip.toFixed(2));
                $('#discount-text').text('$' + discountDisplayValue.toFixed(2));

                renderProductList();
                return finalTotal;
            }

            // Trigger recalculation on any change
            $('#discount-type, #discount-value, #tip').on('change keyup', function() {
                calculateTotal();

            });

            $('.calculateFinalTotal').on('click', function() {
                calculateTotal();
            });



            function renderProductList() {
                let html = "";

                // Count quantities for the same product + staff
                let productMap = {};

                productStaffAssignments.forEach(item => {
                    let key = item.flag + "_" + item.productId + "_" + item
                    .staffId; // unique key for product+staff
                    if (productMap[key]) {
                        productMap[key].qty += 1;
                    } else {
                        productMap[key] = {
                            name: item.productName,
                            price: parseFloat(item.price),
                            qty: 1
                        };
                    }
                });

                // Build table rows
                Object.values(productMap).forEach(p => {
                    html += `<tr>
                    <td>${p.name} x${p.qty}</td>
                    <td style="text-align:right;">$${(p.price * p.qty).toFixed(2)}</td>
                 </tr>`;
                });

                $("#selected-services-list").html(html);
            }


            $('.btn-submit').on('click', function() {

                let oldHtml = $(this).html(); // sto
                $(this).prop('disabled', true).html(`
                    <span class="spinner-border spinner-border-sm me-2"></span> Processing...
                `);
                // 1. Calculate subtotal
                let subTotal = productStaffAssignments.reduce((sum, item) => sum + parseFloat(item.price),
                    0);

                // 2. Get discount type and value
                let discountType = $('#discount-type').val(); // 'percent' or 'fixed' (example)
                let discountValue = parseFloat($('#discount-value').val()) || 0;

                // 3. Apply discount
                let discountedTotal = subTotal;
                let discountDisplayValue = 0;

                let tax = "{{ $tax->rate }}";

                if (discountType === '2') {
                    discountDisplayValue = (subTotal * discountValue / 100);
                    discountedTotal = subTotal - (subTotal * discountValue / 100);
                } else if (discountType === '3') {
                    discountedTotal = subTotal - discountValue;
                    discountDisplayValue = discountValue;
                }


                let taxAmount = (discountedTotal * tax) / 100;

                // 4. Add tip
                let tip = parseFloat($('#tip').val()) || 0;
                let finalTotal = discountedTotal + taxAmount + tip;

                // 5. Update UI
                // $('#sub-total').text(subTotal.toFixed(2));
                $('#final-total').text('$' + finalTotal.toFixed(2));
                $('#tax-amunt-value').text('$' + taxAmount.toFixed(2));

                $('.subtotal-text').text('$' + subTotal.toFixed(2));
                $('.tip-value').text('$' + tip.toFixed(2));
                $('#discount-text').text('$' + discountDisplayValue.toFixed(2));


                // 🔹 Prepare full data
                let payload = {
                    _token: $('meta[name="csrf-token"]').attr("content"),
                    products: productStaffAssignments,
                    totals: {
                        subTotal: subTotal.toFixed(2),
                        discountType: discountType,
                        discountAmount: discountDisplayValue.toFixed(2),
                        tip: tip.toFixed(2),
                        tax: taxAmount,
                        finalTotal: finalTotal
                    },
                    customer: {
                        contact: $('#new-customer-phone').val(),
                        name: $('#new-customer-name').val(),
                        email: $('#new-customer-email').val(),
                    },
                    payment_method: $('#payment-method').val(),
                };

                // 🔹 Send AJAX
                $.ajax({
                    url: "{{ route('admin.invoice.store') }}",
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
            calculateTotal();


        });
    </script>
@endpush
