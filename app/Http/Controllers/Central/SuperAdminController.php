<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Models\Central\Tenant;
use App\Models\Central\SubscriptionPackage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuperAdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_tenants' => Tenant::count(),
            'active_tenants' => Tenant::where('subscription_active', true)->count(),
            'total_revenue' => Tenant::sum('subscription_amount') ?? 0,
            'packages' => SubscriptionPackage::count(),
        ];
        return view('central.dashboard', compact('stats'));
    }

    public function login()
    {
        return view('central.auth.login');
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $credentials['role'] = 'super_admin';

        if (Auth::guard('super_admin')->attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('super-admin.dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::guard('super_admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('super-admin.login');
    }

    public function index()
    {
        $tenants = Tenant::with('domains')->paginate(10);
        return view('central.tenants.index', compact('tenants'));
    }

    public function create()
    {
        $packages = SubscriptionPackage::where('is_active', true)->get();
        return view('central.tenants.create', compact('packages'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'domain' => 'required|string|max:255|unique:domains,domain',
            'package_id' => 'required|exists:subscription_packages,id',
        ]);

        $tenant = Tenant::create([
            'name' => $validated['name'],
            'subscription_package_id' => $validated['package_id'],
            'subscription_active' => true,
            'subscription_starts_at' => now(),
            'subscription_ends_at' => now()->addMonth(),
        ]);

        $tenant->domains()->create(['domain' => $validated['domain']]);

        return redirect()->route('super-admin.tenants.index')->with('success', 'Tenant created successfully.');
    }

    public function edit($id)
    {
        $tenant = Tenant::findOrFail($id);
        $packages = SubscriptionPackage::where('is_active', true)->get();
        return view('central.tenants.edit', compact('tenant', 'packages'));
    }

    public function update(Request $request, $id)
    {
        $tenant = Tenant::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subscription_active' => 'boolean',
            'subscription_ends_at' => 'nullable|date',
        ]);
        $tenant->update($validated);
        return redirect()->route('super-admin.tenants.index')->with('success', 'Tenant updated successfully.');
    }

    public function destroy($id)
    {
        $tenant = Tenant::findOrFail($id);
        $tenant->delete();
        return redirect()->route('super-admin.tenants.index')->with('success', 'Tenant deleted successfully.');
    }
}
