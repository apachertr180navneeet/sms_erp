<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\School;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubscriptionController extends Controller
{
    public function index(Request $request)
    {
        $query = Subscription::with(['school', 'plan'])->latest();

        if ($request->has('school_id')) {
            $query->where('school_id', $request->school_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        return response()->json([
            'subscriptions' => $query->paginate($request->per_page ?? 15),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'school_id' => 'required|exists:schools,id',
            'plan_id' => 'required|exists:plans,id',
            'amount' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'trial_ends_at' => 'nullable|date|after:start_date',
            'payment_method' => 'nullable|string',
            'transaction_id' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        return DB::transaction(function () use ($request) {
            $subscription = Subscription::create($request->only([
                'school_id', 'plan_id', 'amount', 'start_date', 'end_date',
                'trial_ends_at', 'payment_method', 'transaction_id', 'notes',
            ]));

            $school = School::findOrFail($request->school_id);
            $school->update([
                'plan_id' => $request->plan_id,
                'subscription_ends_at' => $request->end_date,
                'is_active' => true,
            ]);

            $plan = $school->plan;
            if ($plan) {
                $moduleIds = $plan->modules()->pluck('modules.id')->toArray();
                $school->modules()->syncWithPivotValues($moduleIds, ['is_enabled' => true]);
            }

            return response()->json([
                'message' => 'Subscription created successfully',
                'subscription' => $subscription->load(['school', 'plan']),
            ], 201);
        });
    }

    public function show(Subscription $subscription)
    {
        return response()->json([
            'subscription' => $subscription->load(['school', 'plan']),
        ]);
    }

    public function update(Request $request, Subscription $subscription)
    {
        $request->validate([
            'plan_id' => 'sometimes|exists:plans,id',
            'amount' => 'sometimes|numeric|min:0',
            'status' => 'sometimes|in:active,expired,cancelled,trial',
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date',
            'notes' => 'nullable|string',
        ]);

        $subscription->update($request->only([
            'plan_id', 'amount', 'status', 'start_date', 'end_date', 'notes',
        ]));

        if ($request->has('plan_id')) {
            $school = $subscription->school;
            $school->update(['plan_id' => $request->plan_id]);
        }

        if ($request->has('end_date')) {
            $subscription->school->update(['subscription_ends_at' => $request->end_date]);
        }

        return response()->json([
            'message' => 'Subscription updated',
            'subscription' => $subscription->fresh()->load(['school', 'plan']),
        ]);
    }

    public function destroy(Subscription $subscription)
    {
        $subscription->delete();
        return response()->json(['message' => 'Subscription deleted']);
    }

    public function checkExpiry()
    {
        $expired = Subscription::where('status', 'active')
            ->where('end_date', '<=', now())
            ->get();

        foreach ($expired as $sub) {
            $sub->update(['status' => 'expired']);

            $school = $sub->school;
            $school->update([
                'is_active' => false,
                'subscription_ends_at' => $sub->end_date,
            ]);

            $school->modules()->updateExistingPivot(
                $school->modules()->pluck('modules.id'),
                ['is_enabled' => false]
            );
        }

        return response()->json([
            'message' => 'Checked ' . $expired->count() . ' expired subscriptions',
            'expired_count' => $expired->count(),
        ]);
    }

    public function schoolModules(School $school)
    {
        $modules = Module::all()->map(function ($module) use ($school) {
            $isEnabled = $school->modules()
                ->where('modules.id', $module->id)
                ->wherePivot('is_enabled', true)
                ->exists();

            return [
                'id' => $module->id,
                'name' => $module->name,
                'slug' => $module->slug,
                'icon' => $module->icon,
                'is_enabled' => $isEnabled,
            ];
        });

        return response()->json([
            'school' => $school->only(['id', 'name', 'code']),
            'subscription_active' => $school->isSubscriptionActive(),
            'subscription_ends_at' => $school->subscription_ends_at,
            'modules' => $modules,
        ]);
    }

    public function updateSchoolModules(Request $request, School $school)
    {
        $request->validate([
            'modules' => 'required|array',
            'modules.*.id' => 'required|exists:modules,id',
            'modules.*.is_enabled' => 'required|boolean',
        ]);

        foreach ($request->modules as $moduleData) {
            $school->modules()->updateExistingPivot(
                $moduleData['id'],
                ['is_enabled' => $moduleData['is_enabled']]
            );
        }

        return response()->json([
            'message' => 'School modules updated',
            'modules' => $school->modules()->get(),
        ]);
    }

    public function stats()
    {
        return response()->json([
            'total' => Subscription::count(),
            'active' => Subscription::where('status', 'active')->where('end_date', '>', now())->count(),
            'expired' => Subscription::where('status', 'expired')->orWhere('end_date', '<=', now())->count(),
            'trial' => Subscription::where('status', 'trial')->count(),
            'total_revenue' => Subscription::where('status', 'active')->sum('amount'),
        ]);
    }
}
