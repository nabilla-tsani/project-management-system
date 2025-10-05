<div>

    {{-- Daftar Catatan --}}
<div class="divide-y divide-gray-200 border rounded-md bg-gray-100">
    @forelse($catatan as $c)
        <div class="px-3 py-2 flex flex-col gap-1 text-sm">
            {{-- Header --}}
            <div class="flex justify-between items-center">
                <span class="text-xs font-semibold text-blue-700 flex items-center gap-1">
                    <i class="fa-solid fa-tag"></i> {{ $c->jenis }}
                </span>
                <span class="text-gray-500 text-[11px] flex items-center gap-1">
                    <i class="fa-solid fa-user"></i> {{ $c->user?->name ?? '-' }}
                </span>
            </div>

            {{-- Isi --}}
            <p class="text-gray-700 leading-snug">{{ $c->catatan }}</p>

            {{-- Aksi --}}
            @if(auth()->id() === $c->user_id)
                <div class="flex justify-end gap-2 mt-1 text-xs">
                    <button wire:click="openModal({{ $c->id }})"
                        class="text-blue-600 hover:text-blue-800 flex items-center gap-1">
                        <i class="fa-solid fa-pen"></i>
                    </button>
                    <button wire:click="delete({{ $c->id }})"
                        class="text-red-600 hover:text-red-800 flex items-center gap-1">
                        <i class="fa-solid fa-trash"></i> 
                    </button>
                </div>
            @endif
        </div>
        
    @empty
        <div class="px-3 py-2 text-center text-gray-400 italic text-xs">Belum ada catatan</div>
        
    @endforelse
</div>

    
    {{-- Tombol Tambah --}}
    <div class="flex justify-end mt-2">
        <button wire:click="openModal"
            class="px-3 py-1.5 bg-blue-600 text-white rounded-md shadow hover:bg-blue-700 transition text-xs flex items-center gap-1">
            <i class="fa-solid fa-plus"></i> Catatan
        </button>
    </div>



    {{-- Modal Tambah/Edit --}}
    @if($modalOpen)
        <div class="fixed inset-0 bg-black/40 backdrop-blur-sm flex items-center justify-center z-50">
            <div class="bg-white rounded-xl shadow-lg p-5 w-full max-w-md border border-gray-200">
                <h3 class="text-lg font-bold text-gray-800 mb-4">
                    {{ $catatanId ? 'Edit Catatan' : 'Tambah Catatan' }}
                </h3>

                <select wire:model="jenis" class="border rounded-lg p-2 w-full mb-3 focus:ring-2 focus:ring-blue-400">
                    <option value="">-- Pilih Jenis --</option>
                    <option value="bug">Bug</option>
                    <option value="pekerjaan">Pekerjaan</option>
                </select>
                @error('jenis') <div class="text-red-600 text-xs mb-2">{{ $message }}</div> @enderror

                <textarea wire:model="isiCatatan" placeholder="Catatan"
                    class="border rounded-lg p-2 w-full mb-3 focus:ring-2 focus:ring-blue-400"></textarea>
                @error('isiCatatan') <div class="text-red-600 text-xs mb-2">{{ $message }}</div> @enderror

                <div class="flex justify-end gap-2">
                    <button wire:click="$set('modalOpen', false)"
                        class="bg-gray-200 text-gray-700 px-3 py-1.5 rounded-lg hover:bg-gray-300 hover:scale-105 transition">
                        Batal
                    </button>
                    <button wire:click="save"
                        class="bg-blue-600 text-white px-3 py-1.5 rounded-lg shadow hover:scale-105 transition">
                        Simpan
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
