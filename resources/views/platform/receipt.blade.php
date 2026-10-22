<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Receipt {{ $subscription->payment_reference ?: ('SUB-' . $subscription->id) }} | BarberPro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('frontend/css/super-admin.css') }}?v=2" rel="stylesheet">
    <style>
        body{padding:30px}.pa-receipt{max-width:760px;margin:30px auto;padding:42px;background:#fff;border:1px solid var(--pa-line);border-radius:14px;box-shadow:var(--pa-shadow)}.pa-receipt-top{display:flex;justify-content:space-between;gap:20px;border-bottom:1px solid var(--pa-line);padding-bottom:24px}.pa-receipt h1{font:800 25px Manrope,sans-serif;margin:4px 0}.pa-receipt p{color:var(--pa-muted);margin:4px 0}.pa-receipt dl{display:grid;grid-template-columns:1fr auto;gap:13px;margin:27px 0}.pa-receipt dt{color:var(--pa-muted)}.pa-receipt dd{margin:0;text-align:right;font-weight:700}.pa-receipt-total{padding-top:16px;border-top:1px solid var(--pa-line);font:800 20px Manrope,sans-serif}.pa-print{display:flex;justify-content:flex-end;margin:0 auto 12px;max-width:760px}@media print{body{padding:0;background:#fff}.pa-receipt{margin:0 auto;box-shadow:none;border:0}.pa-print{display:none}}
    </style>
</head>
<body class="pa-body">
    <div class="pa-print"><button class="pa-button" type="button" onclick="window.print()"><i class="bi bi-printer"></i> Print receipt</button></div>
    <main class="pa-receipt">
        <div class="pa-receipt-top">
            <div><p class="pa-eyebrow">BarberPro · Subscription payment</p><h1>Payment receipt</h1><p>Payment successfully recorded.</p></div>
            <div style="text-align:right"><strong>Receipt</strong><p>{{ $subscription->payment_reference ?: 'SUB-' . $subscription->id }}</p></div>
        </div>
        <dl>
            <dt>Shop</dt><dd>{{ $subscription->shop->name ?? 'Shop removed' }}</dd>
            <dt>Shop email</dt><dd>{{ $subscription->shop->email ?? '—' }}</dd>
            <dt>Plan</dt><dd>{{ $subscription->plan->name ?? 'Plan removed' }}</dd>
            <dt>Billing cycle</dt><dd>{{ ucfirst($subscription->billing_cycle) }}</dd>
            <dt>Payment method</dt><dd>{{ $subscription->payment_method ? ucfirst($subscription->payment_method) : '—' }}</dd>
            <dt>Payment date</dt><dd>{{ $subscription->paid_at->format('M j, Y') }}</dd>
            <dt>Subscription period</dt><dd>{{ optional($subscription->starts_at)->format('M j, Y') ?: '—' }} – {{ optional($subscription->ends_at)->format('M j, Y') ?: 'No expiry' }}</dd>
            <dt class="pa-receipt-total">Total paid</dt><dd class="pa-receipt-total">{{ $subscription->currency ?: 'INR' }} {{ number_format((float) $subscription->amount, 2) }}</dd>
        </dl>
        <p>Thank you. This receipt reflects the subscription payment information recorded by the platform.</p>
    </main>
</body>
</html>
