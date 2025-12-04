<div class="pt-0 p-2 space-y-2">

        {{-- Header: Judul & Tombol Tambah --}}
    <div class="flex items-center justify-between mb-2">
        <div class="flex items-center gap-3">
            <h2 class="text-md font-medium flex items-center gap-2 text-[#5ca9ff]">
                <i class="fa-solid fa-list-check"></i>
                Tasks List
            </h2>
            <input 
                type="text"
                wire:model.live="search" 
                placeholder="Find tasks by features and users..."
                class="text-xs px-3 py-1.5 border border-gray-500 rounded-3xl focus:ring-[#5ca9ff] focus:border-[#5ca9ff] outline-none w-96"
            />
        </div>

        <div class="flex items-center gap-3">
            <button wire:click="showModal" onclick="console.log('tombol diklik')"
                class="px-4 py-1.5 rounded-3xl text-white shadow hover:shadow-md transition-all duration-200 text-xs"
                style="background-color: #5ca9ff;">
            <i class="fa-solid fa-pen pr-2"></i> Create General Task
            </button>
        </div>
    </div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">

    {{-- LEFT COLUMN: feature task --}}
    <div>
        <h3 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
            <i class="fa-solid fa-layer-group"></i> Feature Notes
        </h3>

        @forelse($catatanFitur as $item)
            <div class="p-3 shadow-sm border border-gray-200 bg-white hover:shadow-md transition rounded-lg mb-2">

                {{-- Top Row --}}
                <div class="flex justify-between items-center">
                    @if ($item->jenis === 'pekerjaan')
                        <span class="text-[10px] font-semibold px-2 py-1 rounded-full bg-blue-50 text-[#5ca9ff]">
                    @else
                        <span class="text-[10px] font-semibold px-2 py-1 rounded-full bg-purple-100 text-[#9c62ff]">
                    @endif
                            @if ($item->jenis === 'pekerjaan')
                            Task - {{ $item->fitur->nama_fitur ?? '-' }}
                            @else
                                Bug - {{ $item->fitur->nama_fitur ?? '-' }}
                            @endif
                        </span>

                    <div class="text-[10px] italic text-gray-400">
                        Report from: {{ $item->user->name ?? '-' }}
                        <span class="mx-1">•</span>
                        <span>
                            {{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('d M Y') }}
                            -
                            @if ($item->tanggal_selesai)
                                {{ \Carbon\Carbon::parse($item->tanggal_selesai)->format('d M Y') }}
                            @elseif ($item->proyek?->tanggal_selesai)
                                {{ \Carbon\Carbon::parse($item->proyek->tanggal_selesai)->format('d M Y') }}
                            @else
                                Project done
                            @endif
                        </span>

                    </div>
                </div>

                <p class="text-xs text-gray-600 mt-2 leading-relaxed">
                    {{ $item->catatan }}
                </p>
            </div>
        @empty
            <p class="text-xs text-gray-400 italic">No feature notes yet.</p>
        @endforelse
    </div>
        
    {{-- RIGHT COLUMN: general task --}}
    <div>
       <h3 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
            <i class="fa-solid fa-note-sticky"></i> General Tasks
        </h3>

        @if (session()->has('success'))
            <div 
                x-data="{ show: true }"
                x-init="setTimeout(() => show = false, 1000)"
                x-show="show"
                x-transition.duration.500ms
                class="text-xs p-2 rounded bg-green-100 text-green-700 border border-green-300"
            >
                {{ session('success') }}
            </div>
        @endif

        @forelse($catatanNonFitur as $item)
            <div class="p-3 shadow-sm border border-gray-200 bg-gradient-to-br 
                from-white to-blue-50 hover:shadow-md transition rounded-lg mt-2">

                <div class="flex justify-between items-center">
                    <div class="text-[10px] italic text-gray-400">
                        Assign to: {{ $item->user->name ?? '-' }}
                        <span class="mx-1">•</span>
                        <span>
                            {{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('d M Y') }}
                            -
                            @if ($item->tanggal_selesai)
                                {{ \Carbon\Carbon::parse($item->tanggal_selesai)->format('d M Y') }}
                            @elseif ($item->proyek?->tanggal_selesai)
                                {{ \Carbon\Carbon::parse($item->proyek->tanggal_selesai)->format('d M Y') }}
                            @else
                                Project done
                            @endif
                        </span>
                    </div>

                    <div class="flex items-center gap-3 text-[10px] text-gray-500">

                        <button 
                            class="hover:text-blue-500 transition"
                            wire:click="edit({{ $item->id }})"
                        >
                            <i class="fa-solid fa-pen"></i>
                        </button>
                        <button 
                            class="hover:text-red-500 transition"
                            wire:click="delete({{ $item->id }})"
                        >
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
                </div>


                <p class="text-xs text-gray-600 mt-2 leading-relaxed">
                    {{ $item->catatan }}
                </p>
            </div>
        @empty
            <p class="text-xs text-gray-400 italic">No general tasks yet.</p>
        @endforelse
    </div>
