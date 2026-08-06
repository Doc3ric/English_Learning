<div class="max-w-5xl mx-auto space-y-8">
    <x-slot:header>
        Achievements
    </x-slot>

    <!-- Header Stats -->
    <div class="ds-card-accent-amber p-8 flex flex-col md:flex-row items-center justify-between">
        <div>
            <h2 class="text-3xl font-bold text-slate-100 mb-2">Your Trophy Room</h2>
            <p class="ds-muted">Keep pushing your limits to unlock more badges!</p>
        </div>
        <div class="mt-6 md:mt-0 text-center">
            <div class="text-5xl font-black text-amber-400 mb-1">{{ $totalUnlocked }}<span class="text-2xl text-slate-500">/{{ $totalAchievements }}</span></div>
            <p class="ds-eyebrow !text-amber-500">Unlocked</p>
        </div>
    </div>

    <!-- Achievement Grid -->
    @foreach(['vocabulary', 'grammar', 'reading', 'journal', 'mistakes', 'time', 'streak'] as $cat)
        @if(isset($achievements[$cat]))
            <div class="mb-10">
                <h3 class="ds-section-title capitalize mb-6 border-b border-slate-800 pb-2">{{ $cat }}</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    @foreach($achievements[$cat] as $ach)
                        @php
                            $isUnlocked = in_array($ach['key'], $unlockedKeys);
                        @endphp
                        <div class="ds-card relative flex flex-col items-center text-center transition-all duration-300 {{ $isUnlocked ? '!border-amber-500/50 shadow-[0_0_15px_rgba(245,158,11,0.15)]' : 'opacity-60 grayscale' }}">
                            
                            @if($isUnlocked)
                                <div class="absolute -top-3 -right-3 bg-amber-500 text-white p-1 rounded-full shadow-lg">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                </div>
                            @endif

                            <div class="w-16 h-16 rounded-full flex items-center justify-center mb-4 {{ $isUnlocked ? 'bg-amber-500/20 text-amber-400' : 'bg-slate-800 text-slate-500' }}">
                                <!-- Simple fallback icons based on category -->
                                @if($ach['icon'] === 'sparkles')
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                                @elseif($ach['icon'] === 'academic-cap')
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"></path></svg>
                                @elseif($ach['icon'] === 'book-open')
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                @elseif($ach['icon'] === 'pencil-alt')
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                @elseif($ach['icon'] === 'exclamation-circle')
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                @elseif($ach['icon'] === 'clock')
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                @elseif($ach['icon'] === 'fire')
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.879 16.121A3 3 0 1012.015 11L11 14H9c0 .768.293 1.536.879 2.121z"></path></svg>
                                @endif
                            </div>
                            
                            <h4 class="font-bold {{ $isUnlocked ? 'text-amber-400' : 'text-slate-300' }} mb-1">{{ $ach['title'] }}</h4>
                            <p class="ds-muted text-xs">{{ $ach['desc'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    @endforeach
</div>
