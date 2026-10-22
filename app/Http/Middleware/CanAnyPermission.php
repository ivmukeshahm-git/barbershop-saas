<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CanAnyPermission
{
    public function handle(Request $request, Closure $next, ...$permissions)
    {
        if (!auth()->check() || !auth()->user()->canAny($permissions)) {
            abort(403, 'Unauthorized.');
        }

        return $next($request);
    }
}
