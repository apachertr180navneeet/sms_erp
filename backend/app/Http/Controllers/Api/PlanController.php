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
            'plans' => Plan::latest()->get(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'billing_cycle' => 'required|in:monthly,yearly',
            'features' => 'nullable|array',
            'modules' => 'nullable|array',
        ]);

        $plan = Plan::create($request->all());

        return response()->json(['message' => 'Plan created', 'plan' => $plan], 201);
    }

    public function show(Plan $plan)
    {
        return response()->json(['plan' => $plan]);
    }

    public function update(Request $request, Plan $plan)
    {
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'price' => 'sometimes|numeric|min:0',
            'billing_cycle' => 'sometimes|in:monthly,yearly',
            'features' => 'nullable|array',
            'modules' => 'nullable|array',
            'is_active' => 'sometimes|boolean',
        ]);

        $plan->update($request->all());

        return response()->json(['message' => 'Plan updated', 'plan' => $plan]);
    }

    public function destroy(Plan $plan)
    {
        $plan->delete();
        return response()->json(['message' => 'Plan deleted']);
    }
}
