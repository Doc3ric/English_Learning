<div>
    <x-slot:header>
        AI Reading
    </x-slot>

    <div class="max-w-3xl mx-auto">

        {{-- ================================================================ --}}
        {{-- IDLE STATE --}}
        {{-- ================================================================ --}}
        @if($state === 'idle')
            <div class="space-y-6">

                {{-- Error banner --}}
                @if($errorMessage)
                    <div class="flex items-center gap-3 p-4 rounded-lg bg-red-500/10 border border-red-500/20 text-red-400 text-sm">
                        <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        {{ $errorMessage }}
                    </div>
                @endif

                {{-- Today's Article Card --}}
                <div class="relative overflow-hidden bg-gradient-to-br from-sky-900/20 via-slate-900 to-slate-950 border border-sky-600/30 rounded-xl p-10 shadow-xl">
                    <div class="absolute -top-16 -right-16 w-64 h-64 bg-sky-500/5 rounded-full pointer-events-none"></div>
                    <div class="absolute -bottom-12 -left-12 w-48 h-48 bg-blue-500/5 rounded-full pointer-events-none"></div>

                    <div class="relative">
                        {{-- Label --}}
                        <div class="flex items-center gap-2 text-sky-400 text-xs font-bold uppercase tracking-widest mb-6">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                            Today's Reading
                        </div>

                        {{-- Topic --}}
                        <h2 class="text-2xl font-bold text-white leading-snug mb-6">
                            {{ $topic }}
                        </h2>

                        {{-- Meta --}}
                        <div class="flex items-center gap-3 mb-8">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-sky-500/15 border border-sky-500/25 text-sky-300 text-xs font-bold rounded-full">
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                                Level {{ $cefrLevel }}
                            </span>
                            <span class="text-slate-500 text-xs">·</span>
                            <span class="text-slate-400 text-xs">~300–600 words</span>
                            <span class="text-slate-500 text-xs">·</span>
                            <span class="text-slate-400 text-xs">2–4 min read</span>
                        </div>

                        {{-- CTA --}}
                        <button
                            wire:click="generate"
                            class="inline-flex items-center gap-2.5 px-7 py-3.5 bg-sky-600 hover:bg-sky-500 text-white font-bold rounded-lg transition-all duration-200 shadow-lg hover:shadow-sky-500/25 text-sm"
                        >
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            Generate Article
                        </button>
                    </div>
                </div>

                {{-- Info blurb --}}
                <p class="text-slate-500 text-xs text-center">
                    A unique article is generated for you each time using AI — tailored to your {{ $cefrLevel }} level. Topics rotate daily.
                </p>
            </div>
        @endif

        {{-- ================================================================ --}}
        {{-- LOADING STATE --}}
        {{-- ================================================================ --}}
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

        {{-- ================================================================ --}}
        {{-- READING STATE --}}
        {{-- ================================================================ --}}
        @if($state === 'reading' && $article)
            <div class="space-y-6">

                {{-- Header bar --}}
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3 flex-wrap">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-sky-500/15 border border-sky-500/25 text-sky-300 text-xs font-bold rounded-full">
                            {{ $article['cefr_level'] }}
                        </span>
                        <span class="flex items-center gap-1.5 text-slate-400 text-xs">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            {{ $article['estimated_read_time'] }} min read
                        </span>
                        <span class="flex items-center gap-1.5 text-slate-400 text-xs">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            {{ number_format($article['word_count']) }} words
                        </span>
                    </div>
                    <button
                        wire:click="startNew"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 text-sm font-medium rounded-lg transition-colors border border-slate-700"
                    >
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        Generate Another
                    </button>
                </div>

                {{-- Article --}}
                <article class="bg-slate-900 border border-slate-800 rounded-xl px-10 py-10 shadow-lg">
                    {{-- Title --}}
                    <h1 class="text-2xl font-bold text-white leading-tight mb-2">
                        {{ $article['title'] }}
                    </h1>
                    <div class="h-px bg-slate-800 mb-8"></div>

                    {{-- Body --}}
                    <div class="prose prose-invert prose-lg max-w-none">
                        @foreach(explode("\n", trim($article['article'])) as $paragraph)
                            @if(trim($paragraph))
                                <p class="text-slate-300 leading-8 text-[1.05rem] mb-5 last:mb-0">{{ trim($paragraph) }}</p>
                            @endif
                        @endforeach
                    </div>
                </article>

                {{-- Topic tag --}}
                <p class="text-center text-slate-600 text-xs">
                    Topic: <span class="text-slate-500 italic">{{ $topic }}</span>
                </p>

            </div>
        @endif

    </div>
</div>
