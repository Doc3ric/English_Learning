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
                <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded-md text-sm transition-colors {{ request()->routeIs('dashboard') ? 'bg-slate-800 text-emerald-400 font-semibold shadow-sm' : 'font-medium text-slate-400 hover:bg-slate-800/50 hover:text-slate-300' }}">Dashboard</a>
                <a href="{{ route('vocabulary') }}" class="block px-3 py-2 rounded-md text-sm transition-colors {{ request()->routeIs('vocabulary') ? 'bg-slate-800 text-emerald-400 font-semibold shadow-sm' : 'font-medium text-slate-400 hover:bg-slate-800/50 hover:text-slate-300' }}">Vocabulary</a>
                <a href="{{ route('grammar') }}" class="block px-3 py-2 rounded-md text-sm transition-colors {{ request()->routeIs('grammar*') ? 'bg-slate-800 text-emerald-400 font-semibold shadow-sm' : 'font-medium text-slate-400 hover:bg-slate-800/50 hover:text-slate-300' }}">Grammar</a>
                <a href="{{ route('mistakes') }}" class="block px-3 py-2 rounded-md text-sm transition-colors {{ request()->routeIs('mistakes') ? 'bg-slate-800 text-emerald-400 font-semibold shadow-sm' : 'font-medium text-slate-400 hover:bg-slate-800/50 hover:text-slate-300' }}">Mistakes</a>
                <a href="{{ route('reading') }}" class="block px-3 py-2 rounded-md text-sm transition-colors {{ request()->routeIs('reading*') ? 'bg-slate-800 text-emerald-400 font-semibold shadow-sm' : 'font-medium text-slate-400 hover:bg-slate-800/50 hover:text-slate-300' }}">Reading Tracker</a>
                <a href="{{ route('ai-reading') }}" class="block px-3 py-2.5 rounded-md text-sm font-semibold transition-colors {{ request()->routeIs('ai-reading') ? 'bg-sky-600 text-white shadow-lg shadow-sky-500/20' : 'bg-sky-600/10 text-sky-400 border border-sky-600/30 hover:bg-sky-600/20' }}">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        📖 AI Reading
                    </div>
                </a>
                <a href="{{ route('conversation') }}" class="block px-3 py-2.5 rounded-md text-sm font-semibold transition-colors {{ request()->routeIs('conversation') ? 'bg-violet-600 text-white shadow-lg shadow-violet-500/20' : 'bg-violet-600/10 text-violet-400 border border-violet-600/30 hover:bg-violet-600/20' }}">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/></svg>
                        💬 Conversation
                    </div>
                </a>
                
                <p class="px-2 text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2 mt-6">Practice & Track</p>
                <a href="{{ route('journal') }}" class="block px-3 py-2 rounded-md text-sm transition-colors {{ request()->routeIs('journal') ? 'bg-slate-800 text-emerald-400 font-semibold shadow-sm' : 'font-medium text-slate-400 hover:bg-slate-800/50 hover:text-slate-300' }}">Journal</a>
                <a href="{{ route('timer') }}" class="block px-3 py-2 rounded-md text-sm transition-colors {{ request()->routeIs('timer') ? 'bg-slate-800 text-emerald-400 font-semibold shadow-sm' : 'font-medium text-slate-400 hover:bg-slate-800/50 hover:text-slate-300' }}">Study Timer</a>
                <a href="{{ route('timeline') }}" class="block px-3 py-2 rounded-md text-sm transition-colors {{ request()->routeIs('timeline') ? 'bg-slate-800 text-emerald-400 font-semibold shadow-sm' : 'font-medium text-slate-400 hover:bg-slate-800/50 hover:text-slate-300' }}">Timeline</a>
                <a href="{{ route('stats') }}" class="block px-3 py-2 rounded-md text-sm transition-colors {{ request()->routeIs('stats') ? 'bg-slate-800 text-emerald-400 font-semibold shadow-sm' : 'font-medium text-slate-400 hover:bg-slate-800/50 hover:text-slate-300' }}">Stats & Goals</a>
                <a href="{{ route('achievements') }}" class="block px-3 py-2 rounded-md text-sm transition-colors {{ request()->routeIs('achievements') ? 'bg-slate-800 text-emerald-400 font-semibold shadow-sm' : 'font-medium text-slate-400 hover:bg-slate-800/50 hover:text-slate-300' }}">Achievements</a>
            </nav>

            <!-- User Info (Bottom) -->
            <div class="mt-auto pt-6 border-t border-slate-800 p-4">
                <div class="flex items-center gap-3">
                    <div class="h-9 w-9 rounded-full bg-slate-800 flex items-center justify-center text-sm font-bold text-slate-300 shadow-inner">
                        {{ strtoupper(substr(auth()->user()->name ?? 'L', 0, 1)) }}
                    </div>
                    <div class="flex flex-col truncate">
                        <span class="text-sm font-semibold text-slate-200 truncate">{{ auth()->user()->name ?? 'Learner' }}</span>
                        <span class="text-xs text-slate-500 truncate">{{ auth()->user()->email ?? 'learner@englishos.local' }}</span>
                    </div>
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
            
            @if(isset($fullHeight) && $fullHeight)
            <div class="flex-1 overflow-hidden flex flex-col">
                {{ $slot }}
            </div>
            @else
            <div class="flex-1 overflow-y-auto p-8">
                {{ $slot }}
            </div>
            @endif
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
