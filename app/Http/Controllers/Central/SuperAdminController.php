<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Models\Central\Tenant;
use App\Models\Central\SubscriptionPackage;
use App\Models\Tenant\CmsPage;
use App\Models\Tenant\SiteSetting;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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

    public function profile()
    {
        $user = Auth::guard('super_admin')->user();

        return view('central.profile.edit', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::guard('super_admin')->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];

        if (! empty($validated['password'])) {
            $user->password = $validated['password'];
        }

        $user->save();

        return redirect()->route('super-admin.profile.edit')->with('success', 'Profile updated successfully.');
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
            'school_logo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'school_address' => 'nullable|string|max:1000',
            'school_phone' => 'nullable|string|max:50',
            'school_email' => 'nullable|email|max:255',
            'domain' => 'required|string|max:255|unique:domains,domain',
            'package_id' => 'required|exists:subscription_packages,id',
        ]);

        if ($request->hasFile('school_logo')) {
            $validated['school_logo'] = $request->file('school_logo')->store('school-logos', 'public');
        }

        $tenant = Tenant::create([
            'name' => $validated['name'],
            'school_logo' => $validated['school_logo'] ?? null,
            'school_address' => $validated['school_address'] ?? null,
            'school_phone' => $validated['school_phone'] ?? null,
            'school_email' => $validated['school_email'] ?? null,
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
            'school_logo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'school_address' => 'nullable|string|max:1000',
            'school_phone' => 'nullable|string|max:50',
            'school_email' => 'nullable|email|max:255',
            'subscription_active' => 'boolean',
            'subscription_ends_at' => 'nullable|date',
        ]);

        if ($request->hasFile('school_logo')) {
            if ($tenant->school_logo) {
                Storage::disk('public')->delete($tenant->school_logo);
            }

            $validated['school_logo'] = $request->file('school_logo')->store('school-logos', 'public');
        }

        $tenant->update($validated);
        return redirect()->route('super-admin.tenants.index')->with('success', 'School updated successfully.');
    }

    public function destroy($id)
    {
        $tenant = Tenant::findOrFail($id);
        $tenant->delete();
        return redirect()->route('super-admin.tenants.index')->with('success', 'Tenant deleted successfully.');
    }

    public function website($id)
    {
        $tenant = Tenant::findOrFail($id);
        tenancy()->initialize($tenant);

        $settings = SiteSetting::first();
        $pages = CmsPage::orderBy('sort_order')->get();

        return view('central.tenants.website', compact('tenant', 'settings', 'pages'));
    }

    public function updateWebsiteSettings(Request $request, $id)
    {
        $tenant = Tenant::findOrFail($id);
        tenancy()->initialize($tenant);

        $validated = $request->validate([
            'site_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:1000',
            'footer_text' => 'nullable|string|max:255',
        ]);

        SiteSetting::updateOrCreate(['id' => 1], $validated);
        tenancy()->end();

        return redirect()->route('super-admin.tenants.website', $tenant->id)->with('success', 'Website settings updated successfully.');
    }

    public function storeCmsPage(Request $request, $id)
    {
        $tenant = Tenant::findOrFail($id);
        tenancy()->initialize($tenant);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'is_published' => 'boolean',
        ]);

        CmsPage::create([
            'title' => $validated['title'],
            'slug' => $validated['slug'] ? Str::slug($validated['slug']) : Str::slug($validated['title']),
            'type' => 'page',
            'content' => $validated['content'] ?? '',
            'is_published' => $request->boolean('is_published'),
            'sort_order' => (CmsPage::max('sort_order') ?? 0) + 1,
        ]);

        tenancy()->end();

        return redirect()->route('super-admin.tenants.website', $tenant->id)->with('success', 'CMS page created successfully.');
    }

    public function updateCmsPage(Request $request, $id, $page)
    {
        $tenant = Tenant::findOrFail($id);
        tenancy()->initialize($tenant);

        $page = CmsPage::findOrFail($page->id);
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => ['required', 'string', 'max:255', Rule::unique('cms_pages')->ignore($page->id)],
            'content' => 'nullable|string',
            'is_published' => 'boolean',
        ]);

        $page->update([
            'title' => $validated['title'],
            'slug' => Str::slug($validated['slug']),
            'content' => $validated['content'] ?? '',
            'is_published' => $request->boolean('is_published'),
        ]);

        tenancy()->end();

        return redirect()->route('super-admin.tenants.website', $tenant->id)->with('success', 'CMS page updated successfully.');
    }

    public function destroyCmsPage($id, $page)
    {
        $tenant = Tenant::findOrFail($id);
        tenancy()->initialize($tenant);

        CmsPage::whereKey($page)->delete();
        tenancy()->end();

        return redirect()->route('super-admin.tenants.website', $tenant->id)->with('success', 'CMS page deleted successfully.');
    }
}
