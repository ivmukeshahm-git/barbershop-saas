@extends('saas.layout')

@section('title', 'Barber Shop Management Software')
@section('meta_description', 'Manage appointments, customers, barbers, services, billing and your barber shop operations with BarberPro.')
@section('body_class', 'lp-page')

@php
    $img = fn ($path) => asset('frontend/images/' . ltrim($path, '/'));

    $modules = [
        ['Dashboard', 'The day, before the first chair fills', 'Open admin and the floor is already on screen: who is due, what is next, and which services are moving.', ['Today’s appointments', 'Upcoming bookings', 'Top services', 'Staff performance'], 'landing/admin-dashboard.jpg', 'Modern barber shop interior', true],
        ['Calendar', 'Book the chair on the calendar', 'Pick a barber, a time, and a service. Add a break when a chair is off the floor.', ['Book an appointment', 'Add a break', 'See who is free'], 'landing/admin-calendar.jpg', 'Barber giving a client a haircut', false],
        ['Appointments', 'Confirm, move, or turn down a request', 'Public bookings land as requests. The list is where the shop accepts, rejects, or reschedules them.', ['Confirm or reject', 'Reschedule', 'Open booking details'], 'landing/admin-appointments.jpg', 'Barber cutting a client’s hair in the shop', false],
        ['Customers', 'A profile for every regular', 'Name, phone, and the visits already on file — including bookings that are still waiting.', ['Customer list', 'Profile and history', 'Pending appointments'], 'landing/admin-customers.jpg', 'Client seated for a haircut in a barbershop', false],
        ['Staff', 'Barbers, hours, and what they can cut', 'Add the team, give each person a role, and mark the services and days they actually work.', ['Staff and roles', 'Assigned services', 'Working hours and days off', 'Daily earnings'], 'landing/admin-staff.jpg', 'Barber standing beside a chair in the shop', false],
        ['Services & products', 'One menu for the chair and the bill', 'Categories, prices, and durations live in one place. Products sold at the chair use the same admin.', ['Service categories', 'Prices and durations', 'Retail products'], 'landing/admin-services.jpg', 'Barber scissors, comb, and razor laid out', false],
        ['Invoices & GST', 'The bill goes out with the visit', 'Services, products, and GST close on an invoice. Record how they paid, then email the bill.', ['Create and edit invoices', 'GST rates', 'Payment methods', 'Email the invoice'], 'landing/admin-invoice.jpg', 'Receipt printers and barber tools on the counter', false],
        ['Reports', 'What the shop did, without a spreadsheet', 'Appointments, payments, staff earnings, and what products actually sold.', ['Appointment report', 'Payments', 'Staff earnings', 'Product sales'], 'landing/admin-reports.jpg', 'Barber shop floor with chairs and mirrors', false],
    ];

    $benefits = [
        ['bi-clock', 'Hours back in the day', 'Less time rewriting bookings and adding up cash by hand.'],
        ['bi-calendar-check', 'Fewer missed chairs', 'The schedule is shared, so a slot is either taken or open.'],
        ['bi-people', 'A real customer list', 'Regulars are on file, not only in someone’s memory.'],
        ['bi-person-badge', 'Staff you can see', 'Who is in, which services they do, and how full they are.'],
        ['bi-receipt', 'Cleaner checkout', 'The bill matches the service, with GST included when you need it.'],
        ['bi-graph-up', 'A shop you can read', 'Revenue and bookings show up without exporting a file.'],
    ];

    $steps = [
        [
            'num' => '01',
            'title' => 'Create Your Shop',
            'desc' => 'Set up your barber business and get your private admin workspace ready.',
            'icon' => 'bi-shop',
            'details' => [
                'Choose your shop name',
                'Set timezone and currency',
                'Create your owner account',
                'Configure basic shop information',
            ],
            'image' => 'landing/owner-desk.jpg',
            'imageAlt' => 'Barber shop owner managing business in modern shop interior',
        ],
        [
            'num' => '02',
            'title' => 'Add Services & Barbers',
            'desc' => 'Add your services, prices, durations and barber team in just a few clicks.',
            'icon' => 'bi-scissors',
            'details' => [
                'Add haircut services',
                'Set service prices',
                'Set service duration',
                'Add barbers',
                'Configure staff availability',
            ],
            'image' => 'landing/barber-working.jpg',
            'imageAlt' => 'Professional barber working with customer in modern barber shop',
        ],
        [
            'num' => '03',
            'title' => "Manage Today's Bookings",
            'desc' => 'See your appointments, confirm bookings and keep your entire day organized.',
            'icon' => 'bi-calendar-event',
            'details' => [
                'View appointment calendar',
                'Confirm bookings',
                'Track appointment status',
                'Reschedule appointments',
                'Manage daily schedule',
            ],
            'image' => 'landing/admin-calendar.jpg',
            'imageAlt' => 'BarberPro appointment calendar dashboard with bookings and barber schedule',
        ],
        [
            'num' => '04',
            'title' => 'Track & Grow Your Business',
            'desc' => 'Use reports and insights to understand your shop and make better decisions.',
            'icon' => 'bi-graph-up',
            'details' => [
                'Track revenue',
                'View appointment reports',
                'Monitor customers',
                'Track staff activity',
                'Understand business performance',
            ],
            'image' => 'landing/admin-reports.jpg',
            'imageAlt' => 'BarberPro analytics dashboard with revenue charts and business metrics',
        ],
    ];

    $faqs = [
        ['What is BarberPro?', 'It is shop software for barbers: appointments, customers, staff, services, billing and reports in one admin.'],
        ['Who is it for?', 'Owners, single-chair barbers and teams that are adding more staff.'],
        ['Can I manage more than one barber?', 'Yes. Staff limits depend on the plan you buy.'],
        ['Can customers request a time?', 'Yes. The public booking page takes a request. Your team confirms or moves it in the admin.'],
        ['Can I take payments and GST?', 'Yes. Invoices cover services, products and GST.'],
        ['How do I start?', 'Choose Get Started, create the owner login, add the shop and pick a plan. Payment happens at checkout.'],
        ['Can I change the plan later?', 'Yes. Upgrade when the shop grows, or renew after the current plan ends. The same shop account stays.'],
        ['What do I get after checkout?', 'Your admin opens on that shop: appointments, customers, staff, services, bills and reports for the plan you picked.'],
    ];
