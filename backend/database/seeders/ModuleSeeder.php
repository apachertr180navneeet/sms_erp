<?php

namespace Database\Seeders;

use App\Models\Module;
use Illuminate\Database\Seeder;

class ModuleSeeder extends Seeder
{
    public function run(): void
    {
        $modules = [
            ['name' => 'Students', 'slug' => 'students', 'description' => 'Student management system', 'icon' => 'users', 'sort_order' => 1],
            ['name' => 'Staff', 'slug' => 'staff', 'description' => 'Staff and employee management', 'icon' => 'user-tie', 'sort_order' => 2],
            ['name' => 'Attendance', 'slug' => 'attendance', 'description' => 'Daily attendance tracking', 'icon' => 'calendar-check', 'sort_order' => 3],
            ['name' => 'Fees', 'slug' => 'fees', 'description' => 'Fee collection and management', 'icon' => 'credit-card', 'sort_order' => 4],
            ['name' => 'Exams', 'slug' => 'exams', 'description' => 'Exam scheduling and results', 'icon' => 'file-text', 'sort_order' => 5],
            ['name' => 'Timetable', 'slug' => 'timetable', 'description' => 'Class and teacher timetable', 'icon' => 'clock', 'sort_order' => 6],
            ['name' => 'Transport', 'slug' => 'transport', 'description' => 'Bus and transport management', 'icon' => 'bus', 'sort_order' => 7],
            ['name' => 'Library', 'slug' => 'library', 'description' => 'Book and library management', 'icon' => 'book', 'sort_order' => 8],
            ['name' => 'Homework', 'slug' => 'homework', 'description' => 'Assignment and homework tracking', 'icon' => 'edit', 'sort_order' => 9],
            ['name' => 'Reports', 'slug' => 'reports', 'description' => 'Analytics and reporting', 'icon' => 'bar-chart', 'sort_order' => 10],
            ['name' => 'Website', 'slug' => 'website', 'description' => 'School website builder', 'icon' => 'globe', 'sort_order' => 11],
            ['name' => 'Messages', 'slug' => 'messages', 'description' => 'Internal messaging system', 'icon' => 'message-square', 'sort_order' => 12],
            ['name' => 'Inventory', 'slug' => 'inventory', 'description' => 'School asset and inventory management', 'icon' => 'package', 'sort_order' => 13],
            ['name' => 'Hostel', 'slug' => 'hostel', 'description' => 'Hostel and dormitory management', 'icon' => 'home', 'sort_order' => 14],
            ['name' => 'Notice Board', 'slug' => 'notice-board', 'description' => 'Announcements and notices', 'icon' => 'bell', 'sort_order' => 15],
            ['name' => 'Events', 'slug' => 'events', 'description' => 'School events and calendar', 'icon' => 'calendar', 'sort_order' => 16],
            ['name' => 'Gallery', 'slug' => 'gallery', 'description' => 'Photo and media gallery', 'icon' => 'image', 'sort_order' => 17],
            ['name' => 'Alumni', 'slug' => 'alumni', 'description' => 'Alumni management system', 'icon' => 'award', 'sort_order' => 18],
            ['name' => 'Payroll', 'slug' => 'payroll', 'description' => 'Staff salary and payroll management', 'icon' => 'dollar-sign', 'sort_order' => 19],
            ['name' => 'Leave Management', 'slug' => 'leave-management', 'description' => 'Staff leave requests and approvals', 'icon' => 'calendar-x', 'sort_order' => 20],
            ['name' => 'Online Admission', 'slug' => 'online-admission', 'description' => 'Online student admission portal', 'icon' => 'user-plus', 'sort_order' => 21],
            ['name' => 'Certificate', 'slug' => 'certificate', 'description' => 'Certificate generation system', 'icon' => 'file-check', 'sort_order' => 22],
            ['name' => 'Complaint', 'slug' => 'complaint', 'description' => 'Complaint tracking system', 'icon' => 'alert-circle', 'sort_order' => 23],
            ['name' => 'Visitor', 'slug' => 'visitor', 'description' => 'Visitor log management', 'icon' => 'user-check', 'sort_order' => 24],
        ];

        foreach ($modules as $module) {
            Module::firstOrCreate(['slug' => $module['slug']], $module);
        }
    }
}
