<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Plan expired | {{ $shop->name }}</title>
    <link rel="icon" type="image/png" href="{{ asset('frontend/images/brand/logo-icon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('frontend/css/saas.css') }}?v=8" rel="stylesheet">
    <link href="{{ asset('frontend/css/saas-marketing.css') }}?v=7" rel="stylesheet">
    <link href="{{ asset('frontend/css/saas-landing.css') }}?v=8" rel="stylesheet">
    <style>
        .ex-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 1rem 1.25rem;
            background: #fff;
            border-bottom: 1px solid rgba(28, 20, 15, 0.08);
        }
        .ex-bar strong { font-family: var(--bp-display, Manrope, sans-serif); }
        .ex-logout {
            border: 0;
            background: transparent;
            color: #6b5c50;
            font-weight: 600;
            cursor: pointer;
        }
        .ex-note { margin: 0.4rem 0 0; color: #9a6232; font-weight: 600; }
        .bp-price-card.is-used { opacity: 0.72; }
        .bp-price-card button.bp-btn { width: 100%; margin-top: auto; cursor: pointer; }
        .bp-price-card button.bp-btn:disabled { cursor: not-allowed; opacity: 0.65; }
        .ex-error { margin: 0 auto 1rem; max-width: 40rem; text-align: center; }
        .ex-modal {
            position: fixed;
            inset: 0;
            z-index: 80;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 1.25rem;
            background: rgba(28, 20, 15, 0.48);
        }
        .ex-modal.is-open { display: flex; }
        .ex-modal-card {
            width: min(100%, 28rem);
            background: #fff;
            border-radius: 1.25rem;
            padding: 1.35rem 1.35rem 1.2rem;
            box-shadow: 0 24px 60px rgba(28, 20, 15, 0.22);
        }
        .ex-modal-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 0.35rem;
        }
        .ex-modal-head h3 {
            margin: 0;
            font-family: var(--bp-display, Manrope, sans-serif);
            font-size: 1.35rem;
        }
        .ex-modal-close {
            border: 0;
            background: transparent;
            color: #6b5c50;
            font-size: 1.35rem;
            line-height: 1;
            cursor: pointer;
            padding: 0.1rem 0.25rem;
        }
        .ex-modal-sub { margin: 0 0 1rem; color: #6b5c50; }
        .ex-calc { margin: 0; }
        .ex-calc div {
            display: flex;
            justify-content: space-between;
            gap: 1rem;
            padding: 0.55rem 0;
            border-bottom: 1px solid rgba(28, 20, 15, 0.08);
        }
        .ex-calc div span { color: #6b5c50; }
        .ex-calc div strong { color: #1c140f; }
        .ex-calc .is-discount strong { color: #2f6b3a; }
        .ex-calc .is-total {
            border-bottom: 0;
            padding-top: 0.75rem;
            font-size: 1.05rem;
        }
        .ex-modal-actions {
            display: flex;
            gap: 0.6rem;
            margin-top: 1.1rem;
        }
        .ex-modal-actions .bp-btn { flex: 1; cursor: pointer; }
        .ex-modal-actions .bp-btn:disabled { cursor: wait; opacity: 0.7; }
        .lp-page .bp-pricing-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        @media (max-width: 1100px) {
            .lp-page .bp-pricing-grid { grid-template-columns: 1fr 1fr; }
        }
        @media (max-width: 700px) {
            .lp-page .bp-pricing-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body class="bp-body lp-page">
    <header class="ex-bar">
        <strong>{{ $shop->name }}</strong>
        <form method="POST" action="{{ route('logout', ['shop' => $shop->slug]) }}">
            @csrf
            <button type="submit" class="ex-logout">Log out</button>
        </form>
    </header>

    <main>
        <section class="bp-section bp-section-light">
            <div class="bp-container">
                <div class="bp-section-head">
                    <h2>Your plan has expired.</h2>
                    <p class="bp-lead">The admin is locked until you pick a plan. Your shop stays <strong>{{ $shop->name }}</strong>.</p>
                </div>

                @if (session('error'))
                    <div class="bp-alert bp-alert-danger ex-error">{{ session('error') }}</div>
                @endif
                @if ($errors->any())
                    <div class="bp-alert bp-alert-danger ex-error">{{ $errors->first() }}</div>
                @endif

                <div class="bp-billing-toggle" role="group" aria-label="Billing cycle" id="exBilling">
                    <button type="button" class="is-active" data-billing="monthly">Monthly</button>
                    <button type="button" data-billing="yearly">Yearly <em>Save ~17%</em></button>
                </div>

                <form method="POST" action="{{ route('admin.billing.renew', ['shop' => $shop->slug]) }}" id="exRenewForm">
                    @csrf
                    <input type="hidden" name="plan_id" id="exPlanId" value="{{ old('plan_id') }}">
                    <input type="hidden" name="billing_cycle" id="exCycle" value="{{ old('billing_cycle', 'monthly') }}">
                    <input type="hidden" name="payment_method" id="exPayInput" value="manual">
                </form>

                <div class="bp-pricing-grid">
                    @foreach ($plans as $plan)
                        @php
                            $isFreePlan = $plan->slug === 'free' || ((float) $plan->price_monthly <= 0 && (float) $plan->price_yearly <= 0);
                        @endphp
                        @continue($isFreePlan)
                        @php
                            $displayName = $plan->slug === 'growth' ? 'Professional' : $plan->name;
                            $subtitle = match ($plan->slug) {
                                'free' => 'For one barber getting started.',
                                'starter' => 'For a small shop.',
                                'growth' => 'For a shop that is adding chairs.',
                                'business' => 'For a larger team.',
                                default => $plan->tagline,
                            };
                            $symbol = $currencySymbol ?? '₹';
                        @endphp
                        <article class="bp-price-card {{ $plan->is_popular ? 'is-featured' : '' }}">
                            @if ($plan->is_popular)
                                <span class="bp-price-badge">Most popular</span>
                            @endif
                            <h3>{{ $displayName }}</h3>
                            <p class="bp-price-sub">{{ $subtitle }}</p>
                            <div class="bp-price">
                                <strong class="js-price"
                                    data-monthly="{{ $isFreePlan ? 'Free' : $symbol . number_format((float) $plan->price_monthly, 0) }}"
                                    data-yearly="{{ $isFreePlan ? 'Free' : $symbol . number_format((float) $plan->price_yearly, 0) }}">{{ $isFreePlan ? 'Free' : $symbol . number_format((float) $plan->price_monthly, 0) }}</strong>
                                <span class="js-cycle">{{ $isFreePlan ? 'to start' : '/ month' }}</span>
                            </div>
                            <ul>
                                @foreach (($plan->features ?? []) as $feature)
                                    <li><i class="bi bi-check2" aria-hidden="true"></i> {{ $feature }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="bp-btn {{ $plan->is_popular ? 'bp-btn-primary' : 'bp-btn-outline' }}"
                                data-plan="{{ $plan->id }}">
                                Choose {{ $displayName }}
                            </button>
                        </article>
                    @endforeach
                </div>
                <p class="bn-price-note">Paid plans are billed for the cycle you pick. GST {{ number_format($gstRate ?? 18, 0) }}% is added at checkout.</p>
            </div>
        </section>
    </main>

    <div class="ex-modal" id="exModal" hidden>
        <div class="ex-modal-card" role="dialog" aria-modal="true" aria-labelledby="exModalTitle">
            <div class="ex-modal-head">
                <h3 id="exModalTitle">Review your plan</h3>
                <button type="button" class="ex-modal-close" id="exModalClose" aria-label="Close">&times;</button>
            </div>
            <p class="ex-modal-sub" id="exModalSub"></p>
            <div class="ex-calc" id="exCalc"></div>
            <div class="ex-modal-actions">
                <button type="button" class="bp-btn bp-btn-outline" id="exModalCancel">Cancel</button>
                <button type="button" class="bp-btn bp-btn-primary" id="exModalPay">Continue to payment</button>
            </div>
        </div>
    </div>

    @php
        $exPlanData = $plans->reject(function ($plan) {
            return $plan->slug === 'free' || ((float) $plan->price_monthly <= 0 && (float) $plan->price_yearly <= 0);
        })->map(function ($plan) {
            return [
                'id' => (int) $plan->id,
                'name' => $plan->slug === 'growth' ? 'Professional' : $plan->name,
                'monthly' => (float) $plan->price_monthly,
                'yearly' => (float) $plan->price_yearly,
                'free' => false,
            ];
        })->values();
    @endphp
    <script type="application/json" id="exPlanData">@json($exPlanData)</script>
    <script>
        (function () {
            var billing = 'monthly';
            var gstRate = {{ (float) ($gstRate ?? 18) }};
            var symbol = @json($currencySymbol ?? '₹');
            var plans = JSON.parse(document.getElementById('exPlanData').textContent || '[]');
            var selected = null;
            var form = document.getElementById('exRenewForm');
            var cycleInput = document.getElementById('exCycle');
            var planInput = document.getElementById('exPlanId');
            var modal = document.getElementById('exModal');
            var calc = document.getElementById('exCalc');
            var payBtn = document.getElementById('exModalPay');

            function money(amount) {
                return symbol + Number(amount).toLocaleString('en-IN', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            }

            function round2(n) {
                return Math.round((n + Number.EPSILON) * 100) / 100;
            }

            function totalsFor(plan) {
                var subtotal = billing === 'yearly' ? plan.yearly : plan.monthly;
                var listPrice = plan.monthly * (billing === 'yearly' ? 12 : 1);
                var discount = Math.max(0, round2(listPrice - subtotal));
                var gst = round2(subtotal * (gstRate / 100));
                return {
                    listPrice: listPrice,
                    subtotal: subtotal,
                    discount: discount,
                    gst: gst,
                    total: round2(subtotal + gst)
                };
            }

            function paint() {
                document.querySelectorAll('#exBilling button').forEach(function (btn) {
                    btn.classList.toggle('is-active', btn.getAttribute('data-billing') === billing);
                });
                document.querySelectorAll('.js-price').forEach(function (el) {
                    var raw = billing === 'yearly' ? el.getAttribute('data-yearly') : el.getAttribute('data-monthly');
                    el.textContent = raw;
                    var cycle = el.parentElement.querySelector('.js-cycle');
                    if (cycle) cycle.textContent = raw === 'Free' ? 'to start' : (billing === 'yearly' ? '/ year' : '/ month');
                });
                cycleInput.value = billing;
                if (selected && modal.classList.contains('is-open')) fillModal();
            }

            function row(label, value, extraClass) {
                return '<div class="' + (extraClass || '') + '"><span>' + label + '</span><strong>' + value + '</strong></div>';
            }

            function fillModal() {
                var t = totalsFor(selected);
                var cycleLabel = billing === 'yearly' ? 'Yearly' : 'Monthly';
                document.getElementById('exModalSub').textContent = selected.name + ' · ' + cycleLabel;
                var html = '';
                if (selected.free) {
                    html += row('Plan price', 'Free');
                    html += row('GST (' + gstRate + '%)', money(0));
                    html += row('Total', 'Free', 'is-total');
                    payBtn.textContent = 'Continue';
                } else {
                    html += row(billing === 'yearly' ? '12 × monthly price' : 'Plan price', money(t.listPrice));
                    if (t.discount > 0) html += row('Yearly discount', '−' + money(t.discount), 'is-discount');
                    html += row('Subtotal', money(t.subtotal));
                    html += row('GST (' + gstRate + '%)', money(t.gst));
                    html += row('Total due', money(t.total), 'is-total');
                    payBtn.textContent = 'Continue to payment';
                }
                calc.innerHTML = html;
            }

            function openModal(plan) {
                selected = plan;
                planInput.value = plan.id;
                cycleInput.value = billing;
                fillModal();
                modal.hidden = false;
                modal.classList.add('is-open');
                payBtn.disabled = false;
                document.body.style.overflow = 'hidden';
            }

            function closeModal() {
                modal.classList.remove('is-open');
                modal.hidden = true;
                selected = null;
                document.body.style.overflow = '';
            }

            document.querySelectorAll('#exBilling button').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    billing = btn.getAttribute('data-billing');
                    paint();
                });
            });

            document.querySelectorAll('.bp-price-card button[data-plan]').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    if (btn.disabled) return;
                    var id = parseInt(btn.getAttribute('data-plan'), 10);
                    var plan = plans.find(function (p) { return p.id === id; });
                    if (!plan) return;
                    openModal(plan);
                });
            });

            document.getElementById('exModalClose').addEventListener('click', closeModal);
            document.getElementById('exModalCancel').addEventListener('click', closeModal);
            modal.addEventListener('click', function (e) {
                if (e.target === modal) closeModal();
            });
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' && modal.classList.contains('is-open')) closeModal();
            });

            payBtn.addEventListener('click', function () {
                if (!selected || payBtn.disabled) return;
                planInput.value = selected.id;
                cycleInput.value = billing;
                payBtn.disabled = true;
                payBtn.textContent = 'Processing…';
                form.submit();
            });

            paint();
        })();
    </script>
</body>
</html>
