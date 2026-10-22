<?php

namespace App\Http\Middleware;

use App\Support\CurrentShop;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureUserBelongsToShop
{
    public function handle(Request $request, Closure $next)
    {
        $shop = CurrentShop::get();
        $user = Auth::user();

        if (!$shop || !$user) {
            abort(404);
        }

        // Platform admins can access any shop panel.
        if (!empty($user->is_platform_admin)) {
            return $next($request);
        }

        if ((int) $user->shop_id !== (int) $shop->id) {
            Auth::logout();

            return redirect()
                ->route('login', ['shop' => $shop->slug])
                ->with('error', 'This account does not belong to this shop.');
        }

        return $next($request);
    }
}