@endphp

@push('head')
<link href="{{ asset('frontend/css/saas-landing.css') }}?v=18" rel="stylesheet">
@endpush

@section('content')

<section class="bn-banner" id="top">
    <video class="bn-banner-video" autoplay muted loop playsinline poster="{{ $img('landing/banner-shop.jpg') }}" aria-hidden="true">
        <source src="{{ asset('frontend/videos/barber-shop.mp4') }}" type="video/mp4">
    </video>
    <div class="bn-banner-shade" aria-hidden="true"></div>
    <div class="bp-container">
        <div class="bn-banner-copy" data-reveal>
            <p class="bn-kicker">BARBER SHOP MANAGEMENT SOFTWARE</p>
            <h1>Run Your Barber Shop.<br><span class="bn-highlight">Everything You Need in One Place.</span></h1>
            <p class="bn-banner-desc">Manage appointments, customers, barbers, services, billing and daily shop operations from one simple platform.</p>
            <p class="bn-banner-benefit">Spend less time managing your shop and more time serving your customers.</p>
            <div class="bn-actions">
                <a class="bp-btn bp-btn-primary bp-btn-lg" href="{{ route('saas.signup') }}">
                    Start Your Shop
                    <i class="bi bi-arrow-right" aria-hidden="true"></i>
                </a>
                <a class="bp-btn bp-btn-lg bn-btn-ghost" href="#features">Explore Features</a>
            </div>
            <ul class="bn-trust">
                <li><i class="bi bi-check-lg" aria-hidden="true"></i>Easy Setup</li>
                <li><i class="bi bi-check-lg" aria-hidden="true"></i>No Technical Skills</li>
                <li><i class="bi bi-check-lg" aria-hidden="true"></i>Secure & Reliable</li>
                <li><i class="bi bi-check-lg" aria-hidden="true"></i>GST Ready</li>
            </ul>
            <p class="bn-tagline">Built for modern barber shops.</p>
        </div>
    </div>
</section>

