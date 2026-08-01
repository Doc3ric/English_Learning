<div>
    <x-slot:header>
        Home Dashboard
    </x-slot>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Today's Mission -->
        <div class="md:col-span-2 bg-slate-900 border border-slate-800 rounded-lg p-6 shadow-sm">
            <h3 class="text-lg font-semibold text-slate-100 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Today's Mission
            </h3>
            
            <div class="space-y-3">
                <label class="flex items-center gap-3 p-3 rounded bg-slate-800/50 border border-slate-800 cursor-pointer hover:bg-slate-800 transition-colors">
                    <input type="checkbox" class="w-5 h-5 rounded border-slate-600 text-emerald-500 focus:ring-emerald-500 bg-slate-950">
                    <span class="text-slate-300">Learn 10 new words</span>
                </label>
                <label class="flex items-center gap-3 p-3 rounded bg-slate-800/50 border border-slate-800 cursor-pointer hover:bg-slate-800 transition-colors">
                    <input type="checkbox" class="w-5 h-5 rounded border-slate-600 text-emerald-500 focus:ring-emerald-500 bg-slate-950">
                    <span class="text-slate-300">Complete 1 grammar lesson</span>
                </label>
                <label class="flex items-center gap-3 p-3 rounded bg-slate-800/50 border border-slate-800 cursor-pointer hover:bg-slate-800 transition-colors">
                    <input type="checkbox" class="w-5 h-5 rounded border-slate-600 text-emerald-500 focus:ring-emerald-500 bg-slate-950">
                    <span class="text-slate-300">Review yesterday's vocabulary</span>
                </label>
            </div>
        </div>

        <!-- Level & Progress -->
        <div class="bg-slate-900 border border-slate-800 rounded-lg p-6 shadow-sm">
            <h3 class="text-lg font-semibold text-slate-100 mb-4">Level</h3>
            <div class="flex items-end gap-2 mb-4">
                <span class="text-4xl font-bold text-emerald-500">A2</span>
                <span class="text-slate-500 text-sm mb-1">Target: B2</span>
            </div>
            
            <div class="space-y-2 mt-6">
                <div class="flex justify-between text-sm">
                    <span class="text-slate-400">Daily Progress</span>
                    <span class="text-slate-200 font-medium">30%</span>
                </div>
                <div class="w-full bg-slate-950 rounded-full h-2">
                    <div class="bg-emerald-500 h-2 rounded-full" style="width: 30%"></div>
                </div>
            </div>
        </div>
    </div>
</div>
