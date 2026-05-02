<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'super_admin' => [
                'manage schools',
                'manage users',
                'manage plans',
                'manage subscriptions',
                'view analytics',
            ],
            'school_admin' => [
                'manage school settings',
                'manage teachers',
                'manage students',
                'manage staff',
                'manage fees',
                'manage exams',
                'manage classes',
                'manage timetable',
                'manage website',
            ],
            'teacher' => [
                'view students',
                'manage attendance',
                'manage marks',
                'view timetable',
                'create homework',
            ],
            'student' => [
                'view attendance',
                'view marks',
                'view timetable',
                'view homework',
                'view fees',
            ],
            'parent' => [
                'view child attendance',
                'view child marks',
                'view child timetable',
                'view child homework',
                'view child fees',
            ],
        ];

        foreach ($roles as $roleName => $permissions) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            foreach ($permissions as $permission) {
                $perm = Permission::firstOrCreate(['name' => $permission]);
                $role->givePermissionTo($perm);
            }
        }

        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@admin.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('admin123'),
            ]
        );
        $superAdmin->assignRole('super_admin');
    }
}