<div class="bn-offer-bar" aria-label="Offers">
    <div class="bn-offer-track">
        @foreach ([false, true] as $copy)
            <ul {{ $copy ? 'aria-hidden=true' : '' }}>
                <li><i class="bi bi-tag" aria-hidden="true"></i> Yearly plans save about 17%</li>
                <li><i class="bi bi-gift" aria-hidden="true"></i> Start on the free plan, upgrade when the shop grows</li>
                <li><i class="bi bi-receipt" aria-hidden="true"></i> GST invoices included with every paid plan</li>
                <li><i class="bi bi-calendar-check" aria-hidden="true"></i> Appointments, staff and bills in one admin</li>
                <li><i class="bi bi-stars" aria-hidden="true"></i> Professional plan for shops adding more chairs</li>
                <li><i class="bi bi-shop" aria-hidden="true"></i> Open your shop today and pay at checkout</li>
            </ul>
        @endforeach
    </div>
</div>

<section class="bn-section bn-admin" id="features">
    <div class="bp-container">
        <div id="solution" class="bn-head" data-reveal>
            <h2>What the software actually does.</h2>
            <p>These are the screens inside the shop admin — the same places a booking, a barber, and a bill are handled.</p>
        </div>

        <div class="bn-admin-grid">
            @foreach ($modules as [$area, $title, $copy, $points, $photo, $alt, $wide])
                <article class="bn-mod {{ $wide ? 'is-wide' : '' }}" data-reveal>
                    <figure>
                        <img src="{{ $img($photo) }}" alt="{{ $alt }}" width="1200" height="800" loading="lazy">
                    </figure>
                    <div class="bn-mod-body">
                        <span class="bn-mod-label">{{ $area }}</span>
                        <h3>{{ $title }}</h3>
                        <p>{{ $copy }}</p>
                        <ul class="bn-chips">
                            @foreach ($points as $point)
                                <li>{{ $point }}</li>
                            @endforeach
                        </ul>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>

