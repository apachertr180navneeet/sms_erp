<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Stancl\Tenancy\Tenancy;

class EnsureTenantIsolation
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!tenancy()->initialized) {
            return $next($request);
        }

        $tenant = tenancy()->tenant;
        $user = $request->user();

        if ($user && method_exists($user, 'belongsToTenant')) {
            if (!$user->belongsToTenant($tenant)) {
                abort(403, 'Unauthorized action - tenant isolation violation.');
            }
        }

        return $next($request);
    }
}
