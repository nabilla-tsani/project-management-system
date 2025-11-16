
<div class="pt-0 p-2 space-y-2">
        @if (session()->has('message'))
            <div 
                x-data="{ show: true }"
                x-init="setTimeout(() => show = false, 1000)"
                x-show="show"
                x-transition.duration.500ms
                class="text-xs p-2 rounded bg-green-100 text-green-700 border border-green-300"
            >
                {{ session('message') }}
            </div>
        @endif

    {{-- Header: Judul & Tombol Tambah --}}
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-3">
            <h2 class="text-md font-medium flex items-center gap-2 text-[#5ca9ff]">
                <i class="fa-solid fa-users"></i>
                Project Members ({{ $proyekUsers->count() }})
            </h2>
            <input 
                type="text"
                wire:model.live="search" 
                placeholder="Search member..."
                class="text-xs px-3 py-1.5 border border-gray-500 rounded-3xl focus:ring-[#5ca9ff] focus:border-[#5ca9ff] outline-none w-96"
            />
        </div>

        <div class="flex items-center gap-3">
            <button wire:click="openModal"
                class="px-4 py-1.5 rounded-3xl text-white shadow hover:shadow-md transition-all duration-200 text-xs"
                style="background-color: #5ca9ff;">
                <i class="fa-solid fa-plus mr-1"></i> Add Member
            </button>
        </div>
    </div>


    {{-- List Item --}}
<div class="space-y-2">
    @forelse($proyekUsers as $pu)
        <div class="px-4 py-3 border border-gray-200 bg-white shadow-sm hover:shadow-md transition-all duration-200 text-xs">
            
            {{-- BARIS 1: dengan persentase --}}
            <div class="flex items-start gap-3">

                {{-- Role --}}
                <div class="basis-[7%] flex items-center justify-center">
                    <span
                        class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] text-white shadow-sm
                        @if($pu->sebagai === 'manajer proyek') bg-[#9c62ff]
                        @elseif($pu->sebagai === 'programmer') bg-[#5ca9ff]
                        @elseif($pu->sebagai === 'tester') bg-[#F97316]
                        @else bg-gradient-to-r from-blue-500 to-cyan-600 @endif">

                        @if($pu->sebagai === 'manajer proyek')
                            <i class="fa-solid fa-user-tie"></i>
                        @elseif($pu->sebagai === 'programmer')
                            <i class="fa-solid fa-laptop-code"></i>
                        @elseif($pu->sebagai === 'tester')
                            <i class="fa-solid fa-vial-circle-check"></i>
                        @else
                            <i class="fa-solid fa-user"></i>
                        @endif

                        {{ 
                            $pu->sebagai === 'manajer proyek' ? 'Manager' :
                            ($pu->sebagai === 'programmer' ? 'Programmer' :
                            ($pu->sebagai === 'tester' ? 'Tester' : ucfirst($pu->sebagai)))
                        }}
                    </span>
                </div>

                {{-- Nama User --}}
                <div class="basis-[20%]">
                    <p class="text-xs font-medium text-gray-900">
                        {{ $pu->user->name }}
                    </p>
                </div>

                {{-- Fitur --}}
                <div class="basis-[67%] flex flex-wrap gap-1">
                    @foreach($pu->fitur as $fitur)
                        <span class="px-2 py-0.5 bg-gray-200 text-gray-700 text-[10px] rounded-full">
                            {{ $fitur->nama_fitur }}
                        </span>
                    @endforeach
                </div>


                {{-- Aksi --}}
                <div class="basis-[3%] flex justify-end gap-2 shrink-0">
                    <button wire:click="openModal({{ $pu->id }})"
                        class="text-blue-500 hover:text-blue-700 transition">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button wire:click="delete({{ $pu->id }})"
                        class="text-red-500 hover:text-red-700 transition">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>

            </div>

            {{-- BARIS 2: FITUR --}}
                <div class="mt-2 flex flex-wrap gap-1">
                    @if($pu->keterangan)
                        <p class="text-gray-600 text-[11px] leading-tight whitespace-normal">
                            {{ $pu->keterangan }}
                        </p>
                    @else
                        <p class="text-gray-400 text-[11px] italic">-</p>
                    @endif
                </div>
        </div>
    @empty
        <div class="text-center text-gray-500 bg-gray-50 rounded-lg p-3 border border-gray-200 text-xs">
            This project has no members yet. Please add project members.
        </div>
    @endforelse
</div>

    {{-- Modal --}}
    @if($showModal)
        <div class="fixed inset-0 flex items-center justify-center bg-black/40 backdrop-blur-sm z-50">
            <div class="bg-white shadow-2xl p-6 w-[32rem] max-w-[90%] border border-gray-200">
                <h3 class="text-md font-medium text-[#9c62ff] mb-5 text-center">
                    {{ $editId ? 'Edit Member' : 'Add Member' }}
                </h3>

                <select wire:model="user_id"
                    class="text-xs border border-gray-300 rounded-3xl p-2.5 w-full mb-3 bg-white text-gray-800 
                        focus:ring-2 focus:ring-blue-400 text-sm">
                    <option value="">-- Select Member --</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>

                <select wire:model="sebagai"
                    class="text-xs border border-gray-300 rounded-3xl p-2.5 w-full mb-3 bg-white text-gray-800 
                        focus:ring-2 focus:ring-blue-400 text-sm">
                    <option value="">-- Role --</option>
                    <option value="manajer proyek">Manager</option>
                    <option value="programmer">Programmer</option>
                    <option value="tester">Tester</option>
                </select>

                <textarea wire:model="keterangan" placeholder="Keterangan"
                    rows="5"
                    class="text-xs border border-gray-300 rounded-xl p-3 w-full mb-4 bg-white text-gray-800 
                        placeholder-gray-400 focus:ring-2 focus:ring-blue-400 text-sm resize-y"></textarea>

                <div class="flex justify-end gap-3">
                    <button wire:click="save"
                        class="bg-[#5ca9ff] text-white px-4 py-2 
                            rounded-3xl shadow hover:scale-105 transition text-xs">
                        {{ $editId ? 'Update' : 'Save' }}
                    </button>
                    <button wire:click="$set('showModal', false)"
                        class="bg-gray-200 text-gray-700 px-4 py-2 rounded-3xl hover:bg-gray-300 
                            hover:scale-105 transition text-xs">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>