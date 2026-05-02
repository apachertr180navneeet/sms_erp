<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function index()
    {
        return response()->json([
            'plans' => Plan::with('modules')->latest()->get(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'student_limit' => 'sometimes|integer|min:0',
            'staff_limit' => 'sometimes|integer|min:0',
            'storage_limit_mb' => 'sometimes|integer|min:0',
            'billing_cycle' => 'required|in:monthly,yearly',
            'features' => 'nullable|array',
            'module_ids' => 'nullable|array',
            'module_ids.*' => 'exists:modules,id',
        ]);

        $plan = Plan::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'student_limit' => $request->student_limit ?? 0,
            'staff_limit' => $request->staff_limit ?? 0,
            'storage_limit_mb' => $request->storage_limit_mb ?? 1024,
            'billing_cycle' => $request->billing_cycle,
            'features' => $request->features ?? [],
            'is_active' => true,
        ]);

        if ($request->has('module_ids')) {
            $plan->modules()->sync($request->module_ids);
        }

        return response()->json(['message' => 'Plan created', 'plan' => $plan->load('modules')], 201);
    }

    public function show(Plan $plan)
    {
        return response()->json(['plan' => $plan->load('modules')]);
    }

    public function update(Request $request, Plan $plan)
    {
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'price' => 'sometimes|numeric|min:0',
            'student_limit' => 'sometimes|integer|min:0',
            'staff_limit' => 'sometimes|integer|min:0',
            'storage_limit_mb' => 'sometimes|integer|min:0',
            'billing_cycle' => 'sometimes|in:monthly,yearly',
            'features' => 'nullable|array',
            'module_ids' => 'nullable|array',
            'module_ids.*' => 'exists:modules,id',
            'is_active' => 'sometimes|boolean',
        ]);

        $plan->update($request->only([
            'name', 'description', 'price', 'student_limit', 'staff_limit',
            'storage_limit_mb', 'billing_cycle', 'features', 'is_active',
        ]));

        if ($request->has('module_ids')) {
            $plan->modules()->sync($request->module_ids);
        }

        return response()->json(['message' => 'Plan updated', 'plan' => $plan->load('modules')]);
    }

    public function destroy(Plan $plan)
    {
        $plan->delete();
        return response()->json(['message' => 'Plan deleted']);
    }
}
