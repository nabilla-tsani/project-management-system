<div class="relative bg-gray-50 rounded-2xl shadow-lg border border-gray-100 p-4">

    {{-- HEADER --}}
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
            <i class="fa-solid fa-folder-open text-blue-600"></i> File Proyek
        </h3>
        <button wire:click="openModal"
            class="px-4 py-1.5 bg-blue-600 text-white rounded-xl shadow hover:bg-blue-700 
                   hover:shadow-md transition-all duration-200 text-sm flex items-center gap-1">
            <i class="fa-solid fa-upload"></i> Upload File
        </button>
    </div>

{{-- GRID FILE --}}
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
    @forelse($files as $f)
        @php
            $ext = strtolower(pathinfo($f->path, PATHINFO_EXTENSION));
        @endphp

        <div class="bg-white border border-gray-200 rounded-xl shadow-md p-4 hover:shadow-lg transition 
                    flex flex-col justify-between text-center">
            
            {{-- Bagian atas: konten file --}}
            <div class="flex flex-col items-center flex-grow">
                {{-- Ikon utama --}}
                @if(in_array($ext, ['jpg','jpeg','png','gif']))
                    <i class="fa-solid fa-file-image text-purple-600 text-6xl mb-3"></i>
                @elseif($ext === 'pdf')
                    <i class="fa-solid fa-file-pdf text-red-600 text-6xl mb-3"></i>
                @elseif(in_array($ext, ['doc','docx']))
                    <i class="fa-solid fa-file-word text-blue-600 text-6xl mb-3"></i>
                @elseif(in_array($ext, ['xls','xlsx']))
                    <i class="fa-solid fa-file-excel text-green-600 text-6xl mb-3"></i>
                @elseif(in_array($ext, ['ppt','pptx']))
                    <i class="fa-solid fa-file-powerpoint text-orange-600 text-6xl mb-3"></i>
                @elseif(in_array($ext, ['zip','rar']))
                    <i class="fa-solid fa-file-zipper text-yellow-600 text-6xl mb-3"></i>
                @else
                    <i class="fa-solid fa-file-lines text-gray-600 text-6xl mb-3"></i>
                @endif

                {{-- Nama file dengan tooltip --}}
            <div class="font-bold text-[13px] text-gray-900 break-words mb-1 line-clamp-2 leading-tight"
                title="{{ $f->{'nama_file'} }}">
                {{ $f->{'nama_file'} }}
            </div>


                {{-- Keterangan dengan tooltip --}}
                <div class="text-[11px] text-gray-500 mb-1 break-words line-clamp-2 p-1" 
                     title="{{ $f->keterangan }}">
                    {{ $f->keterangan }}
                </div>

                {{-- User --}}
                <div class="text-[10px] text-gray-400 mb-2">
                    <i class="fa-solid fa-user mr-1"></i> {{ $f->user?->name ?? '-' }}
                </div>
            </div>

            {{-- Bagian bawah: tombol aksi --}}
            <div class="flex justify-center gap-3 text-xs mt-3">
                <a href="{{ asset('storage/'.$f->path) }}" target="_blank"
                    class="text-green-600 hover:text-green-800 transition">
                    <i class="fa-solid fa-eye"></i>
                </a>

                <a href="{{ asset('storage/'.$f->path) }}" download="{{ $f->nama_file }}"
                    class="text-gray-600 hover:text-gray-800 transition">
                    <i class="fa-solid fa-download"></i>
                </a>

                <button wire:click="openModal({{ $f->id }})"
                    class="text-blue-600 hover:text-blue-800 transition">
                    <i class="fa-solid fa-pen-to-square"></i>
                </button>

                <button x-data 
                    @click="if(confirm('Yakin hapus file ini?')) { $wire.delete({{ $f->id }}) }"
                    class="text-red-600 hover:text-red-800 transition">
                    <i class="fa-solid fa-trash"></i>
                </button>
            </div>
        </div>
    @empty
        <div class="col-span-full text-center text-gray-500 bg-gray-50 rounded-lg p-3 border border-gray-200 text-xs">
            Belum ada file
        </div>
    @endforelse
</div>


    {{-- MODAL --}}
@if($modalOpen)
    <div class="fixed inset-0 flex items-center justify-center bg-black/40 backdrop-blur-sm z-50">
        <div class="bg-white shadow-2xl p-6 w-[600px] border border-gray-200">
            <h3 class="text-xl font-bold text-gray-800 mb-5 text-center">
                {{ $fileId ? 'Edit File' : 'Upload File' }}
            </h3>

            <input type="text" wire:model.defer="namaFile" placeholder="Nama File"
                class="border border-gray-300 rounded-lg p-2.5 w-full mb-4 focus:ring-2 focus:ring-blue-400 text-sm">
            @error('namaFile') 
                <div class="text-red-600 text-xs mb-2">{{ $message }}</div> 
            @enderror

            <textarea wire:model.defer="keterangan" placeholder="Keterangan"
                rows="5"
                class="border border-gray-300 rounded-lg p-2.5 w-full mb-4 focus:ring-2 focus:ring-blue-400 text-sm"></textarea>
            @error('keterangan') 
                <div class="text-red-600 text-xs mb-2">{{ $message }}</div> 
            @enderror


            @if(!$fileId)
                <input type="file" wire:model="file"
                    class="border border-gray-300 rounded-lg p-2.5 w-full mb-4 focus:ring-2 focus:ring-blue-400 text-sm">
                @error('file') 
                    <div class="text-red-600 text-xs mb-2">{{ $message }}</div> 
                @enderror
            @endif

            <div class="flex justify-end gap-3 mt-4">
                <button wire:click="$set('modalOpen', false)"
                    class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 hover:scale-105 transition text-sm">
                    Batal
                </button>
                <button wire:click="save"
                    class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:scale-105 transition text-sm">
                    Simpan
                </button>
            </div>
        </div>
    </div>
@endif

</div>
