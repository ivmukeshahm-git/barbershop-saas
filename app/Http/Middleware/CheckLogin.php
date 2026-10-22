<?php

namespace App\Http\Middleware;

use App\Support\CurrentShop;
use Closure;
use Illuminate\Support\Facades\Auth;

class CheckLogin
{
    /**
     * Handle an incoming request.
     */
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            return $next($request);
        }

        Auth::logout();

        $shop = CurrentShop::get();
        if ($shop) {
            return redirect()
                ->route('login', ['shop' => $shop->slug])
                ->with('error', 'Your session has expired. Please login again.');
        }

        return redirect()
            ->route('saas.find-shop')
            ->with('error', 'Your session has expired. Please login again.');
    }
}
