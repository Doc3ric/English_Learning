<div>
    <x-slot:header>
        Reading Tracker (IELTS Practice)
    </x-slot>

    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-slate-900 border border-slate-800 rounded-lg p-6 flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-emerald-900/30 text-emerald-400 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
            </div>
            <div>
                <p class="text-slate-400 text-sm font-medium">Total Practices</p>
                <p class="text-2xl font-bold text-slate-100">{{ $stats['total_attempts'] }}</p>
            </div>
        </div>
        
        <div class="bg-slate-900 border border-slate-800 rounded-lg p-6 flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-blue-900/30 text-blue-400 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <p class="text-slate-400 text-sm font-medium">Avg Score</p>
                <p class="text-2xl font-bold text-slate-100">{{ $stats['avg_score'] }}%</p>
            </div>
        </div>
        
        <div class="bg-slate-900 border border-slate-800 rounded-lg p-6 flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-purple-900/30 text-purple-400 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <p class="text-slate-400 text-sm font-medium">Avg Reading Speed</p>
                <p class="text-2xl font-bold text-slate-100">{{ $stats['avg_wpm'] }} <span class="text-sm font-normal text-slate-500">WPM</span></p>
            </div>
        </div>
    </div>

    <!-- Articles List -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold text-slate-100">Reading Articles</h2>
        <a href="{{ route('reading.create') }}" class="bg-emerald-600 hover:bg-emerald-500 text-slate-950 font-bold py-2 px-4 rounded-md transition-colors text-sm shadow">
            + Add Article
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @forelse($articles as $article)
            <div class="bg-slate-900 border border-slate-800 rounded-lg p-6 flex flex-col h-full">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="text-lg font-bold text-slate-100 mb-1 line-clamp-1">{{ $article->title }}</h3>
                        <div class="flex items-center gap-2">
                            <span class="bg-slate-800 text-slate-300 text-xs font-semibold px-2 py-0.5 rounded border border-slate-700">{{ $article->level }}</span>
                            @if($article->target_band)
                                <span class="bg-indigo-900/30 text-indigo-400 text-xs font-semibold px-2 py-0.5 rounded border border-indigo-500/30">Target: Band {{ $article->target_band }}</span>
                            @endif
                        </div>
                    </div>
                </div>
                
                <p class="text-slate-400 text-sm mb-6 line-clamp-3">{{ Str::limit(strip_tags($article->full_text), 150) }}</p>
                
                <div class="mt-auto border-t border-slate-800 pt-4 flex items-center justify-between">
                    <div class="text-sm">
                        @if($article->attempts_count > 0)
                            @php 
                                $best = $article->attempts->max(function($a) { return $a->total_questions > 0 ? ($a->score / $a->total_questions) * 100 : 0; });
                            @endphp
                            <p class="text-slate-300"><span class="text-slate-500">Attempts:</span> {{ $article->attempts_count }}</p>
                            <p class="text-emerald-400 font-medium"><span class="text-slate-500">Best Score:</span> {{ round($best) }}%</p>
                        @else
                            <p class="text-slate-500">Not attempted yet</p>
                        @endif
                    </div>
                    
                    <a href="{{ route('reading.practice', $article->id) }}" class="bg-slate-800 hover:bg-slate-700 text-emerald-400 text-sm font-bold py-2 px-4 rounded transition-colors border border-slate-700">
                        {{ $article->attempts_count > 0 ? 'Practice Again' : 'Start Practice' }}
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-1 md:col-span-2 text-center text-slate-500 py-12 border border-slate-800 border-dashed rounded bg-slate-900/50">
                <p class="mb-4">No reading articles added yet.</p>
                <a href="{{ route('reading.create') }}" class="inline-block bg-slate-800 hover:bg-slate-700 text-slate-300 font-medium py-2 px-6 rounded-md transition-colors border border-slate-700">
                    Add Your First Article
                </a>
            </div>
        @endforelse
    </div>
</div>
