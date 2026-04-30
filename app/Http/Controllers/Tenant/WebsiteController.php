<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\CmsPage;
use App\Models\Tenant\SiteSetting;

class WebsiteController extends Controller
{
    public function home()
    {
        return $this->renderPage('home');
    }

    public function contact()
    {
        return $this->renderPage('contact');
    }

    public function page(string $slug)
    {
        return $this->renderPage($slug);
    }

    private function renderPage(string $slug)
    {
        $settings = SiteSetting::first();
        $page = CmsPage::where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();
        $pages = CmsPage::where('is_published', true)
            ->orderBy('sort_order')
            ->get();

        return view('tenant.website.page', compact('settings', 'page', 'pages'));
    }
}
