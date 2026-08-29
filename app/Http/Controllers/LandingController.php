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

        $templateInfo = TemplateService::get($selectedTemplate);

        if ($selectedTemplate === 'kapan-pulang') {
            $siteTitle = Setting::get('site_title', $templateInfo['title'] ?? 'Kapan Pulang?');
            $siteDescription = Setting::get('site_description', $templateInfo['preview_description'] ?? 'Kangen nih, kapan pulang?');
            $rawOgImage = Setting::get('og_image_url', $templateInfo['og_image'] ?? 'images/landing/kapan-pulang.jfif');
            $decoyIframeUrl = Setting::get('decoy_iframe_url', $templateInfo['decoy_iframe_url'] ?? 'https://tugas-besar-webdanmobile.vercel.app/');
            $landingHeading = Setting::get('landing_heading', $templateInfo['heading'] ?? 'Kangen');
        } else {
            $siteTitle = Setting::get("template_{$selectedTemplate}_title", $templateInfo['title'] ?? ($templateInfo['name'] ?? 'Gampil Akses'));
            $siteDescription = Setting::get("template_{$selectedTemplate}_description", $templateInfo['preview_description'] ?? '');
            $rawOgImage = Setting::get("template_{$selectedTemplate}_og_image", $templateInfo['og_image'] ?? 'images/landing/default-thumbnail.jpg');
            $decoyIframeUrl = Setting::get("template_{$selectedTemplate}_decoy_url", $templateInfo['decoy_iframe_url'] ?? 'https://tugas-besar-webdanmobile.vercel.app/');
            $landingHeading = Setting::get("template_{$selectedTemplate}_heading", $templateInfo['heading'] ?? ($templateInfo['name'] ?? ''));
        }

        $ogImageUrl = (str_starts_with($rawOgImage, 'http://') || str_starts_with($rawOgImage, 'https://'))
            ? $rawOgImage
            : asset($rawOgImage);

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
