<div>
    <x-slot:header>
        Grammar Roadmap
    </x-slot>

    <div class="flex justify-between items-center mb-6 border-b border-slate-800 pb-4">
        <p class="text-slate-400">Master grammar step by step. Complete a lesson and its quiz to unlock the next one.</p>
        <button wire:click="$toggle('showAddForm')" class="bg-slate-800 hover:bg-slate-700 text-emerald-400 font-medium py-2 px-4 rounded-md transition-colors border border-slate-700 text-sm">
            {{ $showAddForm ? 'Cancel' : '+ Add Lesson' }}
        </button>
    </div>

    @if (session()->has('error'))
        <div class="mb-6 p-3 rounded bg-red-900/20 border border-red-500/30 text-red-400 text-sm">
            {{ session('error') }}
        </div>
    @endif

    @if($showAddForm)
        <div class="bg-slate-900 border border-slate-800 rounded-lg p-6 mb-8">
            <h3 class="text-lg font-semibold text-slate-100 mb-4">Add New Lesson</h3>
            <form wire:submit="save" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">Lesson Title</label>
                    <input type="text" wire:model="title" class="w-full bg-slate-950 border border-slate-800 rounded-md py-2 px-3 text-slate-200 focus:outline-none focus:border-emerald-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">Content</label>
                    <textarea wire:model="content" rows="4" class="w-full bg-slate-950 border border-slate-800 rounded-md py-2 px-3 text-slate-200 focus:outline-none focus:border-emerald-500" required></textarea>
                </div>
                <button type="submit" class="bg-emerald-600 hover:bg-emerald-500 text-slate-950 font-bold py-2 px-6 rounded-md transition-colors">
                    Save Lesson
                </button>
            </form>
        </div>
    @endif

    <div class="space-y-4 relative before:absolute before:inset-0 before:ml-5 before:-translate-x-px md:before:mx-auto md:before:translate-x-0 before:h-full before:w-0.5 before:bg-gradient-to-b before:from-transparent before:via-slate-800 before:to-transparent">
        @forelse($lessons as $lesson)
            @php $isUnlocked = $unlockedStatus[$lesson->id]; @endphp
            <div class="relative flex items-center justify-between md:justify-normal md:odd:flex-row-reverse group is-active">
                <div class="flex items-center justify-center w-10 h-10 rounded-full border-4 {{ $lesson->is_completed ? 'border-emerald-500 bg-emerald-900 text-emerald-400' : ($isUnlocked ? 'border-slate-500 bg-slate-800 text-slate-300' : 'border-slate-800 bg-slate-900 text-slate-600') }} shrink-0 md:order-1 md:group-odd:-translate-x-1/2 md:group-even:translate-x-1/2 shadow flex-shrink-0 z-10 font-bold text-sm">
                    {{ $lesson->order_index }}
                </div>
                <div class="w-[calc(100%-4rem)] md:w-[calc(50%-2.5rem)] p-4 rounded-lg border {{ $lesson->is_completed ? 'border-emerald-500/30 bg-slate-900' : ($isUnlocked ? 'border-slate-600 bg-slate-900' : 'border-slate-800 bg-slate-950 opacity-60') }}">
                    <div class="flex items-center justify-between mb-1">
                        <h4 class="font-bold {{ $isUnlocked ? 'text-slate-100' : 'text-slate-500' }}">{{ $lesson->title }}</h4>
                        @if($lesson->is_completed)
                            <span class="text-xs font-medium text-emerald-400 bg-emerald-400/10 px-2 py-1 rounded">Completed</span>
                        @elseif(!$isUnlocked)
                            <span class="text-xs font-medium text-slate-500 bg-slate-800 px-2 py-1 rounded flex items-center gap-1">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path></svg>
                                Locked
                            </span>
                        @endif
                    </div>
                    <p class="text-sm {{ $isUnlocked ? 'text-slate-400' : 'text-slate-600' }} line-clamp-2 mb-4">{{ $lesson->content }}</p>
                    
                    @if($isUnlocked)
                        <a href="{{ route('grammar.show', $lesson->id) }}" class="inline-block bg-slate-800 hover:bg-slate-700 text-slate-200 text-sm font-medium py-1.5 px-4 rounded transition-colors border border-slate-700">
                            {{ $lesson->is_completed ? 'Review Lesson' : 'Start Lesson' }}
                        </a>
                    @endif
                </div>
            </div>
        @empty
            <div class="text-center text-slate-500 py-12">No grammar lessons added yet.</div>
        @endforelse
    </div>
</div>
