<div class="pt-0 p-2 space-y-2">

        {{-- Header: Judul & Tombol Tambah --}}
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-3">
            <h2 class="text-md font-medium flex items-center gap-2 text-[#5ca9ff]">
                <i class="fa-solid fa-folder"></i>
                File's ({{ $files->count() }}) 
            </h2>
            <input 
                type="text"
                wire:model.live="search" 
                placeholder="Search for files..."
                class="text-xs px-3 py-1.5 border border-gray-500 rounded-3xl focus:ring-[#5ca9ff] focus:border-[#5ca9ff] outline-none w-96"
            />
        </div>

        <div class="flex items-center gap-3">
            <button wire:click="openModal"
                class="px-4 py-1.5 rounded-3xl text-white shadow hover:shadow-md transition-all duration-200 text-xs"
                style="background-color: #5ca9ff;">
            <i class="fa-solid fa-upload"></i> Upload File
            </button>
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


<div class="space-y-2">
    @forelse ($files as $f)
        @php
            $ext = strtolower(pathinfo($f->path, PATHINFO_EXTENSION));
        @endphp

        <div class="px-4 border border-gray-200 bg-white shadow-xl hover:shadow-32xl transition-all duration-200 text-xs">
            <div class="flex items-center gap-3 w-full">

                {{-- ICON (FIT) --}}
                <span class="px-3 py-3 text-center w-auto shrink-0">
                    @if (in_array($ext, ['jpg','jpeg','png','gif']))
                        <i class="fa-solid fa-file-image text-purple-600 text-xl"></i>
                    @elseif ($ext === 'pdf')
                        <i class="fa-solid fa-file-pdf text-red-600 text-xl"></i>
                    @elseif (in_array($ext, ['doc','docx']))
                        <i class="fa-solid fa-file-word text-blue-600 text-xl"></i>
                    @elseif (in_array($ext, ['xls','xlsx']))
                        <i class="fa-solid fa-file-excel text-green-600 text-xl"></i>
                    @elseif (in_array($ext, ['ppt','pptx']))
                        <i class="fa-solid fa-file-powerpoint text-orange-600 text-xl"></i>
                    @elseif (in_array($ext, ['zip','rar']))
                        <i class="fa-solid fa-file-zipper text-yellow-600 text-xl"></i>
                    @else
                        <i class="fa-solid fa-file-lines text-gray-600 text-xl"></i>
                    @endif
                </span>

               {{-- NAME (NARROWER) --}}
                <span class="px-3 py-3 font-medium text-gray-900 break-words flex-[2] min-w-[120px]"
                    title="{{ $f->nama_file }}">
                    {{ $f->nama_file }}
                </span>

                {{-- DESCRIPTION (WIDE) --}}
                <span class="px-3 py-3 text-gray-600 break-words flex-[3] min-w-[200px] text-justify"
                    title="{{ $f->keterangan }}">
                    {{ $f->keterangan ?: '-' }}
                </span>


                {{-- USER (FIT) --}}
                <span class="px-3 py-3 text-gray-500 text-[10px] w-auto shrink-0 flex items-center italic">
                    <i class="fa-solid fa-user mr-1"></i>
                    {{ $f->user?->name ?? '-' }}
                </span>

                {{-- ACTIONS (FIT) --}}
                <div class="flex justify-end gap-2 items-center w-auto shrink-0">

                    {{-- View --}}
                    <a href="{{ asset('storage/'.$f->path) }}" target="_blank"
                       class="text-green-600 hover:text-green-800">
                        <i class="fa-solid fa-eye"></i>
                    </a>

                    {{-- Download --}}
                    <a href="{{ asset('storage/'.$f->path) }}"
                       download="{{ $f->nama_file }}"
                       class="text-gray-600 hover:text-gray-800">
                        <i class="fa-solid fa-download"></i>
                    </a>

                    {{-- Edit --}}
                    <button wire:click="openModal({{ $f->id }})"
                            class="text-blue-600 hover:text-blue-800">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </button>

                    {{-- Delete --}}
                    <button x-data
                        @click="if (confirm('Are you sure you want to delete this file?')) { $wire.delete({{ $f->id }}) }"
                        class="text-red-600 hover:text-red-800">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </div>

            </div>
        </div>

    @empty
        <div class="text-center text-gray-500 bg-gray-50 rounded-lg p-3 border border-gray-200 text-xs">
            No files have been uploaded yet.
        </div>
    @endforelse
</div>




    {{-- MODAL --}}
@if($modalOpen)
    <div class="fixed inset-0 flex items-center justify-center bg-black/40 backdrop-blur-sm z-50">
        <div class="bg-white shadow-2xl p-6 w-1/3 border border-gray-200">
            <h3 class="text-sm font-medium text-[#9c62ff] mb-5 text-center">
                {{ $fileId ? 'Edit File' : 'Upload File' }}
            </h3>

            <label class="text-xs text-gray-700 mb-2">File Name</label>    
            <input type="text" wire:model.defer="namaFile" placeholder="Nama File"
                class="border border-gray-300 rounded-3xl p-2.5 w-full mb-4 focus:ring-2 focus:ring-blue-400 text-xs">
            @error('namaFile') 
                <div class="text-red-600 text-xs mb-2">{{ $message }}</div> 
            @enderror

            <label class="text-xs text-gray-700 mb-2">Notes</label>    
            <textarea wire:model.defer="keterangan" placeholder="Keterangan"
                rows="5"
                class="border border-gray-300 rounded-md p-2.5 w-full mb-4 focus:ring-2 focus:ring-blue-400 text-xs"></textarea>
            @error('keterangan') 
                <div class="text-red-600 text-xs mb-2">{{ $message }}</div> 
            @enderror


            @if(!$fileId)
                <input type="file" wire:model="file"
                    class="text-xs">
                @error('file') 
                    <div class="text-red-600 text-xs mb-2">{{ $message }}</div> 
                @enderror
            @endif

            <div class="flex justify-end gap-3 mt-4">
                <button wire:click="$set('modalOpen', false)"
                    class="bg-gray-200 text-gray-700 px-4 py-2 rounded-3xl hover:bg-gray-300 hover:scale-105 transition text-xs">
                    Cancel
                </button>
                <button wire:click="save"
                    class="bg-[#5ca9ff] text-white px-4 py-2 rounded-3xl shadow hover:scale-105 transition text-xs">
                    Upload
                </button>
            </div>
        </div>
    </div>
@endif

</div>
