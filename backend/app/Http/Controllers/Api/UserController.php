<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['school', 'roles']);

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->has('role')) {
            $query->whereHas('roles', function ($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        if ($request->has('school_id')) {
            $query->where('school_id', $request->school_id);
        }

        return response()->json([
            'users' => $query->latest()->paginate($request->per_page ?? 15),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'school_id' => 'nullable|exists:schools,id',
            'roles' => 'required|array',
            'roles.*' => ['required', 'string', 'exists:roles,name'],
            'password' => ['required', 'min:8'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'school_id' => $request->school_id,
            'password' => Hash::make($request->password),
        ]);

        $user->syncRoles($request->roles);

        return response()->json([
            'message' => 'User created successfully',
            'user' => $user->load(['school', 'roles']),
        ], 201);
    }

    public function show(User $user)
    {
        return response()->json([
            'user' => $user->load(['school', 'roles']),
        ]);
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => ['sometimes', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => 'nullable|string|max:20',
            'school_id' => 'nullable|exists:schools,id',
            'roles' => 'sometimes|array',
            'roles.*' => ['required', 'string', 'exists:roles,name'],
            'password' => 'sometimes|min:8',
        ]);

        $user->update($request->only(['name', 'email', 'phone', 'school_id']));

        if ($request->has('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        if ($request->has('roles')) {
            $user->syncRoles($request->roles);
        }

        return response()->json([
            'message' => 'User updated successfully',
            'user' => $user->load(['school', 'roles']),
        ]);
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return response()->json(['message' => 'Cannot delete yourself'], 403);
        }

        $user->delete();
        return response()->json(['message' => 'User deleted successfully']);
    }

    public function assignRole(Request $request, User $user)
    {
        $request->validate([
            'roles' => 'required|array',
            'roles.*' => ['required', 'string', 'exists:roles,name'],
        ]);

        $user->syncRoles($request->roles);

        return response()->json([
            'message' => 'Roles updated',
            'user' => $user->load(['school', 'roles']),
        ]);
    }

    public function stats()
    {
        $roles = ['super_admin', 'school_admin', 'teacher', 'student', 'parent'];
        $roleCounts = [];
        foreach ($roles as $role) {
            $roleCounts[$role] = \Spatie\Permission\Models\Role::where('name', $role)->first()?->users()->count() ?? 0;
        }

        return response()->json([
            'total' => User::count(),
            'by_role' => $roleCounts,
        ]);
    }
}
