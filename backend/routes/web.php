<?php

use App\Http\Middleware\DetectTenant;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::domain('{subdomain}.' . parse_url(config('app.url'), PHP_URL_HOST))->middleware(DetectTenant::class)->group(function () {

    Route::get('/', function ($subdomain) {
        $school = config('app.tenant.school');
        return response()->json([
            'message' => 'Welcome to ' . $school->name,
            'school' => $school,
        ]);
    });

    Route::get('/api/{any}', function ($subdomain) {
        return response()->json([
            'message' => 'API requests should go through api.php routes with DetectTenant middleware',
            'tenant' => config('app.tenant.school'),
        ]);
    })->where('any', '.*');
});
