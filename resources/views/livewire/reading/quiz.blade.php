<div class="max-w-4xl mx-auto">
    <x-slot:header>
        Quiz: {{ $article->title }}
    </x-slot>

    @if(!$isSubmitted)
        <div class="mb-6 flex justify-between items-center" x-data="readingTimer({{ $elapsedSeconds }})">
            <h2 class="text-xl font-bold text-slate-100">Test Your Comprehension</h2>
            
            <div class="bg-slate-900 border border-slate-700 px-4 py-2 rounded-full flex items-center gap-2 text-emerald-400 font-mono text-xl shadow shadow-slate-900">
                <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span x-text="formattedTime()">00:00</span>
            </div>
            
            <script>
                document.addEventListener('alpine:init', () => {
                    Alpine.data('readingTimer', (initialElapsed) => ({
                        elapsed: initialElapsed,
                        interval: null,
                        init() {
                            this.interval = setInterval(() => {
                                this.elapsed++;
                            }, 1000);
                        },
                        formattedTime() {
                            let m = Math.floor(this.elapsed / 60).toString().padStart(2, '0');
                            let s = (this.elapsed % 60).toString().padStart(2, '0');
                            return `${m}:${s}`;
                        }
                    }))
                })
            </script>
        </div>

        <form wire:submit="submitQuiz" class="space-y-6">
            @foreach($questions as $index => $q)
                <div class="bg-slate-900 border border-slate-800 rounded-lg p-6">
                    <p class="text-slate-100 font-medium mb-4 text-lg"><span class="text-emerald-500 font-bold mr-2">{{ $index + 1 }}.</span> {{ $q->question_text }}</p>
                    
                    @if($q->question_type === 'multiple_choice')
                        <div class="space-y-2">
                            <label class="flex items-center gap-3 p-3 rounded border border-slate-800 hover:border-emerald-500/50 cursor-pointer transition-colors bg-slate-950/50 has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-900/10">
                                <input type="radio" wire:model="answers.{{ $q->id }}" value="A" class="text-emerald-500 focus:ring-emerald-500 bg-slate-900 border-slate-700" required>
                                <span class="text-slate-300"><span class="font-bold text-slate-500 mr-2">A.</span> {{ $q->option_a }}</span>
                            </label>
                            <label class="flex items-center gap-3 p-3 rounded border border-slate-800 hover:border-emerald-500/50 cursor-pointer transition-colors bg-slate-950/50 has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-900/10">
                                <input type="radio" wire:model="answers.{{ $q->id }}" value="B" class="text-emerald-500 focus:ring-emerald-500 bg-slate-900 border-slate-700" required>
                                <span class="text-slate-300"><span class="font-bold text-slate-500 mr-2">B.</span> {{ $q->option_b }}</span>
                            </label>
                            @if($q->option_c)
                                <label class="flex items-center gap-3 p-3 rounded border border-slate-800 hover:border-emerald-500/50 cursor-pointer transition-colors bg-slate-950/50 has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-900/10">
                                    <input type="radio" wire:model="answers.{{ $q->id }}" value="C" class="text-emerald-500 focus:ring-emerald-500 bg-slate-900 border-slate-700" required>
                                    <span class="text-slate-300"><span class="font-bold text-slate-500 mr-2">C.</span> {{ $q->option_c }}</span>
                                </label>
                            @endif
                            @if($q->option_d)
                                <label class="flex items-center gap-3 p-3 rounded border border-slate-800 hover:border-emerald-500/50 cursor-pointer transition-colors bg-slate-950/50 has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-900/10">
                                    <input type="radio" wire:model="answers.{{ $q->id }}" value="D" class="text-emerald-500 focus:ring-emerald-500 bg-slate-900 border-slate-700" required>
                                    <span class="text-slate-300"><span class="font-bold text-slate-500 mr-2">D.</span> {{ $q->option_d }}</span>
                                </label>
                            @endif
                        </div>
                    @elseif($q->question_type === 'true_false_not_given')
                        <div class="flex gap-4">
                            <label class="flex-1 text-center p-3 rounded border border-slate-800 hover:border-emerald-500/50 cursor-pointer transition-colors bg-slate-950/50 has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-900/20">
                                <input type="radio" wire:model="answers.{{ $q->id }}" value="True" class="hidden" required>
                                <span class="text-slate-300 font-bold block">True / Yes</span>
                            </label>
                            <label class="flex-1 text-center p-3 rounded border border-slate-800 hover:border-emerald-500/50 cursor-pointer transition-colors bg-slate-950/50 has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-900/20">
                                <input type="radio" wire:model="answers.{{ $q->id }}" value="False" class="hidden" required>
                                <span class="text-slate-300 font-bold block">False / No</span>
                            </label>
                            <label class="flex-1 text-center p-3 rounded border border-slate-800 hover:border-emerald-500/50 cursor-pointer transition-colors bg-slate-950/50 has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-900/20">
                                <input type="radio" wire:model="answers.{{ $q->id }}" value="Not Given" class="hidden" required>
                                <span class="text-slate-300 font-bold block">Not Given</span>
                            </label>
                        </div>
                    @elseif($q->question_type === 'short_answer')
                        <input type="text" wire:model="answers.{{ $q->id }}" class="w-full bg-slate-950 border border-slate-800 rounded-md py-3 px-4 text-slate-200 focus:outline-none focus:border-emerald-500 font-bold" required placeholder="Type your answer...">
                    @endif
                </div>
            @endforeach
            
            <div class="pt-6 flex justify-end">
                <button type="submit" class="bg-emerald-600 hover:bg-emerald-500 text-slate-950 font-bold text-xl py-4 px-12 rounded-full transition-colors shadow-lg shadow-emerald-900/50 w-full md:w-auto">
                    Submit Answers
                </button>
            </div>
        </form>
        
    @else
        <!-- Results Section -->
        <div class="mb-8 p-8 bg-slate-900 border border-slate-800 rounded-lg text-center">
            <h2 class="text-3xl font-bold text-slate-100 mb-2">Practice Completed</h2>
            
            <div class="flex flex-wrap justify-center gap-6 mt-6">
                <div class="bg-slate-950 px-6 py-4 rounded-lg border border-slate-800">
                    <p class="text-slate-500 text-sm font-semibold uppercase tracking-wider mb-1">Score</p>
                    <p class="text-3xl font-bold {{ $score === count($questions) ? 'text-emerald-400' : 'text-blue-400' }}">
                        {{ $score }} <span class="text-slate-500 text-xl">/ {{ count($questions) }}</span>
                    </p>
                </div>
                
                <div class="bg-slate-950 px-6 py-4 rounded-lg border border-slate-800">
                    <p class="text-slate-500 text-sm font-semibold uppercase tracking-wider mb-1">Time Taken</p>
                    <p class="text-3xl font-bold text-slate-200">
                        {{ floor($timeTakenSeconds / 60) }}:{{ str_pad($timeTakenSeconds % 60, 2, '0', STR_PAD_LEFT) }}
                    </p>
                </div>
                
                @if($wpm)
                <div class="bg-slate-950 px-6 py-4 rounded-lg border border-slate-800">
                    <p class="text-slate-500 text-sm font-semibold uppercase tracking-wider mb-1">Reading Speed</p>
                    <p class="text-3xl font-bold text-slate-200">
                        {{ $wpm }} <span class="text-slate-500 text-xl">WPM</span>
                    </p>
                </div>
                @endif
            </div>
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-6">
                <h3 class="text-xl font-bold text-slate-100 border-b border-slate-800 pb-2">Review Your Answers</h3>
                
                @foreach($questions as $index => $q)
                    @php $res = $results[$q->id]; @endphp
                    <div class="bg-slate-900 border {{ $res['is_correct'] ? 'border-emerald-500/30' : 'border-red-500/30' }} rounded-lg p-5">
                        <p class="text-slate-200 font-medium mb-3"><span class="text-slate-400 font-bold mr-2">{{ $index + 1 }}.</span> {{ $q->question_text }}</p>
                        
                        <div class="text-sm space-y-2 mb-3">
                            <p class="text-slate-400">Your Answer: <span class="{{ $res['is_correct'] ? 'text-emerald-400' : 'text-red-400' }} font-bold">{{ $res['user_answer'] ?: 'None' }} {!! $res['is_correct'] ? '&#10003;' : '&#10007;' !!}</span></p>
                            
                            @if(!$res['is_correct'])
                                <p class="text-slate-400">Correct Answer: <span class="text-emerald-400 font-bold">{{ $res['correct_answer'] }}</span></p>
                            @endif
                        </div>
                        
                        @if($q->explanation)
                            <div class="mt-4 pt-3 border-t border-slate-800 text-sm text-slate-400">
                                <span class="font-bold text-slate-500">Explanation:</span> {{ $q->explanation }}
                            </div>
                        @endif
                    </div>
                @endforeach
                
                <!-- Summary Writing Task -->
                <div class="mt-12">
                    <h3 class="text-xl font-bold text-slate-100 border-b border-slate-800 pb-2 mb-6">Write a Summary (IELTS Task 2 Practice)</h3>
                    @livewire('reading.summary', ['articleId' => $article->id])
                </div>
            </div>
            
            <div class="space-y-6">
                <!-- Vocabulary Extraction -->
                <h3 class="text-xl font-bold text-slate-100 border-b border-slate-800 pb-2">Extract Vocabulary</h3>
                <p class="text-slate-400 text-sm mb-4">Did you encounter any new words in the text? Add them to your vocabulary now.</p>
                @livewire('reading.vocabulary-add', ['articleId' => $article->id])
                
                <div class="mt-8 pt-6 border-t border-slate-800 text-center">
                    <a href="{{ route('reading') }}" class="text-slate-400 hover:text-emerald-400 transition-colors underline">Back to Reading Tracker</a>
                </div>
            </div>
        </div>
    @endif
</div>
