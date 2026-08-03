<div class="max-w-4xl mx-auto space-y-8">
    <x-slot:header>
        Learning Timeline
    </x-slot>

    <div class="bg-slate-900 border border-slate-800 rounded-lg p-8 shadow-sm">
        <h2 class="text-2xl font-bold text-slate-100 mb-8">Your Journey</h2>

        @if($timeline->isEmpty())
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-slate-500 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h3 class="text-lg font-medium text-slate-300">No activity yet</h3>
                <p class="text-slate-500 mt-1">Start learning to build your timeline!</p>
            </div>
        @else
            <div class="relative border-l-2 border-slate-700/50 ml-4 md:ml-6 pb-4">
                @foreach($timeline as $date => $activities)
                    <!-- Date Header -->
                    <div class="relative mt-8 mb-4">
                        <div class="absolute -left-[33px] bg-slate-900 p-1">
                            <div class="w-4 h-4 rounded-full bg-slate-700 border-2 border-slate-900"></div>
                        </div>
                        <h3 class="text-lg font-bold text-slate-300 pl-6">{{ $date }}</h3>
                    </div>

                    <div class="space-y-6 pl-6">
                        @foreach($activities as $activity)
                            @php
                                $colors = [
                                    'vocabulary' => 'text-purple-400 bg-purple-400/10 border-purple-500/20',
                                    'grammar' => 'text-blue-400 bg-blue-400/10 border-blue-500/20',
                                    'journal' => 'text-emerald-400 bg-emerald-400/10 border-emerald-500/20',
                                    'reading' => 'text-amber-400 bg-amber-400/10 border-amber-500/20',
                                ];
                                $color = $colors[$activity->type] ?? 'text-slate-400 bg-slate-800 border-slate-700';
                            @endphp

                            <a href="{{ $activity->url }}" class="block p-4 rounded-lg border border-slate-800 bg-slate-800/20 hover:bg-slate-800/50 transition-colors group">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="flex items-start gap-4">
                                        <div class="mt-1 px-2.5 py-1 rounded text-xs font-bold uppercase tracking-wider {{ $color }} shrink-0">
                                            {{ $activity->type }}
                                        </div>
                                        <div>
                                            <h4 class="text-slate-200 font-medium group-hover:text-white transition-colors">{{ $activity->title }}</h4>
                                            @if($activity->subtitle)
                                                <p class="text-sm text-slate-500 mt-1">{{ $activity->subtitle }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="text-xs text-slate-500 shrink-0 mt-1">
                                        {{ $activity->date->format('g:i A') }}
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
