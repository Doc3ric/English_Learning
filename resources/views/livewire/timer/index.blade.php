<div class="max-w-5xl mx-auto">
    <x-slot:header>
        Study Timer
    </x-slot>

    @if (session()->has('message'))
        <div class="mb-6 p-4 rounded bg-emerald-900/20 border border-emerald-500/30 text-emerald-400 font-medium text-center shadow-lg shadow-emerald-900/20">
            {{ session('message') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Timer Section -->
        <div class="lg:col-span-2 bg-slate-900 border border-slate-800 rounded-lg p-10 text-center shadow-lg relative overflow-hidden" x-data="pomodoroTimer(25)">
            
            <!-- Progress Background -->
            <div class="absolute bottom-0 left-0 right-0 bg-slate-800/50 -z-0 transition-all duration-1000 ease-linear" :style="`height: ${((totalSeconds - timeLeft) / totalSeconds) * 100}%`"></div>

            <div class="relative z-10">
                <div class="mb-10 flex justify-center gap-4">
                    <button @click="setDuration(25)" :class="durationMinutes == 25 ? 'bg-emerald-600 text-slate-950 shadow-lg shadow-emerald-900/50' : 'bg-slate-950 text-slate-400 border border-slate-700 hover:text-emerald-400'" class="px-5 py-2 rounded-full font-bold transition-all">25 min</button>
                    <button @click="setDuration(15)" :class="durationMinutes == 15 ? 'bg-blue-600 text-slate-950 shadow-lg shadow-blue-900/50' : 'bg-slate-950 text-slate-400 border border-slate-700 hover:text-blue-400'" class="px-5 py-2 rounded-full font-bold transition-all">15 min</button>
                    <button @click="setDuration(5)" :class="durationMinutes == 5 ? 'bg-purple-600 text-slate-950 shadow-lg shadow-purple-900/50' : 'bg-slate-950 text-slate-400 border border-slate-700 hover:text-purple-400'" class="px-5 py-2 rounded-full font-bold transition-all">5 min break</button>
                </div>

                <!-- Timer Display -->
                <div class="relative w-72 h-72 mx-auto mb-12 flex items-center justify-center rounded-full border-[10px] transition-colors duration-500 bg-slate-950/80 backdrop-blur-sm"
                     :class="isRunning ? 'border-emerald-500 shadow-[0_0_80px_rgba(16,185,129,0.3)]' : 'border-slate-800'">
                    <span class="text-8xl font-bold font-mono text-slate-100 tracking-tight" x-text="formattedTime()">25:00</span>
                </div>

                <!-- Controls -->
                <div class="flex justify-center flex-wrap gap-6 mb-10">
                    <button x-show="!isRunning" @click="startTimer" class="bg-emerald-600 hover:bg-emerald-500 text-slate-950 text-2xl font-bold py-3 px-14 rounded-full transition-colors shadow-lg shadow-emerald-900/50 w-full sm:w-auto">
                        Start Focus
                    </button>
                    
                    <template x-if="isRunning">
                        <button @click="pauseTimer" class="bg-amber-600 hover:bg-amber-500 text-slate-950 text-2xl font-bold py-3 px-14 rounded-full transition-colors shadow-lg shadow-amber-900/50 w-full sm:w-auto">
                            Pause
                        </button>
                    </template>
                    
                    <button x-show="timeLeft < totalSeconds && !isRunning" @click="resetTimer" class="bg-slate-800 hover:bg-slate-700 text-slate-300 text-lg font-bold py-3 px-10 rounded-full transition-colors border border-slate-700 w-full sm:w-auto">
                        Reset
                    </button>
                </div>

                <!-- Pre-start Settings -->
                <div x-show="!isRunning && timeLeft === totalSeconds" class="pt-6 border-t border-slate-800/50" x-transition>
                    <p class="text-slate-500 mb-4 text-xs font-bold uppercase tracking-widest">Session Settings</p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center items-center max-w-xl mx-auto">
                        <select wire:model="activityType" class="bg-slate-950 border border-slate-700 rounded-md py-3 px-4 text-slate-200 focus:outline-none focus:border-emerald-500 w-full sm:w-auto font-medium">
                            <option value="General">General Study</option>
                            <option value="Vocabulary">Vocabulary</option>
                            <option value="Grammar">Grammar</option>
                            <option value="Reading">Reading</option>
                        </select>
                        
                        <input type="text" wire:model="notes" placeholder="What are you working on? (Optional)" class="bg-slate-950 border border-slate-700 rounded-md py-3 px-4 text-slate-200 focus:outline-none focus:border-emerald-500 w-full sm:w-auto flex-1 font-medium">
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Sessions -->
        <div>
            <h3 class="text-xl font-bold text-slate-100 mb-6 border-b border-slate-800 pb-2">Recent Sessions</h3>
            
            <div class="space-y-4">
                @forelse($recentSessions as $session)
                    <div class="bg-slate-900 border border-slate-800 rounded-lg p-5 flex items-center justify-between hover:border-slate-700 transition-colors">
                        <div>
                            <div class="flex items-center gap-2 mb-2">
                                <span class="bg-slate-800 text-slate-300 text-xs font-bold px-2 py-1 rounded-sm border border-slate-700">{{ $session->activity_type }}</span>
                                <span class="text-slate-500 text-xs font-medium">{{ $session->created_at->diffForHumans() }}</span>
                            </div>
                            @if($session->notes)
                                <p class="text-slate-400 text-sm font-medium">{{ $session->notes }}</p>
                            @else
                                <p class="text-slate-600 text-sm italic">No notes</p>
                            @endif
                        </div>
                        <div class="text-emerald-400 font-mono font-bold text-xl bg-slate-950 px-3 py-1 rounded border border-slate-800 shadow-inner">
                            {{ floor($session->duration_seconds / 60) }}m
                        </div>
                    </div>
                @empty
                    <div class="text-center text-slate-500 py-12 border border-slate-800 border-dashed rounded-lg bg-slate-900/30">
                        <svg class="w-12 h-12 mx-auto text-slate-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <p class="font-medium">No study sessions logged yet.</p>
                        <p class="text-sm mt-1">Start the timer to begin!</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>

    <!-- Audio Element for Alarm -->
    <audio id="alarm-sound" src="https://assets.mixkit.co/sfx/preview/mixkit-software-interface-back-2575.mp3" preload="auto"></audio>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('pomodoroTimer', (durationMinutesProxy) => ({
                durationMinutes: durationMinutesProxy,
                totalSeconds: 25 * 60,
                timeLeft: 25 * 60,
                isRunning: false,
                interval: null,
                
                init() {
                    // Watch for Livewire changes to durationMinutes
                    this.$watch('durationMinutes', value => {
                        if (!this.isRunning) {
                            this.totalSeconds = value * 60;
                            this.timeLeft = this.totalSeconds;
                        }
                    });
                    
                    this.totalSeconds = this.durationMinutes * 60;
                    this.timeLeft = this.totalSeconds;
                },
                
                setDuration(mins) {
                    if (this.isRunning) return;
                    this.durationMinutes = mins;
                },
                
                startTimer() {
                    this.isRunning = true;
                    this.interval = setInterval(() => {
                        this.timeLeft--;
                        
                        if (this.timeLeft <= 0) {
                            this.completeTimer();
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
                    this.timeLeft = this.totalSeconds;
                },
                
                completeTimer() {
                    this.isRunning = false;
                    clearInterval(this.interval);
                    this.timeLeft = 0;
                    
                    // Play sound
                    const alarm = document.getElementById('alarm-sound');
                    if(alarm) {
                        alarm.volume = 0.7;
                        alarm.play();
                    }
                    
                    // Save to backend using Livewire
                    // For short break (5m), don't log a study session. Only log >= 15m.
                    if (this.totalSeconds >= 15 * 60) {
                        this.$wire.saveSession(this.totalSeconds);
                    }
                    
                    // Reset UI after a short delay
                    setTimeout(() => {
                        this.timeLeft = this.totalSeconds;
                    }, 2000);
                },
                
                formattedTime() {
                    let m = Math.floor(this.timeLeft / 60).toString().padStart(2, '0');
                    let s = (this.timeLeft % 60).toString().padStart(2, '0');
                    return `${m}:${s}`;
                }
            }))
        })
    </script>
</div>
