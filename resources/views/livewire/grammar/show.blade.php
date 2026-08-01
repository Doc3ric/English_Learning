<div>
    <x-slot:header>
        Grammar Lesson: {{ $lesson->title }}
    </x-slot>

    <div class="mb-6 flex items-center justify-between">
        <a href="{{ route('grammar') }}" class="text-slate-400 hover:text-emerald-400 text-sm flex items-center gap-1 transition-colors">
            &larr; Back to Roadmap
        </a>
        <button wire:click="$toggle('showQuestionForm')" class="bg-slate-800 hover:bg-slate-700 text-emerald-400 font-medium py-1.5 px-3 rounded text-xs transition-colors border border-slate-700">
            {{ $showQuestionForm ? 'Cancel' : '+ Add Question' }}
        </button>
    </div>

    @if (session()->has('message'))
        <div class="mb-6 p-3 rounded bg-emerald-900/20 border border-emerald-500/30 text-emerald-400 text-sm">
            {{ session('message') }}
        </div>
    @endif

    @if($showQuestionForm)
        <div class="bg-slate-900 border border-slate-800 rounded-lg p-6 mb-8">
            <h3 class="text-lg font-semibold text-slate-100 mb-4">Add Quiz Question</h3>
            <form wire:submit="saveQuestion" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">Question</label>
                    <textarea wire:model="question" rows="2" class="w-full bg-slate-950 border border-slate-800 rounded-md py-2 px-3 text-slate-200 focus:outline-none focus:border-emerald-500" required></textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-1">Option A</label>
                        <input type="text" wire:model="option_a" class="w-full bg-slate-950 border border-slate-800 rounded-md py-2 px-3 text-slate-200 focus:outline-none focus:border-emerald-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-1">Option B</label>
                        <input type="text" wire:model="option_b" class="w-full bg-slate-950 border border-slate-800 rounded-md py-2 px-3 text-slate-200 focus:outline-none focus:border-emerald-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-1">Option C</label>
                        <input type="text" wire:model="option_c" class="w-full bg-slate-950 border border-slate-800 rounded-md py-2 px-3 text-slate-200 focus:outline-none focus:border-emerald-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-1">Option D</label>
                        <input type="text" wire:model="option_d" class="w-full bg-slate-950 border border-slate-800 rounded-md py-2 px-3 text-slate-200 focus:outline-none focus:border-emerald-500">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-1">Correct Answer</label>
                        <select wire:model="correct_answer" class="w-full bg-slate-950 border border-slate-800 rounded-md py-2 px-3 text-slate-200 focus:outline-none focus:border-emerald-500" required>
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="C">C</option>
                            <option value="D">D</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-1">Explanation (Optional)</label>
                        <input type="text" wire:model="explanation" class="w-full bg-slate-950 border border-slate-800 rounded-md py-2 px-3 text-slate-200 focus:outline-none focus:border-emerald-500">
                    </div>
                </div>
                <button type="submit" class="bg-emerald-600 hover:bg-emerald-500 text-slate-950 font-bold py-2 px-6 rounded-md transition-colors">
                    Add Question
                </button>
            </form>
        </div>
    @endif

    <div class="bg-slate-900 border border-slate-800 rounded-lg p-8 mb-8">
        <div class="prose prose-invert prose-emerald max-w-none">
            {!! nl2br(e($lesson->content)) !!}
        </div>
    </div>

    <div class="text-center">
        @if($lesson->questions->count() > 0)
            <a href="{{ route('grammar.quiz', $lesson->id) }}" class="inline-block bg-emerald-600 hover:bg-emerald-500 text-slate-950 text-lg font-bold py-4 px-12 rounded-full transition-colors shadow-lg shadow-emerald-900/50">
                Take the Quiz
            </a>
            <p class="text-slate-500 text-sm mt-3">{{ $lesson->questions->count() }} questions. Score 100% to pass!</p>
        @else
            <p class="text-slate-500 p-4 border border-slate-800 border-dashed rounded bg-slate-900/50">Add some questions before you can take the quiz.</p>
        @endif
    </div>
</div>
