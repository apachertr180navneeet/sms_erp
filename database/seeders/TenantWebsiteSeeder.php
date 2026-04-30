<?php

namespace Database\Seeders;

use App\Models\Tenant\CmsPage;
use App\Models\Tenant\SiteSetting;
use Illuminate\Database\Seeder;

class TenantWebsiteSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = tenant();

        SiteSetting::updateOrCreate(
            ['id' => 1],
            [
                'site_name' => $tenant?->name ?: 'School Website',
                'logo' => $tenant?->school_logo,
                'email' => $tenant?->school_email,
                'phone' => $tenant?->school_phone,
                'address' => $tenant?->school_address,
                'footer_text' => 'Powered by School ERP',
            ]
        );

        CmsPage::updateOrCreate(
            ['slug' => 'home'],
            [
                'title' => 'Welcome',
                'type' => 'home',
                'content' => 'Welcome to ' . ($tenant?->name ?: 'our school') . '. This website is ready to customize from the tenant CMS.',
                'is_published' => true,
                'sort_order' => 1,
            ]
        );

        CmsPage::updateOrCreate(
            ['slug' => 'contact'],
            [
                'title' => 'Contact Us',
                'type' => 'contact',
                'content' => 'Contact the school office for admissions, fees, academics, and general enquiries.',
                'is_published' => true,
                'sort_order' => 2,
            ]
        );
    }
}
