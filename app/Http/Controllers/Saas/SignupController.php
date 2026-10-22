<?php

namespace App\Http\Controllers\Saas;

use App\Http\Controllers\Controller;
use App\Models\SaasPlan;
use App\Models\SaasShop;
use App\Services\Saas\ShopSignupService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class SignupController extends Controller
{
    public function home()
    {
        $plans = SaasPlan::active()->orderBy('sort_order')->orderBy('id')->get();

        return view('saas.home', compact('plans'));
    }

    public function pricing()
    {
        $plans = SaasPlan::active()->orderBy('sort_order')->orderBy('id')->get();

        return view('saas.pricing', compact('plans'));
    }

    public function create(Request $request)
    {
        $plans = SaasPlan::active()->orderBy('sort_order')->orderBy('id')->get();

        if ($plans->isEmpty()) {
            return redirect()
                ->route('saas.pricing')
                ->with('error', 'No plans are available yet. Please contact support.');
        }

        $selectedPlan = null;
        if ($request->filled('plan')) {
            $selectedPlan = $plans->firstWhere('slug', $request->query('plan'));
        }
        $selectedPlan = $selectedPlan ?: $plans->firstWhere('is_popular') ?: $plans->first();

        $billingCycle = $request->query('billing', 'monthly') === 'yearly' ? 'yearly' : 'monthly';

        return view('saas.signup', compact('plans', 'selectedPlan', 'billingCycle'));
    }

    public function checkSlug(Request $request)
    {
        $slug = Str::slug((string) $request->query('slug', ''));

        if ($slug === '' || in_array($slug, SaasShop::RESERVED_SLUGS, true)) {
            return response()->json([
                'available' => false,
                'slug' => $slug,
                'message' => 'This shop URL is not available.',
            ]);
        }

        $exists = SaasShop::withTrashed()->where('slug', $slug)->exists();

        return response()->json([
            'available' => !$exists,
            'slug' => $slug,
            'message' => $exists ? 'This shop URL is already taken.' : 'Shop URL is available.',
            'preview' => url('/' . $slug . '/admin'),
        ]);
    }

    public function store(Request $request, ShopSignupService $signupService)
    {
        $plans = SaasPlan::active()->pluck('id')->all();

        // Existing shop owner renewing via signup form → renew same account, do not create new user/shop.
        $existingUser = \App\Models\User::where('email', $request->input('owner_email'))->first();
        if ($existingUser && $existingUser->shop_id) {
            $existingShop = SaasShop::find($existingUser->shop_id);
            if ($existingShop) {
                $validated = $request->validate([
                    'owner_email' => ['required', 'email'],
                    'password' => ['required', 'string'],
                    'plan_id' => ['required', 'integer', Rule::in($plans)],
                    'billing_cycle' => ['required', Rule::in(['monthly', 'yearly'])],
                    'payment_method' => ['required', Rule::in(['manual', 'upi', 'card', 'netbanking'])],
                    'terms' => ['accepted'],
                ]);

                if (!\Illuminate\Support\Facades\Hash::check($validated['password'], $existingUser->password)) {
                    return back()
                        ->withInput()
                        ->withErrors([
                            'owner_email' => 'This email already has a shop. Enter the correct password to renew that account, or log in at /' . $existingShop->slug . '/admin/billing.',
                        ]);
                }

                $plan = SaasPlan::findOrFail($validated['plan_id']);
                $result = $signupService->renew(
                    $existingShop,
                    $plan,
                    $validated['billing_cycle'],
                    $validated['payment_method']
                );

                Auth::login($existingUser);

                return redirect()
                    ->route('saas.success', ['shop' => $result['shop']->slug])
                    ->with('success', 'Plan renewed on your existing shop account.');
            }
        }

        $validated = $request->validate([
            'owner_name' => ['required', 'string', 'max:120'],
            'owner_email' => ['required', 'email', 'max:190', 'unique:users,email'],
            'owner_phone' => ['nullable', 'string', 'max:30'],
            'password' => ['required', 'confirmed', Password::min(8)],

            'shop_name' => ['required', 'string', 'max:150'],
            'shop_slug' => [
                'required',
                'string',
                'max:80',
                'alpha_dash',
                Rule::notIn(SaasShop::RESERVED_SLUGS),
                Rule::unique('saas_shops', 'slug'),
            ],
            'shop_email' => ['nullable', 'email', 'max:190'],
            'shop_phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100', Rule::in(\App\Support\CountryCurrency::countries())],
            'currency' => ['nullable', 'string', 'max:10', Rule::in(array_keys(\App\Support\CountryCurrency::currencies()))],
            'pincode' => ['nullable', 'string', 'max:20'],
            'theme_color' => ['nullable', 'string', 'max:20', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'shop_logo' => ['nullable', 'image', 'max:2048'],

            'plan_id' => ['required', 'integer', Rule::in($plans)],
            'billing_cycle' => ['required', Rule::in(['monthly', 'yearly'])],
            'payment_method' => ['required', Rule::in(['manual', 'upi', 'card', 'netbanking'])],
            'terms' => ['accepted'],
        ]);

        $plan = SaasPlan::findOrFail($validated['plan_id']);

        $result = $signupService->provision(
            [
                'name' => $validated['owner_name'],
                'email' => $validated['owner_email'],
                'phone' => $validated['owner_phone'] ?? null,
                'password' => $validated['password'],
            ],
            [
                'name' => $validated['shop_name'],
                'slug' => $validated['shop_slug'],
                'email' => $validated['shop_email'] ?? null,
                'phone' => $validated['shop_phone'] ?? null,
                'address' => $validated['address'] ?? null,
                'city' => $validated['city'] ?? null,
                'state' => $validated['state'] ?? null,
                'country' => $validated['country'] ?? 'India',
                'currency' => $validated['currency']
                    ?? \App\Support\CountryCurrency::currencyForCountry($validated['country'] ?? 'India'),
                'pincode' => $validated['pincode'] ?? null,
                'theme_color' => $validated['theme_color'] ?? null,
                'logo' => $request->file('shop_logo'),
            ],
            $plan,
            $validated['billing_cycle'],
            $validated['payment_method']
        );

        Auth::login($result['user']);

        return redirect()
            ->route('saas.success', ['shop' => $result['shop']->slug]);
    }

    public function success(string $shop)
    {
        $shopModel = SaasShop::with(['activeSubscription.plan', 'owner'])
            ->where('slug', $shop)
            ->firstOrFail();

        $subscription = $shopModel->activeSubscription
            ?? $shopModel->subscriptions()->with('plan')->latest('id')->first();

        return view('saas.success', [
            'shop' => $shopModel,
            'subscription' => $subscription,
        ]);
    }

    public function findShop()
    {
        return view('saas.find-shop');
    }

    public function resolveShop(Request $request)
    {
        $validated = $request->validate([
            'shop' => ['required', 'string', 'max:80'],
        ]);

        $slug = Str::slug($validated['shop']);
        $shop = SaasShop::where('slug', $slug)->where('status', 'active')->first();

        if (!$shop) {
            return back()
                ->withInput()
                ->with('error', 'No active shop found for that URL.');
        }

        return redirect()->to($shop->adminLoginUrl());
    }
}
