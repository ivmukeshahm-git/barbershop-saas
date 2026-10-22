@extends('platform.layout')

@section('title', 'Overview')

@section('content')
<div class="pa-page-head">
    <div>
        <p class="pa-eyebrow">Your platform at a glance</p>
        <h1>Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 18 ? 'afternoon' : 'evening') }}, {{ auth('platform')->user()->name }}</h1>
        <p>Here’s what’s happening across your barber shop network.</p>
    </div>
    <a class="pa-button pa-button-light" href="{{ route('platform.shops') }}"><i class="bi bi-shop"></i> Manage shops</a>
</div>

<div class="pa-stats">
    <article class="pa-stat">
        <div class="pa-stat-top"><span>Total shop accounts</span><span class="pa-stat-icon"><i class="bi bi-shop"></i></span></div>
        <div class="pa-stat-value">{{ number_format($stats['shops']) }}</div>
        <div class="pa-stat-note">{{ number_format($stats['active_shops']) }} currently active</div>
    </article>
    <article class="pa-stat">
        <div class="pa-stat-top"><span>Pending activation</span><span class="pa-stat-icon"><i class="bi bi-hourglass-split"></i></span></div>
        <div class="pa-stat-value">{{ number_format($stats['pending_shops']) }}</div>
        <div class="pa-stat-note">Shops awaiting review</div>
    </article>
    <article class="pa-stat">
        <div class="pa-stat-top"><span>Active subscriptions</span><span class="pa-stat-icon"><i class="bi bi-receipt"></i></span></div>
        <div class="pa-stat-value">{{ number_format($stats['subscriptions']) }}</div>
        <div class="pa-stat-note">Not expired as of today</div>
    </article>
    <article class="pa-stat">
        <div class="pa-stat-top"><span>Monthly recurring revenue</span><span class="pa-stat-icon"><i class="bi bi-graph-up-arrow"></i></span></div>
        <div class="pa-stat-value" style="font-size:19px">
            @forelse ($stats['monthly_revenue'] as $currency => $amount)
                {{ $currency }} {{ number_format($amount, 0) }}{{ !$loop->last ? ' · ' : '' }}
            @empty
                —
            @endforelse
        </div>
        <div class="pa-stat-note">Active plan amounts, yearly normalized monthly</div>
    </article>
</div>

<div class="pa-grid-2">
    <section class="pa-panel">
        <div class="pa-panel-head"><h2>Recently created shops</h2><a href="{{ route('platform.shops') }}">All shops <i class="bi bi-arrow-right"></i></a></div>
        <div class="pa-table-wrap">
            <table class="pa-table">
                <thead><tr><th>Shop</th><th>Plan</th><th>Status</th></tr></thead>
                <tbody>
                @forelse ($recentShops as $shop)
                    <tr>
                        <td><span class="pa-primary-cell">{{ $shop->name }}</span><span class="pa-sub-cell">{{ $shop->owner->email ?? $shop->email ?? 'No owner email' }}</span></td>
                        <td>{{ $shop->activeSubscription->plan->name ?? 'No active plan' }}</td>
                        <td><span class="pa-status pa-status-{{ $shop->status }}">{{ $shop->status }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="3">No shops have signed up yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </section>
    <section class="pa-panel">
        <div class="pa-panel-head"><h2>Latest recorded payments</h2><a href="{{ route('platform.payments') }}">All payments <i class="bi bi-arrow-right"></i></a></div>
        <div class="pa-table-wrap">
            <table class="pa-table">
                <thead><tr><th>Shop</th><th>Amount</th><th>Date</th></tr></thead>
                <tbody>
                @forelse ($recentPayments as $payment)
                    <tr>
                        <td><span class="pa-primary-cell">{{ $payment->shop->name ?? 'Shop removed' }}</span><span class="pa-sub-cell">{{ $payment->plan->name ?? 'Plan removed' }}</span></td>
                        <td>{{ $payment->currency }} {{ number_format((float) $payment->amount, 2) }}</td>
                        <td>{{ $payment->paid_at->format('M j, Y') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3">No paid subscriptions have been recorded yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="pa-panel-head" style="border-top:1px solid var(--pa-line);border-bottom:0">
            <span class="pa-sub-cell" style="margin:0">Recorded payment totals</span>
            <strong>
                @forelse ($stats['paid_totals'] as $currency => $amount)
                    {{ $currency }} {{ number_format($amount, 0) }}{{ !$loop->last ? ' · ' : '' }}
                @empty
                    —
                @endforelse
            </strong>
        </div>
    </section>
</div>
@endsection
