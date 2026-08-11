<div>
    <x-slot:header>
        Dashboard
    </x-slot>

    <div class="max-w-6xl mx-auto space-y-6">

        {{-- ================================================================ --}}
        {{-- HEADER BANNER: Personal Daily English Coach                       --}}
        {{-- ================================================================ --}}
        <div class="bg-gradient-to-br from-slate-900 via-slate-900 to-slate-950 border border-slate-800 rounded-2xl p-6 sm:p-8 shadow-xl flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-violet-500/10 border border-violet-500/20 text-violet-400 text-xs font-semibold uppercase tracking-wider mb-2">
                    🎯 Personal Daily Coach
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight">
                    Good {{ date('H') < 12 ? 'Morning' : (date('H') < 18 ? 'Afternoon' : 'Evening') }}, {{ explode(' ', $user->name ?? 'Learner')[0] }} 👋
                </h1>
                <p class="text-slate-400 text-sm mt-1">Here is what you should focus on today to improve your English precision.</p>
            </div>
            <div class="flex items-center gap-3">
                <div class="bg-slate-800/80 border border-slate-700/80 px-4 py-2.5 rounded-xl text-center">
                    <span class="block text-xl font-black text-orange-500 flex items-center justify-center gap-1">
                        🔥 {{ $streak }}
                    </span>
                    <span class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Day Streak</span>
                </div>
                <div class="bg-slate-800/80 border border-slate-700/80 px-4 py-2.5 rounded-xl text-center">
                    <span class="block text-xl font-black text-emerald-400">
                        {{ $progressPercent }}%
                    </span>
                    <span class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Today's Progress</span>
                </div>
            </div>
        </div>

        {{-- ================================================================ --}}
        {{-- MAIN GRID: 2 COLUMNS (Plan + Weaknesses | Profile + Weekly Stats)  --}}
        {{-- ================================================================ --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- LEFT COLUMN (2/3 width): Today's Plan + Top Weaknesses --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- TODAY'S ENGLISH PLAN CARD --}}
                <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-lg">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-800 mb-5">
                        <div>
                            <h2 class="text-lg font-bold text-white tracking-tight flex items-center gap-2">
                                📋 TODAY'S ENGLISH PLAN
                            </h2>
                            <p class="text-xs text-slate-400 mt-0.5">Recommended activities prioritized by your current state (~{{ $estimatedMinutesTotal }} min total)</p>
                        </div>
                        <span class="text-xs font-bold text-slate-300 px-3 py-1 rounded-full bg-slate-800 border border-slate-700">
                            {{ $completedItems }} / {{ $totalItems }} Completed
                        </span>
                    </div>

                    {{-- Progress Bar --}}
                    <div class="w-full bg-slate-800 rounded-full h-2.5 mb-6 overflow-hidden">
                        <div class="bg-gradient-to-r from-violet-500 to-emerald-400 h-2.5 rounded-full transition-all duration-500"
                             style="width: {{ $progressPercent }}%;"></div>
                    </div>

                    {{-- Plan Items List --}}
                    <div class="space-y-3.5">
                        @foreach($planItems as $index => $item)
                            <div class="p-4 rounded-xl border transition-all flex flex-col sm:flex-row sm:items-center justify-between gap-4 {{ $item->status === 'completed' ? 'bg-slate-900/40 border-slate-800/60 opacity-75' : 'bg-slate-800/40 border-slate-700/70 hover:border-slate-600' }}">
                                <div class="flex items-start gap-3.5">
                                    <div class="mt-0.5">
                                        @if($item->status === 'completed')
                                            <div class="w-6 h-6 rounded-full bg-emerald-500/20 border border-emerald-500/40 flex items-center justify-center text-emerald-400 text-xs font-bold">✓</div>
                                        @else
                                            <div class="w-6 h-6 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center text-slate-400 text-xs font-bold">{{ $index + 1 }}</div>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <h3 class="text-sm font-bold text-white {{ $item->status === 'completed' ? 'line-through text-slate-400' : '' }}">
                                                {{ $item->title }}
                                            </h3>
                                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-800 border border-slate-700 text-slate-400">
                                                ~{{ $item->estimated_minutes }} min
                                            </span>
                                            @if($item->priority >= 50 && $item->status !== 'completed')
                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-500/10 border border-amber-500/30 text-amber-400">
                                                    🔥 High Priority
                                                </span>
                                            @endif
                                        </div>
                                        @if($item->description)
                                            <p class="text-xs text-slate-400 mt-1">{{ $item->description }}</p>
                                        @endif
                                    </div>
                                </div>

                                <div class="self-end sm:self-center">
                                    @if($item->status === 'completed')
                                        <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs font-semibold">
                                            Done ✓
                                        </span>
                                    @else
                                        @php
                                            $routeUrl = $item->route_name ? route($item->route_name, $item->route_params ?? []) : '#';
                                        @endphp
                                        <a href="{{ $routeUrl }}" class="ds-btn ds-btn-sm ds-btn-primary whitespace-nowrap">
                                            @if($item->activity_type === 'vocabulary') Start Review
                                            @elseif($item->activity_type === 'weakness_practice') Fix Weakness
                                            @elseif($item->activity_type === 'conversation') Practice Speaking
                                            @elseif($item->activity_type === 'writing') Start Writing
                                            @else Start Reading
                                            @endif ➔
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if($completedItems === $totalItems && $totalItems > 0)
                        <div class="mt-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-center">
                            <h3 class="text-base font-bold text-emerald-400 flex items-center justify-center gap-2">
                                🎉 DAILY GOAL COMPLETE!
                            </h3>
                            <p class="text-xs text-slate-300 mt-1">You've completed all recommended activities for today. Great dedication!</p>
                        </div>
                    @endif
                </div>

                {{-- YOUR TOP WEAKNESSES PRIORITY CARD --}}
                <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-lg">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-800 mb-4">
                        <div>
                            <h2 class="text-lg font-bold text-white tracking-tight flex items-center gap-2">
                                ⚠️ YOUR TOP WEAKNESSES
                            </h2>
                            <p class="text-xs text-slate-400 mt-0.5">Categories with highest mistake frequency across your practice</p>
                        </div>
                        <a href="{{ route('mistakes') }}" class="text-xs text-violet-400 hover:underline font-semibold">
                            View All Mistakes →
                        </a>
                    </div>

                    @if(count($topWeaknesses) > 0)
                        <div class="space-y-3">
                            @foreach($topWeaknesses as $w)
                                <div class="p-4 rounded-xl bg-slate-800/40 border border-slate-700/60 flex items-center justify-between gap-4">
                                    <div class="flex items-center gap-3">
                                        <span class="text-xl">
                                            @if($w['severity'] === 'red') 🔴 @elseif($w['severity'] === 'amber') 🟡 @else 🟢 @endif
                                        </span>
                                        <div>
                                            <h3 class="text-sm font-bold text-white">{{ $w['category'] }}</h3>
                                            <p class="text-xs text-slate-400 mt-0.5">
                                                {{ $w['count'] }} mistakes logged • {{ $w['accuracy'] }}% accuracy
                                            </p>
                                        </div>
                                    </div>
                                    <a href="{{ route('mistakes.practice', ['category' => $w['category']]) }}"
                                       class="px-3 py-1.5 rounded-lg bg-amber-500/10 hover:bg-amber-500/20 text-amber-400 border border-amber-500/30 text-xs font-semibold transition-colors">
                                        Practice
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-6 text-slate-500 text-xs">
                            No major weaknesses logged yet! Keep practicing to track error patterns.
                        </div>
                    @endif
                </div>

            </div>

            {{-- RIGHT COLUMN (1/3 width): Skill Profile + Weekly Stats + Gamer Level --}}
            <div class="space-y-6">

                {{-- MY ENGLISH PROFILE CARD --}}
                <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-lg">
                    <h2 class="text-base font-bold text-white tracking-tight pb-3 border-b border-slate-800 mb-4 flex items-center gap-2">
                        🇬🇧 MY ENGLISH SKILL PROFILE
                    </h2>
                    <div class="space-y-3">
                        @foreach($skillProfile as $skill => $level)
                            <div class="flex items-center justify-between text-xs p-2.5 rounded-lg bg-slate-800/40 border border-slate-800">
                                <span class="text-slate-300 font-medium">{{ $skill }}</span>
                                <span class="font-bold text-violet-400 px-2 py-0.5 rounded bg-violet-500/10 border border-violet-500/20">{{ $level }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- WEEKLY PROGRESS SUMMARY --}}
                <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-lg">
                    <h2 class="text-base font-bold text-white tracking-tight pb-3 border-b border-slate-800 mb-4 flex items-center gap-2">
                        📈 THIS WEEK'S PROGRESS
                    </h2>
                    <div class="grid grid-cols-2 gap-3 text-center">
                        <div class="p-3 rounded-xl bg-slate-800/40 border border-slate-800">
                            <span class="block text-lg font-black text-emerald-400">{{ $weeklyStats['study_minutes'] }}m</span>
                            <span class="text-[10px] text-slate-400 uppercase font-semibold">Study Time</span>
                        </div>
                        <div class="p-3 rounded-xl bg-slate-800/40 border border-slate-800">
                            <span class="block text-lg font-black text-sky-400">{{ $weeklyStats['words_reviewed'] }}</span>
                            <span class="text-[10px] text-slate-400 uppercase font-semibold">Words Reviewed</span>
                        </div>
                        <div class="p-3 rounded-xl bg-slate-800/40 border border-slate-800">
                            <span class="block text-lg font-black text-violet-400">{{ $weeklyStats['writing_submissions'] }}</span>
                            <span class="text-[10px] text-slate-400 uppercase font-semibold">Writing</span>
                        </div>
                        <div class="p-3 rounded-xl bg-slate-800/40 border border-slate-800">
                            <span class="block text-lg font-black text-amber-400">{{ $weeklyStats['speaking_sessions'] }}</span>
                            <span class="text-[10px] text-slate-400 uppercase font-semibold">Speaking</span>
                        </div>
                    </div>
                </div>

                {{-- GAMER XP & LEVEL CARD --}}
                <div class="bg-gradient-to-br from-slate-900 to-slate-950 border border-slate-800 rounded-2xl p-6 shadow-lg">
                    @php
                        $xpInfo = $user->xp_level_info ?? ['level' => 1, 'xp' => 0, 'current_level_xp' => 0, 'next_level_xp' => 100, 'percent' => 0];
                    @endphp
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">🎮 Gamification Level</span>
                        <span class="text-sm font-black text-amber-400">Level {{ $xpInfo['level'] }}</span>
                    </div>
                    <div class="w-full bg-slate-800 rounded-full h-2 mb-2 overflow-hidden">
                        <div class="bg-gradient-to-r from-amber-500 to-yellow-400 h-2 rounded-full transition-all duration-500"
                             style="width: {{ $xpInfo['percent'] }}%;"></div>
                    </div>
                    <div class="flex justify-between text-[11px] text-slate-400 font-medium">
                        <span>{{ number_format($xpInfo['xp']) }} Total XP</span>
                        <span>{{ number_format($xpInfo['current_level_xp']) }} / {{ number_format($xpInfo['next_level_xp']) }} XP</span>
                    </div>
                </div>

            </div>

        </div>

    </div>
</div>
