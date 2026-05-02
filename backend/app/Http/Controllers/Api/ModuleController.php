<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ModuleController extends Controller
{
    public function index()
    {
        return response()->json([
            'modules' => Module::orderBy('sort_order')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:modules,name',
            'description' => 'nullable|string',
            'icon' => 'nullable|string',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ]);

        $module = Module::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'icon' => $request->icon,
            'is_active' => $request->is_active ?? true,
            'sort_order' => $request->sort_order ?? 0,
        ]);

        return response()->json(['message' => 'Module created', 'module' => $module], 201);
    }

    public function show(Module $module)
    {
        return response()->json(['module' => $module->load('schools')]);
    }

    public function update(Request $request, Module $module)
    {
        $request->validate([
            'name' => 'sometimes|string|max:255|unique:modules,name,' . $module->id,
            'description' => 'nullable|string',
            'icon' => 'nullable|string',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ]);

        $module->update($request->only(['name', 'description', 'icon', 'is_active', 'sort_order']));

        if ($request->has('name')) {
            $module->update(['slug' => Str::slug($request->name)]);
        }

        return response()->json(['message' => 'Module updated', 'module' => $module]);
    }

    public function destroy(Module $module)
    {
        $module->delete();
        return response()->json(['message' => 'Module deleted']);
    }

    public function assignToSchool(Request $request)
    {
        $request->validate([
            'school_id' => 'required|exists:schools,id',
            'module_ids' => 'required|array',
            'module_ids.*' => 'exists:modules,id',
        ]);

        $school = \App\Models\School::findOrFail($request->school_id);
        $school->modules()->sync($request->module_ids);

        return response()->json(['message' => 'Modules assigned to school']);
    }
}
