@extends('saas.layout')

@section('title', 'Get started')
@section('meta_description', 'Create your BarberPro account, set up your shop, and choose a plan.')

@php
    $gstRate = \App\Services\Saas\PlanEntitlementService::GST_RATE;
    $plansPayload = $plans->map(function ($plan) {
        return [
            'id' => $plan->id,
            'name' => $plan->name,
            'slug' => $plan->slug,
            'tagline' => $plan->tagline,
            'description' => $plan->description,
            'price_monthly' => (float) $plan->price_monthly,
            'price_yearly' => (float) $plan->price_yearly,
            'max_staff' => $plan->max_staff,
            'max_appointments_month' => $plan->max_appointments_month,
            'features' => $plan->features ?? [],
            'is_popular' => (bool) $plan->is_popular,
        ];
    })->values();
@endphp

@section('content')
<div class="bp-setup">
    <div class="bp-container bp-setup-inner">
        <header class="bp-setup-head">
            <p class="bp-eyebrow">Get started</p>
            <h1>Set up BarberPro for your shop</h1>
            <p>Create your account, add shop details, then choose a plan. Your private admin URL is ready right after checkout.</p>
        </header>

        <div class="bp-setup-card saas-panel">
            <ol class="bp-setup-steps saas-steps" aria-label="Signup steps">
                <li class="bp-setup-step saas-step-pill is-active" data-step-indicator="1"><span class="num">1</span><span class="bp-setup-step-label">Account</span></li>
                <li class="bp-setup-step saas-step-pill" data-step-indicator="2"><span class="num">2</span><span class="bp-setup-step-label">Shop info</span></li>
                <li class="bp-setup-step saas-step-pill" data-step-indicator="3"><span class="num">3</span><span class="bp-setup-step-label">Plan &amp; pay</span></li>
            </ol>

        @if ($errors->any())
            <div class="saas-alert saas-alert-danger">
                <strong>Please fix the following:</strong>
                <ul style="margin:0.5rem 0 0;padding-left:1.1rem">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('saas.signup.store') }}" id="saasSignupForm" enctype="multipart/form-data" novalidate>
            @csrf

            {{-- Step 1: Owner account --}}
            <div class="saas-step is-active" data-step="1">
                <div class="saas-grid-2">
                    <div class="saas-field">
                        <label for="owner_name">Your name *</label>
                        <input id="owner_name" name="owner_name" type="text" value="{{ old('owner_name') }}" required maxlength="120" autocomplete="name">
                        @error('owner_name')<div class="saas-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="saas-field">
                        <label for="owner_phone">Phone</label>
                        <input id="owner_phone" name="owner_phone" type="text" value="{{ old('owner_phone') }}" maxlength="30" autocomplete="tel">
                        @error('owner_phone')<div class="saas-error">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="saas-field">
                    <label for="owner_email">Login email *</label>
                    <input id="owner_email" name="owner_email" type="email" value="{{ old('owner_email') }}" required maxlength="190" autocomplete="email">
                    @error('owner_email')<div class="saas-error">{{ $message }}</div>@enderror
                </div>
                <div class="saas-grid-2">
                    <div class="saas-field">
                        <label for="password">Password *</label>
                        <input id="password" name="password" type="password" required minlength="8" autocomplete="new-password">
                        @error('password')<div class="saas-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="saas-field">
                        <label for="password_confirmation">Confirm password *</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" required minlength="8" autocomplete="new-password">
                    </div>
                </div>
                <div class="saas-actions">
                    <span></span>
                    <button type="button" class="saas-btn saas-btn-primary" data-next="2">Continue to shop info</button>
                </div>
            </div>

            {{-- Step 2: Shop info --}}
            <div class="saas-step" data-step="2">
                <div class="saas-field">
                    <label for="shop_name">Shop name *</label>
                    <input id="shop_name" name="shop_name" type="text" value="{{ old('shop_name') }}" required maxlength="150">
                    @error('shop_name')<div class="saas-error">{{ $message }}</div>@enderror
                </div>
                <div class="saas-field">
                    <label for="shop_slug">Shop URL slug *</label>
                    <input id="shop_slug" name="shop_slug" type="text" value="{{ old('shop_slug') }}" required maxlength="80" pattern="[a-z0-9]+(?:-[a-z0-9]+)*" placeholder="e.g. rose-barbers">
                    <div class="saas-slug-preview">
                        Your admin URL:
                        <code id="slugPreview">{{ url('/your-shop/admin') }}</code>
                    </div>
                    <span class="hint" id="slugStatus">Lowercase letters, numbers, and hyphens only.</span>
                    @error('shop_slug')<div class="saas-error">{{ $message }}</div>@enderror
                </div>
                <div class="saas-grid-2">
                    <div class="saas-field">
                        <label for="shop_email">Shop email</label>
                        <input id="shop_email" name="shop_email" type="email" value="{{ old('shop_email') }}" maxlength="190">
                        @error('shop_email')<div class="saas-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="saas-field">
                        <label for="shop_phone">Shop phone</label>
                        <input id="shop_phone" name="shop_phone" type="text" value="{{ old('shop_phone') }}" maxlength="30">
                        @error('shop_phone')<div class="saas-error">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="saas-field">
                    <label for="address">Address</label>
                    <input id="address" name="address" type="text" value="{{ old('address') }}" maxlength="255">
                    @error('address')<div class="saas-error">{{ $message }}</div>@enderror
                </div>
                <div class="saas-grid-2">
                    <div class="saas-field">
                        <label for="city">City</label>
                        <input id="city" name="city" type="text" value="{{ old('city') }}" maxlength="100">
                    </div>
                    <div class="saas-field">
                        <label for="state">State</label>
                        <input id="state" name="state" type="text" value="{{ old('state') }}" maxlength="100">
                    </div>
                    <div class="saas-field">
                        <label for="country">Country *</label>
                        @php
                            $countries = \App\Support\CountryCurrency::countries();
                            $selectedCountry = old('country', 'India');
                            if (!in_array($selectedCountry, $countries, true)) {
                                $selectedCountry = 'India';
                            }
                        @endphp
                        <select id="country" name="country" required>
                            @foreach ($countries as $countryName)
                                <option value="{{ $countryName }}" @selected($selectedCountry === $countryName)>{{ $countryName }}</option>
                            @endforeach
                        </select>
                        @error('country')<div class="saas-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="saas-field">
                        <label for="currency">Currency *</label>
                        @php
                            $currencies = \App\Support\CountryCurrency::currencies();
                            $selectedCurrency = old(
                                'currency',
                                \App\Support\CountryCurrency::currencyForCountry($selectedCountry)
                            );
                            if (!isset($currencies[$selectedCurrency])) {
                                $selectedCurrency = 'INR';
                            }
                        @endphp
                        <select id="currency" name="currency" required>
                            @foreach ($currencies as $code => $label)
                                <option value="{{ $code }}" @selected($selectedCurrency === $code)>{{ $label }}</option>
                            @endforeach
                        </select>
                        <span class="hint">Auto-selected from country — change if needed.</span>
                        @error('currency')<div class="saas-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="saas-field">
                        <label for="pincode">Pincode</label>
                        <input id="pincode" name="pincode" type="text" value="{{ old('pincode') }}" maxlength="20">
                    </div>
                </div>

                <div class="saas-branding-box">
                    <h3>Shop branding</h3>
                    <p class="hint" style="margin-top:0">Choose your admin theme color and upload a logo. This appears on your shop admin panel.</p>

                    <div class="saas-field">
                        <label>Theme color *</label>
                        <div class="saas-theme-grid" id="themeColorGrid">
                            @foreach (\App\Models\SaasShop::THEME_PRESETS as $hex => $label)
                                @php $themeChecked = old('theme_color', \App\Models\SaasShop::DEFAULT_THEME_COLOR) === $hex; @endphp
                                <label class="saas-theme-swatch {{ $themeChecked ? 'is-selected' : '' }}" title="{{ $label }}">
                                    <input type="radio" name="theme_color" value="{{ $hex }}" {{ $themeChecked ? 'checked' : '' }} required>
                                    <span class="swatch" style="background:{{ $hex }}"></span>
                                    <em>{{ $label }}</em>
                                </label>
                            @endforeach
                        </div>
                        <div class="saas-theme-custom">
                            <label for="theme_color_custom">Custom</label>
                            <input type="color" id="theme_color_custom" value="{{ old('theme_color', \App\Models\SaasShop::DEFAULT_THEME_COLOR) }}" aria-label="Custom theme color">
                        </div>
                        @error('theme_color')<div class="saas-error">{{ $message }}</div>@enderror
                    </div>

                    <div class="saas-field">
                        <label for="shop_logo">Shop logo</label>
                        <div class="saas-logo-upload">
                            <img id="logoPreview" src="{{ asset('assets/img/logo.webp') }}" alt="Logo preview">
                            <div>
                                <input id="shop_logo" name="shop_logo" type="file" accept="image/png,image/jpeg,image/jpg,image/webp,image/gif">
                                <span class="hint">PNG, JPG or WEBP up to 2MB. Leave empty to use the BarberPro logo.</span>
                            </div>
                        </div>
                        @error('shop_logo')<div class="saas-error">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="saas-actions">
                    <button type="button" class="saas-btn saas-btn-ghost" data-prev="1">Back</button>
                    <button type="button" class="saas-btn saas-btn-primary" data-next="3">Continue to plan</button>
                </div>
            </div>

            {{-- Step 3: Plan + payment + calculation --}}
            <div class="saas-step" data-step="3">
                <div class="saas-checkout">
                    <div class="saas-checkout-main">
                        @if ($selectedPlan)
                            <div class="saas-selected-banner" id="selectedPlanBanner">
                                <div>
                                    <span class="eyebrow">Selected from pricing</span>
                                    <h3 id="bannerPlanName">{{ $selectedPlan->name }}</h3>
                                    <p id="bannerPlanTagline">{{ $selectedPlan->tagline }}</p>
                                </div>
                                <div class="banner-price">
                                    <strong id="bannerPlanPrice">₹{{ number_format((float) ($billingCycle === 'yearly' ? $selectedPlan->price_yearly : $selectedPlan->price_monthly), 0) }}</strong>
                                    <span id="bannerPlanCycle">/ {{ $billingCycle === 'yearly' ? 'year' : 'month' }}</span>
                                </div>
                            </div>
                        @endif

                        <div class="saas-field">
                            <label>Billing cycle *</label>
                            <div class="saas-cycle-row" role="group">
                                <label class="saas-cycle-chip">
                                    <input type="radio" name="billing_cycle" value="monthly" {{ old('billing_cycle', $billingCycle) === 'monthly' ? 'checked' : '' }}>
                                    <span>Monthly</span>
                                </label>
                                <label class="saas-cycle-chip">
                                    <input type="radio" name="billing_cycle" value="yearly" {{ old('billing_cycle', $billingCycle) === 'yearly' ? 'checked' : '' }}>
                                    <span>Yearly <em>save ~17%</em></span>
                                </label>
                            </div>
                            @error('billing_cycle')<div class="saas-error">{{ $message }}</div>@enderror
                        </div>

                        <div class="saas-field">
                            <label>Choose plan *</label>
                            <div class="saas-plan-cards" id="saasPlanCards">
                                @foreach ($plans as $plan)
                                    @php
                                        $checked = (string) old('plan_id', optional($selectedPlan)->id) === (string) $plan->id;
                                        $staffLabel = $plan->max_staff === null ? 'Unlimited staff' : 'Up to ' . $plan->max_staff . ' staff';
                                        $apptLabel = $plan->max_appointments_month === null ? 'Unlimited appointments' : $plan->max_appointments_month . ' appointments / month';
                                    @endphp
                                    <label class="saas-plan-card {{ $checked ? 'is-selected' : '' }} {{ $plan->is_popular ? 'is-popular' : '' }}">
                                        <input type="radio" name="plan_id" value="{{ $plan->id }}" {{ $checked ? 'checked' : '' }} required
                                               data-plan-id="{{ $plan->id }}">
                                        @if ($plan->is_popular)
                                            <span class="saas-plan-card-badge">Popular</span>
                                        @endif
                                        <div class="saas-plan-card-top">
                                            <div>
                                                <h3>{{ $plan->name }}</h3>
                                                <p>{{ $plan->tagline }}</p>
                                            </div>
                                            <div class="saas-plan-card-price">
                                                <strong class="js-card-price" data-monthly="{{ (float) $plan->price_monthly }}" data-yearly="{{ (float) $plan->price_yearly }}">₹{{ number_format((float) ($billingCycle === 'yearly' ? $plan->price_yearly : $plan->price_monthly), 0) }}</strong>
                                                <span class="js-card-cycle">/ {{ $billingCycle === 'yearly' ? 'yr' : 'mo' }}</span>
                                            </div>
                                        </div>
                                        <p class="saas-plan-card-desc">{{ $plan->description }}</p>
                                        <div class="saas-plan-limits">
                                            <span><i class="bi bi-people"></i> {{ $staffLabel }}</span>
                                            <span><i class="bi bi-calendar-check"></i> {{ $apptLabel }}</span>
                                        </div>
                                        <ul class="saas-plan-card-features">
                                            @foreach (($plan->features ?? []) as $feature)
                                                <li><i class="bi bi-check2"></i> {{ $feature }}</li>
                                            @endforeach
                                        </ul>
                                    </label>
                                @endforeach
                            </div>
                            @error('plan_id')<div class="saas-error">{{ $message }}</div>@enderror
                        </div>

                        <div class="saas-field">
                            <label>Payment method *</label>
                            <div class="saas-pay-options">
                                @foreach (['manual' => 'Manual / cash', 'upi' => 'UPI', 'card' => 'Card', 'netbanking' => 'Net banking'] as $value => $label)
                                    <label class="saas-pay-option">
                                        <input type="radio" name="payment_method" value="{{ $value }}" {{ old('payment_method', 'manual') === $value ? 'checked' : '' }} required>
                                        {{ $label }}
                                    </label>
                                @endforeach
                            </div>
                            <span class="hint">Demo mode: purchase activates your shop immediately after submit.</span>
                            @error('payment_method')<div class="saas-error">{{ $message }}</div>@enderror
                        </div>

                        <label class="saas-check">
                            <input type="checkbox" name="terms" value="1" {{ old('terms') ? 'checked' : '' }} required>
                            <span>I agree to the terms and confirm this purchase for my shop admin panel.</span>
                        </label>
                        @error('terms')<div class="saas-error">{{ $message }}</div>@enderror
                    </div>

                    <aside class="saas-order-summary" id="orderSummary">
                        <h3>Order summary</h3>
                        <div class="saas-order-shop" id="summaryShopBlock">
                            <span>Shop</span>
                            <strong id="summaryShopName">—</strong>
                            <code id="summaryShopUrl">—</code>
                        </div>

                        <div class="saas-order-plan" id="summaryPlanBlock">
                            <strong id="summaryPlanName">—</strong>
                            <p id="summaryPlanTagline">—</p>
                            <ul id="summaryPlanFeatures"></ul>
                        </div>

                        <div class="saas-calc-rows">
                            <div class="saas-calc-row">
                                <span>Plan price</span>
                                <strong id="calcListPrice">₹0</strong>
                            </div>
                            <div class="saas-calc-row is-discount" id="calcDiscountRow" hidden>
                                <span>Yearly discount</span>
                                <strong id="calcDiscount">−₹0</strong>
                            </div>
                            <div class="saas-calc-row">
                                <span>Subtotal</span>
                                <strong id="calcSubtotal">₹0</strong>
                            </div>
                            <div class="saas-calc-row">
                                <span>GST ({{ number_format($gstRate, 0) }}%)</span>
                                <strong id="calcGst">₹0</strong>
                            </div>
                            <div class="saas-calc-row is-total">
                                <span>Total due</span>
                                <strong id="calcTotal">₹0</strong>
                            </div>
                        </div>

                        <p class="saas-order-note" id="summaryLimits">Plan limits apply in your admin panel.</p>

                        <div class="saas-actions saas-order-actions">
                            <button type="button" class="saas-btn saas-btn-ghost" data-prev="2">Back</button>
                            <button type="submit" class="saas-btn saas-btn-primary" id="saasSubmitBtn">
                                <i class="bi bi-bag-check"></i> Complete purchase
                            </button>
                        </div>
                    </aside>
                </div>
            </div>
        </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    var form = document.getElementById('saasSignupForm');
    var baseUrl = @json(rtrim(url('/'), '/'));
    var checkSlugUrl = @json(route('saas.check-slug'));
    var plans = @json($plansPayload);
    var gstRate = {{ $gstRate }};
    var countryCurrencyMap = @json(\App\Support\CountryCurrency::map());
    var currentStep = {{ $errors->any() ? 3 : 1 }};
    var slugTimer = null;
    var slugOk = true;

    var countrySelect = document.getElementById('country');
    var currencySelect = document.getElementById('currency');
    if (countrySelect && currencySelect) {
        countrySelect.addEventListener('change', function () {
            var code = countryCurrencyMap[countrySelect.value];
            if (!code) return;
            var opt = currencySelect.querySelector('option[value="' + code + '"]');
            if (opt) {
                currencySelect.value = code;
            }
        });
    }

    function money(n) {
        return '₹' + Number(n || 0).toLocaleString('en-IN', { maximumFractionDigits: 0 });
    }

    function moneyExact(n) {
        return '₹' + Number(n || 0).toLocaleString('en-IN', { minimumFractionDigits: 0, maximumFractionDigits: 2 });
    }

    function selectedPlan() {
        var id = form.querySelector('input[name="plan_id"]:checked');
        if (!id) return null;
        return plans.find(function (p) { return String(p.id) === String(id.value); }) || null;
    }

    function billingCycle() {
        var el = form.querySelector('input[name="billing_cycle"]:checked');
        return el && el.value === 'yearly' ? 'yearly' : 'monthly';
    }

    function calcTotals(plan, cycle) {
        if (!plan) return null;
        var subtotal = cycle === 'yearly' ? plan.price_yearly : plan.price_monthly;
        var list = plan.price_monthly * (cycle === 'yearly' ? 12 : 1);
        var discount = Math.max(0, Math.round((list - subtotal) * 100) / 100);
        var gst = Math.round(subtotal * (gstRate / 100) * 100) / 100;
        var total = Math.round((subtotal + gst) * 100) / 100;
        return { list: list, subtotal: subtotal, discount: discount, gst: gst, total: total, cycle: cycle };
    }

    function updateOrderSummary() {
        var plan = selectedPlan();
        var cycle = billingCycle();
        var totals = calcTotals(plan, cycle);
        var shopName = (form.shop_name.value || '').trim() || 'Your shop';
        var slug = (form.shop_slug.value || '').trim() || 'your-shop';

        document.getElementById('summaryShopName').textContent = shopName;
        document.getElementById('summaryShopUrl').textContent = baseUrl + '/' + slug + '/admin';

        if (!plan || !totals) return;

        document.getElementById('summaryPlanName').textContent = plan.name + ' · ' + (cycle === 'yearly' ? 'Yearly' : 'Monthly');
        document.getElementById('summaryPlanTagline').textContent = plan.tagline || '';

        var feat = document.getElementById('summaryPlanFeatures');
        feat.innerHTML = '';
        (plan.features || []).forEach(function (f) {
            var li = document.createElement('li');
            li.innerHTML = '<i class="bi bi-check2"></i> ' + f;
            feat.appendChild(li);
        });

        document.getElementById('calcListPrice').textContent = moneyExact(totals.list);
        document.getElementById('calcSubtotal').textContent = moneyExact(totals.subtotal);
        document.getElementById('calcGst').textContent = moneyExact(totals.gst);
        document.getElementById('calcTotal').textContent = moneyExact(totals.total);

        var discountRow = document.getElementById('calcDiscountRow');
        if (totals.discount > 0) {
            discountRow.hidden = false;
            document.getElementById('calcDiscount').textContent = '−' + moneyExact(totals.discount);
        } else {
            discountRow.hidden = true;
        }

        var staff = plan.max_staff == null ? 'Unlimited staff' : ('Up to ' + plan.max_staff + ' staff');
        var appts = plan.max_appointments_month == null ? 'Unlimited appointments' : (plan.max_appointments_month + ' appointments / month');
        document.getElementById('summaryLimits').textContent = 'Admin limits: ' + staff + ' · ' + appts;

        var bannerName = document.getElementById('bannerPlanName');
        if (bannerName) {
            bannerName.textContent = plan.name;
            document.getElementById('bannerPlanTagline').textContent = plan.tagline || '';
            document.getElementById('bannerPlanPrice').textContent = money(totals.subtotal);
            document.getElementById('bannerPlanCycle').textContent = '/ ' + (cycle === 'yearly' ? 'year' : 'month');
        }

        document.querySelectorAll('.js-card-price').forEach(function (el) {
            var m = Number(el.getAttribute('data-monthly'));
            var y = Number(el.getAttribute('data-yearly'));
            el.textContent = money(cycle === 'yearly' ? y : m);
        });
        document.querySelectorAll('.js-card-cycle').forEach(function (el) {
            el.textContent = '/ ' + (cycle === 'yearly' ? 'yr' : 'mo');
        });
    }

    function showStep(step) {
        currentStep = step;
        document.querySelectorAll('.saas-step').forEach(function (el) {
            el.classList.toggle('is-active', Number(el.getAttribute('data-step')) === step);
        });
        document.querySelectorAll('[data-step-indicator]').forEach(function (el) {
            var n = Number(el.getAttribute('data-step-indicator'));
            el.classList.toggle('is-active', n === step);
            el.classList.toggle('is-done', n < step);
        });
        if (step === 3) updateOrderSummary();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function slugify(value) {
        return String(value || '')
            .toLowerCase()
            .trim()
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-+|-+$/g, '')
            .replace(/-{2,}/g, '-');
    }

    function updatePreview(slug) {
        document.getElementById('slugPreview').textContent = baseUrl + '/' + (slug || 'your-shop') + '/admin';
        updateOrderSummary();
    }

    function setSlugStatus(ok, message) {
        var el = document.getElementById('slugStatus');
        el.textContent = message;
        el.classList.toggle('ok', !!ok);
        el.classList.toggle('bad', !ok);
        slugOk = !!ok;
    }

    function checkSlug(slug) {
        if (!slug) {
            updatePreview('');
            setSlugStatus(false, 'Enter a shop URL slug.');
            return;
        }
        updatePreview(slug);
        fetch(checkSlugUrl + '?slug=' + encodeURIComponent(slug), {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (data.slug) updatePreview(data.slug);
                setSlugStatus(!!data.available, data.message || '');
            })
            .catch(function () {
                setSlugStatus(true, 'Could not verify slug right now — we will check on submit.');
            });
    }

    var shopName = document.getElementById('shop_name');
    var shopSlug = document.getElementById('shop_slug');
    var slugTouched = !!shopSlug.value;

    shopName.addEventListener('input', function () {
        if (!slugTouched) {
            shopSlug.value = slugify(shopName.value);
            clearTimeout(slugTimer);
            slugTimer = setTimeout(function () { checkSlug(shopSlug.value); }, 280);
        }
        updateOrderSummary();
    });

    shopSlug.addEventListener('input', function () {
        slugTouched = true;
        shopSlug.value = slugify(shopSlug.value);
        clearTimeout(slugTimer);
        slugTimer = setTimeout(function () { checkSlug(shopSlug.value); }, 280);
    });

    if (shopSlug.value) checkSlug(shopSlug.value);
    else updatePreview('');

    document.querySelectorAll('[data-next]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var next = Number(btn.getAttribute('data-next'));
            if (next === 2) {
                if (!form.owner_name.value.trim() || !form.owner_email.value.trim() || !form.password.value || form.password.value !== form.password_confirmation.value) {
                    alert('Please complete your account details and matching passwords.');
                    return;
                }
            }
            if (next === 3) {
                if (!form.shop_name.value.trim() || !form.shop_slug.value.trim()) {
                    alert('Please enter shop name and URL slug.');
                    return;
                }
                if (!slugOk) {
                    alert('Please choose an available shop URL.');
                    return;
                }
            }
            showStep(next);
        });
    });

    document.querySelectorAll('[data-prev]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            showStep(Number(btn.getAttribute('data-prev')));
        });
    });

    document.querySelectorAll('.saas-plan-card').forEach(function (label) {
        label.addEventListener('click', function () {
            document.querySelectorAll('.saas-plan-card').forEach(function (el) { el.classList.remove('is-selected'); });
            label.classList.add('is-selected');
            setTimeout(updateOrderSummary, 0);
        });
    });

    form.querySelectorAll('input[name="billing_cycle"]').forEach(function (el) {
        el.addEventListener('change', updateOrderSummary);
    });
    form.querySelectorAll('input[name="plan_id"]').forEach(function (el) {
        el.addEventListener('change', updateOrderSummary);
    });

    form.addEventListener('submit', function () {
        var btn = document.getElementById('saasSubmitBtn');
        btn.disabled = true;
        btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Creating your shop...';
    });

    // Theme color presets + custom picker
    var customColor = document.getElementById('theme_color_custom');
    document.querySelectorAll('.saas-theme-swatch').forEach(function (label) {
        label.addEventListener('click', function () {
            document.querySelectorAll('.saas-theme-swatch').forEach(function (el) { el.classList.remove('is-selected'); });
            label.classList.add('is-selected');
            var input = label.querySelector('input');
            if (input && customColor) customColor.value = input.value;
        });
    });
    if (customColor) {
        customColor.addEventListener('input', function () {
            var hex = customColor.value.toUpperCase();
            var matched = false;
            document.querySelectorAll('input[name="theme_color"]').forEach(function (radio) {
                if (radio.value.toUpperCase() === hex) {
                    radio.checked = true;
                    matched = true;
                    radio.closest('.saas-theme-swatch').classList.add('is-selected');
                } else {
                    radio.closest('.saas-theme-swatch').classList.remove('is-selected');
                }
            });
            if (!matched) {
                // Inject/update a hidden custom radio so validation passes
                var customRadio = form.querySelector('input[name="theme_color"][data-custom="1"]');
                if (!customRadio) {
                    customRadio = document.createElement('input');
                    customRadio.type = 'radio';
                    customRadio.name = 'theme_color';
                    customRadio.setAttribute('data-custom', '1');
                    customRadio.style.display = 'none';
                    form.appendChild(customRadio);
                }
                customRadio.value = hex;
                customRadio.checked = true;
            }
        });
    }

    // Logo live preview
    var logoInput = document.getElementById('shop_logo');
    var logoPreview = document.getElementById('logoPreview');
    if (logoInput && logoPreview) {
        logoInput.addEventListener('change', function () {
            var file = logoInput.files && logoInput.files[0];
            if (!file) return;
            var url = URL.createObjectURL(file);
            logoPreview.src = url;
        });
    }

    showStep(currentStep);
    updateOrderSummary();
})();
</script>
@endpush
