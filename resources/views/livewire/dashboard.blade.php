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
                    <span class="block text-xl font-black text-orange-500">🔥 {{ $streak }}</span>
                    <span class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Day Streak</span>
                </div>
                <div class="bg-slate-800/80 border border-slate-700/80 px-4 py-2.5 rounded-xl text-center">
                    <span class="block text-xl font-black text-emerald-400">{{ $progressPercent }}%</span>
                    <span class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Today's Plan</span>
                </div>
            </div>
        </div>

        {{-- ================================================================ --}}
        {{-- MAIN GRID: 2/3 LEFT + 1/3 RIGHT                                   --}}
        {{-- ================================================================ --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- ─── LEFT COLUMN (2/3): Today's Plan + Top Weaknesses ─────── --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- ══ TODAY'S ENGLISH PLAN CARD ══════════════════════════ --}}
                <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-lg">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-800 mb-5">
                        <div>
                            <h2 class="text-lg font-bold text-white tracking-tight flex items-center gap-2">
                                📋 TODAY'S ENGLISH PLAN
                            </h2>
                            <p class="text-xs text-slate-400 mt-0.5">Prioritized by your data (~{{ $estimatedMinutesTotal }} min total)</p>
                        </div>
                        <span class="text-xs font-bold text-slate-300 px-3 py-1 rounded-full bg-slate-800 border border-slate-700">
                            {{ $completedItems }} / {{ $totalItems }} Done
                        </span>
                    </div>

                    {{-- Progress Bar --}}
                    <div class="w-full bg-slate-800 rounded-full h-2.5 mb-6 overflow-hidden">
                        <div class="bg-gradient-to-r from-violet-500 to-emerald-400 h-2.5 rounded-full transition-all duration-700"
                             style="width: {{ $progressPercent }}%;"></div>
                    </div>

                    {{-- Plan Items List --}}
                    <div class="space-y-3.5" x-data>
                        @foreach($planItems as $index => $item)
                            <div class="rounded-xl border transition-all {{ $item->status === 'completed' ? 'bg-slate-900/40 border-slate-800/60 opacity-70' : 'bg-slate-800/40 border-slate-700/70 hover:border-slate-600' }}">
                                <div class="p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                    <div class="flex items-start gap-3.5">
                                        <div class="mt-0.5 flex-shrink-0">
                                            @if($item->status === 'completed')
                                                <div class="w-6 h-6 rounded-full bg-emerald-500/20 border border-emerald-500/40 flex items-center justify-center text-emerald-400 text-xs">✓</div>
                                            @else
                                                <div class="w-6 h-6 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center text-slate-400 text-xs font-bold">{{ $index + 1 }}</div>
                                            @endif
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <h3 class="text-sm font-bold text-white {{ $item->status === 'completed' ? 'line-through text-slate-400' : '' }}">
                                                    {{ $item->title }}
                                                </h3>
                                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-800 border border-slate-700 text-slate-400 flex-shrink-0">
                                                    ~{{ $item->estimated_minutes }} min
                                                </span>
                                                @if($item->priority >= 50 && $item->status !== 'completed')
                                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-500/10 border border-amber-500/30 text-amber-400 flex-shrink-0">
                                                        🔥 High Priority
                                                    </span>
                                                @endif
                                            </div>
                                            <p class="text-xs text-slate-400 mt-1">{{ $item->description }}</p>
                                        </div>
                                    </div>

                                    <div class="self-end sm:self-center flex-shrink-0">
                                        @if($item->status === 'completed')
                                            <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs font-semibold">Done ✓</span>
                                        @else
                                            @php $routeUrl = $item->route_name ? route($item->route_name, $item->route_params ?? []) : '#'; @endphp
                                            <a href="{{ $routeUrl }}" class="ds-btn ds-btn-sm ds-btn-primary whitespace-nowrap">
                                                @if($item->activity_type === 'vocabulary') Start Review
                                                @elseif($item->activity_type === 'weakness_practice') Fix Weakness
                                                @elseif($item->activity_type === 'conversation') Practice Speaking
                                                @elseif($item->activity_type === 'writing') Start Writing
                                                @else Start Reading @endif ➔
                                            </a>
                                        @endif
                                    </div>
                                </div>

                                {{-- ★ Phase 18.2: "Why this?" collapsible reason --}}
                                @if($item->reason && $item->status !== 'completed')
                                    <div x-data="{ open: false }" class="border-t border-slate-800/60">
                                        <button @click="open = !open"
                                                class="w-full flex items-center gap-2 px-4 py-2.5 text-left text-xs text-slate-400 hover:text-slate-300 transition-colors group">
                                            <svg class="w-3.5 h-3.5 flex-shrink-0 transition-transform duration-200 text-violet-400" :class="{'rotate-180': open}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                                            </svg>
                                            <span class="font-semibold text-violet-400 group-hover:text-violet-300">ℹ Why is this today's priority?</span>
                                        </button>
                                        <div x-show="open"
                                             x-transition:enter="transition ease-out duration-200"
                                             x-transition:enter-start="opacity-0 -translate-y-1"
                                             x-transition:enter-end="opacity-100 translate-y-0"
                                             class="px-4 pb-3">
                                            <div class="bg-slate-800/60 border border-slate-700/50 rounded-xl p-3.5 space-y-1.5">
                                                @foreach(explode(' | ', $item->reason) as $line)
                                                    <div class="flex items-start gap-2 text-xs text-slate-300">
                                                        <span class="text-emerald-400 mt-0.5 flex-shrink-0">●</span>
                                                        <span>{{ $line }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    @if($completedItems === $totalItems && $totalItems > 0)
                        <div class="mt-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-center">
                            <h3 class="text-base font-bold text-emerald-400">🎉 DAILY GOAL COMPLETE!</h3>
                            <p class="text-xs text-slate-300 mt-1">You've completed all recommended activities for today. Outstanding work!</p>
                        </div>
                    @endif
                </div>

                {{-- ══ TOP WEAKNESSES PRIORITY CARD ════════════════════════ --}}
                <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-lg">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-800 mb-4">
                        <div>
                            <h2 class="text-lg font-bold text-white tracking-tight flex items-center gap-2">⚠️ YOUR TOP WEAKNESSES</h2>
                            <p class="text-xs text-slate-400 mt-0.5">Categories with highest mistake frequency — fix these first</p>
                        </div>
                        <a href="{{ route('mistakes') }}" class="text-xs text-violet-400 hover:underline font-semibold">View All →</a>
                    </div>

                    @if(count($topWeaknesses) > 0)
                        <div class="space-y-3">
                            @foreach($topWeaknesses as $w)
                                <div class="p-4 rounded-xl bg-slate-800/40 border border-slate-700/60 flex items-center justify-between gap-4">
                                    <div class="flex items-center gap-3">
                                        <span class="text-xl">@if($w['severity'] === 'red') 🔴 @elseif($w['severity'] === 'amber') 🟡 @else 🟢 @endif</span>
                                        <div>
                                            <h3 class="text-sm font-bold text-white">{{ $w['category'] }}</h3>
                                            <p class="text-xs text-slate-400 mt-0.5">{{ $w['count'] }} mistakes logged • {{ $w['accuracy'] }}% reviewed</p>
                                        </div>
                                    </div>
                                    <a href="{{ route('mistakes.practice', ['category' => $w['category']]) }}"
                                       class="px-3 py-1.5 rounded-lg bg-amber-500/10 hover:bg-amber-500/20 text-amber-400 border border-amber-500/30 text-xs font-semibold transition-colors flex-shrink-0">
                                        Practice →
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-6 text-slate-500 text-xs">
                            No mistakes logged yet. Keep using the app and your real weaknesses will surface here.
                        </div>
                    @endif
                </div>

            </div>

            {{-- ─── RIGHT COLUMN (1/3): Skill Profile + Weekly Stats + XP + Reflection ─ --}}
            <div class="space-y-6">

                {{-- ══ ENGLISH SKILL PROFILE (Phase 18.1) ═════════════════ --}}
                <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-lg">
                    <h2 class="text-base font-bold text-white tracking-tight pb-3 border-b border-slate-800 mb-4 flex items-center gap-2">
                        🇬🇧 ENGLISH SKILL PROFILE
                    </h2>
                    <div class="space-y-4">
                        @foreach($skillProfile as $skillName => $skill)
                            <div>
                                <div class="flex items-center justify-between mb-1.5">
                                    <span class="text-xs font-semibold text-slate-300">{{ $skillName }}</span>
                                    <div class="flex items-center gap-2">
                                        @if(isset($weeklyDeltas[$skillName]) && $weeklyDeltas[$skillName] !== null)
                                            @php $delta = $weeklyDeltas[$skillName]; @endphp
                                            <span class="text-[10px] font-bold {{ $delta >= 0 ? 'text-emerald-400' : 'text-red-400' }}">
                                                {{ $delta >= 0 ? '+' : '' }}{{ $delta }}%
                                            </span>
                                        @endif
                                        @if($skill['score'] !== null)
                                            <span class="text-xs font-black {{ $skill['score'] >= 70 ? 'text-emerald-400' : ($skill['score'] >= 55 ? 'text-sky-400' : ($skill['score'] >= 40 ? 'text-amber-400' : 'text-slate-400')) }}">
                                                {{ $skill['score'] }}%
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                @if($skill['score'] !== null)
                                    <div class="w-full bg-slate-800 rounded-full h-2 overflow-hidden">
                                        <div class="h-2 rounded-full transition-all duration-700
                                            {{ $skill['score'] >= 70 ? 'bg-gradient-to-r from-emerald-500 to-teal-400' :
                                               ($skill['score'] >= 55 ? 'bg-gradient-to-r from-sky-500 to-blue-400' :
                                               ($skill['score'] >= 40 ? 'bg-gradient-to-r from-amber-500 to-yellow-400' :
                                               'bg-gradient-to-r from-slate-500 to-slate-400')) }}"
                                             style="width: {{ $skill['score'] }}%;">
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-1.5 mt-1 flex-wrap">
                                        <span class="text-[10px] font-bold text-slate-500">{{ $skill['cefr'] }}</span>
                                        <span class="text-[10px] text-slate-600">·</span>
                                        <span class="text-[10px] text-slate-500">{{ $skill['confidence'] }}</span>
                                        @if(!empty($skill['measurement_note']))
                                            <span class="text-[10px] text-slate-600">·</span>
                                            <span class="text-[10px] text-slate-600 italic">{{ $skill['measurement_note'] }}</span>
                                        @endif
                                    </div>

                                @elseif(!empty($skill['cannot_measure']))
                                    {{-- Listening: cannot be measured by any existing activity --}}
                                    <div class="w-full bg-slate-800/30 border border-slate-800 rounded-lg px-3 py-2 flex items-center gap-2">
                                        <span class="text-slate-600 text-sm">—</span>
                                        <span class="text-[10px] text-slate-600 italic">Cannot be measured yet</span>
                                    </div>
                                    @if(!empty($skill['estimation_note']))
                                        <p class="text-[10px] text-slate-700 mt-1 italic">{{ $skill['estimation_note'] }}</p>
                                    @endif

                                @else
                                    {{-- Not enough data yet, but will unlock with more sessions --}}
                                    <div class="w-full bg-slate-800/50 rounded-full h-2 overflow-hidden">
                                        <div class="h-2 rounded-full bg-slate-700/60 w-full animate-pulse"></div>
                                    </div>
                                    <p class="text-[10px] text-slate-600 mt-1 italic">
                                        @if(!empty($skill['estimation_note']))
                                            {{ $skill['estimation_note'] }}
                                        @else
                                            Complete more sessions to unlock this score
                                        @endif
                                    </p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- ══ WEEKLY PROGRESS SUMMARY ══════════════════════════════ --}}
                <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-lg">
                    <h2 class="text-base font-bold text-white tracking-tight pb-3 border-b border-slate-800 mb-4 flex items-center gap-2">
                        📈 THIS WEEK'S ACTIVITY
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

                {{-- ══ GAMER XP & LEVEL ═════════════════════════════════════ --}}
                <div class="bg-gradient-to-br from-slate-900 to-slate-950 border border-slate-800 rounded-2xl p-6 shadow-lg">
                    @php
                        $xpInfo = $user->xp_level_info ?? ['level' => 1, 'xp' => 0, 'current_level_xp' => 0, 'next_level_xp' => 100, 'percent' => 0];
                        $xpPct  = $xpInfo['required_level_xp'] > 0 ? round(($xpInfo['current_level_xp'] / $xpInfo['required_level_xp']) * 100) : 0;
                    @endphp
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">🎮 Gamification Level</span>
                        <span class="text-sm font-black text-amber-400">Level {{ $xpInfo['level'] }}</span>
                    </div>
                    <div class="w-full bg-slate-800 rounded-full h-2 mb-2 overflow-hidden">
                        <div class="bg-gradient-to-r from-amber-500 to-yellow-400 h-2 rounded-full transition-all duration-700"
                             style="width: {{ $xpPct }}%;"></div>
                    </div>
                    <div class="flex justify-between text-[11px] text-slate-400 font-medium">
                        <span>{{ number_format($user->xp ?? 0) }} Total XP</span>
                        <span>{{ number_format($xpInfo['current_level_xp']) }} / {{ number_format($xpInfo['required_level_xp']) }} XP</span>
                    </div>
                </div>

                {{-- ══ DAILY REFLECTION (Phase 18.4) — shown after 6 PM ════ --}}
                @if($showReflectionCard)
                    <livewire:daily-reflection-component />
                @endif

            </div>

        </div>

    </div>
</div>
