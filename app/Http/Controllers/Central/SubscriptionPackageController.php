<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Models\Central\SubscriptionPackage;
use Illuminate\Http\Request;

class SubscriptionPackageController extends Controller
{
    public function index()
    {
        $packages = SubscriptionPackage::paginate(10);
        return view('central.subscription-packages.index', compact('packages'));
    }

    public function create()
    {
        return view('central.subscription-packages.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:subscription_packages',
            'description' => 'nullable|string',
            'price_monthly' => 'required|numeric|min:0',
            'price_yearly' => 'required|numeric|min:0',
            'features' => 'nullable|string',
            'max_students' => 'nullable|integer|min:0',
            'max_teachers' => 'nullable|integer|min:0',
        ]);

        $validated['features'] = $this->parseFeatures($validated['features'] ?? null);

        SubscriptionPackage::create($validated);

        return redirect()->route('super-admin.subscription-packages.index')
            ->with('success', 'Subscription package created successfully.');
    }

    public function edit(SubscriptionPackage $subscription_package)
    {
        return view('central.subscription-packages.edit', compact('subscription_package'));
    }

    public function update(Request $request, SubscriptionPackage $subscription_package)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:subscription_packages,name,' . $subscription_package->id,
            'description' => 'nullable|string',
            'price_monthly' => 'required|numeric|min:0',
            'price_yearly' => 'required|numeric|min:0',
            'features' => 'nullable|string',
            'max_students' => 'nullable|integer|min:0',
            'max_teachers' => 'nullable|integer|min:0',
        ]);

        $validated['features'] = $this->parseFeatures($validated['features'] ?? null);

        $subscription_package->update($validated);

        return redirect()->route('super-admin.subscription-packages.index')
            ->with('success', 'Subscription package updated successfully.');
    }

    public function destroy(SubscriptionPackage $subscription_package)
    {
        $subscription_package->delete();

        return redirect()->route('super-admin.subscription-packages.index')
            ->with('success', 'Subscription package deleted successfully.');
    }

    private function parseFeatures(?string $features): array
    {
        return collect(preg_split('/\r\n|\r|\n/', $features ?? ''))
            ->map(fn ($feature) => trim($feature))
            ->filter()
            ->values()
            ->all();
    }
}
