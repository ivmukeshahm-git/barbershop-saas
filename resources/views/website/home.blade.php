@extends('layouts.master')

@section('title', ($shopName ?? 'Misha Hair Studio Mumbai') . ' | Hair Studio')

@section('content')

{{-- HERO --}}
<section class="lx-hero" style="background-image:url('{{ asset('frontend/images/luxury/hero.jpg') }}')">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6" data-aos="fade-up">
                <div class="lx-hero-eyebrow">Luxury Barbershop Experience</div>
                <h1>{{ $shopName ?? 'Misha Hair Studio Mumbai' }}</h1>
                <p class="lead">Professional haircuts and grooming, tailored to your style and delivered with care.</p>
                <div class="lx-hero-actions">
                    <a href="{{ route('book') }}" class="btn-lx btn-lx-primary" target="_blank">
                        <i class="far fa-calendar-check"></i> Book Appointment
                    </a>
                    <a href="{{ route('services') }}" class="btn-lx btn-lx-outline">Explore Services</a>
                </div>
            </div>
            <div class="col-lg-6" data-aos="fade-up">
                <div class="lx-hero-visual">
                    <span class="lx-shape lx-shape-1"></span>
                    <span class="lx-shape lx-shape-2"></span>
                    <img src="{{ asset('frontend/images/luxury/hero-barber.jpg') }}" alt="Professional barber" class="main-img">
                    <div class="stat-wrap">
                        <div class="lx-stat-card lx-stat-1">
                            <strong>25+</strong>
                            <span>Years Experience</span>
                        </div>
                        <div class="lx-stat-card lx-stat-2">
                            <strong>5000+</strong>
                            <span>Happy Clients</span>
                        </div>
                        <div class="lx-stat-card lx-stat-3">
                            <strong>Pro</strong>
                            <span>Expert Barbers</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ABOUT --}}
<section class="section-pad bg-white-lx">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6" data-aos="zoom-in">
                <div class="lx-collage">
                    <img src="{{ asset('frontend/images/luxury/about-1.jpg') }}" alt="Salon interior" class="img-main">
                    <img src="{{ asset('frontend/images/luxury/about-2.jpg') }}" alt="Barber at work" class="img-float">
                    <div class="exp-badge">
                        <strong>25+</strong>
                        <span>Years</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-6" data-aos="fade-right">
                <span class="section-eyebrow">About Us</span>
                <h2 class="section-title">{{ $shopName ?? 'Misha Hair Studio Mumbai' }}</h2>
                <p>We provide a professional barbering experience that combines traditional techniques with modern styling, delivered with precision and care.</p>
                <p><strong style="color:var(--lx-navy)">Precision. Style. For Every Generation.</strong> Book an appointment with our team today.</p>
                <ul class="lx-checklist">
                    <li><i class="fas fa-check-circle"></i><span>Modern cuts, fades, line-ups &amp; classic styles</span></li>
                    <li><i class="fas fa-check-circle"></i><span>Beard sculpting, hot towel shaves &amp; facials</span></li>
                    <li><i class="fas fa-check-circle"></i><span>Kids’ haircuts in a family-friendly shop</span></li>
                    <li><i class="fas fa-check-circle"></i><span>Skill, consistency &amp; respect for the craft</span></li>
                </ul>
                <a href="{{ route('about') }}" class="btn-lx btn-lx-primary">Learn More</a>
            </div>
        </div>

        <div class="lx-counters mt-5" data-aos="fade-up">
            <div class="row">
                <div class="col-6 col-md-3">
                    <div class="counter-item"><strong data-count="25" data-suffix="+">25+</strong><span>Years Experience</span></div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="counter-item"><strong data-count="5000" data-suffix="+">0</strong><span>Happy Clients</span></div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="counter-item"><strong data-count="100" data-suffix="%">0</strong><span>Satisfaction</span></div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="counter-item"><strong data-count="4.9" data-suffix="">0</strong><span>Client Rating</span></div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- WHY CHOOSE --}}
<section class="section-pad bg-soft-lx">
    <div class="container">
        <div class="section-head" data-aos="fade-up">
            <span class="section-eyebrow">Why Choose Us</span>
            <h2 class="section-title">The Premium Difference</h2>
            <p class="section-sub">Everything you need for a refined grooming experience under one roof.</p>
        </div>
        <div class="row g-4">
            @php
            $features = [
                ['fa-certificate', 'Certified Barbers', 'Skilled professionals trained in classic and modern techniques.'],
                ['fa-gem', 'Luxury Experience', 'A calm, upscale environment designed for every client.'],
                ['fa-tags', 'Affordable Pricing', 'Transparent packages with premium value every visit.'],
                ['fa-pump-soap', 'Premium Products', 'High-end formulas that protect hair and skin.'],
                ['fa-laptop', 'Online Booking', 'Reserve your chair in seconds — anytime, anywhere.'],
                ['fa-face-smile', 'Customer Satisfaction', 'We stand behind every cut with guaranteed quality.'],
            ];
            @endphp
            @foreach($features as $i => $f)
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="{{ $i * 50 }}">
                <div class="lx-card lx-feature-card">
                    <div class="lx-icon-circle"><i class="fas {{ $f[0] }}"></i></div>
                    <h3>{{ $f[1] }}</h3>
                    <p>{{ $f[2] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- SERVICES PREVIEW --}}
