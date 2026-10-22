<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SaasPlan;
use App\Models\SaasSubscription;
use App\Services\Saas\PlanEntitlementService;
use App\Services\Saas\ShopSignupService;
use App\Support\CountryCurrency;
use App\Support\CurrentShop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class BillingController extends Controller
{
    public function index(PlanEntitlementService $entitlements)
    {
        $shop = CurrentShop::require();
        $shop->syncExpiredSubscriptions();

        if (!$shop->hasActiveSubscription()) {
            return redirect()->route('admin.plan.expired', ['shop' => $shop->slug]);
        }

        return view('admin.billing.index', $this->billingPayload($shop, $entitlements));
    }

    /**
     * Shown instead of the admin shell when the shop plan has expired.
     */
    public function expired(PlanEntitlementService $entitlements)
    {
        $shop = CurrentShop::require();
        $shop->syncExpiredSubscriptions();

        if ($shop->hasActiveSubscription()) {
            return redirect()->route(auth()->user()->preferredAdminHomeRoute(), ['shop' => $shop->slug]);
        }

        return view('admin.billing.expired', $this->billingPayload($shop, $entitlements));
    }

    /**
     * Renew / change plan on the CURRENT shop — same shop_id & owner, no new account.
     */
    public function renew(Request $request, ShopSignupService $signupService)
    {
        $shop = CurrentShop::require();
        $planIds = SaasPlan::active()->pluck('id')->all();

        $validated = $request->validate([
            'plan_id' => ['required', 'integer', Rule::in($planIds)],
            'billing_cycle' => ['required', Rule::in(['monthly', 'yearly'])],
            'payment_method' => ['required', Rule::in(['manual', 'upi', 'card', 'netbanking'])],
        ]);

        $plan = SaasPlan::findOrFail($validated['plan_id']);

        $activeSubscription = SaasSubscription::query()
            ->where('shop_id', $shop->id)
            ->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>', now());
            })
            ->latest('id')
            ->first();

        if ($activeSubscription && (int) $activeSubscription->plan_id === (int) $plan->id) {
            return redirect()
                ->route('admin.billing.index', ['shop' => $shop->slug])
                ->withInput()
                ->withErrors([
                    'plan_id' => 'Your current plan is still active until '
                        . optional($activeSubscription->ends_at)->format('d M Y')
                        . '. Renew after it expires, or choose a different plan to upgrade.',
                ]);
        }

        $result = $signupService->renew(
            $shop,
            $plan,
            $validated['billing_cycle'],
            $validated['payment_method']
        );

        $wasUpgrade = $activeSubscription && (int) $activeSubscription->plan_id !== (int) $plan->id;
        $message = $wasUpgrade
            ? 'Plan upgraded successfully. New plan is active until '
            : 'Plan renewed successfully on your existing shop account. Valid until ';

        return redirect()
            ->route('admin.billing.index', ['shop' => $shop->slug])
            ->with('success', $message . optional($result['subscription']->ends_at)->format('d M Y') . '.');
    }

    protected function billingPayload($shop, PlanEntitlementService $entitlements): array
    {
        $subscription = $entitlements->subscription($shop)
            ?? $shop->subscriptions()->with('plan')->latest('id')->first();
        $usage = $entitlements->usageSummary($shop);

        return [
            'shop' => $shop,
            'subscription' => $subscription,
            'usage' => $usage,
            'plan' => $usage['plan'] ?? $subscription?->plan,
            'plans' => SaasPlan::active()->orderBy('sort_order')->orderBy('id')->get(),
            'needsRenewal' => !$shop->hasActiveSubscription(),
            'freeUsed' => $shop->hasUsedFreePlan(),
            'gstRate' => PlanEntitlementService::GST_RATE,
            'currencies' => CountryCurrency::currencies(),
            'currencyCode' => CountryCurrency::currentCode(),
            'currencySymbol' => CountryCurrency::symbol(),
        ];
    }

    public function updateBranding(Request $request)
    {
        $shop = CurrentShop::require();

        $validated = $request->validate([
            'theme_color' => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'shop_logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:2048'],
            'remove_logo' => ['nullable', 'boolean'],
        ]);

        $shop->theme_color = strtoupper($validated['theme_color']);

        if ($request->boolean('remove_logo') && $shop->logo) {
            Storage::disk('public')->delete($shop->logo);
            $shop->logo = null;
        }

        if ($request->hasFile('shop_logo')) {
            if ($shop->logo) {
                Storage::disk('public')->delete($shop->logo);
            }
            $shop->logo = $request->file('shop_logo')->store('shop-logos', 'public');
        }

        $shop->save();

        return redirect()
            ->route('admin.billing.index', ['shop' => $shop->slug])
            ->with('success', 'Shop logo and theme color updated for the whole admin panel.');
    }

    public function updateCurrency(Request $request)
    {
        $shop = CurrentShop::require();

        if (!Schema::hasColumn('saas_shops', 'currency')) {
            return redirect()
                ->route('admin.billing.index', ['shop' => $shop->slug])
                ->with('error', 'Currency column is not available yet. Please run migrations.');
        }

        $validated = $request->validate([
            'currency' => ['required', 'string', 'max:10', Rule::in(array_keys(CountryCurrency::currencies()))],
        ]);

        $shop->currency = strtoupper($validated['currency']);
        $shop->save();

        return redirect()
            ->route('admin.billing.index', ['shop' => $shop->slug])
            ->with('success', 'Shop currency updated to ' . $shop->currency . '. Admin amounts will use '
                . CountryCurrency::symbol($shop->currency) . ' going forward.');
    }
}
