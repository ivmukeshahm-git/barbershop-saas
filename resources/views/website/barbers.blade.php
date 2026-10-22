@extends('layouts.master')

@section('title', 'Our Barbers | Rosenthal Barbershop')
@section('body_class', 'inner-page')

@section('content')
<section class="lx-page-banner" style="background-image:url('{{ asset('frontend/images/luxury/page-banner.jpg') }}')">
    <div class="banner-inner">
        <h1>Our Expert Team</h1>
        <ul class="lx-breadcrumb">
            <li><a href="{{ route('home') }}">Home</a></li>
            <li>/</li>
            <li>Barbers</li>
        </ul>
    </div>
</section>

<section class="section-pad bg-soft-lx">
    <div class="container">
        <div class="section-head" data-aos="fade-up">
            <span class="section-eyebrow">Meet the Team</span>
            <h2 class="section-title">Master Barbers</h2>
            <p class="section-sub">Skilled professionals dedicated to crafting your best look.</p>
        </div>

        <div class="row g-4 justify-content-center">
            @forelse($barebers as $i => $baraber)
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="{{ $i * 60 }}">
                <div class="lx-card lx-barber-card">
                    <div class="barber-img">
                        <img src="{{ $baraber->profile_img ? asset('storage/'.$baraber->profile_img) : asset('frontend/images/luxury/hero-barber.jpg') }}" alt="{{ $baraber->name }}">
                    </div>
                    <div class="barber-body">
                        <h3>{{ $baraber->name }}</h3>
                        <div class="role">Professional Barber</div>
                        @if ($baraber->expertise->isNotEmpty())
                            <p class="mt-2 mb-0" style="font-size:.92rem;">Services: {{ $baraber->expertise->implode(', ') }}</p>
                        @endif
                        <div class="lx-social">
                            <a href="{{ route('book') }}" target="_blank" aria-label="Book"><i class="far fa-calendar-check"></i></a>
                            <a href="{{ $siteSetting->instagram_url ?? 'https://www.instagram.com/bluecrownbarbers' }}" target="_blank" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                        </div>
                        <a href="{{ route('book') }}" class="btn-lx btn-lx-primary mt-3" target="_blank">Book Your Barber</a>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center">
                <p>Our team profiles will appear here soon.</p>
                <a href="{{ route('book') }}" class="btn-lx btn-lx-primary" target="_blank">Book Appointment</a>
            </div>
            @endforelse
        </div>
    </div>
</section>
@endsection
