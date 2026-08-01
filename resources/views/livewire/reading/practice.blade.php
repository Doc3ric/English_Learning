<div class="max-w-4xl mx-auto" x-data="readingTimer({{ $hasStarted ? $elapsedSeconds : -1 }})">
    <x-slot:header>
        Practice: {{ $article->title }}
    </x-slot>

    <div class="mb-6 flex justify-between items-center">
        <a href="{{ route('reading') }}" class="text-slate-400 hover:text-emerald-400 text-sm flex items-center gap-1 transition-colors">
            &larr; Back to Reading Tracker
        </a>
        
        @if($hasStarted)
            <div class="bg-slate-900 border border-slate-700 px-4 py-2 rounded-full flex items-center gap-2 text-emerald-400 font-mono text-xl shadow shadow-slate-900">
                <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span x-text="formattedTime()">00:00</span>
            </div>
        @endif
    </div>

    @if(!$hasStarted)
        <div class="bg-slate-900 border border-slate-800 rounded-lg p-10 text-center">
            <h2 class="text-2xl font-bold text-slate-100 mb-2">{{ $article->title }}</h2>
            <div class="flex items-center justify-center gap-3 mb-8">
                <span class="bg-slate-800 text-slate-300 text-sm font-semibold px-3 py-1 rounded border border-slate-700">{{ $article->level }}</span>
                @if($article->target_band)
                    <span class="bg-indigo-900/30 text-indigo-400 text-sm font-semibold px-3 py-1 rounded border border-indigo-500/30">Target: Band {{ $article->target_band }}</span>
                @endif
                <span class="text-slate-400 text-sm flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    {{ $article->recommended_time_minutes }} mins recommended
                </span>
            </div>

            <p class="text-slate-400 mb-8 max-w-2xl mx-auto">
                In this timed practice, the clock will start counting up as soon as you begin. Read the article thoroughly. When you're ready, proceed to the quiz. The timer will keep running while you answer the questions, just like the real IELTS exam.
            </p>

            <button wire:click="startPractice" class="inline-block bg-emerald-600 hover:bg-emerald-500 text-slate-950 text-xl font-bold py-4 px-12 rounded-full transition-colors shadow-lg shadow-emerald-900/50">
                Start Reading
            </button>
            
            <div class="mt-8 pt-6 border-t border-slate-800 flex justify-center gap-4">
                <a href="{{ route('reading.questions.create', $article->id) }}" class="text-slate-500 hover:text-slate-300 text-sm underline">Edit Questions</a>
            </div>
        </div>
    @else
        <div class="bg-slate-900 border border-slate-800 rounded-lg p-8 shadow-lg">
            <div class="prose prose-invert prose-emerald max-w-none font-serif text-lg leading-loose text-slate-300">
                {!! nl2br(e($article->full_text)) !!}
            </div>
            
            <div class="mt-12 pt-8 border-t border-slate-800 flex justify-end">
                <button wire:click="finishReading" class="bg-emerald-600 hover:bg-emerald-500 text-slate-950 font-bold text-lg py-3 px-8 rounded-md transition-colors shadow">
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
