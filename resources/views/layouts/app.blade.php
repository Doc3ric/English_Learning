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
                {{-- Primary daily action --}}
                <a href="{{ route('writing-coach') }}" class="block px-3 py-2.5 rounded-md text-sm font-semibold transition-colors mb-2 {{ request()->routeIs('writing-coach') ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-500/20' : 'bg-emerald-600/10 text-emerald-400 border border-emerald-600/30 hover:bg-emerald-600/20' }}">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        ✍ Writing Coach
                    </div>
                </a>
                <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('dashboard') ? 'bg-slate-800/50 text-emerald-400' : 'text-slate-400 hover:bg-slate-800 hover:text-emerald-400' }}">Dashboard</a>
                <a href="{{ route('vocabulary') }}" class="block px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('vocabulary') ? 'bg-slate-800/50 text-emerald-400' : 'text-slate-400 hover:bg-slate-800 hover:text-emerald-400' }}">Vocabulary</a>
                <a href="{{ route('grammar') }}" class="block px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('grammar*') ? 'bg-slate-800/50 text-emerald-400' : 'text-slate-400 hover:bg-slate-800 hover:text-emerald-400' }}">Grammar</a>
                <a href="{{ route('mistakes') }}" class="block px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('mistakes') ? 'bg-slate-800/50 text-emerald-400' : 'text-slate-400 hover:bg-slate-800 hover:text-emerald-400' }}">Mistakes</a>
                <a href="{{ route('reading') }}" class="block px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('reading*') ? 'bg-slate-800/50 text-emerald-400' : 'text-slate-400 hover:bg-slate-800 hover:text-emerald-400' }}">Reading</a>
                
                <p class="px-2 text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2 mt-6">Practice & Track</p>
                <a href="{{ route('journal') }}" class="block px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('journal') ? 'bg-slate-800/50 text-emerald-400' : 'text-slate-400 hover:bg-slate-800 hover:text-emerald-400' }}">Journal</a>
                <a href="{{ route('timer') }}" class="block px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('timer') ? 'bg-slate-800/50 text-emerald-400' : 'text-slate-400 hover:bg-slate-800 hover:text-emerald-400' }}">Study Timer</a>
                <a href="{{ route('timeline') }}" class="block px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('timeline') ? 'bg-slate-800/50 text-emerald-400' : 'text-slate-400 hover:bg-slate-800 hover:text-emerald-400' }}">Timeline</a>
                <a href="{{ route('stats') }}" class="block px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('stats') ? 'bg-slate-800/50 text-emerald-400' : 'text-slate-400 hover:bg-slate-800 hover:text-emerald-400' }}">Stats & Goals</a>
                <a href="{{ route('achievements') }}" class="block px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('achievements') ? 'bg-slate-800/50 text-emerald-400' : 'text-slate-400 hover:bg-slate-800 hover:text-emerald-400' }}">Achievements</a>
            </nav>

            <!-- User Info (Bottom) -->
            <div class="mt-auto pt-6 border-t border-slate-800 p-4">
                <div class="flex items-center gap-3">
                    <div class="h-8 w-8 rounded-full bg-slate-800 flex items-center justify-center text-sm font-medium text-slate-300">
                        Me
                    </div>
                    <div class="text-sm font-medium text-slate-300">Learner</div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 flex flex-col h-full overflow-hidden bg-slate-950">
            <!-- Top Header -->
            <header class="bg-slate-900 border-b border-slate-800 p-4 sticky top-0 z-40 flex justify-between items-center">
                <h1 class="text-xl font-bold text-slate-100">{{ $header ?? '' }}</h1>
                <div class="flex-1 max-w-md ml-8">
                    <livewire:global-search />
                </div>
                <div class="flex items-center gap-4 ml-4">
                    <!-- Notifications/Profile could go here -->
                </div>
            </header>
            
            <div class="flex-1 overflow-y-auto p-8">
                {{ $slot }}
            </div>
        </main>
    </div>

    <!-- Achievement Toast Notification -->
    <div x-data="{ show: false, title: '', icon: '' }"
         @achievement-unlocked.window="
            title = $event.detail.title;
            icon = $event.detail.icon;
            show = true;
            setTimeout(() => { show = false; }, 4000);
         "
         class="fixed bottom-4 right-4 z-50">
        <div x-show="show" 
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="translate-y-10 opacity-0"
             x-transition:enter-end="translate-y-0 opacity-100"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="translate-y-0 opacity-100"
             x-transition:leave-end="translate-y-10 opacity-0"
             class="bg-slate-800 border-l-4 border-amber-500 rounded-lg shadow-2xl p-4 flex items-start gap-4 max-w-sm"
             style="display: none;">
            
            <div class="bg-amber-500/20 text-amber-500 rounded-full p-2 shrink-0">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
            </div>
            
            <div>
                <h4 class="text-amber-400 font-bold text-sm uppercase tracking-wider mb-1">Achievement Unlocked!</h4>
                <p class="text-slate-200 font-medium" x-text="title"></p>
            </div>
            
            <button @click="show = false" class="text-slate-500 hover:text-slate-300 transition-colors ml-auto">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
    </div>

        @livewireScripts
    </body>
</html>
