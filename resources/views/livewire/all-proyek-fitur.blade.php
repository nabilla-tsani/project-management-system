<div class="pt-0 p-2 space-y-2">
    {{-- Flash message --}}
        @if (session()->has('message'))
           <div 
                x-data="{ show: true }"
                x-init="setTimeout(() => show = false, 2000)" 
                x-show="show"
                x-transition.duration.500ms
                class="text-xs p-2 rounded bg-green-100 text-green-700 border border-green-300"
                style="position: relative; z-index: 10;"
            >
                {{ session('message') }}
            </div>
        @endif


    {{-- Header: Judul & Tombol Tambah --}}
    <div class="flex items-center justify-between mb-3">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <h2 class="text-sm font-semibold flex items-center gap-2 text-gray-700 pr-4">
                    <i class="fa-solid fa-layer-group text-blue-500 text-2xl"></i>
                    Daftar Fitur ({{ $fiturs->count() }})
                </h2>
            </div>
            <input 
                type="text"
                wire:model.live="search" 
                placeholder="Cari fitur..."
                class="text-xs px-3 py-1.5 border border-gray-300 rounded-full focus:ring-1 focus:ring-[#5ca9ff] focus:border-[#5ca9ff] outline-none w-72"
            />
            <div class="pl-2">
            <select wire:model.live="filterStatus"
                class="text-[10px] border rounded-full px-6 py-1.5">
                <option value="">Semua Status</option>
                <option value="belum_dimulai">Belum Dimulai</option>
                <option value="sedang_berjalan">Sedang Berjalan</option>
                <option value="ditunda">Ditunda</option>
                <option value="selesai">Selesai</option>
            </select>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <button wire:click="openAiModal"
                class="px-3 py-1.5 rounded-full text-white shadow
                    transition-all duration-200 ease-out
                    text-xs font-medium
                    bg-gradient-to-r from-cyan-400 to-purple-600
                    transform hover:scale-105 hover:shadow-lg">
                <i class="fa-solid fa-wand-magic-sparkles"></i> Buat Fitur dengan AI
            </button>
            <button wire:click="openModal"
                class="px-3 py-1.5 rounded-full text-white shadow 
                    transition-all duration-200 ease-out
                    text-xs font-medium
                    bg-gradient-to-r from-blue-500 to-indigo-600
                    transform hover:scale-105 hover:shadow-lg">
                <i class="fa-solid fa-plus mr-1 text-xs"></i>
                Tambah Fitur
            </button>

        </div>
    </div>

    {{-- Daftar Fitur --}}
    <div class="space-y-2">
        @forelse($fiturs as $fitur)
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm
                        hover:shadow-md hover:border-blue-300
                        transition-all duration-200 overflow-hidden">

                {{-- Baris Atas --}}
                <div class="grid grid-cols-12 items-center px-3 pt-2 pb-1">

                {{-- Nama Fitur --}}
                <div wire:click="openCatatan({{ $fitur->id }})"
                    class="col-span-3 flex items-center gap-2
                        text-xs font-medium text-gray-900 break-words cursor-pointer">

                    <div class="w-6 h-6 rounded-lg bg-gradient-to-br from-blue-400 to-blue-600
                                flex items-center justify-center shadow-sm flex-shrink-0
                                hover:scale-110 transition-transform">
                        <i class="fa-solid fa-cube text-white text-[10px]"></i>
                    </div>

                    <span class="leading-tight">
                        {{ $fitur->nama_fitur }}
                    </span>
                </div>


                {{-- Status --}}
                <div wire:click="openCatatan({{ $fitur->id }})"
                    class="col-span-1 flex justify-center cursor-pointer">

                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold
                        @if($fitur->status_fitur === 'selesai')
                            text-white bg-gradient-to-r from-green-500 to-green-600
                        @elseif($fitur->status_fitur === 'sedang_berjalan')
                            text-white bg-gradient-to-r from-orange-500 to-orange-600
                        @elseif($fitur->status_fitur === 'ditunda')
                            text-white bg-gradient-to-r from-red-500 to-red-600
                        @else
                            text-gray-700 bg-gray-200
                        @endif">

                        @if($fitur->status_fitur === 'selesai')
                            <i class="fa-solid fa-check text-[8px]"></i> Selesai 
                        @elseif($fitur->status_fitur === 'sedang_berjalan')
                            <i class="fa-solid fa-spinner text-[8px]"></i> Berjalan
                        @elseif($fitur->status_fitur === 'ditunda')
                            <i class="fa-solid fa-pause text-[8px]"></i> Ditunda
                        @else
                            <i class="fa-solid fa-clock text-[8px]"></i> Belum Dimulai
                        @endif
                    </span>
                </div>

                {{-- Anggota --}}
                <div wire:click.stop="openUserFitur({{ $fitur->id }})"
                    class=" pl-2 col-span-7 text-[10px] text-gray-600 hover:scale-105 flex items-center gap-1 cursor-pointer">

                    <button
                        title="Kelola Anggota"
                        class="transition hover:scale-110"
                    >
                        <i class="fa-solid fa-user-plus text-[12px]
                            bg-gradient-to-r from-blue-500 to-purple-600
                            bg-clip-text text-transparent">
                        </i>
                    </button>

                    @php
                        $countUser = $fitur->anggota->count();
                        $listUser = $fitur->anggota->pluck('user.name')->implode(', ');
                    @endphp

                    @if($countUser)
                        <div class="leading-relaxed break-words">
                            <span class="font-semibold text-gray-700">
                                {{ $countUser }} anggota:
                            </span>
                            <span class="text-gray-600">
                                {{ $listUser }}
                            </span>
                        </div>
                    @else
                        <span class="italic text-gray-400">Belum ada anggota pada fitur ini.</span>
                    @endif
                </div>

                {{-- Aksi --}}
                <div class="col-span-1 flex justify-end gap-2.5">
                    <button wire:click.stop="openCatatan({{ $fitur->id }})"
                        class="text-purple-600 text-xs hover:scale-150"
                        title="Lihat Catatan Fitur">
                        <i class="fa-solid fa-note-sticky text-[12px]"></i>
                    </button>
                    <button wire:click.stop="openModal({{ $fitur->id }})"
                        class="text-blue-500 text-xs hover:scale-150"
                        title="Edit">
                        <i class="fas fa-edit"></i>
                    </button>

                    <button wire:click="confirmDelete({{ $fitur->id }})"
                        class="text-red-500 text-xs hover:scale-150"
                        title="Hapus">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>

            {{-- Baris Bawah --}}
            <div wire:click="openCatatan({{ $fitur->id }})"
                class="grid grid-cols-12 items-start px-3 pb-2 text-xs cursor-pointer">

                <div class="col-span-12 text-gray-700 flex items-start gap-2 flex-wrap">

                    @php
                        $isSelesai = $fitur->status_fitur === 'selesai';

                        $isOverdue = !$isSelesai
                            && $fitur->target
                            && \Carbon\Carbon::now()->gt(\Carbon\Carbon::parse($fitur->target));
                    @endphp

                    {{-- Target --}}
                    @if($fitur->target)
                        <span class="flex items-center gap-1 text-[9px]
                            {{ $isOverdue ? 'text-red-600 font-semibold' : 'text-gray-700' }}">

                            {{-- Icon Target hanya jika TIDAK overdue --}}
                            @if(!$isOverdue)
                                <i class="fa-solid fa-crosshairs"></i>
                            @endif

                            {{-- Icon Warning hanya jika overdue --}}
                            @if($isOverdue)
                                <i class="fa-solid fa-triangle-exclamation"></i>
                            @endif

                            Target: {{ \Carbon\Carbon::parse($fitur->target)->format('d M Y') }}
                        </span>
                    @endif

                    {{-- Keterangan --}}
                    <p class="text-gray-600 text-justify leading-relaxed break-words text-[10px]">
                        Keterangan: {{ $fitur->keterangan ?? '-' }}
                    </p>

                </div>
            </div>
        </div>

    @empty
        @if (!empty($search))
            <div class="text-center text-gray-400 italic bg-gray-50 rounded-lg p-4 border border-gray-200 text-xs">
                Tidak ada fitur yang cocok dengan
                <span class="font-semibold text-[#5ca9ff]">"{{ $search }}"</span>.
            </div>
        @else
             <div class="text-center text-gray-500 bg-gradient-to-r from-blue-50 to-purple-50 p-8">
                <i class="fa-solid fa-layer-group text-3xl text-gray-300 mb-3"></i>
                <p class="font-medium text-xs">Proyek ini belum memiliki Fitur.</p>
                <p class="text-[10px] text-gray-400 mt-1">Silakan buat Fitur.</p>
            </div>
        @endif
    @endforelse
    </div>

    {{-- Tombol Kembali --}}
    <div class="flex justify-start pt-4">
        <a href="{{ route('proyek') }}"
            class="px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-[10px] rounded-3xl shadow hover:bg-[#884fd9] transition">
            Kembali ke Daftar Proyek
        </a>
    </div>

                
    {{-- MODAL KONFIRMASI DELETE --}}   
    @if ($showConfirmDelete)
        <div class="fixed inset-0 flex items-center justify-center bg-black/40 backdrop-blur-sm z-50">
            <div class="bg-white rounded-2xl shadow-2xl p-5 w-[26rem] border border-gray-100">

                <div class="text-center mb-4">
                    <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-exclamation-triangle text-red-500 text-lg"></i>
                    </div>
                    <h3 class="text-sm font-semibold text-red-600">
                        Konfirmasi Hapus Fitur
                    </h3>
                </div>
                    <p class="text-xs text-gray-600 mb-4 text-center">
                        Apakah Anda yakin ingin menghapus fitur ini? Tindakan ini tidak dapat dibatalkan.
                    </p>

                <div class="flex justify-center gap-2">
                    <button wire:click="$set('showConfirmDelete', false)"
                        class="bg-gray-200 text-xs text-gray-700 px-4 py-1.5 rounded-full hover:bg-gray-300 hover:scale-105 transition font-medium">
                        <i class="fa-solid fa-times mr-1 text-[10px]"></i>
                        Batal
                    </button>
                    <button wire:click="delete"
                        class="bg-gradient-to-r from-red-500 to-red-600 text-xs text-white px-4 py-1.5 rounded-full shadow hover:shadow-md hover:scale-105 transition font-medium">
                        <i class="fa-solid fa-trash mr-1 text-[10px]"></i>
                        Ya, Hapus
                    </button>
                </div>

            </div>
        </div>
    @endif


    {{-- MODAL TAMBAH/EDIT --}}
       @if($modalOpen)
            <div class="fixed inset-0 flex items-center justify-center bg-black/40 backdrop-blur-sm z-50">
                <div class="bg-white rounded-2xl shadow-2xl p-5 w-[28rem] max-w-[90%] border border-gray-100">

                    {{-- Judul --}}
                    <h3 class="text-sm font-semibold text-transparent bg-clip-text 
                            bg-gradient-to-r from-blue-600 to-purple-600 mb-4 text-center">
                        {{ $fiturId ? 'Edit Fitur' : 'Tambah Fitur' }}
                    </h3>

                    <div class="space-y-3">

                        {{-- Nama Fitur --}}
                        <div>
                            <label class="text-xs font-medium text-gray-700 mb-1 block">
                                Nama Fitur
                            </label>
                            <input type="text"
                                wire:model.defer="nama_fitur"
                                placeholder="Masukkan nama fitur"
                                class="text-xs border border-gray-300 rounded-lg p-2 w-full bg-white text-gray-800
                                    placeholder-gray-400 focus:ring-2 focus:ring-blue-400
                                    focus:border-blue-400 outline-none">
                        </div>

                        {{-- Deskripsi --}}
                        <div>
                            <label class="text-xs font-medium text-gray-700 mb-1 block">
                                Deskripsi
                            </label>
                            <textarea
                                wire:model.defer="keterangan"
                                rows="4"
                                placeholder="Tambahkan deskripsi atau catatan fitur"
                                class="text-xs border border-gray-300 rounded-lg p-2.5 w-full bg-white text-gray-800
                                    placeholder-gray-400 focus:ring-2 focus:ring-blue-400
                                    focus:border-blue-400 outline-none resize-y"></textarea>
                        </div>

                        {{-- Target --}}
                        <div>
                            <label class="text-xs font-medium text-gray-700 mb-1 block">
                                Target Tanggal
                            </label>
                            <input type="date"
                                wire:model.defer="target"
                                class="text-xs border border-gray-300 rounded-lg p-2 w-full bg-white text-gray-800
                                    focus:ring-2 focus:ring-blue-400
                                    focus:border-blue-400 outline-none">
                        </div>

                        {{-- Status --}}
                        <div>
                            <label class="text-xs font-medium text-gray-700 mb-1 block">
                                Status Proyek
                            </label>
                            <select
                                wire:model.defer="status_fitur"
                                class="text-xs border border-gray-300 rounded-lg p-2 w-full bg-white text-gray-800
                                    focus:ring-2 focus:ring-blue-400
                                    focus:border-blue-400 outline-none">
                                <option value="">-- Pilih Status --</option>
                                <option value="belum_dimulai">Belum Dimulai</option>
                                <option value="sedang_berjalan">Sedang Berjalan</option>
                                <option value="selesai">Selesai</option>
                                <option value="ditunda">Ditunda</option>
                            </select>
                        </div>

                    </div>

                    {{-- Tombol --}}
                    <div class="flex justify-end gap-2 mt-4">
                        <button wire:click="save"
                            class="bg-gradient-to-r from-blue-500 to-purple-600 text-white px-4 py-1.5
                                rounded-full shadow hover:shadow-md hover:scale-105
                                transition text-xs font-medium">
                            <i class="fa-solid fa-check mr-1 text-[10px]"></i>
                            {{ $fiturId ? 'Perbarui' : 'Simpan' }}
                        </button>

                        <button wire:click="closeModal"
                            class="bg-gray-200 text-gray-700 px-4 py-1.5 rounded-full
                                hover:bg-gray-300 hover:scale-105
                                transition text-xs font-medium">
                            <i class="fa-solid fa-times mr-1 text-[10px]"></i>
                            Batal
                        </button>
                    </div>

                </div>
            </div>
        @endif



    {{-- MODAL TAMBAH FITUR WITH AI--}}
    @if($aiModalOpen)
        <div class="fixed inset-0 flex items-center justify-center bg-black/40 backdrop-blur-sm z-50 p-4">
            <div class="bg-white rounded-2xl shadow-2xl p-5 w-[28rem] max-w-[90%] border border-gray-100">

                {{-- Header --}}
                <h3 class="text-sm font-semibold text-transparent bg-clip-text
                        bg-gradient-to-r from-blue-600 to-purple-600
                        mb-4 text-center flex items-center justify-center gap-2">
                    <i class="fa-solid fa-robot"></i>
                    Buat Fitur dengan AI
                </h3>

                {{-- Form --}}
                <div class="space-y-3">

                    {{-- Jumlah Fitur --}}
                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <label class="text-xs font-medium text-gray-700">
                                Jumlah Fitur yang Diinginkan
                            </label>
                            <span class="text-[11px] text-gray-500 italic">Maksimal 10</span>
                        </div>

                        <input type="number"
                            min="1"
                            max="10"
                            wire:model.defer="jumlah_fitur_ai"
                            oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                            class="text-xs border border-gray-300 rounded-lg p-2 w-full
                                    bg-white text-gray-800
                                    focus:ring-2 focus:ring-blue-400 focus:border-blue-400
                                    outline-none">
                    </div>

                    {{-- Prompt AI --}}
                    <div>
                        <label class="text-xs font-medium text-gray-700 mb-1 block">
                            Deskripsi / Prompt untuk AI
                        </label>

                        <textarea wire:model.defer="deskripsi_ai"
                                rows="5"
                                placeholder="Contoh: fitur autentikasi, laporan admin, dan integrasi pembayaran"
                                class="text-xs border border-gray-300 rounded-lg p-2.5 w-full
                                        bg-white text-gray-800 placeholder-gray-400
                                        focus:ring-2 focus:ring-blue-400 focus:border-blue-400
                                        outline-none resize-y"></textarea>
                    </div>

                    {{-- Loading --}}
                    <div wire:loading wire:target="generateFiturAI"
                        class="text-xs text-purple-600 italic">
                        AI sedang memproses, mohon tunggu...
                    </div>

                </div>

                {{-- Action Button --}}
                <div class="flex justify-end gap-2 mt-4">
                    <button wire:click="generateFiturAI"
                            wire:loading.attr="disabled"
                            wire:target="generateFiturAI"
                            class="bg-gradient-to-r from-blue-500 to-purple-600 text-white
                                px-4 py-1.5 rounded-full shadow
                                hover:shadow-md hover:scale-110 transition
                                text-xs font-medium
                                disabled:bg-gray-300 disabled:text-gray-500
                                disabled:opacity-50 disabled:cursor-not-allowed
                                disabled:hover:scale-100">
                        <i class="fa-solid fa-wand-magic-sparkles mr-1 text-[10px]"></i>
                        Buat Fitur dengan AI
                    </button>

                    <button wire:click="closeAiModal"
                            wire:loading.attr="disabled"
                            wire:target="generateFiturAI"
                            class="bg-gray-200 text-gray-700 px-4 py-1.5 rounded-full
                                hover:bg-gray-300 hover:scale-105 transition
                                text-xs font-medium
                                disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fa-solid fa-times mr-1 text-[10px]"></i>
                        Batal
                    </button>
                </div>

            </div>
        </div>
    @endif


    {{-- MODAL REVIEW AI --}}
    @if($showAiReview)
        <div class="fixed inset-0 flex items-center justify-center bg-black/40 backdrop-blur-sm z-50 p-4">
            <div class="bg-white rounded-2xl shadow-2xl p-5 w-[28rem] max-w-[90%] border border-gray-100">

                {{-- Header --}}
                <h3 class="text-sm font-semibold text-transparent bg-clip-text
                        bg-gradient-to-r from-blue-600 to-purple-600
                        mb-4 text-center flex items-center justify-center gap-2">
                    <i class="fa-solid fa-robot"></i>
                    Pratinjau Fitur dari AI
                </h3>

                {{-- Daftar fitur hasil AI --}}
                <div class="mb-4">
                    <label class="text-xs font-medium text-gray-700 mb-1 block">
                        Daftar Fitur yang Dihasilkan
                    </label>

                    <ul class="space-y-2 max-h-56 overflow-y-auto
                            border border-gray-200 rounded-lg p-2 bg-gray-50">
                        @foreach($aiFiturList as $fitur)
                            <li class="p-2 text-xs text-gray-700
                                    bg-white border border-gray-200 rounded-md">
                                {{ $fitur }}
                            </li>
                        @endforeach
                    </ul>
                </div>

                {{-- Revisi Prompt --}}
                <div class="space-y-3 mb-3">

                    <div>
                        <label class="text-xs font-medium text-gray-700 mb-1 block">
                            Revisi / Tambahan Prompt AI
                        </label>

                        <textarea
                            wire:model="revisi_deskripsi_ai"
                            x-data
                            x-ref="rev"
                            @clear-revisi.window="$refs.rev.value = ''"
                            rows="3"
                            placeholder="Contoh: tambahkan fitur pencarian dan filter data"
                            class="text-xs border border-gray-300 rounded-lg p-2.5 w-full
                                bg-white text-gray-800 placeholder-gray-400
                                focus:ring-2 focus:ring-blue-400 focus:border-blue-400
                                outline-none resize-y"></textarea>
                    </div>

                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <label class="text-xs font-medium text-gray-700">
                                Jumlah Fitur (Revisi)
                            </label>
                            <span class="text-[11px] text-gray-500 italic">
                                Bisa berbeda dari permintaan awal
                            </span>
                        </div>

                        <input type="number"
                            min="1"
                            max="10"
                            wire:model="jumlah_fitur_revisi"
                            placeholder="{{ $jumlah_fitur_ai }}"
                            oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                            class="text-xs border border-gray-300 rounded-lg p-2 w-full
                                    bg-white text-gray-800
                                    focus:ring-2 focus:ring-blue-400 focus:border-blue-400
                                    outline-none">
                    </div>

                </div>

                {{-- Loading --}}
                <div wire:loading wire:target="regenerateAiFitur"
                    class="text-xs text-purple-600 italic mb-2">
                    AI sedang membuat ulang fitur, mohon tunggu...
                </div>

                {{-- Tombol Aksi --}}
                <div class="flex justify-end gap-2 mt-4">

                    <button wire:click="regenerateAiFitur"
                            wire:loading.attr="disabled"
                            wire:target="regenerateAiFitur"
                            class="bg-gradient-to-r from-cyan-400 to-purple-600 text-white
                                px-4 py-1.5 rounded-full shadow
                                hover:shadow-md hover:scale-110 transition
                                text-xs font-medium
                                disabled:bg-gray-300 disabled:text-gray-500
                                disabled:opacity-50 disabled:cursor-not-allowed
                                disabled:hover:scale-100">
                        <i class="fa-solid fa-arrows-rotate mr-1 text-[10px]"></i>
                        Buat Ulang
                    </button>

                    <button wire:click="approveAiFitur"
                            wire:loading.attr="disabled"
                            wire:target="regenerateAiFitur"
                            class="bg-gradient-to-r from-blue-500 to-indigo-600 text-white
                                px-4 py-1.5 rounded-full shadow
                                hover:shadow-md hover:scale-110 transition
                                text-xs font-medium
                                disabled:bg-gray-300 disabled:text-gray-500
                                disabled:opacity-50 disabled:cursor-not-allowed
                                disabled:hover:scale-100">
                        <i class="fa-solid fa-check mr-1 text-[10px]"></i>
                        Setujui & Tambahkan
                    </button>

                    <button wire:click="$set('showAiReview', false)"
                            wire:loading.attr="disabled"
                            wire:target="regenerateAiFitur"
                            class="bg-gray-200 text-gray-700 px-4 py-1.5 rounded-full
                                hover:bg-gray-300 hover:scale-105 transition
                                text-xs font-medium
                                disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fa-solid fa-times mr-1 text-[10px]"></i>
                        Batal
                    </button>

                </div>

            </div>
        </div>
    @endif



@livewire('catatan-pekerjaan', ['proyekId' => $proyekId])
@livewire('all-fitur-user')

</div>
