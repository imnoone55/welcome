<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel') - gampil</title>

    <!-- Tailwind CSS CDN -->
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
            -webkit-tap-highlight-color: transparent;
        }
        .scrollbar-slim::-webkit-scrollbar {
            width: 5px;
            height: 5px;
        }
        .scrollbar-slim::-webkit-scrollbar-thumb {
            background: #27272a;
            border-radius: 4px;
        }
        .touch-scroll {
            -webkit-overflow-scrolling: touch;
        }
    </style>
</head>
<body class="bg-dark-900 text-zinc-100 min-h-screen flex flex-col lg:flex-row antialiased overflow-x-hidden">

    <!-- Mobile Top Navigation Header -->
    <header class="lg:hidden bg-dark-850 border-b border-zinc-800 px-4 py-3 flex items-center justify-between sticky top-0 z-40">
        <div class="flex items-center space-x-3">
            <button onclick="toggleMobileSidebar()" class="w-9 h-9 rounded-lg bg-zinc-800/80 hover:bg-zinc-700 text-zinc-300 flex items-center justify-center transition active:scale-95">
                <i class="fa-solid fa-bars text-base"></i>
            </button>
            <div class="flex items-center space-x-2">
                <div class="w-7 h-7 rounded-lg bg-gradient-to-tr from-pink-600 to-rose-400 flex items-center justify-center font-black text-white text-xs shadow-md shadow-pink-500/20">
                    G
                </div>
                <span class="font-bold tracking-wide text-white text-base">gampil</span>
            </div>
        </div>

        <div class="flex items-center space-x-2">
            <a href="{{ route('landing') }}" target="_blank" class="px-2.5 py-1.5 rounded-lg bg-zinc-800 hover:bg-zinc-700 text-zinc-300 text-xs font-medium flex items-center gap-1 transition">
                <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                <span class="hidden sm:inline">Preview</span>
            </a>
            <form action="{{ route('logout') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="w-8 h-8 rounded-lg bg-red-950/40 hover:bg-red-900/60 text-red-400 border border-red-800/40 flex items-center justify-center text-xs transition" title="Logout">
                    <i class="fa-solid fa-right-from-bracket"></i>
                </button>
            </form>
        </div>
    </header>

    <!-- Mobile Backdrop Overlay -->
    <div id="mobileBackdrop" onclick="toggleMobileSidebar(false)" 
         class="fixed inset-0 bg-black/70 backdrop-blur-sm z-40 hidden transition-opacity duration-300 lg:hidden"></div>

    <!-- Sidebar Navigation (Responsive Drawer on Mobile, Fixed on Desktop) -->
    <aside id="sidebar" 
           class="fixed inset-y-0 left-0 z-50 w-72 lg:w-64 bg-dark-850 border-r border-zinc-800 flex flex-col justify-between shrink-0 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out lg:static lg:z-auto">
        <div>
            <!-- Brand (Desktop Header) -->
            <div class="p-5 border-b border-zinc-800 flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-9 h-9 rounded-lg bg-gradient-to-tr from-pink-600 to-rose-400 flex items-center justify-center font-black text-white text-lg shadow-lg shadow-pink-500/20">
                        G
                    </div>
                    <div>
                        <span class="font-bold text-lg tracking-wider text-white">gampil</span>
                        <span class="text-[10px] block text-pink-400 font-mono font-semibold uppercase tracking-wider">Gampil Akses</span>
                    </div>
                </div>

                <!-- Close button for mobile -->
                <button onclick="toggleMobileSidebar(false)" class="lg:hidden text-zinc-400 hover:text-white p-2">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <!-- Nav Links -->
            <nav class="p-4 space-y-1.5">
                <a href="{{ route('admin.dashboard') }}" 
                   class="flex items-center px-4 py-3 rounded-xl text-sm font-medium transition {{ request()->routeIs('admin.dashboard') ? 'bg-pink-600/15 text-pink-400 border border-pink-500/30 font-semibold' : 'text-zinc-400 hover:text-white hover:bg-zinc-800/50' }}">
                    <i class="fa-solid fa-chart-pie w-5 mr-3 text-base {{ request()->routeIs('admin.dashboard') ? 'text-pink-400' : 'text-zinc-500' }}"></i>
                    Dashboard
                </a>
                <a href="{{ route('admin.logs') }}" 
                   class="flex items-center px-4 py-3 rounded-xl text-sm font-medium transition {{ request()->routeIs('admin.logs*') ? 'bg-pink-600/15 text-pink-400 border border-pink-500/30 font-semibold' : 'text-zinc-400 hover:text-white hover:bg-zinc-800/50' }}">
                    <i class="fa-solid fa-crosshairs w-5 mr-3 text-base {{ request()->routeIs('admin.logs*') ? 'text-pink-400' : 'text-zinc-500' }}"></i>
                    Target Logs
                </a>
                <a href="{{ route('admin.snapshots') }}" 
                   class="flex items-center px-4 py-3 rounded-xl text-sm font-medium transition {{ request()->routeIs('admin.snapshots*') ? 'bg-pink-600/15 text-pink-400 border border-pink-500/30 font-semibold' : 'text-zinc-400 hover:text-white hover:bg-zinc-800/50' }}">
                    <i class="fa-solid fa-camera w-5 mr-3 text-base {{ request()->routeIs('admin.snapshots*') ? 'text-pink-400' : 'text-zinc-500' }}"></i>
                    Webcam Captures
                </a>
                <a href="{{ route('admin.settings') }}" 
                   class="flex items-center px-4 py-3 rounded-xl text-sm font-medium transition {{ request()->routeIs('admin.settings*') ? 'bg-pink-600/15 text-pink-400 border border-pink-500/30 font-semibold' : 'text-zinc-400 hover:text-white hover:bg-zinc-800/50' }}">
                    <i class="fa-solid fa-gear w-5 mr-3 text-base {{ request()->routeIs('admin.settings*') ? 'text-pink-400' : 'text-zinc-500' }}"></i>
                    Discord & Settings
                </a>
            </nav>
        </div>

        <!-- User Profile & Logout -->
        <div class="p-4 border-t border-zinc-800 bg-zinc-950/40">
            <div class="flex items-center justify-between mb-3 px-1">
                <div class="flex items-center space-x-2.5 truncate">
                    <div class="w-8 h-8 rounded-full bg-pink-950/60 border border-pink-800/60 flex items-center justify-center text-xs text-pink-400 shrink-0">
                        <i class="fa-solid fa-user-shield"></i>
                    </div>
                    <div class="text-xs truncate">
                        <div class="text-white font-semibold truncate">{{ auth()->user()->name }}</div>
                        <div class="text-zinc-500 text-[10px] uppercase font-mono tracking-wider">{{ auth()->user()->role }}</div>
                    </div>
                </div>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('landing') }}" target="_blank" class="flex-1 text-center py-2 px-3 text-xs font-medium rounded-lg bg-zinc-800 hover:bg-zinc-700 text-zinc-300 transition flex items-center justify-center gap-1.5">
                    <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i> Preview
                </a>
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="py-2 px-3 text-xs rounded-lg bg-red-950/40 hover:bg-red-900/60 text-red-400 border border-red-800/40 transition" title="Logout">
                        <i class="fa-solid fa-right-from-bracket"></i>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0 overflow-y-auto">
        <!-- Topbar Notification Alerts -->
        @if(session('success'))
            <div class="bg-emerald-950/90 border-b border-emerald-800 text-emerald-300 px-4 sm:px-6 py-3 text-xs sm:text-sm flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <i class="fa-solid fa-circle-check text-sm shrink-0"></i>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-950/90 border-b border-red-800 text-red-300 px-4 sm:px-6 py-3 text-xs sm:text-sm flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <i class="fa-solid fa-triangle-exclamation text-sm shrink-0"></i>
                    <span>{{ session('error') }}</span>
                </div>
            </div>
        @endif

        <main class="p-4 sm:p-6 lg:p-8 flex-1 w-full max-w-full">
            @yield('content')
        </main>
    </div>

    <!-- Mobile Drawer Helper Script -->
    <script>
        function toggleMobileSidebar(show) {
            const sidebar = document.getElementById('sidebar');
            const backdrop = document.getElementById('mobileBackdrop');
            
            const isCurrentlyOpen = !sidebar.classList.contains('-translate-x-full');
            const shouldOpen = show !== undefined ? show : !isCurrentlyOpen;

            if (shouldOpen) {
                sidebar.classList.remove('-translate-x-full');
                backdrop.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            } else {
                sidebar.classList.add('-translate-x-full');
                backdrop.classList.add('hidden');
                document.body.style.overflow = '';
            }
        }
    </script>
    @yield('scripts')
</body>
</html>
