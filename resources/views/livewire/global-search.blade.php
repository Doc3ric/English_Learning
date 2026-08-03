<div class="relative w-full max-w-md" x-data="{ open: false }" @click.away="open = false">
    <div class="relative">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <svg class="h-5 w-5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </div>
        <input 
            wire:model.live.debounce.300ms="query" 
            @focus="open = true"
            @keydown.escape.window="open = false"
            type="text" 
            placeholder="Search across vocabulary, journal, grammar..." 
            class="block w-full pl-10 pr-3 py-2 border border-slate-700 rounded-md leading-5 bg-slate-900 text-slate-300 placeholder-slate-500 focus:outline-none focus:bg-slate-800 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 sm:text-sm transition-colors"
        >
        
        <div wire:loading wire:target="query" class="absolute inset-y-0 right-0 pr-3 flex items-center">
            <svg class="animate-spin h-4 w-4 text-emerald-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>
    </div>

    <!-- Dropdown Results -->
    <div x-show="open && $wire.query.length >= 2" 
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="transform opacity-0 scale-95"
         x-transition:enter-end="transform opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="transform opacity-100 scale-100"
         x-transition:leave-end="transform opacity-0 scale-95"
         class="absolute z-50 mt-2 w-full rounded-md bg-slate-800 border border-slate-700 shadow-xl overflow-hidden" 
         style="display: none;">
        
        @if(count($results) > 0)
            <div class="max-h-96 overflow-y-auto">
                @foreach(collect($results)->groupBy('type') as $type => $typeResults)
                    <div class="px-4 py-2 bg-slate-900 border-y border-slate-700/50 text-xs font-semibold text-slate-400 uppercase tracking-wider">
                        {{ $type }}
                    </div>
                    <ul>
                        @foreach($typeResults as $result)
                            <li>
                                <a href="{{ $result['url'] }}" class="block px-4 py-3 hover:bg-slate-700/50 transition-colors">
                                    <p class="text-sm font-medium text-emerald-400">{{ $result['title'] }}</p>
                                    <p class="text-xs text-slate-400 mt-1 truncate">{{ $result['subtitle'] }}</p>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endforeach
            </div>
        @elseif(strlen($query) >= 2)
            <div class="px-4 py-8 text-center text-slate-400 text-sm">
                No results found for "{{ $query }}".
            </div>
        @endif
    </div>
</div>
