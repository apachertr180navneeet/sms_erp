<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Stancl\Tenancy\Tenancy;

class CheckTenantSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $tenant = tenancy()->tenant;

        if (!$tenant) {
            return $next($request);
        }

        if (!$tenant->subscription_active) {
            return redirect()->route('subscription.expired');
        }

        if ($tenant->subscription_ends_at && $tenant->subscription_ends_at->isPast()) {
            return redirect()->route('subscription.expired');
        }

        return $next($request);
    }
}
