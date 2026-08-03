<div>
    <x-slot:header>
        Dashboard
    </x-slot>

    <div class="max-w-6xl mx-auto space-y-6">

        {{-- ================================================================ --}}
        {{-- ROW 1: Writing Challenge (primary CTA) + Streak + Level --}}
        {{-- ================================================================ --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Writing Challenge — takes up 2/3 --}}
            <div class="lg:col-span-2 relative overflow-hidden bg-gradient-to-br from-emerald-700/20 via-slate-900 to-slate-950 border {{ $writtenToday ? 'border-emerald-500/30' : 'border-emerald-600/40' }} rounded-xl p-8 shadow-lg">
                <div class="absolute top-0 right-0 w-72 h-72 bg-emerald-500/5 rounded-full -translate-y-1/2 translate-x-1/4 pointer-events-none"></div>

                @if($writtenToday)
                    {{-- Already written today --}}
                    <div class="relative">
                        <div class="flex items-center gap-2 text-emerald-400 text-xs font-bold uppercase tracking-widest mb-3">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            Today's Writing — Done ✓
                        </div>
                        <h2 class="text-xl font-bold text-slate-200 mb-1">Great work today!</h2>
                        <p class="text-slate-400 text-sm mb-5 leading-relaxed">You've already completed your writing challenge. Come back tomorrow for a new prompt, or do another session for extra practice.</p>
                        <a href="{{ route('writing-coach') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-300 font-semibold rounded-lg text-sm transition-colors">
                            Write Again (Optional)
                        </a>
                    </div>
                @else
                    {{-- Not written yet --}}
                    <div class="relative">
                        <div class="flex items-center gap-2 text-emerald-400 text-xs font-bold uppercase tracking-widest mb-4">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            Today's Writing Challenge
                        </div>
                        <h2 class="text-xl font-bold text-white leading-snug mb-5">{{ $todayPrompt }}</h2>
                        <a href="{{ route('writing-coach') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-lg transition-all duration-200 shadow-lg hover:shadow-emerald-500/25 text-sm">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                            Start Writing
                        </a>
                    </div>
                @endif
            </div>

            {{-- Streak + Level stacked --}}
            <div class="flex flex-col gap-4">
                {{-- Streak --}}
                <div class="bg-gradient-to-br from-slate-900 to-slate-950 border border-slate-800 rounded-xl p-6 flex items-center justify-between shadow relative overflow-hidden group flex-1">
                    <div class="absolute -right-6 -bottom-6 opacity-5 group-hover:opacity-10 transition-opacity">
                        <svg class="w-32 h-32 text-orange-500" fill="currentColor" viewBox="0 0 24 24"><path d="M17.58,4.03c-1.39-1.38-3.41-1.63-5.06-0.65C12,3.67,11.39,4.2,10.96,4.86C10.53,5.52,10.33,6.29,10.4,7.06 c0.09,1.15,0.72,2.15,1.64,2.77c0.2,0.14,0.41,0.25,0.64,0.34c0.67,0.27,1.4,0.32,2.1,0.15c0.5-0.12,0.97-0.34,1.38-0.64 C16.57,9.37,16.89,8.9,17.1,8.38c0.23-0.56,0.32-1.16,0.27-1.76C17.3,5.77,17.02,4.98,17.58,4.03z M12,22c5.52,0,10-4.48,10-10 c0-4.75-3.31-8.72-7.75-9.74c0.88,1.41,1.1,3.13,0.61,4.71c-0.63,2.02-2.31,3.58-4.38,4.06c-1.29,0.3-2.61,0.06-3.7-0.64 C5.73,9.72,5,8.5,5,7.18C5,6.59,5.12,6,5.34,5.46C3.33,7.17,2,9.75,2,12C2,17.52,6.48,22,12,22z"/></svg>
                    </div>
                    <div class="z-10">
                        <p class="text-slate-400 font-semibold uppercase tracking-wider text-xs mb-1">Streak</p>
                        <div class="flex items-baseline gap-1">
                            <span class="text-4xl font-black text-orange-500">{{ $streak }}</span>
                            <span class="text-slate-500 font-medium">days</span>
                        </div>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-orange-500/10 flex items-center justify-center border border-orange-500/20 z-10 text-2xl">🔥</div>
                </div>

                {{-- Level --}}
                <div class="bg-gradient-to-br from-indigo-900/30 to-slate-950 border border-indigo-500/20 rounded-xl p-6 shadow relative flex-1">
                    <p class="text-indigo-400 font-semibold uppercase tracking-wider text-xs mb-1">Current Level</p>
                    <div class="flex items-end justify-between mb-3">
                        <span class="text-4xl font-black text-white">{{ strtoupper($user->level) }}</span>
                        <span class="text-slate-500 text-sm font-medium">Target: <span class="text-slate-300 font-bold">C1</span></span>
                    </div>
                    @php
                        $levels = ['A1'=>10,'A2'=>30,'B1'=>50,'B2'=>70,'C1'=>90,'C2'=>100];
                        $pct = $levels[strtoupper($user->level)] ?? 10;
                    @endphp
                    <div class="w-full bg-slate-800 rounded-full h-1.5 overflow-hidden">
                        <div class="h-1.5 rounded-full bg-indigo-500 shadow-[0_0_8px_rgba(99,102,241,0.4)] transition-all duration-1000" style="width: {{ $pct }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ================================================================ --}}
        {{-- ROW 2: Yesterday's Improvement + Weakness of the Week --}}
        {{-- ================================================================ --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            {{-- Yesterday's Improvement --}}
            <div class="bg-slate-900 border border-slate-800 rounded-xl p-6 shadow">
                <h3 class="text-sm font-bold text-slate-300 uppercase tracking-wider mb-5 flex items-center gap-2">
                    <svg class="w-4 h-4 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    Yesterday's Improvement
                </h3>

                @if(!$scoreDeltas)
                    <div class="text-center py-6 text-slate-500">
                        <p class="text-sm">No writing session today yet.</p>
                        <p class="text-xs mt-1">Complete today's challenge to see your score deltas here.</p>
                    </div>
                @else
                    @php
                        $scoreLabels = [
                            'grammar_score'     => 'Grammar',
                            'vocabulary_score'  => 'Vocabulary',
                            'naturalness_score' => 'Naturalness',
                            'clarity_score'     => 'Clarity',
                        ];
                    @endphp
                    <div class="space-y-3">
                        @foreach($scoreLabels as $key => $label)
                            @php
                                $d = $scoreDeltas['scores'][$key];
                                $hasYesterday = $d['delta'] !== null;
                                $up = $d['delta'] > 0;
                                $same = $d['delta'] === 0;
                            @endphp
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-slate-400">{{ $label }}</span>
                                <div class="flex items-center gap-3">
                                    <span class="text-sm font-bold text-slate-200 tabular-nums w-8 text-right">{{ $d['today'] }}</span>
                                    @if($hasYesterday)
                                        @if($same)
                                            <span class="text-xs text-slate-500 tabular-nums w-10">—</span>
                                        @elseif($up)
                                            <span class="inline-flex items-center gap-0.5 text-xs font-bold text-emerald-400 tabular-nums">
                                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 15l7-7 7 7"/></svg>
                                                +{{ $d['delta'] }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-0.5 text-xs font-bold text-red-400 tabular-nums">
                                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"/></svg>
                                                {{ $d['delta'] }}
                                            </span>
                                        @endif
                                    @else
                                        <span class="text-xs text-slate-600 w-10">first</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-4 pt-4 border-t border-slate-800 flex items-center justify-between">
                        <span class="text-xs text-slate-500">CEFR estimate</span>
                        <span class="text-xs font-bold text-indigo-400 bg-indigo-500/10 border border-indigo-500/20 px-2 py-0.5 rounded-full">{{ $scoreDeltas['cefr'] }}</span>
                    </div>
                @endif
            </div>

            {{-- Weakness of the Week --}}
            <div class="bg-slate-900 border border-slate-800 rounded-xl p-6 shadow">
                <h3 class="text-sm font-bold text-slate-300 uppercase tracking-wider mb-5 flex items-center gap-2">
                    <svg class="w-4 h-4 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    Weakness of the Month
                </h3>

                @if(!$weekTopWeakness)
                    <div class="text-center py-6 text-slate-500">
                        <p class="text-sm">No mistakes tracked yet.</p>
                        <p class="text-xs mt-1">Submit Writing Coach sessions to start tracking weaknesses.</p>
                    </div>
                @else
                    @php
                        $weakColors = [
                            'Grammar'    => ['ring' => 'ring-red-500/30',    'text' => 'text-red-400',    'bg' => 'bg-red-500/10',    'bar' => 'bg-red-500'],
                            'Vocabulary' => ['ring' => 'ring-amber-500/30',  'text' => 'text-amber-400',  'bg' => 'bg-amber-500/10',  'bar' => 'bg-amber-500'],
                            'Writing'    => ['ring' => 'ring-purple-500/30', 'text' => 'text-purple-400', 'bg' => 'bg-purple-500/10', 'bar' => 'bg-purple-500'],
                        ];
                        $wc = $weakColors[$weekTopWeakness->category] ?? $weakColors['Grammar'];
                    @endphp
                    <div class="flex flex-col items-center justify-center py-4 text-center">
                        <div class="w-20 h-20 rounded-full ring-4 {{ $wc['ring'] }} {{ $wc['bg'] }} flex items-center justify-center mb-4">
                            <span class="text-3xl font-black {{ $wc['text'] }}">⚠</span>
                        </div>
                        <h4 class="text-2xl font-bold {{ $wc['text'] }} mb-1">{{ $weekTopWeakness->category }}</h4>
                        <p class="text-slate-400 text-sm mb-4">{{ $weekTopWeakness->total }} mistake{{ $weekTopWeakness->total > 1 ? 's' : '' }} in the last 30 days</p>
                        <p class="text-slate-500 text-xs leading-relaxed">
                            Your Writing Coach prompts and grammar lessons are now targeting this area to help you improve.
                        </p>
                    </div>
                    <div class="mt-4 pt-4 border-t border-slate-800">
                        <a href="{{ route('stats') }}" class="text-xs text-emerald-400 hover:text-emerald-300 font-medium">View full weakness breakdown →</a>
                    </div>
                @endif
            </div>
        </div>

        {{-- ================================================================ --}}
        {{-- ROW 3: Weekly Goals Snapshot --}}
        {{-- ================================================================ --}}
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-6 shadow">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-sm font-bold text-slate-300 uppercase tracking-wider">Weekly Snapshot</h3>
                <a href="{{ route('stats') }}" class="text-xs text-emerald-400 hover:text-emerald-300 font-medium">Full Stats →</a>
            </div>

            @php
                $v_pct = min(100, ($progress['vocabulary'] / max(1, $goal->target_vocabulary)) * 100);
                $g_pct = min(100, ($progress['grammar']    / max(1, $goal->target_grammar))    * 100);
                $r_pct = min(100, ($progress['reading']    / max(1, $goal->target_reading))    * 100);
                $w_pct = min(100, ($progress['writing']    / max(1, $goal->target_writing))    * 100);
                $s_pct = min(100, ($progress['study_time'] / max(1, $goal->target_study_time)) * 100);

                $rings = [
                    ['label' => 'Vocab',   'pct' => $v_pct, 'color' => 'text-purple-500', 'val' => $progress['vocabulary'],  'target' => $goal->target_vocabulary,  'unit' => 'words'],
                    ['label' => 'Grammar', 'pct' => $g_pct, 'color' => 'text-blue-500',   'val' => $progress['grammar'],     'target' => $goal->target_grammar,     'unit' => 'lessons'],
                    ['label' => 'Reading', 'pct' => $r_pct, 'color' => 'text-amber-500',  'val' => $progress['reading'],     'target' => $goal->target_reading,     'unit' => 'articles'],
                    ['label' => 'Writing', 'pct' => $w_pct, 'color' => 'text-rose-500',   'val' => $progress['writing'],     'target' => $goal->target_writing,     'unit' => 'entries'],
                    ['label' => 'Time',    'pct' => $s_pct, 'color' => 'text-cyan-500',   'val' => $progress['study_time'],  'target' => $goal->target_study_time,  'unit' => 'min'],
                ];
            @endphp

            <div class="grid grid-cols-5 gap-4">
                @foreach($rings as $r)
                    <div class="text-center">
                        <div class="relative w-16 h-16 mx-auto mb-2">
                            <svg class="w-full h-full transform -rotate-90" viewBox="0 0 36 36">
                                <path stroke-dasharray="100, 100" class="text-slate-800" stroke-width="4" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                                <path stroke-dasharray="{{ $r['pct'] }}, 100" class="{{ $r['color'] }}" stroke-width="4" stroke-linecap="round" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                            </svg>
                            <div class="absolute inset-0 flex items-center justify-center text-xs font-bold text-slate-300">{{ round($r['pct']) }}%</div>
                        </div>
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-500">{{ $r['label'] }}</p>
                        <p class="text-xs text-slate-600 tabular-nums">{{ $r['val'] }}/{{ $r['target'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>

    </div>
</div>
