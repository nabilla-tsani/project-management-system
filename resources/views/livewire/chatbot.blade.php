<div class="fixed bottom-4 right-4 z-50">
    {{-- Tombol Chat - hanya tampil jika chat belum terbuka --}}
    @if(!$isOpen)
        <button wire:click="toggleChat"
    class="bg-gradient-to-r from-cyan-400 to-purple-600 text-white font-extrabold tracking-widest text-lg w-14 h-14 rounded-full flex items-center justify-center shadow-lg hover:scale-110 hover:shadow-2xl transition transform">
    AI
</button>

    @endif

    @if($isOpen)
        <div class="mt-2 w-80 h-[550px] border border-gray-200 rounded-xl shadow-2xl bg-white flex flex-col p-4 text-sm">
            <div class="flex justify-between items-center mb-2">
                <h2 class="text-lg font-semibold text-gray-700">Virtual Assistant</h2>
                <button wire:click="toggleChat" class="text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
            </div>

            <div class="flex-1 overflow-y-auto space-y-2">
                @foreach($messages as $msg)
                    <div
                        class="max-w-[70%] p-2 rounded-xl {{ $msg['role'] === 'user' ? 'bg-blue-500 text-white self-end ml-auto' : 'bg-gray-200 text-gray-900 self-start mr-auto' }}">
                        {{ $msg['message'] }}
                    </div>
                @endforeach

                {{-- ✅ Indikator Loading --}}
                @if($isLoading)
                    <div class="max-w-[70%] p-2 rounded-xl bg-gray-200 text-gray-600 text-sm italic self-start mr-auto animate-pulse">
                        AI sedang mengetik...
                    </div>
                @endif
            </div>

            <div class="mt-3 flex items-end space-x-1">
                <textarea wire:model.defer="input"
                    wire:keydown.enter="sendMessage"
                    class="flex-1 border rounded-xl p-2 focus:outline-none resize-none overflow-hidden"
                    placeholder="Tanyakan sesuatu..."
                    rows="1"
                    oninput="
                        this.style.height = '';
                        this.style.height = Math.min(this.scrollHeight, 120) + 'px';
                    ">
                </textarea>

                <button wire:click="sendMessage"
                    class="bg-blue-500 hover:bg-blue-600 text-white p-3 rounded-full flex items-center justify-center shadow">
                    <!-- Ikon Panah (Paper Plane) -->
                    <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                        viewBox="0 0 16 16" class="w-5 h-5">
                        <path
                            d="M15.854.146a.5.5 0 0 0-.548-.105l-15 6a.5.5 0 0 0-.014.924l6.65 2.66 2.66 6.65a.5.5 0 0 0 .924-.014l6-15a.5.5 0 0 0-.105-.548zM6.832 8.482 1.77 6.56l12.26-4.904L6.832 8.482zm.82.82 4.904-4.904-1.923 5.062L7.652 9.302z" />
                    </svg>
                </button>
            </div>

            <button wire:click="resetChat" class="mt-2 text-red-500 text-xs">
                Hapus Riwayat
            </button>
        </div>
    @endif
</div>

<script>
document.addEventListener('livewire:init', () => {
    Livewire.on('fetch-ai-response', () => {
        @this.call('fetchAiResponse');
    });
});
</script>
