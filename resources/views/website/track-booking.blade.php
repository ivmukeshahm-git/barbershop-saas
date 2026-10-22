@extends('layouts.master')

@section('title', ($shopName ?? 'Misha Hair Studio Mumbai') . ' | Track Your Booking')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="{{ asset('frontend/css/track-booking.css') }}?v=2" rel="stylesheet">

<div class="tb-page">
    <section class="tb-hero text-center">
        <div class="container">
            <p class="text-uppercase fw-semibold mb-2" style="letter-spacing:.08em;color:#136207;font-size:.8rem;">
                {{ $shopName ?? 'Misha Hair Studio Mumbai' }}
            </p>
            <h1>Track Your Booking</h1>
            <p class="lead">Enter your Booking Code to view your appointment status and details in real time.</p>
        </div>
    </section>

    <section class="tb-section">
        <div class="container">
            <div class="tb-search-card">
                @if (session('track_error'))
                    <div class="tb-alert mb-3" role="alert">
                        <i class="bi bi-exclamation-circle me-1"></i> {{ session('track_error') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="tb-alert mb-3" role="alert">
                        <i class="bi bi-exclamation-circle me-1"></i> {{ $errors->first() }}
                    </div>
                @endif

                <form id="trackBookingForm" action="{{ route('track-booking.search') }}" method="POST" novalidate>
                    @csrf
                    <label for="booking_code">Booking Code</label>
                    <div class="row g-2 align-items-stretch mt-1">
                        <div class="col-md-8">
                            <input type="text" class="form-control" id="booking_code" name="booking_code"
                                value="{{ old('booking_code') }}"
                                placeholder="e.g. {{ $bookingCodePrefix ?? 'MHS' }}-..." autocomplete="off" required>
                        </div>
                        <div class="col-md-4 d-grid">
                            <button type="submit" class="tb-btn-primary" id="trackSubmitBtn">
                                <span class="btn-label"><i class="bi bi-search me-1"></i> Track Booking</span>
                                <span class="btn-loading d-none">
                                    <span class="spinner-border spinner-border-sm me-1" role="status"></span>
                                    Searching...
                                </span>
                            </button>
                        </div>
                    </div>
                    <p class="text-muted small mt-3 mb-0">
                        Your Booking Code was shared on the confirmation screen and in your confirmation email.
                    </p>
                </form>
            </div>

            <div class="row g-4 justify-content-center">
                <div class="col-lg-4">
                    <div class="tb-card p-4 h-100 text-center">
                        <div class="mb-3" style="font-size:1.8rem;color:#136207;"><i class="bi bi-ticket-perforated"></i></div>
                        <h5 class="fw-bold">Have a Booking Code?</h5>
                        <p class="text-muted mb-0 small">Enter it above to see live status, barber details, and appointment time.</p>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="tb-card p-4 h-100 text-center">
                        <div class="mb-3" style="font-size:1.8rem;color:#136207;"><i class="bi bi-shield-check"></i></div>
                        <h5 class="fw-bold">Private & Secure</h5>
                        <p class="text-muted mb-0 small">Only the exact Booking Code can open your appointment. No login required.</p>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="tb-card p-4 h-100 text-center">
                        <div class="mb-3" style="font-size:1.8rem;color:#136207;"><i class="bi bi-calendar2-plus"></i></div>
                        <h5 class="fw-bold">Need a New Cut?</h5>
                        <p class="text-muted mb-3 small">Book your next appointment in minutes.</p>
                        <a href="{{ route('book') }}" class="tb-btn-outline">Book Appointment</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
(function () {
    var form = document.getElementById('trackBookingForm');
    if (!form) return;
    form.addEventListener('submit', function () {
        var btn = document.getElementById('trackSubmitBtn');
        if (!btn) return;
        btn.disabled = true;
        var label = btn.querySelector('.btn-label');
        var loading = btn.querySelector('.btn-loading');
        if (label) label.classList.add('d-none');
        if (loading) loading.classList.remove('d-none');
    });
})();
</script>
@endsection