<section class="bn-showcase" id="watch" aria-roledescription="carousel" aria-label="See BarberPro in action">
    @php
        $showcase = [
            [
                'key' => 'appointments',
                'num' => '01',
                'label' => 'Appointments',
                'title' => 'Keep Every Appointment Organized',
                'desc' => 'Manage your daily bookings, confirm appointments and quickly see what is coming next.',
                'points' => ["Today's appointments", 'Easy rescheduling', 'Available time slots'],
                'cta' => 'Explore Appointments',
                'image' => 'landing/barber-working.jpg',
                'alt' => 'Barber working with a customer in the shop',
            ],
            [
                'key' => 'customers',
                'num' => '02',
                'label' => 'Customers',
                'title' => 'Know Your Customers Better',
                'desc' => 'Keep customer details and booking history organized so you can give a better visit every time.',
                'points' => ['Customer profiles', 'Booking history', 'Returning customers'],
                'cta' => 'Manage Customers',
                'image' => 'landing/feature-client.jpg',
                'alt' => 'Client seated for a haircut in a barber shop',
            ],
            [
                'key' => 'staff',
                'num' => '03',
                'label' => 'Barbers & Staff',
                'title' => 'Manage Your Team With Ease',
                'desc' => 'Add your barbers, assign services and set working hours from one place.',
                'points' => ['Barber profiles', 'Working schedules', 'Assigned services'],
                'cta' => 'Manage Your Team',
                'image' => 'landing/team-floor.jpg',
                'alt' => 'Barber working on the shop floor',
            ],
            [
                'key' => 'billing',
                'num' => '04',
                'label' => 'Billing & Payments',
                'title' => 'Finish Every Visit With Simple Billing',
                'desc' => 'Create bills, record payments and keep the day’s transactions organized.',
                'points' => ['Quick invoices', 'Payment tracking', 'GST-ready billing'],
                'cta' => 'Explore Billing',
                'image' => 'landing/admin-invoice.jpg',
                'alt' => 'Barber shop counter set up for checkout',
            ],
            [
                'key' => 'reports',
                'num' => '05',
                'label' => 'Reports & Insights',
                'title' => 'Understand How Your Shop Is Doing',
                'desc' => 'See appointments, revenue, payments and staff activity without a spreadsheet.',
                'points' => ['Revenue overview', 'Appointment reports', 'Staff performance'],
                'cta' => 'View Reports',
                'image' => 'landing/owner-managing.jpg',
                'alt' => 'Shop owner reviewing the day’s numbers',
            ],
        ];
    @endphp
    <div class="bp-container">
        <header class="bn-showcase-head">
            <h2>See BarberPro in Action</h2>
            <p>Everything you need to manage your barber shop, from bookings to billing, in one simple platform.</p>
        </header>

        <div class="bn-showcase-stage" data-showcase>
            <p class="bn-showcase-status" data-showcase-status aria-live="polite">Slide 1 of 5, Appointments</p>
            <div class="bn-showcase-viewport">
                @foreach ($showcase as $index => $slide)
                    <article class="bn-slide {{ $index === 0 ? 'is-active' : '' }}" data-slide="{{ $index }}" role="group" aria-roledescription="slide" aria-label="{{ $slide['num'] }} of 05, {{ $slide['label'] }}" @if ($index !== 0) aria-hidden="true" inert @endif>
                        <div class="bn-slide-copy">
                            <span class="bn-slide-label">{{ $slide['label'] }}</span>
                            <h3>{{ $slide['title'] }}</h3>
                            <p>{{ $slide['desc'] }}</p>
                            <ul>
                                @foreach ($slide['points'] as $point)
                                    <li><i class="bi bi-check-lg" aria-hidden="true"></i> {{ $point }}</li>
                                @endforeach
                            </ul>
                            <a class="bn-slide-cta" href="#features">{{ $slide['cta'] }} <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
                        </div>
                        <div class="bn-slide-visual">
                            <img src="{{ $img($slide['image']) }}" alt="{{ $slide['alt'] }}" width="960" height="720" @if ($index > 0) loading="lazy" @endif>
                            @if ($slide['key'] === 'appointments')
                                <aside class="bn-float" aria-hidden="true">
                                    <strong>Today’s appointments</strong>
                                    <ul>
                                        <li><span>10:00</span> Haircut</li>
                                        <li><span>11:30</span> Beard trim</li>
                                        <li><span>12:15</span> Haircut + beard</li>
                                    </ul>
                                </aside>
                            @elseif ($slide['key'] === 'customers')
                                <aside class="bn-float" aria-hidden="true">
                                    <span class="bn-float-kicker">Customer</span>
                                    <strong>Rahul Sharma</strong>
                                    <p>12 visits<br>Last visit: Today</p>
                                </aside>
                            @elseif ($slide['key'] === 'staff')
                                <aside class="bn-float" aria-hidden="true">
                                    <strong>Today</strong>
                                    <ul>
                                        <li><i class="bi bi-check-lg"></i> Alex — Available</li>
                                        <li><i class="bi bi-check-lg"></i> Daniel — Busy</li>
                                        <li><i class="bi bi-check-lg"></i> Mike — Available</li>
                                    </ul>
                                </aside>
                            @elseif ($slide['key'] === 'billing')
                                <aside class="bn-float bn-float-bill" aria-hidden="true">
                                    <div class="bn-float-bill-head"><strong>Invoice #1024</strong><em>Paid</em></div>
                                    <ul>
                                        <li><span>Haircut</span><span>₹400</span></li>
                                        <li><span>Beard</span><span>₹200</span></li>
                                    </ul>
                                    <p><span>Total</span><strong>₹600</strong></p>
                                </aside>
                            @else
                                <aside class="bn-float bn-float-stats" aria-hidden="true">
                                    <strong>Today’s overview</strong>
                                    <ul>
                                        <li><span>Appointments</span><b>24</b></li>
                                        <li><span>Revenue</span><b>₹18,400</b></li>
                                        <li><span>Customers</span><b>31</b></li>
                                    </ul>
                                    <svg viewBox="0 0 160 42" aria-hidden="true">
                                        <path d="M4 34 L28 28 L52 30 L76 18 L100 20 L124 10 L156 8" fill="none" stroke="#8c4e24" stroke-width="2.5" stroke-linecap="round"/>
                                        <path d="M4 34 L28 28 L52 30 L76 18 L100 20 L124 10 L156 8 L156 42 L4 42 Z" fill="rgba(196,162,101,0.28)"/>
                                    </svg>
                                </aside>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>
            <div class="bn-showcase-nav">
                <button type="button" class="bn-showcase-arrow" data-showcase-dir="-1" aria-label="Previous slide">
                    <i class="bi bi-arrow-left" aria-hidden="true"></i>
                    <span>Previous</span>
                </button>
                <div class="bn-showcase-tabs" role="tablist" aria-label="Showcase slides">
                    @foreach ($showcase as $index => $slide)
                        <button type="button" class="bn-showcase-tab {{ $index === 0 ? 'is-active' : '' }}" role="tab" id="showcase-tab-{{ $index }}" aria-selected="{{ $index === 0 ? 'true' : 'false' }}" aria-label="{{ $slide['label'] }}" data-showcase-go="{{ $index }}">
                            <span class="bn-tab-num">{{ $slide['num'] }}</span>
                            <span class="bn-tab-track"><span class="bn-tab-fill"></span></span>
                        </button>
                    @endforeach
                </div>
                <button type="button" class="bn-showcase-arrow" data-showcase-dir="1" aria-label="Next slide">
                    <span>Next</span>
                    <i class="bi bi-arrow-right" aria-hidden="true"></i>
                </button>
            </div>
        </div>
    </div>
