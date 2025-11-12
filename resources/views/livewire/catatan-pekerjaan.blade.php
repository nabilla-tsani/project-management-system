<div>
    @if($catatanModal)
        <div 
            class="fixed inset-0 bg-gray-800/50 flex items-center justify-center z-50"
            wire:click.self="closeModal"
        >
            <div class="bg-white rounded-2xl p-6 w-full max-w-5xl shadow-xl flex gap-6">
                {{-- Kiri: Form --}}
                <div class="w-1/3 border-r pr-5">
                    <h2 class="text-base font-semibold mb-4 text-gray-700 text-center">
                        {{ $catatanId ? 'Edit Catatan' : 'Tambah Catatan' }}
                    </h2>

                    <form 
                        wire:submit.prevent="save" 
                        wire:key="{{ $formKey }}" 
                        class="space-y-3 text-[13px]"
                    >
                        {{-- Jenis --}}
                        <div>
                            <label class="block text-gray-600 mb-1">Jenis</label>
                            <select 
                                wire:model.live="jenis"
                                class="w-full border rounded-lg px-3 py-1.5 bg-white focus:outline-none focus:ring focus:ring-[#5ca9ff]/50"
                            >
                                <option value="">-- Pilih Jenis --</option>
                                <option value="bug">Bug</option>
                                <option value="pekerjaan">Pekerjaan</option>
                            </select>
                            @error('jenis') 
                                <span class="text-xs text-red-500">{{ $message }}</span> 
                            @enderror
                        </div>

                        {{-- User --}}
                        <div>
                            <label class="block text-gray-600 mb-1">User</label>
                            <select 
                                wire:model.live="user_id"
                                class="w-full border rounded-lg px-3 py-1.5 bg-white focus:outline-none focus:ring focus:ring-[#5ca9ff]/50"
                            >
                                <option value="">-- Pilih User --</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                            @error('user_id') 
                                <span class="text-xs text-red-500">{{ $message }}</span> 
                            @enderror
                        </div>

                        {{-- Catatan --}}
                        <div>
                            <label class="block text-gray-600 mb-1">Catatan</label>
                            <textarea 
                                wire:model.live="isiCatatan" 
                                rows="4"
                                class="w-full border rounded-lg px-3 py-1.5 focus:outline-none focus:ring focus:ring-[#5ca9ff]/50"
                                placeholder="Masukkan isi catatan"
                            ></textarea>
                            @error('isiCatatan') 
                                <span class="text-xs text-red-500">{{ $message }}</span> 
                            @enderror
                        </div>

                        {{-- Tombol --}}
                        <div class="flex justify-end gap-2 pt-2">
                            @if($catatanId)
                                <button 
                                    type="button" 
                                    wire:click="cancelEdit"
                                    class="px-3 py-1.5 bg-gray-300 text-gray-700 rounded-3xl text-xs hover:bg-gray-400 transition"
                                >
                                    Batal
                                </button>
                            @endif
                            <button 
                                type="submit"
                                class="px-3 py-1.5 bg-[#5ca9ff] text-white rounded-3xl text-xs hover:bg-[#449bff] transition"
                            >
                                {{ $catatanId ? 'Update' : 'Simpan' }}
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Kanan: Daftar --}}
                <div class="w-2/3">
                <h2 class="text-base font-semibold mb-4 text-center text-gray-700">
                    Notes and Tasks — {{ $namaFitur ?? 'Nama Fitur' }}
                </h2>

                <div class="h-[400px] overflow-y-auto pr-2">
                    @if($catatan->isEmpty())
                        <p class="text-gray-400 text-xs italic text-center">No notes or tasks yet.</p>
                    @else
                        <ul class="text-[13px]">
                            @foreach($catatan as $item)
                                <li class="border-b py-3">
                                    <div class="flex justify-between items-center">
                                        <span class="font-medium capitalize">{{ $item->jenis }}</span>

                                        <div class="flex items-center gap-3 text-xs text-gray-400">
                                            <span>{{ $item->user->name ?? '-' }}</span>

                                            <button 
                                                wire:click="edit({{ $item->id }})"
                                                class="text-blue-500 hover:text-blue-700"
                                                title="Edit"
                                            >
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </button>

                                            <button 
                                                wire:click="delete({{ $item->id }})"
                                                class="text-red-500 hover:text-red-700"
                                                title="Delete"
                                            >
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <p class="text-gray-700 mt-1">{{ $item->catatan }}</p>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>



                    <div class="flex justify-end mt-4">
                        <button 
                            wire:click="closeModal"
                            class="bg-gray-300 hover:bg-gray-400 text-xs px-4 py-2 rounded-lg"
                        >
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
