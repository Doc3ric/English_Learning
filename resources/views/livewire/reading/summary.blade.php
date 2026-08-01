<div class="bg-slate-900 border border-slate-800 rounded-lg p-6">
    @if($saved)
        <div class="p-4 rounded bg-emerald-900/20 border border-emerald-500/30 text-emerald-400 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            Summary saved successfully!
        </div>
    @endif

    <form wire:submit="saveSummary">
        <label class="block text-sm font-medium text-slate-300 mb-2">Write a short summary of the main points in your own words.</label>
        <textarea wire:model.live.debounce.500ms="summaryText" rows="6" class="w-full bg-slate-950 border border-slate-800 rounded-md py-3 px-4 text-slate-200 focus:outline-none focus:border-emerald-500 leading-relaxed" placeholder="The article discusses..."></textarea>
        @error('summaryText') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
        
        <div class="mt-4 flex justify-between items-center">
            <span class="text-sm font-medium {{ $wordCount > 50 ? 'text-emerald-400' : 'text-slate-500' }}">
                {{ $wordCount }} words
            </span>
            <button type="submit" class="bg-slate-800 hover:bg-slate-700 text-emerald-400 font-bold py-2 px-6 rounded-md transition-colors border border-slate-700">
                Save Summary
            </button>
        </div>
    </form>
</div>
