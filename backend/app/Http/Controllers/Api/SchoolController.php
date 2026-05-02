<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\School;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class SchoolController extends Controller
{
    public function index(Request $request)
    {
        $query = School::with(['plan', 'users' => function ($q) {
            $q->with('roles')->limit(1);
        }])->latest();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->has('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        return response()->json([
            'schools' => $query->paginate($request->per_page ?? 15),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:schools,name',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'subdomain' => ['nullable', 'string', 'max:63', 'regex:/^[a-z0-9][a-z0-9-]*$/', 'unique:schools,subdomain'],
            'url' => 'nullable|url|max:255',
            'plan_id' => 'nullable|exists:plans,id',
            'create_admin' => 'boolean',
            'admin_name' => 'required_if:create_admin,true|string|max:255',
            'admin_email' => 'required_if:create_admin,true|email|max:255|unique:users,email',
            'admin_password' => 'required_if:create_admin,true',
        ]);

        return DB::transaction(function () use ($request) {
            $school = School::create([
                'name' => $request->name,
                'slug' => Str::slug($request->name) . '-' . Str::random(4),
                'code' => strtoupper(Str::random(6)),
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'subdomain' => $request->subdomain,
                'url' => $request->url,
                'plan_id' => $request->plan_id,
                'is_active' => true,
            ]);

            $admin = null;
            if ($request->create_admin) {
                $admin = User::create([
                    'name' => $request->admin_name,
                    'email' => $request->admin_email,
                    'school_id' => $school->id,
                    'password' => Hash::make($request->admin_password),
                ]);
                $admin->assignRole('school_admin');
            }

            return response()->json([
                'message' => 'School created successfully',
                'school' => $school->load('plan'),
                'admin' => $admin?->load('roles'),
            ], 201);
        });
    }

    public function show(School $school)
    {
        return response()->json([
            'school' => $school->load(['plan', 'users' => function ($q) {
                $q->with('roles');
            }]),
        ]);
    }

    public function update(Request $request, School $school)
    {
        $request->validate([
            'name' => ['sometimes', 'string', 'max:255', Rule::unique('schools')->ignore($school->id)],
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'subdomain' => ['nullable', 'string', 'max:63', 'regex:/^[a-z0-9][a-z0-9-]*$/', Rule::unique('schools')->ignore($school->id)],
            'url' => 'nullable|url|max:255',
            'plan_id' => 'nullable|exists:plans,id',
            'is_active' => 'boolean',
        ]);

        $school->update($request->only(['name', 'email', 'phone', 'address', 'subdomain', 'url', 'plan_id', 'is_active']));

        if ($request->has('slug')) {
            $school->update(['slug' => Str::slug($request->name)]);
        }

        return response()->json([
            'message' => 'School updated successfully',
            'school' => $school->fresh()->load('plan'),
        ]);
    }

    public function destroy(School $school)
    {
        $school->delete();

        return response()->json(['message' => 'School deleted successfully']);
    }

    public function stats()
    {
        return response()->json([
            'total' => School::count(),
            'active' => School::where('is_active', true)->count(),
            'inactive' => School::where('is_active', false)->count(),
        ]);
    }
}
