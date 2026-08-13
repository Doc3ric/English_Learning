<div class="max-w-5xl mx-auto space-y-8">
    <x-slot:header>
        Study Timer
    </x-slot>

    @if (session()->has('message'))
        <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 font-bold text-center shadow-lg shadow-emerald-950/40 animate-fade-in flex items-center justify-center gap-2">
            <span>✓</span> {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="p-4 rounded-2xl bg-amber-500/10 border border-amber-500/30 text-amber-400 font-semibold text-center shadow-lg">
            ⚠️ {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Timer Main Section -->
        <div class="lg:col-span-2 bg-slate-900 border border-slate-800 rounded-3xl p-6 sm:p-10 text-center shadow-xl relative overflow-hidden flex flex-col justify-between"
             x-data="advancedStudyTimer(25)">
            
            <!-- Progress Background fill (Countdown Mode) -->
            <div x-show="mode === 'countdown'"
                 class="absolute bottom-0 left-0 right-0 bg-slate-800/40 -z-0 transition-all duration-1000 ease-linear pointer-events-none"
                 :style="`height: ${totalSeconds > 0 ? ((totalSeconds - timeLeft) / totalSeconds) * 100 : 0}%`"></div>

            <div class="relative z-10 space-y-8">

                <!-- Mode Switcher (Countdown vs Stopwatch) -->
                <div class="flex items-center justify-between flex-wrap gap-4 pb-4 border-b border-slate-800">
                    <!-- Mode Buttons -->
                    <div class="inline-flex p-1 rounded-xl bg-slate-950 border border-slate-800">
                        <button type="button" @click="switchMode('countdown')"
                                :class="mode === 'countdown' ? 'bg-violet-600 text-white font-bold shadow-md shadow-violet-600/30' : 'text-slate-400 hover:text-slate-200'"
                                class="px-4 py-2 rounded-lg text-xs font-semibold transition-all">
                            ⏳ Countdown (Pomodoro)
                        </button>
                        <button type="button" @click="switchMode('stopwatch')"
                                :class="mode === 'stopwatch' ? 'bg-violet-600 text-white font-bold shadow-md shadow-violet-600/30' : 'text-slate-400 hover:text-slate-200'"
                                class="px-4 py-2 rounded-lg text-xs font-semibold transition-all">
                            ⏱️ Stopwatch (Count Up)
                        </button>
                    </div>

                    <!-- Pomodoro Cycle Counter -->
                    <div class="flex items-center gap-2 bg-slate-950/80 border border-slate-800 px-3.5 py-1.5 rounded-xl text-xs font-semibold text-slate-300">
                        <span>🍅 Pomodoros Today:</span>
                        <span class="text-amber-400 font-bold text-sm" x-text="completedPomodoros">0</span>
                    </div>
                </div>

                <!-- COUNTDOWN DURATION SELECTOR & CUSTOM TIME -->
                <div x-show="mode === 'countdown'" class="space-y-4">
                    <div class="flex flex-wrap justify-center items-center gap-3">
                        <button @click="setDuration(25)"
                                :class="durationMinutes === 25 ? 'bg-emerald-500 text-slate-950 font-bold shadow-lg shadow-emerald-500/20' : 'bg-slate-950 text-slate-400 border border-slate-800 hover:text-emerald-400 hover:border-slate-700'"
                                class="px-5 py-2 rounded-full font-bold text-sm transition-all">
                            25 min
                        </button>
                        <button @click="setDuration(15)"
                                :class="durationMinutes === 15 ? 'bg-sky-500 text-slate-950 font-bold shadow-lg shadow-sky-500/20' : 'bg-slate-950 text-slate-400 border border-slate-800 hover:text-sky-400 hover:border-slate-700'"
                                class="px-5 py-2 rounded-full font-bold text-sm transition-all">
                            15 min
                        </button>
                        <button @click="setDuration(5)"
                                :class="durationMinutes === 5 ? 'bg-purple-500 text-white font-bold shadow-lg shadow-purple-500/20' : 'bg-slate-950 text-slate-400 border border-slate-800 hover:text-purple-400 hover:border-slate-700'"
                                class="px-5 py-2 rounded-full font-bold text-sm transition-all">
                            5 min (break)
                        </button>

                        <!-- Custom duration input -->
                        <div class="inline-flex items-center gap-2 bg-slate-950 border border-slate-800 rounded-full px-3 py-1 text-xs">
                            <span class="text-slate-400 font-semibold">Custom:</span>
                            <input type="number" min="1" max="180" x-model.number="customInputMinutes"
                                   @keydown.enter.prevent="setCustomDuration()"
                                   class="w-14 bg-slate-900 border border-slate-700 rounded-md px-2 py-1 text-center font-bold text-slate-100 focus:outline-none focus:border-violet-500 text-xs">
                            <span class="text-slate-400 font-semibold">m</span>
                            <button type="button" @click="setCustomDuration()"
                                    class="bg-slate-800 hover:bg-slate-700 text-violet-400 px-2.5 py-1 rounded-md font-bold text-xs transition-colors">
                                Set
                            </button>
                        </div>
                    </div>
                </div>

                <!-- TIMER DISPLAY CIRCLE -->
                <div class="relative w-64 h-64 sm:w-72 sm:h-72 mx-auto flex flex-col items-center justify-center rounded-full border-[10px] transition-all duration-500 bg-slate-950/90 shadow-2xl"
                     :class="isRunning ? 'border-emerald-500 shadow-[0_0_80px_rgba(16,185,129,0.25)]' : 'border-slate-800'">
                    
                    <span class="text-6xl sm:text-7xl font-black font-mono tracking-tight text-white drop-shadow-md"
                          x-text="formattedDisplay()">25:00</span>

                    <span class="text-xs font-bold uppercase tracking-widest text-slate-500 mt-2"
                          x-text="getTimerSublabel()">Focus Session</span>
                </div>

                <!-- CONTROLS -->
                <div class="space-y-4">
                    <div class="flex justify-center flex-wrap gap-4">
                        <!-- START -->
                        <button x-show="!isRunning" @click="startTimer()"
                                class="bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-400 hover:to-teal-400 text-slate-950 text-xl font-black py-3.5 px-12 rounded-full transition-all shadow-xl shadow-emerald-500/20 hover:scale-105 cursor-pointer">
                            Start Focus
                        </button>
                        
                        <!-- PAUSE -->
                        <button x-show="isRunning" @click="pauseTimer()"
                                class="bg-amber-500 hover:bg-amber-400 text-slate-950 text-xl font-black py-3.5 px-12 rounded-full transition-all shadow-xl shadow-amber-500/20 cursor-pointer">
                            Pause
                        </button>

                        <!-- RESET -->
                        <button x-show="!isRunning && ((mode === 'countdown' && timeLeft < totalSeconds) || (mode === 'stopwatch' && elapsedSeconds > 0))"
                                @click="resetTimer()"
                                class="bg-slate-800 hover:bg-slate-700 text-slate-300 text-sm font-bold py-3.5 px-8 rounded-full transition-colors border border-slate-700 cursor-pointer">
                            Reset
                        </button>
                    </div>

                    <!-- PARTIAL PROGRESS / FINISH EARLY & SAVE -->
                    <div x-show="getWorkedSeconds() >= 15"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 -translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="pt-2">
                        <button type="button" @click="finishEarlyAndSave()"
                                class="inline-flex items-center gap-2 px-6 py-2.5 rounded-full bg-violet-600/20 hover:bg-violet-600/30 border border-violet-500/40 text-violet-300 text-xs font-bold transition-all shadow-lg cursor-pointer">
                            <span>💾</span>
                            <span>Finish & Log Completed Time (<span x-text="getFormattedWorkedTime()"></span>)</span>
                        </button>
                    </div>
                </div>

                <!-- SESSION SETTINGS & TARGET ACTIVITY LINKS -->
                <div class="pt-6 border-t border-slate-800/80 space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-left">
                        
                        <!-- Activity selector -->
                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Study Category</label>
                            <select wire:model.live="activityType"
                                    class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-200 font-semibold focus:outline-none focus:border-violet-500 transition-colors">
                                @foreach($activities as $act)
                                    <option value="{{ $act['id'] }}">{{ $act['icon'] }} {{ $act['label'] }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Notes input -->
                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Focus Goal (Optional)</label>
                            <input type="text" wire:model="notes" placeholder="e.g. Chapter 3 grammar or 10 flashcards"
                                   class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-200 placeholder-slate-600 font-medium focus:outline-none focus:border-violet-500 transition-colors">
                        </div>

                    </div>

                    <!-- Shortcut link to target learning module -->
                    @php
                        $selectedAct = collect($activities)->firstWhere('id', $activityType);
                    @endphp
                    @if($selectedAct && $selectedAct['route'])
                        <div class="flex items-center justify-between p-3 rounded-xl bg-violet-500/10 border border-violet-500/20 text-xs">
                            <span class="text-violet-300 font-semibold">Want to practice {{ $selectedAct['label'] }} right now?</span>
                            <a href="{{ route($selectedAct['route']) }}" target="_blank"
                               class="inline-flex items-center gap-1 font-bold text-violet-400 hover:text-violet-300 hover:underline">
                                Launch Module ↗
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Desktop Notifications Permission Toggle -->
                <div class="flex items-center justify-center gap-3 pt-2 text-xs text-slate-500">
                    <button type="button" @click="requestNotificationPermission()"
                            class="hover:text-slate-300 flex items-center gap-1.5 transition-colors cursor-pointer">
                        <span x-text="hasNotificationPermission ? '🔔 Desktop Notifications Enabled' : '🔕 Enable Desktop Notifications'"></span>
                    </button>
                </div>

            </div>
        </div>

        <!-- Recent Sessions History -->
        <div class="space-y-4">
            <h3 class="text-lg font-bold text-white tracking-tight border-b border-slate-800 pb-3 flex items-center justify-between">
                <span>📋 Recent Timer Sessions</span>
                <span class="text-xs text-slate-500 font-normal">Last 10</span>
            </h3>
            
            <div class="space-y-3">
                @forelse($recentSessions as $session)
                    <div class="bg-slate-900 border border-slate-800/80 rounded-2xl p-4 flex items-center justify-between hover:border-slate-700/80 transition-all">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-800 border border-slate-700 text-violet-400">
                                    {{ $session->activity_type }}
                                </span>
                                <span class="text-[11px] text-slate-500 font-medium">
                                    {{ $session->created_at->diffForHumans() }}
                                </span>
                            </div>
                            @if($session->notes)
                                <p class="text-xs text-slate-300 font-medium">{{ $session->notes }}</p>
                            @else
                                <p class="text-xs text-slate-600 italic">No notes</p>
                            @endif
                        </div>
                        <div class="text-emerald-400 font-mono font-bold text-base bg-slate-950 px-3 py-1.5 rounded-xl border border-slate-800 shadow-inner">
                            {{ max(1, round($session->duration_seconds / 60)) }}m
                        </div>
                    </div>
                @empty
                    <div class="text-center text-slate-500 py-12 border border-slate-800 border-dashed rounded-2xl bg-slate-900/30">
                        <span class="text-3xl mb-3 block">⏱️</span>
                        <p class="font-bold text-slate-400 text-sm">No timer sessions logged yet.</p>
                        <p class="text-xs text-slate-500 mt-1">Start a session above to track your study time!</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>

    <!-- Alpine.js Timer Component Script -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('advancedStudyTimer', (initialMinutes) => ({
                mode: 'countdown', // 'countdown' | 'stopwatch'
                durationMinutes: initialMinutes,
                customInputMinutes: 30,
                totalSeconds: initialMinutes * 60,
                timeLeft: initialMinutes * 60,
                elapsedSeconds: 0,
                isRunning: false,
                interval: null,
                completedPomodoros: 0,
                hasNotificationPermission: false,

                init() {
                    if ("Notification" in window && Notification.permission === "granted") {
                        this.hasNotificationPermission = true;
                    }
                    this.totalSeconds = this.durationMinutes * 60;
                    this.timeLeft = this.totalSeconds;
                },

                switchMode(newMode) {
                    if (this.isRunning) {
                        if (!confirm("Switching modes will reset your current running timer. Proceed?")) return;
                    }
                    this.resetTimer();
                    this.mode = newMode;
                },

                setDuration(mins) {
                    if (this.isRunning) return;
                    this.durationMinutes = mins;
                    this.totalSeconds = mins * 60;
                    this.timeLeft = this.totalSeconds;
                },

                setCustomDuration() {
                    const mins = parseInt(this.customInputMinutes);
                    if (isNaN(mins) || mins < 1 || mins > 360) return;
                    this.setDuration(mins);
                },

                startTimer() {
                    this.isRunning = true;
                    this.interval = setInterval(() => {
                        if (this.mode === 'countdown') {
                            this.timeLeft--;
                            if (this.timeLeft <= 0) {
                                this.completeTimer();
                            }
                        } else {
                            this.elapsedSeconds++;
                        }
                    }, 1000);
                },

                pauseTimer() {
                    this.isRunning = false;
                    clearInterval(this.interval);
                },

                resetTimer() {
                    this.isRunning = false;
                    clearInterval(this.interval);
                    if (this.mode === 'countdown') {
                        this.timeLeft = this.totalSeconds;
                    } else {
                        this.elapsedSeconds = 0;
                    }
                },

                getWorkedSeconds() {
                    if (this.mode === 'countdown') {
                        return this.totalSeconds - this.timeLeft;
                    } else {
                        return this.elapsedSeconds;
                    }
                },

                getFormattedWorkedTime() {
                    const secs = this.getWorkedSeconds();
                    const m = Math.floor(secs / 60);
                    const s = secs % 60;
                    return m > 0 ? `${m}m ${s}s` : `${s}s`;
                },

                finishEarlyAndSave() {
                    const worked = this.getWorkedSeconds();
                    if (worked < 15) return;

                    this.pauseTimer();
                    const mins = Math.max(1, Math.round(worked / 60));
                    
                    if (confirm(`Save your completed ${mins} min session to study stats?`)) {
                        this.$wire.saveSession(worked);
                        this.resetTimer();
                    }
                },

                completeTimer() {
                    this.isRunning = false;
                    clearInterval(this.interval);
                    this.timeLeft = 0;

                    const worked = this.totalSeconds;
                    this.completedPomodoros++;

                    // 1. Play local synth chime (100% reliable offline sound)
                    this.playSynthChime();

                    // 2. Desktop notification if allowed
                    if (this.hasNotificationPermission) {
                        new Notification("🎉 Pomodoro Complete!", {
                            body: `Great job! You finished your ${this.durationMinutes} min study session.`,
                            icon: "/favicon.ico"
                        });
                    }

                    // 3. Save to backend
                    this.$wire.saveSession(worked);

                    // Reset UI
                    setTimeout(() => {
                        this.timeLeft = this.totalSeconds;
                    }, 2000);
                },

                playSynthChime() {
                    try {
                        const AudioCtx = window.AudioContext || window.webkitAudioContext;
                        if (!AudioCtx) return;
                        const ctx = new AudioCtx();
                        
                        const notes = [659.25, 783.99, 1046.50]; // E5, G5, C6
                        notes.forEach((freq, idx) => {
                            const osc = ctx.createOscillator();
                            const gain = ctx.createGain();
                            osc.type = 'sine';
                            osc.frequency.value = freq;
                            gain.gain.setValueAtTime(0.2, ctx.currentTime + idx * 0.15);
                            gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + idx * 0.15 + 0.6);
                            osc.connect(gain);
                            gain.connect(ctx.destination);
                            osc.start(ctx.currentTime + idx * 0.15);
                            osc.stop(ctx.currentTime + idx * 0.15 + 0.6);
                        });
                    } catch (e) {
                        console.log("AudioContext chime error:", e);
                    }
                },

                requestNotificationPermission() {
                    if (!("Notification" in window)) {
                        alert("Desktop notifications are not supported by your browser.");
                        return;
                    }
                    Notification.requestPermission().then((perm) => {
                        this.hasNotificationPermission = (perm === "granted");
                        if (perm === "granted") {
                            alert("Desktop notifications enabled! You will be notified when your timer finishes.");
                        }
                    });
                },

                formattedDisplay() {
                    const secs = this.mode === 'countdown' ? this.timeLeft : this.elapsedSeconds;
                    const m = Math.floor(secs / 60).toString().padStart(2, '0');
                    const s = (secs % 60).toString().padStart(2, '0');
                    return `${m}:${s}`;
                },

                getTimerSublabel() {
                    if (this.mode === 'stopwatch') {
                        return this.isRunning ? 'Stopwatch Running...' : 'Stopwatch Paused';
                    }
                    if (this.durationMinutes === 5) return 'Short Break';
                    return 'Focus Session';
                }
            }));
        });
    </script>
</div>
