<div>
@if($modalOpen)
    <div 
        class="fixed inset-0 flex items-center justify-center bg-gradient-to-br from-black/60 to-black/40 backdrop-blur-md z-50 p-4"
        wire:click.self="closeModal"
    >

        {{-- ======================= TAMPILAN MANAJER (Bisa Tambah/Edit) ======================= --}}
        @if($userRole === 'manajer proyek')
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl max-h-[85vh] overflow-hidden"
            wire:key="modal-{{ $proyekFiturId ?? 'new' }}">
                
                {{-- Header Modal --}}
                <div class="bg-gradient-to-r from-[#5ca9ff] to-[#9c62ff] pl-4 pr-3 py-2">
                    <div class="flex items-center justify-between">
                        
                        <div class="flex items-center gap-2">
                            <div class="flex items-center justify-center bg-white/20 backdrop-blur-sm w-7 h-7 rounded-lg">
                                <i class="fas fa-layer-group text-white text-xs leading-none"></i>
                            </div>

                            <h3 class="text-white text-sm font-semibold leading-none">
                                {{ $namaFitur ?? '-' }}
                            </h3>
                        </div>

                        <button 
                            wire:click="closeModal"
                            class="group flex items-center justify-center w-7 h-7
                                text-white/70
                                rounded-full
                                transition-all duration-200
                                hover:text-white
                                hover:bg-white/20
                                hover:scale-105"
                        >
                            <i class="fas fa-times text-sm leading-none"></i>
                        </button>

                    </div>
                </div>


                <div class="flex gap-4 p-2">
                    {{-- ======================= KOLOM KIRI - DAFTAR ANGGOTA ======================= --}}
                    <div class="w-3/5">
                        <div class="bg-gradient-to-br from-gray-50 to-white rounded-xl border border-gray-200 p-2 h-full">
                            
                            {{-- Header Daftar --}}
                           <div class="flex items-center justify-between mb-2 px-1">
                                <div class="flex items-center gap-2">
                                    <div class="flex items-center justify-center w-6 h-6 bg-gradient-to-br from-[#5ca9ff] to-[#449bff] rounded-md">
                                        <i class="fas fa-users text-white text-[10px] leading-none"></i>
                                    </div>
                                    <h3 class="text-xs font-semibold text-gray-800">
                                        Daftar Anggota
                                    </h3>
                                </div>

                                <span class="bg-[#5ca9ff]/10 text-[#5ca9ff] px-2.5 py-0.5 rounded-full text-[10px] font-semibold">
                                    {{ $fiturUsers->count() }} Anggota
                                </span>
                            </div>

                            {{-- Pesan Flash --}}
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
                                    class="mb-3 text-xs text-green-700 bg-gradient-to-r from-green-50 to-green-100 px-3 py-2 rounded-lg shadow-sm border border-green-200 flex items-center gap-2"
                                >
                                    <i class="fas fa-check-circle"></i>
                                    {{ session('message') }}
                                </div>
                            @endif

                            {{-- Daftar Anggota --}}
                            <div class="h-[420px] overflow-y-auto pr-2 space-y-1 custom-scrollbar">
                                @if($fiturUsers->isNotEmpty())
                                    @foreach($fiturUsers as $index => $fu)
                                        <div 
                                            wire:key="fu-{{ $fu->id }}" 
                                            class="bg-white rounded-lg p-2 shadow-sm hover:shadow-md transition-all duration-200 border border-gray-100 group"
                                        >
                                            <div class="flex items-start gap-3">
                                                {{-- Nomor --}}
                                                <div class="flex-shrink-0 w-6 h-6 bg-gradient-to-br from-[#5ca9ff] to-[#449bff] rounded-lg flex items-center justify-center">
                                                    <span class="text-white font-bold text-xs">{{ $index + 1 }}</span>
                                                </div>
                                                
                                                {{-- Info Anggota --}}
                                                <div class="flex-1 min-w-0">
                                                    <div class="flex items-center gap-1.5 ">
                                                        <i class="fas fa-user text-gray-400 text-[10px]"></i>
                                                        <h4 class="font-semibold text-gray-800 text-[10px]">{{ $fu->user?->name ?? '-' }}</h4>
                                                    </div>
                                                    <div class="flex items-start gap-1.5">
                                                        <i class="fas fa-sticky-note text-gray-400 text-[10px] mt-0.5"></i>
                                                        <p class="text-gray-600 text-[9px] text-justify leading-relaxed" style="text-align:justify">
                                                            {{ $fu->keterangan ?? 'Tidak ada catatan' }}
                                                        </p>
                                                    </div>
                                                </div>

                                                {{-- Tombol Aksi --}}
                                                <div class="flex-shrink-0 flex items-center gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                                    <button 
                                                        wire:click="edit({{ $fu->id }})" 
                                                        class="text-blue-600 hover:scale-150 transition-colors duration-200" 
                                                        title="Edit"
                                                    >
                                                        <i class="fa-solid fa-pen-to-square text-[9px]"></i>
                                                    </button>
                                                    <button 
                                                        wire:click="delete({{ $fu->id }})" 
                                                        class="text-red-600 hover:scale-150 transition-colors duration-200" 
                                                        title="Hapus"
                                                    >
                                                        <i class="fa-solid fa-trash text-[9px]"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="h-full flex flex-col items-center justify-center text-center py-8">
                                        <div class="bg-gray-100 rounded-full p-4 mb-3">
                                            <i class="fas fa-users text-gray-400 text-2xl"></i>
                                        </div>
                                        <p class="text-gray-400 font-medium text-xs">Belum ada anggota di fitur ini</p>
                                        <p class="text-gray-400 text-[11px] mt-1">Tambahkan anggota pertama Anda →</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- ======================= KOLOM KANAN - FORM ======================= --}}
                    <div class="w-2/5">
                        <div class="bg-gradient-to-br from-purple-50 to-white rounded-xl border border-purple-100 p-4 sticky top-0">
                            
                            {{-- Header Form --}}
                            <div class="flex items-center gap-2 mb-2">
                                <div class="flex items-center justify-center w-6 h-6 bg-gradient-to-br from-[#9c62ff] to-[#7c3eff] rounded-md">
                                    <i class="fas fa-user-plus text-white text-[10px] leading-none"></i>
                                </div>
                                <h3 class="text-xs font-semibold text-gray-800">
                                    {{ $fiturUserId ? 'Edit Anggota' : 'Tambah Anggota' }}
                                </h3>
                            </div>

                            <form 
                                wire:submit.prevent="save" 
                                wire:key="{{ $formKey }}"  
                                class="space-y-3"
                            >

                                {{-- Pilih Anggota --}}
                                <div>
                                    <label class="block text-gray-700 font-semibold mb-1.5 mt-5 text-[10px] flex items-center gap-1.5">
                                        <i class="fas fa-user text-[#9c62ff] text-[10px]"></i>
                                        Pilih Anggota
                                    </label>
                                    <div class="relative">
                                        <select 
                                            wire:model="user_id"
                                            wire:key="user-select-{{ $isEdit ? $fiturUserId : 'create' }}"
                                            class="w-full border-2 border-gray-200 rounded-lg px-3 py-2 bg-white focus:outline-none focus:border-[#9c62ff] focus:ring-2 focus:ring-[#9c62ff]/10 transition-all duration-200 appearance-none text-[10px]"
                                        >
                                            <option value="">-- Pilih Anggota --</option>
                                            @foreach($userList as $user)
                                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                        <i class="fas fa-chevron-down absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 pointer-events-none text-xs"></i>
                                    </div>
                                    @error('user_id')
                                        <span class="text-red-500 text-[10px] mt-1 flex items-center gap-1">
                                            <i class="fas fa-exclamation-circle"></i>
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>

                                {{-- Catatan --}}
                                <div>
                                    <label class="block text-gray-700 font-semibold mb-1.5 text-[10px] flex items-center gap-1.5">
                                        <i class="fas fa-sticky-note text-[#9c62ff] text-[10px]"></i>
                                        Catatan
                                    </label>
                                    <textarea 
                                        wire:model="keterangan"
                                        wire:key="ket-{{ $isEdit ? $fiturUserId : 'create' }}"
                                        rows="6"
                                        class="w-full border-2 border-gray-200 rounded-lg px-3 py-2 bg-white focus:outline-none focus:border-[#9c62ff] focus:ring-2 focus:ring-[#9c62ff]/10 transition-all duration-200 resize-none text-[10px]"
                                        placeholder="Tambahkan catatan untuk anggota ini..."
                                    ></textarea>
                                    @error('keterangan')
                                        <span class="text-red-500 text-[10px] mt-1 flex items-center gap-1">
                                            <i class="fas fa-exclamation-circle"></i>
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>

                                {{-- Tombol Aksi --}}
                                <div class="flex gap-2 pt-1">
                                    @if($fiturUserId)
                                        <button 
                                            type="button" 
                                            wire:click="cancelEdit"
                                            class="flex-1 px-3 py-2 bg-gray-100 text-gray-700 rounded-lg text-xs font-semibold hover:bg-gray-200 transition-all duration-200 flex items-center justify-center gap-1.5"
                                        >
                                            <i class="fas fa-times text-[10px]"></i>
                                            Batal
                                        </button>
                                    @endif

                                    <button 
                                        type="submit"
                                        class="flex-1 px-3 py-2 bg-gradient-to-r from-[#9c62ff] to-[#7c3eff] text-white rounded-lg text-[10px] font-semibold hover:shadow-lg hover:scale-[1.02] transition-all duration-200 flex items-center justify-center gap-1.5"
                                    >
                                        <i class="fas fa-{{ $fiturUserId ? 'check' : 'save' }} text-[9px]"></i>
                                        {{ $fiturUserId ? 'Perbarui' : 'Simpan' }}
                                    </button>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>

            </div>

        {{-- ======================= TAMPILAN NON-MANAJER (Hanya Baca) ======================= --}}
        @else
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[85vh] overflow-hidden wire:key="modal-{{ $proyekFiturId ?? 'new' }}">
                
                {{-- HEADER MODAL --}}
                    <div class="bg-gradient-to-r from-[#5ca9ff] to-[#9c62ff] pl-4 pr-3 py-2">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <div class="flex items-center justify-center w-6 h-6 bg-white/20 backdrop-blur-sm rounded-md">
                                    <i class="fas fa-layer-group text-white text-[10px] leading-none"></i>
                                </div>
                                <div class="leading-none">
                                    <h3 class="text-white text-sm font-semibold leading-none">
                                        {{ $namaFitur ?? '-' }}
                                    </h3>
                                </div>
                            </div>

                            <button 
                                wire:click="closeModal"
                                class="group flex items-center justify-center w-7 h-7
                                    text-white/70
                                    rounded-full
                                    transition-all duration-200
                                    hover:text-white
                                    hover:bg-white/20
                                    hover:scale-105"
                            >
                                <i class="fas fa-times text-sm leading-none"></i>
                            </button>
                        </div>
                    </div>

                    {{-- BODY --}}
                    <div class="p-3">
                        <div class="bg-gradient-to-br from-gray-50 to-white rounded-xl border border-gray-200 p-2">

                            {{-- HEADER DAFTAR --}}
                            <div class="flex items-center justify-between mb-2 px-1">
                                <div class="flex items-center gap-2">
                                    <div class="flex items-center justify-center w-6 h-6 bg-gradient-to-br from-[#5ca9ff] to-[#449bff] rounded-md">
                                        <i class="fas fa-users text-white text-[10px] leading-none"></i>
                                    </div>
                                    <h3 class="text-xs font-semibold text-gray-800">
                                        Daftar Anggota
                                    </h3>
                                </div>

                                <span class="bg-[#5ca9ff]/10 text-[#5ca9ff] px-2.5 py-0.5 rounded-full text-[10px] font-semibold">
                                    {{ $fiturUsers->count() }} Anggota
                                </span>
                            </div>

                            {{-- PESAN FLASH --}}
                            @if (session()->has('message'))
                                <div 
                                    x-data="{ show: true }"
                                    x-init="setTimeout(() => show = false, 1000)"
                                    x-show="show"
                                    class="mb-3 text-[10px] text-green-700 bg-gradient-to-r from-green-50 to-green-100 px-3 py-2 rounded-lg border border-green-200 flex items-center gap-2"
                                >
                                    <i class="fas fa-check-circle"></i>
                                    {{ session('message') }}
                                </div>
                            @endif

                            {{-- DAFTAR ANGGOTA --}}
                            <div class="h-[420px] overflow-y-auto pr-2 space-y-1 custom-scrollbar">
                                @if($fiturUsers->isNotEmpty())
                                    @foreach($fiturUsers as $index => $fu)
                                        <div 
                                            wire:key="fu-{{ $fu->id }}" 
                                            class="bg-white rounded-lg p-2 shadow-sm hover:shadow-md transition border border-gray-100 group"
                                        >
                                            <div class="flex items-start gap-3">
                                                
                                                {{-- Nomor --}}
                                                <div class="flex-shrink-0 w-6 h-6 bg-gradient-to-br from-[#5ca9ff] to-[#449bff] rounded-lg flex items-center justify-center">
                                                    <span class="text-white font-bold text-xs">
                                                        {{ $index + 1 }}
                                                    </span>
                                                </div>

                                                {{-- Info --}}
                                                <div class="flex-1 min-w-0">
                                                    <div class="flex items-center gap-1.5">
                                                        <i class="fas fa-user text-gray-400 text-[10px]"></i>
                                                        <h4 class="font-semibold text-gray-800 text-[10px]">
                                                            {{ $fu->user?->name ?? '-' }}
                                                        </h4>
                                                    </div>

                                                    <div class="flex items-start gap-1.5">
                                                        <i class="fas fa-sticky-note text-gray-400 text-[10px] mt-0.5"></i>
                                                        <p class="text-gray-600 text-[9px] leading-relaxed text-justify">
                                                            {{ $fu->keterangan ?? 'Tidak ada catatan' }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="h-full flex flex-col items-center justify-center text-center py-8">
                                        <div class="bg-gray-100 rounded-full p-4 mb-3">
                                            <i class="fas fa-users text-gray-400 text-2xl"></i>
                                        </div>
                                        <p class="text-gray-400 font-medium text-xs">
                                            Belum ada anggota di fitur ini
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

        @endif

    </div>

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
    </style>
@endif

{{-- Reload listener --}}
<script>
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('reloadPage', () => {
            location.reload();
        });
    });
</script>
</div>