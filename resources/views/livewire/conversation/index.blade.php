<div>
    <x-slot:header>
        AI Conversation
    </x-slot:header>

    @if($state === 'scenarios')
        {{-- ================ SCENARIO PICKER ================ --}}
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
                    <svg class="animate-spin w-4 h-4 text-emerald-400" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                    Starting conversation...
                </div>
            </div>
        </div>

    @elseif($state === 'chat')
        {{-- ================ CHAT INTERFACE ================ --}}
        <div class="max-w-3xl mx-auto flex flex-col" style="height: calc(100vh - 200px);"
             x-data="conversationRecorder()"
             @speak-reply.window="speakText($event.detail.text)">

            {{-- Chat Header --}}
            <div class="flex items-center justify-between pb-4 border-b border-slate-800 mb-4 flex-shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-emerald-500/10 border border-emerald-500/30 flex items-center justify-center text-lg">🤖</div>
                    <div>
                        <h3 class="text-sm font-bold text-white">AI Partner</h3>
                        <p class="text-xs text-slate-500">{{ $scenario }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button @click="toggleMute()" class="p-2 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white transition-colors text-xs" :title="isMuted ? 'Unmute AI voice' : 'Mute AI voice'">
                        <template x-if="!isMuted"><span>🔊</span></template>
                        <template x-if="isMuted"><span>🔇</span></template>
                    </button>
                    <button wire:click="newConversation" class="ds-btn ds-btn-sm ds-btn-secondary">
                        ← New Topic
                    </button>
                </div>
            </div>

            {{-- Messages Area --}}
            <div class="flex-1 overflow-y-auto space-y-4 pr-2 pb-4" id="chat-messages" wire:poll.keep-alive>
                @foreach($messages as $msg)
                    @if($msg['role'] === 'assistant')
                        {{-- AI Message --}}
                        <div class="flex gap-3 items-start">
                            <div class="w-8 h-8 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center text-sm flex-shrink-0 mt-1">🤖</div>
                            <div class="max-w-[85%]">
                                <div class="bg-slate-800/80 border border-slate-700/60 rounded-2xl rounded-tl-md p-4 shadow-sm">
                                    <p class="text-sm text-slate-200 leading-relaxed">{{ $msg['text'] }}</p>
                                </div>
                                @if(!empty($msg['corrections']))
                                    <div class="mt-2 space-y-1.5">
                                        @foreach($msg['corrections'] as $c)
                                            <div class="flex items-start gap-2 text-xs px-3 py-2 rounded-lg bg-amber-500/10 border border-amber-500/20">
                                                <span class="text-amber-400 font-bold flex-shrink-0 mt-0.5">📝</span>
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
                            <div class="max-w-[85%]">
                                <div class="bg-emerald-600/20 border border-emerald-500/30 rounded-2xl rounded-tr-md p-4 shadow-sm">
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
                            <div class="flex gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-slate-500 animate-bounce" style="animation-delay: 0ms;"></span>
                                <span class="w-2 h-2 rounded-full bg-slate-500 animate-bounce" style="animation-delay: 150ms;"></span>
                                <span class="w-2 h-2 rounded-full bg-slate-500 animate-bounce" style="animation-delay: 300ms;"></span>
                            </div>
                        </div>
                    </div>
                @endif

                @if($errorMessage)
                    <div class="p-3 rounded-lg bg-red-500/10 border border-red-500/20 text-red-400 text-sm text-center">
                        {{ $errorMessage }}
                    </div>
                @endif
            </div>

            {{-- Mic Input Bar --}}
            <div class="flex-shrink-0 pt-4 border-t border-slate-800">
                {{-- Hidden file input for Livewire upload --}}
                <input type="file" wire:model="audioFile" x-ref="audioInput" class="hidden" accept="audio/*" />

                <div class="flex items-center justify-center gap-4">
                    {{-- Mic Button --}}
                    <button @mousedown="startRecording()" @mouseup="stopRecording()" @mouseleave="stopRecording()"
                            @touchstart.prevent="startRecording()" @touchend.prevent="stopRecording()"
                            :disabled="isProcessing"
                            class="relative w-20 h-20 rounded-full transition-all duration-200 flex items-center justify-center shadow-lg"
                            :class="isRecording 
                                ? 'bg-red-500 shadow-red-500/40 scale-110 animate-pulse border-4 border-red-400' 
                                : (isProcessing 
                                    ? 'bg-slate-700 cursor-wait border-2 border-slate-600' 
                                    : 'bg-emerald-600 hover:bg-emerald-500 hover:scale-105 hover:shadow-emerald-500/30 border-2 border-emerald-500')">
                        
                        <template x-if="isRecording">
                            <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 14c1.66 0 2.99-1.34 2.99-3L15 5c0-1.66-1.34-3-3-3S9 3.34 9 5v6c0 1.66 1.34 3 3 3zm5.3-3c0 3-2.54 5.1-5.3 5.1S6.7 14 6.7 11H5c0 3.41 2.72 6.23 6 6.72V21h2v-3.28c3.28-.48 6-3.3 6-6.72h-1.7z"/></svg>
                        </template>
                        <template x-if="!isRecording && !isProcessing">
                            <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 14c1.66 0 2.99-1.34 2.99-3L15 5c0-1.66-1.34-3-3-3S9 3.34 9 5v6c0 1.66 1.34 3 3 3zm5.3-3c0 3-2.54 5.1-5.3 5.1S6.7 14 6.7 11H5c0 3.41 2.72 6.23 6 6.72V21h2v-3.28c3.28-.48 6-3.3 6-6.72h-1.7z"/></svg>
                        </template>
                        <template x-if="isProcessing">
                            <svg class="w-6 h-6 text-white animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                        </template>
                    </button>
                </div>

                <p class="text-center text-xs text-slate-500 mt-3 font-medium"
                   x-text="isRecording ? '🔴 Recording... Release to send' : (isProcessing ? '⏳ Processing your message...' : 'Hold the mic button to speak')">
                </p>
            </div>
        </div>

        {{-- Alpine.js Recorder Logic --}}
        <script>
            function conversationRecorder() {
                return {
                    isRecording: false,
                    isProcessing: false,
                    isMuted: false,
                    mediaRecorder: null,
                    audioChunks: [],
                    stream: null,

                    async startRecording() {
                        if (this.isRecording || this.isProcessing) return;

                        try {
                            this.stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                            this.mediaRecorder = new MediaRecorder(this.stream, { mimeType: 'audio/webm;codecs=opus' });
                            this.audioChunks = [];

                            this.mediaRecorder.ondataavailable = (e) => {
                                if (e.data.size > 0) this.audioChunks.push(e.data);
                            };

                            this.mediaRecorder.onstop = () => {
                                const blob = new Blob(this.audioChunks, { type: 'audio/webm' });
                                this.uploadAudio(blob);
                                this.stream.getTracks().forEach(t => t.stop());
                            };

                            this.mediaRecorder.start();
                            this.isRecording = true;
                        } catch (err) {
                            console.error('Microphone access denied:', err);
                            alert('Please allow microphone access to use voice conversation.');
                        }
                    },

                    stopRecording() {
                        if (!this.isRecording || !this.mediaRecorder) return;
                        this.isRecording = false;
                        this.isProcessing = true;
                        this.mediaRecorder.stop();
                    },

                    uploadAudio(blob) {
                        // Create a File from the Blob and set it on the Livewire file input
                        const file = new File([blob], 'recording.webm', { type: 'audio/webm' });
                        const dataTransfer = new DataTransfer();
                        dataTransfer.items.add(file);
                        this.$refs.audioInput.files = dataTransfer.files;
                        this.$refs.audioInput.dispatchEvent(new Event('change', { bubbles: true }));

                        // Reset processing state after Livewire finishes
                        const component = this;
                        Livewire.hook('request', ({ respond }) => {
                            respond(() => {
                                component.isProcessing = false;
                                // Auto-scroll to bottom
                                setTimeout(() => {
                                    const container = document.getElementById('chat-messages');
                                    if (container) container.scrollTop = container.scrollHeight;
                                }, 100);
                            });
                        });
                    },

                    speakText(text) {
                        if (this.isMuted || !window.speechSynthesis) return;
                        window.speechSynthesis.cancel();
                        const utterance = new SpeechSynthesisUtterance(text);
                        utterance.lang = 'en-US';
                        utterance.rate = 0.95;
                        utterance.pitch = 1;
                        window.speechSynthesis.speak(utterance);
                    },

                    toggleMute() {
                        this.isMuted = !this.isMuted;
                        if (this.isMuted) window.speechSynthesis.cancel();
                    }
                }
            }
        </script>
    @endif
</div>
