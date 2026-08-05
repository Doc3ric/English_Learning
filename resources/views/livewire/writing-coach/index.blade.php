<div class="max-w-5xl mx-auto space-y-6">
    <x-slot:header>
        Writing Coach
    </x-slot>

    {{-- ============================================================ --}}
    {{-- STATE: WRITING --}}
    {{-- ============================================================ --}}
    @if($state === 'writing')
        <div class="space-y-6">

            {{-- Error --}}
            @if($errorMessage)
                <div class="ds-card-accent-red p-4 flex items-center gap-3">
                    <svg class="w-5 h-5 text-red-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="text-sm text-red-400">{{ $errorMessage }}</span>
                </div>
            @endif

            {{-- Prompt Card --}}
            <div class="relative overflow-hidden bg-gradient-to-br from-emerald-600/20 via-slate-800 to-slate-900 border border-emerald-500/30 rounded-xl p-8 shadow-lg">
                <div class="absolute top-0 right-0 w-64 h-64 bg-emerald-500/5 rounded-full -translate-y-1/2 translate-x-1/2 pointer-events-none"></div>
                <div class="relative">
                    <div class="flex items-center gap-2 text-emerald-400 mb-4 ds-eyebrow">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Today's Writing Challenge
                    </div>
                    <h2 class="text-2xl font-bold text-white leading-snug">{{ $prompt }}</h2>
                    <p class="ds-body mt-3">Write as naturally as you can in English. Aim for at least 50 words. The AI will analyse your writing and give you detailed feedback.</p>
                </div>
            </div>

            {{-- Writing Area --}}
            <div class="ds-card p-6 space-y-4">
                <div class="flex items-center justify-between mb-2">
                    <label class="ds-label mb-0">Your Response</label>
                    <div class="flex items-center gap-2">
                        <span class="text-xs {{ $wordCount >= 50 ? 'text-emerald-400' : ($wordCount > 0 ? 'text-amber-400' : 'text-slate-500') }} font-mono font-bold tabular-nums">
                            {{ $wordCount }} words
                        </span>
                        @if($wordCount >= 50)
                            <span class="text-xs text-emerald-400">✓ Good length</span>
                        @elseif($wordCount > 0)
                            <span class="text-xs text-amber-400">Aim for 50+</span>
                        @endif
                    </div>
                </div>

                <textarea
                    wire:model.live.debounce.300ms="userResponse"
                    rows="10"
                    placeholder="Start writing here... Express yourself freely and don't worry about being perfect — that's what the coach is for."
                    class="ds-textarea text-base"
                ></textarea>

                {{-- Warning --}}
                @if($showWordWarning)
                    <div class="flex items-center gap-2 text-amber-400 text-sm">
                        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        You've written {{ $wordCount }} words. Consider adding more detail to get richer feedback (50+ recommended).
                    </div>
                @endif

                <div class="flex justify-end pt-2">
                    <button
                        wire:click="submit"
                        wire:loading.attr="disabled"
                        class="ds-btn ds-btn-lg ds-btn-primary"
                    >
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Submit for AI Feedback
                    </button>
                </div>
            </div>
        </div>

    {{-- ============================================================ --}}
    {{-- STATE: LOADING --}}
    {{-- ============================================================ --}}
    @elseif($state === 'loading')
        <div class="flex flex-col items-center justify-center min-h-[400px] space-y-6">
            <div class="relative">
                <div class="w-20 h-20 border-4 border-slate-700 border-t-emerald-500 rounded-full animate-spin"></div>
                <div class="absolute inset-0 flex items-center justify-center">
                    <svg class="w-8 h-8 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </div>
            </div>
            <div class="text-center">
                <h3 class="text-xl font-semibold text-slate-200 mb-2">AI is analysing your writing...</h3>
                <p class="ds-muted">This takes about 5-10 seconds. Hang tight!</p>
            </div>
        </div>

    {{-- ============================================================ --}}
    {{-- STATE: RESULTS --}}
    {{-- ============================================================ --}}
    @elseif($state === 'results' && $result)

        {{-- Header bar --}}
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-slate-100">Your Feedback</h2>
                <p class="ds-muted mt-1">Topic: <span class="text-slate-400">{{ $prompt }}</span></p>
            </div>
            <button wire:click="startNewSession" class="ds-btn ds-btn-sm ds-btn-secondary">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                New Session
            </button>
        </div>

        {{-- 12B: Auto-population summary --}}
        <div class="flex flex-wrap gap-3">
            @if($journalSaved)
                <x-ui.badge variant="emerald">
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Saved to Journal
                </x-ui.badge>
            @endif
            @if($vocabAdded > 0)
                <a href="{{ route('vocabulary') }}" class="hover:opacity-80 transition-opacity">
                    {{-- Remapping purple to slate --}}
                    <x-ui.badge>
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        {{ $vocabAdded }} word{{ $vocabAdded > 1 ? 's' : '' }} added to Vocabulary
                    </x-ui.badge>
                </a>
            @endif
            @if($mistakesAdded > 0)
                <a href="{{ route('mistakes') }}" class="hover:opacity-80 transition-opacity">
                    <x-ui.badge variant="red">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        {{ $mistakesAdded }} mistake{{ $mistakesAdded > 1 ? 's' : '' }} logged
                    </x-ui.badge>
                </a>
            @endif
        </div>

        {{-- Score cards --}}
        @php
            $scores = [
                ['label' => 'Grammar',     'key' => 'grammar_score',     'color' => 'blue'],
                ['label' => 'Vocabulary',  'key' => 'vocabulary_score',  'color' => 'purple'],
                ['label' => 'Naturalness', 'key' => 'naturalness_score', 'color' => 'emerald'],
                ['label' => 'Clarity',     'key' => 'clarity_score',     'color' => 'amber'],
            ];
        @endphp

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach($scores as $s)
                @php
                    $val = $result[$s['key']];
                    $ring = $val >= 80 ? 'text-emerald-400 border-emerald-500/50 bg-emerald-500/10'
                          : ($val >= 60 ? 'text-amber-400 border-amber-500/50 bg-amber-500/10'
                          : 'text-red-400 border-red-500/50 bg-red-500/10');
                @endphp
                <div class="ds-card p-5 flex flex-col items-center gap-2">
                    <div class="w-16 h-16 rounded-full border-4 {{ $ring }} flex items-center justify-center">
                        <span class="text-xl font-black">{{ $val }}</span>
                    </div>
                    <span class="ds-eyebrow text-[10px]">{{ $s['label'] }}</span>
                </div>
            @endforeach
        </div>

        {{-- CEFR + word count --}}
        <div class="flex flex-wrap items-center gap-3">
            <x-ui.badge>
                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                CEFR Estimate: {{ $result['cefr_estimate'] }}
            </x-ui.badge>
            <x-ui.badge>
                {{ $wordCount }} words written
            </x-ui.badge>
        </div>

        {{-- Side-by-side: Original vs Corrected --}}
        <div class="grid md:grid-cols-2 gap-4">
            <div class="ds-card p-6">
                <h3 class="ds-eyebrow mb-4 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-slate-500 inline-block"></span> Your Original
                </h3>
                <div class="ds-body whitespace-pre-wrap">{{ $userResponse }}</div>
            </div>
            <div class="ds-card-accent-emerald p-6">
                <h3 class="ds-eyebrow text-emerald-500 mb-4 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 inline-block"></span> AI Corrected
                </h3>
                <div class="text-slate-200 leading-relaxed text-sm whitespace-pre-wrap">{{ $result['corrected_version'] }}</div>
            </div>
        </div>

        {{-- Explanation --}}
        <div class="ds-card p-6">
            <h3 class="ds-section-title mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                What Changed &amp; Why
            </h3>
            <div class="ds-body whitespace-pre-wrap">{{ $result['explanation'] }}</div>
        </div>

        {{-- Rewrite Challenge --}}
        <div class="ds-card overflow-hidden !p-0">
            <button
                wire:click="$set('showRewriteBox', {{ $showRewriteBox ? 'false' : 'true' }})"
                class="w-full flex items-center justify-between px-6 py-4 text-left hover:bg-slate-800/50 transition-colors"
            >
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    </div>
                    <div>
                        <p class="font-semibold text-slate-200 text-sm">Try Rewriting It Yourself</p>
                        <p class="ds-muted text-xs">Apply the feedback and rewrite your response. Optional but highly effective.</p>
                    </div>
                </div>
                <svg class="w-5 h-5 text-slate-400 transition-transform {{ $showRewriteBox ? 'rotate-180' : '' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>

            @if($showRewriteBox)
                <div class="px-6 pb-6 border-t border-slate-800 pt-4">
                    @if($rewriteSaved)
                        <div class="flex items-center gap-2 text-emerald-400 text-sm py-4">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Rewrite saved! Great work practising.
                        </div>
                    @else
                        <textarea
                            wire:model="rewriteAttempt"
                            rows="6"
                            placeholder="Rewrite your response here, incorporating the AI's corrections..."
                            class="ds-textarea mb-4"
                        ></textarea>
                        <button
                            wire:click="saveRewrite"
                            class="ds-btn ds-btn-md ds-btn-primary"
                        >
                            Save Rewrite
                        </button>
                    @endif
                </div>
            @endif
        </div>

        {{-- 12E: Style Rewrites --}}
        <div class="ds-card p-6">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-8 h-8 rounded-full bg-slate-500/20 text-slate-400 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/></svg>
                </div>
                <div>
                    <p class="font-semibold text-slate-200 text-sm">See It in Another Style</p>
                    <p class="ds-muted text-xs">How would your corrected text sound in a different register?</p>
                </div>
            </div>

            <div class="flex gap-3 mt-4 mb-5">
                <button
                    wire:click="makeProfessional"
                    wire:loading.attr="disabled"
                    wire:target="makeProfessional"
                    class="ds-btn ds-btn-md ds-btn-secondary disabled:opacity-50"
                >
                    <span wire:loading.remove wire:target="makeProfessional">🏢 Make it Professional</span>
                    <span wire:loading wire:target="makeProfessional" class="flex items-center gap-2">
                        <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        Rewriting...
                    </span>
                </button>

                <button
                    wire:click="makeNative"
                    wire:loading.attr="disabled"
                    wire:target="makeNative"
                    class="ds-btn ds-btn-md ds-btn-secondary disabled:opacity-50"
                >
                    <span wire:loading.remove wire:target="makeNative">🗣 Make it Sound Native</span>
                    <span wire:loading wire:target="makeNative" class="flex items-center gap-2">
                        <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        Rewriting...
                    </span>
                </button>
            </div>

            {{-- Style comparison stack --}}
            @if($professionalVersion || $nativeVersion)
                <div class="space-y-4 mt-2">

                    @if($professionalVersion)
                        <div class="ds-card-nested p-4">
                            <p class="ds-eyebrow text-slate-400 mb-2 flex items-center gap-1.5">
                                <span>🏢</span> Professional Version
                            </p>
                            <p class="ds-body whitespace-pre-wrap">{{ $professionalVersion }}</p>
                        </div>
                    @endif

                    @if($nativeVersion)
                        <div class="ds-card-nested p-4">
                            <p class="ds-eyebrow text-slate-400 mb-2 flex items-center gap-1.5">
                                <span>🗣</span> Native Speaker Version
                            </p>
                            <p class="ds-body whitespace-pre-wrap">{{ $nativeVersion }}</p>
                        </div>
                    @endif

                </div>
            @endif
        </div>

    @endif
</div>
