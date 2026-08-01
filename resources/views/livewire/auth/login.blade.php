<div class="flex items-center justify-center h-full">
    <div class="w-full max-w-md bg-slate-900 border border-slate-800 rounded-lg shadow-xl p-8">
        <div class="text-center mb-8">
            <div class="w-12 h-12 rounded bg-emerald-500 mx-auto flex items-center justify-center font-bold text-slate-950 text-xl mb-4">E</div>
            <h2 class="text-2xl font-bold text-slate-100">Welcome to EnglishOS</h2>
            <p class="text-slate-400 mt-2 text-sm">Log in to your personal learning dashboard</p>
        </div>

        <form wire:submit="login" class="space-y-6">
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-2">Email</label>
                <input wire:model="email" type="email" class="w-full bg-slate-950 border border-slate-700 rounded-md py-2 px-3 text-slate-200 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500" required>
                @error('email') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-300 mb-2">Password</label>
                <input wire:model="password" type="password" class="w-full bg-slate-950 border border-slate-700 rounded-md py-2 px-3 text-slate-200 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500" required>
            </div>

            <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-500 text-slate-950 font-bold py-2.5 px-4 rounded-md transition-colors">
                <span wire:loading.remove wire:target="login">Log In</span>
                <span wire:loading wire:target="login">Logging in...</span>
            </button>
        </form>
    </div>
</div>
