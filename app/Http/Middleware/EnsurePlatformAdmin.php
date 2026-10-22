<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsurePlatformAdmin
{
    public function handle(Request $request, Closure $next)
    {
        $guard = Auth::guard('platform');

        if (!$guard->check()) {
            return redirect()->route('platform.login');
        }

        if (!$guard->user()->is_active) {
            abort(403, 'Platform administrator access is required.');
        }

        return $next($request);
    }
}
