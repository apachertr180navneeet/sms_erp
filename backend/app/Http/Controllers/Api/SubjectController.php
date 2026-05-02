<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SubjectController extends Controller
{
    public function index(Request $request)
    {
        $query = Subject::with(['school', 'schoolClass', 'teacher', 'teachers']);

        if ($request->has('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        if ($request->has('teacher_id')) {
            $query->whereHas('teachers', function ($q) use ($request) {
                $q->where('teacher_id', $request->teacher_id);
            });
        }

        return response()->json([
            'subjects' => $query->latest()->paginate($request->per_page ?? 15),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:subjects,code',
            'class_id' => 'nullable|exists:school_classes,id',
            'teacher_id' => 'nullable|exists:teachers,id',
            'description' => 'nullable|string',
        ]);

        $subject = Subject::create($request->only(['name', 'code', 'class_id', 'teacher_id', 'description']));

        return response()->json([
            'message' => 'Subject created successfully',
            'subject' => $subject->load(['school', 'schoolClass', 'teacher']),
        ], 201);
    }

    public function show(Subject $subject)
    {
        return response()->json([
            'subject' => $subject->load(['school', 'schoolClass', 'teacher', 'teachers']),
        ]);
    }

    public function update(Request $request, Subject $subject)
    {
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'code' => ['sometimes', 'string', 'max:50', Rule::unique('subjects')->ignore($subject->id)],
            'class_id' => 'nullable|exists:school_classes,id',
            'teacher_id' => 'nullable|exists:teachers,id',
            'description' => 'nullable|string',
        ]);

        $subject->update($request->only(['name', 'code', 'class_id', 'teacher_id', 'description']));

        return response()->json([
            'message' => 'Subject updated successfully',
            'subject' => $subject->load(['school', 'schoolClass', 'teacher']),
        ]);
    }

    public function destroy(Subject $subject)
    {
        $subject->delete();
        return response()->json(['message' => 'Subject deleted successfully']);
    }
}
