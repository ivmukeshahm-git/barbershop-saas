@extends('layouts.master')

@section('title', 'Services | Rosenthal Barbershop')
@section('body_class', 'inner-page')

@section('content')
<section class="lx-page-banner" style="background-image:url('{{ asset('frontend/images/luxury/page-banner.jpg') }}')">
    <div class="banner-inner">
        <h1>Our Services</h1>
        <ul class="lx-breadcrumb">
            <li><a href="{{ route('home') }}">Home</a></li>
            <li>/</li>
            <li>Services</li>
        </ul>
    </div>
</section>

<section class="section-pad bg-soft-lx">
    <div class="container">
        <div class="section-head" data-aos="fade-up">
            <span class="section-eyebrow">Grooming Menu</span>
            <h2 class="section-title">Premium Services</h2>
            <p class="section-sub">Explore our full menu of expert services. Book online in minutes.</p>
        </div>

        @forelse($servicesCategory as $index => $category)
            <div class="lx-svc-block {{ $index % 2 != 0 ? 'reverse' : '' }}" data-aos="fade-up">
                <div class="svc-media">
                    <img src="{{ $category->service_image ? asset('storage/'.$category->service_image) : asset('frontend/images/luxury/cut.jpg') }}" alt="{{ $category->category_name }}">
                </div>
                <div class="svc-panel">
                    <h3><i class="fas fa-scissors"></i> {{ $category->category_name }}</h3>
                    <div class="service-items-scroll">
                        @foreach($category->services as $service)
                            <div class="lx-svc-item">
                                <div>
                                    <strong>{{ html_entity_decode($service->service_name, ENT_QUOTES, 'UTF-8') }}</strong>
                                    <div class="meta"><i class="far fa-clock me-1"></i>{{ $service->duration }} min</div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <span class="price">{{ $shopCurrencySymbol ?? '₹' }}{{ number_format((float) $service->price, 2) }}</span>
                                    <button type="button" class="book-mini" onclick="window.location.href='{{ route('book') }}'" aria-label="Book">
                                        <i class="fas fa-arrow-right"></i>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @empty
            <div class="alert alert-light text-center">Services will be listed here once they are added by the shop.</div>
        @endforelse
        @if(!empty($defaultGst) && $servicesCategory->isNotEmpty())
            <div class="gst-price-note {{ $defaultGst->isTaxExcluded() ? 'gst-price-note--extra' : 'gst-price-note--include' }}">
                <i class="fas {{ $defaultGst->isTaxExcluded() ? 'fa-exclamation-circle' : 'fa-check-circle' }}"></i>
                <span>{{ $defaultGst->websiteNote() }}</span>
            </div>
        @endif
    </div>
</section>
@endsection
