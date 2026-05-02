<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Section;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    public function index(Request $request)
    {
        $query = Section::with(['school', 'schoolClass', 'students']);

        if ($request->has('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        return response()->json([
            'sections' => $query->latest()->paginate($request->per_page ?? 15),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:school_classes,id',
            'name' => 'required|string|max:255',
            'capacity' => 'nullable|integer|min:0',
            'room_number' => 'nullable|string|max:255',
        ]);

        $section = Section::create($request->only(['class_id', 'name', 'capacity', 'room_number']));

        return response()->json([
            'message' => 'Section created successfully',
            'section' => $section->load(['school', 'schoolClass']),
        ], 201);
    }

    public function show(Section $section)
    {
        return response()->json([
            'section' => $section->load(['school', 'schoolClass', 'students']),
        ]);
    }

    public function update(Request $request, Section $section)
    {
        $request->validate([
            'class_id' => 'sometimes|exists:school_classes,id',
            'name' => 'sometimes|string|max:255',
            'capacity' => 'nullable|integer|min:0',
            'room_number' => 'nullable|string|max:255',
        ]);

        $section->update($request->only(['class_id', 'name', 'capacity', 'room_number']));

        return response()->json([
            'message' => 'Section updated successfully',
            'section' => $section->load(['school', 'schoolClass']),
        ]);
    }

    public function destroy(Section $section)
    {
        $section->delete();
        return response()->json(['message' => 'Section deleted successfully']);
    }
}
