<div class="relative bg-white rounded-2xl shadow-lg border border-gray-100 p-4">
    
    {{-- Header: Judul & Tombol Tambah --}}
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
            <i class="fa-solid fa-users text-blue-600"></i> Anggota Proyek
        </h2>

        <button wire:click="openModal"
            class="px-4 py-1.5 bg-blue-600 text-white rounded-xl shadow hover:bg-blue-700 
                hover:shadow-md transition-all duration-200 text-sm">
            <i class="fa-solid fa-plus mr-1"></i> Anggota
        </button>
    </div>

    {{-- List Item --}}
    <div class="space-y-2">
        @forelse($proyekUsers as $pu)
            <div
                class="px-3 py-2 rounded-xl border border-gray-200 bg-white shadow-sm hover:shadow-md transition-all duration-200 text-xs">
                {{-- Baris 1: Badge peran + nama + aksi --}}
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3 min-w-0">
                        {{-- Badge Role --}}
                        <span
                            class="flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-medium text-white shadow-sm
                            @if($pu->sebagai === 'manajer proyek') bg-gradient-to-r from-yellow-500 to-yellow-700
                            @elseif($pu->sebagai === 'programmer') bg-gradient-to-r from-gray-400 to-gray-600
                            @elseif($pu->sebagai === 'tester') bg-gradient-to-r from-orange-500 to-red-600
                            @else bg-gradient-to-r from-blue-500 to-cyan-600 @endif">
                            @if($pu->sebagai === 'manajer proyek')
                                <i class="fa-solid fa-user-tie"></i>
                            @elseif($pu->sebagai === 'programmer')
                                <i class="fa-solid fa-laptop-code"></i>
                            @elseif($pu->sebagai === 'tester')
                                <i class="fa-solid fa-vial"></i>
                            @else
                                <i class="fa-solid fa-user"></i>
                            @endif
                            {{ ucfirst($pu->sebagai) }}
                        </span>
                        <p class="text-sm font-medium text-black truncate">{{ $pu->user->name }}</p>
                    </div>

                    {{-- Aksi --}}
                    <div class="flex gap-2 shrink-0">
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

                {{-- Baris 2: Deskripsi / Keterangan --}}
                @if($pu->keterangan)
                    <p class="mt-1 pt-2 text-gray-500 text-[12px]">
                        {{ $pu->keterangan }}
                    </p>
                @endif

                {{-- Baris 3: Daftar fitur --}}
                @if($pu->fitur->count())
                    <div class="mt-2 flex flex-wrap gap-1">
                        @foreach($pu->fitur as $fitur)
                            <span class="px-2 py-0.5 bg-blue-100 text-blue-700 text-[10px] rounded-full">
                                {{ $fitur->nama_fitur }}
                            </span>
                        @endforeach
                    </div>
                @endif
            </div>
        @empty
            <div
                class="text-center text-gray-500 bg-gray-50 rounded-lg p-3 border border-gray-200 text-xs">
                Proyek ini belum memiliki anggota. Silakan tambahkan anggota proyek.
            </div>
        @endforelse
</div>



    {{-- Modal --}}
@if($showModal)
    <div class="fixed inset-0 flex items-center justify-center bg-black/40 backdrop-blur-sm z-50">
        <div class="bg-white shadow-2xl p-6 w-[32rem] max-w-[90%] border border-gray-200">
            <h3 class="text-xl font-bold text-gray-800 mb-5 text-center">
                {{ $editId ? 'Edit Anggota Proyek' : 'Tambah Anggota Proyek' }}
            </h3>

            <select wire:model="user_id"
                class="border border-gray-300 rounded-lg p-2.5 w-full mb-3 bg-white text-gray-800 
                       focus:ring-2 focus:ring-blue-400 text-sm">
                <option value="">-- Pilih User --</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </select>

            <select wire:model="sebagai"
                class="border border-gray-300 rounded-lg p-2.5 w-full mb-3 bg-white text-gray-800 
                       focus:ring-2 focus:ring-blue-400 text-sm">
                <option value="">-- Sebagai --</option>
                <option value="manajer proyek">Manajer Proyek</option>
                <option value="programmer">Programmer</option>
                <option value="tester">Tester</option>
            </select>

            <textarea wire:model="keterangan" placeholder="Keterangan"
                rows="5"
                class="border border-gray-300 rounded-lg p-3 w-full mb-4 bg-white text-gray-800 
                       placeholder-gray-400 focus:ring-2 focus:ring-blue-400 text-sm resize-y"></textarea>

            <div class="flex justify-end gap-3">
                <button wire:click="save"
                    class="bg-blue-600 text-white px-4 py-2 
                           rounded-lg shadow hover:scale-105 transition text-sm">
                    Simpan
                </button>
                <button wire:click="$set('showModal', false)"
                    class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 
                           hover:scale-105 transition text-sm">
                    Batal
                </button>
            </div>
        </div>
    </div>
@endif

</div>
