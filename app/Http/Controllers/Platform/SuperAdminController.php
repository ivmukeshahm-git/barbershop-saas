<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\PlatformSetting;
use App\Models\PlatformAdmin;
use App\Models\SaasPlan;
use App\Models\SaasShop;
use App\Models\SaasSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class SuperAdminController extends Controller
{
    public function showLogin()
    {
        if (Auth::guard('platform')->check()) {
            return redirect()->route('platform.dashboard');
        }

        return view('platform.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email', 'max:190'],
            'password' => ['required', 'string'],
        ]);

        $admin = PlatformAdmin::where('email', $credentials['email'])->first();

        if (
            !$admin
            || !$admin->is_active
            || !\Illuminate\Support\Facades\Hash::check($credentials['password'], $admin->password)
        ) {
            return back()
                ->withErrors(['email' => 'The provided credentials are invalid or not authorized for platform access.'])
                ->onlyInput('email');
        }

        Auth::guard('platform')->login($admin, $request->boolean('remember'));
        $request->session()->regenerate();

        return redirect()->intended(route('platform.dashboard'));
    }

    public function logout(Request $request)
    {
        Auth::guard('platform')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('platform.login');
    }

    public function dashboard()
    {
        $activeShops = SaasShop::where('status', 'active')->count();
        $activeSubscriptions = SaasSubscription::where('status', 'active')
            ->where(function ($query) {
                $query->whereNull('ends_at')->orWhere('ends_at', '>', now());
            });
        $monthlyRevenue = [];
        foreach ((clone $activeSubscriptions)->get() as $subscription) {
            $currency = $subscription->currency ?: 'INR';
            $monthlyRevenue[$currency] = ($monthlyRevenue[$currency] ?? 0)
                + ($subscription->billing_cycle === 'yearly'
                    ? (float) $subscription->amount / 12
                    : (float) $subscription->amount);
        }
        $paidTotals = SaasSubscription::whereNotNull('paid_at')
            ->get(['currency', 'amount'])
            ->groupBy(function (SaasSubscription $subscription) {
                return $subscription->currency ?: 'INR';
            })
            ->map(function ($subscriptions) {
                return $subscriptions->sum(function (SaasSubscription $subscription) {
                    return (float) $subscription->amount;
                });
            });

        $stats = [
            'shops' => SaasShop::count(),
            'active_shops' => $activeShops,
            'pending_shops' => SaasShop::where('status', 'pending')->count(),
            'subscriptions' => (clone $activeSubscriptions)->count(),
            'monthly_revenue' => $monthlyRevenue,
            'paid_totals' => $paidTotals,
        ];

        $recentShops = SaasShop::with(['owner', 'activeSubscription.plan'])
            ->latest()
            ->limit(6)
            ->get();
        $recentPayments = SaasSubscription::with(['shop', 'plan'])
            ->whereNotNull('paid_at')
            ->latest('paid_at')
            ->limit(6)
            ->get();

        return view('platform.dashboard', compact('stats', 'recentShops', 'recentPayments'));
    }

    public function shops()
    {
        $statuses = ['pending', 'active', 'suspended', 'cancelled'];

        return view('platform.shops', compact('statuses'));
    }

    public function shopsData(Request $request)
    {
        $query = SaasShop::with(['owner', 'activeSubscription.plan', 'latestSubscription.plan'])
            ->withCount('users')
            ->when(
                in_array($request->input('status'), ['pending', 'active', 'suspended', 'cancelled'], true),
                function ($query) use ($request) {
                    $query->where('status', $request->input('status'));
                }
            )
            ->latest();

        return DataTables::eloquent($query)
            ->filter(function ($query) use ($request) {
                $search = trim((string) $request->input('search.value'));
                if ($search === '') {
                    return;
                }

                $query->where(function ($shops) use ($search) {
                    $shops->where('name', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhereHas('owner', function ($owner) use ($search) {
                            $owner->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        });
                });
            }, false)
            ->addColumn('shop_owner', function (SaasShop $shop) {
                return '<span class="pa-primary-cell">' . e($shop->name) . '</span>'
                    . '<span class="pa-sub-cell">/' . e($shop->slug) . ' · ' . e($shop->owner->name ?? 'Owner not assigned') . '</span>';
            })
            ->addColumn('contact', function (SaasShop $shop) {
                return e($shop->email ?? '—') . '<span class="pa-sub-cell">'
                    . e($shop->phone ?? ($shop->owner->email ?? '')) . '</span>';
            })
            ->addColumn('team', function (SaasShop $shop) {
                return number_format($shop->users_count);
            })
            ->addColumn('plan', function (SaasShop $shop) {
                return e($shop->activeSubscription->plan->name ?? $shop->latestSubscription->plan->name ?? '—');
            })
            ->addColumn('plan_dates', function (SaasShop $shop) {
                $subscription = $shop->activeSubscription ?: $shop->latestSubscription;
                if (!$subscription) {
                    return '—';
                }

                return '<span class="pa-sub-cell">Start: ' . e(optional($subscription->starts_at)->format('M j, Y') ?: '—') . '</span>'
                    . '<span class="pa-sub-cell">End: ' . e(optional($subscription->ends_at)->format('M j, Y') ?: 'No expiry') . '</span>';
            })
            ->addColumn('created', function (SaasShop $shop) {
                return $shop->created_at->format('M j, Y');
            })
            ->addColumn('status_control', function (SaasShop $shop) {
                $options = '';
                foreach (['pending', 'active', 'suspended', 'cancelled'] as $status) {
                    $selected = $shop->status === $status ? ' selected' : '';
                    $options .= '<option value="' . $status . '"' . $selected . '>' . ucfirst($status) . '</option>';
                }

                return '<form class="pa-inline-form" method="POST" action="' . e(route('platform.shops.status', $shop)) . '">'
                    . csrf_field() . '<input type="hidden" name="_method" value="PATCH">'
                    . '<select name="status" aria-label="Status for ' . e($shop->name) . '">' . $options . '</select>'
                    . '<button class="pa-mini-button" type="submit" aria-label="Save ' . e($shop->name) . ' status"><i class="bi bi-check2"></i></button></form>';
            })
            ->addColumn('actions', function (SaasShop $shop) {
                return '<button type="button" class="pa-button pa-button-light js-plan-history" data-url="'
                    . e(route('platform.shops.history', $shop)) . '" data-shop="' . e($shop->name)
                    . '"><i class="bi bi-clock-history"></i> Plan history</button> '
                    . '<a class="pa-button pa-button-light" href="' . e($shop->adminLoginUrl())
                    . '" target="_blank" rel="noopener"><i class="bi bi-box-arrow-up-right"></i> Open</a>';
            })
            ->rawColumns(['shop_owner', 'contact', 'plan_dates', 'status_control', 'actions'])
            ->toJson();
    }

    public function shopPlanHistory(SaasShop $shop)
    {
        $history = $shop->subscriptions()
            ->with('plan')
            ->latest('starts_at')
            ->latest('id')
            ->get()
            ->map(function (SaasSubscription $subscription) {
                $status = $subscription->isActive()
                    ? 'active'
                    : ($subscription->status === 'active' ? 'expired' : $subscription->status);

                return [
                    'plan' => $subscription->plan->name ?? 'Plan removed',
                    'amount' => ($subscription->currency ?: 'INR') . ' ' . number_format((float) $subscription->amount, 2),
                    'cycle' => ucfirst($subscription->billing_cycle),
                    'start' => optional($subscription->starts_at)->format('M j, Y') ?: '—',
                    'end' => optional($subscription->ends_at)->format('M j, Y') ?: 'No expiry',
                    'status' => $status,
                ];
            });

        return response()->json(['data' => $history]);
    }

    public function updateShopStatus(Request $request, SaasShop $shop)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['pending', 'active', 'suspended', 'cancelled'])],
        ]);

        $shop->status = $validated['status'];
        if ($validated['status'] === 'active' && !$shop->activated_at) {
            $shop->activated_at = now();
        }
        $shop->save();

        return back()->with('success', 'Shop account status updated.');
    }

    public function payments()
    {
        return view('platform.payments');
    }

    public function paymentsData(Request $request)
    {
        $query = SaasSubscription::with(['shop', 'plan'])->latest();

        return DataTables::eloquent($query)
            ->filter(function ($query) use ($request) {
                $search = trim((string) $request->input('search.value'));
                if ($search === '') {
                    return;
                }

                $query->where(function ($subscriptions) use ($search) {
                    $subscriptions->where('payment_reference', 'like', "%{$search}%")
                        ->orWhere('payment_method', 'like', "%{$search}%")
                        ->orWhereHas('shop', function ($shop) use ($search) {
                            $shop->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        })
                        ->orWhereHas('plan', function ($plan) use ($search) {
                            $plan->where('name', 'like', "%{$search}%");
                        });
                });
            }, false)
            ->addColumn('shop', function (SaasSubscription $subscription) {
                return '<span class="pa-primary-cell">' . e($subscription->shop->name ?? 'Shop removed') . '</span>'
                    . '<span class="pa-sub-cell">' . e($subscription->shop->email ?? '—') . '</span>';
            })
            ->addColumn('plan_name', function (SaasSubscription $subscription) {
                return e($subscription->plan->name ?? 'Plan removed');
            })
            ->addColumn('amount_display', function (SaasSubscription $subscription) {
                return e($subscription->currency ?: 'INR') . ' ' . number_format((float) $subscription->amount, 2);
            })
            ->addColumn('cycle_display', function (SaasSubscription $subscription) {
                return ucfirst($subscription->billing_cycle);
            })
            ->addColumn('payment_details', function (SaasSubscription $subscription) {
                return e($subscription->payment_method ? ucfirst($subscription->payment_method) : '—')
                    . '<span class="pa-sub-cell">' . e($subscription->payment_reference ?: 'No reference recorded') . '</span>';
            })
            ->addColumn('paid_date', function (SaasSubscription $subscription) {
                return optional($subscription->paid_at)->format('M j, Y') ?: '—';
            })
            ->addColumn('subscription_status', function (SaasSubscription $subscription) {
                return '<span class="pa-status pa-status-' . e($subscription->status) . '">' . e($subscription->status) . '</span>';
            })
            ->addColumn('receipt', function (SaasSubscription $subscription) {
                if (!$subscription->paid_at) {
                    return '<span class="pa-sub-cell">Not paid</span>';
                }

                return '<a class="pa-button pa-button-light" href="' . e(route('platform.payments.receipt', $subscription))
                    . '" target="_blank" rel="noopener"><i class="bi bi-receipt"></i> View receipt</a>';
            })
            ->rawColumns(['shop', 'payment_details', 'subscription_status', 'receipt'])
            ->toJson();
    }

    public function receipt(SaasSubscription $subscription)
    {
        abort_unless($subscription->paid_at, 404);

        $subscription->load(['shop', 'plan']);

        return view('platform.receipt', compact('subscription'));
    }

    public function plans()
    {
        $plans = SaasPlan::withCount('subscriptions')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return view('platform.plans', compact('plans'));
    }

    public function updatePlan(Request $request, SaasPlan $plan)
    {
        $validated = $request->validate([
            'price_monthly' => ['required', 'numeric', 'min:0', 'max:99999999'],
            'price_yearly' => ['required', 'numeric', 'min:0', 'max:99999999'],
            'max_staff' => ['nullable', 'integer', 'min:1', 'max:100000'],
            'max_appointments_month' => ['nullable', 'integer', 'min:1', 'max:10000000'],
            'is_active' => ['required', 'boolean'],
            'is_popular' => ['required', 'boolean'],
        ]);

        $plan->update($validated);

        return back()->with('success', $plan->name . ' plan updated.');
    }

    public function website()
    {
        $settings = PlatformSetting::values();

        return view('platform.website', compact('settings'));
    }

    public function updateWebsite(Request $request)
    {
        $validated = $request->validate([
            'site_name' => ['required', 'string', 'max:100'],
            'site_tagline' => ['nullable', 'string', 'max:180'],
            'support_email' => ['nullable', 'email', 'max:190'],
            'support_phone' => ['nullable', 'string', 'max:40'],
            'announcement' => ['nullable', 'string', 'max:240'],
        ]);

        PlatformSetting::setValues($validated);

        return back()->with('success', 'Website settings saved and published.');
    }
}
