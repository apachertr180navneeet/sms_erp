<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Central\SubscriptionPackage;

class SubscriptionPackageSeeder extends Seeder
{
    public function run(): void
    {
        $packages = [
            [
                'name' => 'Basic',
                'description' => 'For small schools with basic features',
                'price_monthly' => 49.99,
                'price_yearly' => 499.99,
                'features' => [
                    'Up to 200 students',
                    'Up to 20 teachers',
                    'Basic attendance',
                    'Fee management',
                    'Email support'
                ],
                'max_students' => 200,
                'max_teachers' => 20,
                'is_active' => true,
            ],
            [
                'name' => 'Premium',
                'description' => 'For medium schools with advanced features',
                'price_monthly' => 99.99,
                'price_yearly' => 999.99,
                'features' => [
                    'Up to 1000 students',
                    'Up to 100 teachers',
                    'Advanced attendance (biometric)',
                    'Full exam management',
                    'Library management',
                    'Transport management',
                    'Priority support'
                ],
                'max_students' => 1000,
                'max_teachers' => 100,
                'is_active' => true,
            ],
            [
                'name' => 'Enterprise',
                'description' => 'For large schools with all features',
                'price_monthly' => 199.99,
                'price_yearly' => 1999.99,
                'features' => [
                    'Unlimited students',
                    'Unlimited teachers',
                    'All Premium features',
                    'Hostel management',
                    'Inventory management',
                    'Custom branding',
                    'API access',
                    'Dedicated support'
                ],
                'max_students' => null,
                'max_teachers' => null,
                'is_active' => true,
            ],
        ];

        foreach ($packages as $package) {
            SubscriptionPackage::firstOrCreate(
                ['name' => $package['name']],
                $package
            );
        }
    }
}
