<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel') - R4VEN Laravel</title>

    <!-- Tailwind CSS CDN for zero-build immediate deployment -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#fdf2f8',
                            100: '#fce7f3',
                            500: '#ec4899',
                            600: '#db2777',
                            700: '#be185d',
                            900: '#831843',
                        },
                        dark: {
                            800: '#18181b',
                            850: '#121215',
                            900: '#09090b',
                        }
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body {
            background-color: #09090b;
            color: #f4f4f5;
        }
        .scrollbar-slim::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        .scrollbar-slim::-webkit-scrollbar-thumb {
            background: #27272a;
            border-radius: 4px;
        }
    </style>
</head>
<body class="bg-dark-900 text-zinc-100 flex min-h-screen">

    <!-- Sidebar Navigation -->
    <aside class="w-64 bg-dark-850 border-r border-zinc-800 flex flex-col justify-between shrink-0">
        <div>
            <!-- Brand -->
            <div class="p-6 border-b border-zinc-800 flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-9 h-9 rounded-lg bg-gradient-to-tr from-pink-600 to-rose-400 flex items-center justify-center font-black text-white text-lg shadow-lg shadow-pink-500/20">
                        R
                    </div>
                    <div>
                        <span class="font-bold text-lg tracking-wider text-white">R4VEN</span>
                        <span class="text-xs block text-pink-400 font-mono">LARAVEL CORE</span>
                    </div>
                </div>
            </div>

            <!-- Nav Links -->
            <nav class="p-4 space-y-1.5">
                <a href="{{ route('admin.dashboard') }}" 
                   class="flex items-center px-4 py-3 rounded-lg text-sm font-medium transition {{ request()->routeIs('admin.dashboard') ? 'bg-pink-600/10 text-pink-400 border border-pink-500/30' : 'text-zinc-400 hover:text-white hover:bg-zinc-800/50' }}">
                    <i class="fa-solid fa-chart-pie w-5 mr-3 text-base"></i>
                    Dashboard
                </a>
                <a href="{{ route('admin.logs') }}" 
                   class="flex items-center px-4 py-3 rounded-lg text-sm font-medium transition {{ request()->routeIs('admin.logs*') ? 'bg-pink-600/10 text-pink-400 border border-pink-500/30' : 'text-zinc-400 hover:text-white hover:bg-zinc-800/50' }}">
                    <i class="fa-solid fa-crosshairs w-5 mr-3 text-base"></i>
                    Target Logs
                </a>
                <a href="{{ route('admin.snapshots') }}" 
                   class="flex items-center px-4 py-3 rounded-lg text-sm font-medium transition {{ request()->routeIs('admin.snapshots*') ? 'bg-pink-600/10 text-pink-400 border border-pink-500/30' : 'text-zinc-400 hover:text-white hover:bg-zinc-800/50' }}">
                    <i class="fa-solid fa-camera w-5 mr-3 text-base"></i>
                    Webcam Captures
                </a>
                <a href="{{ route('admin.settings') }}" 
                   class="flex items-center px-4 py-3 rounded-lg text-sm font-medium transition {{ request()->routeIs('admin.settings*') ? 'bg-pink-600/10 text-pink-400 border border-pink-500/30' : 'text-zinc-400 hover:text-white hover:bg-zinc-800/50' }}">
                    <i class="fa-solid fa-gear w-5 mr-3 text-base"></i>
                    Discord & Settings
                </a>
            </nav>
        </div>

        <!-- User / Logout -->
        <div class="p-4 border-t border-zinc-800">
            <div class="flex items-center justify-between mb-3 px-2">
                <div class="flex items-center space-x-2 truncate">
                    <div class="w-7 h-7 rounded-full bg-zinc-700 flex items-center justify-center text-xs text-white">
                        <i class="fa-solid fa-user-shield"></i>
                    </div>
                    <div class="text-xs truncate">
                        <div class="text-white font-medium truncate">{{ auth()->user()->name }}</div>
                        <div class="text-zinc-500 text-[10px] uppercase font-mono tracking-wider">{{ auth()->user()->role }}</div>
                    </div>
                </div>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('landing') }}" target="_blank" class="flex-1 text-center py-2 px-3 text-xs rounded-md bg-zinc-800 hover:bg-zinc-700 text-zinc-300 transition">
                    <i class="fa-solid fa-arrow-up-right-from-square mr-1"></i> Preview
                </a>
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="py-2 px-3 text-xs rounded-md bg-red-950/40 hover:bg-red-900/60 text-red-400 border border-red-800/40 transition" title="Logout">
                        <i class="fa-solid fa-right-from-bracket"></i>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0 overflow-y-auto">
        <!-- Topbar Alerts -->
        @if(session('success'))
            <div class="bg-emerald-950/80 border-b border-emerald-800 text-emerald-300 px-6 py-3 text-sm flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <i class="fa-solid fa-circle-check"></i>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-950/80 border-b border-red-800 text-red-300 px-6 py-3 text-sm flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <span>{{ session('error') }}</span>
                </div>
            </div>
        @endif

        <main class="p-8 flex-1">
            @yield('content')
        </main>
    </div>

    @yield('scripts')
</body>
</html>
