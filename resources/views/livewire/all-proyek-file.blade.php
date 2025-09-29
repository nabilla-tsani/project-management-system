<div class="mt-4">
    @if(session()->has('message'))
        <div class="bg-green-100 text-green-700 p-2 rounded mb-4">
            {{ session('message') }}
        </div>
    @endif


    <div class="flex justify-between items-center mb-4">
        <h3 class="text-xl font-semibold">File Proyek</h3>
        <button wire:click="openModal" class="px-3 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            + Upload File
        </button>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
        @forelse($files as $f)
            @php
                $ext = strtolower(pathinfo($f->path, PATHINFO_EXTENSION));
            @endphp
            <div class="border rounded p-4 shadow-sm hover:shadow-md flex flex-col items-center text-center">
                {{-- Ikon sesuai jenis file --}}
                @if(in_array($ext, ['jpg','jpeg','png','gif']))
                    <i class="fa-solid fa-file-image text-purple-600 text-5xl mb-2"></i>
                @elseif($ext === 'pdf')
                    <i class="fa-solid fa-file-pdf text-red-600 text-5xl mb-2"></i>
                @elseif(in_array($ext, ['doc','docx']))
                    <i class="fa-solid fa-file-word text-blue-600 text-5xl mb-2"></i>
                @elseif(in_array($ext, ['xls','xlsx']))
                    <i class="fa-solid fa-file-excel text-green-600 text-5xl mb-2"></i>
                @elseif(in_array($ext, ['ppt','pptx']))
                    <i class="fa-solid fa-file-powerpoint text-orange-600 text-5xl mb-2"></i>
                @elseif(in_array($ext, ['zip','rar']))
                    <i class="fa-solid fa-file-zipper text-yellow-600 text-5xl mb-2"></i>
                @else
                    <i class="fa-solid fa-file-lines text-gray-600 text-5xl mb-2"></i>
                @endif

                <div class="font-semibold break-words">{{ $f->{'nama_file'} }}</div>
                <div class="text-sm text-gray-500 mb-2 break-words">{{ $f->keterangan }}</div>
                <div class="text-xs text-gray-400 mb-2">Diunggah oleh: {{ $f->user?->name ?? '-' }}</div>

                <div class="flex space-x-3">
                    {{-- Lihat --}}
                    <a href="{{ asset('storage/'.$f->path) }}" target="_blank" 
                    class="text-gray-600 hover:text-gray-800">
                        <i class="fa-solid fa-eye"></i>
                    </a>

                    {{-- Download --}}
                    <a href="{{ asset('storage/'.$f->path) }}" 
                    download="{{ $f->nama_file }}"
                    class="text-blue-600 hover:text-blue-800">
                        <i class="fa-solid fa-download"></i>
                    </a>


                    {{-- Edit --}}
                    <button wire:click="openModal({{ $f->id }})" 
                            class="text-green-600 hover:text-green-800">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </button>

                    {{-- Hapus --}}
                    <button x-data 
                            @click="if(confirm('Yakin hapus file ini?')) { $wire.delete({{ $f->id }}) }"
                            class="text-red-600 hover:text-red-800">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center text-gray-500">Belum ada file</div>
        @endforelse
    </div>

    {{-- Modal --}}
    @if($modalOpen)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white p-6 rounded shadow w-full max-w-md">
                <h3 class="text-lg font-semibold mb-4">{{ $fileId ? 'Edit File' : 'Upload File' }}</h3>

                <input type="text" wire:model="namaFile" placeholder="Nama File" class="border rounded p-2 w-full mb-3">
                @error('namaFile') <div class="text-red-600 text-sm mb-2">{{ $message }}</div> @enderror

                <textarea wire:model="keterangan" placeholder="Keterangan" class="border rounded p-2 w-full mb-3"></textarea>
                @error('keterangan') <div class="text-red-600 text-sm mb-2">{{ $message }}</div> @enderror

                {{-- Hanya tampilkan input file saat create --}}
                @if(!$fileId)
                    <input type="file" wire:model="file" class="border rounded p-2 w-full mb-3">
                    @error('file') <div class="text-red-600 text-sm mb-2">{{ $message }}</div> @enderror
                @endif

                <div class="flex justify-end">
                    <button wire:click="$set('modalOpen', false)" class="px-3 py-2 bg-gray-300 rounded mr-2">Batal</button>
                    <button wire:click="save" class="px-3 py-2 bg-blue-600 text-white rounded">Simpan</button>
                </div>
            </div>
        </div>
    @endif
</div>


