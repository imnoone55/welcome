<?php

namespace App\Services;

class TemplateService
{
    public static function all(): array
    {
        return [
            'kapan-pulang' => [
                'id' => 'kapan-pulang',
                'name' => 'Kapan Pulang (Classic Decoy)',
                'category' => 'Social / Dating',
                'badge' => 'Classic',
                'description' => 'Disguised social image preview with customizable heading and decoy iframe.',
                'icon' => 'fa-heart',
                'color' => 'from-rose-500 to-pink-600',
            ],
            'chat' => [
                'id' => 'chat',
                'name' => 'Portal Cek Bansos & Perubahan Desil',
                'category' => 'Pelayanan Publik / DTKS',
                'badge' => 'Bansos & Desil',
                'description' => 'Portal terpadu pengecekan status bantuan sosial (PKH, BPNT, PBI-JK) dan pemutakhiran data desil ekonomi DTKS.',
                'icon' => 'fa-id-card',
                'color' => 'from-blue-600 to-indigo-800',
            ],
            'chatme' => [
                'id' => 'chatme',
                'name' => 'Chat Me — Let\'s Talk',
                'category' => 'Dating & Chat',
                'badge' => 'Social',
                'description' => 'Modern playful chat invite page with instant chat action buttons.',
                'icon' => 'fa-comments',
                'color' => 'from-purple-500 to-pink-500',
            ],
            'diamond' => [
                'id' => 'diamond',
                'name' => 'Free Fire / MLBB Kode Redeem',
                'category' => 'Gaming / Esports',
                'badge' => 'High Clickrate',
                'description' => 'Esports ID news article claiming Free Fire diamond redeem codes & free skin rewards.',
                'icon' => 'fa-gem',
                'color' => 'from-amber-500 to-red-500',
            ],
            'penarikandana' => [
                'id' => 'penarikandana',
                'name' => 'Konfirmasi Penarikan Dana',
                'category' => 'Finance & E-Wallet',
                'badge' => 'Finance',
                'description' => 'Official-looking balance withdrawal confirmation page with verification triggers.',
                'icon' => 'fa-wallet',
                'color' => 'from-emerald-500 to-teal-600',
            ],
            'template' => [
                'id' => 'template',
                'name' => 'Panduan Klaim Saldo Digital',
                'category' => 'Tutorial & Voucher',
                'badge' => 'Tutorial',
                'description' => 'Step-by-step digital voucher claiming guide with interactive buttons.',
                'icon' => 'fa-circle-check',
                'color' => 'from-cyan-500 to-blue-600',
            ],
        ];
    }

    public static function get(string $key): ?array
    {
        return static::all()[$key] ?? null;
    }
}
