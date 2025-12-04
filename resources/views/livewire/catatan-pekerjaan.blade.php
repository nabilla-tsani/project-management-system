<div>
    @if($catatanModal)
        <div 
        class="fixed inset-0 flex items-center justify-center bg-black/40 backdrop-blur-sm z-50"
            wire:click.self="closeModal"
        >
            <div class="bg-white p-6 w-full max-w-5xl shadow-xl flex gap-6">
                @if($isMember)
                {{-- Kiri: Form --}}
                <div class="w-1/3 border-r pr-5">
                    <h2 class="text-base font-semibold mb-4 text-[#9c62ff] text-center">
                        {{ $catatanId ? 'Edit Task' : 'Add Task' }}
                    </h2>

                    <form 
                        wire:submit.prevent="save" 
                        wire:key="{{ $formKey }}" 
                        class="space-y-3 text-[13px]"
                    >
                        {{-- Jenis --}}
                        <div class="text-xs">
                            <label class="block text-gray-600 mb-1">Type</label>
                            <select 
                                wire:model.live="jenis"
                                class="w-full text-gray-600 border rounded-3xl px-3 py-1.5 bg-white focus:outline-none focus:ring focus:ring-[#5ca9ff]/50"
                            >
                                <option value="">-- Select Type --</option>
                                <option value="bug">Bug</option>
                                <option value="pekerjaan">Task</option>
                            </select>
                            @error('jenis') 
                                <span class="text-xs text-red-500">{{ $message }}</span> 
                            @enderror
                        </div>

                        <div class="text-gray-600 flex items-center gap-3">
                            {{-- Tanggal Mulai --}}
                            <div class="w-1/2 text-xs">
                                <label class="block text-gray-600 mb-1">Start Date</label>
                                <input 
                                    type="date" 
                                    wire:model.live="tanggal_mulai"
                                    class="text-xs text-gray-600 w-full border rounded-3xl px-3 py-1.5 bg-white focus:outline-none focus:ring focus:ring-[#5ca9ff]/50"
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
                                    class="text-xs text-gray-600 w-full border rounded-3xl px-3 py-1.5 bg-white focus:outline-none focus:ring focus:ring-[#5ca9ff]/50"
                                >
                                @error('tanggal_selesai') 
                                    <span class="text-xs text-red-500">{{ $message }}</span> 
                                @enderror
                            </div>
                        </div>

                        {{-- Catatan --}}
                        <div class="text-xs">
                            <label class="block text-gray-600 mb-1">Task</label>
                            <textarea 
                                wire:model.live="isiCatatan" 
                                rows="10"
                                class="text-gray-600 w-full border rounded-lg px-3 py-1.5 focus:outline-none focus:ring focus:ring-[#5ca9ff]/50"
                                placeholder="Add tasks here"
                            ></textarea>
                            @error('isiCatatan') 
                                <span class="text-red-500">{{ $message }}</span> 
                            @enderror
                        </div>

                        {{-- Tombol --}}
                        <div class="flex justify-end gap-2 pt-2">
                            @if($catatanId)
                                <button 
                                    type="button" 
                                    wire:click="cancelEdit"
                                    class="text-gray-400 px-3 py-1.5 bg-gray-300 text-gray-700 rounded-3xl text-xs hover:bg-gray-400 transition"
                                >
                                    Cancel
                                </button>
                            @endif

                            <button 
                                type="submit"
                                class="px-3 py-1.5 bg-[#5ca9ff] text-white rounded-3xl text-xs hover:bg-[#449bff] transition"
                            >
                                {{ $catatanId ? 'Update' : 'Save' }}
                            </button>
                        </div>
                    </form>

                </div>
                @endif

                {{-- Kanan: Daftar --}}
                <div class="{{ $isMember ? 'w-2/3' : 'w-full' }}">
                    <div class="mb-1 flex items-center justify-between">
                    <h2 class="text-base font-semibold mb-1 text-center">
                        <span class="text-gray-800">Features :</span>
                        <span class="text-[#5ca9ff]">{{ $namaFitur ?? 'Nama Fitur' }}</span>
                    </h2>

                    {{-- Filter --}}
                    <div class="mb-3 flex justify-end gap-2 text-[10px]">
                        <label for="filterJenis" class="text-gray-600">Filter by Type:</label>
                        <select 
                            id="filterJenis"
                            wire:model="filterJenis"
                            class="text-xs border rounded-3xl px-7 bg-white focus:outline-none focus:ring focus:ring-[#5ca9ff]/50"
                        >
                            <option value="">All</option>
                            <option value="pekerjaan">Task</option>
                            <option value="bug">Bug</option>
                        </select>
                    </div>
                </div>
                
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
                    
                <div class="h-[400px] overflow-y-auto pr-2">
                    @if($catatan->isEmpty())
                        <p class="text-gray-400 text-xs italic text-center">No tasks yet.</p>
                    @else
                        <ul class="text-[13px]">
                            @foreach($catatan as $item)
                                <li class="border-b py-3">
                                    <div class="flex justify-between items-center">

                                        {{-- Jenis + User --}}
                                        <div class="flex items-center gap-2 text-[12px] capitalize pl-1">

                                            {{-- Jenis --}}
                                            <span class="flex items-center gap-1
                                                {{ $item->jenis === 'pekerjaan' 
                                                    ? 'text-[#5ca9ff]' 
                                                    : ($item->jenis === 'bug' 
                                                        ? 'text-[#9c62ff]' 
                                                        : 'text-gray-700') }}">
                                                
                                                {{-- Icon --}}
                                                <i class="{{ $item->jenis === 'pekerjaan' 
                                                    ? 'fa-solid fa-check-circle' 
                                                    : ($item->jenis === 'bug' 
                                                        ? 'fa-solid fa-bug' 
                                                        : 'fa-solid fa-circle') }}">
                                                </i>

                                                {{-- Teks --}}
                                                {{ $item->jenis === 'pekerjaan' ? 'Task' : ($item->jenis === 'bug' ? 'Bug' : $item->jenis) }}
                                            </span>

                                            {{-- User (menempel tepat setelah jenis) --}}
                                            <span class="text-[10px] italic text-gray-400">
                                                Report from: {{ $item->user->name ?? '-' }}
                                            </span>
                                        </div>

                                        {{-- Tanggal + Aksi --}}
                                        <div class="flex items-center gap-3 text-[10px] italic text-gray-400">

                                            {{-- Tanggal --}}
                                            <span>
                                                {{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('d M Y') }}
                                                -
                                                {{ $item->tanggal_selesai 
                                                    ? \Carbon\Carbon::parse($item->tanggal_selesai)->format('d M Y') 
                                                    : 'Project done' }}
                                            </span>

                                        @if($item->user_id === auth()->id())
                                            {{-- Edit --}}
                                            <button wire:click="edit({{ $item->id }})" class="text-blue-500 hover:text-blue-700" title="Edit">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </button>

                                            {{-- Delete --}}
                                            <button wire:click="delete({{ $item->id }})" class="text-red-500 hover:text-red-700" title="Delete">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        @endif

                                        @if($roleUser === 'manajer proyek')
                                            <button 
                                                wire:click="openFeedbackModal({{ $item->id }})"
                                                class="text-blue-500 hover:text-blue-600 rounded-lg hover:bg-blue-50 transition"
                                                title="Give Feedback"
                                            >
                                                <i class="fa-regular fa-comment-dots text-sm"></i>
                                            </button>
                                        @endif
                                                </div>
                                    </div>

                                    <p class="text-xs text-gray-700 mt-1 pl-1 text-justify">
                                        {{ $item->catatan }}
                                    </p>

                                    @if($item->feedback)
                                    <div class="mt-1 ml-1 bg-gray-50 border border-gray-200 rounded-lg p-2">

                                        {{-- Text Feedback --}}
                                        <p class="text-[11px] text-gray-600 italic text-justify">
                                            <i class="fa-solid fa-comment-dots mr-1"></i>
                                            {{ $item->feedback }}
                                        </p>

                                        {{-- Action Buttons --}}
                                        <div class="flex justify-end gap-3 mt-1">
                                            {{-- Delete --}}
                                        @if($roleUser === 'manajer proyek')                                         
                                            <button 
                                                wire:click="deleteFeedback({{ $item->id }})"
                                                class="text-[10px] text-red-500 hover:text-red-700 flex items-center gap-1"
                                            >
                                                <i class="fa-solid fa-trash"></i> Delete
                                            </button>
                                        @endif
                                        </div>
                                    </div>
                                    @endif

                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
                    <div class="flex justify-end mt-1"> 
                        <button wire:click="closeModal" class="text-gray-600 text-xs bg-gray-300 hover:bg-gray-400 text-xs px-4 py-2 rounded-3xl"> 
                            Close 
                        </button> 
                    </div>

                </div>
            </div>
        </div>
    @endif

    {{-- Modal Feedback --}}
    @if($feedbackModal)
    <div 
        class="fixed inset-0 flex items-center justify-center bg-black/40 z-50"
        wire:click.self="closeFeedbackModal"
    >
        <div class="bg-white p-6 w-full max-w-lg shadow-xl">
            
            {{-- Header --}}
            <div class="flex items-center justify-center gap-2 mb-4">
                <i class="fa-solid fa-comment-dots text-lg text-[a#5ca9ff]"></i>
                <h2 class="text-md">Feedback</h2>
            </div>


            {{-- Textarea --}}
            <textarea
                wire:model="feedbackText"
                class="text-xs w-full border rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-blue-400"
                rows="8"
                placeholder="Add notes or feedback..."
            ></textarea>

            {{-- Actions --}}
            <div class="flex justify-end gap-3 mt-5">
                <button 
                    wire:click="closeFeedbackModal"
                    class="text-xs px-4 py-2 bg-gray-200 rounded-3xl hover:bg-gray-300"
                >
                    Cancel
                </button>

                <button 
                    wire:click="saveFeedback"
                    class="text-xs px-4 py-2 bg-[#5ca9ff] text-white rounded-3xl"
                >
                    Save
                </button>
            </div>

        </div>
    </div>
    @endif



<style>
input[type="date"] {
    color-scheme: light;
}
</style>


</div>