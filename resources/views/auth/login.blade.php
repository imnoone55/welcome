<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - R4VEN</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-zinc-950 text-zinc-100 min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md bg-zinc-900 border border-zinc-800 rounded-2xl p-8 shadow-2xl relative overflow-hidden">
        <!-- Accent Glow -->
        <div class="absolute -top-24 -left-24 w-48 h-48 bg-pink-600/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-24 -right-24 w-48 h-48 bg-rose-600/20 rounded-full blur-3xl pointer-events-none"></div>

        <div class="text-center mb-8 relative">
            <div class="w-14 h-14 mx-auto rounded-2xl bg-gradient-to-tr from-pink-600 to-rose-400 flex items-center justify-center font-black text-white text-2xl shadow-lg shadow-pink-500/30 mb-4">
                R
            </div>
            <h1 class="text-2xl font-bold text-white tracking-wide">R4VEN ACCESS</h1>
            <p class="text-xs text-zinc-500 font-mono mt-1">SECURE MANAGEMENT PORTAL</p>
        </div>

        @if ($errors->any())
            <div class="mb-6 p-3.5 rounded-lg bg-red-950/60 border border-red-800/60 text-red-300 text-xs flex items-center space-x-2">
                <i class="fa-solid fa-circle-exclamation text-sm"></i>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <form action="{{ route('login.submit') }}" method="POST" class="space-y-4 relative">
            @csrf
            <div>
                <label for="email" class="block text-xs font-semibold text-zinc-400 mb-1.5 uppercase tracking-wider">Email Address</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-zinc-500 pointer-events-none">
                        <i class="fa-solid fa-envelope text-sm"></i>
                    </span>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="admin@example.com" required autofocus
                           class="w-full pl-10 pr-4 py-2.5 bg-zinc-950 border border-zinc-800 rounded-xl text-sm text-white focus:outline-none focus:border-pink-500 focus:ring-1 focus:ring-pink-500 transition placeholder-zinc-600">
                </div>
            </div>

            <div>
                <label for="password" class="block text-xs font-semibold text-zinc-400 mb-1.5 uppercase tracking-wider">Password</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-zinc-500 pointer-events-none">
                        <i class="fa-solid fa-lock text-sm"></i>
                    </span>
                    <input type="password" id="password" name="password" placeholder="••••••••" required
                           class="w-full pl-10 pr-4 py-2.5 bg-zinc-950 border border-zinc-800 rounded-xl text-sm text-white focus:outline-none focus:border-pink-500 focus:ring-1 focus:ring-pink-500 transition placeholder-zinc-600">
                </div>
            </div>

            <div class="flex items-center justify-between text-xs pt-1">
                <label class="flex items-center text-zinc-400 cursor-pointer">
                    <input type="checkbox" name="remember" class="w-4 h-4 rounded bg-zinc-950 border-zinc-700 text-pink-600 focus:ring-0 focus:ring-offset-0">
                    <span class="ml-2">Remember session</span>
                </label>
            </div>

            <button type="submit" class="w-full mt-2 py-3 px-4 bg-gradient-to-r from-pink-600 to-rose-600 hover:from-pink-500 hover:to-rose-500 text-white font-medium text-sm rounded-xl shadow-lg shadow-pink-600/30 transition duration-200">
                Authenticate & Enter
            </button>
        </form>
    </div>

</body>
</html>
