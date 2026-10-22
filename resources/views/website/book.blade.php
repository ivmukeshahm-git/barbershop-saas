<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $shopName ?? 'Misha Hair Studio Mumbai' }} - Appointment Book</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link href="{{ asset('assets/img/logo.png') }}" rel="icon" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <link href="{{ asset('frontend/css/booking-luxury.css') }}?v=10" rel="stylesheet">
    <style>
        /* Keep original loader animation */
        .loader {
            width: 120px; height: 150px; background-color: #fff; background-repeat: no-repeat;
            background-image: linear-gradient(#ddd 100px, transparent 0),
                linear-gradient(#ddd 100px, transparent 0),
                linear-gradient(#ddd 100px, transparent 0),
                linear-gradient(#ddd 100px, transparent 0),
                linear-gradient(#ddd 100px, transparent 0),
                linear-gradient(#ddd 100px, transparent 0),
                linear-gradient(#ddd 100px, transparent 0),
                linear-gradient(#ddd 100px, transparent 0),
                linear-gradient(#ddd 100px, transparent 0);
            background-size: 21px 20px, 4px 53px, 4px 53px, 4px 53px, 4px 53px, 4px 53px, 4px 53px, 4px 53px, 4px 53px;
            background-position: 50% 80px, 0% 50%, 14% 50%, 28% 50%, 42% 50%, 57% 50%, 71% 50%, 85% 50%, 100% 50%;
            position: relative; border-radius: 6px; animation: pianoTyping1 1s linear infinite alternate; margin: 20% auto;
            box-shadow: 0 0 10px rgba(0,0,0,.25);
        }
        @keyframes pianoTyping1 {
            0%{background-position:50% 80px,0% 50%,14% 50%,28% 50%,42% 50%,57% 50%,71% 50%,85% 50%,100% 50%}
            5%,15%,25%,35%,45%,55%,65%,75%,85%{background-position:50% 80px,0% 50%,14% 50%,28% 50%,42% 50%,57% 50%,71% 50%,85% 50%,100% 50%}
            10%,14%,30%,34%,50%,54%,70%,74%,90%{background-position:50% 80px,0% 55%,14% 55%,28% 55%,42% 55%,57% 55%,71% 55%,85% 55%,100% 55%}
            to{background-position:50% 80px,0% 50%,14% 50%,28% 50%,42% 50%,57% 50%,71% 50%,85% 50%,100% 50%}
        }
        .loader:before {
            content:''; position:absolute; left:10px; right:10px; top:10px; bottom:10px; border-radius:8px;
            background:#f5f5f5 no-repeat; background-size:60px 10px;
            background-image:linear-gradient(#ddd 100px,transparent 0),linear-gradient(#ddd 100px,transparent 0),linear-gradient(#ddd 100px,transparent 0),linear-gradient(#ddd 100px,transparent 0),linear-gradient(#ddd 100px,transparent 0),linear-gradient(#ddd 100px,transparent 0);
            background-position:15px 30px,15px 60px,15px 90px,105px 30px,105px 60px,105px 90px;
            box-shadow:0 0 10px rgba(0,0,0,.25);
        }
        .loader:after {
            content:''; position:absolute; width:calc(50% - 10px); right:10px; top:10px; bottom:10px; border-radius:8px;
            background:#fff no-repeat; background-size:60px 10px;
            background-image:linear-gradient(#ddd 100px,transparent 0),linear-gradient(#ddd 100px,transparent 0),linear-gradient(#ddd 100px,transparent 0);
            background-position:50% 30px,50% 60px,50% 90px; transform:rotateY(0deg); transform-origin:left center;
            animation:paging 1s linear infinite;
        }
        @keyframes paging { to { transform: rotateY(-180deg); } }
        .shop-close-error { color:#b91c1c; font-weight:600; padding:12px; }
    </style>
</head>
<body class="bk-page">
    {{-- Premium full-page booking loader --}}
    <div class="bk-book-loader" id="Loader" aria-live="polite" aria-busy="true" hidden>
        <div class="bk-book-loader-inner">
            <img class="bk-book-loader-logo" src="{{ asset('assets/img/logo.png') }}" alt="{{ $shopName ?? 'Misha Hair Studio Mumbai' }}">
            <div class="bk-book-loader-pole" aria-hidden="true">
                <span></span>
            </div>
            <h3 class="bk-book-loader-title">Booking Your Appointment<span class="bk-dots" aria-hidden="true"><span></span><span></span><span></span></span></h3>
            <p class="bk-book-loader-sub">Please wait while we confirm your booking.</p>
        </div>
    </div>

    {{-- Booking Failed modal --}}
    <div class="modal fade" id="BookingErrorModal" tabindex="-1" aria-labelledby="BookingErrorTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bk-error-modal">
                <div class="modal-body text-center p-4 p-md-5">
                    <div class="bk-error-ico"><i class="bi bi-exclamation-triangle-fill"></i></div>
                    <h4 id="BookingErrorTitle" class="mb-2">Booking Failed</h4>
                    <p class="text-muted mb-1" id="BookingErrorMessage">Something went wrong.</p>
                    <p class="text-muted mb-4">Please try again.</p>
                    <div class="d-flex flex-column flex-sm-row gap-2 justify-content-center">
                        <button type="button" class="btn btn-primary bk-error-retry" id="BookingErrorRetry">Try Again</button>
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <header class="bk-header">
        <div class="container">
            <div class="inner">
                <a href="{{ route('home') }}" class="bk-logo" aria-label="{{ $shopName ?? 'Misha Hair Studio Mumbai' }} Home">
                    <img class="bk-logo-img" src="{{ asset('assets/img/logo.png') }}" alt="{{ $shopName ?? 'Misha Hair Studio Mumbai' }}" width="56" height="56">
                    <span class="bk-brand-copy">
                        <strong>{{ $shopName ?? 'Misha Hair Studio Mumbai' }}</strong>
                        <span>Barbershop</span>
                    </span>
                </a>
                <a href="{{ route('home') }}" class="home-link"><i class="fas fa-arrow-left"></i> Back to Home</a>
            </div>
        </div>
    </header>

    {{-- Premium Booking Success (shown after successful submit) --}}
    <div class="bk-success-page" id="BookingSuccessPage" hidden>
        <canvas id="bkConfettiCanvas" class="bk-confetti-canvas" aria-hidden="true"></canvas>
        <div class="container">
            <div class="bk-success-wrap">
                <div class="bk-success-hero">
                    <div class="bk-success-check" aria-hidden="true">
                        <svg viewBox="0 0 52 52" class="bk-check-svg">
                            <circle class="bk-check-circle" cx="26" cy="26" r="25" fill="none"/>
                            <path class="bk-check-mark" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
                        </svg>
                    </div>
                    <h1>Booking Confirmed!</h1>
                    <p class="bk-success-sub">Your appointment has been successfully booked.<br>We look forward to seeing you.</p>
                    <span class="bk-status-badge"><i class="bi bi-check-circle-fill"></i> Confirmed</span>
                    <p class="bk-email-sent" id="SuccessEmailSent" hidden>
                        <i class="bi bi-envelope-check-fill"></i> Confirmation email has been sent.
                    </p>
                </div>

                <div class="bk-success-card">
                    <div class="bk-success-card-head">
                        <h2><i class="bi bi-receipt-cutoff"></i> Booking Summary</h2>
                    </div>
                    <div class="row g-3 bk-success-grid">
                        <div class="col-md-12">
                            <div class="bk-success-field">
                                <span class="lbl">Booking Code</span>
                                <span class="val d-flex flex-wrap align-items-center gap-2">
                                    <strong id="SuccessApptNumber" style="letter-spacing:.04em;">—</strong>
                                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill" id="SuccessCopyCodeBtn">
                                        <i class="bi bi-clipboard"></i> Copy
                                    </button>
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="bk-success-field">
                                <span class="lbl">Booking ID</span>
                                <span class="val" id="SuccessBookingId">—</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="bk-success-field">
                                <span class="lbl"><i class="bi bi-person-vcard"></i> Customer Name</span>
                                <span class="val" id="SuccessCustomer">—</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="bk-success-field">
                                <span class="lbl"><i class="bi bi-scissors"></i> Service</span>
                                <span class="val" id="SuccessService">—</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="bk-success-field">
                                <span class="lbl"><i class="bi bi-person"></i> Barber</span>
                                <span class="val" id="SuccessBarber">—</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="bk-success-field">
                                <span class="lbl"><i class="bi bi-calendar3"></i> Appointment Date</span>
                                <span class="val" id="SuccessDate">—</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="bk-success-field">
                                <span class="lbl"><i class="bi bi-clock"></i> Appointment Time</span>
                                <span class="val" id="SuccessTime">—</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="bk-success-field">
                                <span class="lbl"><i class="bi bi-hourglass-split"></i> Duration</span>
                                <span class="val" id="SuccessDuration">—</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="bk-success-field">
                                <span class="lbl"><i class="bi bi-cash-coin"></i> Total Price</span>
                                <span class="val bk-success-price" id="SuccessPrice">—</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="bk-success-field">
                                <span class="lbl"><i class="bi bi-telephone"></i> Phone</span>
                                <span class="val" id="SuccessPhone">—</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="bk-success-field">
                                <span class="lbl"><i class="bi bi-envelope"></i> Email</span>
                                <span class="val" id="SuccessEmail">—</span>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="bk-success-field">
                                <span class="lbl"><i class="bi bi-journal-text"></i> Notes</span>
                                <span class="val" id="SuccessNotes">—</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bk-success-card">
                    <div class="bk-success-card-head">
                        <h2><i class="bi bi-geo-alt"></i> Shop Information</h2>
                    </div>
                    <ul class="bk-shop-info-list">
                        <li>
                            <i class="bi bi-geo-alt-fill"></i>
                            <div>
                                <strong>Address</strong>
                                <span>{{ $siteSetting->contact_address ?? '245 King Street, Downtown, Toronto, Ontario, Canada' }}</span>
                            </div>
                        </li>
                        <li>
                            <i class="bi bi-telephone-fill"></i>
                            <div>
                                <strong>Contact</strong>
                                <span><a href="tel:{{ optional($siteSetting)->phoneTel() ?: '+15875552489' }}">{{ $siteSetting->contact_phone ?? '+1 (587) 555-2489' }}</a></span>
                            </div>
                        </li>
                        <li>
                            <i class="bi bi-envelope-fill"></i>
                            <div>
                                <strong>Email</strong>
                                <span><a href="mailto:{{ $siteSetting->contact_email ?? 'info@bluecrownbarbers.com' }}">{{ $siteSetting->contact_email ?? 'info@bluecrownbarbers.com' }}</a></span>
                            </div>
                        </li>
                    </ul>
                </div>

                <div class="bk-success-actions">
                    <a href="{{ route('track-booking') }}" class="btn btn-primary bk-success-btn-primary" id="SuccessTrackBtn">
                        <i class="bi bi-geo-alt me-1"></i> Track Booking
                    </a>
                    <a href="{{ route('home') }}" class="btn btn-outline-primary bk-success-btn-secondary">
                        <i class="bi bi-house-door me-1"></i> Back to Home
                    </a>
                    <a href="{{ route('book') }}" class="btn btn-outline-primary bk-success-btn-secondary">
                        <i class="bi bi-calendar-plus me-1"></i> Book Another Appointment
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div id="BookingWizardShell">
    <div class="bk-intro container" data-aos="fade-up">
        <h1>Book Your Appointment</h1>
        <p>Schedule your premium grooming experience in just a few simple steps.</p>
    </div>

    <div class="container bk-layout">
        <div class="row g-4 align-items-start">
            {{-- LEFT: stepper + summary --}}
            <div class="col-lg-4" data-aos="fade-right">
                <aside class="bk-sidebar">
                    <div class="bk-side-card">
                        <h3>Booking Progress</h3>
                        <div class="bk-stepper-vert">
                            <div class="bk-step-row" data-step-row="1">
                                <div class="progress-step active" id="step-indicator-1">1</div>
                                <div>
                                    <div class="step-title">Booking Method</div>
                                    <div class="step-desc">Service or barber first</div>
                                </div>
                            </div>
                            <div class="progress-line" id="line-1"></div>

                            <div class="bk-step-row" data-step-row="2">
                                <div class="progress-step pending" id="step-indicator-2">2</div>
                                <div>
                                    <div class="step-title" id="StepSecondType">Choose Services</div>
                                    <div class="step-desc">Pick what you need</div>
                                </div>
                            </div>
                            <div class="progress-line" id="line-2"></div>

                            <div class="bk-step-row" data-step-row="3">
                                <div class="progress-step pending" id="step-indicator-3">3</div>
                                <div>
                                    <div class="step-title">Date &amp; Time</div>
                                    <div class="step-desc">Select your slot</div>
                                </div>
                            </div>
                            <div class="progress-line" id="line-3"></div>

                            <div class="bk-step-row" data-step-row="4">
                                <div class="progress-step pending" id="step-indicator-4">4</div>
                                <div>
                                    <div class="step-title">Your Details</div>
                                    <div class="step-desc">Contact information</div>
                                </div>
                            </div>
                            <div class="progress-line" id="line-4"></div>

                            <div class="bk-step-row" data-step-row="5">
                                <div class="progress-step pending" id="step-indicator-5">5</div>
                                <div>
                                    <div class="step-title">Confirmation</div>
                                    <div class="step-desc">Review &amp; book</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bk-side-card bk-summary-card">
                        <h3><i class="bi bi-receipt-cutoff me-1"></i> Booking Summary</h3>
                        <div class="bk-summary-row">
                            <span class="lbl"><i class="bi bi-scissors"></i> Service</span>
                            <span class="val" id="liveService">-</span>
                        </div>
                        <div class="bk-summary-row">
                            <span class="lbl"><i class="bi bi-person"></i> Barber</span>
                            <span class="val" id="liveBarber">-</span>
                        </div>
                        <div class="bk-summary-row">
                            <span class="lbl"><i class="bi bi-calendar3"></i> Date</span>
                            <span class="val" id="liveDate">-</span>
                        </div>
                        <div class="bk-summary-row">
                            <span class="lbl"><i class="bi bi-clock"></i> Time</span>
                            <span class="val" id="liveTime">-</span>
                        </div>
                        <div class="bk-summary-row">
                            <span class="lbl"><i class="bi bi-hourglass-split"></i> Duration</span>
                            <span class="val" id="liveDuration">-</span>
                        </div>
                        <div class="bk-summary-total">
                            <span><i class="bi bi-cash-coin me-1"></i> Total</span>
                            <strong id="liveTotal">{{ shop_currency_symbol() }}0</strong>
                        </div>
                    </div>

                    <div class="bk-side-card">
                        <h3>Why Book With Us</h3>
                        <div class="bk-trust">
                            <div class="item"><i class="fas fa-shield-halved"></i><span>100% Secure Booking</span></div>
                            <div class="item"><i class="fas fa-calendar-xmark"></i><span>Free Cancellation</span></div>
                            <div class="item"><i class="fas fa-gem"></i><span>Premium Experience</span></div>
                        </div>
                    </div>
                </aside>
            </div>

            {{-- RIGHT: steps --}}
            <div class="col-lg-8" data-aos="fade-up">
        <div class="wizard-container">
            <div class="card shadow bk-main-card">
                <div class="card-body">
                    <!-- Hidden legacy progress placeholder (indicators live in sidebar) -->
                    <div class="row mb-4 d-none">
                        <div class="col-12"></div>
                    </div>

                    <!-- Step 1: Personal Information -->
                    <div class="step-content active" id="step-1">
                        <h5 class="mb-1">
                            <i class="fas fa-hand-pointer me-2 text-primary"></i>
                            Choose Your Booking Method
                        </h5>
                        <p class="mb-4 text-muted">Select whether to start by service or preferred barber</p>
                        <div class="row mb-3">
                            <div class="col-lg-12">
                                <div class="toggle-tabs">
                                    <button class="tab-btn active" id="byServiceBtn"
                                        onclick="showWizardTab('service')" type="button">
                                        <span class="tab-btn-title">Book by Service</span>
                                        <span class="tab-btn-sub">Select services &amp; quantity, then choose barbers</span>
                                    </button>
                                    <button class="tab-btn" id="byStaffBtn" onclick="showWizardTab('staff')" type="button">
                                        <span class="tab-btn-title">Book by Barber</span>
                                        <span class="tab-btn-sub">Choose a barber first — quantity option is not available</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" id="step_one_staff_service" name="step_one_staff_service">
                        <input type="hidden" id="step_one_select_type" name="step_one_select_type" value="Service">
                        <div id="byServicePanel">
                            <div class="row">
                                <label class="form-label fs-5 fw-bold mb-1">Choose Your Service</label>
                                <p class="mb-3 text-muted">Select one or more services for your appointment</p>
                                <div class="col-md-12 mb-3">
                                    @foreach ($servicesByCategory as $categoryId => $servicesGroup)
                                        @php
                                            $categoryName =
                                                $servicesGroup->first()->category_name ?? 'Unknown Category';
                                        @endphp
                                        <div class="card px-0 py-2 border-0 shadow-none mb-4" style="background: transparent;">
                                            <div class="fw-bold fs-6 mb-3" style="color:#0F172A;">
                                                <i class="fas fa-layer-group me-2" style="color:#136207;"></i>{{ $categoryName }}
                                            </div>
                                            <div class="row g-3">
                                                @foreach ($servicesGroup as $service)
                                                    <div class="col-md-6 col-lg-4">
                                                        <div
                                                            class="service-card step-one-service-card px-3 py-3 h-100 d-flex flex-column justify-content-between"
                                                            data-duration="{{ $service->duration }}"
                                                            data-price="{{ $service->price }}"
                                                            data-qty="0">
                                                            <input type="hidden" id="service_id" name="service_id"
                                                                class="service_id_select"
                                                                value="{{ $service->service_id }}">
                                                            <div class="fw-semibold">{{ $service->service_name }}
                                                            </div>
                                                            <div
                                                                class="d-flex justify-content-between align-items-center mt-2 gap-2">
                                                                <div class="text-muted"><i class="bx bx-time"></i>
                                                                    {{ $service->duration }} min</div>
                                                                <div class="fw-bold text-danger fs-6">
                                                                    {{ shop_currency_symbol() }}{{ number_format((float) $service->price, 2) }}</div>
                                                            </div>
                                                            <div class="bk-qty-wrap" data-qty-wrap>
                                                                <div class="bk-qty-label">Select qty</div>
                                                                <div class="bk-qty">
                                                                    <button type="button" class="bk-qty-btn bk-qty-minus" aria-label="Decrease quantity">−</button>
                                                                    <span class="bk-qty-val">1</span>
                                                                    <button type="button" class="bk-qty-btn bk-qty-plus" aria-label="Increase quantity">+</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                @if(!empty($defaultGst))
                                    <div class="gst-price-note {{ $defaultGst->isTaxExcluded() ? 'gst-price-note--extra' : 'gst-price-note--include' }}">
                                        <i class="fas {{ $defaultGst->isTaxExcluded() ? 'fa-exclamation-circle' : 'fa-check-circle' }}"></i>
                                        <span>{{ $defaultGst->websiteNote() }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div id="byStaffPanel" class="d-none">
                            <div class="mb-4">
                                <label class="form-label fs-5 fw-bold mb-1">Choose Your
                                    Barber</label>
                                <p class="mb-3 text-muted">Select a staff member for
                                    your appointment</p>
                                <div class="card px-0 py-2 border-0 shadow-none" style="background: transparent;">
                                    <div class="row g-3">
                                        <!-- Any staff -->
                                        @forelse ($Staff as $row)
                                            <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                                                <div
                                                    class="staff-card step-one-staff-card h-100 d-flex align-items-center p-3 justify-content-between">
                                                    <input type="hidden" id="staff_id" name="staff_id"
                                                        value="{{ $row->id }}" class="staff_id_select">
                                                    <div class="d-flex align-items-center">
                                                        <img src="{{ $row->profile_image_url }}"
                                                            class="rounded-circle me-3 bk-staff-photo" width="48" height="48"
                                                            alt="{{ $row->name }}"
                                                            onerror="this.onerror=null;this.src='{{ asset('assets/img/user.svg') }}';" />
                                                        <div>
                                                            <div class="fw-semibold">{{ $row->name }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <span
                                                        class="badge bg-light fw-semibold border border-success text-success ms-2">Available</span>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="col-12">
                                                <div class="shop-close-error">
                                                    No Staff Found
                                                </div>
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Step 2: Service or Barber selection -->
                    <div class="step-content" id="step-2">
                        <input type="hidden" id="step_two_staff_service" name="step_two_staff_service">
                        <input type="hidden" id="step_two_select_type" name="step_two_select_type" value="Staff">

                        {{-- Shown when booking by Barber first --}}
                        <div id="ServiceByStaffStep" style="display: none;">
                            <label class="form-label fs-5 fw-bold mb-1">Choose Your Service</label>
                            <p class="mb-3 text-muted">Select one or more services for your appointment</p>
                            <div id="StepTwoServiceHtml"></div>
                            @if(!empty($defaultGst))
                                <div class="gst-price-note {{ $defaultGst->isTaxExcluded() ? 'gst-price-note--extra' : 'gst-price-note--include' }}">
                                    <i class="fas {{ $defaultGst->isTaxExcluded() ? 'fa-exclamation-circle' : 'fa-check-circle' }}"></i>
                                    <span>{{ $defaultGst->websiteNote() }}</span>
                                </div>
                            @endif
                        </div>

                        {{-- Shown when booking by Service first --}}
                        <div id="StaffByServiceStep">
                            <label class="form-label fs-5 fw-bold mb-1">Choose Your Barber</label>
                            <p class="mb-3 text-muted" id="barberStepHint">Select a staff member for your appointment</p>
                            <div class="row g-3" id="StepTwoStaffHtml"></div>
                            <div id="StepTwoStaffAssign" class="d-none"></div>
                        </div>
                    </div>

                    <!-- Step 3: Date & Time -->
                    <div class="step-content" id="step-3">
                        <div class="bk-dt-head">
                            <h5 class="mb-1">
                                <i class="fa fa-calendar-check me-2 text-primary"></i>
                                Choose Date &amp; Time
                            </h5>
                            <p class="text-muted mb-0">Select a date, then pick an available time</p>
                        </div>

                        {{-- Selection info card --}}
                        <div class="bk-dt-info-card" id="dtContextStrip">
                            <div class="bk-dt-info-main">
                                <div class="bk-dt-info-service" id="dtCtxService">-</div>
                                <div class="bk-dt-info-barber">with <strong id="dtCtxBarber">-</strong></div>
                            </div>
                            <div class="bk-dt-info-meta">
                                <div class="bk-dt-info-item">
                                    <i class="fas fa-clock"></i>
                                    <span>Duration: <strong><span id="ServiceMinite">0</span> Minutes</strong></span>
                                </div>
                                <div class="bk-dt-info-item bk-dt-info-price">
                                    <i class="fas fa-tag"></i>
                                    <span>Price: <strong id="dtCtxPrice">{{ shop_currency_symbol() }}0</strong></span>
                                </div>
                            </div>
                            {{-- keep for JS duration sync --}}
                            <span class="d-none" id="ServiceMinite1">0</span>
                        </div>

                        <div class="bk-datetime bk-datetime-stack">
                            {{-- Full-width calendar --}}
                            <div class="slot-calendar" id="slotCalendarWrap">
                                <div class="bk-section-label">
                                    <i class="fas fa-calendar-alt"></i> Calendar
                                </div>
                                <div class="bk-cal-header">
                                    <button type="button" class="bk-cal-nav" id="prevMonthBtn" aria-label="Previous month">
                                        <i class="fa fa-chevron-left"></i>
                                    </button>
                                    <span id="calendarMonth" class="bk-cal-title">August 2026</span>
                                    <button type="button" class="bk-cal-nav" id="nextMonthBtn" aria-label="Next month">
                                        <i class="fa fa-chevron-right"></i>
                                    </button>
                                </div>
                                <div class="bk-cal-table-wrap">
                                    <table class="table table-borderless mb-0" id="calendarTable">
                                        <thead>
                                            <tr class="text-center">
                                                <th>Su</th>
                                                <th>Mo</th>
                                                <th>Tu</th>
                                                <th>We</th>
                                                <th>Th</th>
                                                <th>Fr</th>
                                                <th>Sa</th>
                                            </tr>
                                        </thead>
                                        <tbody id="calendarBody"></tbody>
                                    </table>
                                </div>
                                <div class="bk-cal-legend">
                                    <span><i class="bk-dot bk-dot-avail"></i> Available</span>
                                    <span><i class="bk-dot bk-dot-today"></i> Today</span>
                                    <span><i class="bk-dot bk-dot-off"></i> Unavailable</span>
                                </div>
                            </div>

                            {{-- Time slots below --}}
                            <div class="bk-time-panel">
                                <div class="bk-time-header">
                                    <div class="bk-section-label mb-0">
                                        <i class="fas fa-clock"></i> Available Time Slots
                                    </div>
                                    <div class="bk-slot-count" id="bkSlotCount" hidden>
                                        <span class="bk-slot-count-dot"></span>
                                        <span id="bkSlotCountText">0 Available slots</span>
                                    </div>
                                </div>

                                <div id="timeSlotsMorning" class="bk-slot-scroll">
                                    <div id="MorningSlotHtml">
                                        <div class="bk-slot-empty-state">
                                            <div class="bk-empty-illu">
                                                <i class="fas fa-calendar-day"></i>
                                            </div>
                                            <h6>Select a date</h6>
                                            <p>Choose a day on the calendar to see available times.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-none" id="timeSlotsEvening">
                                    <div class="bk-slot-grid" id="EveningSlotHtml"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Step 4: Security -->
                    <div class="step-content" id="step-4">
                        <h5 class="mb-3">
                            <i class="fas fa-user-pen me-2 text-primary"></i>
                            Your Details
                        </h5>
                        <p class="text-muted mb-4">Tell us how to reach you about your appointment</p>
                        <div class="mb-3">
                            <label for="phone" class="form-label">Full Name <span
                                    style="color: red;">*</span></label>
                            <input type="text" class="form-control" id="full_name" name="full_name"
                                placeholder="Enter Your Full Name">
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Phone Number <span style="color: red;">*</span>
                            </label>
                            <input type="tel" class="form-control" id="phone_number" name="phone_number"
                                placeholder="Enter Phone Number" maxlength="10">
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">Email (Optional)</label>
                            <input type="email" class="form-control" id="email" name="email"
                                placeholder="Enter Your Email">
                        </div>
                        <div class="mb-3">
                            <label for="note" class="form-label">Notes (Optional)</label>
                            <textarea class="form-control" id="note" name="note" rows="3" placeholder="Enter Note"></textarea>
                        </div>
                    </div>
                    <!-- Step 5: Confirm Your Booking -->
                    <div class="step-content" id="step-5">
                        <div class="bk-confirm-head">
                            <h5 class="mb-1">
                                <i class="bi bi-check-circle-fill me-2 text-primary"></i>
                                Confirm Your Booking
                            </h5>
                            <p class="text-muted mb-0">Review your appointment before confirming.</p>
                        </div>

                        <div class="bk-confirm-stack">
                            {{-- Card 1: Appointment Details --}}
                            <div class="bk-confirm-card" data-aos="fade-up">
                                <div class="bk-confirm-card-head">
                                    <span class="bk-confirm-ico"><i class="bi bi-calendar2-check"></i></span>
                                    <h6>Appointment Details</h6>
                                </div>
                                <div class="bk-confirm-meta-grid">
                                    <div class="bk-confirm-meta">
                                        <span class="meta-lbl"><i class="bi bi-calendar3"></i> Date</span>
                                        <span class="meta-val" id="ConfirmDate">-</span>
                                    </div>
                                    <div class="bk-confirm-meta">
                                        <span class="meta-lbl"><i class="bi bi-clock"></i> Time</span>
                                        <span class="meta-val" id="ConfirmTime">-</span>
                                    </div>
                                    <div class="bk-confirm-meta">
                                        <span class="meta-lbl"><i class="bi bi-hourglass-split"></i> Duration</span>
                                        <span class="meta-val" id="TotalDuration">-</span>
                                    </div>
                                </div>
                                {{-- Keep composite value for existing summary sync --}}
                                <span id="AppointmentDate" class="d-none"></span>
                            </div>

                            {{-- Card 2: Selected Service(s) — filled by FetchAppointmentDetails --}}
                            <div class="bk-confirm-card" data-aos="fade-up">
                                <div class="bk-confirm-card-head">
                                    <span class="bk-confirm-ico"><i class="bi bi-scissors"></i></span>
                                    <h6>Selected Service</h6>
                                </div>
                                <div class="service-details bk-confirm-services" id="ViewServiceHtml"></div>
                            </div>

                            {{-- Card 3: Selected Barber — filled by FetchAppointmentDetails --}}
                            <div class="bk-confirm-card" data-aos="fade-up">
                                <div class="bk-confirm-card-head">
                                    <span class="bk-confirm-ico"><i class="bi bi-person-badge"></i></span>
                                    <h6>Selected Barber</h6>
                                </div>
                                <div id="ViewStaffHtml" class="bk-confirm-staff-wrap"></div>
                            </div>

                            {{-- Card 4: Customer Details --}}
                            <div class="bk-confirm-card" data-aos="fade-up">
                                <div class="bk-confirm-card-head">
                                    <span class="bk-confirm-ico"><i class="bi bi-person"></i></span>
                                    <h6>Customer Details</h6>
                                </div>
                                <div class="row g-3 customer-details">
                                    <div class="col-md-6">
                                        <div class="bk-confirm-field">
                                            <span class="field-lbl"><i class="bi bi-person"></i> Full Name</span>
                                            <span class="field-val" id="PreviewName">-</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="bk-confirm-field">
                                            <span class="field-lbl"><i class="bi bi-telephone"></i> Phone</span>
                                            <span class="field-val" id="PreviewPhone">-</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="bk-confirm-field">
                                            <span class="field-lbl"><i class="bi bi-envelope"></i> Email</span>
                                            <span class="field-val" id="PreviewEmail">-</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="bk-confirm-field">
                                            <span class="field-lbl"><i class="bi bi-journal-text"></i> Notes</span>
                                            <span class="field-val" id="PreviewNote">-</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Card 5: Payment Summary --}}
                            <div class="bk-confirm-card bk-confirm-pay" data-aos="fade-up">
                                <div class="bk-confirm-card-head">
                                    <span class="bk-confirm-ico"><i class="bi bi-credit-card"></i></span>
                                    <h6>Payment Summary</h6>
                                </div>
                                <div class="bk-pay-rows">
                                    <div class="bk-pay-row">
                                        <span>Subtotal</span>
                                        <strong id="ConfirmSubtotal">{{ shop_currency_symbol() }}0.00</strong>
                                    </div>
                                    <div class="bk-pay-row">
                                        <span id="ConfirmTaxLabel">Tax</span>
                                        <strong id="ConfirmTax">{{ shop_currency_symbol() }}0.00</strong>
                                    </div>
                                    <div class="bk-pay-row bk-pay-total">
                                        <span>Total</span>
                                        <strong id="ConfirmGrandTotal">{{ shop_currency_symbol() }}0.00</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Navigation Buttons -->
                <div class="card-footer bg-light">
                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-outline-secondary" id="prevBtn"
                            onclick="changeStep(-1,true)" disabled>
                            <i class="bi bi-arrow-left me-1"></i>
                            Previous
                        </button>
                        <button type="button" class="btn btn-primary" id="nextBtn" onclick="changeStep(1)">
                            Next
                            <i class="bi bi-arrow-right ms-1"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
            </div><!-- /.col-lg-8 -->
        </div><!-- /.row -->
    </div><!-- /.bk-layout -->
    </div><!-- /#BookingWizardShell -->

    <style>
        .success-message {
            font-weight: 700;
            color: #136207;
            margin-bottom: 24px;
            font-size: 1.8rem;
        }
        .button-group .btn {
            padding: 12px 28px;
            font-size: 1.1rem;
            border-radius: 50px;
            transition: background-color 0.3s ease, color 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }
        .btn-primary {
            background: linear-gradient(135deg, #136207, #000000) !important;
            color: #fff;
            border: none;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #000000, #136207) !important;
            color: #fff;
            text-decoration: none;
        }
        .btn-outline-primary {
            background-color: transparent;
            border: 2px solid #136207;
            color: #136207;
        }
        .btn-outline-primary:hover {
            background: #136207;
            color: #fff;
            text-decoration: none;
        }
        .ms-3 {
            margin-left: 1rem !important;
        }
    </style>
    <div class="modal fade" id="SuccessMsgModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                {{-- <div class="modal-header">
                    <h5 class="modal-title" id="modalCenterTitle">Modal title</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div> --}}
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12" style="text-align: center;">
                            <img src="{{ asset('assets/img/Completed.png') }}" style="max-width: 70%;"
                                alt="Success">
                        </div>
                    </div>
                    <div class="row g-6">
                        <div class="col-lg-12 text-center">
                            <h4 class="success-message">Your Appointment Was Successfully Booked!</h4>
                            <div class="button-group">
                                <a href="{{ route('home') }}" class="btn btn-primary">Go to Website</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" id="final_staff_id" name="final_staff_id">
    <input type="hidden" id="final_service_id" name="final_service_id">
    <input type="hidden" id="selectedSlotStartValue" name="selectedSlotStartValue">
    <input type="hidden" id="selectedSlotEndValue" name="selectedSlotEndValue">
    <input type="hidden" id="staffSlotPicksJson" name="staffSlotPicksJson">
    <input type="hidden" id="total_amt" name="total_amt">
    <input type="hidden" id="appointment_date" name="appointment_date">
    @php
        $bookingGstPayload = !empty($defaultGst) ? [
            'name' => $defaultGst->name,
            'rate' => (float) $defaultGst->rate,
            'tax_type' => $defaultGst->tax_type,
        ] : null;
    @endphp
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Step One
        window.requiredBarberCount = 1;
        window.bookingGst = @json($bookingGstPayload);
        window.SHOP_CURRENCY_SYMBOL = @json(shop_currency_symbol());
        window.BOOKING_CODE_PREFIX = @json($bookingCodePrefix ?? 'MHS');

        function calcBookingPaymentSummary(subtotal) {
            subtotal = parseFloat(subtotal) || 0;
            var gst = window.bookingGst || {};
            var rate = parseFloat(gst.rate) || 0;
            var taxType = String(gst.tax_type || '').toLowerCase();
            var taxExcluded = taxType === 'exclude';
            var tax = 0;
            var total = subtotal;
            var label = 'Tax';

            if (rate > 0) {
                var gstName = gst.name ? String(gst.name) : 'GST';
                label = gstName + ' (' + rate + '%)';
                if (taxExcluded) {
                    tax = subtotal * rate / 100;
                    total = subtotal + tax;
                }
            }

            return {
                subtotal: subtotal,
                tax: tax,
                total: total,
                taxLabel: label,
                taxExcluded: taxExcluded
            };
        }

        function getServiceCardQty($card) {
            return parseInt($card.attr('data-qty') || '0', 10) || 0;
        }

        function setServiceCardQty($card, qty) {
            qty = Math.max(0, Math.min(5, parseInt(qty, 10) || 0));
            $card.attr('data-qty', qty);
            $card.find('.bk-qty-val').text(qty);
            $card.toggleClass('active', qty > 0);
            syncServiceSelection();
        }

        function collectServiceSelections() {
            var items = [];
            $('.step-one-service-card, .step-two-service-card').each(function() {
                var $card = $(this);
                if (!$card.is(':visible') && !$card.closest('#byServicePanel').length && !$card.hasClass('step-two-service-card')) {
                    return;
                }
                var qty = getServiceCardQty($card);
                if (qty < 1) return;
                var id = String($card.find('.service_id_select, .step_two_service_id_select').first().val() || '').trim();
                if (!id) return;
                items.push({
                    id: id,
                    qty: qty,
                    name: $card.find('.fw-semibold').first().text().trim(),
                    duration: parseInt($card.data('duration'), 10) || 0,
                    price: parseFloat($card.data('price')) || 0
                });
            });
            return items;
        }

        function expandServiceIds(items) {
            var ids = [];
            items.forEach(function(item) {
                for (var i = 0; i < item.qty; i++) ids.push(item.id);
            });
            return ids.join(',');
        }

        function uniqueServiceIds(items) {
            var ids = [];
            items.forEach(function(item) {
                if (ids.indexOf(item.id) === -1) ids.push(item.id);
            });
            return ids.join(',');
        }

        function calcRequiredBarbers(items) {
            var maxQty = 0;
            items.forEach(function(item) {
                if (item.qty > maxQty) maxQty = item.qty;
            });
            return Math.max(maxQty, items.length ? 1 : 0);
        }

        function getCurrentServiceItems() {
            var isServiceFirst = $("#step_one_select_type").val() === 'Service';
            var items = [];
            var $cards = isServiceFirst ? $('.step-one-service-card') : $('.step-two-service-card');
            $cards.each(function() {
                var $card = $(this);
                var qty = getServiceCardQty($card);
                if (!isServiceFirst && qty < 1 && $card.hasClass('active')) {
                    qty = 1;
                }
                if (qty < 1) return;
                var id = String($card.find('.service_id_select, .step_two_service_id_select').first().val() || '').trim();
                if (!id) return;
                items.push({
                    id: id,
                    qty: isServiceFirst ? qty : 1,
                    name: $card.find('.fw-semibold').first().text().trim(),
                    duration: parseInt($card.data('duration'), 10) || 0,
                    price: parseFloat($card.data('price')) || parseFloat(String($card.find('.text-danger').last().text()).replace(/[^0-9.]/g, '')) || 0
                });
            });
            return items;
        }

        function getAssignmentSlots(items) {
            items = items || getCurrentServiceItems();
            var slots = [];
            items.forEach(function(item) {
                for (var i = 0; i < item.qty; i++) {
                    slots.push({
                        id: item.id,
                        name: item.name,
                        unit: i + 1,
                        qty: item.qty,
                        duration: item.duration,
                        label: item.qty > 1 ? (item.name + ' #' + (i + 1)) : item.name
                    });
                }
            });
            return slots;
        }

        function isFlexibleStaffId(id) {
            var v = String(id || '').trim().toLowerCase();
            if (!v) return false;
            if (v === '0' || v === 'any' || v === 'first') return true;
            var flex = window.flexibleStaffIds || [];
            return flex.map(String).indexOf(String(id)) !== -1;
        }

        function isFlexibleStaffName(name) {
            return String(name || '').trim().toLowerCase().indexOf('first available') === 0;
        }

        function dedupeStaffNames(names) {
            var seen = {};
            var out = [];
            (names || []).forEach(function(name) {
                name = String(name || '').trim();
                if (!name) return;
                var key = isFlexibleStaffName(name) ? '__flexible__' : name.toLowerCase();
                if (seen[key]) return;
                seen[key] = true;
                out.push(name);
            });
            return out;
        }

        function calcBookingDuration(items) {
            items = items || getCurrentServiceItems();
            var named = {};
            var anySum = 0;
            var $rows = $('#StepTwoStaffAssign .bk-assign-row');
            if ($rows.length) {
                $rows.each(function() {
                    var sid = String($(this).attr('data-staff-id') || '').trim();
                    var dur = parseInt($(this).attr('data-duration'), 10) || 0;
                    var name = $(this).find('.bk-staff-select-name').text();
                    if (!sid || dur < 1) return;
                    if (isFlexibleStaffId(sid) || isFlexibleStaffName(name)) {
                        anySum += dur;
                    } else {
                        named[sid] = (named[sid] || 0) + dur;
                    }
                });
                var parts = Object.keys(named).map(function(k) { return named[k]; });
                if (anySum > 0) parts.push(anySum);
                if (parts.length) {
                    return Math.max.apply(null, parts);
                }
            }
            var total = 0;
            items.forEach(function(item) { total += item.duration * (item.qty || 1); });
            return total;
        }

        function isMultiStaffAssign(items) {
            return getAssignmentSlots(items).length > 1;
        }

        function syncServiceSelection() {
            var isServiceFirst = $("#step_one_select_type").val() === 'Service';
            var items = getCurrentServiceItems();
            var expanded = expandServiceIds(items);
            var unique = uniqueServiceIds(items);
            var slots = getAssignmentSlots(items);
            window.requiredBarberCount = isServiceFirst ? Math.max(slots.length, 1) : 1;
            $('#final_service_id').val(expanded);
            if (isServiceFirst) {
                $('#step_one_staff_service').val(unique);
            } else {
                $('#step_two_staff_service').val(expanded);
                var barberIds = selectedStaffIds();
                var barberNames = selectedStaffNames();
                if (barberIds.length) {
                    $('#final_staff_id').val(barberIds.join(','));
                }
                if (barberNames.length) {
                    $('#liveBarber').text(dedupeStaffNames(barberNames).join(', '));
                }
            }

            var total_duration = calcBookingDuration(items);
            var total_price = 0;
            items.forEach(function(item) {
                total_price += item.price * item.qty;
            });
            $("#ServiceMinite").html(total_duration);
            $("#ServiceMinite1").html(total_duration);
            $("#total_amt").val(total_price ? total_price.toFixed(2) : '');
            $("#liveDuration").text(total_duration ? (total_duration + ' min') : '-');
            $("#liveTotal").text(total_price ? (window.SHOP_CURRENCY_SYMBOL + total_price.toFixed(2)) : window.SHOP_CURRENCY_SYMBOL + '0');
            var liveNames = items.map(function(item) {
                return item.qty > 1 ? (item.name + ' ×' + item.qty) : item.name;
            });
            $('#liveService').text(liveNames.length ? liveNames.join(', ') : '-');
            updateBarberHint();
        }

        function updateBarberHint() {
            var slots = getAssignmentSlots();
            var hint = slots.length > 1
                ? 'Choose a barber for each selected service.'
                : 'Select a staff member for your appointment';
            $('#barberStepHint').text(hint);
        }

        function isBookByBarber() {
            return $("#step_one_select_type").val() === 'Staff';
        }

        function selectedStaffIds() {
            if (isBookByBarber()) {
                var ids = [];
                $('.step-one-staff-card.selected').each(function() {
                    var id = String($(this).find('.staff_id_select').first().val() || '').trim();
                    if (id && ids.indexOf(id) === -1) ids.push(id);
                });
                return ids;
            }
            if ($('#StepTwoStaffAssign').length && !$('#StepTwoStaffAssign').hasClass('d-none')) {
                var ids = [];
                $('#StepTwoStaffAssign .bk-assign-row').each(function() {
                    ids.push(String($(this).attr('data-staff-id') || '').trim());
                });
                return ids.filter(Boolean);
            }
            var ids = [];
            $('.step-two-staff-card.selected, .step-one-staff-card.selected').each(function() {
                var id = String($(this).find('.step_two_staff_id_select, .staff_id_select').first().val() || '').trim();
                if (id && ids.indexOf(id) === -1) ids.push(id);
            });
            return ids;
        }

        function selectedStaffNames() {
            if (isBookByBarber()) {
                var names = [];
                $('.step-one-staff-card.selected').each(function() {
                    var name = $(this).find('.fw-semibold').first().text().trim();
                    if (name) names.push(name);
                });
                return names;
            }
            if ($('#StepTwoStaffAssign').length && !$('#StepTwoStaffAssign').hasClass('d-none')) {
                var names = [];
                $('#StepTwoStaffAssign .bk-assign-row').each(function() {
                    var name = $(this).find('.bk-staff-select-name').text().trim();
                    if (name && name !== 'Select barber') names.push(name);
                });
                return names;
            }
            var names = [];
            $('.step-two-staff-card.selected, .step-one-staff-card.selected').each(function() {
                var name = $(this).find('.fw-semibold').first().text().trim();
                if (name) names.push(name);
            });
            return names;
        }

        function syncAssignedStaff() {
            var ids = [];
            var names = [];
            $('#StepTwoStaffAssign .bk-assign-row').each(function() {
                var id = String($(this).attr('data-staff-id') || '').trim();
                var name = $(this).find('.bk-staff-select-name').text().trim();
                if (id) ids.push(id);
                if (name && name !== 'Select barber') names.push(name);
            });
            $('#final_staff_id').val(ids.join(','));
            $('#liveBarber').text(names.length ? dedupeStaffNames(names).join(', ') : '-');
            if (typeof window.setActiveStaffDayOffs === 'function') {
                window.setActiveStaffDayOffs(ids.join(','));
            }
            syncServiceSelection();
        }

        function updateSelectedServiceTotals($cards) {
            syncServiceSelection();
        }

        $(document).ready(function() {
            $('#BookingErrorRetry').on('click', function() {
                var modalEl = document.getElementById("BookingErrorModal");
                if (modalEl && window.bootstrap) {
                    bootstrap.Modal.getOrCreateInstance(modalEl).hide();
                }
                updateReviewSection();
            });

            $(document).on('click', '.bk-qty-plus, .bk-qty-minus', function(e) {
                e.preventDefault();
                e.stopPropagation();
                var $card = $(this).closest('.service-card');
                var qty = getServiceCardQty($card);
                if ($(this).hasClass('bk-qty-plus')) qty += 1;
                else qty -= 1;
                setServiceCardQty($card, qty);
            });

            $('.step-one-service-card').on('click', function(e) {
                if ($(e.target).closest('[data-qty-wrap]').length) return;
                var qty = getServiceCardQty($(this));
                setServiceCardQty($(this), qty > 0 ? 0 : 1);
            });
            $('.step-one-staff-card').on('click', function() {
                $('.step-one-staff-card').removeClass('selected');
                $(this).addClass('selected');
                var selectedstaff_id = String($(this).find('.staff_id_select').val() || '').trim();
                if (!selectedstaff_id) return;
                $('#step_one_staff_service').val(selectedstaff_id);
                $('#final_staff_id').val(selectedstaff_id);
                window.requiredBarberCount = 1;
                if (typeof window.setActiveStaffDayOffs === 'function') {
                    window.setActiveStaffDayOffs(selectedstaff_id);
                }
            });
        });
        $(document).on('click', '.step-two-service-card', function(e) {
            if ($(e.target).closest('[data-qty-wrap]').length) return;
            var qty = getServiceCardQty($(this));
            setServiceCardQty($(this), qty > 0 ? 0 : 1);
        });
        $(document).on('click', '.step-two-staff-card', function() {
            $('.step-two-staff-card').removeClass('selected');
            $(this).addClass('selected');
            var selectedstaff_id = String($(this).find('.step_two_staff_id_select').val() || '').trim();
            if (!selectedstaff_id) return;
            $('#step_two_staff_service').val(selectedstaff_id);
            $('#final_staff_id').val(selectedstaff_id);
            $('#liveBarber').text(selectedStaffNames().length ? dedupeStaffNames(selectedStaffNames()).join(', ') : '-');
            if (typeof window.setActiveStaffDayOffs === 'function') {
                window.setActiveStaffDayOffs(selectedstaff_id);
            }
            syncServiceSelection();
        });
        $(document).on('click', '.bk-staff-select-btn', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var $wrap = $(this).closest('.bk-staff-select');
            $('.bk-staff-select').not($wrap).removeClass('open');
            $wrap.toggleClass('open');
        });
        $(document).on('click', '.bk-staff-option', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var $opt = $(this);
            var $row = $opt.closest('.bk-assign-row');
            $row.attr('data-staff-id', $opt.attr('data-id'));
            $row.find('.bk-staff-select-name').text($opt.attr('data-name') || $opt.data('name'));
            $row.find('.bk-staff-select-photo').attr('src', $opt.attr('data-img') || $opt.data('img'));
            $row.find('.bk-staff-select').addClass('has-value').removeClass('open');
            syncAssignedStaff();
        });
        $(document).on('click', function() {
            $('.bk-staff-select').removeClass('open');
        });
        function resetBookingDraft() {
            $('.step-one-service-card, .step-two-service-card').removeClass('active').attr('data-qty', '0').find('.bk-qty-val').text('1');
            $('.step-one-staff-card, .step-two-staff-card').removeClass('selected');
            $('#StepTwoStaffAssign').addClass('d-none').empty();
            $('#StepTwoStaffHtml').removeClass('d-none').empty();
            $('#StepTwoServiceHtml').empty();
            $('#final_service_id').val('');
            $('#final_staff_id').val('');
            $('#step_one_staff_service').val('');
            $('#step_two_staff_service').val('');
            $('#liveService').text('-');
            $('#liveBarber').text('-');
            $('#liveDuration').text('-');
            $('#liveTotal').text(window.SHOP_CURRENCY_SYMBOL + '0');
            $('#total_amt').val('');
            window.requiredBarberCount = 1;
        }

        function showWizardTab(tab) {
            let byServicePanel = document.getElementById('byServicePanel');
            let byStaffPanel = document.getElementById('byStaffPanel');
            let byServiceBtn = document.getElementById('byServiceBtn');
            let byStaffBtn = document.getElementById('byStaffBtn');
            let serviceByStaff = document.getElementById('ServiceByStaffStep');
            let staffByService = document.getElementById('StaffByServiceStep');
            resetBookingDraft();
            if (tab === 'service') {
                byServicePanel.classList.remove('d-none');
                byStaffPanel.classList.add('d-none');
                byServiceBtn.classList.add('active');
                byStaffBtn.classList.remove('active');
                document.getElementById("StepSecondType").innerHTML = "Select Staff";
                if (serviceByStaff) serviceByStaff.style.display = "none";
                if (staffByService) staffByService.style.display = "block";
                $("#step_two_select_type").val("Staff");
                $("#step_one_select_type").val("Service");
            } else {
                byServicePanel.classList.add('d-none');
                byStaffPanel.classList.remove('d-none');
                byServiceBtn.classList.remove('active');
                byStaffBtn.classList.add('active');
                document.getElementById("StepSecondType").innerHTML = "Select Service";
                if (serviceByStaff) serviceByStaff.style.display = "block";
                if (staffByService) staffByService.style.display = "none";
                $("#step_one_select_type").val("Staff");
                $("#step_two_select_type").val("Service");
            }
        }
        showWizardTab('service');
    </script>
    <script>
        function SlotTime(typ) {
            let byMorning = document.getElementById('byMorning');
            let byEvening = document.getElementById('byEvening');
            let timeSlotsMorning = document.getElementById('timeSlotsMorning');
            let timeSlotsEvening = document.getElementById('timeSlotsEvening');
            if (!byMorning || !byEvening || !timeSlotsMorning || !timeSlotsEvening) return;
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
    </script>
    <script>
        function staffPhotoUrl(staff, storagePath, defaultImg) {
            var hasImg = staff && staff.profile_img && String(staff.profile_img).trim() !== '' && String(staff.profile_img).toLowerCase() !== 'null';
            return hasImg ? (staff.profile_image_url || (storagePath + String(staff.profile_img).replace(/^(public\/|storage\/)+/, ''))) : defaultImg;
        }

        function renderStaffCards(staffs, storagePath, defaultImg) {
            if (!staffs || !staffs.length) {
                return `<div class="col-12"><div class="text-center text-muted py-3"><i class="fa fa-exclamation-circle me-2"></i>Staff Not Available</div></div>`;
            }
            var html = '';
            staffs.forEach(function(staff) {
                var profileImg = staffPhotoUrl(staff, storagePath, defaultImg);
                html += `
        <div class="col-12 col-sm-12 col-md-6 col-lg-6">
            <div class="staff-card step-two-staff-card h-100 d-flex align-items-center p-3 justify-content-between">
                <input type="hidden" value="${staff.id}" class="step_two_staff_id_select">
                <div class="d-flex align-items-center">
                    <img src="${profileImg}" class="rounded-circle me-3 bk-staff-photo" width="48" height="48" alt="${staff.name || 'Staff'}" onerror="this.onerror=null;this.src='${defaultImg}';" />
                    <div><div class="fw-semibold">${staff.name}</div></div>
                </div>
                <span class="badge bg-light fw-semibold border border-success text-success ms-2">Available</span>
            </div>
        </div>`;
            });
            return html;
        }

        function renderServiceStaffAssign(staffs, slots, storagePath, defaultImg) {
            if (!staffs || !staffs.length) {
                return `<div class="text-center text-muted py-3"><i class="fa fa-exclamation-circle me-2"></i>Staff Not Available</div>`;
            }
            var anyImg = defaultImg;
            var options = `<button type="button" class="bk-staff-option" data-id="any" data-name="First available" data-img="${anyImg}">
                    <img src="${anyImg}" alt="First available" onerror="this.onerror=null;this.src='${defaultImg}';">
                    <span>First available</span>
                </button>`;
            staffs.forEach(function(staff) {
                if (isFlexibleStaffId(staff.id) || isFlexibleStaffName(staff.name)) return;
                var img = staffPhotoUrl(staff, storagePath, defaultImg);
                var safeName = String(staff.name || 'Barber').replace(/"/g, '&quot;');
                options += `<button type="button" class="bk-staff-option" data-id="${staff.id}" data-name="${safeName}" data-img="${img}">
                    <img src="${img}" alt="${safeName}" onerror="this.onerror=null;this.src='${defaultImg}';">
                    <span>${safeName}</span>
                </button>`;
            });
            var html = '<div class="bk-assign-list">';
            slots.forEach(function(slot, index) {
                html += `
                <div class="bk-assign-row" data-slot="${index}" data-service-id="${slot.id}" data-duration="${slot.duration || 0}" data-staff-id="">
                    <div class="bk-assign-service">
                        <span class="bk-assign-num">${index + 1}</span>
                        <div>
                            <strong>${slot.label}</strong>
                            <small>Choose a barber</small>
                        </div>
                    </div>
                    <div class="bk-staff-select">
                        <button type="button" class="bk-staff-select-btn">
                            <img class="bk-staff-select-photo" src="${defaultImg}" alt="">
                            <span class="bk-staff-select-name">Select barber</span>
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <div class="bk-staff-select-menu">${options}</div>
                    </div>
                </div>`;
            });
            html += '</div>';
            return html;
        }

      function loadServiceStaff(staff_service_id, select_type) {
            var data = {
                'staff_service_id': staff_service_id,
                'select_type': select_type,
            }
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                url: "{{ route('get-staff-service') }}",
                data: data,
                dataType: "json",
                success: function(response) {
                    if (response.status == 1) {
                        // -------- When user selects SERVICE (show services grouped by category)
                        if (select_type === "Service") {
                            var staffs = response.staff; // backend à¤¸à¥‡ staff list
                            const storagePath = "{{ asset('storage') }}/";
                            const defaultImg = "{{ asset('assets/img/user.svg') }}";
                            if (response.staff_day_offs && window.staffDayOffMap) {
                                Object.keys(response.staff_day_offs).forEach(function (sid) {
                                    window.staffDayOffMap[sid] = response.staff_day_offs[sid];
                                });
                            }
                            window.bookingStaffList = staffs || [];
                            var items = getCurrentServiceItems();
                            var slots = getAssignmentSlots(items);
                            if (slots.length > 1) {
                                $("#StepTwoStaffHtml").addClass('d-none').empty();
                                $("#StepTwoStaffAssign").removeClass('d-none').html(renderServiceStaffAssign(window.bookingStaffList, slots, storagePath, defaultImg));
                            } else {
                                $("#StepTwoStaffAssign").addClass('d-none').empty();
                                $("#StepTwoStaffHtml").removeClass('d-none').html(renderStaffCards(window.bookingStaffList, storagePath, defaultImg));
                            }
                            updateBarberHint();
                        }
                        // -------- When user selects STAFF (show staff & his services)
                        else if (select_type === "Staff") {
                            var data = response.data;
                            $("#StepTwoStaffAssign").addClass('d-none').empty();
                            $("#StepTwoStaffHtml").addClass('d-none').empty();
                            window.requiredBarberCount = 1;
                            if (response.staff_day_offs) {
                                window.activeStaffDayOffs = response.staff_day_offs;
                                if (window.staffDayOffMap) {
                                    window.staffDayOffMap[staff_service_id] = response.staff_day_offs;
                                }
                            }
                            let StaffList = ``;
                            if (data && data.length > 0) {
                                data.forEach(category => {
                                    StaffList += `
            <div class="card px-0 py-2 border-0 shadow-none mb-4" style="background: transparent;">
                <div class="fw-bold fs-6 mb-3" style="color:#0F172A;">
                    <i class="fas fa-layer-group me-2" style="color:#136207;"></i>${category.category_name}
                </div>
                <div class="row g-3">`;
                                    if (category.services && category.services.length > 0) {
                                        category.services.forEach(service => {
                                            StaffList += `
                    <div class="col-md-6 col-lg-4">
                        <div class="service-card step-two-service-card px-3 py-3 h-100 d-flex flex-column justify-content-between"
                             data-duration="${service.duration}" data-price="${service.price}" data-qty="0">
                            <input type="hidden"
                                class="step_two_service_id_select"
                                value="${service.service_id}">
                            <div class="fw-semibold">${service.service_name}</div>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <div class="text-muted"><i class="fas fa-clock me-1"></i> ${service.duration} min</div>
                                <div class="fw-bold text-danger fs-6">${window.SHOP_CURRENCY_SYMBOL}${service.price}</div>
                            </div>
                        </div>
                    </div>`;
                                        });
                                    } else {
                                        StaffList += `
                <div class="col-12">
                    <div class="text-center text-muted">
                        <i class="fa fa-exclamation-circle me-2"></i>
                        No Services Available in this Category
                    </div>
                </div>`;
                                    }
                                    StaffList += `</div></div>`;
                                });
                            } else {
                                StaffList = `
        <div class="col-12">
            <div class="alert alert-warning text-center mb-0">
                <i class="fa fa-exclamation-circle me-2"></i>
                Service Not Available
            </div>
        </div>`;
                            }
                            $("#StepTwoServiceHtml").html(StaffList);
                        }
                    } else {
                        console.error("No data found");
                    }
                }
            });
            return false;
        }
        function updateStepIndicators(oldStep, newStep) {
            const stepsTotal = (typeof totalSteps !== 'undefined') ? totalSteps : 5;
            const oldIndicator = document.getElementById(`step-indicator-${oldStep}`);
            if (oldIndicator) {
                if (newStep > oldStep) {
                    oldIndicator.className = 'progress-step completed';
                    oldIndicator.innerHTML = '<i class="bi bi-check"></i>';
                    if (oldStep < stepsTotal) {
                        const line = document.getElementById(`line-${oldStep}`);
                        if (line) line.classList.add('completed');
                    }
                } else {
                    oldIndicator.className = 'progress-step pending';
                    oldIndicator.textContent = oldStep;
                    if (oldStep < stepsTotal) {
                        const line = document.getElementById(`line-${oldStep}`);
                        if (line) line.classList.remove('completed');
                    }
                }
            }
            const newIndicator = document.getElementById(`step-indicator-${newStep}`);
            if (newIndicator) {
                newIndicator.className = 'progress-step active';
                newIndicator.textContent = newStep;
            }
            document.querySelectorAll('.bk-step-row').forEach(function (row) {
                const n = parseInt(row.getAttribute('data-step-row'), 10);
                row.classList.toggle('done', n < newStep);
                row.classList.toggle('current', n === newStep);
            });
        }
        window.updateStepIndicators = updateStepIndicators;
    </script>
    <script>
        // Calendar rendering
        const currentDate = new Date();
        let selectedYear = currentDate.getFullYear();
        let selectedMonth = currentDate.getMonth(); // 0-based (Jan = 0)
        let selectedDay = currentDate.getDate(); // Today's date by default
        let dayName = new Intl.DateTimeFormat('en-US', {
            weekday: 'long'
        }).format(currentDate);
        let currentStep = 1;
        const totalSteps = 5;

        // Schedule meta from server (for calendar disable rules)
        window.shopSchedule = @json($shopSchedule ?? new \stdClass());
        window.shopFullClosures = @json($shopFullClosures ?? []);
        window.staffDayOffMap = @json($staffDayOffMap ?? new \stdClass());
        window.flexibleStaffIds = @json($flexibleStaffIds ?? []);
        window.activeStaffDayOffs = [];

        function setActiveStaffDayOffs(staffId) {
            if (!staffId) {
                window.activeStaffDayOffs = [];
                return;
            }
            var ids = String(staffId).split(',').map(function(id) { return id.trim(); }).filter(function(id) {
                return id && !isFlexibleStaffId(id);
            });
            var union = [];
            ids.forEach(function(id) {
                var days = window.staffDayOffMap[id] || [];
                days.forEach(function(day) {
                    if (union.indexOf(day) === -1) union.push(day);
                });
            });
            window.activeStaffDayOffs = union;
        }
        window.setActiveStaffDayOffs = setActiveStaffDayOffs;

        function pad2(n) {
            return String(n).padStart(2, '0');
        }

        function toYmd(year, monthIndex, day) {
            return year + '-' + pad2(monthIndex + 1) + '-' + pad2(day);
        }

        function dayNameFor(year, monthIndex, day) {
            return new Intl.DateTimeFormat('en-US', { weekday: 'long' })
                .format(new Date(year, monthIndex, day));
        }

        function isDateBookable(year, monthIndex, day) {
            try {
                const today = new Date();
                today.setHours(0, 0, 0, 0);
                const check = new Date(year, monthIndex, day);
                check.setHours(0, 0, 0, 0);
                if (check < today) return false;

                const name = dayNameFor(year, monthIndex, day);
                const schedule = window.shopSchedule || {};
                const shopDay = schedule[name];
                if (shopDay && String(shopDay.is_day_off) === '1') return false;

                const ymd = toYmd(year, monthIndex, day);
                if ((window.shopFullClosures || []).indexOf(ymd) !== -1) return false;

                if ((window.activeStaffDayOffs || []).indexOf(name) !== -1) return false;

                return true;
            } catch (e) {
                console.error('isDateBookable error', e);
                return true;
            }
        }
        window.isDateBookable = isDateBookable;

        function syncBookingSelectionFromDom() {
            syncServiceSelection();
            var ids = selectedStaffIds();
            if (ids.length) {
                $('#final_staff_id').val(ids.join(','));
            }
        }

        function prepareDateTimeStep() {
            var final_staff_id = $("#final_staff_id").val();
            setActiveStaffDayOffs(final_staff_id);
            const now = new Date();
            selectedYear = now.getFullYear();
            selectedMonth = now.getMonth();
            selectedDay = now.getDate();
            let guard = 0;
            while (!isDateBookable(selectedYear, selectedMonth, selectedDay) && guard < 370) {
                const dt = new Date(selectedYear, selectedMonth, selectedDay + 1);
                selectedYear = dt.getFullYear();
                selectedMonth = dt.getMonth();
                selectedDay = dt.getDate();
                guard++;
            }
            $("#selectedSlotStartValue").val('');
            $("#selectedSlotEndValue").val('');
            $("#liveTime").text('-');
            if (typeof syncDtContextStrip === 'function') syncDtContextStrip();
            if (typeof window.renderCalendar === 'function') {
                window.renderCalendar();
            }
            if (typeof window.selectDay === 'function') {
                window.selectDay(selectedDay);
            }
        }

        function bookingAlert(message) {
            if (window.Swal && typeof Swal.fire === 'function') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Almost there',
                    text: message,
                    confirmButtonText: 'OK'
                });
            } else {
                alert(message);
            }
        }

        function changeStep(direction, skipValidation) {
            skipValidation = !!skipValidation;
            try {
                if (!skipValidation) {
                    if (currentStep == 1) {
                        let step_one_staff_service = $("#step_one_staff_service").val();
                        if (!step_one_staff_service) {
                            bookingAlert("Please select a service or barber to continue.");
                            return false;
                        }
                        let step_one_select_type = $("#step_one_select_type").val();
                        loadServiceStaff(step_one_staff_service, step_one_select_type);
                    } else if (currentStep == 2) {
                        syncBookingSelectionFromDom();
                        let final_staff_id = $("#final_staff_id").val();
                        let final_service_id = $("#final_service_id").val();
                        if (isBookByBarber()) {
                            if (!final_staff_id) {
                                bookingAlert("Please select a barber to continue.");
                                return false;
                            }
                            if (!final_service_id) {
                                bookingAlert("Please select at least one service.");
                                return false;
                            }
                        } else {
                            if (!final_staff_id || !final_service_id) {
                                bookingAlert("Please select both a service and a barber to continue.");
                                return false;
                            }
                            var needed = window.requiredBarberCount || 1;
                            var picked = selectedStaffIds().length;
                            if ($('#StepTwoStaffAssign').length && !$('#StepTwoStaffAssign').hasClass('d-none')) {
                                var rows = $('#StepTwoStaffAssign .bk-assign-row').length;
                                var filled = $('#StepTwoStaffAssign .bk-assign-row[data-staff-id]').filter(function() {
                                    return String($(this).attr('data-staff-id') || '').trim() !== '';
                                }).length;
                                if (filled !== rows || rows === 0) {
                                    bookingAlert("Please choose a barber for each selected service.");
                                    return false;
                                }
                            } else if (picked !== needed) {
                                bookingAlert(needed > 1
                                    ? "Please select " + needed + " barbers to continue."
                                    : "Please select a barber to continue.");
                                return false;
                            }
                        }
                    } else if (currentStep == 3) {
                        let selectedSlotStartValue = $("#selectedSlotStartValue").val();
                        var groupCount = window.staffSlotGroupCount || 0;
                        var pickCount = window.staffSlotPicks ? Object.keys(window.staffSlotPicks).length : 0;
                        if (groupCount > 1 && pickCount < groupCount) {
                            bookingAlert("Please select a time for each barber.");
                            return false;
                        }
                        if (!selectedSlotStartValue) {
                            bookingAlert("Please select a time slot.");
                            return false;
                        }
                    } else if (currentStep == 4) {
                        let phone_number = $("#phone_number").val();
                        let full_name = $("#full_name").val();
                        if (!phone_number) {
                            bookingAlert("Please enter your phone number.");
                            return false;
                        } else if (!full_name) {
                            bookingAlert("Please enter your full name.");
                            return false;
                        }
                        FetchAppointmentDetails();
                    }
                }

                const newStep = currentStep + direction;
                if (newStep < 1 || newStep > totalSteps) return false;

                var currentEl = document.getElementById('step-' + currentStep);
                var nextEl = document.getElementById('step-' + newStep);
                if (!currentEl || !nextEl) {
                    console.error('Booking step element missing', currentStep, newStep);
                    return false;
                }

                currentEl.classList.remove("active");
                updateStepIndicators(currentStep, newStep);
                currentStep = newStep;
                nextEl.classList.add("active");
                updateButtons();

                // Prepare calendar only after the date step is visible
                if (currentStep === 3 && direction > 0) {
                    prepareDateTimeStep();
                }

                try {
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                } catch (e) {
                    window.scrollTo(0, 0);
                }

                return true;
            } catch (err) {
                console.error('changeStep error', err);
                bookingAlert('Something went wrong moving to the next step. Please try again.');
                return false;
            }
        }
        window.changeStep = changeStep;

        function updateButtons() {
            const prevBtn = document.getElementById("prevBtn");
            const nextBtn = document.getElementById("nextBtn");
            if (!prevBtn || !nextBtn) return;
            prevBtn.disabled = currentStep === 1;
            nextBtn.disabled = false;
            if (currentStep === totalSteps) {
                nextBtn.classList.add('bk-finish-btn');
                nextBtn.innerHTML = `Finish Booking <i class="bi bi-arrow-right ms-1"></i>`;
                nextBtn.onclick = function() {
                    updateReviewSection();
                };
            } else {
                nextBtn.classList.remove('bk-finish-btn');
                nextBtn.innerHTML = `Next <i class="bi bi-arrow-right ms-1"></i>`;
                nextBtn.onclick = function() {
                    changeStep(1);
                };
            }
        }
        window.updateButtons = updateButtons;
        // Called only on Finish
        function showBookingLoader() {
            var loader = document.getElementById("Loader");
            if (!loader) return;
            loader.hidden = false;
            loader.style.display = "flex";
            document.body.classList.add("bk-loading");
        }

        function hideBookingLoader() {
            var loader = document.getElementById("Loader");
            if (!loader) return;
            loader.hidden = true;
            loader.style.display = "none";
            document.body.classList.remove("bk-loading");
        }

        function resetFinishButton() {
            var nextBtn = document.getElementById("nextBtn");
            if (!nextBtn) return;
            nextBtn.dataset.submitting = '0';
            nextBtn.disabled = false;
            nextBtn.innerHTML = `Finish Booking <i class="bi bi-arrow-right ms-1"></i>`;
        }

        function showBookingError(message) {
            hideBookingLoader();
            resetFinishButton();
            var msgEl = document.getElementById("BookingErrorMessage");
            if (msgEl) {
                msgEl.textContent = message || "Something went wrong.";
            }
            var modalEl = document.getElementById("BookingErrorModal");
            if (modalEl && window.bootstrap) {
                bootstrap.Modal.getOrCreateInstance(modalEl).show();
            } else {
                alert(message || "Something went wrong. Please try again.");
            }
        }

        function collectBookingSuccessData(response) {
            var email = ($("#email").val() || "").trim();
            var price = $("#liveTotal").text() || (window.SHOP_CURRENCY_SYMBOL + (parseFloat($("#total_amt").val()) || 0).toFixed(2));
            var duration = $("#liveDuration").text() || $("#TotalDuration").text() || "-";
            var apptId = (response && (response.appointment_id || response.appointmentId)) || null;
            var bookingCode = (response && response.booking_code) ? String(response.booking_code) : null;
            var bookingRef = bookingCode || (apptId ? String(apptId) : (window.BOOKING_CODE_PREFIX + "-" + Date.now().toString().slice(-8)));
            var apptNumber = bookingCode || bookingRef;
            return {
                appointmentNumber: apptNumber,
                bookingId: apptId || bookingRef,
                bookingCode: bookingCode || apptNumber,
                trackUrl: bookingCode
                    ? ("{{ url('/track-booking') }}/" + encodeURIComponent(bookingCode))
                    : "{{ route('track-booking') }}",
                service: $("#liveService").text() || "-",
                barber: $("#liveBarber").text() || "-",
                date: $("#ConfirmDate").text() || $("#liveDate").text() || "-",
                time: $("#ConfirmTime").text() || $("#liveTime").text() || "-",
                duration: duration,
                price: price,
                customer: $("#full_name").val() || $("#PreviewName").text() || "-",
                phone: $("#phone_number").val() || $("#PreviewPhone").text() || "-",
                email: email || "-",
                notes: ($("#note").val() || "").trim() || $("#PreviewNote").text() || "-",
                hasEmail: !!email
            };
        }

        function populateSuccessPage(data) {
            $("#SuccessApptNumber").text(data.appointmentNumber);
            $("#SuccessBookingId").text(data.bookingId);
            $("#SuccessService").text(data.service);
            $("#SuccessBarber").text(data.barber);
            $("#SuccessDate").text(data.date);
            $("#SuccessTime").text(data.time);
            $("#SuccessDuration").text(data.duration);
            $("#SuccessPrice").text(data.price);
            $("#SuccessCustomer").text(data.customer);
            $("#SuccessPhone").text(data.phone);
            $("#SuccessEmail").text(data.email);
            $("#SuccessNotes").text(data.notes);
            var trackBtn = document.getElementById("SuccessTrackBtn");
            if (trackBtn && data.trackUrl) {
                trackBtn.setAttribute("href", data.trackUrl);
            }
            var emailSent = document.getElementById("SuccessEmailSent");
            if (emailSent) {
                emailSent.hidden = !data.hasEmail;
            }
        }

        function playConfettiBurst() {
            var canvas = document.getElementById("bkConfettiCanvas");
            if (!canvas || !canvas.getContext) return;
            var ctx = canvas.getContext("2d");
            var parent = canvas.parentElement;
            var w = parent.clientWidth || window.innerWidth;
            var h = Math.min(parent.clientHeight || window.innerHeight, 900);
            canvas.width = w;
            canvas.height = h;
            var colors = ["#136207", "#000000", "#1a8a0a", "#22C55E", "#FBBF24", "#F87171"];
            var pieces = [];
            for (var i = 0; i < 80; i++) {
                pieces.push({
                    x: Math.random() * w,
                    y: Math.random() * h * 0.35 - 40,
                    r: 3 + Math.random() * 5,
                    c: colors[Math.floor(Math.random() * colors.length)],
                    vx: -2 + Math.random() * 4,
                    vy: 2 + Math.random() * 3.5,
                    a: Math.random() * Math.PI
                });
            }
            var start = performance.now();
            function frame(now) {
                var elapsed = now - start;
                ctx.clearRect(0, 0, w, h);
                pieces.forEach(function(p) {
                    p.x += p.vx;
                    p.y += p.vy;
                    p.a += 0.08;
                    ctx.save();
                    ctx.translate(p.x, p.y);
                    ctx.rotate(p.a);
                    ctx.globalAlpha = Math.max(0, 1 - elapsed / 3000);
                    ctx.fillStyle = p.c;
                    ctx.fillRect(-p.r, -p.r / 2, p.r * 2, p.r);
                    ctx.restore();
                });
                if (elapsed < 3000) {
                    requestAnimationFrame(frame);
                } else {
                    ctx.clearRect(0, 0, w, h);
                }
            }
            requestAnimationFrame(frame);
        }

        function showBookingSuccessPage(response) {
            hideBookingLoader();
            var data = collectBookingSuccessData(response);
            populateSuccessPage(data);
            var wizard = document.getElementById("BookingWizardShell");
            if (wizard) wizard.hidden = true;
            var success = document.getElementById("BookingSuccessPage");
            if (success) {
                success.hidden = false;
                success.classList.add("is-visible");
            }
            document.body.classList.add("bk-success-active");
            window.scrollTo({ top: 0, behavior: "smooth" });
            playConfettiBurst();

            var copyBtn = document.getElementById("SuccessCopyCodeBtn");
            if (copyBtn && !copyBtn.dataset.bound) {
                copyBtn.dataset.bound = "1";
                copyBtn.addEventListener("click", function () {
                    var code = (document.getElementById("SuccessApptNumber").textContent || "").trim();
                    if (!code || code === "—") return;
                    if (navigator.clipboard && navigator.clipboard.writeText) {
                        navigator.clipboard.writeText(code).then(function () {
                            copyBtn.innerHTML = '<i class="bi bi-check2"></i> Copied';
                            setTimeout(function () {
                                copyBtn.innerHTML = '<i class="bi bi-clipboard"></i> Copy';
                            }, 1800);
                        });
                    }
                });
            }
        }

        function updateReviewSection() {
            var nextBtn = document.getElementById("nextBtn");
            if (nextBtn.dataset.submitting === '1') {
                return false;
            }
            nextBtn.dataset.submitting = '1';
            nextBtn.disabled = true;
            nextBtn.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Booking...`;
            showBookingLoader();

            var staff_id = $("#final_staff_id").val();
            var service_id = $("#final_service_id").val();
            var slot_start = $("#selectedSlotStartValue").val();
            var slot_end = $("#selectedSlotEndValue").val();
            var phone_number = $("#phone_number").val();
            var full_name = $("#full_name").val();
            var email = $("#email").val();
            var note = $("#note").val();
            var total_amt = $("#total_amt").val();
            // Create a proper Date object (month is 0-based in JS, so subtract 1)
            let dateObj = new Date(selectedYear, selectedMonth, selectedDay);
            // Pad month/day with leading zero
            let year = dateObj.getFullYear();
            let month = String(dateObj.getMonth() + 1).padStart(2, '0'); // add 1 because month is 0-based
            let day = String(dateObj.getDate()).padStart(2, '0');
            let selectedDate = `${year}-${month}-${day}`;
            var data = {
                "staff_id": staff_id,
                "service_id": service_id,
                "slot_start": slot_start,
                "slot_end": slot_end,
                "group_picks": $("#staffSlotPicksJson").val() || '',
                "phone_number": phone_number,
                "full_name": full_name,
                "email": email,
                "note": note,
                "date": selectedDate,
                "total_amt": total_amt
            }

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                url: "{{ route('submit-appointment-details') }}",
                data: data,
                dataType: "json",
                timeout: 20000,
                success: function(response) {
                    if (response.status === 1) {
                        try {
                            gtag('event', 'conversion', {
                                'send_to': 'AW-17733466405/hfY5CLST8c4bEKXy_IdC',
                                'value': total_amt,
                                'currency': 'CAD'
                            });
                        } catch (e) {}
                        showBookingSuccessPage(response);
                    } else {
                        showBookingError(response.message || "Something went wrong.");
                    }
                },
                error: function(xhr, textStatus) {
                    var msg = textStatus === 'timeout'
                        ? "Request timed out. Please check Track Booking — your appointment may already be saved."
                        : "Something went wrong.";
                    try {
                        var res = xhr.responseJSON || JSON.parse(xhr.responseText || '{}');
                        msg = res.message || res.error || msg;
                    } catch (e) {}
                    showBookingError(msg);
                },
                complete: function() {
                    // Ensure Finish button is usable again if error path didn't reset it
                    if (nextBtn && nextBtn.dataset.submitting === '1') {
                        var errVisible = document.getElementById('BookingErrorModal');
                        var successVisible = document.getElementById('BookingSuccessPage');
                        var isError = errVisible && errVisible.classList.contains('show');
                        var isSuccess = successVisible && !successVisible.hasAttribute('hidden');
                        if (!isError && !isSuccess) {
                            resetFinishButton();
                        }
                    }
                }
            });
            return false;
        }
        function FetchAppointmentDetails() {
            var staff_id = $("#final_staff_id").val();
            var service_id = $("#final_service_id").val();
            var slot_start_value = $("#selectedSlotStartValue").val();
            var slot_end_value = $("#selectedSlotEndValue").val();
            var phone_number = $("#phone_number").val();
            var full_name = $("#full_name").val();
            var email = $("#email").val();
            var note = $("#note").val();
            $("#PreviewName").html(full_name || '-');
            $("#PreviewEmail").html(email || '-');
            $("#PreviewPhone").html(phone_number || '-');
            $("#PreviewNote").html(note || '-');
            // Create a proper Date object (month is 0-based in JS, so subtract 1)
            var dateObj = new Date(selectedYear, selectedMonth, selectedDay);
            // Format into "September 8, 2025"
            var formattedDate = dateObj.toLocaleDateString("en-US", {
                year: "numeric",
                month: "long",
                day: "numeric"
            });
            var slot_start_display = formatTimeWithAmPm(slot_start_value);
            $("#ConfirmDate").html(formattedDate);
            $("#ConfirmTime").html(slot_start_display);
            $("#AppointmentDate").html(formattedDate + " at " + slot_start_display);

            let year = dateObj.getFullYear();
            let month = String(dateObj.getMonth() + 1).padStart(2, '0');
            let day = String(dateObj.getDate()).padStart(2, '0');
            var selectedDate = `${year}-${month}-${day}`;
            var data = {
                'staff_id': staff_id,
                'service_id': service_id,
                'slot_start_value': slot_start_value,
                'slot_end_value': slot_end_value,
                'date': selectedDate,
                'group_picks': $("#staffSlotPicksJson").val() || ''
            }
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                url: "{{ route('get-appointment-details') }}",
                data: data,
                dataType: "json",
                success: function(response) {
                    if (response.status === 1) {
                        var staff = response.staff;
                        var services = response.services;
                        var serviceList = ``;
                        var totalPrice = 0;
                        function minutesBetween(start, end) {
                            if (!start || !end || typeof start !== 'string' || typeof end !== 'string') return 0;
                            var partsA = start.split(':');
                            var partsB = end.split(':');
                            if (partsA.length < 2 || partsB.length < 2) return 0;
                            var sh = parseInt(partsA[0], 10) || 0;
                            var sm = parseInt(partsA[1], 10) || 0;
                            var eh = parseInt(partsB[0], 10) || 0;
                            var em = parseInt(partsB[1], 10) || 0;
                            return Math.max(0, (eh * 60 + em) - (sh * 60 + sm));
                        }
                        var totalDurection = minutesBetween(slot_start_value, slot_end_value);
                        var storagePath = "{{ asset('storage') }}/";
                        var defaultImg = "{{ asset('assets/img/user.svg') }}";
                        services.forEach(element => {
                            var qty = parseInt(element.qty, 10) || 1;
                            var lineTotal = parseFloat(element.price) * qty;
                            totalPrice += lineTotal;
                            var priceFixed = lineTotal.toFixed(2);
                            serviceList += `
        <div class="bk-service-item service-row">
            <div class="bk-service-item-main">
                <div class="service-name">${element.service_name}</div>
                <div class="bk-service-meta">
                    <span class="service-duration"><i class="bi bi-clock"></i> ${element.duration} Minutes</span>
                    <span class="bk-service-qty">Qty: ${qty}</span>
                </div>
            </div>
            <div class="bk-service-price-col">
                <div class="service-price">${window.SHOP_CURRENCY_SYMBOL}${priceFixed}</div>
                <div class="bk-service-sub">Subtotal ${window.SHOP_CURRENCY_SYMBOL}${priceFixed}</div>
            </div>
        </div>`;
                        });
                        $("#ViewServiceHtml").html(serviceList || '<div class="text-muted">No services selected</div>');
                        $("#TotalDuration").html(totalDurection + " Minutes");
                        var payment = calcBookingPaymentSummary(totalPrice);
                        if (response.gst) {
                            window.bookingGst = response.gst;
                            payment = calcBookingPaymentSummary(totalPrice);
                        }
                        $("#total_amt").val(payment.total.toFixed(2));
                        $("#ConfirmSubtotal").html(window.SHOP_CURRENCY_SYMBOL + payment.subtotal.toFixed(2));
                        $("#ConfirmTaxLabel").text(payment.taxLabel);
                        $("#ConfirmTax").html(window.SHOP_CURRENCY_SYMBOL + payment.tax.toFixed(2));
                        $("#ConfirmGrandTotal").html(window.SHOP_CURRENCY_SYMBOL + payment.total.toFixed(2));
                        $("#liveTotal").html(window.SHOP_CURRENCY_SYMBOL + payment.total.toFixed(2));
                        $("#liveDuration").html(totalDurection + ' min');

                        var staffList = response.staff_display && response.staff_display.length
                            ? response.staff_display
                            : (response.staff_list && response.staff_list.length ? response.staff_list : (staff ? [staff] : []));
                        var StffHtml = '';
                        staffList.forEach(function(member) {
                            var avatarHtml = '';
                            var memberName = member && member.name ? member.name : '-';
                            var roleLabel = member && member.flexible ? 'Any available barber' : 'Barber';
                            if (member && (member.profile_image_url || (member.profile_img && String(member.profile_img).trim() !== '' && String(member.profile_img).toLowerCase() !== 'null'))) {
                                var memberImg = member.profile_image_url || (storagePath + String(member.profile_img).replace(/^(public\/|storage\/)+/, ''));
                                avatarHtml = `<img src="${memberImg}" alt="${memberName}" class="bk-staff-avatar-img" onerror="this.onerror=null;this.src='${defaultImg}';">`;
                            } else {
                                avatarHtml = `<img src="${defaultImg}" alt="${memberName}" class="bk-staff-avatar-img">`;
                            }
                            StffHtml += `
    <div class="bk-staff-card staff-details mb-2">
        ${avatarHtml}
        <div class="bk-staff-info">
            <div class="staff-name">${memberName}</div>
            <div class="bk-staff-role">${roleLabel}</div>
        </div>
        <span class="bk-staff-badge">Available</span>
    </div>`;
                        });
                        $("#ViewStaffHtml").html(StffHtml || '<div class="text-muted">No barber selected</div>');
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
        function formatTimeWithAmPm(time) {
            let [hour, minute] = time.split(":").map(Number);
            let ampm = hour >= 12 ? "PM" : "AM";
            hour = hour % 12 || 12;
            return `${hour}:${minute.toString().padStart(2, "0")} ${ampm}`;
        }

        const calendarMonth = document.getElementById('calendarMonth');
        const calendarBody = document.getElementById('calendarBody');
        const prevMonthBtn = document.getElementById('prevMonthBtn');
        const nextMonthBtn = document.getElementById('nextMonthBtn');
        let slotTimes = [];

        function daysInMonth(year, month) {
            return new Date(year, month + 1, 0).getDate();
        }

        function updateLiveDateSummary() {
            const dateObj = new Date(selectedYear, selectedMonth, selectedDay);
            const formatted = dateObj.toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
            const liveDate = document.getElementById('liveDate');
            if (liveDate) liveDate.textContent = formatted;
            const appointmentDate = document.getElementById('appointment_date');
            if (appointmentDate) {
                const y = dateObj.getFullYear();
                const m = String(dateObj.getMonth() + 1).padStart(2, '0');
                const d = String(dateObj.getDate()).padStart(2, '0');
                appointmentDate.value = `${y}-${m}-${d}`;
            }
        }

        function renderCalendar() {
            if (!calendarMonth || !calendarBody) return;
            calendarMonth.textContent = new Date(selectedYear, selectedMonth).toLocaleString('en-US', {
                month: 'long',
                year: 'numeric'
            });
            calendarBody.innerHTML = '';
            const firstDay = new Date(selectedYear, selectedMonth, 1).getDay();
            const numDays = daysInMonth(selectedYear, selectedMonth);
            const today = new Date();
            const todayYear = today.getFullYear();
            const todayMonth = today.getMonth();
            const todayDay = today.getDate();
            let tr = document.createElement('tr');

            for (let i = 0; i < firstDay; i++) {
                tr.appendChild(document.createElement('td'));
            }

            for (let day = 1; day <= numDays; day++) {
                if (tr.children.length === 7) {
                    calendarBody.appendChild(tr);
                    tr = document.createElement('tr');
                }
                const td = document.createElement('td');
                td.className = 'text-center slot-date';
                const isPastDay =
                    (selectedYear < todayYear) ||
                    (selectedYear === todayYear && selectedMonth < todayMonth) ||
                    (selectedYear === todayYear && selectedMonth === todayMonth && day < todayDay);
                const disabled = isPastDay || !isDateBookable(selectedYear, selectedMonth, day);

                if (disabled) {
                    td.innerHTML = `<button type="button" class="bk-day-btn is-disabled" disabled><span class="bk-day-num">${day}</span></button>`;
                } else {
                    const isToday = selectedYear === todayYear && selectedMonth === todayMonth && day === todayDay;
                    const selectedClass = day === selectedDay ? ' is-selected' : '';
                    const todayClass = isToday ? ' is-today' : '';
                    td.innerHTML = `<button type="button" class="bk-day-btn${selectedClass}${todayClass}" onclick="selectDay(${day})"><span class="bk-day-num">${day}</span><span class="bk-day-avail-dot" aria-hidden="true"></span></button>`;
                }
                tr.appendChild(td);
            }

            while (tr.children.length < 7) {
                tr.appendChild(document.createElement('td'));
            }
            calendarBody.appendChild(tr);
        }

        function getSlotDurationLabel() {
            var dur = ($('#ServiceMinite').text() || $('#ServiceMinite1').text() || '0').toString().replace(/[^0-9]/g, '');
            return (dur && dur !== '0') ? (dur + ' Minutes') : 'Minutes';
        }

        function syncDtContextStrip() {
            var service = ($('#liveService').text() || '-').trim();
            var barber = ($('#liveBarber').text() || '-').trim();
            var price = ($('#liveTotal').text() || window.SHOP_CURRENCY_SYMBOL + '0').trim();
            if ($('#dtCtxService').length) $('#dtCtxService').text(service || '-');
            if ($('#dtCtxBarber').length) $('#dtCtxBarber').text(barber || '-');
            if ($('#dtCtxPrice').length) $('#dtCtxPrice').text(price || window.SHOP_CURRENCY_SYMBOL + '0');
        }

        function escapeHtml(str) {
            return String(str || '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');
        }

        function formatServiceLine(svc) {
            var name = svc.name || 'Service';
            var mins = parseInt(svc.duration, 10) || 0;
            var qty = parseInt(svc.qty, 10) || 1;
            if (qty > 1) {
                return escapeHtml(name) + ' ' + mins + ' min × ' + qty + ' = ' + (mins * qty) + ' min';
            }
            return escapeHtml(name) + ' · ' + mins + ' min';
        }

        function clearStaffSlotPicks() {
            window.staffSlotPicks = {};
            window.staffSlotGroupCount = 0;
            $('#staffSlotPicksJson').val('');
        }

        function syncOverallSlotFromPicks() {
            var picks = window.staffSlotPicks || {};
            var keys = Object.keys(picks);
            $('#staffSlotPicksJson').val(keys.length ? JSON.stringify(keys.map(function(k) {
                return picks[k];
            })) : '');
            if (!keys.length) {
                $('#selectedSlotStartValue').val('');
                $('#selectedSlotEndValue').val('');
                $('#liveTime').text('-');
                return;
            }
            var starts = keys.map(function(k) { return picks[k].start; }).sort();
            var ends = keys.map(function(k) { return picks[k].end; }).sort();
            var maxDur = 0;
            keys.forEach(function(k) {
                maxDur = Math.max(maxDur, parseInt(picks[k].duration, 10) || 0);
            });
            $('#selectedSlotStartValue').val(starts[0]);
            $('#selectedSlotEndValue').val(ends[ends.length - 1]);
            var live = starts.map(function(t) { return formatTime(t); }).join(' · ');
            $('#liveTime').text(live);
            if (maxDur) {
                $('#ServiceMinite').html(maxDur);
                $('#ServiceMinite1').html(maxDur);
                $('#liveDuration').text(maxDur + ' min');
            }
            syncDtContextStrip();
        }

        function renderStaffSlotGroups(groups) {
            if (!groups || !groups.length) return '';
            window.staffSlotGroupCount = groups.length;
            var html = '';
            groups.forEach(function(group) {
                var key = String(group.key || '');
                var duration = parseInt(group.duration, 10) || 0;
                var svcLines = (group.services || []).map(formatServiceLine).join('<br>');
                html += '<div class="bk-staff-slot-block" data-group-key="' + escapeHtml(key) + '">';
                html += '<div class="bk-staff-slot-head">';
                html += '<div><div class="bk-staff-slot-name"><i class="fas fa-user-tie me-2"></i>' + escapeHtml(group.staff_name || 'Barber') + '</div>';
                html += '<div class="bk-staff-slot-services">' + (svcLines || 'Selected services') + '</div></div>';
                html += '<span class="bk-staff-slot-mins">' + duration + ' min</span>';
                html += '<button type="button" class="bk-staff-slot-toggle" onclick="toggleStaffSlotSection(this)" aria-expanded="true">Collapse</button>';
                html += '</div>';
                html += '<div class="bk-staff-slot-body">';
                if (group.slots && group.slots.length) {
                    html += '<div class="bk-slot-grid">';
                    group.slots.forEach(function(slot) {
                        html += '<button type="button" class="slot-btn bk-slot-chip" data-group-key="' + escapeHtml(key) + '" data-start="' + escapeHtml(slot.start) + '" data-end="' + escapeHtml(slot.end) + '" data-duration="' + duration + '">';
                        html += '<i class="fas fa-clock"></i>';
                        html += '<span class="bk-slot-chip-copy"><span class="bk-slot-chip-time">' + formatTime(slot.start) + '</span>';
                        html += '<span class="bk-slot-chip-mins-label">' + duration + ' min</span></span>';
                        html += '<span class="bk-slot-chip-check"><i class="fas fa-check"></i></span>';
                        html += '</button>';
                    });
                    html += '</div>';
                } else {
                    html += '<div class="bk-staff-slot-empty">No times available for this barber on the selected date.</div>';
                }
                html += '</div>'; // bk-staff-slot-body
                html += '</div>';
            });
            return html;
        }

        function toggleStaffSlotSection(btn) {
            try {
                var $btn = $(btn);
                var $block = $btn.closest('.bk-staff-slot-block');
                var $body = $block.find('.bk-staff-slot-body').first();
                if (!$body.length) return;

                var collapsed = $body.hasClass('is-collapsed');
                if (collapsed) {
                    $body.removeClass('is-collapsed');
                    $btn.text('Collapse');
                    $btn.attr('aria-expanded', 'true');
                } else {
                    $body.addClass('is-collapsed');
                    $btn.text('Expand');
                    $btn.attr('aria-expanded', 'false');
                }
            } catch (e) {
                // no-op
            }
        }

        function renderSlotEmpty(message, subtitle) {
            var msg = message || 'No Available Slots';
            var sub = subtitle || 'Please choose another date.';
            return `
                <div class="bk-slot-empty-state">
                    <div class="bk-empty-illu">
                        <i class="fas fa-calendar-xmark"></i>
                    </div>
                    <h6>${msg}</h6>
                    <p>${sub}</p>
                </div>`;
        }

        function updateSlotCount(count) {
            var $wrap = $('#bkSlotCount');
            var $text = $('#bkSlotCountText');
            if (!$wrap.length) return;
            if (count > 0) {
                $wrap.prop('hidden', false);
                $text.text(count + ' Available Slots');
            } else {
                $wrap.prop('hidden', true);
            }
        }

        function selectDay(day) {
            selectedDay = day;
            $("#selectedSlotStartValue").val('');
            $("#selectedSlotEndValue").val('');
            $("#liveTime").text('-');
            clearStaffSlotPicks();
            updateLiveDateSummary();
            syncDtContextStrip();
            renderCalendar();

            if (!isDateBookable(selectedYear, selectedMonth, selectedDay)) {
                updateSlotCount(0);
                $("#MorningSlotHtml").html(renderSlotEmpty('No Available Slots', 'Please choose another date.'));
                return false;
            }

            var final_staff_id = $("#final_staff_id").val();
            var final_service_id = $("#final_service_id").val();

            if (!final_staff_id || !final_service_id) {
                updateSlotCount(0);
                $("#MorningSlotHtml").html(renderSlotEmpty('Almost there', 'Please select a service and barber first.'));
                return false;
            }

            setActiveStaffDayOffs(final_staff_id);
            updateSlotCount(0);
            $("#MorningSlotHtml").html("<div class='bk-slot-loading'><span class='spinner-border spinner-border-sm me-2'></span>Loading times...</div>");

            let selectedDate = new Date(selectedYear, selectedMonth, selectedDay);
            let dayName = new Intl.DateTimeFormat('en-US', { weekday: 'long' }).format(selectedDate);
            var data = {
                'selectedYear': selectedYear,
                'selectedMonth': selectedMonth,
                'selectedDay': selectedDay,
                'staff_id': final_staff_id,
                'service_id': final_service_id,
                'day_name': dayName
            };

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
                    if (response.status == 1) {
                        if (response.duration) {
                            $("#ServiceMinite").html(response.duration);
                            $("#ServiceMinite1").html(response.duration);
                            $("#liveDuration").text(response.duration + ' min');
                        }
                        syncDtContextStrip();

                        if (response.groups && response.groups.length) {
                            var groupHtml = renderStaffSlotGroups(response.groups);
                            var totalSlots = 0;
                            response.groups.forEach(function(g) {
                                totalSlots += (g.slots && g.slots.length) ? g.slots.length : 0;
                            });
                            if (totalSlots) {
                                slotTimes = [];
                                response.groups.forEach(function(g) {
                                    (g.slots || []).forEach(function(slot) { slotTimes.push(slot.start); });
                                });
                                $("#MorningSlotHtml").html(groupHtml);
                                updateSlotCount(totalSlots);
                            } else {
                                updateSlotCount(0);
                                $("#MorningSlotHtml").html(renderSlotEmpty(
                                    response.message || 'No Available Slots',
                                    'Please choose another date or different barbers.'
                                ));
                            }
                        } else if (response.slots && response.slots.length) {
                            slotTimes = [];
                            let MorningSlotHtml = '';
                            var slotDur = parseInt(response.duration, 10) || 0;
                            response.slots.forEach(slot => {
                                const slotText = formatTime(slot.start);
                                slotTimes.push(slot.start);
                                MorningSlotHtml += `
                <button type="button"
                        class="slot-btn bk-slot-chip"
                        onclick="selectSlot(this, '${slot.start}', '${slot.end}')">
                    <i class="fas fa-clock"></i>
                    <span class="bk-slot-chip-copy"><span class="bk-slot-chip-time">${slotText}</span>
                    <span class="bk-slot-chip-mins-label">${slotDur ? slotDur + ' min' : ''}</span></span>
                    <span class="bk-slot-chip-check"><i class="fas fa-check"></i></span>
                </button>`;
                            });
                            window.staffSlotGroupCount = 1;
                            $("#MorningSlotHtml").html('<div class="bk-slot-grid">' + MorningSlotHtml + '</div>');
                            updateSlotCount(response.slots.length);
                        } else {
                            updateSlotCount(0);
                            $("#MorningSlotHtml").html(renderSlotEmpty(
                                response.message || 'No Available Slots',
                                'Please choose another date or different barbers.'
                            ));
                        }
                    } else {
                        const msg = response.message || 'No Available slots';
                        updateSlotCount(0);
                        $("#MorningSlotHtml").html(renderSlotEmpty(msg, 'Please choose another date.'));
                        if ($("#EveningSlotHtml").length) {
                            $("#EveningSlotHtml").html(renderSlotEmpty(msg, 'Please choose another date.'));
                        }
                    }
                },
                error: function() {
                    updateSlotCount(0);
                    $("#MorningSlotHtml").html(renderSlotEmpty('Unable to load times', 'Please try again in a moment.'));
                }
            });
            return false;
        }

        function formatTime(timeStr) {
            let [hours, minutes] = timeStr.split(':').map(Number);
            let ampm = hours >= 12 ? 'PM' : 'AM';
            hours = hours % 12 || 12;
            hours = hours < 10 ? "0" + hours : hours;
            return `${hours}:${minutes.toString().padStart(2, '0')} ${ampm}`;
        }

        function selectSlot(button, start, end) {
            document.querySelectorAll('.slot-btn').forEach(btn => btn.classList.remove('active'));
            button.classList.add('active');
            document.getElementById('selectedSlotStartValue').value = start;
            document.getElementById('selectedSlotEndValue').value = end;
            const liveTime = document.getElementById('liveTime');
            if (liveTime) liveTime.textContent = formatTime(start);
            syncDtContextStrip();
        }

        $(document).on('click', '.bk-staff-slot-block .slot-btn', function(e) {
            e.preventDefault();
            var $btn = $(this);
            var key = String($btn.attr('data-group-key') || '');
            if (!key) return;
            $btn.closest('.bk-staff-slot-block').find('.slot-btn').removeClass('active');
            $btn.addClass('active');
            window.staffSlotPicks = window.staffSlotPicks || {};
            window.staffSlotPicks[key] = {
                key: key,
                start: $btn.attr('data-start'),
                end: $btn.attr('data-end'),
                duration: parseInt($btn.attr('data-duration'), 10) || 0
            };
            syncOverallSlotFromPicks();
        });

        function changeMonth(delta) {
            selectedMonth += delta;
            if (selectedMonth < 0) {
                selectedMonth = 11;
                selectedYear--;
            } else if (selectedMonth > 11) {
                selectedMonth = 0;
                selectedYear++;
            }
            const maxDay = daysInMonth(selectedYear, selectedMonth);
            if (selectedDay > maxDay) selectedDay = maxDay;

            const today = new Date();
            if (selectedYear === today.getFullYear() && selectedMonth === today.getMonth() && selectedDay < today.getDate()) {
                selectedDay = today.getDate();
            }

            renderCalendar();
            if (currentStep === 3) {
                selectDay(selectedDay);
            }
        }

        if (prevMonthBtn) {
            prevMonthBtn.onclick = function (e) {
                e.preventDefault();
                changeMonth(-1);
            };
        }
        if (nextMonthBtn) {
            nextMonthBtn.onclick = function (e) {
                e.preventDefault();
                changeMonth(1);
            };
        }

        renderCalendar();
        window.selectDay = selectDay;
        window.selectSlot = selectSlot;
        window.renderCalendar = renderCalendar;
    </script>
    <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
    <script>
    (function () {
        if (window.AOS) AOS.init({ duration: 700, once: true, offset: 40 });

        function textOf($el) {
            return ($el && $el.text()) ? $el.text().trim() : '';
        }

        function syncLiveSummary() {
            // Services from selected cards
            var services = [];
            $('.service-card.active, .step-one-service-card.active, .step-two-service-card.active').each(function () {
                var name = $(this).find('.fw-semibold').first().text().trim();
                var qty = parseInt($(this).attr('data-qty') || '0', 10) || 0;
                if (name && qty > 0) services.push(qty > 1 ? (name + ' ×' + qty) : name);
            });
            if (services.length) $('#liveService').text(services.join(', '));

            // Barber
            var barberNames = [];
            $('.staff-card.selected, .step-one-staff-card.selected, .step-two-staff-card.selected').each(function () {
                var sn = $(this).find('.fw-semibold').first().text().trim();
                if (sn) barberNames.push(sn);
            });
            if (barberNames.length) $('#liveBarber').text(dedupeStaffNames(barberNames).join(', '));

            // Duration
            var dur = $('#ServiceMinite').text() || $('#ServiceMinite1').text() || $('#TotalDuration').text();
            if (dur && dur !== '0') $('#liveDuration').text(String(dur).indexOf('min') > -1 ? dur : (dur + ' min'));

            // Price
            var total = $('#total_amt').val();
            if (total) $('#liveTotal').text(window.SHOP_CURRENCY_SYMBOL + total);

            // Date/time from appointment preview when available
            var appt = $('#AppointmentDate').text().trim();
            if (appt) {
                var parts = appt.split(' at ');
                if (parts[0]) $('#liveDate').text(parts[0]);
                if (parts[1]) $('#liveTime').text(parts[1]);
            }

            // Selected slot button
            var $slot = $('.slot-btn.active').first();
            if ($slot.length) {
                var t = $slot.find('.bk-slot-chip-time').text().trim() || $slot.find('.bk-slot-card-time').text().trim() || $slot.text().trim();
                $('#liveTime').text(t);
            }

            if (typeof syncDtContextStrip === 'function') syncDtContextStrip();
        }

        $(document).on('click', '.service-card, .staff-card, .slot-btn, .tab-btn', function () {
            setTimeout(syncLiveSummary, 50);
        });

        var origUpdate = window.updateStepIndicators;
        // Mark vertical rows
        setInterval(function () {
            $('.bk-step-row').each(function () {
                var n = $(this).data('step-row');
                var $ind = $('#step-indicator-' + n);
                $(this).toggleClass('done', $ind.hasClass('completed') || $ind.hasClass('active'));
            });
            syncLiveSummary();
        }, 800);

        // Observe total_amt changes
        var amt = document.getElementById('total_amt');
        if (amt && window.MutationObserver) {
            new MutationObserver(syncLiveSummary).observe(amt, { attributes: true, attributeFilter: ['value'] });
        }
        $('#total_amt').on('change', syncLiveSummary);
    })();
    </script>
</body>
</html>
