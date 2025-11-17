<div>
@if($modalOpen)
    <div 
        class="fixed inset-0 bg-gray-800/50 flex items-center justify-center z-50"
        wire:click.self="closeModal"
    >
        <div class="bg-white p-6 w-full max-w-3xl shadow-xl flex gap-6" wire:key="modal-{{ $proyekFiturId ?? 'new' }}">

            {{--     KOLOM KIRI — TABEL USER        --}}
            <div class="w-2/3 border-r pr-5">

                {{-- Header Fitur --}}
                <h2 class="text-base font-semibold mb-2 text-center">
                    <span class="text-gray-800">Features :</span>
                    <span class="text-[#5ca9ff]">{{ $namaFitur ?? '-' }}</span>
                </h2>

                @if (session()->has('message'))
                    <div 
                        x-data="{ show: true }"
                        x-init="setTimeout(() => show = false, 1000)"
                        x-show="show"
                        class="mb-3 text-xs text-green-600 bg-green-100 px-3 py-2 rounded-3xl shadow-sm"
                    >
                        {{ session('message') }}
                    </div>
                @endif


                {{-- Scroll List --}}
               <div class="h-[400px] overflow-y-auto pr-2">
                    <ul class="text-xs">
                        @if($fiturUsers->isNotEmpty())
                            @foreach($fiturUsers as $index => $fu)
                                <li wire:key="fu-{{ $fu->id }}" class="border-b py-3 text-xs">
                                    <div class="flex items-center text-xs">
                                        <span class="w-[5%] text-gray-900 pr-2">{{ $index + 1 }}. </span>
                                        <span class="w-[45%] text-gray-900 pr-2">{{ $fu->user?->name ?? '-' }}</span>                                       
                                        <span class="w-[50%] text-gray-600 pr-2 block" style="text-align:justify">
                                            {{ $fu->keterangan ?? '-' }}
                                        </span>
                                        <div class="w-[10%] flex items-center justify-end gap-2">
                                            <button wire:click="edit({{ $fu->id }})" class="text-blue-500 hover:text-blue-700 text-xs" title="Edit">
                                                <i class="fa-solid fa-pen-to-square text-xs"></i>
                                            </button>
                                            <button wire:click="delete({{ $fu->id }})" class="text-red-500 hover:text-red-700 text-xs" title="Delete">
                                                <i class="fa-solid fa-trash text-xs"></i>
                                            </button>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        @else
                            <li class="py-3 text-center text-gray-400 italic text-xs">No one’s on this feature yet.</li>
                        @endif
                    </ul>
                </div>
            <div class="flex justify-end mt-1"> 
                <button wire:click="closeModal" class="text-xs bg-gray-300 hover:bg-gray-400 text-xs px-4 py-2 rounded-3xl"> 
                    Close 
                </button> 
            </div>       
            </div>


            {{--       KOLOM KANAN — FORM        --}}
            <div class="w-1/3">

                <h3 class="text-base font-semibold mb-6 text-[#9c62ff] text-center flex items-center justify-center gap-2">
                    <i class="fas fa-user-plus text-[#9c62ff]"></i>
                    {{ $fiturUserId ? 'Edit Member' : 'Add Member to Feature' }}
                </h3>

                {{-- FORM --}}
                <form wire:submit.prevent="save" wire:key="{{ $formKey }}"  class="space-y-3 text-[13px]">

                    {{-- User --}}
                    <div class="text-xs">
                        <label class="block text-gray-600 mb-1">Member</label>
                        <select 
                            wire:model="user_id"
                            wire:key="user-select-{{ $isEdit ? $fiturUserId : 'create' }}"
                            class="w-full border rounded-3xl px-3 py-1.5 bg-white focus:outline-none focus:ring focus:ring-[#5ca9ff]/50"
                        >
                            <option value="">-- Select Member --</option>
                            @foreach($userList as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                        @error('user_id')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Keterangan --}}
                    <div class="text-xs">
                        <label class="block text-gray-600 mb-1">Notes</label>
                        <textarea 
                            wire:model="keterangan" 
                            wire:key="ket-{{ $isEdit ? $fiturUserId : 'create' }}"
                             rows="6"
                            class="w-full border rounded-lg px-3 py-1.5 bg-white focus:outline-none focus:ring focus:ring-[#5ca9ff]/50 resize-y"
                            placeholder="Add notes for this member, or leave it blank."
                        ></textarea>

                        @error('keterangan') 
                            <span class="text-red-500 text-xs">{{ $message }}</span> 
                        @enderror
                    </div>

                    {{-- Tombol --}}
                    <div class="flex justify-start gap-2">
                        @if($fiturUserId)
                            <button 
                                type="button" 
                                wire:click="cancelEdit"
                                class="px-3 py-1.5 bg-gray-300 text-gray-700 rounded-3xl text-xs hover:bg-gray-400 transition"
                            >
                                Cancel
                            </button>
                        @endif

                        <button 
                            type="submit"
                            class="px-3 py-1.5 bg-[#5ca9ff] text-white rounded-3xl text-xs hover:bg-[#449bff] transition"
                        >
                            {{ $fiturUserId ? 'Update' : 'Save' }}
                        </button>
                    </div>

                </form>

            </div>

        </div>
    </div>
@endif

<script>
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('reloadPage', () => {
            window.location.reload();
        });
    });
</script>


</div>
