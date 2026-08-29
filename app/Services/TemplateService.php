<?php

namespace App\Services;

class TemplateService
{
    public static function all(): array
    {
        return [
            'kapan-pulang' => [
                'id' => 'kapan-pulang',
                'name' => 'Custom Template (Kustomisasi Bebas)',
                'category' => 'Custom / Universal',
                'badge' => 'Customizable',
                'description' => 'Landing page serbaguna dengan hero section modern, foto/thumbnail, artikel dinamis, dan iframe opsional.',
                'title' => 'Portal Berita & Informasi Resmi - Gampil Akses',
                'preview_description' => 'Baca informasi dan pengumuman resmi terbaru hari ini melalui portal Gampil Akses.',
                'heading' => 'Portal Informasi & Publikasi Resmi',
                'og_image' => 'images/landing/default-thumbnail.jpg',
                'decoy_iframe_url' => '',
                'icon' => 'fa-sliders',
                'color' => 'from-slate-700 to-indigo-900',
            ],
            'chat' => [
                'id' => 'chat',
                'name' => 'Portal Cek Bansos & Perubahan Desil',
                'category' => 'Pelayanan Publik / DTKS',
                'badge' => 'Bansos & Desil',
                'description' => 'Portal terpadu pengecekan status bantuan sosial (PKH, BPNT, PBI-JK) dan pemutakhiran data desil ekonomi DTKS.',
                'title' => 'Cek Status Bantuan Sosial & Perubahan Desil DTKS - Gampil Akses',
                'preview_description' => 'Layanan resmi pengecekan data penerima bansos (PKH, BPNT, BLT) dan status desil DTKS 2026.',
                'heading' => 'Portal Cek Bansos & Desil DTKS Terpadu',
                'og_image' => 'images/landing/kapan-pulang.jfif',
                'decoy_iframe_url' => '',
                'icon' => 'fa-id-card',
                'color' => 'from-blue-600 to-indigo-800',
            ],
            'chatme' => [
                'id' => 'chatme',
                'name' => 'Chat Me — Let\'s Talk',
                'category' => 'Dating & Chat',
                'badge' => 'Social',
                'description' => 'Modern playful chat invite page with instant chat action buttons.',
                'title' => 'Chat Me — Pesan Masuk Baru',
                'preview_description' => 'Seseorang telah mengirimkan pesan pribadi untuk Anda. Buka dan balas obrolan sekarang.',
                'heading' => 'Chat Baru Menunggu Anda',
                'og_image' => 'images/landing/kapan-pulang.jfif',
                'decoy_iframe_url' => 'https://web.whatsapp.com/',
                'icon' => 'fa-comments',
                'color' => 'from-purple-500 to-pink-500',
            ],
            'diamond' => [
                'id' => 'diamond',
                'name' => 'Free Fire / MLBB Kode Redeem & Top Up',
                'category' => 'Gaming / Esports',
                'badge' => 'Top Up & Redeem',
                'description' => 'Esports ID news article claiming Free Fire diamond redeem codes & free skin rewards.',
                'title' => 'Kode Redeem Free Fire & Top Up Diamond Gratis 2026 | Esports ID',
                'preview_description' => 'Dapatkan deretan kode redeem Free Fire (FF) terbaru hari ini. Klaim ribuan diamond dan skin langka gratis!',
                'heading' => 'KODE REDEEM FREE FIRE',
                'og_image' => 'images/landing/bg.jpg',
                'decoy_iframe_url' => 'https://reward.ff.garena.com/id',
                'icon' => 'fa-gem',
                'color' => 'from-amber-500 to-red-500',
            ],
            'penarikandana' => [
                'id' => 'penarikandana',
                'name' => 'Konfirmasi Penarikan Dana',
                'category' => 'Finance & E-Wallet',
                'badge' => 'Finance',
                'description' => 'Official-looking balance withdrawal confirmation page with verification triggers.',
                'title' => 'Konfirmasi Penarikan Saldo Dompet Digital - DANA Indonesia',
                'preview_description' => 'Periksa dan konfirmasi detail penarikan saldo e-wallet Anda sebelum pencairan ke rekening tujuan.',
                'heading' => 'Konfirmasi Penarikan Saldo',
                'og_image' => 'images/landing/dana-logo.png',
                'decoy_iframe_url' => 'https://www.dana.id/',
                'icon' => 'fa-wallet',
                'color' => 'from-emerald-500 to-teal-600',
            ],
            'template' => [
                'id' => 'template',
                'name' => 'Panduan Klaim Saldo Digital',
                'category' => 'Tutorial & Voucher',
                'badge' => 'Tutorial',
                'description' => 'Step-by-step digital voucher claiming guide with interactive buttons.',
                'title' => 'Panduan Resmi Klaim Saldo & Voucher Digital Gratis',
                'preview_description' => 'Ikuti langkah mudah untuk klaim saldo digital dan voucher resmi tanpa dipungut biaya.',
                'heading' => 'Panduan Klaim Saldo Digital',
                'og_image' => 'images/landing/default-thumbnail.jpg',
                'decoy_iframe_url' => 'https://tugas-besar-webdanmobile.vercel.app/',
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
