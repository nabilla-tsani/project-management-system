<div class="pt-0 p-2 space-y-2">
    {{-- Flash message --}}
    @if (session()->has('message'))
        <div class="mb-3 text-sm text-green-600 bg-green-100 p-2 rounded">
            {{ session('message') }}
        </div>
    @endif

<div class="flex items-center justify-between mb-4">
    <div class="flex items-center gap-3">
        <h2 class="text-lg font-medium flex items-center gap-2 text-[#5ca9ff]">
            Feature List
            ({{ $fiturs->count() }})
        </h2>
        <input 
            type="text"
            wire:model.live="search" 
            placeholder="Search feature..."
            class="text-sm px-3 py-1.5 border border-gray-300 rounded-3xl focus:ring-[#5ca9ff] focus:border-[#5ca9ff] outline-none w-96"
        />
    </div>

    <div class="flex items-center gap-3">
        <button wire:click="openAiModal"
            class="px-3 py-1.5 bg-gradient-to-r from-cyan-400 to-purple-600 text-white rounded-3xl shadow hover:bg-indigo-700 
                    hover:shadow-md transition-all duration-200 text-xs">
            <i class="fa-solid fa-wand-magic-sparkles"></i> Create Features with AI
        </button>

        <button wire:click="openModal"
            class="px-4 py-1.5 rounded-3xl text-white shadow hover:shadow-md transition-all duration-200 text-xs"
            style="background-color: #5ca9ff;">
            <i class="fa-solid fa-plus mr-1"></i> New Feature
        </button>
    </div>
