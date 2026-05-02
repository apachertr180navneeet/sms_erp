<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use Illuminate\Http\Request;

class SchoolClassController extends Controller
{
    public function index(Request $request)
    {
        $query = SchoolClass::with(['school', 'teacher', 'students', 'sections']);

        if ($request->has('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        return response()->json([
            'classes' => $query->latest()->paginate($request->per_page ?? 15),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'section' => 'nullable|string|max:255',
            'capacity' => 'nullable|integer|min:0',
            'teacher_id' => 'nullable|exists:teachers,id',
            'description' => 'nullable|string',
        ]);

        $class = SchoolClass::create($request->only(['name', 'section', 'capacity', 'teacher_id', 'description']));

        return response()->json([
            'message' => 'Class created successfully',
            'class' => $class->load(['school', 'teacher']),
        ], 201);
    }

    public function show(SchoolClass $schoolClass)
    {
        return response()->json([
            'class' => $schoolClass->load(['school', 'teacher', 'students', 'sections']),
        ]);
    }

    public function update(Request $request, SchoolClass $schoolClass)
    {
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'section' => 'nullable|string|max:255',
            'capacity' => 'nullable|integer|min:0',
            'teacher_id' => 'nullable|exists:teachers,id',
            'description' => 'nullable|string',
        ]);

        $schoolClass->update($request->only(['name', 'section', 'capacity', 'teacher_id', 'description']));

        return response()->json([
            'message' => 'Class updated successfully',
            'class' => $schoolClass->load(['school', 'teacher']),
        ]);
    }

    public function destroy(SchoolClass $schoolClass)
    {
        $schoolClass->delete();
        return response()->json(['message' => 'Class deleted successfully']);
    }
}