</section>

<section class="bn-section bn-benefits" id="benefits">
    <div class="bp-container">
        <div class="bn-head" data-reveal>
            <h2>What the shop gains after you buy.</h2>
            <p>Not another login. A shorter way to run the same floor.</p>
        </div>
        <div class="bn-benefit-grid" data-reveal>
            @foreach ($benefits as [$icon, $title, $copy])
                <article class="bn-benefit">
                    <i class="bi {{ $icon }}" aria-hidden="true"></i>
                    <strong>{{ $title }}</strong>
                    <p>{{ $copy }}</p>
                </article>
            @endforeach
        </div>
        <div class="bn-actions" style="margin-top:1.75rem">
            <a class="bp-btn bp-btn-primary bp-btn-lg" href="{{ route('saas.signup') }}">Get Started</a>
        </div>
    </div>
</section>

<section class="bn-section bn-steps-section" id="how-it-works">
    <div class="bp-container">
        <header class="bn-steps-header" data-reveal>
            <span class="bn-steps-eyebrow">HOW IT WORKS</span>
            <h2>From signup to the first full day.</h2>
            <p class="bn-steps-lead">Four simple steps. No complicated setup. Get your barber shop ready and start managing your business.</p>
        </header>

        <div class="bn-steps-track" data-reveal>
            <div class="bn-steps-connector" aria-hidden="true"></div>
            
            <div class="bn-steps-grid">
                @foreach ($steps as $index => $step)
                    <article class="bn-step-card" data-step="{{ $index }}">
                        <div class="bn-step-visual">
                            <img src="{{ $img($step['image']) }}" alt="{{ $step['imageAlt'] }}" loading="lazy" width="320" height="240">
                        </div>
                        <div class="bn-step-content">
                            <div class="bn-step-header">
                                <span class="bn-step-number">{{ $step['num'] }}</span>
                                <div class="bn-step-icon" aria-hidden="true">
                                    <i class="bi {{ $step['icon'] }}"></i>
                                </div>
                            </div>
                            <h3 class="bn-step-title">{{ $step['title'] }}</h3>
                            <p class="bn-step-desc">{{ $step['desc'] }}</p>
                            
                            <button class="bn-step-toggle" type="button" aria-expanded="false" aria-controls="step-details-{{ $index }}">
                                <span>What's included</span>
                                <i class="bi bi-chevron-down" aria-hidden="true"></i>
                            </button>
                            <ul id="step-details-{{ $index }}" class="bn-step-details" hidden>
                                @foreach ($step['details'] as $detail)
                                    <li><i class="bi bi-check" aria-hidden="true"></i> {{ $detail }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </div>
</section>

<section class="bn-cta-final" id="cta-final" aria-labelledby="cta-final-heading">
    <div class="bp-container">
        <div class="bn-cta-final-inner" data-reveal>
            <div class="bn-cta-final-content">
                <span class="bn-cta-final-eyebrow">READY TO GET STARTED?</span>
                <h2 id="cta-final-heading" class="bn-cta-final-title">
                    Ready to Run Your Barber Shop <span class="bn-cta-final-highlight">Smarter</span>?
                </h2>
                <p class="bn-cta-final-lead">
                    Set up your shop, add your team and services, and start managing appointments, customers and daily operations from one simple platform.
                </p>
                <div class="bn-cta-final-actions">
                    <a class="bp-btn bp-btn-primary bp-btn-lg bn-cta-final-btn-primary" href="{{ route('saas.signup') }}">
                        Start Your Shop
                        <i class="bi bi-arrow-right" aria-hidden="true"></i>
                    </a>
                    <a class="bp-btn bp-btn-outline bp-btn-lg bn-cta-final-btn-secondary" href="#features">
                        Explore Features
                    </a>
                </div>
                <ul class="bn-cta-final-trust" aria-label="Trust indicators">
                    <li><i class="bi bi-check-lg" aria-hidden="true"></i> Easy Setup</li>
                    <li><i class="bi bi-check-lg" aria-hidden="true"></i> Secure</li>
                    <li><i class="bi bi-check-lg" aria-hidden="true"></i> No Technical Skills</li>
                </ul>
            </div>
            <div class="bn-cta-final-visual">
                <div class="bn-cta-final-image-wrap">
                    <img src="{{ asset('assets/img/panel.png') }}" alt="Hariom Barbershop admin login screen" width="1816" height="866">
                </div>
                <div class="bn-cta-final-cards">
                <div class="bn-cta-final-dashboard" aria-label="BarberPro dashboard preview">
                    <div class="bn-dashboard-header">
                        <span class="bn-dashboard-dots">
                            <span></span><span></span><span></span>
                        </span>
                        <span class="bn-dashboard-title">Today's Overview</span>
                    </div>
                    <div class="bn-dashboard-grid">
                        <div class="bn-dashboard-stat">
                            <span class="bn-dashboard-label">Appointments</span>
                            <strong class="bn-dashboard-value">12</strong>
                        </div>
                        <div class="bn-dashboard-stat">
                            <span class="bn-dashboard-label">Revenue</span>
                            <strong class="bn-dashboard-value">₹24,800</strong>
                        </div>
                        <div class="bn-dashboard-stat">
                            <span class="bn-dashboard-label">Customers</span>
                            <strong class="bn-dashboard-value">1,284</strong>
                        </div>
                        <div class="bn-dashboard-stat">
                            <span class="bn-dashboard-label">Active Barbers</span>
                            <strong class="bn-dashboard-value">4</strong>
                        </div>
                    </div>
                    <div class="bn-dashboard-chart" aria-hidden="true">
                        <svg viewBox="0 0 200 80" preserveAspectRatio="none">
                            <defs>
                                <linearGradient id="chartGrad" x1="0" y1="0" x2="0" y2="1">
                                    <stop offset="0%" stop-color="#c4a265" stop-opacity="0.7"/>
                                    <stop offset="100%" stop-color="#8c4e24" stop-opacity="0.05"/>
                                </linearGradient>
                            </defs>
                            <path d="M10,70 L30,45 L50,55 L70,35 L90,40 L110,25 L130,30 L150,20 L170,25 L190,15 L190,80 L10,80 Z"
                                  fill="url(#chartGrad)" stroke="none"/>
                            <path d="M10,70 L30,45 L50,55 L70,35 L90,40 L110,25 L130,30 L150,20 L170,25 L190,15"
                                  fill="none" stroke="#8c4e24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <circle cx="30" cy="45" r="3" fill="#8c4e24"/>
                            <circle cx="70" cy="35" r="3" fill="#8c4e24"/>
                            <circle cx="110" cy="25" r="3" fill="#c4a265"/>
                            <circle cx="150" cy="20" r="3" fill="#c4a265"/>
                            <circle cx="190" cy="15" r="3" fill="#8c4e24"/>
                        </svg>
                    </div>
                </div>
                <div class="bn-cta-final-success" aria-label="Onboarding success">
                    <i class="bi bi-check-circle-fill" aria-hidden="true"></i>
                    <div>
                        <strong>Your shop is ready</strong>
                        <ul>
                            <li><i class="bi bi-check" aria-hidden="true"></i> Services added</li>
                            <li><i class="bi bi-check" aria-hidden="true"></i> Barbers added</li>
                            <li><i class="bi bi-check" aria-hidden="true"></i> Calendar ready</li>
                        </ul>
                    </div>
                </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="bp-section bp-section-light" id="pricing">
    <div class="bp-container">
        <div class="bp-section-head" data-reveal>
            <h2>Plans from the live price list.</h2>
            <p class="bp-lead">Monthly or yearly. You pay at checkout for the cycle you pick.</p>
        </div>
        <div data-reveal>
            <div class="bp-billing-toggle" role="group" aria-label="Billing cycle">
                <button type="button" class="is-active" data-billing="monthly">Monthly</button>
                <button type="button" data-billing="yearly">Yearly <em>Save ~17%</em></button>
            </div>
        </div>
        @if ($plans->isEmpty())
            <div class="bp-alert bp-alert-danger">No plans are available yet.</div>
        @else
            <div class="bp-pricing-grid" data-reveal>
                @foreach ($plans as $plan)
                    @php
                        $displayName = $plan->slug === 'growth' ? 'Professional' : $plan->name;
                        $subtitle = match ($plan->slug) {
                            'free' => 'For one barber getting started.',
                            'starter' => 'For a small shop.',
                            'growth' => 'For a shop that is adding chairs.',
                            'business' => 'For a larger team.',
                            default => $plan->tagline,
                        };
                        $ctaLabel = (float) $plan->price_monthly <= 0 ? 'Start free' : 'Choose ' . $displayName;
                    @endphp
                    <article class="bp-price-card {{ $plan->is_popular ? 'is-featured' : '' }}">
                        @if ($plan->is_popular)
                            <span class="bp-price-badge">Most popular</span>
                        @endif
                        <h3>{{ $displayName }}</h3>
                        <p class="bp-price-sub">{{ $subtitle }}</p>
                        <div class="bp-price">
                            <strong class="js-price" data-monthly="{{ number_format((float) $plan->price_monthly, 0) }}" data-yearly="{{ number_format((float) $plan->price_yearly, 0) }}">{{ (float) $plan->price_monthly <= 0 ? 'Free' : '₹' . number_format((float) $plan->price_monthly, 0) }}</strong>
                            <span class="js-cycle">{{ (float) $plan->price_monthly <= 0 ? 'to start' : '/ month' }}</span>
                        </div>
                        <ul>
                            @foreach (($plan->features ?? []) as $feature)
                                <li><i class="bi bi-check2" aria-hidden="true"></i> {{ $feature }}</li>
                            @endforeach
                        </ul>
                        <a class="bp-btn {{ $plan->is_popular ? 'bp-btn-primary' : 'bp-btn-outline' }} js-cta" href="{{ route('saas.signup', ['plan' => $plan->slug, 'billing' => 'monthly']) }}" data-plan="{{ $plan->slug }}">{{ $ctaLabel }}</a>
                    </article>
                @endforeach
            </div>
            <p class="bn-price-note">Free is ₹0. Paid plans are billed at checkout for the cycle you pick.</p>
        @endif
    </div>
</section>

<section class="bn-faq-section" id="faq">
    <div class="bp-container">
        <div class="bn-faq-layout">
            <aside class="bn-faq-intro">
                <span class="bn-faq-kicker">Questions</span>
                <h2>Before you buy.</h2>
                <p>The short answers shop owners ask before they open an account and pick a plan.</p>
                <div class="bn-faq-points">
                    <span><i class="bi bi-calendar-check" aria-hidden="true"></i> Bookings</span>
                    <span><i class="bi bi-people" aria-hidden="true"></i> Customers</span>
                    <span><i class="bi bi-person-badge" aria-hidden="true"></i> Staff</span>
                    <span><i class="bi bi-receipt" aria-hidden="true"></i> GST bills</span>
                </div>
                <div class="bn-faq-card">
                    <strong>Ready to open the shop?</strong>
                    <p>Create the account, choose a plan, and the admin is yours after checkout.</p>
                    <a class="bp-btn bp-btn-primary" href="{{ route('saas.signup') }}">Get Started</a>
                </div>
                <ul class="bn-faq-notes">
                    <li><i class="bi bi-check-lg" aria-hidden="true"></i> Setup uses the same shop account you create.</li>
                    <li><i class="bi bi-check-lg" aria-hidden="true"></i> Prices on this page are the live plan list.</li>
                    <li><i class="bi bi-check-lg" aria-hidden="true"></i> After checkout, shop login opens the admin.</li>
                </ul>
            </aside>
            <div class="bn-faq lp-faq">
                @foreach ($faqs as $index => [$question, $answer])
                    <details {{ $index === 0 ? 'open' : '' }}>
                        <summary>{{ $question }}</summary>
                        <p>{{ $answer }}</p>
                    </details>
                @endforeach
            </div>
        </div>
    </div>
</section>

<section class="bn-close" id="cta">
    <div class="bp-container">
        <div class="bn-close-card">
            <div class="bn-close-copy">
                <span class="bn-close-kicker">Ready when you are</span>
                <h2>Put the shop on one screen.</h2>
                <p>Create the account, pick a plan, and open your admin after checkout.</p>
                <div class="bn-actions">
                    <a class="bp-btn bp-btn-primary bp-btn-lg" href="{{ route('saas.signup') }}">Get Started</a>
                    <a class="bp-btn bp-btn-lg bn-close-login" href="{{ route('saas.find-shop') }}">Shop login</a>
                </div>
            </div>
            <figure class="bn-close-photo">
                <img src="{{ $img('landing/banner-shop.jpg') }}" alt="Barber finishing a haircut in the shop" width="1280" height="720" loading="lazy">
            </figure>
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
window.BP_PRICING = { signupBase: @json(url('/get-started')) };
</script>
<script>
// Steps expandable details toggle
document.addEventListener('DOMContentLoaded', function () {
  var stage = document.querySelector('[data-showcase]');
  if (stage) {
    var slides = Array.prototype.slice.call(stage.querySelectorAll('[data-slide]'));
    var tabs = Array.prototype.slice.call(stage.querySelectorAll('[data-showcase-go]'));
    var status = stage.querySelector('[data-showcase-status]');
    var index = 0;
    var count = slides.length;
    var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    var resumeTimer = null;
    var moving = false;

    function setPaused(paused) {
      stage.classList.toggle('is-paused', paused);
    }

    function go(next) {
      if (moving) return;
      moving = true;
      index = (next + count) % count;
      slides.forEach(function (slide, i) {
        var active = i === index;
        slide.classList.toggle('is-active', active);
        slide.setAttribute('aria-hidden', active ? 'false' : 'true');
        if (active) slide.removeAttribute('inert');
        else slide.setAttribute('inert', '');
      });
      tabs.forEach(function (tab, i) {
        tab.classList.toggle('is-active', i === index);
        tab.classList.toggle('is-done', i < index);
        tab.setAttribute('aria-selected', i === index ? 'true' : 'false');
        var fill = tab.querySelector('.bn-tab-fill');
        if (!fill) return;
        fill.style.animation = 'none';
        if (i === index && !reduceMotion) {
          void fill.offsetWidth;
          fill.style.animation = '';
        }
      });
      var current = slides[index];
      if (status && current) {
        status.textContent = 'Slide ' + (index + 1) + ' of ' + count + ', ' + (current.querySelector('.bn-slide-label') ? current.querySelector('.bn-slide-label').textContent : '');
      }
      moving = false;
    }

    stage.querySelectorAll('[data-showcase-dir]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        go(index + parseInt(btn.getAttribute('data-showcase-dir'), 10));
      });
    });
    tabs.forEach(function (tab) {
      tab.addEventListener('click', function () {
        go(parseInt(tab.getAttribute('data-showcase-go'), 10));
      });
    });

    stage.addEventListener('animationend', function (event) {
      var activeFill = tabs[index] && tabs[index].querySelector('.bn-tab-fill');
      if (event.animationName !== 'bnShowFill' || event.target !== activeFill) return;
      if (stage.classList.contains('is-paused') || reduceMotion) return;
      go(index + 1);
    });

    stage.addEventListener('mouseenter', function () { setPaused(true); });
    stage.addEventListener('mouseleave', function () { setPaused(false); });
    stage.addEventListener('focusin', function () { setPaused(true); });
    stage.addEventListener('focusout', function (event) {
      if (!stage.contains(event.relatedTarget)) setPaused(false);
    });

    var touchX = null;
    stage.addEventListener('touchstart', function (event) {
      touchX = event.changedTouches[0].clientX;
      setPaused(true);
      clearTimeout(resumeTimer);
    }, { passive: true });
    stage.addEventListener('touchend', function (event) {
      if (touchX === null) return;
      var delta = event.changedTouches[0].clientX - touchX;
      touchX = null;
      if (Math.abs(delta) > 40) go(index + (delta < 0 ? 1 : -1));
      resumeTimer = setTimeout(function () { setPaused(false); }, 2500);
    }, { passive: true });

    stage.addEventListener('keydown', function (event) {
      if (event.key === 'ArrowRight') { event.preventDefault(); go(index + 1); }
      if (event.key === 'ArrowLeft') { event.preventDefault(); go(index - 1); }
    });

    if (reduceMotion) stage.classList.add('is-reduced');
  }

  document.querySelectorAll('.bn-step-toggle').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var expanded = this.getAttribute('aria-expanded') === 'true';
      var targetId = this.getAttribute('aria-controls');
      var panel = document.getElementById(targetId);
      if (!panel) return;
      this.setAttribute('aria-expanded', !expanded);
      if (expanded) {
        panel.hidden = true;
      } else {
        panel.hidden = false;
      }
    });
  });
});
</script>
@endpush
