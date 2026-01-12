<div class="pt-0 p-2 space-y-2">


    {{-- Header: Judul, Search, Tabs, Tombol Tambah --}}
    <div class="flex items-center justify-between mb-3 gap-3">

        {{-- Left: Judul --}}
        <div class="flex items-center gap-2 shrink-0">
            <i class="fa-solid fa-list-check text-blue-500 text-xl"></i>
            <h2 class="text-sm font-semibold text-gray-700">
                Daftar Catatan ({{ $catatan->count() }})
            </h2>
        </div>

        {{-- Middle: Search + Tabs --}}
        <div class="flex items-center gap-3 flex-1">

            {{-- Search --}}
            <input 
                type="text"
                wire:model.live="search" 
                placeholder="Cari catatan..."
                class="text-xs px-3 py-1.5 border border-gray-300 rounded-full
                    focus:ring-1 focus:ring-[#5ca9ff] focus:border-[#5ca9ff]
                    outline-none w-64"
            />

            {{-- Tabs --}}
            <div class="flex gap-1 bg-white border border-gray-200 rounded-lg p-0.5 shadow-sm">
                <button 
                    wire:click="$set('activeTab', 'fitur')"
                    class="px-3 py-1.5 text-[11px] font-semibold rounded-md transition-all duration-200
                        {{ $activeTab === 'fitur'
                            ? 'bg-gradient-to-r from-blue-500 to-indigo-600 text-white shadow'
                            : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50'
                        }}"
                >
                    <i class="fa-solid fa-layer-group text-[10px] mr-1"></i>
                    Fitur
                    <span class="ml-1 px-1 py-0.5 rounded text-[9px] font-bold
                        {{ $activeTab === 'fitur' ? 'bg-white/20' : 'bg-gray-200' }}">
                        {{ $catatanFitur->count() }}
                    </span>
                </button>

                <button 
                    wire:click="$set('activeTab', 'proyek')"
                    class="px-3 py-1.5 text-[11px] font-semibold rounded-md transition-all duration-200
                        {{ $activeTab === 'proyek'
                            ? 'bg-gradient-to-r from-purple-500 to-pink-500 text-white shadow'
                            : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50'
                        }}"
                >
                    <i class="fa-solid fa-note-sticky text-[10px] mr-1"></i>
                    Proyek
                    <span class="ml-1 px-1 py-0.5 rounded text-[9px] font-bold
                        {{ $activeTab === 'proyek' ? 'bg-white/20' : 'bg-gray-200' }}">
                        {{ $catatanNonFitur->count() }}
                    </span>
                </button>
            </div>
        </div>

        {{-- Right: Button --}}
        <button 
            wire:click="showModal"
            class="px-3 py-1.5 rounded-full text-white shadow
                transition-all duration-200 ease-out
                text-xs font-medium
                bg-gradient-to-r from-blue-500 to-indigo-600
                hover:scale-105 hover:shadow-lg shrink-0"
        >
            <i class="fa-solid fa-plus text-[10px]"></i>
            Tambah Catatan
        </button>
    </div>

    {{-- Flash Message --}}
    @if (session()->has('success'))
        <div 
            x-data="{ show: true }"
            x-init="setTimeout(() => show = false, 3000)"
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
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    {{-- TABLE: Catatan Fitur --}}
    @if($activeTab === 'fitur')
        <div class="bg-white rounded-xl shadow border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-blue-100">
                            <th class="px-3 py-2 text-[10px] font-bold uppercase tracking-wider text-blue-700 w-[40px] text-center">
                                No
                            </th>
                            <th class="px-3 py-2 text-[10px] font-bold uppercase tracking-wider text-blue-700">
                                Info Detail
                            </th>
                            <th class="px-3 py-2 text-[10px] font-bold uppercase tracking-wider text-blue-700">
                                Pengguna
                            </th>
                            <th class="px-3 py-2 text-[10px] font-bold uppercase tracking-wider text-blue-700">
                                Waktu
                            </th>
                            <th class="px-3 py-2 text-[10px] font-bold uppercase tracking-wider text-blue-700">
                                Catatan Fitur
                            </th>
                            <th class="px-3 py-2 text-[10px] font-bold uppercase tracking-wider text-blue-700 text-center w-[60px]">
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($catatanFitur as $index => $item)
                            <tr class="group transition-all duration-200
                                {{ auth()->id() === $item->user_id 
                                    ? 'bg-blue-50/50 hover:bg-blue-100/50' 
                                    : 'hover:bg-gradient-to-r hover:from-blue-50/30 hover:to-indigo-50/30' 
                                }}">
                                <td class="px-3 py-2 text-center">
                                    <div class="w-6 h-6 mx-auto rounded-lg flex items-center justify-center
                                        {{ auth()->id() === $item->user_id 
                                            ? 'bg-gradient-to-br from-blue-200 to-indigo-200' 
                                            : 'bg-gradient-to-br from-blue-100 to-indigo-100' 
                                        }}
                                        group-hover:from-blue-200 group-hover:to-indigo-200 transition-all duration-200">
                                        <span class="text-[10px] font-bold text-blue-700">
                                            {{ $loop->iteration }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-3 py-2">
                                    <div class="flex flex-col gap-1">
                                        <span class="inline-flex items-center w-fit px-2 py-0.5 rounded-full text-[9px] font-bold shadow-sm
                                            {{ $item->jenis === 'pekerjaan' 
                                                ? 'bg-gradient-to-r from-blue-500 to-indigo-600 text-white' 
                                                : 'bg-gradient-to-r from-orange-500 to-red-500 text-white' }}">
                                            <span class="w-1 h-1 rounded-full mr-1 animate-pulse
                                                {{ $item->jenis === 'pekerjaan' ? 'bg-blue-200' : 'bg-red-200' }}"></span>
                                            {{ $item->jenis === 'pekerjaan' ? 'Catatan' : 'Bug' }}
                                        </span>
                                        <div class="flex items-center gap-1.5 text-[10px] font-semibold text-gray-700">
                                            <div class="w-4 h-4 rounded bg-gradient-to-br from-purple-100 to-pink-100 flex items-center justify-center">
                                                <i class="fa-solid fa-cube text-[8px] text-purple-600"></i>
                                            </div>
                                            <span class="max-w-[100px]">{{ $item->fitur->nama_fitur ?? '-' }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-3 py-2">
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-lg flex items-center justify-center text-[9px] font-bold border border-white shadow-sm
                                            {{ auth()->id() === $item->user_id 
                                                ? 'bg-gradient-to-br from-blue-200 to-indigo-200 text-blue-700' 
                                                : 'bg-gradient-to-br from-slate-100 to-slate-200 text-slate-600' 
                                            }}">
                                            {{ strtoupper(substr($item->user->name ?? '?', 0, 2)) }}
                                        </div>
                                        <div>
                                            <div class="text-[10px] font-semibold text-gray-800">{{ $item->user->name ?? '-' }}</div>
                                            @if(auth()->id() === $item->user_id)
                                                <div class="text-[8px] text-blue-600 font-medium">Anda</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-3 py-2 text-[9px]">
                                    <div class="flex flex-col gap-1">
                                        <div class="flex items-center gap-1 text-slate-500">
                                            <i class="fa-solid fa-play text-[7px]"></i>
                                            <span>{{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('d M Y') }}</span>
                                        </div>
                                        <div class="flex items-center gap-1 {{ $item->tanggal_selesai ? 'text-slate-400' : 'text-emerald-500 font-medium' }}">
                                            <i class="fa-solid fa-{{ $item->tanggal_selesai ? 'stop' : 'spinner' }} text-[7px]"></i>
                                            <span>
                                                @if ($item->tanggal_selesai)
                                                    {{ \Carbon\Carbon::parse($item->tanggal_selesai)->format('d M Y') }}
                                                @else
                                                    Running
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-3 py-2">
                                    <p class="text-[10px] text-slate-600 leading-relaxed text-justify">
                                        {{ $item->catatan }}
                                    </p>
                                </td>
                                <td class="px-3 py-2">
                                    <div class="flex items-center justify-center gap-1 opacity-0 group-hover:opacity-100 transition-all duration-200">
                                        @if(auth()->id() === $item->user_id)
                                            <button wire:click="edit({{ $item->id }})" 
                                                class="w-6 h-6 flex items-center justify-center text-blue-600 bg-blue-50 hover:bg-blue-100 rounded transition-all duration-200 hover:scale-110" 
                                                title="Edit">
                                                <i class="fa-solid fa-pen-to-square text-[10px]"></i>
                                            </button>
                                            <button wire:click="delete({{ $item->id }})" 
                                                class="w-6 h-6 flex items-center justify-center text-rose-600 bg-rose-50 hover:bg-rose-100 rounded transition-all duration-200 hover:scale-110" 
                                                title="Hapus">
                                                <i class="fa-solid fa-trash-can text-[10px]"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-3 py-8 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-blue-50 to-indigo-50 flex items-center justify-center mb-2">
                                            <i class="fa-solid fa-inbox text-blue-300 text-2xl"></i>
                                        </div>
                                        <p class="text-xs font-semibold text-gray-700 mb-0.5">Belum ada catatan fitur</p>
                                        <p class="text-[10px] text-gray-500">Klik "Tambah" untuk membuat catatan baru</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- TABLE: Catatan Proyek --}}
    @if($activeTab === 'proyek')
        <div class="bg-white rounded-xl shadow border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gradient-to-r from-purple-50 to-pink-50 border-b border-purple-100">
                            <th class="px-3 py-2 text-[10px] font-bold uppercase tracking-wider text-purple-700 w-[40px] text-center">
                                No
                            </th>
                            <th class="px-3 py-2 text-[10px] font-bold uppercase tracking-wider text-purple-700">
                                Pengguna
                            </th>
                            <th class="px-3 py-2 text-[10px] font-bold uppercase tracking-wider text-purple-700">
                                Waktu
                            </th>
                            <th class="px-3 py-2 text-[10px] font-bold uppercase tracking-wider text-purple-700">
                                Catatan Proyek
                            </th>
                            <th class="px-3 py-2 text-[10px] font-bold uppercase tracking-wider text-purple-700 text-center w-[60px]">
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($catatanNonFitur as $index => $item)
                            <tr class="group transition-all duration-200
                                {{ auth()->id() === $item->user_id 
                                    ? 'bg-purple-50/50 hover:bg-purple-100/50' 
                                    : 'hover:bg-gradient-to-r hover:from-purple-50/30 hover:to-pink-50/30' 
                                }}">
                                <td class="px-3 py-2 text-center">
                                    <div class="w-6 h-6 mx-auto rounded-lg flex items-center justify-center
                                        {{ auth()->id() === $item->user_id 
                                            ? 'bg-gradient-to-br from-purple-200 to-pink-200' 
                                            : 'bg-gradient-to-br from-purple-100 to-pink-100' 
                                        }}
                                        group-hover:from-purple-200 group-hover:to-pink-200 transition-all duration-200">
                                        <span class="text-[10px] font-bold text-purple-700">
                                            {{ $loop->iteration }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-3 py-2">
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-lg flex items-center justify-center text-[9px] font-bold border border-white shadow-sm
                                            {{ auth()->id() === $item->user_id 
                                                ? 'bg-gradient-to-br from-purple-200 to-pink-200 text-purple-700' 
                                                : 'bg-gradient-to-br from-purple-100 to-pink-100 text-purple-600' 
                                            }}">
                                            {{ strtoupper(substr($item->user->name ?? '?', 0, 2)) }}
                                        </div>
                                        <div>
                                            <div class="text-[10px] font-semibold text-gray-800">{{ $item->user->name ?? '-' }}</div>
                                            @if(auth()->id() === $item->user_id)
                                                <div class="text-[8px] text-purple-600 font-medium">Anda</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-3 py-2 text-[9px]">
                                    <div class="flex flex-col gap-1">
                                        <div class="flex items-center gap-1 text-slate-500">
                                            <i class="fa-solid fa-play text-[7px]"></i>
                                            <span>{{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('d M Y') }}</span>
                                        </div>
                                        <div class="flex items-center gap-1 {{ $item->tanggal_selesai || $item->proyek?->tanggal_selesai ? 'text-slate-400' : 'text-emerald-500 font-medium' }}">
                                            <i class="fa-solid fa-stop text-[7px]"></i>
                                            <span>
                                                @if ($item->tanggal_selesai)
                                                    {{ \Carbon\Carbon::parse($item->tanggal_selesai)->format('d M Y') }}
                                                @elseif ($item->proyek?->tanggal_selesai)
                                                    {{ \Carbon\Carbon::parse($item->proyek->tanggal_selesai)->format('d M Y') }}
                                                @else
                                                    Selesai
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-3 py-2">
                                    <p class="text-[10px] text-slate-600 leading-relaxed text-justify">
                                        {{ $item->catatan }}
                                    </p>
                                </td>
                                <td class="px-3 py-2">
                                    <div class="flex items-center justify-center gap-1 opacity-0 group-hover:opacity-100 transition-all duration-200">
                                        @if(auth()->id() === $item->user_id)
                                            <button 
                                                wire:click="edit({{ $item->id }})"
                                                class="w-6 h-6 flex items-center justify-center text-purple-600 bg-purple-50 hover:bg-purple-100 rounded transition-all duration-200 hover:scale-110" 
                                                title="Edit">
                                                <i class="fa-solid fa-pen-to-square text-[10px]"></i>
                                            </button>
                                            <button 
                                                wire:click="delete({{ $item->id }})"
                                                class="w-6 h-6 flex items-center justify-center text-rose-600 bg-rose-50 hover:bg-rose-100 rounded transition-all duration-200 hover:scale-110" 
                                                title="Hapus">
                                                <i class="fa-solid fa-trash text-[10px]"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-3 py-8 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-purple-50 to-pink-50 flex items-center justify-center mb-2">
                                            <i class="fa-solid fa-note-sticky text-purple-300 text-2xl"></i>
                                        </div>
                                        <p class="text-xs font-semibold text-gray-700 mb-0.5">Belum ada catatan proyek</p>
                                        <p class="text-[10px] text-gray-500">Klik "Tambah" untuk membuat catatan baru</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- Tombol Kembali --}}
    <div class="flex justify-start pt-4">
        <a href="{{ route('proyek') }}"
            class="px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-[10px] rounded-3xl shadow hover:bg-[#884fd9] transition">
            Kembali ke Daftar Proyek
        </a>
    </div>

    {{-- MODAL TAMBAH/EDIT Catatan --}}
     @if($openModal)
    <div class="fixed inset-0 flex items-center justify-center bg-black/40 backdrop-blur-sm z-50">
        <div class="bg-white rounded-2xl shadow-2xl 
                    p-5 w-[28rem] max-w-[90%] 
                    border border-gray-100">

            {{-- Judul --}}
            <h3 class="text-sm font-semibold text-transparent bg-clip-text 
                    bg-gradient-to-r from-blue-500 to-indigo-600
                    mb-4 text-center">
                {{ $editId ? 'Edit Catatan' : 'Buat Catatan' }}
            </h3>

            {{-- Content --}}
            <div class="space-y-3">

                {{-- Tanggal --}}
                <div class="flex gap-2">
                    <div class="w-1/2">
                        <label class="text-xs font-medium text-gray-700 mb-1 block">
                            Tanggal Mulai
                        </label>
                        <input 
                            type="date"
                            wire:model.live="tanggal_mulai"
                            class="text-xs border border-gray-300 rounded-lg p-2 w-full 
                                bg-white text-gray-800 
                                focus:ring-2 focus:ring-purple-400 
                                focus:border-purple-400 outline-none"
                        >
                        @error('tanggal_mulai')
                            <span class="text-[10px] text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="w-1/2">
                        <label class="text-xs font-medium text-gray-700 mb-1 block">
                            Tanggal Selesai
                        </label>
                        <input 
                            type="date"
                            wire:model.live="tanggal_selesai"
                            class="text-xs border border-gray-300 rounded-lg p-2 w-full 
                                bg-white text-gray-800 
                                focus:ring-2 focus:ring-purple-400 
                                focus:border-purple-400 outline-none"
                        >
                        @error('tanggal_selesai')
                            <span class="text-[10px] text-red-500">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                {{-- Deskripsi --}}
                <div>
                    <label class="text-xs font-medium text-gray-700 mb-1 block">
                        Catatan
                    </label>
                    <textarea
                        wire:model="catatanText"
                        rows="10"
                        placeholder="Tambahkan catatan..."
                        class="text-xs border border-gray-300 rounded-lg p-2.5 w-full 
                            bg-white text-gray-800 placeholder-gray-400 
                            focus:ring-2 focus:ring-purple-400 
                            focus:border-purple-400 outline-none resize-y"
                    ></textarea>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex justify-end gap-2 mt-4">
                <button 
                    wire:click="{{ $editId ? 'update' : 'save' }}"
                    class="bg-gradient-to-r from-blue-500 to-indigo-600
                        text-white px-4 py-1.5 
                        rounded-full shadow 
                        hover:shadow-md hover:scale-105 
                        transition text-xs font-medium"
                >
                    <i class="fa-solid fa-check mr-1 text-[10px]"></i>
                    {{ $editId ? 'Perbarui' : 'Simpan' }}
                </button>

                <button 
                    wire:click="$set('openModal', false)"
                    class="bg-gray-200 text-gray-700 px-4 py-1.5 
                        rounded-full hover:bg-gray-300 
                        hover:scale-105 transition 
                        text-xs font-medium"
                >
                    <i class="fa-solid fa-times mr-1 text-[10px]"></i>
                    Batal
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- Custom Styles --}}
    <style>
        /* Remove default dropdown arrow */
        .dropdown-custom {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: none;
        }
        .dropdown-custom::-ms-expand {
            display: none;
        }
        
        /* Date input styling */
        input[type="date"] {
            color-scheme: light;
        }

        input[type="date"]::-webkit-calendar-picker-indicator {
            cursor: pointer;
            border-radius: 4px;
            padding: 4px;
        }

        input[type="date"]::-webkit-calendar-picker-indicator:hover {
            background-color: rgba(59, 130, 246, 0.1);
        }

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