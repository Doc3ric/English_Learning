<div class="flex flex-col h-full min-h-0">

    @if($state === 'scenarios')
        {{-- ================ SCENARIO & LEVEL PICKER ================ --}}
        <div class="flex-1 min-h-0 overflow-y-auto p-8">
            <div class="max-w-4xl mx-auto">
                <div class="text-center mb-8">
                    <h2 class="text-3xl font-black text-white tracking-tight mb-2">AI English Conversation Partner</h2>
                    <p class="text-slate-400 text-sm">Practice real-time speaking or typing with natural AI feedback and instant corrections.</p>
                </div>

                {{-- Level Selector --}}
                <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-4 mb-8 max-w-2xl mx-auto shadow-sm">
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-3 text-center">Select Your Difficulty Level</label>
                    <div class="grid grid-cols-3 gap-3">
                        @foreach(\App\Livewire\Conversation\Index::LEVELS as $lvl)
                            <button type="button" wire:click="$set('targetLevel', '{{ $lvl['id'] }}')"
                                    class="px-3 py-2.5 rounded-xl border text-xs font-semibold transition-all cursor-pointer text-center {{ $targetLevel === $lvl['id'] ? 'bg-violet-600 border-violet-500 text-white shadow-lg shadow-violet-500/20' : 'bg-slate-800/80 border-slate-700/80 text-slate-400 hover:border-slate-600 hover:text-slate-300' }}">
                                <div>{{ $lvl['title'] }}</div>
                                <div class="text-[10px] opacity-75 font-normal mt-0.5">{{ $lvl['desc'] }}</div>
                            </button>
                        @endforeach
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    @foreach(\App\Livewire\Conversation\Index::SCENARIOS as $s)
                        <button wire:click="startConversation('{{ $s['id'] }}')"
                                @click="if (window.speechSynthesis) { window.speechSynthesis.cancel(); const u = new SpeechSynthesisUtterance(''); window.speechSynthesis.speak(u); }"
                                wire:loading.attr="disabled"
                                class="ds-card p-6 text-left hover:border-violet-500/40 hover:shadow-[0_0_25px_rgba(139,92,246,0.12)] transition-all group cursor-pointer">
                            <div class="text-4xl mb-3">{{ $s['icon'] }}</div>
                            <h3 class="text-lg font-bold text-white group-hover:text-violet-400 transition-colors mb-1">{{ $s['title'] }}</h3>
                            <p class="text-xs text-slate-400">{{ $s['desc'] }}</p>
                        </button>
                    @endforeach
                </div>

                <div wire:loading class="text-center mt-8">
                    <div class="inline-flex items-center gap-3 px-5 py-3 rounded-full bg-slate-800/80 border border-slate-700 text-slate-300 text-sm">
                        <svg class="animate-spin w-4 h-4 text-violet-400" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        Starting {{ $targetLevel }} conversation...
                    </div>
                </div>
            </div>
        </div>

    @elseif($state === 'recap')
        {{-- ================ POST-SESSION ANALYSIS (Phase 19) ================ --}}
        @php
            $analysis     = $sessionAnalysis;
            $accuracy     = $analysis['accuracy']          ?? 100;
            $totalTurns   = $analysis['total_user_turns']  ?? 0;
            $errorTurns   = $analysis['turns_with_errors'] ?? 0;
            $cleanTurns   = $totalTurns - $errorTurns;
            $byRule       = $analysis['corrections_by_rule'] ?? [];
            $topRule      = $analysis['top_rule']           ?? null;

            $accuracyColor = $accuracy >= 80 ? 'text-emerald-400' : ($accuracy >= 60 ? 'text-sky-400' : ($accuracy >= 40 ? 'text-amber-400' : 'text-red-400'));
            $accuracyBg    = $accuracy >= 80 ? 'from-emerald-500/20 to-teal-500/10 border-emerald-500/30' : ($accuracy >= 60 ? 'from-sky-500/20 to-blue-500/10 border-sky-500/30' : ($accuracy >= 40 ? 'from-amber-500/20 to-yellow-500/10 border-amber-500/30' : 'from-red-500/20 to-rose-500/10 border-red-500/30'));
            $accuracyLabel = $accuracy >= 80 ? 'Excellent accuracy' : ($accuracy >= 60 ? 'Good — keep practicing' : ($accuracy >= 40 ? 'Developing — focus on top errors' : 'Needs work — review your errors below'));
        @endphp

        <div class="flex-1 min-h-0 overflow-y-auto p-6 sm:p-8">
            <div class="max-w-2xl mx-auto space-y-5">

                {{-- ── HEADER ──────────────────────────────────────────────── --}}
                <div class="text-center pb-2">
                    <h2 class="text-2xl font-black text-white tracking-tight mb-1">Session Analysis</h2>
                    <p class="text-slate-400 text-sm">{{ $scenario }} · {{ $targetLevel }}</p>
                </div>

                {{-- ── ACCURACY SCORE CARD ─────────────────────────────────── --}}
                <div class="bg-gradient-to-br {{ $accuracyBg }} border rounded-2xl p-6 text-center">
                    <div class="{{ $accuracyColor }} text-6xl font-black tracking-tight mb-1">{{ $accuracy }}%</div>
                    <p class="text-sm font-semibold text-slate-300 mb-3">{{ $accuracyLabel }}</p>
                    <div class="flex items-center justify-center gap-6 text-xs text-slate-400">
                        <span>🎯 <strong class="text-white">{{ $totalTurns }}</strong> turns reviewed</span>
                        <span>✅ <strong class="text-emerald-400">{{ $cleanTurns }}</strong> clean</span>
                        <span>⚠️ <strong class="text-amber-400">{{ $errorTurns }}</strong> with errors</span>
                    </div>
                </div>

                @if($accuracy === 100 && $totalTurns > 0)
                    {{-- Perfect session --}}
                    <div class="bg-emerald-500/10 border border-emerald-500/30 rounded-2xl p-6 text-center">
                        <div class="text-4xl mb-2">🏆</div>
                        <h3 class="text-base font-bold text-emerald-400 mb-1">Perfect Session!</h3>
                        <p class="text-xs text-slate-400">The AI found no grammar errors in any of your turns. Excellent work.</p>
                    </div>

                @elseif(!empty($byRule))

                    {{-- ── TOP ERROR THIS SESSION ───────────────────────────── --}}
                    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5">
                        <div class="flex items-center gap-2 mb-4">
                            <span class="text-xs font-bold text-amber-400 uppercase tracking-wider px-2.5 py-1 rounded-full bg-amber-500/10 border border-amber-500/20">🔥 Most Frequent Error This Session</span>
                        </div>

                        @php $top = $byRule[0]; @endphp
                        <div class="flex items-start justify-between gap-4 mb-4">
                            <div>
                                <h3 class="text-lg font-black text-white">{{ $top['rule'] }}</h3>
                                <p class="text-xs text-slate-400 mt-0.5">{{ $top['count'] }} {{ $top['count'] === 1 ? 'error' : 'errors' }} this session</p>
                            </div>
                            <span class="text-3xl font-black text-amber-400/30">{{ $top['count'] }}×</span>
                        </div>

                        {{-- Examples for top rule --}}
                        @foreach($top['examples'] as $ex)
                            <div class="bg-slate-800/60 border border-slate-700/60 rounded-xl p-3.5 mb-2">
                                <div class="flex items-start gap-2 text-sm">
                                    <span class="text-red-400 line-through font-medium flex-1">{{ $ex['wrong'] }}</span>
                                    <span class="text-slate-600 mx-1 flex-shrink-0">→</span>
                                    <span class="text-emerald-400 font-bold flex-1">{{ $ex['correct'] }}</span>
                                </div>
                                @if(!empty($ex['reason']))
                                    <p class="text-[11px] text-slate-500 mt-2 leading-relaxed">{{ $ex['reason'] }}</p>
                                @endif
                            </div>
                        @endforeach

                        <a href="{{ route('mistakes.practice', ['category' => $top['rule']]) }}"
                           class="mt-3 w-full flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl bg-amber-500/10 hover:bg-amber-500/20 border border-amber-500/30 text-amber-400 text-sm font-bold transition-colors">
                            ⚡ Fix {{ $top['rule'] }} Now →
                        </a>
                    </div>

                    {{-- ── ALL OTHER ERRORS BY RULE ─────────────────────────── --}}
                    @if(count($byRule) > 1)
                        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5">
                            <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">All Corrections This Session</h4>
                            <div class="space-y-4">
                                @foreach($byRule as $ruleData)
                                    <div class="{{ !$loop->first ? 'pt-4 border-t border-slate-800' : '' }}">
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="text-xs font-bold text-slate-300">{{ $ruleData['rule'] }}</span>
                                            <span class="text-[10px] font-bold text-slate-500 bg-slate-800 border border-slate-700 px-2 py-0.5 rounded-full">{{ $ruleData['count'] }}×</span>
                                        </div>
                                        @foreach($ruleData['examples'] as $ex)
                                            <div class="flex items-center gap-2 text-xs bg-slate-800/40 rounded-lg px-3 py-2 mb-1.5">
                                                <span class="text-red-400 line-through">{{ $ex['wrong'] }}</span>
                                                <span class="text-slate-600">→</span>
                                                <span class="text-emerald-400 font-semibold">{{ $ex['correct'] }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                @else
                    <div class="text-center py-4 text-slate-500 text-sm">
                        No correction data available for this session.
                    </div>
                @endif

                {{-- ── ACTION BUTTONS ───────────────────────────────────────── --}}
                <div class="flex items-center gap-3 pt-1 pb-4">
                    @if($topRule)
                        <a href="{{ route('mistakes.practice', ['category' => $topRule]) }}"
                           class="flex-1 text-center ds-btn ds-btn-primary py-3">
                            ⚡ Fix {{ $topRule }}
                        </a>
                    @else
                        <a href="{{ route('mistakes') }}"
                           class="flex-1 text-center ds-btn ds-btn-primary py-3">
                            📋 View All Mistakes
                        </a>
                    @endif
                    <button wire:click="newConversation" class="flex-1 ds-btn ds-btn-secondary py-3">
                        💬 New Scenario
                    </button>
                </div>

            </div>
        </div>


    @elseif($state === 'chat')
        {{-- ================ CHAT INTERFACE ================ --}}
        <div class="flex flex-col h-full min-h-0 overflow-hidden"
             x-data="conversationRecorder()"
             @speak-reply.window="queueSpeak($event.detail.text)">

            {{-- Chat Header --}}
            <div class="flex-shrink-0 flex items-center justify-between px-6 py-4 border-b border-slate-800 bg-slate-900/80 backdrop-blur z-10">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-violet-500/10 border border-violet-500/30 flex items-center justify-center text-lg">🤖</div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-sm font-bold text-white">AI Partner</h3>
                            <span class="px-2 py-0.5 rounded-md bg-slate-800 border border-slate-700 text-[10px] text-violet-400 font-semibold">{{ $targetLevel }}</span>
                        </div>
                        <p class="text-xs text-slate-500">{{ $scenario }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    {{-- Speech Speed Selector --}}
                    <button @click="cycleSpeechRate()"
                            class="px-2.5 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 transition-colors text-xs font-semibold border border-slate-700 cursor-pointer"
                            title="Adjust AI Voice Speed">
                        ⚡ <span x-text="speechRate + 'x'"></span>
                    </button>

                    {{-- Mute Toggle --}}
                    <button @click="toggleMute()"
                            class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white transition-colors text-xs font-medium border border-slate-700 cursor-pointer">
                        <span x-text="isMuted ? '🔇' : '🔊'"></span>
                        <span x-text="isMuted ? 'Muted' : 'Sound On'"></span>
                    </button>

                    {{-- Finish Session Button --}}
                    <button wire:click="finishSession"
                            class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-600/20 hover:bg-emerald-600/30 text-emerald-400 border border-emerald-500/30 transition-colors text-xs font-semibold cursor-pointer">
                        🏁 Finish
                    </button>

                    <button wire:click="newConversation"
                            class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 transition-colors text-xs font-medium border border-slate-700 cursor-pointer">
                        ← Exit
                    </button>
                </div>
            </div>

            {{-- Messages Area (scrollable) --}}
            <div class="flex-1 min-h-0 overflow-y-auto px-6 py-4 space-y-5" id="chat-messages">

                @foreach($messages as $msg)
                    @if($msg['role'] === 'assistant')
                        {{-- AI Message --}}
                        <div class="flex gap-3 items-start">
                            <div class="w-8 h-8 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center text-sm flex-shrink-0 mt-1">🤖</div>
                            <div class="max-w-[80%]">
                                <div class="bg-slate-800/80 border border-slate-700/60 rounded-2xl rounded-tl-md px-4 py-3 shadow-sm">
                                    <p class="text-sm text-slate-200 leading-relaxed">{{ $msg['text'] }}</p>
                                </div>
                                <div class="flex items-center gap-2 mt-1 px-1">
                                    <button @click="doSpeak(@js($msg['text']))" class="text-[11px] text-slate-400 hover:text-emerald-400 flex items-center gap-1 transition-colors cursor-pointer">
                                        <span>🔊</span> Listen
                                    </button>
                                </div>
                                @if(!empty($msg['corrections']))
                                    <div class="mt-2.5 space-y-2">
                                        @foreach($msg['corrections'] as $c)
                                            <div class="rounded-xl bg-amber-500/10 border border-amber-500/20 p-3 text-xs">
                                                <div class="flex items-center justify-between gap-2 mb-1">
                                                    <div class="flex items-center gap-1.5 font-bold text-amber-400">
                                                        <span>📌</span>
                                                        <span>{{ $c['rule'] ?? 'Grammar Correction' }}</span>
                                                    </div>
                                                    <span class="text-[10px] text-emerald-400 font-semibold px-2 py-0.5 rounded bg-emerald-500/10 border border-emerald-500/20">Saved to Mistakes</span>
                                                </div>
                                                <div class="font-medium">
                                                    <span class="text-red-400 line-through">{{ $c['wrong'] ?? '' }}</span>
                                                    <span class="text-slate-500 mx-1.5">→</span>
                                                    <span class="text-emerald-400 font-bold">{{ $c['correct'] ?? '' }}</span>
                                                </div>
                                                @if(!empty($c['reason']))
                                                    <p class="text-slate-300 text-[11px] mt-1">{{ $c['reason'] }}</p>
                                                @endif
                                                @if(!empty($c['example']))
                                                    <div class="mt-1.5 pt-1.5 border-t border-amber-500/20 text-[11px] text-slate-400 italic">
                                                        💡 Example: "{{ $c['example'] }}"
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    @else
                        {{-- User Message --}}
                        <div class="flex gap-3 items-start justify-end">
                            <div class="max-w-[80%]">
                                <div class="bg-emerald-600/20 border border-emerald-500/30 rounded-2xl rounded-tr-md px-4 py-3 shadow-sm">
                                    <p class="text-sm text-emerald-100 leading-relaxed">{{ $msg['text'] }}</p>
                                </div>
                            </div>
                            <div class="w-8 h-8 rounded-full bg-emerald-500/10 border border-emerald-500/30 flex items-center justify-center text-sm flex-shrink-0 mt-1">🎤</div>
                        </div>
                    @endif
                @endforeach

                @if($isLoading)
                    <div class="flex gap-3 items-start">
                        <div class="w-8 h-8 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center text-sm flex-shrink-0">🤖</div>
                        <div class="bg-slate-800/80 border border-slate-700/60 rounded-2xl rounded-tl-md px-5 py-3 shadow-sm">
                            <div class="flex gap-1.5 items-center">
                                <span class="w-2 h-2 rounded-full bg-slate-500 animate-bounce" style="animation-delay:0ms"></span>
                                <span class="w-2 h-2 rounded-full bg-slate-500 animate-bounce" style="animation-delay:150ms"></span>
                                <span class="w-2 h-2 rounded-full bg-slate-500 animate-bounce" style="animation-delay:300ms"></span>
                            </div>
                        </div>
                    </div>
                @endif

                @if($errorMessage)
                    <div class="p-3 rounded-lg bg-red-500/10 border border-red-500/20 text-red-400 text-sm text-center">
                        ⚠️ {{ $errorMessage }}
                    </div>
                @endif

                {{-- Scroll anchor --}}
                <div id="chat-bottom"></div>
            </div>

            {{-- ===== INPUT BAR (Voice Push-to-talk OR Text Mode) ===== --}}
            <div class="flex-shrink-0 bg-slate-900 border-t border-slate-800 px-6 py-4 z-10">

                {{-- Hidden Livewire file input --}}
                <input type="file" wire:model="audioFile" x-ref="audioInput" class="hidden" accept="audio/*" />

                {{-- Input Mode Switcher --}}
                <div class="flex items-center justify-between mb-3 max-w-md mx-auto">
                    <div class="flex items-center gap-1 bg-slate-800/80 p-1 rounded-xl border border-slate-700/60 mx-auto">
                        <button type="button" @click="inputMode = 'voice'"
                                class="px-3 py-1 rounded-lg text-xs font-semibold transition-colors cursor-pointer"
                                :class="inputMode === 'voice' ? 'bg-violet-600 text-white shadow-sm' : 'text-slate-400 hover:text-slate-200'">
                            🎙️ Voice Mode
                        </button>
                        <button type="button" @click="inputMode = 'text'"
                                class="px-3 py-1 rounded-lg text-xs font-semibold transition-colors cursor-pointer"
                                :class="inputMode === 'text' ? 'bg-violet-600 text-white shadow-sm' : 'text-slate-400 hover:text-slate-200'">
                            ⌨️ Text Mode
                        </button>
                    </div>
                </div>

                {{-- Voice Push-to-Talk Mode --}}
                <div x-show="inputMode === 'voice'" class="flex flex-col items-center gap-2">
                    <p class="text-xs font-medium transition-colors"
                       :class="isRecording ? 'text-red-400 font-bold' : (isProcessing ? 'text-amber-400 font-bold' : 'text-slate-400')"
                       x-text="isRecording ? '🔴 Recording... Release button to send'
                              : (isProcessing ? '⏳ Processing audio & generating reply...'
                              : 'Hold microphone button to speak')">
                    </p>

                    <button @mousedown.prevent="startRecording()"
                            @mouseup.prevent="stopRecording()"
                            @mouseleave="stopRecording()"
                            @touchstart.prevent="startRecording()"
                            @touchend.prevent="stopRecording()"
                            :disabled="isProcessing"
                            class="w-16 h-16 rounded-full flex items-center justify-center shadow-xl transition-all duration-150 select-none cursor-pointer"
                            :class="isRecording
                                ? 'bg-red-500 border-4 border-red-300 scale-110 shadow-red-500/50'
                                : (isProcessing
                                    ? 'bg-slate-700 border-2 border-slate-600 cursor-wait opacity-60'
                                    : 'bg-violet-600 border-2 border-violet-400 hover:bg-violet-500 hover:scale-105 hover:shadow-violet-500/40 active:scale-95')">

                        <template x-if="isProcessing">
                            <svg class="w-6 h-6 text-white animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                        </template>
                        <template x-if="!isProcessing">
                            <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 14c1.66 0 3-1.34 3-3V5c0-1.66-1.34-3-3-3S9 3.34 9 5v6c0 1.66 1.34 3 3 3zm5.3-3c0 3-2.54 5.1-5.3 5.1S6.7 14 6.7 11H5c0 3.41 2.72 6.23 6 6.72V21h2v-3.28c3.28-.48 6-3.3 6-6.72h-1.7z"/>
                            </svg>
                        </template>
                    </button>

                    <div class="flex gap-1 h-3 items-end" x-show="isRecording">
                        <span class="w-1 bg-red-400 rounded animate-bounce" style="height:60%;animation-delay:0ms"></span>
                        <span class="w-1 bg-red-400 rounded animate-bounce" style="height:100%;animation-delay:100ms"></span>
                        <span class="w-1 bg-red-400 rounded animate-bounce" style="height:40%;animation-delay:200ms"></span>
                        <span class="w-1 bg-red-400 rounded animate-bounce" style="height:80%;animation-delay:300ms"></span>
                        <span class="w-1 bg-red-400 rounded animate-bounce" style="height:60%;animation-delay:400ms"></span>
                    </div>
                </div>

                {{-- Keyboard Text Mode --}}
                <div x-show="inputMode === 'text'" class="max-w-2xl mx-auto">
                    <form wire:submit.prevent="sendTextMessage" class="flex gap-2">
                        <input type="text" wire:model="userTextInput" placeholder="Type your English response here..."
                               class="flex-1 bg-slate-800 border border-slate-700 rounded-xl px-4 py-3 text-sm text-slate-100 placeholder-slate-500 focus:outline-none focus:border-violet-500" />
                        <button type="submit" wire:loading.attr="disabled"
                                class="px-5 py-3 rounded-xl bg-violet-600 hover:bg-violet-500 text-white font-semibold text-sm transition-colors cursor-pointer flex items-center gap-1.5">
                            <span>Send</span> ➔
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>

{{-- Global Alpine Component Script --}}
<script>
    function conversationRecorder() {
        return {
            isRecording: false,
            isProcessing: false,
            isMuted: false,
            inputMode: 'voice',
            speechRate: 1.0,
            mediaRecorder: null,
            audioChunks: [],
            stream: null,
            pendingSpeech: null,

            async startRecording() {
                if (this.isRecording || this.isProcessing) return;
                if (window.speechSynthesis) window.speechSynthesis.cancel();

                try {
                    this.stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                    const mimeType = MediaRecorder.isTypeSupported('audio/webm;codecs=opus')
                        ? 'audio/webm;codecs=opus'
                        : '';
                    this.mediaRecorder = mimeType
                        ? new MediaRecorder(this.stream, { mimeType })
                        : new MediaRecorder(this.stream);
                    this.audioChunks = [];

                    this.mediaRecorder.ondataavailable = (e) => {
                        if (e.data && e.data.size > 0) this.audioChunks.push(e.data);
                    };
                    this.mediaRecorder.onstop = () => {
                        const type = this.mediaRecorder.mimeType || 'audio/webm';
                        const blob = new Blob(this.audioChunks, { type });
                        this.uploadAudio(blob, type);
                        if (this.stream) {
                            this.stream.getTracks().forEach(t => t.stop());
                        }
                    };
                    this.mediaRecorder.start(100);
                    this.isRecording = true;
                } catch (err) {
                    alert('Microphone access denied. Please allow mic access in your browser.');
                    console.error(err);
                }
            },

            stopRecording() {
                if (!this.isRecording || !this.mediaRecorder) return;
                this.isRecording = false;
                this.isProcessing = true;
                this.mediaRecorder.stop();
            },

            uploadAudio(blob, type) {
                const ext = type.includes('mp4') ? 'mp4' : (type.includes('ogg') ? 'ogg' : 'webm');
                const file = new File([blob], `recording.${ext}`, { type });
                const dt = new DataTransfer();
                dt.items.add(file);
                this.$refs.audioInput.files = dt.files;
                this.$refs.audioInput.dispatchEvent(new Event('change', { bubbles: true }));

                const done = () => {
                    this.isProcessing = false;
                    this.$nextTick(() => {
                        const el = document.getElementById('chat-bottom');
                        if (el) el.scrollIntoView({ behavior: 'smooth' });
                        if (this.pendingSpeech) {
                            this.doSpeak(this.pendingSpeech);
                            this.pendingSpeech = null;
                        }
                    });
                };

                if (window.Livewire) {
                    const unsub = Livewire.hook('morph.updated', () => {
                        done();
                        unsub();
                    });
                    setTimeout(() => { this.isProcessing = false; }, 15000);
                } else {
                    setTimeout(done, 5000);
                }
            },

            queueSpeak(text) {
                if (!this.isProcessing) {
                    this.doSpeak(text);
                } else {
                    this.pendingSpeech = text;
                }
            },

            doSpeak(text) {
                if (this.isMuted || !window.speechSynthesis) return;
                window.speechSynthesis.cancel();
                const utter = new SpeechSynthesisUtterance(text);
                utter.lang = 'en-US';
                utter.rate = this.speechRate;
                utter.pitch = 1.0;

                const voices = window.speechSynthesis.getVoices();
                const preferred = voices.find(v =>
                    v.lang.startsWith('en') && (v.name.includes('Google') || v.name.includes('Natural') || v.name.includes('Neural') || v.name.includes('Samantha') || v.name.includes('Alex'))
                ) || voices.find(v => v.lang.startsWith('en'));
                if (preferred) utter.voice = preferred;

                window.speechSynthesis.speak(utter);
            },

            cycleSpeechRate() {
                if (this.speechRate === 0.8) this.speechRate = 1.0;
                else if (this.speechRate === 1.0) this.speechRate = 1.2;
                else this.speechRate = 0.8;
            },

            toggleMute() {
                this.isMuted = !this.isMuted;
                if (this.isMuted && window.speechSynthesis) {
                    window.speechSynthesis.cancel();
                }
            },

            init() {
                this.$nextTick(() => {
                    const el = document.getElementById('chat-bottom');
                    if (el) el.scrollIntoView();
                });
                if (window.speechSynthesis) {
                    window.speechSynthesis.getVoices();
                    window.speechSynthesis.onvoiceschanged = () => window.speechSynthesis.getVoices();
                }
            }
        };
    }

    window.conversationRecorder = conversationRecorder;

    if (window.Alpine) {
        Alpine.data('conversationRecorder', conversationRecorder);
    } else {
        document.addEventListener('alpine:init', () => {
            Alpine.data('conversationRecorder', conversationRecorder);
        });
    }
</script>
