<div class="max-w-3xl mx-auto">
    <x-slot:header>
        Quiz: {{ $lesson->title }}
    </x-slot>

    <div class="mb-6">
        <a href="{{ route('grammar.show', $lesson->id) }}" class="text-slate-400 hover:text-emerald-400 text-sm flex items-center gap-1 transition-colors">
            &larr; Back to Lesson
        </a>
    </div>

    @if(!$submitted)
        <div class="bg-slate-900 border border-slate-800 rounded-lg p-8">
            <h3 class="text-xl font-bold text-slate-100 mb-6">Test Your Knowledge</h3>
            
            <form wire:submit="submitQuiz" class="space-y-8">
                @foreach($lesson->questions as $index => $question)
                    <div class="p-4 border border-slate-800 rounded-lg bg-slate-950">
                        <p class="font-medium text-slate-200 mb-4"><span class="text-emerald-500 font-bold mr-2">{{ $index + 1 }}.</span> {{ $question->question }}</p>
                        
                        <div class="space-y-3 pl-6">
                            @foreach(['A' => $question->option_a, 'B' => $question->option_b, 'C' => $question->option_c, 'D' => $question->option_d] as $key => $option)
                                @if($option)
                                    <label class="flex items-center gap-3 cursor-pointer group">
                                        <input type="radio" wire:model="answers.{{ $question->id }}" value="{{ $key }}" class="w-4 h-4 text-emerald-500 bg-slate-900 border-slate-700 focus:ring-emerald-500 focus:ring-offset-slate-950" required>
                                        <span class="text-slate-300 group-hover:text-slate-200 transition-colors">{{ $key }}. {{ $option }}</span>
                                    </label>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endforeach
                
                <div class="pt-4 text-center">
                    <button type="submit" class="bg-emerald-600 hover:bg-emerald-500 text-slate-950 text-lg font-bold py-3 px-10 rounded-full transition-colors">
                        Submit Answers
                    </button>
                </div>
            </form>
        </div>
    @else
        <div class="bg-slate-900 border border-slate-800 rounded-lg p-8">
            <div class="text-center mb-10 pb-10 border-b border-slate-800">
                @if($passed)
                    <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-emerald-900/30 text-emerald-400 mb-4">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h2 class="text-3xl font-bold text-slate-100 mb-2">Lesson Completed!</h2>
                    <p class="text-emerald-400 font-medium text-lg mb-6">Score: {{ $score }} / {{ $lesson->questions->count() }}</p>
                    <p class="text-slate-400 mb-8">You've unlocked the next lesson on your roadmap.</p>
                    <a href="{{ route('grammar') }}" class="inline-block bg-emerald-600 hover:bg-emerald-500 text-slate-950 font-bold py-3 px-8 rounded-full transition-colors">
                        Continue to Next Lesson
                    </a>
                @else
                    <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-red-900/30 text-red-400 mb-4">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h2 class="text-3xl font-bold text-slate-100 mb-2">Keep Practicing</h2>
                    <p class="text-red-400 font-medium text-lg mb-6">Score: {{ $score }} / {{ $lesson->questions->count() }}</p>
                    <p class="text-slate-400 mb-8">You need 70% or higher to pass. Review the material and try again.</p>
                    <button wire:click="retry" class="inline-block bg-slate-800 hover:bg-slate-700 text-slate-200 font-medium py-3 px-8 rounded-full transition-colors border border-slate-700">
                        Retry Quiz
                    </button>
                @endif
            </div>

            <h3 class="text-xl font-bold text-slate-100 mb-6">Review Answers</h3>
            
            <div class="space-y-6">
                @foreach($lesson->questions as $index => $question)
                    @php
                        $userAnswer = $answers[$question->id] ?? null;
                        $isCorrect = $userAnswer === $question->correct_answer;
                    @endphp
                    <div class="p-4 border {{ $isCorrect ? 'border-emerald-500/30 bg-emerald-900/10' : 'border-red-500/30 bg-red-900/10' }} rounded-lg">
                        <p class="font-medium text-slate-200 mb-4">
                            <span class="{{ $isCorrect ? 'text-emerald-500' : 'text-red-500' }} font-bold mr-2">{{ $index + 1 }}.</span> {{ $question->question }}
                        </p>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <p class="text-sm text-slate-500 mb-1">Your Answer:</p>
                                <p class="{{ $isCorrect ? 'text-emerald-400' : 'text-red-400' }} font-medium flex items-center gap-2">
                                    {{ $userAnswer }}. {{ $question->{'option_'.strtolower($userAnswer)} ?? 'N/A' }}
                                    @if($isCorrect)
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    @else
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    @endif
                                </p>
                            </div>
                            @if(!$isCorrect)
                                <div>
                                    <p class="text-sm text-slate-500 mb-1">Correct Answer:</p>
                                    <p class="text-emerald-400 font-medium">
                                        {{ $question->correct_answer }}. {{ $question->{'option_'.strtolower($question->correct_answer)} }}
                                    </p>
                                </div>
                            @endif
                        </div>
                        
                        @if($question->explanation)
                            <div class="pt-3 border-t {{ $isCorrect ? 'border-emerald-500/20' : 'border-red-500/20' }}">
                                <p class="text-sm text-slate-300"><span class="font-semibold text-slate-400">Explanation:</span> {{ $question->explanation }}</p>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
