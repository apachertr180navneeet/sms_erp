<?php

namespace Database\Seeders;

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
                'billing_cycle' => 'monthly',
                'features' => ['Up to 200 students', 'Basic reports', 'Email support'],
                'modules' => ['students', 'attendance', 'basic_fees'],
            ],
            [
                'name' => 'Standard',
                'description' => 'For growing schools with more features',
                'price' => 59.99,
                'billing_cycle' => 'monthly',
                'features' => ['Up to 500 students', 'Advanced reports', 'Exam management', 'Priority support'],
                'modules' => ['students', 'attendance', 'fees', 'exams', 'timetable', 'website'],
            ],
            [
                'name' => 'Premium',
                'description' => 'Full-featured solution for large schools',
                'price' => 99.99,
                'billing_cycle' => 'monthly',
                'features' => ['Unlimited students', 'All reports', 'All modules', '24/7 support', 'Custom branding'],
                'modules' => ['students', 'staff', 'attendance', 'fees', 'exams', 'timetable', 'transport', 'library', 'website', 'analytics'],
            ],
        ];

        foreach ($plans as $plan) {
            Plan::firstOrCreate(['name' => $plan['name']], $plan);
        }
    }
}
