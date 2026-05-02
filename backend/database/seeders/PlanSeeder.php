<?php

namespace Database\Seeders;

use App\Models\Module;
use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Basic',
                'description' => 'For small schools with basic needs',
                'price' => 29.99,
                'student_limit' => 200,
                'staff_limit' => 20,
                'storage_limit_mb' => 1024,
                'billing_cycle' => 'monthly',
                'features' => ['Up to 200 students', 'Basic reports', 'Email support'],
                'modules' => ['students', 'attendance', 'fees'],
            ],
            [
                'name' => 'Standard',
                'description' => 'For growing schools with more features',
                'price' => 59.99,
                'student_limit' => 500,
                'staff_limit' => 50,
                'storage_limit_mb' => 5120,
                'billing_cycle' => 'monthly',
                'features' => ['Up to 500 students', 'Advanced reports', 'Exam management', 'Priority support'],
                'modules' => ['students', 'attendance', 'fees', 'exams', 'timetable', 'website'],
            ],
            [
                'name' => 'Premium',
                'description' => 'Full-featured solution for large schools',
                'price' => 99.99,
                'student_limit' => 0,
                'staff_limit' => 0,
                'storage_limit_mb' => 10240,
                'billing_cycle' => 'monthly',
                'features' => ['Unlimited students', 'All reports', 'All modules', '24/7 support', 'Custom branding'],
                'modules' => ['students', 'staff', 'attendance', 'fees', 'exams', 'timetable', 'transport', 'library', 'homework', 'reports', 'website', 'messages'],
            ],
        ];

        foreach ($plans as $planData) {
            $moduleSlugs = $planData['modules'];
            unset($planData['modules']);

            $plan = Plan::firstOrCreate(['name' => $planData['name']], $planData);

            $modules = Module::whereIn('slug', $moduleSlugs)->get();
            $plan->modules()->sync($modules->pluck('id'));
        }
    }
}
