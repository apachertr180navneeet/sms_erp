<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Stancl\Tenancy\Database\Models\Tenant;

class TenantRolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Define permissions for each user type
        $permissions = [
            // Student permissions
            'view_own_grades',
            'view_own_attendance',
            'submit_assignment',
            
            // Teacher permissions
            'manage_students',
            'manage_grades',
            'manage_assignments',
            'view_attendance',
            'manage_lessons',
            
            // School Admin permissions
            'manage_users',
            'manage_staff',
            'manage_fees',
            'manage_exams',
            'view_reports',
            'manage_subscriptions',
            
            // Parent permissions
            'view_child_grades',
            'view_child_attendance',
            'pay_fees',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Create roles with permissions
        $roles = [
            'student' => ['view_own_grades', 'view_own_attendance', 'submit_assignment'],
            'teacher' => ['manage_students', 'manage_grades', 'manage_assignments', 'view_attendance', 'manage_lessons'],
            'school_admin' => ['manage_users', 'manage_staff', 'manage_fees', 'manage_exams', 'view_reports', 'manage_subscriptions'],
            'parent' => ['view_child_grades', 'view_child_attendance', 'pay_fees'],
        ];

        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            $role->syncPermissions($rolePermissions);
        }
    }
}
