<div>
    <x-slot:header>
        Reading Tracker (IELTS Practice)
    </x-slot>

    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="ds-card p-6 flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-emerald-900/30 text-emerald-400 flex items-center justify-center shrink-0 border border-emerald-500/20">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
            </div>
            <div>
                <p class="ds-eyebrow mb-1">Total Practices</p>
                <p class="text-2xl font-bold text-slate-100">{{ $stats['total_attempts'] }}</p>
            </div>
        </div>
        
        <div class="ds-card p-6 flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-blue-900/30 text-blue-400 flex items-center justify-center shrink-0 border border-blue-500/20">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <p class="ds-eyebrow mb-1">Avg Score</p>
                <p class="text-2xl font-bold text-slate-100">{{ $stats['avg_score'] }}%</p>
            </div>
        </div>
        
        <div class="ds-card p-6 flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-purple-900/30 text-purple-400 flex items-center justify-center shrink-0 border border-purple-500/20">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <p class="ds-eyebrow mb-1">Avg Reading Speed</p>
                <p class="text-2xl font-bold text-slate-100">{{ $stats['avg_wpm'] }} <span class="text-sm font-normal text-slate-500">WPM</span></p>
            </div>
        </div>
    </div>

    <!-- Articles List -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="ds-section-title text-xl">Reading Articles</h2>
        <a href="{{ route('reading.create') }}" class="ds-btn ds-btn-sm ds-btn-primary">
            + Add Article
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @forelse($articles as $article)
            <div class="ds-card flex flex-col h-full">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="text-lg font-bold text-slate-100 mb-2 line-clamp-1">{{ $article->title }}</h3>
                        <div class="flex items-center gap-2">
                            <x-ui.badge>{{ $article->level }}</x-ui.badge>
                            @if($article->target_band)
                                <x-ui.badge>Target: Band {{ $article->target_band }}</x-ui.badge>
                            @endif
                        </div>
                    </div>
                </div>
                
                <p class="ds-body mb-6 line-clamp-3">{{ Str::limit(strip_tags($article->full_text), 150) }}</p>
                
                <div class="mt-auto border-t border-slate-800 pt-4 flex items-center justify-between">
                    <div class="text-sm">
                        @if($article->attempts_count > 0)
                            @php 
                                $best = $article->attempts->max(function($a) { return $a->total_questions > 0 ? ($a->score / $a->total_questions) * 100 : 0; });
                            @endphp
                            <p class="text-slate-300"><span class="text-slate-500">Attempts:</span> {{ $article->attempts_count }}</p>
                            <p class="text-emerald-400 font-medium"><span class="text-slate-500">Best Score:</span> {{ round($best) }}%</p>
                        @else
                            <p class="ds-muted">Not attempted yet</p>
                        @endif
                    </div>
                    
                    <a href="{{ route('reading.practice', $article->id) }}" class="ds-btn ds-btn-sm ds-btn-secondary">
                        {{ $article->attempts_count > 0 ? 'Practice Again' : 'Start Practice' }}
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-1 md:col-span-2">
                <x-ui.empty-state
                    icon="📚"
                    title="No reading articles added yet."
                    body="Add your first IELTS reading passage to start practicing."
                >
                    <a href="{{ route('reading.create') }}" class="ds-btn ds-btn-primary mt-4 inline-flex">
                        Add Your First Article
                    </a>
                </x-ui.empty-state>
            </div>
        @endforelse
    </div>
</div>
