<?php

namespace App\Http\Middleware;

use App\Models\School;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DetectTenant
{
    public function handle(Request $request, Closure $next): Response
    {
        $subdomain = $this->resolveSubdomain($request);

        if ($subdomain) {
            $school = School::where('subdomain', $subdomain)->first();

            if (!$school) {
                return response()->json(['message' => 'School not found'], 404);
            }

            if (!$school->is_active) {
                return response()->json(['message' => 'School is inactive'], 403);
            }

            $request->merge(['school_id' => $school->id]);
            config(['app.tenant.school' => $school]);
        }

        return $next($request);
    }

    protected function resolveSubdomain(Request $request): ?string
    {
        // Priority 1: X-School-Subdomain header (for frontend subdomain detection)
        $headerSubdomain = $request->header('X-School-Subdomain');
        if ($headerSubdomain) {
            return $headerSubdomain;
        }

        // Priority 2: Extract from Host header (for direct subdomain access)
        $host = $request->getHost();
        $parts = explode('.', $host);

        // subdomain.domain.tld => 3+ parts
        if (count($parts) >= 3) {
            return $parts[0];
        }

        return null;
    }
}