</div>

    
            {{-- List fitur --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-1">
                @forelse($fiturs as $fitur)
                    <div 
                        wire:click="openCatatan({{ $fitur->id }})"
                        class="bg-white border-y border-gray-200 shadow-sm hover:shadow-md hover:bg-[#f2eaffff] transition duration-200 overflow-hidden"
                    >
                        {{-- Baris pertama (header tabel) --}}
                        <div class="grid grid-cols-12 items-center px-3 pt-2 pb-1">
                            {{-- Nama Fitur --}}
                            <div class="col-span-6 text-[13px] font-medium text-gray-900 whitespace-normal break-words">
                                {{ $fitur->nama_fitur }}
                            </div>

                            {{-- Status --}}
                            <div class="col-span-1 flex items-center justify-center">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold
                                    @if($fitur->status_fitur === 'Done')
                                        text-[#5ca9ff] bg-[#dfeeff]
                                    @elseif($fitur->status_fitur === 'In Progress')
                                        text-orange-600 bg-orange-100
                                    @elseif($fitur->status_fitur === 'Pending')
                                        text-red-600 bg-red-100
                                    @else
                                        text-gray-600 bg-gray-200
                                    @endif">
                                    {{ ucfirst($fitur->status_fitur) }}
                                </span>
                            </div>
                            {{-- Anggota --}}
                            <div class="col-span-4 text-xs text-gray-600 flex items-center gap-1">
                                <button wire:click.stop="openUserModal({{ $fitur->id }})"
                                    class="text-[#5ca9ff] hover:text-[#3b7ed9] transition text-xs"
                                    title="Tambah / Kelola User">
                                    <i class="fa-solid fa-user-plus text-[12px]"></i>
                                </button>

                                @if($fitur->anggota->count())
                                    <span class="whitespace-normal break-words">
                                        {{ $fitur->anggota->pluck('user.name')->implode(', ') }}
                                    </span>
                                @else
                                    <span class="italic text-gray-400">No one’s on this feature yet.</span>
                                @endif
                            </div>

                            {{-- Aksi --}}
                            <div class="col-span-1 flex items-center justify-end gap-2">
                                <button wire:click.stop="openModal({{ $fitur->id }})"
                                    class="text-blue-600 hover:text-blue-800 transition text-xs"
                                    title="Edit">
                                    <i class="fas fa-edit text-[13px]"></i>
                                </button>

                                <button wire:click.stop="delete({{ $fitur->id }})"
                                    class="text-red-600 hover:text-red-800 transition text-xs"
                                    title="Hapus">
                                    <i class="fas fa-trash text-[13px]"></i>
                                </button>
                            </div>
                        </div>

                        {{-- Baris kedua (isi tabel) --}}
                        <div class="grid grid-cols-12 items-start px-3 pb-2 text-xs">
                            {{-- Keterangan --}}
                            <div class="col-span-11 text-gray-700">
                                {{ $fitur->keterangan ?? '—' }}
                            </div>

                            {{-- Notes --}}
                            <div class="col-span-1 flex justify-end">
                                <button wire:click.stop="openCatatan({{ $fitur->id }})"
                                    class="text-gray-500 hover:text-gray-700 flex items-center gap-1">
                                    <i class="fa-solid fa-note-sticky text-[13px]"></i>
                                    Notes
                                </button>
                            </div>
                        </div>
                    </div>
                         @empty
                        {{-- Pesan jika hasil pencarian kosong --}}
                        @if (!empty($search))
                            <div class="col-span-full text-center text-gray-400 italic bg-gray-50 rounded-lg p-4 border border-gray-200 text-sm">
                                No matching features found for 
                                <span class="font-semibold text-[#5ca9ff]">"{{ $search }}"</span>.
                            </div>
                        @else
                            {{-- Pesan default jika belum ada fitur --}}
                            <div class="col-span-full text-center text-gray-500 bg-gray-50 rounded-lg p-3 border border-gray-200 text-xs">
                                No features added yet.
                            </div>
                        @endif
                    @endforelse
                </div>

    {{-- MODAL TAMBAH/EDIT --}}
        @if($modalOpen)
            <div class="fixed inset-0 flex items-center justify-center bg-black/40 backdrop-blur-sm z-50">
                <div class="bg-white shadow-2xl p-5 w-[30rem] max-w-[65%] border border-gray-200">
                    <h3 class="text-md font-medium text-[#9c62ff] mb-2 text-center">
                        {{ $fiturId ? 'Feature Update' : 'New Feature' }}
                    </h3>

                    <label class="block text-xs font-semibold text-gray-600 py-2">Name</label>
                    <input type="text" wire:model.defer="nama_fitur"
                        class="border border-gray-300 rounded-3xl px-3 w-full mb-1
                            focus:ring-2 focus:ring-blue-400 text-xs">

                    <label class="block text-xs font-semibold text-gray-600 py-2">Description</label>
                    <textarea wire:model.defer="keterangan"
                        rows="6"
                        class="border border-gray-300 rounded-xl px-3 py-2 w-full 
                            focus:ring-2 focus:ring-blue-400 text-xs resize-y"></textarea>

                    <label class="block text-xs font-semibold text-gray-600 py-2">Status</label>
                    <select wire:model.defer="status_fitur"
                        class="border border-gray-300 rounded-3xl px-3 py-2 w-full mb-3
                            focus:ring-2 focus:ring-blue-400 text-xs">
                        <option value="">-- Select Status --</option>
                        @foreach($statusList as $status)
                            <option value="{{ $status }}">{{ $status }}</option>
                        @endforeach
                    </select>

                    <div class="flex justify-end gap-3">
                        <button wire:click="closeModal"
                            class="bg-gray-200 text-gray-700 px-4 py-2 rounded-3xl
                                hover:bg-gray-300 hover:scale-105 transition text-xs">
                            Cancel
                        </button>
                        <button wire:click="save"
                            class="bg-[#5ca9ff] text-white px-4 py-2 shadow rounded-3xl
                                hover:scale-105 hover:bg-[#449bffff] transition text-xs">
                            Save
                        </button>
                    </div>
                </div>
            </div>
        @endif


    {{-- MODAL USER --}}
    @if($userModalOpen)
        <div class="fixed inset-0 flex items-center justify-center bg-black/40 backdrop-blur-sm z-50 p-4">
            <div class="bg-white shadow-2xl w-full max-w-4xl max-h-screen overflow-y-auto rounded-lg border border-gray-200 p-5">
                
                {{-- Header --}}
                <h3 class="text-lg font-bold text-gray-800 mb-4 text-center flex items-center justify-center gap-2">
                    <i class="fas fa-users text-blue-600"></i>
                    User Fitur {{ $selectedFitur?->nama_fitur }}
                </h3>

                {{-- Konten --}}
                @livewire('all-fitur-user', ['proyekFiturId' => $selectedFiturId], key('fitur-user-'.$selectedFiturId))

                {{-- Footer --}}
                <div class="flex justify-end mt-4">
                    <button wire:click="closeUserModal"
                        class="bg-gray-200 text-gray-700 px-3 py-1.5 rounded-lg hover:bg-gray-300 hover:scale-105 transition text-sm">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    @endif


    {{-- MODAL TAMBAH FITUR WITH AI--}}
    @if($aiModalOpen)
        <div class="fixed inset-0 flex items-center justify-center bg-black/40 backdrop-blur-sm z-50 p-4">
            <div class="bg-white shadow-2xl w-[36rem] max-w-full border border-gray-200 p-5 relative">
                <div class="flex justify-center items-center mb-4">
                    <h3 class="text-md font-medium text-[#9c62ff] flex items-center gap-2">
                        <i class="fa-solid fa-robot text-indigo-600"></i>
                        Create Features with AI
                    </h3>
                </div>


                <div wire:loading wire:target="generateFiturAI" class="mb-3 text-sm text-gray-600 italic">
                    AI’s thinking, please wait...
                </div>

                <div class="space-y-3">
                    <div>
                        <div class="flex justify-between items-center">
                            <label class="text-xs font-semibold text-gray-600 py-2">Set your desired feature count</label>
                            <span class="text-[11px] text-gray-500 italic">Max. 10</span>
                        </div>
                        <input type="number" min="1" max="10" wire:model.defer="jumlah_fitur_ai"
                               oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                               class="border border-gray-300 rounded-3xl px-3 py-2 w-full
                                        focus:ring-2 focus:ring-blue-400 text-xs">
                    </div>

                    <div>
                    <label class="block text-xs font-semibold text-gray-600 py-2">Prompt for AI</label>
                        <textarea wire:model.defer="deskripsi_ai" rows="6"
                                  placeholder="Examples: focus on authentication, admin reports, and payment integration"
                                  class="border border-gray-300 rounded-xl p-2 w-full
                                        focus:ring-2 focus:ring-blue-400 text-xs">></textarea>
                    </div>

                    <div class="flex justify-end gap-3 pt-2">
                        <button wire:click="closeAiModal"
                                class="bg-gray-200 text-gray-700 px-4 py-1.5 rounded-3xl hover:bg-gray-300 text-xs transition">
                            Cancel
                        </button>
                        <button wire:click="generateFiturAI"
                                class="bg-[#5ca9ff] text-white px-4 py-1.5 rounded-3xl hover:bg-[#449bffff] hover:scale-105 transition text-xs">
                            Generate
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- MODAL REVIEW AI --}}
    @if($showAiReview)
        <div class="fixed inset-0 flex items-center justify-center bg-black/40 backdrop-blur-sm z-50 p-4">
            <div class="bg-white shadow-2xl w-[36rem] max-w-full rounded-lg border border-gray-200 p-5">
                <div class="flex justify-center items-center mb-4">
                    <h3 class="text-md font-medium text-[#9c62ff] flex items-center gap-2">
                        <i class="fa-solid fa-robot text-indigo-600"></i>
                        AI Features Overview
                    </h3>
                </div>


                {{-- Daftar fitur hasil AI --}}
                <ul class="space-y-2 mb-4 max-h-60 overflow-y-auto border border-gray-200 p-2 rounded">
                    @foreach($aiFiturList as $fitur)
                        <li class="p-1 text-xs border border-gray-200 rounded text-gray-700 bg-gray-50">{{ $fitur }}</li>
                    @endforeach
                </ul>

                {{-- Kolom revisi prompt --}}
                <div class="mb-3 space-y-3">
                    <div>
                        <label class="text-xs font-semibold text-gray-600 pb-2 block">Improve AI Prompt</label>
                        <textarea wire:model.defer="revisi_deskripsi_ai" rows="3"
                                placeholder="Example: add a search feature, remove the reports feature ..."
                                class="text-xs border border-gray-300 rounded-lg  p-2 w-full text-sm resize-y focus:ring-2 focus:ring-indigo-400"></textarea>
                    </div>

                    <div>
                        <div class="flex justify-between items-center">
                            <label class="text-xs font-semibold text-gray-600 pb-2">Regeneration Feature Count</label>
                            <span class="text-[11px] text-gray-500 italic">May differ from the initial request</span>
                        </div>
                        <input type="number" min="1" max="10" wire:model.defer="jumlah_fitur_revisi"
                            placeholder="{{ $jumlah_fitur_ai }}" oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                            class="text-xs border border-gray-300 rounded-3xl py-2 px-3 w-full text-sm focus:ring-2 focus:ring-indigo-400">
                    </div>
                </div>


                {{-- Tombol aksi --}}
                <div class="flex justify-end gap-3">
                    <button wire:click="$set('showAiReview', false)"
                            class="bg-gray-200 text-gray-700 px-4 py-1.5 rounded-3xl hover:bg-gray-300 text-xs">
                        Cancel
                    </button>
                    <button wire:click="regenerateAiFitur"
                            class="bg-[#9c62ff] text-white px-4 py-1.5 rounded-3xl hover:bg-purple-700 hover:scale-105 hover:bg-[#8a48fa] transition text-xs">
                        <i class="fa-solid fa-arrows-rotate"></i> Regenerate
                    </button>
                    <button wire:click="approveAiFitur"
                            class="bg-[#5ca9ff] text-white px-4 py-1.5 rounded-3xl hover:bg-indigo-700 hover:scale-105 hover:bg-[#449bffff] transition text-xs">
                        Agree then Add
                    </button>
                </div>

                {{-- Loading indikator --}}
                <div wire:loading wire:target="regenerateAiFitur" class="mt-3 text-sm text-gray-600 italic">
                    Requesting AI to create new features…
                </div>
            </div>
        </div>
        
    @endif

@livewire('catatan-pekerjaan')


</div>