</div>

    {{-- Footer Tombol Kembali --}}
    <div class="flex justify-start pt-4">
        <a href="{{ route('proyek') }}"
           class="px-4 py-2 bg-[#5ca9ff] text-white text-[10px] rounded-3xl shadow hover:bg-[#884fd9] transition">
            Back to Project List
        </a>
    </div>



    {{-- MODAL TAMBAH CATATAN --}}
    @if($openModal)
            <div class="fixed inset-0 flex items-center justify-center bg-black/40 backdrop-blur-sm z-50">
        <div class="bg-white shadow-xl w-[500px] p-5">

            <div class="text-center">
                <h3 class="text-sm font-semibold mb-3 text-[#9c62ff] inline-flex items-center gap-2">
                    {{ $editId ? 'Edit Task' : 'Create New Task' }}
                </h3>
            </div>


            {{-- User --}}
            <label class="block text-gray-600 text-xs">User</label>
            <select wire:model="selectedUser"
                    class="w-full text-xs border  rounded-3xl px-3 py-2 mt-1 mb-3 focus:ring-[#5ca9ff]">
                <option value="">-- Select User --</option>
                @foreach ($users as $u)
                    <option value="{{ $u->id }}">{{ $u->name }}</option>
                @endforeach
            </select>

            <div class="flex items-center gap-3">
                            {{-- Tanggal Mulai --}}
                            <div class="w-1/2 text-xs">
                                <label class="block text-gray-600 mb-1">Start Date</label>
                                <input 
                                    type="date" 
                                    wire:model.live="tanggal_mulai"
                                    class="text-xs w-full border rounded-3xl px-3 py-1.5 bg-white focus:outline-none focus:ring focus:ring-[#5ca9ff]/50"
                                >
                                @error('tanggal_mulai') 
                                    <span class="text-xs text-red-500">{{ $message }}</span> 
                                @enderror
                            </div>

                            {{-- Tanggal Selesai --}}
                            <div class="w-1/2 text-xs">
                                <label class="block text-gray-600 mb-1">End Date
                                    <span class="text-gray-400">(Blank allowed)</span>
                                </label>
                                <input 
                                    type="date" 
                                    wire:model.live="tanggal_selesai"
                                    class="text-xs w-full border rounded-3xl px-3 py-1.5 bg-white focus:outline-none focus:ring focus:ring-[#5ca9ff]/50"
                                >
                                @error('tanggal_selesai') 
                                    <span class="text-xs text-red-500">{{ $message }}</span> 
                                @enderror
                            </div>
                        </div>

            {{-- Catatan --}}
            <label  class="block text-gray-600 text-xs pt-3">Description</label>
            <textarea wire:model="catatanText"
                class="w-full border text-xs rounded-md px-3 py-2 mt-1 h-24 focus:ring-[#5ca9ff]"></textarea>

            {{-- Buttons --}}
            <div class="flex justify-end gap-2 mt-4">
                <button wire:click="$set('openModal', false)"
                    class="px-4 py-1.5 text-xs border rounded-3xl hover:bg-gray-100">
                    Cancel
                </button>

                <button 
                    wire:click="{{ $editId ? 'update' : 'save' }}"
                    class="flex items-center gap-2 px-4 py-1.5 text-xs text-white rounded-3xl shadow 
                        hover:shadow-md transition-all duration-200 bg-[#5ca9ff]">
                    
                    {{ $editId ? 'Update' : 'Save' }}
                </button>

            </div>
        </div>
    </div>
    @endif

</div>