<?php

namespace App\Http\Middleware;

use App\Support\CurrentShop;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureShopSubscriptionActive
{
    public function handle(Request $request, Closure $next)
    {
        $shop = CurrentShop::get();

        if (!$shop) {
            abort(404);
        }

        if ($shop->status === 'suspended' || $shop->status === 'cancelled') {
            abort(403, 'This shop account is not active. Please contact support.');
        }

        $shop->syncExpiredSubscriptions();

        // The expire screen and its renew form must stay reachable.
        if ($request->routeIs('admin.plan.expired', 'admin.billing.renew')) {
            return $next($request);
        }

        if (!$shop->hasActiveSubscription()) {
            if (Auth::check()) {
                return redirect()->route('admin.plan.expired', ['shop' => $shop->slug]);
            }

            return redirect()
                ->route('login', ['shop' => $shop->slug])
                ->with('error', 'Your plan has expired. Please log in to choose a plan.');
        }

        return $next($request);
    }
}
