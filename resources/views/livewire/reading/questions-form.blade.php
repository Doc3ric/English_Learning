<div class="max-w-4xl mx-auto">
    <x-slot:header>
        Add Questions: {{ $article->title }}
    </x-slot>

    <div class="mb-6 flex justify-between items-center">
        <a href="{{ route('reading') }}" class="ds-muted hover:text-emerald-400 text-sm flex items-center gap-1 transition-colors">
            &larr; Back to Reading Tracker
        </a>
        <a href="{{ route('reading.practice', $article->id) }}" class="ds-btn ds-btn-sm ds-btn-primary">
            Done Adding Questions
        </a>
    </div>

    @if (session()->has('message'))
        <div class="mb-6 p-3 rounded-lg bg-emerald-900/20 border border-emerald-500/30 text-emerald-400 text-sm">
            {{ session('message') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        
        <!-- Add Question Form -->
        <div class="ds-card p-6 h-fit">
            <h3 class="text-lg font-bold text-slate-100 mb-6 border-b border-slate-800 pb-2">Add New Question</h3>
            <form wire:submit="saveQuestion" class="space-y-5">
                
                <div>
                    <label class="ds-label">Question Type</label>
                    <select wire:model.live="question_type" class="ds-select">
                        <option value="multiple_choice">Multiple Choice</option>
                        <option value="true_false_not_given">True / False / Not Given</option>
                        <option value="short_answer">Short Answer (Fill in the blank)</option>
                    </select>
                </div>

                <div>
                    <label class="ds-label">Question Text</label>
                    <textarea wire:model="question_text" rows="3" class="ds-textarea" required></textarea>
                    @error('question_text') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                @if($question_type === 'multiple_choice')
                    <div class="space-y-3 p-4 ds-card-nested border border-slate-800">
                        <p class="ds-eyebrow mb-2">Options</p>
                        <div>
                            <label class="ds-label mb-1">A.</label>
                            <input type="text" wire:model="option_a" class="ds-input !py-1.5" required>
                        </div>
                        <div>
                            <label class="ds-label mb-1">B.</label>
                            <input type="text" wire:model="option_b" class="ds-input !py-1.5" required>
                        </div>
                        <div>
                            <label class="ds-label mb-1">C. <span class="text-slate-500 font-normal">(Optional)</span></label>
                            <input type="text" wire:model="option_c" class="ds-input !py-1.5">
                        </div>
                        <div>
                            <label class="ds-label mb-1">D. <span class="text-slate-500 font-normal">(Optional)</span></label>
                            <input type="text" wire:model="option_d" class="ds-input !py-1.5">
                        </div>
                    </div>
                @endif

                <div>
                    <label class="ds-label">Correct Answer</label>
                    
                    @if($question_type === 'multiple_choice')
                        <select wire:model="correct_answer" class="ds-select text-emerald-400 font-bold" required>
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="C">C</option>
                            <option value="D">D</option>
                        </select>
                    @elseif($question_type === 'true_false_not_given')
                        <select wire:model="correct_answer" class="ds-select text-emerald-400 font-bold" required>
                            <option value="True">True / Yes</option>
                            <option value="False">False / No</option>
                            <option value="Not Given">Not Given</option>
                        </select>
                    @else
                        <input type="text" wire:model="correct_answer" class="ds-input text-emerald-400 font-bold" required placeholder="Exact text expected...">
                    @endif
                    @error('correct_answer') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="ds-label">Explanation (Optional)</label>
                    <textarea wire:model="explanation" rows="2" class="ds-textarea" placeholder="Where in the text can this answer be found?"></textarea>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full ds-btn ds-btn-md ds-btn-secondary !text-emerald-400">
                        + Add Question to Article
                    </button>
                </div>
            </form>
        </div>

        <!-- Question List -->
        <div>
            <h3 class="text-lg font-bold text-slate-100 mb-4 border-b border-slate-800 pb-2">Existing Questions ({{ count($questions) }})</h3>
            <div class="space-y-4">
                @forelse($questions as $index => $q)
                    <div class="ds-card p-4 relative group">
                        <button wire:click="deleteQuestion({{ $q->id }})" class="absolute top-3 right-3 text-slate-500 hover:text-red-400 transition-colors opacity-0 group-hover:opacity-100" title="Delete">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                        
                        <div class="flex items-start gap-2 mb-2 pr-6">
                            <span class="text-emerald-500 font-bold">{{ $index + 1 }}.</span>
                            <p class="text-slate-200 font-medium text-sm">{{ $q->question_text }}</p>
                        </div>
                        
                        <div class="pl-6 text-xs text-slate-400 mb-2">
                            Type: <span class="text-slate-300 font-semibold">{{ ucwords(str_replace('_', ' ', $q->question_type)) }}</span>
                        </div>

                        <div class="pl-6 ds-card-nested p-2 text-sm">
                            <span class="text-slate-500">Answer:</span> <span class="text-emerald-400 font-bold">{{ $q->correct_answer }}</span>
                            @if($q->question_type === 'multiple_choice')
                                <span class="text-slate-400 ml-1">({{ current(array_filter([
                                    'A' => $q->option_a, 'B' => $q->option_b, 'C' => $q->option_c, 'D' => $q->option_d
                                ], fn($k) => $k === $q->correct_answer, ARRAY_FILTER_USE_KEY)) }})</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <x-ui.empty-state
                        icon="❓"
                        title="No questions added yet."
                        body="Use the form to build your quiz."
                    />
                @endforelse
            </div>
        </div>
        
    </div>
</div>
