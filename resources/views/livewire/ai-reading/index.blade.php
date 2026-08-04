<div>
    <x-slot:header>AI Reading</x-slot>

    <div class="max-w-3xl mx-auto">

        {{-- ── IDLE ─────────────────────────────────────────────────────── --}}
        @if($state === 'idle')
        <div class="space-y-6">
            @if($errorMessage)
                <div class="flex items-center gap-3 p-4 rounded-lg bg-red-500/10 border border-red-500/20 text-red-400 text-sm">
                    <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    {{ $errorMessage }}
                </div>
            @endif

            <div class="relative overflow-hidden bg-gradient-to-br from-sky-900/20 via-slate-900 to-slate-950 border border-sky-600/30 rounded-xl p-10 shadow-xl">
                <div class="absolute -top-16 -right-16 w-64 h-64 bg-sky-500/5 rounded-full pointer-events-none"></div>
                <div class="relative">
                    <div class="flex items-center gap-2 text-sky-400 text-xs font-bold uppercase tracking-widest mb-6">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        Today's Reading
                    </div>
                    <h2 class="text-2xl font-bold text-white leading-snug mb-6">{{ $topic }}</h2>
                    <div class="flex items-center gap-3 mb-8">
                        <span class="px-3 py-1 bg-sky-500/15 border border-sky-500/25 text-sky-300 text-xs font-bold rounded-full">Level {{ $cefrLevel }}</span>
                        <span class="text-slate-500 text-xs">·</span>
                        <span class="text-slate-400 text-xs">300–600 words · 2–4 min read</span>
                    </div>
                    <button wire:click="generate" class="inline-flex items-center gap-2.5 px-7 py-3.5 bg-sky-600 hover:bg-sky-500 text-white font-bold rounded-lg transition-all duration-200 shadow-lg hover:shadow-sky-500/25 text-sm">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        Generate Article
                    </button>
                </div>
            </div>
            <p class="text-slate-500 text-xs text-center">A unique article is generated for you each time using AI — tailored to your {{ $cefrLevel }} level. Topics rotate daily.</p>
        </div>
        @endif

        {{-- ── LOADING ───────────────────────────────────────────────────── --}}
        @if($state === 'loading')
        <div class="flex flex-col items-center justify-center min-h-96 gap-6">
            <div class="relative">
                <div class="w-20 h-20 rounded-full border-4 border-slate-800"></div>
                <div class="w-20 h-20 rounded-full border-4 border-t-sky-500 border-r-sky-400 animate-spin absolute inset-0"></div>
                <div class="absolute inset-0 flex items-center justify-center text-2xl">📖</div>
            </div>
            <div class="text-center">
                <p class="text-slate-200 font-semibold text-lg mb-1">Writing your article...</p>
                <p class="text-slate-500 text-sm">Crafting a {{ $cefrLevel }}-level piece on <span class="text-slate-400 italic">{{ $topic }}</span></p>
            </div>
        </div>
        @endif

        {{-- ── READING ───────────────────────────────────────────────────── --}}
        @if($state === 'reading' && $article)
        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3 flex-wrap">
                    <span class="px-2.5 py-1 bg-sky-500/15 border border-sky-500/25 text-sky-300 text-xs font-bold rounded-full">{{ $article['cefr_level'] }}</span>
                    <span class="flex items-center gap-1.5 text-slate-400 text-xs">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ $article['estimated_read_time'] }} min read
                    </span>
                    <span class="flex items-center gap-1.5 text-slate-400 text-xs">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        {{ number_format($article['word_count']) }} words
                    </span>
                </div>
                <button wire:click="startNew" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 text-sm font-medium rounded-lg transition-colors border border-slate-700">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    New Article
                </button>
            </div>

            <article class="bg-slate-900 border border-slate-800 rounded-xl px-10 py-10 shadow-lg">
                <h1 class="text-2xl font-bold text-white leading-tight mb-2">{{ $article['title'] }}</h1>
                <div class="h-px bg-slate-800 mb-8"></div>
                <div>
                    @foreach(explode("\n", trim($article['article'])) as $paragraph)
                        @if(trim($paragraph))
                            <p class="text-slate-300 leading-8 text-[1.05rem] mb-5 last:mb-0">{{ trim($paragraph) }}</p>
                        @endif
                    @endforeach
                </div>
            </article>

            {{-- 13B CTA --}}
            <div class="bg-gradient-to-r from-indigo-900/20 to-slate-900 border border-indigo-500/20 rounded-xl p-6 flex items-center justify-between gap-4">
                <div>
                    <p class="font-semibold text-slate-200 text-sm mb-1">Ready to test your comprehension?</p>
                    <p class="text-slate-500 text-xs">Summarize what you just read in your own words — from memory.</p>
                </div>
                <button wire:click="startSummary" class="shrink-0 inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-lg transition-all text-sm shadow-lg hover:shadow-indigo-500/20">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                    I've Finished Reading — Write Summary
                </button>
            </div>
        </div>
        @endif

        {{-- ── SUMMARIZING ───────────────────────────────────────────────── --}}
        @if($state === 'summarizing' && $article)
        <div class="space-y-5">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-bold text-slate-200">Write Your Summary</h2>
                <span class="px-2.5 py-1 bg-sky-500/15 border border-sky-500/25 text-sky-300 text-xs font-bold rounded-full">{{ $cefrLevel }}</span>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

                {{-- Left: Article reference (title + first 2 paragraphs only) --}}
                <div class="bg-slate-900 border border-slate-800 rounded-xl p-6 overflow-y-auto max-h-[460px]">
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-4 flex items-center gap-2">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        Article Reference
                    </p>
                    <h3 class="font-bold text-slate-200 text-base leading-snug mb-4">{{ $article['title'] }}</h3>
                    @php
                        $paragraphs = array_values(array_filter(
                            array_map('trim', explode("\n", trim($article['article'])))
                        ));
                        $preview = array_slice($paragraphs, 0, 2);
                    @endphp
                    @foreach($preview as $p)
                        <p class="text-slate-400 text-sm leading-7 mb-3 last:mb-0">{{ $p }}</p>
                    @endforeach
                    @if(count($paragraphs) > 2)
                        <p class="text-slate-600 text-xs mt-4 italic">— rest of article hidden to test your memory —</p>
                    @endif
                </div>

                {{-- Right: Summary textarea --}}
                <div class="bg-slate-900 border border-slate-800 rounded-xl p-6 flex flex-col">
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-4 flex items-center gap-2">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                        Your Summary
                    </p>
                    <p class="text-slate-400 text-xs mb-4 leading-relaxed">Summarize the article in your own words — from memory. Aim for at least 30 words.</p>

                    @if($summaryError)
                        <div class="text-red-400 text-xs mb-3 p-3 bg-red-500/10 rounded-lg border border-red-500/20">{{ $summaryError }}</div>
                    @endif

                    <textarea
                        wire:model.live="summaryResponse"
                        rows="9"
                        placeholder="Write your summary here..."
                        class="flex-1 w-full bg-slate-950 border border-slate-700 rounded-lg px-4 py-3 text-slate-200 placeholder-slate-600 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-sm leading-relaxed resize-none mb-3"
                    ></textarea>

                    {{-- Word count + warning --}}
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-2">
                            @if($showSummaryWarning)
                                <span class="text-amber-400 text-xs flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                    Aim for at least 30 words
                                </span>
                            @endif
                        </div>
                        <span class="text-xs {{ $summaryWordCount >= 30 ? 'text-emerald-400' : 'text-slate-500' }} font-medium tabular-nums">
                            {{ $summaryWordCount }} words
                        </span>
                    </div>

                    <button
                        wire:click="submitSummary"
                        wire:loading.attr="disabled"
                        wire:target="submitSummary"
                        class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-lg transition-all text-sm disabled:opacity-50"
                    >
                        <span wire:loading.remove wire:target="submitSummary">Submit Summary</span>
                        <span wire:loading wire:target="submitSummary" class="flex items-center gap-2">
                            <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                            Evaluating...
                        </span>
                    </button>
                </div>
            </div>
        </div>
        @endif

        {{-- ── SUMMARY RESULTS ──────────────────────────────────────────── --}}
        @if($state === 'summary_results' && $summaryResult)
        <div class="space-y-5">

            {{-- Score header --}}
            <div class="bg-gradient-to-br from-slate-900 to-slate-950 border border-slate-800 rounded-xl p-8 flex flex-col sm:flex-row items-center gap-6 shadow">
                {{-- Score ring --}}
                <div class="relative w-28 h-28 shrink-0">
                    <svg class="w-full h-full transform -rotate-90" viewBox="0 0 36 36">
                        <path stroke-dasharray="100, 100" class="text-slate-800" stroke-width="3.5" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                        @php
                            $sc = $summaryResult['score'];
                            $ringColor = $sc >= 80 ? 'text-emerald-500' : ($sc >= 60 ? 'text-amber-500' : 'text-red-500');
                        @endphp
                        <path stroke-dasharray="{{ $sc }}, 100" class="{{ $ringColor }}" stroke-width="3.5" stroke-linecap="round" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                    </svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                        <span class="text-2xl font-black text-white">{{ $sc }}</span>
                        <span class="text-xs text-slate-500">/100</span>
                    </div>
                </div>

                <div class="flex-1 text-center sm:text-left">
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Overall Feedback</p>
                    <p class="text-slate-200 leading-relaxed">{{ $summaryResult['overall_feedback'] }}</p>
                </div>
            </div>

            {{-- Accuracy + Grammar --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="bg-slate-900 border border-slate-800 rounded-xl p-5">
                    <p class="text-xs font-bold uppercase tracking-wider text-blue-400 mb-3 flex items-center gap-2">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Accuracy
                    </p>
                    <p class="text-slate-300 text-sm leading-relaxed">{{ $summaryResult['accuracy_feedback'] }}</p>
                </div>
                <div class="bg-slate-900 border border-slate-800 rounded-xl p-5">
                    <p class="text-xs font-bold uppercase tracking-wider text-purple-400 mb-3 flex items-center gap-2">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Grammar
                    </p>
                    <p class="text-slate-300 text-sm leading-relaxed">{{ $summaryResult['grammar_feedback'] }}</p>
                </div>
            </div>

            {{-- What You Missed --}}
            @if(!empty($summaryResult['missing_ideas']))
            <div class="bg-slate-900 border border-amber-500/20 rounded-xl p-5">
                <p class="text-xs font-bold uppercase tracking-wider text-amber-400 mb-4 flex items-center gap-2">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    What You Missed
                </p>
                <ul class="space-y-2">
                    @foreach($summaryResult['missing_ideas'] as $idea)
                        <li class="flex items-start gap-2.5 text-slate-300 text-sm leading-relaxed">
                            <span class="text-amber-500 mt-0.5 shrink-0">→</span>
                            {{ $idea }}
                        </li>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- Vocabulary Suggestions --}}
            @if(!empty($summaryResult['vocabulary_suggestions']))
            <div class="bg-slate-900 border border-emerald-500/20 rounded-xl p-5">
                <p class="text-xs font-bold uppercase tracking-wider text-emerald-400 mb-4 flex items-center gap-2">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    Vocabulary Upgrades
                </p>
                <ul class="space-y-2">
                    @foreach($summaryResult['vocabulary_suggestions'] as $suggestion)
                        <li class="flex items-start gap-2.5 text-slate-300 text-sm leading-relaxed">
                            <span class="text-emerald-500 mt-0.5 shrink-0">✦</span>
                            {{ $suggestion }}
                        </li>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- Actions --}}
            <div class="flex flex-col sm:flex-row gap-3">
                <button wire:click="startQuiz" class="flex-1 inline-flex items-center justify-center gap-2 px-5 py-3 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-lg transition-all text-sm shadow-lg hover:shadow-indigo-500/20">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                    Take Comprehension Quiz
                </button>
                <button wire:click="startNew" class="flex-1 inline-flex items-center justify-center gap-2 px-5 py-3 bg-sky-600 hover:bg-sky-500 text-white font-bold rounded-lg transition-all text-sm">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    Generate Another Article
                </button>
            </div>
        </div>
        @endif

        {{-- ── LOADING QUIZ ──────────────────────────────────────────────── --}}
        @if($state === 'loading_quiz')
        <div class="flex flex-col items-center justify-center min-h-96 gap-6">
            <div class="relative">
                <div class="w-20 h-20 rounded-full border-4 border-slate-800"></div>
                <div class="w-20 h-20 rounded-full border-4 border-t-indigo-500 border-r-indigo-400 animate-spin absolute inset-0"></div>
                <div class="absolute inset-0 flex items-center justify-center text-2xl">🤔</div>
            </div>
            <div class="text-center">
                <p class="text-slate-200 font-semibold text-lg mb-1">Generating Quiz...</p>
                <p class="text-slate-500 text-sm">Creating 5 questions based on your {{ $cefrLevel }} level</p>
            </div>
        </div>
        @endif

        {{-- ── QUIZ ──────────────────────────────────────────────────────── --}}
        @if($state === 'quiz' && $quizData)
        <div class="bg-slate-900 border border-slate-800 rounded-lg p-8">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-slate-100">Test Your Comprehension</h3>
                <span class="px-2.5 py-1 bg-sky-500/15 border border-sky-500/25 text-sky-300 text-xs font-bold rounded-full">{{ $cefrLevel }}</span>
            </div>
            
            @if($quizError)
                <div class="mb-6 p-4 rounded-lg bg-red-500/10 border border-red-500/20 text-red-400 text-sm flex items-center gap-2">
                    <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    {{ $quizError }}
                </div>
            @endif

            <div class="space-y-8">
                @foreach($quizData['questions'] as $index => $question)
                    <div class="p-4 border border-slate-800 rounded-lg bg-slate-950">
                        <p class="font-medium text-slate-200 mb-4">
                            <span class="text-indigo-400 font-bold mr-2">{{ $index + 1 }}.</span> {{ $question['question'] }}
                        </p>
                        
                        <div class="space-y-3 pl-6">
                            @foreach($question['options'] as $key => $option)
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <input type="radio" wire:model="quizAnswers.{{ $question['id'] }}" value="{{ $key }}" class="w-4 h-4 text-indigo-500 bg-slate-900 border-slate-700 focus:ring-indigo-500 focus:ring-offset-slate-950">
                                    <span class="text-slate-300 group-hover:text-slate-200 transition-colors">{{ $key }}. {{ $option }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
                
                <div class="pt-4 text-center">
                    <button wire:click="submitQuiz" class="bg-indigo-600 hover:bg-indigo-500 text-white text-lg font-bold py-3 px-10 rounded-full transition-colors shadow-lg hover:shadow-indigo-500/25">
                        Submit Answers
                    </button>
                </div>
            </div>
        </div>
        @endif

        {{-- ── QUIZ RESULTS ──────────────────────────────────────────────── --}}
        @if($state === 'quiz_results' && $quizData)
        <div class="bg-slate-900 border border-slate-800 rounded-lg p-8">
            <div class="text-center mb-10 pb-10 border-b border-slate-800">
                @php
                    $total = count($quizData['questions']);
                    $passed = $quizScore >= ceil($total * 0.7);
                @endphp

                @if($passed)
                    <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-emerald-900/30 text-emerald-400 mb-4">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h2 class="text-3xl font-bold text-slate-100 mb-2">Great Job!</h2>
                @else
                    <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-amber-900/30 text-amber-400 mb-4">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h2 class="text-3xl font-bold text-slate-100 mb-2">Good Practice</h2>
                @endif

                <p class="font-medium text-lg mb-6 {{ $passed ? 'text-emerald-400' : 'text-amber-400' }}">Score: {{ $quizScore }} / {{ $total }}</p>
                
                <div class="flex justify-center gap-3">
                    <button wire:click="startNew" class="inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-sky-600 hover:bg-sky-500 text-white font-bold rounded-full transition-all text-sm">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        Generate New Article
                    </button>
                    <a href="{{ route('writing-coach') }}" class="inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 font-bold rounded-full transition-all text-sm">
                        Go to Writing Coach
                    </a>
                </div>
            </div>

            <h3 class="text-xl font-bold text-slate-100 mb-6">Review Answers</h3>
            
            <div class="space-y-6">
                @foreach($quizData['questions'] as $index => $question)
                    @php
                        $userAnswer = $quizAnswers[$question['id']] ?? null;
                        $isCorrect = $userAnswer === $question['correct_answer'];
                    @endphp
                    <div class="p-4 border {{ $isCorrect ? 'border-emerald-500/30 bg-emerald-900/10' : 'border-red-500/30 bg-red-900/10' }} rounded-lg">
                        <p class="font-medium text-slate-200 mb-4">
                            <span class="{{ $isCorrect ? 'text-emerald-500' : 'text-red-500' }} font-bold mr-2">{{ $index + 1 }}.</span> {{ $question['question'] }}
                        </p>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <p class="text-sm text-slate-500 mb-1">Your Answer:</p>
                                @if($userAnswer)
                                    <p class="{{ $isCorrect ? 'text-emerald-400' : 'text-red-400' }} font-medium flex items-center gap-2">
                                        {{ $userAnswer }}. {{ $question['options'][$userAnswer] ?? 'Unknown' }}
                                        @if($isCorrect)
                                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        @else
                                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        @endif
                                    </p>
                                @else
                                    <p class="text-slate-500 font-medium italic">No answer provided</p>
                                @endif
                            </div>
                            @if(!$isCorrect)
                                <div>
                                    <p class="text-sm text-slate-500 mb-1">Correct Answer:</p>
                                    <p class="text-emerald-400 font-medium">
                                        {{ $question['correct_answer'] }}. {{ $question['options'][$question['correct_answer']] }}
                                    </p>
                                </div>
                            @endif
                        </div>
                        
                        @if(!empty($question['explanation']))
                            <div class="pt-3 border-t {{ $isCorrect ? 'border-emerald-500/20' : 'border-red-500/20' }}">
                                <p class="text-sm text-slate-300"><span class="font-semibold text-slate-400">Explanation:</span> {{ $question['explanation'] }}</p>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
        @endif

    </div>
</div>
