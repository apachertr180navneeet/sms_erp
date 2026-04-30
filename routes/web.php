<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Central\SuperAdminController;
use App\Http\Controllers\Central\SubscriptionPackageController;
use App\Http\Controllers\Tenant\WebsiteController;
use Stancl\Tenancy\Database\Models\Domain;

function initializeTenantFromRequest(): bool
{
    $host = request()->getHost();

    if (in_array($host, config('tenancy.central_domains'), true)) {
        return false;
    }

    $domain = Domain::where('domain', $host)->first();

    if (! $domain) {
        return false;
    }

    tenancy()->initialize($domain->tenant);

    return true;
}

Route::get('/', function () {
    if (initializeTenantFromRequest()) {
        return app(WebsiteController::class)->home();
    }

    return view('central.welcome');
});

Route::get('/contact', function () {
    abort_unless(initializeTenantFromRequest(), 404);

    return app(WebsiteController::class)->contact();
});

Route::get('/{slug}', function (string $slug) {
    abort_unless(initializeTenantFromRequest(), 404);

    return app(WebsiteController::class)->page($slug);
})->where('slug', '^(?!super-admin|livewire|storage|tenancy|up).+');

// Authentication Routes
Route::middleware('guest:super_admin')->group(function () {
    Route::get('/super-admin/login', [SuperAdminController::class, 'login'])->name('super-admin.login');
    Route::post('/super-admin/login', [SuperAdminController::class, 'authenticate'])->name('super-admin.login.authenticate');
});

Route::middleware('auth:super_admin')->group(function () {
    Route::post('/super-admin/logout', [SuperAdminController::class, 'logout'])->name('super-admin.logout');

    Route::prefix('super-admin')->name('super-admin.')->group(function () {
        Route::get('/dashboard', [SuperAdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/profile', [SuperAdminController::class, 'profile'])->name('profile.edit');
        Route::put('/profile', [SuperAdminController::class, 'updateProfile'])->name('profile.update');

        // School routes
        Route::get('/tenants', [SuperAdminController::class, 'index'])->name('tenants.index');
        Route::get('/tenants/create', [SuperAdminController::class, 'create'])->name('tenants.create');
        Route::post('/tenants', [SuperAdminController::class, 'store'])->name('tenants.store');
        Route::get('/tenants/{id}/edit', [SuperAdminController::class, 'edit'])->name('tenants.edit');
        Route::put('/tenants/{id}', [SuperAdminController::class, 'update'])->name('tenants.update');
        Route::delete('/tenants/{id}', [SuperAdminController::class, 'destroy'])->name('tenants.destroy');
        Route::get('/tenants/{id}/website', [SuperAdminController::class, 'website'])->name('tenants.website');
        Route::put('/tenants/{id}/website/settings', [SuperAdminController::class, 'updateWebsiteSettings'])->name('tenants.website.settings.update');
        Route::post('/tenants/{id}/website/pages', [SuperAdminController::class, 'storeCmsPage'])->name('tenants.website.pages.store');
        Route::put('/tenants/{id}/website/pages/{page}', [SuperAdminController::class, 'updateCmsPage'])->name('tenants.website.pages.update');
        Route::delete('/tenants/{id}/website/pages/{page}', [SuperAdminController::class, 'destroyCmsPage'])->name('tenants.website.pages.destroy');

        // Subscription Package routes
        Route::get('/subscription-packages', [SubscriptionPackageController::class, 'index'])->name('subscription-packages.index');
        Route::get('/subscription-packages/create', [SubscriptionPackageController::class, 'create'])->name('subscription-packages.create');
        Route::post('/subscription-packages', [SubscriptionPackageController::class, 'store'])->name('subscription-packages.store');
        Route::get('/subscription-packages/{subscription_package}/edit', [SubscriptionPackageController::class, 'edit'])->name('subscription-packages.edit');
        Route::put('/subscription-packages/{subscription_package}', [SubscriptionPackageController::class, 'update'])->name('subscription-packages.update');
        Route::delete('/subscription-packages/{subscription_package}', [SubscriptionPackageController::class, 'destroy'])->name('subscription-packages.destroy');
    });
});
