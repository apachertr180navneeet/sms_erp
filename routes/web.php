<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Central\SuperAdminController;
use App\Http\Controllers\Central\SubscriptionPackageController;
use Stancl\Tenancy\Database\Models\Domain;

Route::get('/', function () {
    $host = request()->getHost();

    if (! in_array($host, config('tenancy.central_domains'), true)) {
        $domain = Domain::where('domain', $host)->first();

        if ($domain) {
            tenancy()->initialize($domain->tenant);

            return 'This is your multi-tenant application. The id of the current tenant is ' . tenant('id');
        }
    }

    return view('central.welcome');
});

// Authentication Routes
Route::middleware('guest:super_admin')->group(function () {
    Route::get('/super-admin/login', [SuperAdminController::class, 'login'])->name('super-admin.login');
    Route::post('/super-admin/login', [SuperAdminController::class, 'authenticate'])->name('super-admin.login.authenticate');
});

Route::middleware('auth:super_admin')->group(function () {
    Route::post('/super-admin/logout', [SuperAdminController::class, 'logout'])->name('super-admin.logout');

    Route::prefix('super-admin')->name('super-admin.')->group(function () {
        Route::get('/dashboard', [SuperAdminController::class, 'dashboard'])->name('dashboard');

        // Tenant routes
        Route::get('/tenants', [SuperAdminController::class, 'index'])->name('tenants.index');
        Route::get('/tenants/create', [SuperAdminController::class, 'create'])->name('tenants.create');
        Route::post('/tenants', [SuperAdminController::class, 'store'])->name('tenants.store');
        Route::get('/tenants/{id}/edit', [SuperAdminController::class, 'edit'])->name('tenants.edit');
        Route::put('/tenants/{id}', [SuperAdminController::class, 'update'])->name('tenants.update');
        Route::delete('/tenants/{id}', [SuperAdminController::class, 'destroy'])->name('tenants.destroy');

        // Subscription Package routes
        Route::get('/subscription-packages', [SubscriptionPackageController::class, 'index'])->name('subscription-packages.index');
        Route::get('/subscription-packages/create', [SubscriptionPackageController::class, 'create'])->name('subscription-packages.create');
        Route::post('/subscription-packages', [SubscriptionPackageController::class, 'store'])->name('subscription-packages.store');
        Route::get('/subscription-packages/{subscription_package}/edit', [SubscriptionPackageController::class, 'edit'])->name('subscription-packages.edit');
        Route::put('/subscription-packages/{subscription_package}', [SubscriptionPackageController::class, 'update'])->name('subscription-packages.update');
        Route::delete('/subscription-packages/{subscription_package}', [SubscriptionPackageController::class, 'destroy'])->name('subscription-packages.destroy');
    });
});
