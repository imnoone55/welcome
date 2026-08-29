<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Services\TemplateService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LandingController extends Controller
{
    public function index(Request $request, ?string $slug = null): View
    {
        $activeTemplate = Setting::get('active_template', 'kapan-pulang');

        // Check if slug matches a specific template ID
        if ($slug && array_key_exists($slug, TemplateService::all())) {
            $selectedTemplate = $slug;
        } elseif ($slug && $slug === Setting::get('custom_landing_slug', 'kapan-pulang')) {
            $selectedTemplate = $activeTemplate;
        } else {
            $selectedTemplate = $activeTemplate;
        }

        $siteTitle = Setting::get('site_title', 'Kapan Pulang?');
        $siteDescription = Setting::get('site_description', 'Kangen nih, kapan pulang?');
        $rawOgImage = Setting::get('og_image_url', 'images/landing/bansos-banner.jpg');
        $ogImageUrl = (str_starts_with($rawOgImage, 'http://') || str_starts_with($rawOgImage, 'https://'))
            ? $rawOgImage
            : asset($rawOgImage);
        $decoyIframeUrl = Setting::get('decoy_iframe_url', 'https://tugas-besar-webdanmobile.vercel.app/');
        $landingHeading = Setting::get('landing_heading', 'Kangen');
        $templateInfo = TemplateService::get($selectedTemplate);

        // View path
        $viewName = "landing.templates.{$selectedTemplate}";
        if (!view()->exists($viewName)) {
            $viewName = "landing.templates.kapan-pulang";
        }

        return view($viewName, compact(
            'siteTitle',
            'siteDescription',
            'ogImageUrl',
            'decoyIframeUrl',
            'landingHeading',
            'selectedTemplate',
            'templateInfo'
        ));
    }
}
