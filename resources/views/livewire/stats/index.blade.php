<div class="max-w-5xl mx-auto space-y-8">
    <x-slot:header>
        Stats & Goals
    </x-slot>

    @if (session()->has('message'))
        <div class="p-4 rounded bg-emerald-900/20 border border-emerald-500/30 text-emerald-400 font-medium text-center shadow-lg shadow-emerald-900/20">
            {{ session('message') }}
        </div>
    @endif

    <!-- Top Highlight: Streak & Total Time -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-gradient-to-br from-slate-900 to-slate-950 border border-slate-800 rounded-lg p-8 flex items-center justify-between shadow-lg relative overflow-hidden group">
            <div class="absolute -right-10 -bottom-10 opacity-5 group-hover:opacity-10 transition-opacity">
                <svg class="w-48 h-48 text-orange-500" fill="currentColor" viewBox="0 0 24 24"><path d="M17.58,4.03c-1.39-1.38-3.41-1.63-5.06-0.65C12,3.67,11.39,4.2,10.96,4.86C10.53,5.52,10.33,6.29,10.4,7.06 c0.09,1.15,0.72,2.15,1.64,2.77c0.2,0.14,0.41,0.25,0.64,0.34c0.67,0.27,1.4,0.32,2.1,0.15c0.5-0.12,0.97-0.34,1.38-0.64 C16.57,9.37,16.89,8.9,17.1,8.38c0.23-0.56,0.32-1.16,0.27-1.76C17.3,5.77,17.02,4.98,17.58,4.03z M12,22c5.52,0,10-4.48,10-10 c0-4.75-3.31-8.72-7.75-9.74c0.88,1.41,1.1,3.13,0.61,4.71c-0.63,2.02-2.31,3.58-4.38,4.06c-1.29,0.3-2.61,0.06-3.7-0.64 C5.73,9.72,5,8.5,5,7.18C5,6.59,5.12,6,5.34,5.46C3.33,7.17,2,9.75,2,12C2,17.52,6.48,22,12,22z"></path></svg>
            </div>
            <div class="z-10">
                <p class="text-slate-400 font-semibold uppercase tracking-wider text-sm mb-1">Current Streak</p>
                <div class="flex items-baseline gap-2">
                    <span class="text-6xl font-bold text-orange-500">{{ $streak }}</span>
                    <span class="text-xl text-slate-500 font-medium">Days</span>
                </div>
            </div>
            <div class="w-16 h-16 rounded-full bg-orange-500/10 flex items-center justify-center border border-orange-500/20 z-10">
                <svg class="w-8 h-8 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.879 16.121A3 3 0 1012.015 11L11 14H9c0 .768.293 1.536.879 2.121z"></path></svg>
            </div>
        </div>

        <div class="bg-gradient-to-br from-slate-900 to-slate-950 border border-slate-800 rounded-lg p-8 flex items-center justify-between shadow-lg relative overflow-hidden group">
            <div class="absolute -right-10 -bottom-10 opacity-5 group-hover:opacity-10 transition-opacity">
                <svg class="w-48 h-48 text-emerald-500" fill="currentColor" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div class="z-10">
                <p class="text-slate-400 font-semibold uppercase tracking-wider text-sm mb-1">Total Study Time</p>
                <div class="flex items-baseline gap-2">
                    <span class="text-6xl font-bold text-emerald-500">{{ number_format(floor($overall['study_time'] / 60)) }}</span>
                    <span class="text-xl text-slate-500 font-medium">h</span>
                    <span class="text-4xl font-bold text-emerald-400 ml-1">{{ $overall['study_time'] % 60 }}</span>
                    <span class="text-xl text-slate-500 font-medium">m</span>
                </div>
            </div>
            <div class="w-16 h-16 rounded-full bg-emerald-500/10 flex items-center justify-center border border-emerald-500/20 z-10">
                <svg class="w-8 h-8 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>
    </div>

    <!-- Weekly Goals -->
    <div class="bg-slate-900 border border-slate-800 rounded-lg p-8 shadow-lg">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 border-b border-slate-800 pb-4 gap-4">
            <div>
                <h3 class="text-2xl font-bold text-slate-100">Weekly Goals</h3>
                <p class="text-slate-400 text-sm mt-1">This Week: <span class="font-bold text-slate-300">{{ $startOfWeek }} - {{ $endOfWeek }}</span></p>
            </div>
            
            <button wire:click="$toggle('isEditingGoals')" class="bg-slate-800 hover:bg-slate-700 text-slate-300 font-medium py-2 px-4 rounded-md transition-colors border border-slate-700 text-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                {{ $isEditingGoals ? 'Cancel Edit' : 'Edit Goals' }}
            </button>
        </div>

        @if($isEditingGoals)
            <div class="bg-slate-950 border border-slate-800 rounded-lg p-6 mb-8">
                <form wire:submit="saveGoals" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-1">Vocabulary (Words/Week)</label>
                        <input type="number" wire:model="target_vocabulary" class="w-full bg-slate-900 border border-slate-700 rounded-md py-2 px-3 text-slate-200 focus:outline-none focus:border-emerald-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-1">Grammar (Lessons/Week)</label>
                        <input type="number" wire:model="target_grammar" class="w-full bg-slate-900 border border-slate-700 rounded-md py-2 px-3 text-slate-200 focus:outline-none focus:border-emerald-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-1">Reading (Articles/Week)</label>
                        <input type="number" wire:model="target_reading" class="w-full bg-slate-900 border border-slate-700 rounded-md py-2 px-3 text-slate-200 focus:outline-none focus:border-emerald-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-1">Writing (Entries/Week)</label>
                        <input type="number" wire:model="target_writing" class="w-full bg-slate-900 border border-slate-700 rounded-md py-2 px-3 text-slate-200 focus:outline-none focus:border-emerald-500" required>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-300 mb-1">Total Study Time (Minutes/Week)</label>
                        <input type="number" wire:model="target_study_time" class="w-full bg-slate-900 border border-slate-700 rounded-md py-2 px-3 text-slate-200 focus:outline-none focus:border-emerald-500" required>
                    </div>
                    
                    <div class="md:col-span-2 pt-2">
                        <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-500 text-slate-950 font-bold py-3 px-4 rounded-md transition-colors border border-emerald-500 shadow">
                            Save Goal Targets
                        </button>
                    </div>
                </form>
            </div>
        @endif

        <div class="space-y-8">
            <!-- Vocabulary Goal -->
            @php $v_percent = min(100, ($progress['vocabulary'] / max(1, $goal->target_vocabulary)) * 100); @endphp
            <div>
                <div class="flex justify-between items-end mb-2">
                    <div>
                        <p class="font-bold text-slate-200 text-lg flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-purple-500 block"></span> Vocabulary
                        </p>
                    </div>
                    <div class="text-right">
                        <span class="text-2xl font-bold {{ $progress['vocabulary'] >= $goal->target_vocabulary ? 'text-emerald-400' : 'text-slate-100' }}">{{ $progress['vocabulary'] }}</span>
                        <span class="text-slate-500 font-medium">/ {{ $goal->target_vocabulary }} words</span>
                    </div>
                </div>
                <div class="w-full bg-slate-800 rounded-full h-3 overflow-hidden">
                    <div class="h-3 rounded-full {{ $progress['vocabulary'] >= $goal->target_vocabulary ? 'bg-emerald-500 shadow-[0_0_10px_rgba(16,185,129,0.5)]' : 'bg-purple-500' }} transition-all duration-1000" style="width: {{ $v_percent }}%"></div>
                </div>
            </div>

            <!-- Grammar Goal -->
            @php $g_percent = min(100, ($progress['grammar'] / max(1, $goal->target_grammar)) * 100); @endphp
            <div>
                <div class="flex justify-between items-end mb-2">
                    <div>
                        <p class="font-bold text-slate-200 text-lg flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-blue-500 block"></span> Grammar
                        </p>
                    </div>
                    <div class="text-right">
                        <span class="text-2xl font-bold {{ $progress['grammar'] >= $goal->target_grammar ? 'text-emerald-400' : 'text-slate-100' }}">{{ $progress['grammar'] }}</span>
                        <span class="text-slate-500 font-medium">/ {{ $goal->target_grammar }} lessons</span>
                    </div>
                </div>
                <div class="w-full bg-slate-800 rounded-full h-3 overflow-hidden">
                    <div class="h-3 rounded-full {{ $progress['grammar'] >= $goal->target_grammar ? 'bg-emerald-500 shadow-[0_0_10px_rgba(16,185,129,0.5)]' : 'bg-blue-500' }} transition-all duration-1000" style="width: {{ $g_percent }}%"></div>
                </div>
            </div>

            <!-- Reading Goal -->
            @php $r_percent = min(100, ($progress['reading'] / max(1, $goal->target_reading)) * 100); @endphp
            <div>
                <div class="flex justify-between items-end mb-2">
                    <div>
                        <p class="font-bold text-slate-200 text-lg flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-amber-500 block"></span> Reading
                        </p>
                    </div>
                    <div class="text-right">
                        <span class="text-2xl font-bold {{ $progress['reading'] >= $goal->target_reading ? 'text-emerald-400' : 'text-slate-100' }}">{{ $progress['reading'] }}</span>
                        <span class="text-slate-500 font-medium">/ {{ $goal->target_reading }} articles</span>
                    </div>
                </div>
                <div class="w-full bg-slate-800 rounded-full h-3 overflow-hidden">
                    <div class="h-3 rounded-full {{ $progress['reading'] >= $goal->target_reading ? 'bg-emerald-500 shadow-[0_0_10px_rgba(16,185,129,0.5)]' : 'bg-amber-500' }} transition-all duration-1000" style="width: {{ $r_percent }}%"></div>
                </div>
            </div>

            <!-- Writing Goal -->
            @php $w_percent = min(100, ($progress['writing'] / max(1, $goal->target_writing)) * 100); @endphp
            <div>
                <div class="flex justify-between items-end mb-2">
                    <div>
                        <p class="font-bold text-slate-200 text-lg flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-rose-500 block"></span> Writing
                        </p>
                    </div>
                    <div class="text-right">
                        <span class="text-2xl font-bold {{ $progress['writing'] >= $goal->target_writing ? 'text-emerald-400' : 'text-slate-100' }}">{{ $progress['writing'] }}</span>
                        <span class="text-slate-500 font-medium">/ {{ $goal->target_writing }} entries</span>
                    </div>
                </div>
                <div class="w-full bg-slate-800 rounded-full h-3 overflow-hidden">
                    <div class="h-3 rounded-full {{ $progress['writing'] >= $goal->target_writing ? 'bg-emerald-500 shadow-[0_0_10px_rgba(16,185,129,0.5)]' : 'bg-rose-500' }} transition-all duration-1000" style="width: {{ $w_percent }}%"></div>
                </div>
            </div>

            <!-- Study Time Goal -->
            @php $s_percent = min(100, ($progress['study_time'] / max(1, $goal->target_study_time)) * 100); @endphp
            <div>
                <div class="flex justify-between items-end mb-2">
                    <div>
                        <p class="font-bold text-slate-200 text-lg flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-cyan-500 block"></span> Total Time
                        </p>
                    </div>
                    <div class="text-right">
                        <span class="text-2xl font-bold {{ $progress['study_time'] >= $goal->target_study_time ? 'text-emerald-400' : 'text-slate-100' }}">{{ $progress['study_time'] }}</span>
                        <span class="text-slate-500 font-medium">/ {{ $goal->target_study_time }} mins</span>
                    </div>
                </div>
                <div class="w-full bg-slate-800 rounded-full h-4 overflow-hidden shadow-inner">
                    <div class="h-4 rounded-full {{ $progress['study_time'] >= $goal->target_study_time ? 'bg-emerald-500 shadow-[0_0_10px_rgba(16,185,129,0.5)]' : 'bg-gradient-to-r from-cyan-600 to-cyan-400' }} transition-all duration-1000" style="width: {{ $s_percent }}%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- 12C: Weakness Breakdown -->
    <div class="bg-slate-900 border border-slate-800 rounded-lg p-8 shadow-lg">
        <div class="flex items-start justify-between mb-6 border-b border-slate-800 pb-4">
            <div>
                <h3 class="text-2xl font-bold text-slate-100">Weakness Breakdown</h3>
                <p class="text-slate-400 text-sm mt-1">Mistake categories from the last 30 days — sorted by frequency.</p>
            </div>
            @if($topWeakness)
                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-red-500/10 border border-red-500/20 rounded-full text-red-400 text-xs font-bold shrink-0">
                    ⚠ Top weakness: {{ $topWeakness->category }}
                </div>
            @endif
        </div>

        @if($weaknesses->isEmpty())
            <div class="text-center py-8 text-slate-500">
                <p class="text-sm">No mistakes logged in the last 30 days.</p>
                <p class="text-xs mt-1">Submit a Writing Coach session to start tracking your weaknesses.</p>
            </div>
        @else
            @php
                $maxTotal = $weaknesses->max('total');
                $catColors = [
                    'Grammar'    => ['bar' => 'bg-red-500',    'text' => 'text-red-400',    'bg' => 'bg-red-500/10'],
                    'Vocabulary' => ['bar' => 'bg-amber-500',  'text' => 'text-amber-400',  'bg' => 'bg-amber-500/10'],
                    'Writing'    => ['bar' => 'bg-purple-500', 'text' => 'text-purple-400', 'bg' => 'bg-purple-500/10'],
                    'Pronunciation' => ['bar' => 'bg-blue-500','text' => 'text-blue-400',   'bg' => 'bg-blue-500/10'],
                ];
            @endphp
            <div class="space-y-5">
                @foreach($weaknesses as $w)
                    @php
                        $pct = $maxTotal > 0 ? ($w->total / $maxTotal) * 100 : 0;
                        $c = $catColors[$w->category] ?? ['bar' => 'bg-slate-500', 'text' => 'text-slate-400', 'bg' => 'bg-slate-500/10'];
                    @endphp
                    <div>
                        <div class="flex justify-between items-center mb-1.5">
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-semibold {{ $c['text'] }}">{{ $w->category }}</span>
                                @if($loop->first)
                                    <span class="text-xs px-2 py-0.5 {{ $c['bg'] }} {{ $c['text'] }} rounded-full font-bold border border-current/20">Most Common</span>
                                @endif
                            </div>
                            <span class="text-sm font-bold text-slate-300 tabular-nums">{{ $w->total }} mistake{{ $w->total > 1 ? 's' : '' }}</span>
                        </div>
                        <div class="w-full bg-slate-800 rounded-full h-3 overflow-hidden">
                            <div class="h-3 rounded-full {{ $c['bar'] }} transition-all duration-700" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Overall All-Time Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-slate-900 border border-slate-800 p-6 rounded-lg text-center shadow">
            <p class="text-slate-500 text-xs font-bold uppercase tracking-wider mb-2">Total Words</p>
            <p class="text-3xl font-bold text-slate-200">{{ number_format($overall['vocabulary']) }}</p>
        </div>
        <div class="bg-slate-900 border border-slate-800 p-6 rounded-lg text-center shadow">
            <p class="text-slate-500 text-xs font-bold uppercase tracking-wider mb-2">Total Lessons</p>
            <p class="text-3xl font-bold text-slate-200">{{ number_format($overall['grammar']) }}</p>
        </div>
        <div class="bg-slate-900 border border-slate-800 p-6 rounded-lg text-center shadow">
            <p class="text-slate-500 text-xs font-bold uppercase tracking-wider mb-2">Total Articles</p>
            <p class="text-3xl font-bold text-slate-200">{{ number_format($overall['reading']) }}</p>
        </div>
        <div class="bg-slate-900 border border-slate-800 p-6 rounded-lg text-center shadow">
            <p class="text-slate-500 text-xs font-bold uppercase tracking-wider mb-2">Total Journals</p>
            <p class="text-3xl font-bold text-slate-200">{{ number_format($overall['writing']) }}</p>
        </div>
    </div>
</div>
