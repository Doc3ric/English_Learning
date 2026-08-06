<div class="ds-card-nested">
    @if($saved)
        <div class="p-4 rounded-lg bg-emerald-900/20 border border-emerald-500/30 text-emerald-400 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            Summary saved successfully!
        </div>
    @endif

    <form wire:submit="saveSummary">
        <label class="ds-label mb-2">Write a short summary of the main points in your own words.</label>
        <textarea wire:model.live.debounce.500ms="summaryText" rows="6" class="ds-textarea leading-relaxed" placeholder="The article discusses..."></textarea>
        @error('summaryText') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
        
        <div class="mt-4 flex justify-between items-center">
            <span class="text-sm font-medium {{ $wordCount > 50 ? 'text-emerald-400' : 'text-slate-500' }}">
                {{ $wordCount }} words
            </span>
            <button type="submit" class="ds-btn ds-btn-sm ds-btn-secondary">
                Save Summary
            </button>
        </div>
    </form>
</div>
