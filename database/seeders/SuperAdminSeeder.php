<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@sms-erp.com'],
            [
                'name' => 'Super Admin',
                'password' => 'password123',
                'role' => 'super_admin',
            ]
        );
    }
}
