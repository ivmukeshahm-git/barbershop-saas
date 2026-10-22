<?php

namespace App\Http\Middleware;

use App\Models\SaasShop;
use App\Services\Saas\PlanEntitlementService;
use App\Support\CurrentShop;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class ResolveShopTenant
{
    public function handle(Request $request, Closure $next)
    {
        $slug = $request->route('shop');

        if (!$slug || in_array($slug, SaasShop::RESERVED_SLUGS, true)) {
            abort(404);
        }

        $shop = SaasShop::where('slug', $slug)->first();

        if (!$shop) {
            abort(404, 'Shop not found.');
        }

        CurrentShop::set($shop);
        URL::defaults(['shop' => $shop->slug]);

        $entitlements = app(PlanEntitlementService::class);
        $usage = $entitlements->usageSummary($shop);

        view()->share('currentShop', $shop);
        view()->share('shopLogo', $shop->logoUrl());
        view()->share('shopName', $shop->name);
        view()->share('shopTheme', $shop->themeColor());
        view()->share('currentPlan', $usage['plan']);
        view()->share('currentSubscription', $entitlements->subscription($shop));
        view()->share('planUsage', $usage);

        return $next($request);
    }
}
