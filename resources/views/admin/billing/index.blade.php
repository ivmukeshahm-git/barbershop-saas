@extends('admin.layout.layout')

@section('content')
<script>
    page_active_id = "Billing";
</script>
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <h4 class="mb-1">Plan &amp; billing</h4>
            <p class="mb-0 text-muted">Your purchased plan benefits apply across this shop admin panel.</p>
        </div>
        @if ($plan)
            <span class="badge {{ !empty($needsRenewal) ? 'bg-label-danger' : 'bg-label-success' }} rounded-pill px-3 py-2">
                {{ $plan->name }} plan{{ !empty($needsRenewal) ? ' · expired' : '' }}
            </span>
        @endif
    </div>

    @if (session('error'))
        <div class="alert alert-warning">{{ session('error') }}</div>
    @endif
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title mb-1">{{ $shop->name }}</h5>
                    <p class="text-muted mb-3"><code>/{{ $shop->slug }}/admin</code></p>

                    @if ($subscription && $plan)
                        <div class="row g-3 mb-4">
                            <div class="col-sm-6">
                                <div class="border rounded p-3 h-100">
                                    <div class="text-muted small">Billing</div>
                                    <div class="fw-semibold text-capitalize">{{ $subscription->billing_cycle }}</div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="border rounded p-3 h-100">
                                    <div class="text-muted small">Valid until</div>
                                    <div class="fw-semibold">{{ optional($subscription->ends_at)->format('d M Y') ?? '—' }}</div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="border rounded p-3 h-100">
                                    <div class="text-muted small">Amount paid</div>
                                    <div class="fw-semibold">{{ \App\Support\CountryCurrency::format((float) $subscription->amount, $subscription->currency ?: ($currencyCode ?? null)) }} <span class="text-muted small">{{ $subscription->currency ?: ($currencyCode ?? '') }}</span></div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="border rounded p-3 h-100">
                                    <div class="text-muted small">Status</div>
                                    @php
                                        $statusLabel = $needsRenewal ? 'expired' : $subscription->status;
                                        $statusClass = $needsRenewal ? 'text-danger' : 'text-success';
                                    @endphp
                                    <div class="fw-semibold {{ $statusClass }} text-capitalize">{{ $statusLabel }}</div>
                                </div>
                            </div>
                        </div>

                        @if (!empty($subscription->meta['gst_amount']))
                            @php $billSym = $currencySymbol ?? \App\Support\CountryCurrency::symbol($subscription->currency ?? null); @endphp
                            <div class="alert alert-light border">
                                <div class="d-flex justify-content-between"><span>Subtotal</span><strong>{{ $billSym }}{{ number_format((float) ($subscription->meta['subtotal'] ?? 0), 2) }}</strong></div>
                                @if (($subscription->meta['discount'] ?? 0) > 0)
                                    <div class="d-flex justify-content-between"><span>Discount</span><strong>−{{ $billSym }}{{ number_format((float) $subscription->meta['discount'], 2) }}</strong></div>
                                @endif
                                <div class="d-flex justify-content-between"><span>GST ({{ $subscription->meta['gst_rate'] ?? 18 }}%)</span><strong>{{ $billSym }}{{ number_format((float) $subscription->meta['gst_amount'], 2) }}</strong></div>
                                <div class="d-flex justify-content-between mt-1"><span>Total</span><strong>{{ $billSym }}{{ number_format((float) ($subscription->meta['total'] ?? $subscription->amount), 2) }}</strong></div>
                            </div>
                        @endif
                    @else
                        <div class="alert alert-warning mb-0">No subscription found. Renew below to continue on this same shop.</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">Plan benefits in admin</h5>
                    @if ($plan)
                        <p class="text-muted">{{ $plan->description }}</p>
                        <ul class="list-unstyled mb-4">
                            @foreach (($plan->features ?? []) as $feature)
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-1"></i> {{ $feature }}</li>
                            @endforeach
                        </ul>

                        <h6 class="text-uppercase text-muted small">Usage this cycle</h6>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span>Staff</span>
                                <strong>
                                    {{ $usage['staff_used'] }}
                                    /
                                    {{ $usage['staff_limit'] === null ? '∞' : $usage['staff_limit'] }}
                                </strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Appointments (this month)</span>
                                <strong>
                                    {{ $usage['appointments_used'] }}
                                    /
                                    {{ $usage['appointments_limit'] === null ? '∞' : $usage['appointments_limit'] }}
                                </strong>
                            </div>
                        </div>

                        <div class="small text-muted">
                            Staff reports: {{ ($usage['allows_staff_reports'] ?? false) ? 'Included' : 'Not in this plan' }}<br>
                            Advanced reports: {{ ($usage['allows_advanced_reports'] ?? false) ? 'Included' : 'Not in this plan' }}
                        </div>
                    @else
                        <p class="mb-0 text-muted">Purchase a plan to unlock admin benefits.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mt-1">
        <div class="col-12">
            <div class="card border-primary">
                <div class="card-body">
                    <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
                        <div>
                            <h5 class="card-title mb-1">{{ !empty($needsRenewal) ? 'Renew your plan' : 'Change / upgrade plan' }}</h5>
                            <p class="text-muted mb-0">
                                @if (!empty($needsRenewal))
                                    Your plan has expired. Renew the same plan or choose another — shop ID stays the same.
                                @else
                                    Your current plan stays locked until
                                    <strong>{{ optional($subscription->ends_at)->format('d M Y') }}</strong>.
                                    You can upgrade to any other plan now; renew the same plan only after it expires.
                                @endif
                            </p>
                        </div>
                    </div>

                    @if (($plans ?? collect())->isEmpty())
                        <div class="alert alert-danger mb-0">No plans available. Contact support.</div>
                    @else
                        @php
                            $currentPlanId = (!$needsRenewal && $plan) ? (int) $plan->id : null;
                            $upgradePlans = $plans->filter(function ($p) use ($currentPlanId, $freeUsed) {
                                if ($currentPlanId !== null && (int) $p->id === $currentPlanId) {
                                    return false;
                                }
                                $isFreePlan = $p->slug === 'free' || ((float) $p->price_monthly <= 0 && (float) $p->price_yearly <= 0);

                                return !($freeUsed && $isFreePlan);
                            });
                            $defaultPlanId = old('plan_id', optional($upgradePlans->first())->id ?? optional($plan)->id);
                        @endphp
                        <form method="POST" action="{{ route('admin.billing.renew') }}" class="row g-3" id="billingRenewForm">
                            @csrf
                            <div class="col-md-6">
                                <label class="form-label">Plan *</label>
                                <select name="plan_id" class="form-select" required id="billingPlanSelect">
                                    @foreach ($plans as $p)
                                        @php
                                            $isCurrent = $currentPlanId !== null && (int) $p->id === $currentPlanId;
                                        @endphp
                                        @php
                                            $isFreePlan = $p->slug === 'free' || ((float) $p->price_monthly <= 0 && (float) $p->price_yearly <= 0);
                                            $freeBlocked = !empty($freeUsed) && $isFreePlan;
                                        @endphp
                                        <option value="{{ $p->id }}"
                                            @selected((string) $defaultPlanId === (string) $p->id && !$freeBlocked)
                                            @disabled($isCurrent || $freeBlocked)
                                            data-monthly="{{ (float) $p->price_monthly }}"
                                            data-yearly="{{ (float) $p->price_yearly }}"
                                            data-current="{{ $isCurrent ? '1' : '0' }}">
                                            {{ $p->name }} — {{ $currencySymbol }}{{ number_format((float) $p->price_monthly, 0) }}/mo
                                            · {{ $currencySymbol }}{{ number_format((float) $p->price_yearly, 0) }}/yr
                                            @if ($isCurrent)
                                                (current — renew after {{ optional($subscription->ends_at)->format('d M Y') }})
                                            @elseif ($freeBlocked)
                                                (already used — free is once only)
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('plan_id')<div class="text-danger small">{{ $message }}</div>@enderror
                                @if ($currentPlanId && $upgradePlans->isEmpty())
                                    <div class="text-muted small mt-1">No other plans available to upgrade to right now.</div>
                                @endif
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Billing cycle *</label>
                                <select name="billing_cycle" class="form-select" required>
                                    <option value="monthly" @selected(old('billing_cycle', optional($subscription)->billing_cycle) === 'monthly')>Monthly</option>
                                    <option value="yearly" @selected(old('billing_cycle', optional($subscription)->billing_cycle) === 'yearly')>Yearly</option>
                                </select>
                                @error('billing_cycle')<div class="text-danger small">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Payment *</label>
                                <select name="payment_method" class="form-select" required>
                                    @foreach (['manual' => 'Manual / cash', 'upi' => 'UPI', 'card' => 'Card', 'netbanking' => 'Net banking'] as $value => $label)
                                        <option value="{{ $value }}" @selected(old('payment_method', 'manual') === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('payment_method')<div class="text-danger small">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <p class="small text-muted mb-2">
                                    GST {{ number_format($gstRate ?? 18, 0) }}% is added at checkout.
                                    @if (!empty($needsRenewal))
                                        Renewing starts a new billing period from today.
                                    @else
                                        Upgrading applies the new plan immediately from today.
                                    @endif
                                </p>
                                <button type="submit" class="btn btn-primary" id="billingRenewBtn"
                                    @disabled($currentPlanId && $upgradePlans->isEmpty())>
                                    <i class="bi bi-arrow-up-circle me-1"></i>
                                    <span id="billingRenewBtnLabel">
                                        {{ !empty($needsRenewal) ? 'Renew plan now' : 'Upgrade plan' }}
                                    </span>
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mt-1">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-1">Shop currency</h5>
                    <p class="text-muted mb-3">
                        Used across the admin panel for invoices, dashboard, and reports.
                        Current: <strong>{{ $currencySymbol }}</strong> ({{ $currencyCode }})
                    </p>
                    <form method="POST" action="{{ route('admin.billing.currency') }}" class="row g-3 align-items-end">
                        @csrf
                        <div class="col-md-6 col-lg-5">
                            <label class="form-label" for="shop_currency">Currency *</label>
                            <select name="currency" id="shop_currency" class="form-select" required>
                                @foreach (($currencies ?? []) as $code => $label)
                                    <option value="{{ $code }}" @selected(old('currency', $currencyCode) === $code)>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('currency')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-currency-exchange me-1"></i> Save currency
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mt-1">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-1">Shop branding</h5>
                    <p class="text-muted mb-3">Theme color and logo apply across the whole admin panel (sidebar, buttons, login, invoices).</p>

                    <form method="POST" action="{{ route('admin.billing.branding') }}" enctype="multipart/form-data" class="row g-3 align-items-start">
                        @csrf
                        <div class="col-md-3 text-center">
                            <img src="{{ $shop->logoUrl() }}" alt="{{ $shop->name }}" class="border rounded p-2 mb-2" style="width:96px;height:96px;object-fit:contain;background:#fff">
                            <div class="form-check justify-content-center d-flex gap-2">
                                <input class="form-check-input" type="checkbox" name="remove_logo" value="1" id="remove_logo">
                                <label class="form-check-label" for="remove_logo">Remove logo</label>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label">Theme color</label>
                            <div class="d-flex flex-wrap gap-2 mb-2">
                                @foreach (\App\Models\SaasShop::THEME_PRESETS as $hex => $label)
                                    <label class="border rounded px-2 py-1 d-inline-flex align-items-center gap-2" style="cursor:pointer">
                                        <input type="radio" name="theme_color" value="{{ $hex }}" {{ strtoupper($shop->themeColor()) === $hex ? 'checked' : '' }} required>
                                        <span style="width:16px;height:16px;border-radius:50%;background:{{ $hex }};display:inline-block"></span>
                                        <small>{{ $label }}</small>
                                    </label>
                                @endforeach
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <input type="color" id="billing_theme_custom" value="{{ $shop->themeColor() }}" class="form-control form-control-color">
                                <small class="text-muted">Or pick a custom color</small>
                            </div>
                            @error('theme_color')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="shop_logo">Upload logo</label>
                            <input type="file" name="shop_logo" id="shop_logo" class="form-control" accept="image/png,image/jpeg,image/jpg,image/webp,image/gif">
                            @error('shop_logo')<div class="text-danger small">{{ $message }}</div>@enderror
                            <button type="submit" class="btn btn-primary mt-3">Save branding</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
document.getElementById('billing_theme_custom')?.addEventListener('input', function (e) {
    var hex = e.target.value.toUpperCase();
    var matched = false;
    document.querySelectorAll('input[name="theme_color"]').forEach(function (radio) {
        if (radio.value.toUpperCase() === hex) {
            radio.checked = true;
            matched = true;
        }
    });
    if (!matched) {
        var custom = document.querySelector('input[name="theme_color"][data-custom="1"]');
        if (!custom) {
            custom = document.createElement('input');
            custom.type = 'radio';
            custom.name = 'theme_color';
            custom.setAttribute('data-custom', '1');
            custom.classList.add('d-none');
            e.target.closest('form').appendChild(custom);
        }
        custom.value = hex;
        custom.checked = true;
    }
});
</script>
@endpush
