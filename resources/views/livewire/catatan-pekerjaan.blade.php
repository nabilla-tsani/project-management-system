<div>
    @if($catatanModal)
        <div 
            class="fixed inset-0 flex items-center justify-center bg-gradient-to-br from-black/60 to-black/40 backdrop-blur-sm z-50 p-4"
            wire:click.self="closeModal"
        >
            <div class="bg-white rounded-md shadow-2xl 
            w-full max-w-5xl 
            h-screen max-h-screen
            flex flex-col overflow-hidden">
                {{-- Header Modal --}}
                <div class="bg-gradient-to-r from-[#5ca9ff] to-[#9c62ff] px-4 py-2">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="flex items-center justify-center bg-white/20 backdrop-blur-sm w-7 h-7 rounded-lg">
                                <i class="fas fa-tasks text-white text-xs"></i>
                            </div>
                            <div>
                                <h3 class="text-white text-sm font-semibold">{{ $namaFitur ?? 'Nama Fitur' }}</h3>
                            </div>
                        </div>
                        <button 
                            wire:click="closeModal"
                            class="group flex items-center justify-center w-7 h-7 text-white/70 rounded-full transition-all duration-200 hover:text-white hover:bg-white/20 hover:scale-105"
                        >
                            <i class="fas fa-times text-sm leading-none"></i>
                        </button>
                    </div>
                </div>

                <div class="flex gap-3 p-2 flex-1 overflow-hidden" x-data="{ expanded: false }">
                    
                    @if($isMember)
                    {{-- Kiri: Form (Expand/Collapse) --}}
                    <div 
                        x-show="expanded"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 -translate-x-full"
                        x-transition:enter-end="opacity-100 translate-x-0"
                        x-transition:leave="transition ease-in duration-300"
                        x-transition:leave-start="opacity-100 translate-x-0"
                        x-transition:leave-end="opacity-0 -translate-x-full"
                        class="w-1/3 flex-shrink-0"
                    >
                        <div class="bg-gradient-to-br from-purple-50 to-white rounded-lg border border-purple-100 p-3 sticky top-0">
                            
                            {{-- Header Form --}}
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <div class="flex items-center justify-center w-6 h-6 bg-gradient-to-r from-[#9c62ff] to-[#7c3eff] rounded-md">
                                        <i class="fas fa-plus text-white text-xs"></i>
                                    </div>
                                    <h3 class="text-xs font-bold text-gray-800">
                                        {{ $catatanId ? 'Edit Catatan' : 'Tambah Catatan' }}
                                    </h3>
                                </div>
                                
                                {{-- Tombol Collapse --}}
                                <button 
                                    @click="expanded = false"
                                    class="text-gray-400 hover:text-gray-600 transition-colors duration-200"
                                    title="Sembunyikan Form"
                                >
                                    <i class="fas fa-chevron-left text-xs"></i>
                                </button>
                            </div>

                            <form 
                                wire:submit.prevent="save" 
                                wire:key="{{ $formKey }}" 
                                class="space-y-2 mt-3"
                            >
                                {{-- Jenis --}}
                                <div class="text-xs">
                                    <label class="block text-gray-700 font-semibold mb-1 text-[10px] flex items-center gap-1">
                                        <i class="fas fa-tag text-[#9c62ff] text-[8px]"></i>
                                        Jenis Catatan
                                    </label>
                                    <select 
                                        wire:model.live="jenis"
                                        class="w-full border border-gray-200 rounded-lg px-2 py-1.5 bg-white focus:outline-none focus:border-[#9c62ff] focus:ring-1 focus:ring-[#9c62ff]/10 transition-all duration-200 text-[10px] text-gray-600 pr-7"
                                    >
                                        <option value="">-- Pilih Jenis Catatan --</option>
                                        <option value="bug">Bug</option>
                                        <option value="pekerjaan">Catatan</option>
                                    </select>
                                    @error('jenis') 
                                        <span class="text-xs text-red-500">{{ $message }}</span> 
                                    @enderror
                                </div>

                                <div class="flex gap-1.5">
                                    {{-- Tanggal Mulai --}}
                                    <div class="w-1/2">
                                        <label class="block text-gray-700 font-semibold mb-1 text-[10px] flex items-center gap-1">
                                            <i class="fas fa-calendar-plus text-[#9c62ff] text-[8px]"></i>
                                            Tgl Mulai
                                        </label>
                                        <input 
                                            type="date" 
                                            wire:model.live="tanggal_mulai"
                                            class="w-full border border-gray-200 rounded-lg px-2 py-1.5 bg-white focus:outline-none focus:border-[#9c62ff] focus:ring-1 focus:ring-[#9c62ff]/10 transition-all duration-200 text-[10px] text-gray-600"
                                        >
                                        @error('tanggal_mulai') 
                                            <span class="text-red-500 text-[9px] mt-0.5 flex items-center gap-1">
                                                <i class="fas fa-exclamation-circle"></i>
                                                {{ $message }}
                                            </span> 
                                        @enderror
                                    </div>

                                    {{-- Tanggal Selesai --}}
                                    <div class="w-1/2">
                                        <label class="block text-gray-700 font-semibold mb-1 text-[10px] flex items-center gap-1">
                                            <i class="fas fa-calendar-check text-[#9c62ff] text-[8px]"></i>
                                            Tgl Selesai
                                        </label>
                                        <input 
                                            type="date" 
                                            wire:model.live="tanggal_selesai"
                                            class="w-full border border-gray-200 rounded-lg px-2 py-1.5 bg-white focus:outline-none focus:border-[#9c62ff] focus:ring-1 focus:ring-[#9c62ff]/10 transition-all duration-200 text-[10px] text-gray-600"
                                        >
                                        @error('tanggal_selesai') 
                                            <span class="text-red-500 text-[9px] mt-0.5 flex items-center gap-1">
                                                <i class="fas fa-exclamation-circle"></i>
                                                {{ $message }}
                                            </span> 
                                        @enderror
                                    </div>
                                </div>

                                {{-- Catatan --}}
                                <div>
                                    <label class="block text-gray-700 font-semibold mb-1 text-[10px] flex items-center gap-1">
                                        <i class="fas fa-clipboard-list text-[#9c62ff] text-[8px]"></i>
                                        Deskripsi Catatan
                                    </label>
                                    <textarea 
                                        wire:model.live="isiCatatan" 
                                        rows="6"
                                        class="w-full border border-gray-200 rounded-lg px-2 py-1.5 bg-white focus:outline-none focus:border-[#9c62ff] focus:ring-1 focus:ring-[#9c62ff]/10 transition-all duration-200 resize-none text-[10px] text-gray-600"
                                        placeholder="Tambahkan catatan di sini..."
                                    ></textarea>
                                    @error('isiCatatan') 
                                        <span class="text-red-500 text-[9px] mt-0.5 flex items-center gap-1">
                                            <i class="fas fa-exclamation-circle"></i>
                                            {{ $message }}
                                        </span> 
                                    @enderror
                                </div>

                                {{-- Tombol --}}
                                <div class="flex gap-1.5 pt-0.5">
                                    @if($catatanId)
                                        <button 
                                            type="button" 
                                            wire:click="cancelEdit"
                                            class="flex-1 px-2 py-1.5 bg-gray-100 text-gray-700 rounded-lg text-[10px] font-semibold hover:bg-gray-200 transition-all duration-200 flex items-center justify-center gap-1"
                                        >
                                            <i class="fas fa-times text-[8px]"></i>
                                            Batal
                                        </button>
                                    @endif

                                    <button 
                                        type="submit"
                                        class="flex-1 px-2 py-1.5 bg-gradient-to-r from-[#9c62ff] to-[#7c3eff] text-white rounded-lg text-[10px] font-semibold hover:shadow-lg hover:scale-[1.02] transition-all duration-200 flex items-center justify-center gap-1"
                                    >
                                        <i class="fas fa-{{ $catatanId ? 'check' : 'save' }} text-[8px]"></i>
                                        {{ $catatanId ? 'Perbarui' : 'Simpan' }}
                                    </button>
                                </div>
                            </form>

                        </div>
                    </div>
                    @endif

                    {{-- Kanan: Daftar --}}
                    <div 
                        :class="expanded ? 'w-2/3' : 'w-full'"
                        class="transition-all duration-300 ease-in-out 
                            relative flex-shrink-0
                            flex flex-col h-full"
                    >

                        <div class="bg-gradient-to-br from-gray-50 to-white rounded-lg border border-gray-200 p-2 h-full">
                            
                            {{-- Header dengan Filter --}}
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center gap-2">
                                    @if($isMember)
                                        {{-- ICON TAMBAH (MEMBER) --}}
                                        <div
                                            x-show="!expanded"
                                            @click="expanded = true"
                                            x-transition:enter="transition ease-out duration-200"
                                            x-transition:enter-start="opacity-0 scale-90"
                                            x-transition:enter-end="opacity-100 scale-100"
                                            class="flex items-center justify-center w-6 h-6
                                                bg-gradient-to-br from-[#9c62ff] to-[#7c3eff]
                                                rounded-md cursor-pointer
                                                hover:scale-110 transition-all duration-200"
                                            title="Tampilkan Form"
                                        >
                                            <i class="fas fa-plus text-white text-[10px]"></i>
                                        </div>
                                    @else
                                        {{-- ICON LIST (BUKAN MEMBER) --}}
                                        <div
                                            class="flex items-center justify-center w-6 h-6
                                                bg-gradient-to-br from-[#5ca9ff] to-[#449bff]
                                                rounded-md"
                                            title="Daftar"
                                        >
                                            <i class="fas fa-list text-white text-[10px]"></i>
                                        </div>
                                    @endif

                                    <h3 class="text-xs font-bold text-gray-800">Daftar Catatan</h3>
                                </div>
                                
                                {{-- Filter --}}
                                <div class="mb-3 flex justify-end gap-2 text-[10px]">
                                    <label for="filterJenis" class="text-gray-600">Filter:</label>
                                    <select 
                                        id="filterJenis"
                                        wire:model="filterJenis"
                                        class="text-[10px] border rounded-3xl px-7 bg-white focus:outline-none focus:ring focus:ring-[#5ca9ff]/50"
                                    >
                                        <option value="">Semua</option>a
                                        <option value="pekerjaan">Catatan</option>
                                        <option value="bug">Bug</option>
                                    </select>
                                </div>
                            </div>
                            
                            {{-- Flash Message --}}
                            @if (session()->has('message'))
                                <div 
                                    x-data="{ show: true }"
                                    x-init="setTimeout(() => show = false, 2000)"
                                    x-show="show"
                                    x-transition:enter="transition ease-out duration-300"
                                    x-transition:enter-start="opacity-0 transform scale-90"
                                    x-transition:enter-end="opacity-100 transform scale-100"
                                    x-transition:leave="transition ease-in duration-200"
                                    x-transition:leave-start="opacity-100"
                                    x-transition:leave-end="opacity-0"
                                    class="mb-2 text-[10px] text-green-700 bg-gradient-to-r from-green-50 to-green-100 px-2 py-1.5 rounded-lg shadow-sm border border-green-200 flex items-center gap-1.5"
                                >
                                    <i class="fas fa-check-circle text-[9px]"></i>
                                    {{ session('message') }}
                                </div>
                            @endif
                            
                            {{-- List Catatan --}}
                            <div class="h-[380px] overflow-y-auto pr-2 space-y-1.5 custom-scrollbar">
                            <!-- <div class="flex-1 overflow-y-auto pr-2 space-y-1.5 custom-scrollbar"> -->
                                @if($catatan->isEmpty())
                                    <div class="h-full flex flex-col items-center justify-center text-center py-6">
                                        <div class="bg-gray-100 rounded-full p-3 mb-2">
                                            <i class="fas fa-clipboard-list text-gray-400 text-xl"></i>
                                        </div>
                                        <p class="text-gray-400 font-medium text-[10px]">Belum ada Catatan</p>
                                    </div>
                                @else
                                    @foreach($catatan as $item)
                                        <div class="bg-white rounded-lg p-2 shadow-sm hover:shadow-md transition-all duration-200 border border-gray-100">
                                            
                                            {{-- Header Item --}}
                                            <div class="flex justify-between items-start mb-1.5">
                                                {{-- Jenis + User --}}
                                                <div class="flex items-center gap-1.5">
                                                    {{-- Badge Jenis --}}
                                                    <span class="flex items-center gap-0.5 text-[10px] font-semibold px-1.5 py-0.5 rounded-md
                                                        {{ $item->jenis === 'pekerjaan' 
                                                            ? 'bg-[#5ca9ff]/10 text-[#5ca9ff]' 
                                                            : 'bg-[#9c62ff]/10 text-[#9c62ff]' }}">
                                                        <i class="{{ $item->jenis === 'pekerjaan' 
                                                            ? 'fa-solid fa-check-circle' 
                                                            : 'fa-solid fa-bug' }} text-[8px]">
                                                        </i>
                                                        {{ $item->jenis === 'pekerjaan' ? 'Catatan' : 'Bug' }}
                                                    </span>
                                                    
                                                    {{-- User --}}
                                                    <span class="text-[9px] text-gray-400 italic">
                                                        oleh: {{ $item->user->name ?? '-' }}
                                                    </span>
                                                </div>

                                                {{-- Tanggal + Aksi --}}
                                                <div class="flex items-center gap-1.5">
                                                    {{-- Tanggal --}}
                                                    <div class="flex items-center gap-0.5 text-[9px] text-gray-400">
                                                        <i class="fas fa-calendar text-[7px]"></i>
                                                        <span>
                                                            {{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('d M') }}
                                                            -
                                                            {{ $item->tanggal_selesai 
                                                                ? \Carbon\Carbon::parse($item->tanggal_selesai)->format('d M') 
                                                                : 'Selesai' }}
                                                        </span>
                                                    </div>

                                                    {{-- Tombol Aksi --}}
                                                    <div class="flex items-center gap-1">
                                                        @if($item->user_id === auth()->id())
                                                            <button 
                                                                wire:click="edit({{ $item->id }})" 
                                                                @click="expanded = true"
                                                                class="p-1 text-blue-600 hover:scale-150 rounded transition-colors duration-200" 
                                                                title="Edit Catatan"
                                                            >
                                                                <i class="fa-solid fa-pen-to-square text-[10px]"></i>
                                                            </button>

                                                            <button 
                                                                wire:click="delete({{ $item->id }})" 
                                                                class="p-1 text-red-600 hover:scale-150 rounded transition-colors duration-200" 
                                                                title="Hapus Catatan"
                                                            >
                                                                <i class="fa-solid fa-trash text-[10px]"></i>
                                                            </button>
                                                        @endif

                                                        @if($roleUser === 'manajer proyek')
                                                            <button 
                                                                wire:click="openFeedbackModal({{ $item->id }})"
                                                                class="p-1 text-purple-600 hover:scale-150 rounded transition-colors duration-200"
                                                                title="Beri Feedback"
                                                            >
                                                                <i class="fa-regular fa-comment-dots text-xs"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Deskripsi --}}
                                            <p class="text-[10px] text-gray-700 leading-relaxed text-justify">
                                                {{ $item->catatan }}
                                            </p>

                                            {{-- Feedback --}}
                                            @if($item->feedback)
                                                <div class="mt-1.5 bg-gradient-to-br from-purple-50 to-purple-100/50 border border-purple-200 rounded-lg p-1.5">
                                                    <div class="flex items-start gap-1.5">
                                                        <i class="fa-solid fa-comment-dots text-purple-500 text-[10px] mt-0.5"></i>
                                                        <p class="flex-1 text-[10px] text-gray-700 italic leading-relaxed text-justify">
                                                            {{ $item->feedback }}
                                                        </p>
                                                        
                                                        @if($roleUser === 'manajer proyek')
                                                            <button 
                                                                wire:click="deleteFeedback({{ $item->id }})"
                                                                class="p-0.5 text-red-500 hover:scale-150 rounded transition-colors duration-200"
                                                                title="Hapus Umpan Balik"
                                                            >
                                                                <i class="fa-solid fa-trash text-[10px]"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endif

                                        </div>
                                    @endforeach
                                @endif
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    @endif

    {{-- Modal Feedback --}}
    @if($feedbackModal)
        <div 
            class="fixed inset-0 flex items-center justify-center bg-black/40 z-[60]"
            wire:click.self="closeFeedbackModal"
        >
            <div class="bg-white rounded-2xl shadow-3xl w-full max-w-md overflow-hidden">
                
                {{-- Header --}}
                <div class="bg-gradient-to-r from-purple-500 to-purple-600 px-4 py-2">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-1.5">
                            <div class="p-1 rounded-lg">
                                <i class="fa-solid fa-comment-dots text-white text-xs"></i>
                            </div>
                            <h2 class="text-white text-sm font-semibold">Berikan Umpan Balik</h2>
                        </div>
                        <button 
                            wire:click="closeFeedbackModal"
                            class="text-white/80 hover:text-white hover:bg-white/20 p-1 rounded-full transition-all duration-200"
                        >
                            <i class="fas fa-times text-xs"></i>
                        </button>
                    </div>
                </div>

                {{-- Content --}}
                <div class="p-4">
                    <label class="block text-gray-700 font-semibold mb-1.5 text-[10px] flex items-center gap-1">
                        <i class="fas fa-pen text-purple-500 text-[10px]"></i>
                        Tulis Umpan Balik Anda
                    </label>
                    <textarea
                        wire:model="feedbackText"
                        class="w-full border border-gray-200 rounded-lg px-2 py-1.5 bg-white focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500/10 transition-all duration-200 resize-none text-xs text-gray-800"
                        rows="10"
                        placeholder="Tambahkan umpan balik untuk catatan ini..."
                    ></textarea>

                    {{-- Actions --}}
                    <div class="flex justify-end gap-1.5 mt-3">
                        <button 
                            wire:click="closeFeedbackModal"
                            class="px-2 py-1.5 bg-gray-100 text-gray-700 rounded-lg text-[10px] font-semibold hover:bg-gray-200 transition-all duration-200"
                        >
                            Batal
                        </button>

                        <button 
                            wire:click="saveFeedback"
                            class="px-2 py-1.5 bg-gradient-to-r from-purple-500 to-purple-600 text-white rounded-lg text-[10px] font-semibold hover:shadow-lg hover:scale-[1.02] transition-all duration-200"
                        >
                            Simpan
                        </button>
                    </div>
                </div>

            </div>
        </div>
    @endif

    {{-- Custom Scrollbar Styles --}}
    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: linear-gradient(to bottom, #5ca9ff, #9c62ff);
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(to bottom, #449bff, #7c3eff);
        }
        
        input[type="date"] {
            color-scheme: light;
        }
    </style>

</div>