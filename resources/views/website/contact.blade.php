@extends('layouts.master')



@section('title', 'Contact | Rosenthal Barbershop')

@section('body_class', 'inner-page')



@section('content')

@php

    $cAddress = $siteSetting->contact_address ?? '245 King Street, Downtown, Toronto, Ontario, Canada';

    $cPhone = $siteSetting->contact_phone ?? '+1 (587) 555-2489';

    $cPhoneTel = optional($siteSetting)->phoneTel() ?: '+15875552489';

    $cEmailInfo = $siteSetting->contact_email ?? 'info@bluecrownbarbers.com';

    $cEmailSupport = $siteSetting->support_email ?? 'support@bluecrownbarbers.com';

    $cWhatsApp = optional($siteSetting)->whatsappUrl() ?: 'https://wa.me/15875552489';

    $cMapQuery = optional($siteSetting)->mapQuery() ?: rawurlencode('245 King Street, Toronto, Ontario, Canada');

@endphp

<section class="lx-page-banner" style="background-image:url('{{ asset('frontend/images/luxury/page-banner.jpg') }}')">

    <div class="banner-inner">

        <h1>Contact Us</h1>

        <ul class="lx-breadcrumb">

            <li><a href="{{ route('home') }}">Home</a></li>

            <li>/</li>

            <li>Contact</li>

        </ul>

    </div>

</section>



<section class="section-pad bg-soft-lx">

    <div class="container">

        <div class="section-head" data-aos="fade-up">
            <span class="section-eyebrow">Get in Touch</span>
            <h2 class="section-title">Visit Rosenthal Barbershop</h2>
            <p class="section-sub">Questions, bookings, or directions — we’re here to help.</p>
        </div>



        <div class="row g-4 mb-5">

            <div class="col-md-4" data-aos="fade-up">

                <div class="lx-card lx-info-card">

                    <div class="ic"><i class="fas fa-map-marker-alt"></i></div>

                    <h3 style="font-size:1.15rem;">Visit Us</h3>

                    <p>{!! nl2br(e($cAddress)) !!}</p>

                    <a href="https://www.google.com/maps?q={{ $cMapQuery }}" target="_blank" class="btn-lx btn-lx-outline-blue mt-2">Get Directions</a>

                </div>

            </div>

            <div class="col-md-4" data-aos="fade-up" data-aos-delay="60">

                <div class="lx-card lx-info-card">

                    <div class="ic"><i class="far fa-clock"></i></div>

                    <h3 style="font-size:1.15rem;">Business Hours</h3>

                    <p class="mb-1" style="font-size:.92rem;"><strong style="color:var(--lx-navy)">Monday–Friday:</strong> 9:00 AM – 8:00 PM</p>

                    <p class="mb-1" style="font-size:.92rem;"><strong style="color:var(--lx-navy)">Saturday:</strong> 9:00 AM – 7:00 PM</p>

                    <p class="mb-1" style="font-size:.92rem;"><strong style="color:var(--lx-navy)">Sunday:</strong> 10:00 AM – 5:00 PM</p>

                    <a href="{{ route('book') }}" class="btn-lx btn-lx-primary mt-3" target="_blank">Book Now</a>

                </div>

            </div>

            <div class="col-md-4" data-aos="fade-up" data-aos-delay="120">

                <div class="lx-card lx-info-card">

                    <div class="ic"><i class="fas fa-phone"></i></div>

                    <h3 style="font-size:1.15rem;">Call & Email</h3>

                    <p>

                        Phone: <a href="tel:{{ $cPhoneTel }}">{{ $cPhone }}</a><br>

                        Info: <a href="mailto:{{ $cEmailInfo }}">{{ $cEmailInfo }}</a><br>

                        @if($cEmailSupport)

                            Support: <a href="mailto:{{ $cEmailSupport }}">{{ $cEmailSupport }}</a>

                        @endif

                    </p>

                    <div class="d-flex flex-wrap gap-2 mt-2">

                        <a href="tel:{{ $cPhoneTel }}" class="btn-lx btn-lx-outline-blue">Call Now</a>

                        <a href="{{ $cWhatsApp }}" target="_blank" class="btn-lx btn-lx-primary"><i class="fab fa-whatsapp"></i> WhatsApp</a>

                    </div>

                </div>

            </div>

        </div>



        <div class="row g-4 align-items-stretch">

            <div class="col-lg-7" data-aos="fade-right">

                <div class="lx-card p-4 p-md-5 lx-form h-100">

                    <h3 class="mb-1">Send a Message</h3>

                    <p class="mb-4">We’ll get back to you as soon as possible.</p>

                    <form id="contactForm" method="POST" action="{{ route('contact.send') }}">
                        @csrf
                        <div class="d-none" aria-hidden="true">
                            <label for="cWebsite">Leave this field empty</label>
                            <input id="cWebsite" name="website" type="text" tabindex="-1" autocomplete="off">
                        </div>
                        <p id="contactFormMessage" class="mb-3" role="status" aria-live="polite"></p>

                        <div class="row g-3">

                            <div class="col-md-6">

                                <label for="cName">Full Name</label>

                                <input id="cName" name="name" type="text" class="form-control" maxlength="120" required placeholder="Your name">

                            </div>

                            <div class="col-md-6">

                                <label for="cEmail">Email</label>

                                <input id="cEmail" name="email" type="email" class="form-control" maxlength="150" required placeholder="you@email.com">

                            </div>

                            <div class="col-md-6">

                                <label for="cPhone">Phone</label>

                                <input id="cPhone" name="phone" type="tel" class="form-control" maxlength="50" placeholder="Optional">

                            </div>

                            <div class="col-md-6">

                                <label for="cSubject">Subject</label>

                                <input id="cSubject" name="subject" type="text" class="form-control" maxlength="180" required placeholder="How can we help?">

                            </div>

                            <div class="col-12">

                                <label for="cMessage">Message</label>

                                <textarea id="cMessage" name="message" class="form-control" rows="5" maxlength="5000" required placeholder="Write your message..."></textarea>

                            </div>

                            <div class="col-12">

                                <button type="submit" class="btn-lx btn-lx-primary">Submit Message</button>

                            </div>

                        </div>

                    </form>

                </div>

            </div>

            <div class="col-lg-5" data-aos="fade-left">

                <div class="lx-map h-100">

                    <iframe

                        src="https://www.google.com/maps?q={{ $cMapQuery }}&output=embed"

                        allowfullscreen=""

                        loading="lazy"

                        referrerpolicy="no-referrer-when-downgrade"

                        title="Rosenthal Barbershop location map"></iframe>

                </div>

            </div>

        </div>

    </div>

</section>

@endsection

@push('scripts')
<script>
    document.getElementById('contactForm').addEventListener('submit', async function (event) {
        event.preventDefault();
        const form = this;
        const button = form.querySelector('button[type="submit"]');
        const status = document.getElementById('contactFormMessage');
        button.disabled = true;
        status.textContent = 'Sending…';

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: new FormData(form)
            });
            const result = await response.json();
            if (!response.ok) {
                const message = result.message || Object.values(result.errors || {}).flat()[0] || 'Unable to send your message.';
                throw new Error(message);
            }

            status.textContent = result.message;
            status.className = 'mb-3 text-success';
            form.reset();
        } catch (error) {
            status.textContent = error.message || 'Unable to send your message. Please try again.';
            status.className = 'mb-3 text-danger';
        } finally {
            button.disabled = false;
        }
    });
</script>
@endpush
