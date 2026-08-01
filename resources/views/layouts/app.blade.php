<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>{{ $title ?? 'English Learning Dashboard' }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="bg-slate-950 text-slate-200 antialiased font-sans flex h-screen overflow-hidden selection:bg-emerald-500/30">
        <!-- Sidebar -->
        <aside class="w-64 bg-slate-900 border-r border-slate-800 flex flex-col h-full flex-shrink-0">
            <div class="p-6 border-b border-slate-800 flex items-center gap-3">
                <div class="w-8 h-8 rounded bg-emerald-500 flex items-center justify-center font-bold text-slate-950">E</div>
                <h1 class="font-bold text-slate-100 tracking-tight">EnglishOS</h1>
            </div>

            <nav class="flex-1 overflow-y-auto p-4 space-y-1">
                <p class="px-2 text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2 mt-4">Learning</p>
                <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('dashboard') ? 'bg-slate-800/50 text-emerald-400' : 'text-slate-400 hover:bg-slate-800 hover:text-emerald-400' }}">Dashboard</a>
                <a href="{{ route('vocabulary') }}" class="block px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('vocabulary') ? 'bg-slate-800/50 text-emerald-400' : 'text-slate-400 hover:bg-slate-800 hover:text-emerald-400' }}">Vocabulary</a>
                <a href="{{ route('grammar') }}" class="block px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('grammar*') ? 'bg-slate-800/50 text-emerald-400' : 'text-slate-400 hover:bg-slate-800 hover:text-emerald-400' }}">Grammar</a>
                <a href="{{ route('mistakes') }}" class="block px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('mistakes') ? 'bg-slate-800/50 text-emerald-400' : 'text-slate-400 hover:bg-slate-800 hover:text-emerald-400' }}">Mistakes</a>
                <a href="#" class="block px-3 py-2 rounded-md text-sm font-medium text-slate-400 hover:bg-slate-800 hover:text-slate-100 transition-colors">Reading</a>
                
                <p class="px-2 text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2 mt-6">Practice & Track</p>
                <a href="{{ route('journal') }}" class="block px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('journal') ? 'bg-slate-800/50 text-emerald-400' : 'text-slate-400 hover:bg-slate-800 hover:text-emerald-400' }}">Journal</a>
                <a href="#" class="block px-3 py-2 rounded-md text-sm font-medium text-slate-400 hover:bg-slate-800 hover:text-slate-100 transition-colors">Study Timer</a>
                <a href="#" class="block px-3 py-2 rounded-md text-sm font-medium text-slate-400 hover:bg-slate-800 hover:text-slate-100 transition-colors">Timeline</a>
                <a href="#" class="block px-3 py-2 rounded-md text-sm font-medium text-slate-400 hover:bg-slate-800 hover:text-slate-100 transition-colors">Stats & Goals</a>
            </nav>

            <div class="p-4 border-t border-slate-800">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-slate-800 flex items-center justify-center text-xs border border-slate-700">Me</div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-slate-200 truncate">Learner</p>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 flex flex-col h-full overflow-hidden bg-slate-950">
            <!-- Topbar (Optional, can just have header in slot) -->
            <header class="h-16 border-b border-slate-800 flex items-center px-8 bg-slate-950/50 backdrop-blur-sm sticky top-0 z-10">
                <h2 class="text-lg font-semibold text-slate-100">{{ $header ?? 'Dashboard' }}</h2>
            </header>
            
            <div class="flex-1 overflow-y-auto p-8">
                <div class="max-w-5xl mx-auto">
                    {{ $slot }}
                </div>
            </div>
        </main>

        @livewireScripts
    </body>
</html>
