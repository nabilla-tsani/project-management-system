<div class="pt-0 p-2 space-y-2">
    {{-- Flash message --}}
        @if (session()->has('message'))
           <div 
                x-data="{ show: true }"
                x-init="setTimeout(() => show = false, 2000)" 
                x-show="show"
                x-transition.duration.500ms
                class="text-xs p-2 rounded bg-green-100 text-green-700 border border-green-300"
                style="position: relative; z-index: 10;"
            >
                {{ session('message') }}
            </div>
        @endif

        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-3 pt-1">
                <h2 class="text-md font-medium flex items-center gap-2 text-[#5ca9ff]">
                    <i class="fa-solid fa-layer-group"></i>
                    Feature List ({{ $fiturs->count() }})
                </h2>
                <input 
                    type="text"
                    wire:model.live="search" 
                    placeholder="Search feature..."
                    class="text-xs px-3 py-1.5 border border-gray-500 rounded-3xl focus:ring-[#5ca9ff] focus:border-[#5ca9ff] outline-none w-96"
                />

                <select wire:model.live="filterStatus"
                    class="text-xs border rounded-full px-7 py-1.5">
                    <option value="">All Status</option>
                    <option value="Upcoming">Upcoming</option>
                    <option value="Pending">Pending</option>
                    <option value="In Progress">In Progress</option>
                    <option value="Done">Done</option>
                </select>


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
                        class="bg-white border-y border-gray-200 shadow-sm hover:shadow-md transition duration-200 overflow-hidden transform hover:scale-[1.02]">
                        {{-- Baris pertama (header tabel) --}}
                        <div class="grid grid-cols-12 items-center px-3 pt-2 pb-1">
                            {{-- Nama Fitur --}}
                            <div wire:click="openCatatan({{ $fitur->id }})"
                                 class="col-span-5 text-[13px] font-medium text-gray-900 whitespace-normal break-words">
                                {{ $fitur->nama_fitur }}
                            </div>

                            {{-- Status --}}
                            <div wire:click="openCatatan({{ $fitur->id }})"
                                 class="col-span-1 flex items-center justify-center">
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
                            <div wire:click.stop="openUserFitur({{ $fitur->id }})"
                                class="col-span-5 text-xs text-gray-600 flex items-center gap-1">

                                <button wire:click.stop="openUserFitur({{ $fitur->id }})"
                                    class="text-[#5ca9ff] hover:text-[#3b7ed9] transition text-xs"
                                    title="Manage Users">
                                    <i class="fa-solid fa-user-plus text-[12px]"></i>
                                </button>

                                @php
                                    $countUser = $fitur->anggota->count();
                                    $listUser = $fitur->anggota->pluck('user.name')->implode(', ');
                                @endphp

                                @if($countUser)
                                    <span class="whitespace-normal break-words">
                                        <span class="font-semibold text-gray-700">({{ $countUser }})</span>
                                        {{ $listUser }}
                                    </span>
                                @else
                                    <span class="italic text-gray-400">No one’s on this feature yet.</span>
                                @endif
                            </div>


                            {{-- Aksi --}}
                            <div class="col-span-1 flex items-center justify-end gap-2">
                                <button wire:click.stop="openModal({{ $fitur->id }})"
                                    class="text-blue-500 hover:text-blue-800 transition text-xs"
                                    title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>

                                <button wire:click="confirmDelete({{ $fitur->id }})"
                                    class="text-red-500 hover:text-red-800 transition text-xs"
                                    title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>

                        {{-- Baris kedua (isi tabel) --}}
                        <div wire:click="openCatatan({{ $fitur->id }})"
                             class="grid grid-cols-12 items-start px-3 pb-2 text-xs">
                            {{-- Keterangan --}}
                            <div class="col-span-11 text-gray-700 flex items-center gap-2">

                            @php
                                $isOverdue = $fitur->target && \Carbon\Carbon::now()->gt(\Carbon\Carbon::parse($fitur->target));
                            @endphp

                            <span class="font-md flex items-center gap-1 {{ $isOverdue ? 'text-red-600 font-semibold' : '' }}">
                                
                                {{-- Icon Warning jika overdue --}}
                                @if ($isOverdue)
                                    <i class="fa-solid fa-triangle-exclamation text-red-600 text-xs"></i>
                                @endif

                                Goal Date: 
                                {{ \Carbon\Carbon::parse($fitur->target)->format('d M Y') }}
                            </span>

                            {{-- Notes --}}
                            <span class="ml-1 font-normal text-gray-600">
                                | Notes: {{ $fitur->keterangan ?? '-' }}
                            </span>

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
                            <div class="col-span-full text-center text-gray-400 italic bg-gray-50 rounded-lg p-4 border border-gray-200 text-xs">
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

                {{-- Footer Tombol Kembali --}}
                <div class="flex justify-start pt-4">
                    <a href="{{ route('proyek') }}"
                    class="px-4 py-2 bg-[#5ca9ff] text-white text-[10px] rounded-3xl shadow hover:bg-[#884fd9] transition">
                        Back to Project List
                    </a>
                </div>
                
    {{-- MODAL KONFIRMASI DELETE --}}   
        @if ($showConfirmDelete)
        <div class="fixed inset-0 flex items-center justify-center bg-black/40 backdrop-blur-sm z-50">
            <div class="bg-white shadow-2xl w-96 p-6 text-center animate-fadeIn">
                <h3 class="text-md font-semibold text-red-500 mb-4">Confirm Delete Feature</h3>
                <p class="text-gray-600 mb-5 text-sm">Are you sure you want to delete this feature? This action cannot be undone.</p>

                <div class="flex justify-center gap-3">
                    <button 
                        wire:click="$set('showConfirmDelete', false)"
                        class="px-4 py-2 rounded-3xl bg-gray-200 border border-gray-300 text-gray-700 text-xs hover:bg-gray-400 transition">
                        Cancel
                    </button>

                    <button 
                        wire:click="delete"
                        class="px-4 py-2 rounded-3xl bg-red-500 text-white text-xs hover:bg-red-600 transition">
                        Yes, Delete
                    </button>
                </div>
            </div>
        </div>
        @endif

    {{-- MODAL TAMBAH/EDIT --}}
        @if($modalOpen)
            <div class="fixed inset-0 flex items-center justify-center bg-black/40 backdrop-blur-sm z-50">
                <div class="bg-white shadow-2xl p-5 w-[30rem] max-w-[65%] border border-gray-200">
                    <h3 class="text-md font-medium text-[#9c62ff] mb-2 text-center">
                        {{ $fiturId ? 'Edit Feature' : 'New Feature' }}
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

                    <div class="flex items-center gap-3">
                    <div class="w-1/2 text-xs">
                        <label class="block text-xs font-semibold text-gray-600 py-2">Target</label>
                        <input type="date" wire:model.defer="target"
                            class="border border-gray-300 rounded-3xl px-3 w-full mb-1
                                focus:ring-2 focus:ring-blue-400 text-xs">

                    </div>
                    <div class="w-1/2 text-xs">
                        <label class="block text-xs font-semibold text-gray-600 py-2">Status</label>
                        <select wire:model.defer="status_fitur"
                            class="border aborder-gray-300 rounded-3xl px-3 py-2 w-full mb-1
                                focus:ring-2 focus:ring-blue-400 text-xs">
                            <option value="">-- Select Status --</option>
                            @foreach($statusList as $status)
                                <option value="{{ $status }}">{{ $status }}</option>
                            @endforeach
                        </select>
                    </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-3">
                        <button wire:click="closeModal"
                            class="bg-gray-200 text-gray-700 px-4 py-2 rounded-3xl
                                hover:bg-gray-300 hover:scale-105 transition text-xs">
                            Cancel
                        </button>
                        <button wire:click="save"
                            class="bg-[#5ca9ff] text-white px-4 py-2 shadow rounded-3xl
                                hover:scale-105 hover:bg-[#449bffff] transition text-xs">
                            {{ $fiturId ? 'Update' : 'Save' }}
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

                    <div wire:loading wire:target="generateFiturAI" class="text-xs text-[#9c62ff] italic">
                        AI's thinking, please wait...
                    </div>

                    <div class="flex justify-end gap-3">
                       <div class="flex justify-end gap-3">
                            <button wire:click="closeAiModal"
                                    type="button"
                                    wire:loading.attr="disabled"
                                    wire:target="generateFiturAI"
                                    class="bg-gray-200 text-gray-700 px-4 py-1.5 rounded-3xl text-xs transition hover:scale-105
                                        disabled:bg-gray-300 disabled:text-gray-500 disabled:opacity-50 disabled:cursor-not-allowed">
                                Cancel
                            </button>
                            <button wire:click="generateFiturAI"
                                    wire:loading.attr="disabled"
                                    wire:target="generateFiturAI"
                                    class="px-4 py-1.5 rounded-3xl text-xs transition text-white bg-[#5ca9ff] hover:bg-[#449bffff] hover:scale-105
                                        disabled:bg-gray-300 disabled:text-gray-500 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100 disabled:hover:bg-gray-300">
                                Generate
                            </button>
                        </div>

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
                        <textarea 
                            wire:model="revisi_deskripsi_ai"
                            x-data
                            x-ref="rev"
                            @clear-revisi.window="$refs.rev.value = ''"
                            rows="3"
                            placeholder="Example: add a search feature..."
                            class="text-xs border border-gray-300 rounded-lg p-2 w-full text-sm"
                        ></textarea>
                    </div>

                    <div>
                        <div class="flex justify-between items-center">
                            <label class="text-xs font-semibold text-gray-600 pb-2">Regeneration Feature Count</label>
                            <span class="text-[11px] text-gray-500 italic">May differ from the initial request</span>
                        </div>
                        <input type="number" min="1" max="10" wire:model="jumlah_fitur_revisi"
                            placeholder="{{ $jumlah_fitur_ai }}" oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                            class="text-xs border border-gray-300 rounded-3xl py-2 px-3 w-full text-sm focus:ring-2 focus:ring-indigo-400">
                    </div>
                </div>

                {{-- Loading indikator --}}
                <div wire:loading wire:target="regenerateAiFitur" class="mt-3 text-xs text-[#9c62ff] italic">
                    Requesting AI to create new features…
                </div>

                {{-- Tombol aksi --}}
                <div class="flex justify-end gap-3">
                    <button wire:click="$set('showAiReview', false)"
                            type="button"
                            wire:loading.attr="disabled"
                            wire:target="regenerateAiFitur"
                            class="bg-gray-200 text-gray-700 px-4 py-1.5 rounded-3xl text-xs transition
                                disabled:bg-gray-300 disabled:text-gray-500 disabled:opacity-50 disabled:cursor-not-allowed">
                        Cancel
                    </button>

                    <button wire:click="regenerateAiFitur"
                            wire:loading.attr="disabled"
                            wire:target="regenerateAiFitur"
                            class="bg-[#9c62ff] text-white px-4 py-1.5 rounded-3xl hover:bg-purple-700 hover:scale-105 hover:bg-[#8a48fa] transition text-xs
                                disabled:bg-gray-300 disabled:text-gray-500 disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fa-solid fa-arrows-rotate"></i> Regenerate
                    </button>

                    <button wire:click="approveAiFitur"
                            wire:loading.attr="disabled"
                            wire:target="regenerateAiFitur"
                            class="bg-[#5ca9ff] text-white px-4 py-1.5 rounded-3xl hover:bg-indigo-700 hover:scale-105 hover:bg-[#449bffff] transition text-xs
                                disabled:bg-gray-300 disabled:text-gray-500 disabled:opacity-50 disabled:cursor-not-allowed">
                        Agree then Add
                    </button>
                </div>

            </div>
        </div>
        
    @endif


@livewire('catatan-pekerjaan', ['proyekId' => $proyekId])
@livewire('all-fitur-user')

</div>
