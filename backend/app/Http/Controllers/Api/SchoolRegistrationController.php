<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class SchoolRegistrationController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'school_name' => 'required|string|max:255|unique:schools,name',
            'school_email' => 'nullable|email|max:255',
            'school_phone' => 'nullable|string|max:20',
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|string|email|max:255|unique:users,email',
            'admin_password' => ['required', 'confirmed', Password::defaults()],
        ]);

        return DB::transaction(function () use ($request) {
            $schoolCode = strtoupper(Str::random(6));
            $slug = Str::slug($request->school_name) . '-' . Str::random(4);

            $school = School::create([
                'name' => $request->school_name,
                'slug' => $slug,
                'code' => $schoolCode,
                'email' => $request->school_email,
                'phone' => $request->school_phone,
                'is_active' => true,
            ]);

            $admin = User::create([
                'name' => $request->admin_name,
                'email' => $request->admin_email,
                'school_id' => $school->id,
                'password' => Hash::make($request->admin_password),
            ]);
            $admin->assignRole('school_admin');

            $token = $admin->createToken($admin->name)->plainTextToken;

            return response()->json([
                'message' => 'School registered successfully',
                'school' => $school,
                'admin' => $admin->load('roles'),
                'token' => $token,
            ], 201);
        });
    }
}
