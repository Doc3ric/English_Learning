<div>
    <x-slot:header>
        Grammar Lesson: {{ $lesson->title }}
    </x-slot>

    <div class="mb-6 flex items-center justify-between">
        <a href="{{ route('grammar') }}" class="text-slate-400 hover:text-emerald-400 text-sm flex items-center gap-1 transition-colors">
            &larr; Back to Roadmap
        </a>
        <button wire:click="$toggle('showQuestionForm')" class="ds-btn ds-btn-sm ds-btn-secondary">
            {{ $showQuestionForm ? 'Cancel' : '+ Add Question' }}
        </button>
    </div>

    @if (session()->has('message'))
        <div class="mb-6 p-3 rounded-lg bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm">
            {{ session('message') }}
        </div>
    @endif

    @if($showQuestionForm)
        <div class="ds-card p-6 mb-8">
            <h3 class="ds-section-title mb-4">Add Quiz Question</h3>
            <form wire:submit="saveQuestion" class="space-y-4">
                <div>
                    <label class="ds-label">Question</label>
                    <textarea wire:model="question" rows="2" class="ds-textarea" required></textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="ds-label">Option A</label>
                        <input type="text" wire:model="option_a" class="ds-input" required>
                    </div>
                    <div>
                        <label class="ds-label">Option B</label>
                        <input type="text" wire:model="option_b" class="ds-input" required>
                    </div>
                    <div>
                        <label class="ds-label">Option C</label>
                        <input type="text" wire:model="option_c" class="ds-input">
                    </div>
                    <div>
                        <label class="ds-label">Option D</label>
                        <input type="text" wire:model="option_d" class="ds-input">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="ds-label">Correct Answer</label>
                        <select wire:model="correct_answer" class="ds-select" required>
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="C">C</option>
                            <option value="D">D</option>
                        </select>
                    </div>
                    <div>
                        <label class="ds-label">Explanation (Optional)</label>
                        <input type="text" wire:model="explanation" class="ds-input">
                    </div>
                </div>
                <button type="submit" class="ds-btn ds-btn-md ds-btn-primary">
                    Add Question
                </button>
            </form>
        </div>
    @endif

    {{-- Lesson content --}}
    <div class="ds-card p-8 mb-8">
        <div class="prose prose-invert prose-emerald max-w-none">
            {!! nl2br(e($lesson->content)) !!}
        </div>
    </div>

    {{-- Quiz CTA --}}
    <div class="text-center">
        @if($lesson->questions->count() > 0)
            {{-- "Take the Quiz" rounded-full → ds-btn-primary (flagged fix) --}}
            <a href="{{ route('grammar.quiz', $lesson->id) }}" class="ds-btn ds-btn-lg ds-btn-primary inline-flex">
                Take the Quiz
            </a>
            <p class="ds-muted mt-3">{{ $lesson->questions->count() }} questions. Score 70% or higher to pass!</p>
        @else
            <div class="ds-card p-6">
                <p class="ds-muted">Add some questions before you can take the quiz.</p>
            </div>
        @endif
    </div>
</div>
