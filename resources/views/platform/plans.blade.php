@extends('platform.layout')

@section('title', 'Plans & pricing')

@section('content')
<div class="pa-page-head">
    <div><p class="pa-eyebrow">Catalog management</p><h1>Plans &amp; pricing</h1><p>Changes to active plans are reflected on the public pricing page and signup flow.</p></div>
    <a class="pa-button pa-button-light" href="{{ route('saas.pricing') }}" target="_blank" rel="noopener"><i class="bi bi-box-arrow-up-right"></i> View pricing page</a>
</div>
<div class="pa-plan-list">
    @foreach ($plans as $plan)
        <section class="pa-panel pa-plan-card">
            <div class="pa-plan-head">
                <div><h2>{{ $plan->name }} <span class="pa-status {{ $plan->is_active ? 'pa-status-active' : 'pa-status-suspended' }}">{{ $plan->is_active ? 'public' : 'hidden' }}</span></h2>
                    <p>{{ $plan->tagline ?: $plan->description }} · {{ number_format($plan->subscriptions_count) }} subscriptions</p>
                </div>
                @if ($plan->is_popular)<span class="pa-status pa-status-pending">Popular plan</span>@endif
            </div>
            <form class="pa-plan-form" method="POST" action="{{ route('platform.plans.update', $plan) }}">
                @csrf
                @method('PATCH')
                <div class="pa-field"><label for="monthly-{{ $plan->id }}">Monthly price</label><input id="monthly-{{ $plan->id }}" name="price_monthly" type="number" min="0" step="0.01" required value="{{ old('price_monthly', $plan->price_monthly) }}"></div>
                <div class="pa-field"><label for="yearly-{{ $plan->id }}">Yearly price</label><input id="yearly-{{ $plan->id }}" name="price_yearly" type="number" min="0" step="0.01" required value="{{ old('price_yearly', $plan->price_yearly) }}"></div>
                <div class="pa-field"><label for="staff-{{ $plan->id }}">Maximum staff</label><input id="staff-{{ $plan->id }}" name="max_staff" type="number" min="1" value="{{ old('max_staff', $plan->max_staff) }}" placeholder="Unlimited"></div>
                <div class="pa-field"><label for="appointments-{{ $plan->id }}">Appointments / month</label><input id="appointments-{{ $plan->id }}" name="max_appointments_month" type="number" min="1" value="{{ old('max_appointments_month', $plan->max_appointments_month) }}" placeholder="Unlimited"></div>
                <div class="pa-checks">
                    <label><input type="hidden" name="is_active" value="0"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $plan->is_active))> Show on public pricing</label>
                    <label><input type="hidden" name="is_popular" value="0"><input type="checkbox" name="is_popular" value="1" @checked(old('is_popular', $plan->is_popular))> Mark as popular</label>
                </div>
                <div class="pa-plan-actions"><button class="pa-button" type="submit"><i class="bi bi-check2"></i> Save {{ $plan->name }}</button></div>
            </form>
        </section>
    @endforeach
</div>
@endsection