<section class="section-pad bg-white-lx">
    <div class="container">
        <div class="section-head" data-aos="fade-up">
            <span class="section-eyebrow">Our Services</span>
            <h2 class="section-title">Signature Grooming Menu</h2>
            <p class="section-sub">From sharp fades to hot-towel shaves — tailored services for every style.</p>
        </div>
        <div class="row g-4">
            @forelse($signatureServices as $i => $s)
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="{{ $i * 40 }}">
                <div class="lx-card lx-service-card">
                    <div class="svc-img">
                        <img src="{{ $s->image_url }}" alt="{{ $s->service_name }}"
                            onerror="this.onerror=null;this.src='{{ asset('frontend/images/luxury/cut.jpg') }}';">
                    </div>
                    <div class="svc-body">
                        <h3 style="font-size:1.2rem;">{{ $s->service_name }}</h3>
                        <p style="font-size:.95rem;margin:0;">{{ $s->description ?: 'Premium grooming service tailored to you.' }}</p>
                        <div class="svc-meta">
                            <span><i class="far fa-clock me-1"></i>{{ $s->duration }} min</span>
                            <span class="svc-price">From {{ $shopCurrencySymbol ?? '₹' }}{{ number_format($s->price, 2) }}</span>
                        </div>
                        <a href="{{ route('book') }}" class="btn-lx btn-lx-primary w-100" target="_blank">Book Now</a>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <p class="text-center text-muted mb-0">Services will appear here once they are added from the admin panel.</p>
            </div>
            @endforelse
        </div>
        <div class="text-center mt-5" data-aos="fade-up">
            <a href="{{ route('services') }}" class="btn-lx btn-lx-outline-blue">View All Services</a>
        </div>
    </div>
</section>

{{-- TESTIMONIALS --}}
@if(isset($testimonials) && $testimonials->isNotEmpty())
<section class="section-pad bg-white-lx">
    <div class="container">
        <div class="section-head" data-aos="fade-up">
            <span class="section-eyebrow">Testimonials</span>
            <h2 class="section-title">What Clients Say</h2>
            <p class="section-sub">Real feedback from clients who trust our chairs.</p>
        </div>
        <div id="testimonialCarousel" class="carousel slide" data-bs-ride="carousel" data-aos="fade-left">
            <div class="carousel-inner">
                @foreach($testimonials as $i => $item)
                <div class="carousel-item {{ $i === 0 ? 'active' : '' }}">
                    <div class="row justify-content-center">
                        <div class="col-lg-8">
                            <div class="lx-card lx-testimonial">
                                <div class="stars">
                                    @for($s = 1; $s <= 5; $s++)
                                        @if($s <= (int) $item->rating)
                                            <i class="fas fa-star"></i>
                                        @else
                                            <i class="far fa-star"></i>
                                        @endif
                                    @endfor
                                </div>
                                <p class="quote">“{{ $item->review }}”</p>
                                <div class="person">
                                    <img src="{{ $item->image_url }}" alt="{{ $item->client_name }}"
                                        onerror="this.onerror=null;this.src='{{ asset('frontend/images/luxury/customer.jpg') }}';">
                                    <div>
                                        <strong>{{ $item->client_name }}</strong>
                                        <span>{{ $item->client_role ?: 'Regular Client' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @if($testimonials->count() > 1)
            <button class="carousel-control-prev" type="button" data-bs-target="#testimonialCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#testimonialCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
            @endif
        </div>
    </div>
</section>
@endif

{{-- CTA --}}
<section class="section-pad bg-white-lx">
    <div class="container">
        <div class="lx-cta" style="background-image:url('{{ asset('frontend/images/luxury/cta-bg.jpg') }}')" data-aos="zoom-in">
            <div class="cta-inner">
                <h2>Ready for Your Best Look Yet?</h2>
                <p>Book online in under a minute and enjoy a premium appointment tailored to you.</p>
                <a href="{{ route('book') }}" class="btn-lx btn-lx-outline" target="_blank">
                    <i class="far fa-calendar-check"></i> Book Appointment
                </a>
            </div>
        </div>
    </div>
</section>

@endsection
