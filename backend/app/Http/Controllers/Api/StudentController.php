<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::with(['school', 'schoolClass', 'section', 'user']);

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('admission_no', 'like', "%{$search}%")
                  ->orWhere('roll_no', 'like', "%{$search}%")
                  ->orWhere('parent_name', 'like', "%{$search}%")
                  ->orWhere('parent_phone', 'like', "%{$search}%");
            });
        }

        if ($request->has('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        if ($request->has('section_id')) {
            $query->where('section_id', $request->section_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        return response()->json([
            'students' => $query->latest()->paginate($request->per_page ?? 15),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'class_id' => 'nullable|exists:school_classes,id',
            'section_id' => 'nullable|exists:sections,id',
            'roll_no' => 'nullable|string|max:20',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:male,female,other',
            'blood_group' => 'nullable|string|max:10',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'parent_name' => 'nullable|string|max:255',
            'parent_phone' => 'nullable|string|max:20',
            'parent_email' => 'nullable|email|max:255',
            'admission_date' => 'required|date',
            'photo' => 'nullable|string',
            'status' => 'sometimes|in:active,inactive,graduated',
        ]);

        $data = $request->only([
            'user_id', 'class_id', 'section_id', 'roll_no', 'date_of_birth',
            'gender', 'blood_group', 'phone', 'address', 'parent_name',
            'parent_phone', 'parent_email', 'admission_date', 'photo',
        ]);

        $data['admission_no'] = $request->admission_no ?? 'ADM-' . strtoupper(Str::random(6));

        $student = Student::create($data);

        return response()->json([
            'message' => 'Student created successfully',
            'student' => $student->load(['school', 'schoolClass', 'section']),
        ], 201);
    }

    public function show(Student $student)
    {
        return response()->json([
            'student' => $student->load(['school', 'schoolClass', 'section', 'user']),
        ]);
    }

    public function update(Request $request, Student $student)
    {
        $request->validate([
            'class_id' => 'nullable|exists:school_classes,id',
            'section_id' => 'nullable|exists:sections,id',
            'roll_no' => 'nullable|string|max:20',
            'date_of_birth' => 'sometimes|date',
            'gender' => 'sometimes|in:male,female,other',
            'blood_group' => 'nullable|string|max:10',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'parent_name' => 'nullable|string|max:255',
            'parent_phone' => 'nullable|string|max:20',
            'parent_email' => 'nullable|email|max:255',
            'admission_date' => 'sometimes|date',
            'photo' => 'nullable|string',
            'status' => 'sometimes|in:active,inactive,graduated',
        ]);

        $student->update($request->only([
            'class_id', 'section_id', 'roll_no', 'date_of_birth', 'gender',
            'blood_group', 'phone', 'address', 'parent_name', 'parent_phone',
            'parent_email', 'admission_date', 'photo', 'status',
        ]));

        return response()->json([
            'message' => 'Student updated successfully',
            'student' => $student->load(['school', 'schoolClass', 'section']),
        ]);
    }

    public function destroy(Student $student)
    {
        $student->delete();
        return response()->json(['message' => 'Student deleted successfully']);
    }

    public function stats()
    {
        return response()->json([
            'total' => Student::count(),
            'active' => Student::where('status', 'active')->count(),
            'inactive' => Student::where('status', 'inactive')->count(),
            'graduated' => Student::where('status', 'graduated')->count(),
        ]);
    }
}
