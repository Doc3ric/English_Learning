<div class="max-w-4xl mx-auto space-y-8">
    <x-slot:header>
        Learning Timeline
    </x-slot>

    <div class="ds-card p-8">
        <h2 class="text-2xl font-bold text-slate-100 mb-8">Your Journey</h2>

        @if($timeline->isEmpty())
            <x-ui.empty-state
                icon="⏳"
                title="No activity yet"
                body="Start learning to build your timeline!"
            />
        @else
            <div class="relative border-l-2 border-slate-800 ml-4 md:ml-6 pb-4">
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
                                    'vocabulary' => '!text-purple-400 !bg-purple-400/10 !border-purple-500/20',
                                    'grammar' => '!text-blue-400 !bg-blue-400/10 !border-blue-500/20',
                                    'journal' => '!text-emerald-400 !bg-emerald-400/10 !border-emerald-500/20',
                                    'reading' => '!text-amber-400 !bg-amber-400/10 !border-amber-500/20',
                                ];
                                $color = $colors[$activity->type] ?? '';
                            @endphp

                            <a href="{{ $activity->url }}" class="ds-card-nested block hover:bg-slate-800/50 transition-colors group">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="flex items-start gap-4">
                                        <x-ui.badge class="{{ $color }} shrink-0 mt-1">
                                            {{ $activity->type }}
                                        </x-ui.badge>
                                        <div>
                                            <h4 class="text-slate-200 font-medium group-hover:text-white transition-colors">{{ $activity->title }}</h4>
                                            @if($activity->subtitle)
                                                <p class="ds-muted mt-1">{{ $activity->subtitle }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="ds-muted text-xs shrink-0 mt-1">
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
