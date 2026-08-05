<div>
    <x-slot:header>
        Grammar Roadmap
    </x-slot>

    <div class="flex justify-between items-center mb-6 pb-4 border-b border-slate-800">
        <p class="ds-muted">Master grammar step by step. Complete a lesson and its quiz to unlock the next one.</p>
        <div class="flex gap-3">
            <button wire:click="$toggle('showAddForm')" class="ds-btn ds-btn-sm ds-btn-secondary">
                {{ $showAddForm ? 'Cancel Manual' : 'Add Manual' }}
            </button>
            @if($nextSlotAvailable)
                <button wire:click="generateNextLesson" wire:loading.attr="disabled" class="ds-btn ds-btn-sm ds-btn-primary">
                    <svg wire:loading wire:target="generateNextLesson" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span>Generate Next Lesson</span>
                </button>
            @endif
        </div>
    </div>

    @if (session()->has('message'))
        <div class="mb-6 p-3 rounded-lg bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-6 p-3 rounded-lg bg-red-500/10 border border-red-500/20 text-red-400 text-sm">
            {{ session('error') }}
        </div>
    @endif

    @if($showAddForm)
        <div class="ds-card p-6 mb-8">
            <h3 class="ds-section-title mb-4">Add Manual Lesson</h3>
            <form wire:submit="save" class="space-y-4">
                <div>
                    <label class="ds-label">Lesson Title</label>
                    <input type="text" wire:model="title" class="ds-input" required>
                </div>
                <div>
                    <label class="ds-label">Content</label>
                    <textarea wire:model="content" rows="4" class="ds-textarea" required></textarea>
                </div>
                <button type="submit" class="ds-btn ds-btn-md ds-btn-primary">
                    Save Lesson
                </button>
            </form>
        </div>
    @endif

    <div class="space-y-4 relative before:absolute before:inset-0 before:ml-5 before:-translate-x-px md:before:mx-auto md:before:translate-x-0 before:h-full before:w-0.5 before:bg-gradient-to-b before:from-transparent before:via-slate-800 before:to-transparent">
        @forelse($lessons as $lesson)
            @php $isUnlocked = $unlockedStatus[$lesson->id]; @endphp
            <div class="relative flex items-center justify-between md:justify-normal md:odd:flex-row-reverse group is-active">

                {{-- Timeline node --}}
                <div class="flex items-center justify-center w-10 h-10 rounded-full border-4 {{ $lesson->is_completed ? 'border-emerald-500 bg-emerald-900 text-emerald-400' : ($isUnlocked ? 'border-slate-500 bg-slate-800 text-slate-300' : 'border-slate-800 bg-slate-900 text-slate-600') }} shrink-0 md:order-1 md:group-odd:-translate-x-1/2 md:group-even:translate-x-1/2 shadow flex-shrink-0 z-10 font-bold text-sm">
                    {{ $lesson->order_index }}
                </div>

                {{-- Lesson card --}}
                <div class="w-[calc(100%-4rem)] md:w-[calc(50%-2.5rem)] p-4 rounded-xl border {{ $lesson->is_completed ? 'border-emerald-500/30 bg-slate-900' : ($isUnlocked ? 'border-slate-700 bg-slate-900' : 'border-slate-800 bg-slate-950 opacity-60') }}">
                    <div class="flex items-center justify-between mb-1">
                        <div class="flex items-center gap-2">
                            <h4 class="font-bold {{ $isUnlocked ? 'text-slate-100' : 'text-slate-500' }}">{{ $lesson->title }}</h4>
                            @if($lesson->is_generated)
                                {{-- AI Generated: slate (retired indigo) --}}
                                <x-ui.badge>AI</x-ui.badge>
                            @endif
                        </div>
                        @if($lesson->is_completed)
                            <x-ui.badge variant="emerald">Completed</x-ui.badge>
                        @elseif(!$isUnlocked)
                            <x-ui.badge>
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path></svg>
                                Locked
                            </x-ui.badge>
                        @endif
                    </div>
                    <p class="ds-muted line-clamp-2 mb-4 {{ !$isUnlocked ? 'text-slate-600' : '' }}">{{ $lesson->content }}</p>

                    @if($isUnlocked)
                        <a href="{{ route('grammar.show', $lesson->id) }}" class="ds-btn ds-btn-sm ds-btn-secondary inline-flex">
                            {{ $lesson->is_completed ? 'Review Lesson' : 'Start Lesson' }}
                        </a>
                    @endif
                </div>

            </div>
        @empty
            {{-- Empty state: rounded-full → ds-btn-primary (flagged fix) --}}
            <div class="text-center py-12">
                <p class="ds-muted mb-6">No grammar lessons yet.</p>
                <button wire:click="generateNextLesson" wire:loading.attr="disabled" class="ds-btn ds-btn-lg ds-btn-primary">
                    <span wire:loading.remove wire:target="generateNextLesson">Start First AI Lesson</span>
                    <span wire:loading wire:target="generateNextLesson">Generating...</span>
                </button>
            </div>
        @endforelse
    </div>
</div>
