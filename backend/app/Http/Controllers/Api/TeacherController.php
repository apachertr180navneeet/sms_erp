<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TeacherController extends Controller
{
    public function index(Request $request)
    {
        $query = Teacher::with(['school', 'user', 'classes', 'subjects']);

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('employee_id', 'like', "%{$search}%")
                  ->orWhere('specialization', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        return response()->json([
            'teachers' => $query->latest()->paginate($request->per_page ?? 15),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'qualification' => 'nullable|string|max:255',
            'experience' => 'nullable|integer|min:0',
            'specialization' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'joining_date' => 'required|date',
            'salary' => 'nullable|numeric|min:0',
            'photo' => 'nullable|string',
            'status' => 'sometimes|in:active,inactive',
        ]);

        $data = $request->only([
            'user_id', 'qualification', 'experience', 'specialization',
            'phone', 'address', 'date_of_birth', 'gender', 'joining_date',
            'salary', 'photo', 'status',
        ]);

        $data['employee_id'] = $request->employee_id ?? 'TCH-' . strtoupper(Str::random(6));

        $teacher = Teacher::create($data);

        return response()->json([
            'message' => 'Teacher created successfully',
            'teacher' => $teacher->load(['school', 'user']),
        ], 201);
    }

    public function show(Teacher $teacher)
    {
        return response()->json([
            'teacher' => $teacher->load(['school', 'user', 'classes', 'subjects']),
        ]);
    }

    public function update(Request $request, Teacher $teacher)
    {
        $request->validate([
            'qualification' => 'nullable|string|max:255',
            'experience' => 'nullable|integer|min:0',
            'specialization' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'joining_date' => 'sometimes|date',
            'salary' => 'nullable|numeric|min:0',
            'photo' => 'nullable|string',
            'status' => 'sometimes|in:active,inactive',
        ]);

        $teacher->update($request->only([
            'qualification', 'experience', 'specialization', 'phone',
            'address', 'date_of_birth', 'gender', 'joining_date',
            'salary', 'photo', 'status',
        ]));

        return response()->json([
            'message' => 'Teacher updated successfully',
            'teacher' => $teacher->load(['school', 'user']),
        ]);
    }

    public function destroy(Teacher $teacher)
    {
        $teacher->delete();
        return response()->json(['message' => 'Teacher deleted successfully']);
    }

    public function stats()
    {
        return response()->json([
            'total' => Teacher::count(),
            'active' => Teacher::where('status', 'active')->count(),
            'inactive' => Teacher::where('status', 'inactive')->count(),
        ]);
    }
}
