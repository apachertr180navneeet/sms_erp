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
        $host = $request->getHost();
        $subdomain = $this->extractSubdomain($host);

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

    protected function extractSubdomain(string $host): ?string
    {
        $parts = explode('.', $host);

        if (count($parts) < 3) {
            return null;
        }

        return $parts[0];
    }
}
