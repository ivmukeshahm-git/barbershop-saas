@extends('saas.layout')

@section('title', 'Shop ready')
@section('meta_description', 'Your BarberPro shop admin is ready. Open your private dashboard URL.')

@section('content')
<div class="bp-setup bp-success-page">
    <div class="bp-container bp-setup-inner">
        @if (session('success'))
            {{-- flash already shown in layout; keep page clean --}}
        @endif

        <div class="bp-success-card">
            <div class="bp-success-icon" aria-hidden="true">
                <i class="bi bi-check-lg"></i>
            </div>
            <h1>Your shop admin is ready</h1>
            <p class="bp-success-lead">
                <strong>{{ $shop->name }}</strong> is active. Bookmark your custom admin URL — this is how you and your staff sign in.
            </p>

            <div class="bp-success-url">
                <span>Admin login URL</span>
                <code>{{ $shop->adminLoginUrl() }}</code>
            </div>

            <ul class="bp-success-meta">
                <li><span>Shop URL</span><strong>/{{ $shop->slug }}/admin</strong></li>
                @if ($subscription && $subscription->plan)
                    <li><span>Plan</span><strong>{{ $subscription->plan->name }} ({{ $subscription->billing_cycle }})</strong></li>
                    @if (!empty($subscription->meta['subtotal']))
                        <li><span>Subtotal</span><strong>₹{{ number_format((float) $subscription->meta['subtotal'], 2) }}</strong></li>
                        @if (($subscription->meta['discount'] ?? 0) > 0)
                            <li><span>Discount</span><strong>−₹{{ number_format((float) $subscription->meta['discount'], 2) }}</strong></li>
                        @endif
                        <li><span>GST ({{ $subscription->meta['gst_rate'] ?? 18 }}%)</span><strong>₹{{ number_format((float) ($subscription->meta['gst_amount'] ?? 0), 2) }}</strong></li>
                    @endif
                    <li><span>Total paid</span><strong>₹{{ number_format((float) $subscription->amount, 2) }} {{ $subscription->currency }}</strong></li>
                    <li><span>Valid until</span><strong>{{ optional($subscription->ends_at)->format('d M Y') ?? '—' }}</strong></li>
                @endif
                @if ($shop->owner)
                    <li><span>Owner login</span><strong>{{ $shop->owner->email }}</strong></li>
                @endif
            </ul>

            <div class="bp-success-actions">
                <a class="bp-btn bp-btn-primary bp-btn-lg" href="{{ $shop->adminHomeUrl() }}">
                    Open admin dashboard
                </a>
                <a class="bp-btn bp-btn-outline bp-btn-lg" href="{{ $shop->adminLoginUrl() }}">
                    Go to login page
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
