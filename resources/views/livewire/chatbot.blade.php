<div class="fixed bottom-4 right-4 z-50">

    {{-- Floating Button --}}
    @if(!$isOpen)
        <button wire:click="toggleChat"
    class="bg-gradient-to-r from-cyan-400 to-purple-600 text-white w-14 h-14 rounded-full flex items-center justify-center shadow-lg hover:scale-110 hover:shadow-2xl transition transform">

    {{-- Icon Chat sesuai contoh --}}
    <svg xmlns="http://www.w3.org/2000/svg"
         viewBox="0 0 24 24"
         fill="none"
         stroke="currentColor"
         stroke-width="2"
         stroke-linecap="round"
         stroke-linejoin="round"
         class="w-7 h-7">

        <path d="M7 4h10a3 3 0 0 1 3 3v7a3 3 0 0 1-3 3h-5l-4 4v-4H7a3 3 0 0 1-3-3V7a3 3 0 0 1 3-3z" />
        <path d="M9 9h6" />
        <path d="M9 13h4" />

    </svg>

</button>

    @endif

    {{-- Chat Window --}}
    @if($isOpen)
        <div class="w-96 h-[550px] bg-white shadow-xl rounded-xl border border-gray-300 flex flex-col">

            {{-- Header --}}
           <div class="relative p-2 bg-white rounded-t-xl flex items-center">
                    <h2 class="absolute left-1/2 -translate-x-1/2 font-medium text-md
                        bg-gradient-to-r from-cyan-400 to-purple-600 
                        bg-clip-text text-transparent">
                    Virtual Assistant
                </h2>
                <div class="w-8"></div>
                <button 
                    wire:click="toggleChat"
                    class="ml-auto p-1 rounded-full text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition"
                    aria-label="Close chat"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" 
                        class="w-5 h-5" 
                        fill="none" 
                        viewBox="0 0 24 24" 
                        stroke="currentColor" 
                        stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

            </div>

            {{-- Messages --}}
            <div class="flex-1 overflow-y-auto pt-0 p-4 space-y-3">

                {{-- Welcome Message (hanya tampil jika tidak ada pesan) --}}
                @if(count($messages) === 0 && !$isLoading)
                    <div class="flex items-center justify-center h-full">
                        <div class="text-center text-gray-400">
                            <p class="text-sm">Welcome!</p>
                            <p class="text-xs mt-1">I’m your Virtual Assistant, <br>here to help you track projects and get information quickly.</p>
                        </div>
                    </div>
                @else
                    @foreach($messages as $msg)
                        @if($msg['role'] === 'user')
                            <div class="text-right flex justify-end">
                                <div
                                    class="max-w-[85%] px-4 py-2 
                                        bg-gradient-to-r from-cyan-400 to-purple-600 
                                        text-white text-sm rounded-3xl break-words">
                                    {{ $msg['message'] }}
                                </div>
                            </div>

                        @else
                            <div class="text-left flex justify-start">
                                <div
                                    class="max-w-[85%] px-4 py-2 
                                        bg-white text-gray-800 text-sm 
                                        border border-gray-300 rounded-3xl break-words">
                                    {{ $msg['message'] }}
                                </div>
                            </div>

                        @endif
                    @endforeach

                    {{-- Loading Indicator --}}
                    @if($isLoading)
                        <div class="text-left">
                            <div class="inline-block px-4 py-2 bg-gradient-to-r from-purple-500 to-pink-400 text-white rounded-full">
                                <span class="animate-pulse">AI sedang mengetik...</span>
                            </div>
                        </div>
                    @endif
                @endif

            </div>

            {{-- Input --}}
            <div class="p-4 pt-1 flex items-end space-x-1">
                <textarea
                    wire:model.defer="input"
                    wire:keydown.enter="sendMessage"
                    rows="1"
                    placeholder="Ask anything..."
                    class="text-sm flex-1 border rounded-3xl p-2 focus:outline-none resize-none overflow-y-auto"
                    style="
                        scrollbar-width: none;        /* Firefox */
                        -ms-overflow-style: none;     /* IE & Edge */
                    "
                    oninput="
                        this.style.height = '';
                        this.style.height = Math.min(this.scrollHeight, 120) + 'px';
                    "
                    onwheel="this.scrollTop += event.deltaY"
                ></textarea>

                <style>
                textarea::-webkit-scrollbar {
                    display: none; /* Chrome, Safari */
                }
                </style>


                <button wire:click="sendMessage"
                    class="bg-blue-500 hover:bg-blue-600 text-white p-3 rounded-full flex items-center justify-center shadow">
                    <!-- Ikon Panah (Paper Plane) -->
                    <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                        viewBox="0 0 16 16" class="w-4 h-4">
                        <path
                            d="M15.854.146a.5.5 0 0 0-.548-.105l-15 6a.5.5 0 0 0-.014.924l6.65 2.66 2.66 6.65a.5.5 0 0 0 .924-.014l6-15a.5.5 0 0 0-.105-.548zM6.832 8.482 1.77 6.56l12.26-4.904L6.832 8.482zm.82.82 4.904-4.904-1.923 5.062L7.652 9.302z" />
                    </svg>
                </button>
            </div>

        </div>
    @endif
</div>