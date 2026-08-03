<div>
    <x-slot:header>
        Home Dashboard
    </x-slot>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Today's Mission -->
        <div class="md:col-span-2 bg-slate-900 border border-slate-800 rounded-lg p-6 shadow-sm">
            <h3 class="text-lg font-semibold text-slate-100 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Today's Mission
            </h3>
            
            <div class="space-y-3">
                <label class="flex items-center gap-3 p-3 rounded bg-slate-800/50 border border-slate-800 cursor-pointer hover:bg-slate-800 transition-colors">
                    <input type="checkbox" class="w-5 h-5 rounded border-slate-600 text-emerald-500 focus:ring-emerald-500 bg-slate-950">
    <!-- Top Level & Streak -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Level Card -->
        <div class="bg-gradient-to-br from-indigo-900 to-slate-950 border border-slate-800 rounded-lg p-8 flex flex-col justify-center shadow-lg relative overflow-hidden group">
            <div class="absolute -right-10 -bottom-10 opacity-10 group-hover:opacity-20 transition-opacity">
                <svg class="w-48 h-48 text-indigo-400" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"></path></svg>
            </div>
            <div class="z-10">
                <div class="flex justify-between items-end mb-4">
                    <div>
                        <p class="text-indigo-400 font-semibold uppercase tracking-wider text-sm mb-1">Current Level</p>
                        <h2 class="text-5xl font-bold text-white">{{ strtoupper($user->level) }}</h2>
                    </div>
                    <div class="text-right">
                        <p class="text-slate-500 font-semibold uppercase tracking-wider text-sm mb-1">Target</p>
                        <h3 class="text-3xl font-bold text-slate-300">C1</h3>
                    </div>
                </div>
                <div class="w-full bg-slate-800 rounded-full h-2 overflow-hidden shadow-inner">
                    @php 
                        $levels = ['A1'=>10, 'A2'=>30, 'B1'=>50, 'B2'=>70, 'C1'=>90, 'C2'=>100]; 
                        $pct = $levels[strtoupper($user->level)] ?? 10;
                    @endphp
                    <div class="h-2 rounded-full bg-indigo-500 transition-all duration-1000 shadow-[0_0_10px_rgba(99,102,241,0.5)]" style="width: {{ $pct }}%"></div>
                </div>
            </div>
        </div>

        <!-- Streak Card -->
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
        </div>
    </div>

    <!-- Daily Mission -->
    <div class="bg-slate-900 border border-slate-800 rounded-lg p-8 shadow-lg">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-2xl font-bold text-slate-100">Daily Mission</h3>
            <span class="text-emerald-400 font-bold bg-emerald-400/10 px-3 py-1 rounded-full text-sm">{{ round($missionProgress) }}%</span>
        </div>
        
        <div class="w-full bg-slate-800 rounded-full h-4 overflow-hidden shadow-inner mb-8">
            <div class="h-4 rounded-full bg-emerald-500 transition-all duration-1000 shadow-[0_0_10px_rgba(16,185,129,0.5)]" style="width: {{ $missionProgress }}%"></div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <a href="{{ route('vocabulary') }}" class="flex items-center gap-4 p-4 rounded-lg border {{ $mission['vocab'] ? 'border-emerald-500/30 bg-emerald-900/10' : 'border-slate-800 bg-slate-950 hover:bg-slate-900' }} transition-colors group">
                <div class="w-8 h-8 rounded-full border-2 flex items-center justify-center shrink-0 {{ $mission['vocab'] ? 'border-emerald-500 bg-emerald-500/20 text-emerald-400' : 'border-slate-700 text-slate-700 group-hover:border-slate-500' }}">
                    @if($mission['vocab']) <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg> @endif
                </div>
                <div>
                    <h4 class="font-bold {{ $mission['vocab'] ? 'text-emerald-400' : 'text-slate-300' }}">Learn New Vocabulary</h4>
                    <p class="text-xs text-slate-500">Add and use a new word today.</p>
                </div>
            </a>
            
            <a href="{{ route('grammar') }}" class="flex items-center gap-4 p-4 rounded-lg border {{ $mission['grammar'] ? 'border-emerald-500/30 bg-emerald-900/10' : 'border-slate-800 bg-slate-950 hover:bg-slate-900' }} transition-colors group">
                <div class="w-8 h-8 rounded-full border-2 flex items-center justify-center shrink-0 {{ $mission['grammar'] ? 'border-emerald-500 bg-emerald-500/20 text-emerald-400' : 'border-slate-700 text-slate-700 group-hover:border-slate-500' }}">
                    @if($mission['grammar']) <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg> @endif
                </div>
                <div>
                    <h4 class="font-bold {{ $mission['grammar'] ? 'text-emerald-400' : 'text-slate-300' }}">Complete a Grammar Lesson</h4>
                    <p class="text-xs text-slate-500">Finish one lesson & quiz.</p>
                </div>
            </a>

            <a href="{{ route('reading') }}" class="flex items-center gap-4 p-4 rounded-lg border {{ $mission['reading'] ? 'border-emerald-500/30 bg-emerald-900/10' : 'border-slate-800 bg-slate-950 hover:bg-slate-900' }} transition-colors group">
                <div class="w-8 h-8 rounded-full border-2 flex items-center justify-center shrink-0 {{ $mission['reading'] ? 'border-emerald-500 bg-emerald-500/20 text-emerald-400' : 'border-slate-700 text-slate-700 group-hover:border-slate-500' }}">
                    @if($mission['reading']) <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg> @endif
                </div>
                <div>
                    <h4 class="font-bold {{ $mission['reading'] ? 'text-emerald-400' : 'text-slate-300' }}">Practice Reading</h4>
                    <p class="text-xs text-slate-500">Read an article and take a quiz.</p>
                </div>
            </a>

            <a href="{{ route('journal') }}" class="flex items-center gap-4 p-4 rounded-lg border {{ $mission['journal'] ? 'border-emerald-500/30 bg-emerald-900/10' : 'border-slate-800 bg-slate-950 hover:bg-slate-900' }} transition-colors group">
                <div class="w-8 h-8 rounded-full border-2 flex items-center justify-center shrink-0 {{ $mission['journal'] ? 'border-emerald-500 bg-emerald-500/20 text-emerald-400' : 'border-slate-700 text-slate-700 group-hover:border-slate-500' }}">
                    @if($mission['journal']) <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg> @endif
                </div>
                <div>
                    <h4 class="font-bold {{ $mission['journal'] ? 'text-emerald-400' : 'text-slate-300' }}">Write in Journal</h4>
                    <p class="text-xs text-slate-500">Write your thoughts for today.</p>
                </div>
            </a>

            <a href="{{ route('vocabulary') }}" class="flex items-center gap-4 p-4 rounded-lg border {{ $mission['review'] ? 'border-emerald-500/30 bg-emerald-900/10' : 'border-slate-800 bg-slate-950 hover:bg-slate-900' }} transition-colors group md:col-span-2 lg:col-span-1">
                <div class="w-8 h-8 rounded-full border-2 flex items-center justify-center shrink-0 {{ $mission['review'] ? 'border-emerald-500 bg-emerald-500/20 text-emerald-400' : 'border-slate-700 text-slate-700 group-hover:border-slate-500' }}">
                    @if($mission['review']) <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg> @endif
                </div>
                <div>
                    <h4 class="font-bold {{ $mission['review'] ? 'text-emerald-400' : 'text-slate-300' }}">Review Vocabulary</h4>
                    <p class="text-xs text-slate-500">Master previous days' words.</p>
                </div>
            </a>
        </div>
    </div>

    <!-- Weekly Goals Snapshot -->
    <div class="bg-slate-900 border border-slate-800 rounded-lg p-8 shadow-lg">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold text-slate-100">Weekly Snapshot</h3>
            <a href="{{ route('stats') }}" class="text-sm text-emerald-400 hover:text-emerald-300 font-medium">View Full Stats &rarr;</a>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-6">
            @php 
                $v_pct = min(100, ($progress['vocabulary'] / max(1, $goal->target_vocabulary)) * 100);
                $g_pct = min(100, ($progress['grammar'] / max(1, $goal->target_grammar)) * 100);
                $r_pct = min(100, ($progress['reading'] / max(1, $goal->target_reading)) * 100);
                $w_pct = min(100, ($progress['writing'] / max(1, $goal->target_writing)) * 100);
                $s_pct = min(100, ($progress['study_time'] / max(1, $goal->target_study_time)) * 100);
            @endphp

            <div class="text-center">
                <div class="relative w-20 h-20 mx-auto mb-3">
                    <svg class="w-full h-full transform -rotate-90" viewBox="0 0 36 36">
                        <path stroke-dasharray="100, 100" class="text-slate-800" stroke-width="4" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                        <path stroke-dasharray="{{ $v_pct }}, 100" class="text-purple-500" stroke-width="4" stroke-linecap="round" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                    </svg>
                    <div class="absolute inset-0 flex items-center justify-center font-bold text-slate-200">{{ round($v_pct) }}%</div>
                </div>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Vocab</p>
            </div>

            <div class="text-center">
                <div class="relative w-20 h-20 mx-auto mb-3">
                    <svg class="w-full h-full transform -rotate-90" viewBox="0 0 36 36">
                        <path stroke-dasharray="100, 100" class="text-slate-800" stroke-width="4" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                        <path stroke-dasharray="{{ $g_pct }}, 100" class="text-blue-500" stroke-width="4" stroke-linecap="round" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                    </svg>
                    <div class="absolute inset-0 flex items-center justify-center font-bold text-slate-200">{{ round($g_pct) }}%</div>
                </div>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Grammar</p>
            </div>

            <div class="text-center">
                <div class="relative w-20 h-20 mx-auto mb-3">
                    <svg class="w-full h-full transform -rotate-90" viewBox="0 0 36 36">
                        <path stroke-dasharray="100, 100" class="text-slate-800" stroke-width="4" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                        <path stroke-dasharray="{{ $r_pct }}, 100" class="text-amber-500" stroke-width="4" stroke-linecap="round" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                    </svg>
                    <div class="absolute inset-0 flex items-center justify-center font-bold text-slate-200">{{ round($r_pct) }}%</div>
                </div>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Reading</p>
            </div>

            <div class="text-center">
                <div class="relative w-20 h-20 mx-auto mb-3">
                    <svg class="w-full h-full transform -rotate-90" viewBox="0 0 36 36">
                        <path stroke-dasharray="100, 100" class="text-slate-800" stroke-width="4" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                        <path stroke-dasharray="{{ $w_pct }}, 100" class="text-rose-500" stroke-width="4" stroke-linecap="round" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                    </svg>
                    <div class="absolute inset-0 flex items-center justify-center font-bold text-slate-200">{{ round($w_pct) }}%</div>
                </div>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Writing</p>
            </div>

            <div class="text-center">
                <div class="relative w-20 h-20 mx-auto mb-3">
                    <svg class="w-full h-full transform -rotate-90" viewBox="0 0 36 36">
                        <path stroke-dasharray="100, 100" class="text-slate-800" stroke-width="4" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                        <path stroke-dasharray="{{ $s_pct }}, 100" class="text-cyan-500" stroke-width="4" stroke-linecap="round" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                    </svg>
                    <div class="absolute inset-0 flex items-center justify-center font-bold text-slate-200">{{ round($s_pct) }}%</div>
                </div>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Time</p>
            </div>
        </div>
    </div>
</div>
