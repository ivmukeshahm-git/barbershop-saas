@extends('saas.layout')

@section('title', 'Pricing')
@section('meta_description', 'Choose a BarberPro plan for your barber shop — appointments, POS, staff and reports.')

@section('content')
<section class="bp-page bp-page-light">
    <div class="bp-container">
        <header class="bp-section-head" data-reveal>
            <p class="bp-eyebrow">Pricing</p>
            <h2>Simple plans for every shop size.</h2>
            <p class="bp-lead">Create your shop, pick a plan, and get a private admin URL like <strong>yoursite.com/your-shop/admin</strong>.</p>
        </header>

        <div class="bp-billing-toggle" role="group" aria-label="Billing cycle" data-reveal>
            <button type="button" class="is-active" data-billing="monthly">Monthly</button>
            <button type="button" data-billing="yearly">Yearly <em>Save ~17%</em></button>
        </div>

        @if ($plans->isEmpty())
            <div class="bp-alert bp-alert-danger">No plans are available yet. Please contact support.</div>
        @else
            <div class="bp-pricing-grid" data-reveal>
                @foreach ($plans as $plan)
                    @php
                        $displayName = $plan->slug === 'growth' ? 'Professional' : $plan->name;
                        $subtitle = match ($plan->slug) {
                            'starter' => 'For independent barbers.',
                            'growth' => 'For growing barber shops.',
                            'business' => 'For larger operations.',
                            default => $plan->tagline,
                        };
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
                        <p class="bp-price-sub">{{ $plan->description }}</p>
                        <ul>
                            @foreach (($plan->features ?? []) as $feature)
                                <li><i class="bi bi-check2"></i> {{ $feature }}</li>
                            @endforeach
                        </ul>
                        <a class="bp-btn {{ $plan->is_popular ? 'bp-btn-primary' : 'bp-btn-outline' }} js-cta"
                           href="{{ route('saas.signup', ['plan' => $plan->slug, 'billing' => 'monthly']) }}"
                           data-plan="{{ $plan->slug }}">
                            Choose {{ $displayName }}
                        </a>
                    </article>
                @endforeach
            </div>
        @endif
    </div>
</section>
@endsection

@push('scripts')
<script>
window.BP_PRICING = { signupBase: @json(url('/get-started')) };
</script>
@endpush
