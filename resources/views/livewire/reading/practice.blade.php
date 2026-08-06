<div class="max-w-4xl mx-auto" x-data="readingTimer({{ $hasStarted ? $elapsedSeconds : -1 }})">
    <x-slot:header>
        Practice: {{ $article->title }}
    </x-slot>

    <div class="mb-6 flex justify-between items-center">
        <a href="{{ route('reading') }}" class="ds-muted hover:text-emerald-400 text-sm flex items-center gap-1 transition-colors">
            &larr; Back to Reading Tracker
        </a>
        
        @if($hasStarted)
            <div class="ds-card-nested !py-2 !px-4 rounded-full flex items-center gap-2 text-emerald-400 font-mono text-xl">
                <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span x-text="formattedTime()">00:00</span>
            </div>
        @endif
    </div>

    @if(!$hasStarted)
        <div class="ds-card p-10 text-center">
            <h2 class="text-2xl font-bold text-slate-100 mb-2">{{ $article->title }}</h2>
            <div class="flex items-center justify-center gap-3 mb-8">
                <x-ui.badge>{{ $article->level }}</x-ui.badge>
                @if($article->target_band)
                    <x-ui.badge>Target: Band {{ $article->target_band }}</x-ui.badge>
                @endif
                <span class="ds-muted text-sm flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    {{ $article->recommended_time_minutes }} mins recommended
                </span>
            </div>

            <p class="ds-body mb-8 max-w-2xl mx-auto">
                In this timed practice, the clock will start counting up as soon as you begin. Read the article thoroughly. When you're ready, proceed to the quiz. The timer will keep running while you answer the questions, just like the real IELTS exam.
            </p>

            <button wire:click="startPractice" class="ds-btn ds-btn-lg ds-btn-primary rounded-full px-12">
                Start Reading
            </button>
            
            <div class="mt-8 pt-6 border-t border-slate-800 flex justify-center gap-4">
                <a href="{{ route('reading.questions.create', $article->id) }}" class="ds-muted hover:text-slate-300 text-sm underline">Edit Questions</a>
            </div>
        </div>
    @else
        <div class="ds-card p-8 shadow-lg">
            <div class="prose prose-invert prose-emerald max-w-none font-serif text-lg leading-loose text-slate-300">
                {!! nl2br(e($article->full_text)) !!}
            </div>
            
            <div class="mt-12 pt-8 border-t border-slate-800 flex justify-end">
                <button wire:click="finishReading" class="ds-btn ds-btn-lg ds-btn-primary">
                    I've Finished Reading &rarr;
                </button>
            </div>
        </div>
    @endif

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('readingTimer', (initialElapsed) => ({
                elapsed: initialElapsed,
                interval: null,
                init() {
                    if (this.elapsed >= 0) {
                        this.interval = setInterval(() => {
                            this.elapsed++;
                        }, 1000);
                    }
                },
                formattedTime() {
                    if (this.elapsed < 0) return '00:00';
                    let m = Math.floor(this.elapsed / 60).toString().padStart(2, '0');
                    let s = (this.elapsed % 60).toString().padStart(2, '0');
                    return `${m}:${s}`;
                }
            }))
        })
    </script>
</div>
