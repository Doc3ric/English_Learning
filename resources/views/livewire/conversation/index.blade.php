<x-slot:fullHeight>{{ true }}</x-slot:fullHeight>

<div class="flex flex-col h-full">

    @if($state === 'scenarios')
        {{-- ================ SCENARIO PICKER ================ --}}
        <div class="flex-1 overflow-y-auto p-8">
            <div class="max-w-4xl mx-auto">
                <div class="text-center mb-10">
                    <h2 class="text-3xl font-black text-white tracking-tight mb-2">Choose a Scenario</h2>
                    <p class="text-slate-400 text-sm">Pick a situation and practice speaking English with your AI conversation partner.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    @foreach(\App\Livewire\Conversation\Index::SCENARIOS as $s)
                        <button wire:click="startConversation('{{ $s['id'] }}')"
                                wire:loading.attr="disabled"
                                class="ds-card p-6 text-left hover:border-emerald-500/40 hover:shadow-[0_0_25px_rgba(16,185,129,0.12)] transition-all group cursor-pointer">
                            <div class="text-4xl mb-3">{{ $s['icon'] }}</div>
                            <h3 class="text-lg font-bold text-white group-hover:text-emerald-400 transition-colors mb-1">{{ $s['title'] }}</h3>
                            <p class="text-xs text-slate-400">{{ $s['desc'] }}</p>
                        </button>
                    @endforeach
                </div>

                <div wire:loading class="text-center mt-8">
                    <div class="inline-flex items-center gap-3 px-5 py-3 rounded-full bg-slate-800/80 border border-slate-700 text-slate-300 text-sm">
                        <svg class="animate-spin w-4 h-4 text-emerald-400" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        Starting conversation...
                    </div>
                </div>
            </div>
        </div>

    @elseif($state === 'chat')
        {{-- ================ CHAT INTERFACE ================ --}}
        <div class="flex flex-col h-full"
             x-data="conversationRecorder()"
             @speak-reply.window="queueSpeak($event.detail.text)">

            {{-- Chat Header --}}
            <div class="flex-shrink-0 flex items-center justify-between px-6 py-4 border-b border-slate-800 bg-slate-900/60 backdrop-blur">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-violet-500/10 border border-violet-500/30 flex items-center justify-center text-lg">🤖</div>
                    <div>
                        <h3 class="text-sm font-bold text-white">AI Partner</h3>
                        <p class="text-xs text-slate-500">{{ $scenario }}</p>
                    </div>
                    <div class="ml-2 flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span class="text-xs text-emerald-400 font-medium">Live</span>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button @click="toggleMute()"
                            class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white transition-colors text-xs font-medium border border-slate-700">
                        <span x-text="isMuted ? '🔇' : '🔊'"></span>
                        <span x-text="isMuted ? 'Muted' : 'Sound On'"></span>
                    </button>
                    <button wire:click="newConversation"
                            class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 transition-colors text-xs font-medium border border-slate-700">
                        ← New Topic
                    </button>
                </div>
            </div>

            {{-- Messages Area (scrollable) --}}
            <div class="flex-1 overflow-y-auto px-6 py-4 space-y-5" id="chat-messages">

                @foreach($messages as $msg)
                    @if($msg['role'] === 'assistant')
                        {{-- AI Message --}}
                        <div class="flex gap-3 items-start">
                            <div class="w-8 h-8 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center text-sm flex-shrink-0 mt-1">🤖</div>
                            <div class="max-w-[80%]">
                                <div class="bg-slate-800/80 border border-slate-700/60 rounded-2xl rounded-tl-md px-4 py-3 shadow-sm">
                                    <p class="text-sm text-slate-200 leading-relaxed">{{ $msg['text'] }}</p>
                                </div>
                                @if(!empty($msg['corrections']))
                                    <div class="mt-2 space-y-1.5">
                                        @foreach($msg['corrections'] as $c)
                                            <div class="flex items-start gap-2 text-xs px-3 py-2 rounded-lg bg-amber-500/10 border border-amber-500/20">
                                                <span class="text-amber-400 font-bold flex-shrink-0">📝</span>
                                                <div>
                                                    <span class="text-red-400 line-through">{{ $c['wrong'] ?? '' }}</span>
                                                    <span class="text-slate-500 mx-1">→</span>
                                                    <span class="text-emerald-400 font-semibold">{{ $c['correct'] ?? '' }}</span>
                                                    @if(!empty($c['reason']))
                                                        <span class="text-slate-500 ml-1">({{ $c['reason'] }})</span>
                                                    @endif
                                                </div>
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

            {{-- ===== MIC INPUT BAR (always visible at bottom) ===== --}}
            <div class="flex-shrink-0 bg-slate-900/80 backdrop-blur border-t border-slate-800 px-6 py-5">

                {{-- Hidden Livewire file input --}}
                <input type="file" wire:model="audioFile" x-ref="audioInput" class="hidden" accept="audio/*" />

                <div class="flex flex-col items-center gap-3">

                    {{-- Status label --}}
                    <p class="text-xs font-medium transition-colors"
                       :class="isRecording ? 'text-red-400' : (isProcessing ? 'text-amber-400' : 'text-slate-500')"
                       x-text="isRecording ? '🔴 Recording... Release to send'
                              : (isProcessing ? '⏳ Transcribing & thinking...'
                              : 'Hold the button and speak')">
                    </p>

                    {{-- Mic Button --}}
                    <button @mousedown.prevent="startRecording()"
                            @mouseup.prevent="stopRecording()"
                            @mouseleave="stopRecording()"
                            @touchstart.prevent="startRecording()"
                            @touchend.prevent="stopRecording()"
                            :disabled="isProcessing"
                            class="w-20 h-20 rounded-full flex items-center justify-center shadow-xl transition-all duration-150 select-none"
                            :class="isRecording
                                ? 'bg-red-500 border-4 border-red-300 scale-110 shadow-red-500/50'
                                : (isProcessing
                                    ? 'bg-slate-700 border-2 border-slate-600 cursor-wait opacity-60'
                                    : 'bg-violet-600 border-2 border-violet-400 hover:bg-violet-500 hover:scale-105 hover:shadow-violet-500/40 active:scale-95')">

                        <template x-if="isProcessing">
                            <svg class="w-7 h-7 text-white animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                        </template>
                        <template x-if="!isProcessing">
                            <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 14c1.66 0 3-1.34 3-3V5c0-1.66-1.34-3-3-3S9 3.34 9 5v6c0 1.66 1.34 3 3 3zm5.3-3c0 3-2.54 5.1-5.3 5.1S6.7 14 6.7 11H5c0 3.41 2.72 6.23 6 6.72V21h2v-3.28c3.28-.48 6-3.3 6-6.72h-1.7z"/>
                            </svg>
                        </template>
                    </button>

                    {{-- Waveform visual when recording --}}
                    <div class="flex gap-1 h-4 items-end" x-show="isRecording">
                        <span class="w-1 bg-red-400 rounded animate-bounce" style="height:60%;animation-delay:0ms"></span>
                        <span class="w-1 bg-red-400 rounded animate-bounce" style="height:100%;animation-delay:100ms"></span>
                        <span class="w-1 bg-red-400 rounded animate-bounce" style="height:40%;animation-delay:200ms"></span>
                        <span class="w-1 bg-red-400 rounded animate-bounce" style="height:80%;animation-delay:300ms"></span>
                        <span class="w-1 bg-red-400 rounded animate-bounce" style="height:60%;animation-delay:400ms"></span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Alpine.js Recorder --}}
        <script>
        function conversationRecorder() {
            return {
                isRecording: false,
                isProcessing: false,
                isMuted: false,
                mediaRecorder: null,
                audioChunks: [],
                stream: null,
                pendingSpeech: null,

                async startRecording() {
                    if (this.isRecording || this.isProcessing) return;
                    // Cancel any ongoing speech so mic picks up cleanly
                    window.speechSynthesis.cancel();
                    try {
                        this.stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                        // Try opus/webm, fall back to default
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
                            this.stream.getTracks().forEach(t => t.stop());
                        };
                        this.mediaRecorder.start(100); // collect every 100ms
                        this.isRecording = true;
                    } catch (err) {
                        alert('Microphone access denied. Please allow mic access in your browser and try again.');
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

                    // Watch for Livewire to finish updating, then reset processing state
                    const done = () => {
                        this.isProcessing = false;
                        this.$nextTick(() => {
                            const el = document.getElementById('chat-bottom');
                            if (el) el.scrollIntoView({ behavior: 'smooth' });
                            // Speak queued text now that Livewire re-render is done
                            if (this.pendingSpeech) {
                                this.doSpeak(this.pendingSpeech);
                                this.pendingSpeech = null;
                            }
                        });
                    };

                    // Livewire 3 hook
                    if (window.Livewire) {
                        const unsub = Livewire.hook('morph.updated', ({ component }) => {
                            done();
                            unsub();
                        });
                        // Safety fallback in case hook doesn't fire
                        setTimeout(() => { this.isProcessing = false; }, 15000);
                    } else {
                        setTimeout(done, 5000);
                    }
                },

                // Called by @speak-reply.window event from Livewire
                queueSpeak(text) {
                    // Queue it — actual speak happens after Livewire re-render (in uploadAudio done())
                    // For the opening AI message (no upload in progress), speak immediately
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
                    utter.rate = 0.95;
                    utter.pitch = 1.0;
                    // Pick a natural English voice if available
                    const voices = window.speechSynthesis.getVoices();
                    const preferred = voices.find(v =>
                        v.lang.startsWith('en') && (v.name.includes('Google') || v.name.includes('Natural') || v.name.includes('Neural'))
                    ) || voices.find(v => v.lang.startsWith('en'));
                    if (preferred) utter.voice = preferred;
                    window.speechSynthesis.speak(utter);
                },

                toggleMute() {
                    this.isMuted = !this.isMuted;
                    if (this.isMuted) window.speechSynthesis.cancel();
                },

                // Auto-scroll on init
                init() {
                    this.$nextTick(() => {
                        const el = document.getElementById('chat-bottom');
                        if (el) el.scrollIntoView();
                    });
                    // Warm up voices list (browsers load this lazily)
                    if (window.speechSynthesis) {
                        window.speechSynthesis.getVoices();
                        window.speechSynthesis.onvoiceschanged = () => window.speechSynthesis.getVoices();
                    }
                }
            }
        }
        </script>
    @endif
</div>
