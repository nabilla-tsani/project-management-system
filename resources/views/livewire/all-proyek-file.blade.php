<div class="pt-0 p-2 space-y-2">

    {{-- Header: Judul, Search, Tabs, Tombol Tambah --}}
    <div class="flex items-center justify-between mb-3 gap-3">

        {{-- Left: Judul --}}
        <div class="flex items-center gap-2 shrink-0">
            <i class="fa-solid fa-folder text-blue-500 text-xl"></i>
            <h2 class="text-sm font-semibold text-gray-700">
                Berkas Proyek ({{ $files->count() }})
            </h2>
        </div>

        {{-- Middle: Search + Tabs --}}
        <div class="flex items-center gap-3 flex-1">

            {{-- Search --}}
            <input 
                type="text"
                wire:model.live="search" 
                placeholder="Cari berkas..."
                class="text-xs px-3 py-1.5 border border-gray-300 rounded-full
                    focus:ring-1 focus:ring-[#5ca9ff] focus:border-[#5ca9ff]
                    outline-none w-64"
            />
        </div>

        {{-- Right: Button --}}
        <button 
            wire:click="openModal"
            class="px-3 py-1.5 rounded-full text-white shadow
                transition-all duration-200 ease-out
                text-xs font-medium
                bg-gradient-to-r from-blue-500 to-indigo-600
                hover:scale-105 hover:shadow-lg shrink-0"
        >
            <i class="fa-solid fa-plus text-[10px]"></i>
            Unggah Berkas
        </button>
    </div>

    {{-- Flash Message --}}
    @if (session()->has('message'))
        <div 
            x-data="{ show: true }"
            x-init="setTimeout(() => show = false, 2000)"
            x-show="show"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform -translate-y-2"
            x-transition:enter-end="opacity-100 transform translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="mb-2 text-[10px] text-emerald-700 bg-gradient-to-r from-emerald-50 to-teal-50 px-3 py-2 rounded-lg shadow-sm border border-emerald-200 flex items-center gap-2"
        >
            <div class="w-5 h-5 rounded-full bg-emerald-500 flex items-center justify-center">
                <i class="fas fa-check text-white text-[9px]"></i>
            </div>
            <span class="font-medium">{{ session('message') }}</span>
        </div>
    @endif

    {{-- File List --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 xl:grid-cols-6 gap-3">
        @forelse ($files as $f)
            @php
                $ext = strtolower(pathinfo($f->path, PATHINFO_EXTENSION));
                $isOwner = auth()->id() === $f->user_id;
                
                // Warna berdasarkan tipe file
                $colors = [
                    'image' => ['from' => 'from-purple-500', 'to' => 'to-pink-500', 'bg' => 'bg-purple-50', 'text' => 'text-purple-600'],
                    'pdf' => ['from' => 'from-red-500', 'to' => 'to-orange-500', 'bg' => 'bg-red-50', 'text' => 'text-red-600'],
                    'word' => ['from' => 'from-blue-500', 'to' => 'to-cyan-500', 'bg' => 'bg-blue-50', 'text' => 'text-blue-600'],
                    'excel' => ['from' => 'from-green-500', 'to' => 'to-emerald-500', 'bg' => 'bg-green-50', 'text' => 'text-green-600'],
                    'powerpoint' => ['from' => 'from-orange-500', 'to' => 'to-red-500', 'bg' => 'bg-orange-50', 'text' => 'text-orange-600'],
                    'zip' => ['from' => 'from-yellow-500', 'to' => 'to-amber-500', 'bg' => 'bg-yellow-50', 'text' => 'text-yellow-600'],
                    'default' => ['from' => 'from-gray-500', 'to' => 'to-slate-500', 'bg' => 'bg-gray-50', 'text' => 'text-gray-600'],
                ];

                if (in_array($ext, ['jpg','jpeg','png','gif'])) {
                    $color = $colors['image'];
                    $icon = 'fa-file-image';
                } elseif ($ext === 'pdf') {
                    $color = $colors['pdf'];
                    $icon = 'fa-file-pdf';
                } elseif (in_array($ext, ['doc','docx'])) {
                    $color = $colors['word'];
                    $icon = 'fa-file-word';
                } elseif (in_array($ext, ['xls','xlsx'])) {
                    $color = $colors['excel'];
                    $icon = 'fa-file-excel';
                } elseif (in_array($ext, ['ppt','pptx'])) {
                    $color = $colors['powerpoint'];
                    $icon = 'fa-file-powerpoint';
                } elseif (in_array($ext, ['zip','rar'])) {
                    $color = $colors['zip'];
                    $icon = 'fa-file-zipper';
                } else {
                    $color = $colors['default'];
                    $icon = 'fa-file-lines';
                }
            @endphp

            <div class="group relative bg-white rounded-xl shadow border border-gray-100 overflow-hidden transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
                
                {{-- Badge Owner --}}
                @if($isOwner)
                    <div class="absolute top-2 right-2 z-10">
                        <span class="px-2 py-0.5 bg-blue-500 text-white text-[8px] font-bold rounded-full shadow">
                            Anda
                        </span>
                    </div>
                @endif

                {{-- Header dengan Icon Besar --}}
                <div class="relative {{ $color['bg'] }} pt-3 pb-3 flex flex-col items-center justify-center">
                   {{-- Icon File --}}
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br {{ $color['from'] }} {{ $color['to'] }} 
                        flex items-center justify-center shadow-md mb-1
                        group-hover:scale-105 transition-transform duration-300">
                        <i class="fa-solid {{ $icon }} text-white text-xl"></i>
                    </div>

                    
                    {{-- Extension Badge --}}
                    <span class="px-3 py-1 bg-white/90 backdrop-blur-sm {{ $color['text'] }} text-[9px] font-bold uppercase rounded-full shadow-sm">
                        {{ $ext }}
                    </span>
                </div>

                {{-- Content --}}
                <div class="p-3">
                    {{-- File Name --}}
                    <h3
                        class="text-[11px] font-bold text-gray-800 mb-1 line-clamp-2"
                        title="{{ $f->nama_file }}"
                    >
                        {{ $f->nama_file }}
                    </h3>

                    {{-- Description --}}
                    <p class="text-[9px] text-gray-500 line-clamp-2 mb-3 min-h-[28px]" title="{{ $f->keterangan }}">
                        {{ $f->keterangan ?: 'Tidak ada keterangan' }}
                    </p>

                    {{-- User Info --}}
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-5 h-5 rounded-lg flex items-center justify-center text-[8px] font-bold shadow-sm
                            {{ $isOwner 
                                ? 'bg-gradient-to-br from-blue-500 to-indigo-600 text-white' 
                                : 'bg-gradient-to-br from-gray-200 to-gray-300 text-gray-600' 
                            }}">
                            {{ strtoupper(substr($f->user?->name ?? '?', 0, 2)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="text-[9px] font-medium text-gray-700 truncate">{{ $f->user?->name ?? '-' }}</div>
                            <div class="text-[8px] text-gray-400">Pengunggah</div>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="grid grid-cols-4 place-items-center">
                        {{-- View --}}
                        <a href="{{ asset('storage/'.$f->path) }}" target="_blank"
                        class="inline-flex items-center justify-center w-6 h-6 rounded-md {{ $color['bg'] }} {{ $color['text'] }}
                        hover:bg-opacity-80 transition-all duration-200 group/btn"
                        title="Lihat">
                            <i class="fa-solid fa-eye text-[10px] group-hover/btn:scale-110 transition-transform"></i>
                        </a>

                        {{-- Download --}}
                        <a href="{{ asset('storage/'.$f->path) }}"
                        download="{{ $f->nama_file }}"
                        class="inline-flex items-center justify-center w-6 h-6 rounded-md bg-gray-50 text-gray-600
                        hover:bg-gray-100 transition-all duration-200 group/btn"
                        title="Unduh">
                            <i class="fa-solid fa-download text-[10px] group-hover/btn:scale-110 transition-transform"></i>
                        </a>

                        {{-- Edit --}}
                        <button wire:click="openModal({{ $f->id }})"
                                class="inline-flex items-center justify-center w-6 h-6 rounded-md bg-blue-50 text-blue-600
                                hover:bg-blue-100 transition-all duration-200 group/btn"
                                title="Edit">
                            <i class="fa-solid fa-pen-to-square text-[10px] group-hover/btn:scale-110 transition-transform"></i>
                        </button>

                        {{-- Delete --}}
                        <button wire:click="askDelete({{ $f->id }})"
                                class="inline-flex items-center justify-center w-6 h-6 rounded-md bg-rose-50 text-rose-600
                                hover:bg-rose-100 transition-all duration-200 group/btn"
                                title="Hapus">
                            <i class="fa-solid fa-trash text-[10px] group-hover/btn:scale-110 transition-transform"></i>
                        </button>
                    </div>

                </div>
            </div>

        @empty
            <div class="col-span-full flex flex-col items-center justify-center py-12">
                <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-blue-50 to-indigo-50 flex items-center justify-center mb-4">
                    <i class="fa-solid fa-folder-open text-blue-300 text-4xl"></i>
                </div>
                <p class="text-sm font-semibold text-gray-700 mb-1">Belum ada berkas</p>
                <p class="text-[10px] text-gray-500">Klik "Unggah Berkas" untuk menambahkan berkas baru</p>
            </div>
        @endforelse
    </div>

    {{-- Tombol Kembali --}}
    <div class="flex justify-start pt-4">
        <a href="{{ route('proyek') }}"
            class="px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-[10px] rounded-3xl shadow hover:bg-[#884fd9] transition">
            Kembali ke Daftar Proyek
        </a>
    </div>

    {{-- MODAL Upload/Edit --}}
    @if($modalOpen)
    <div class="fixed inset-0 flex items-center justify-center bg-black/50 backdrop-blur-sm z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl p-5 w-full max-w-md border border-gray-100 animate-fade-in">

            {{-- Header Modal --}}
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shadow">
                        <i class="fa-solid {{ $fileId ? 'fa-pen-to-square' : 'fa-upload' }} text-white text-xs"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-gray-800">
                            {{ $fileId ? 'Edit Berkas' : 'Unggah Berkas' }}
                        </h3>
                        <p class="text-[9px] text-gray-500">Isi form di bawah</p>
                    </div>
                </div>
                <button 
                    wire:click="$set('modalOpen', false)"
                    class="w-7 h-7 rounded-lg hover:bg-gray-100 flex items-center justify-center transition-colors"
                >
                    <i class="fa-solid fa-times text-gray-400 text-xs"></i>
                </button>
            </div>

            {{-- Content --}}
            <div class="space-y-3">

                {{-- File Upload (Hanya saat Create) --}}
                @if(!$fileId)
                    <div>
                        <label class="text-[10px] font-semibold text-gray-700 mb-1 block flex items-center gap-1">
                            <i class="fa-solid fa-file-arrow-up text-blue-500 text-[9px]"></i>
                            Pilih Berkas
                        </label>
                        <input 
                            type="file" 
                            wire:model="file"
                            class="block w-full text-[10px] text-gray-700 border border-gray-200 rounded-lg p-2
                                file:mr-2 file:py-1 file:px-3 file:rounded-lg file:border-0
                                file:text-[10px] file:font-semibold
                                file:bg-blue-50 file:text-blue-700
                                hover:file:bg-blue-100 cursor-pointer
                                focus:outline-none focus:ring-1 focus:ring-blue-400"
                        >
                        @error('file')
                            <p class="text-red-600 text-[9px] mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                @endif

                {{-- Rename File --}}
                <div>
                    <label class="text-[10px] font-semibold text-gray-700 mb-1 block flex items-center gap-1">
                        <i class="fa-solid fa-signature text-indigo-500 text-[9px]"></i>
                        Nama Berkas
                    </label>
                    <input 
                        type="text" 
                        wire:model.defer="namaFile" 
                        placeholder="Kosongkan untuk menggunakan nama asli"
                        class="text-[10px] border border-gray-200 rounded-lg p-2 w-full 
                            bg-white text-gray-800 placeholder-gray-400
                            focus:ring-1 focus:ring-indigo-400 
                            focus:border-indigo-400 outline-none transition-all"
                    >
                    @error('namaFile')
                        <p class="text-red-600 text-[9px] mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Notes --}}
                <div>
                    <label class="text-[10px] font-semibold text-gray-700 mb-1 block flex items-center gap-1">
                        <i class="fa-solid fa-note-sticky text-purple-500 text-[9px]"></i>
                        Keterangan
                    </label>
                    <textarea
                        wire:model.defer="keterangan"
                        rows="10"
                        placeholder="Tambahkan keterangan berkas..."
                        class="text-[10px] border border-gray-200 rounded-lg p-2 w-full 
                            bg-white text-gray-800 placeholder-gray-400
                            focus:ring-1 focus:ring-purple-400 
                            focus:border-purple-400 outline-none resize-none transition-all"
                    ></textarea>
                    @error('keterangan')
                        <p class="text-red-600 text-[9px] mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex justify-end gap-2 mt-4 pt-3 border-t border-gray-100">
                <button 
                    wire:click="$set('modalOpen', false)"
                    class="px-4 py-2 bg-gray-100 text-gray-700 
                        rounded-lg hover:bg-gray-200 
                        hover:scale-105 transition-all duration-200
                        text-[10px] font-semibold shadow-sm
                        flex items-center gap-1"
                >
                    <i class="fa-solid fa-times text-[9px]"></i>
                    Batal
                </button>

                <button 
                    wire:click="save"
                    class="px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600
                        text-white rounded-lg shadow 
                        hover:shadow-lg hover:scale-105 
                        transition-all duration-200 text-[10px] font-semibold
                        flex items-center gap-1"
                >
                    <i class="fa-solid fa-{{ $fileId ? 'check' : 'upload' }} text-[9px]"></i>
                    {{ $fileId ? 'Perbarui' : 'Unggah' }}
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- Modal Konfirmasi Delete --}}
    @if($confirmDelete)
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl p-5 w-full max-w-sm border border-gray-100 animate-fade-in">
            
            {{-- Icon Warning --}}
            <div class="flex justify-center mb-3">
                <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center">
                    <i class="fa-solid fa-exclamation-triangle text-red-600 text-xl"></i>
                </div>
            </div>

            <h3 class="text-sm font-bold text-gray-800 mb-2 text-center">
                Hapus Berkas?
            </h3>

            <p class="text-[10px] text-gray-600 mb-4 text-center leading-relaxed">
                Apakah Anda yakin ingin menghapus berkas <span class="font-semibold text-gray-800">"{{ $deleteName }}"</span>? Tindakan ini tidak dapat dibatalkan.
            </p>

            <div class="flex justify-center gap-2">
                <button 
                    wire:click="$set('confirmDelete', false)"
                    class="px-4 py-2 bg-gray-100 text-gray-700 
                        rounded-lg hover:bg-gray-200 
                        hover:scale-105 transition-all duration-200
                        text-[10px] font-semibold
                        flex items-center gap-1"
                >
                    <i class="fa-solid fa-times text-[9px]"></i>
                    Batal
                </button>

                <button 
                    wire:click="delete({{ $deleteId }})"
                    class="px-4 py-2 bg-gradient-to-r from-red-500 to-rose-600
                        text-white rounded-lg shadow 
                        hover:shadow-lg hover:scale-105 
                        transition-all duration-200 text-[10px] font-semibold
                        flex items-center gap-1"
                >
                    <i class="fa-solid fa-trash text-[9px]"></i>
                    Ya, Hapus
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- Custom Styles --}}
    <style>
        /* Animation */
        @keyframes fade-in {
            from {
                opacity: 0;
                transform: scale(0.95);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .animate-fade-in {
            animation: fade-in 0.2s ease-out;
        }

        /* Scrollbar styling */
        .overflow-x-auto::-webkit-scrollbar {
            height: 8px;
        }

        .overflow-x-auto::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 10px;
        }

        .overflow-x-auto::-webkit-scrollbar-thumb {
            background: linear-gradient(to right, #3b82f6, #6366f1);
            border-radius: 10px;
        }

        .overflow-x-auto::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(to right, #2563eb, #4f46e5);
        }
    </style>

</div>