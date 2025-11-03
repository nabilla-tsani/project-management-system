<div class="relative bg-gray-50 rounded-2xl shadow-lg border border-gray-100 p-4">

    {{-- Flash message --}}
    @if (session()->has('message'))
        <div class="mb-3 text-sm text-green-600 bg-green-100 p-2 rounded">
            {{ session('message') }}
        </div>
    @endif

    {{-- HEADER + BUTTON TAMBAH --}}
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
            <i class="fa-solid fa-list-check text-blue-600"></i> Daftar Fitur
        </h2>

        <div class="flex items-center gap-3">
            <button wire:click="openAiModal"
                class="px-3 py-1.5 bg-gradient-to-r from-cyan-400 to-purple-600 text-white rounded-xl shadow hover:bg-indigo-700 
                        hover:shadow-md transition-all duration-200 text-sm">
                <i class="fa-solid fa-wand-magic-sparkles"></i> Buat Fitur dengan AI
            </button>

            <button wire:click="openModal"
                class="px-4 py-1.5 bg-blue-600 text-white rounded-xl shadow hover:bg-blue-700 
                    hover:shadow-md transition-all duration-200 text-sm">
                <i class="fa-solid fa-plus mr-1"></i> Tambah Fitur
            </button>
        </div>
    </div>

    {{-- List fitur --}}
    <div class="space-y-3">
        @forelse($fiturs as $fitur)
            <div class="bg-white shadow-md rounded-xl p-4 border border-gray-100 hover:shadow-lg transition">
                {{-- Nama & status & user --}}
                <div class="flex flex-wrap items-center gap-3">
                    <p class="font-medium text-black">{{ $fitur->nama_fitur }}</p>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold text-white shrink-0
                        @if($fitur->status_fitur === 'Done') bg-gradient-to-r from-green-400 to-green-600
                        @elseif($fitur->status_fitur === 'In Progress') bg-gradient-to-r from-orange-400 to-orange-600
                        @elseif($fitur->status_fitur === 'Pending') bg-gradient-to-r from-red-400 to-red-600
                        @else bg-gradient-to-r from-gray-300 to-gray-500 @endif">
                        {{ ucfirst($fitur->status_fitur) }}
                    </span>
                    {{-- Anggota --}}
                    @if($fitur->anggota->count())
                        <span class="text-gray-600 text-xs flex items-center gap-1">
                            @if($isManajerProyek)
                                <button wire:click="openUserModal({{ $fitur->id }})"
                                    class="text-green-500 hover:text-green-800 transition text-xs"
                                    title="Tambah / Kelola User">
                                    <i class="fa-solid fa-user-plus text-[14px]"></i>
                                </button>
                            @else
                                <button wire:click="openUserModal({{ $fitur->id }})"
                                    class="text-green-500 hover:text-gray-700 transition text-xs cursor-pointer"
                                    title="Lihat User">
                                    <i class="fas fa-users text-[14px]"></i>
                                </button>
                            @endif
                            {{ $fitur->anggota->pluck('user.name')->implode(', ') }}
                        </span>
                    @else
                        <span class="text-gray-400 text-xs italic flex items-center gap-1">
                            <i class="fas fa-user-slash text-gray-400 text-[14px]"></i> Belum ada yang mengerjakan fitur ini
                        </span>
                    @endif
                </div>

                {{-- Keterangan --}}
                @if($fitur->keterangan)
                    <p class="mt-1 text-gray-700 text-sm py-2">{{ $fitur->keterangan }}</p>
                @endif

                {{-- Aksi --}}
                <div class="flex justify-between mt-3">
                    <button wire:click="toggleCatatan({{ $fitur->id }})"
                        class="text-gray-500 hover:text-gray-700 transition text-xs flex items-center gap-1">
                        <i class="fa-solid fa-note-sticky"></i>
                        {{ isset($showCatatan[$fitur->id]) && $showCatatan[$fitur->id] ? 'Tutup' : 'Catatan' }}
                    </button>

                    <div class="flex gap-3">
                        <button wire:click="openModal({{ $fitur->id }})"
                            class="text-blue-600 hover:text-blue-800 transition text-xs flex items-center gap-1">
                            <i class="fas fa-edit text-[14px]"></i>
                        </button>

                        <button wire:click="delete({{ $fitur->id }})"
                            class="text-red-600 hover:text-red-800 transition text-xs flex items-center gap-1">
                            <i class="fas fa-trash text-[14px]"></i>
                        </button>
                    </div>
                </div>

                {{-- Catatan --}}
                @if(isset($showCatatan[$fitur->id]) && $showCatatan[$fitur->id])
                    <div class="mt-3">
                        @livewire('catatan-pekerjaan', ['proyekFiturId' => $fitur->id], key('catatan-'.$fitur->id))
                    </div>
                @endif
            </div>
        @empty
            <div class="text-center text-gray-500 bg-gray-50 rounded-lg p-3 border border-gray-200 text-xs">
                Belum ada fitur ditambahkan.
            </div>
        @endforelse
    </div>

    {{-- MODAL TAMBAH/EDIT --}}
        @if($modalOpen)
            <div class="fixed inset-0 flex items-center justify-center bg-black/40 backdrop-blur-sm z-50">
                <div class="bg-white shadow-2xl p-6 w-[30rem] max-w-[65%] border border-gray-200">
                    <h3 class="text-xl font-bold text-gray-800 mb-5 text-center">
                        {{ $fiturId ? 'Edit Fitur' : 'Tambah Fitur' }}
                    </h3>

                    <input type="text" wire:model.defer="nama_fitur" placeholder="Nama Fitur"
                        class="border border-gray-300 rounded-lg p-3 w-full mb-4 
                            focus:ring-2 focus:ring-blue-400 text-sm">

                    <textarea wire:model.defer="keterangan" placeholder="Keterangan"
                        rows="6"
                        class="border border-gray-300 rounded-lg p-3 w-full mb-4 
                            focus:ring-2 focus:ring-blue-400 text-sm resize-y"></textarea>

                    <select wire:model.defer="status_fitur"
                        class="border border-gray-300 rounded-lg p-3 w-full mb-4 
                            focus:ring-2 focus:ring-blue-400 text-sm">
                        <option value="">-- Pilih Status --</option>
                        @foreach($statusList as $status)
                            <option value="{{ $status }}">{{ $status }}</option>
                        @endforeach
                    </select>

                    <div class="flex justify-end gap-3">
                        <button wire:click="closeModal"
                            class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg 
                                hover:bg-gray-300 hover:scale-105 transition text-sm">
                            Batal
                        </button>
                        <button wire:click="save"
                            class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow 
                                hover:scale-105 transition text-sm">
                            Simpan
                        </button>
                    </div>
                </div>
            </div>
        @endif


    {{-- MODAL USER --}}
    @if($userModalOpen)
        <div class="fixed inset-0 flex items-center justify-center bg-black/40 backdrop-blur-sm z-50 p-4">
            <div class="bg-white shadow-2xl w-full max-w-4xl max-h-screen overflow-y-auto rounded-lg border border-gray-200 p-5">
                
                {{-- Header --}}
                <h3 class="text-lg font-bold text-gray-800 mb-4 text-center flex items-center justify-center gap-2">
                    <i class="fas fa-users text-blue-600"></i>
                    User Fitur {{ $selectedFitur?->nama_fitur }}
                </h3>

                {{-- Konten --}}
                @livewire('all-fitur-user', ['proyekFiturId' => $selectedFiturId], key('fitur-user-'.$selectedFiturId))

                {{-- Footer --}}
                <div class="flex justify-end mt-4">
                    <button wire:click="closeUserModal"
                        class="bg-gray-200 text-gray-700 px-3 py-1.5 rounded-lg hover:bg-gray-300 hover:scale-105 transition text-sm">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    @endif


    {{-- MODAL TAMBAH/EDIT, USER, AI sudah lengkap seperti yang kamu kirim sebelumnya --}}
    @if($aiModalOpen)
        <div class="fixed inset-0 flex items-center justify-center bg-black/40 backdrop-blur-sm z-50 p-4">
            <div class="bg-white shadow-2xl w-[36rem] max-w-full rounded-lg border border-gray-200 p-5 relative">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                        <i class="fa-solid fa-robot text-indigo-600"></i> Buat Fitur dengan AI
                    </h3>
                    <button wire:click="closeAiModal" class="text-gray-400 hover:text-red-500 transition p-1 rounded-full">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <div wire:loading wire:target="generateFiturAI" class="mb-3 text-sm text-gray-600 italic">
                    Menghubungi AI, mohon tunggu...
                </div>

                <div class="space-y-3">
                    <div>
                        <div class="flex justify-between items-center">
                            <label class="text-xs font-semibold text-gray-600">Jumlah fitur</label>
                            <span class="text-[11px] text-gray-500 italic">Max. 10</span>
                        </div>
                        <input type="number" min="1" max="10" wire:model.defer="jumlah_fitur_ai"
                               oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                               class="border border-gray-300 rounded-lg p-2 w-full text-sm focus:ring-2 focus:ring-indigo-400">
                    </div>

                    <div>
                        <label class="text-xs font-semibold text-gray-600">Deskripsi untuk AI</label>
                        <textarea wire:model.defer="deskripsi_ai" rows="4"
                                  placeholder="Contoh: fokus pada autentikasi, laporan admin, dan integrasi pembayaran"
                                  class="border border-gray-300 rounded-lg p-2 w-full text-sm resize-y focus:ring-2 focus:ring-indigo-400"></textarea>
                    </div>

                    <div class="flex justify-end gap-3 pt-2">
                        <button wire:click="closeAiModal"
                                class="bg-gray-200 text-gray-700 px-4 py-1.5 rounded-lg hover:bg-gray-300 text-sm transition">
                            Batal
                        </button>
                        <button wire:click="generateFiturAI"
                                class="bg-indigo-600 text-white px-4 py-1.5 rounded-lg hover:bg-indigo-700 hover:scale-105 transition text-sm">
                            Generate
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

{{-- MODAL REVIEW AI --}}
@if($showAiReview)
    <div class="fixed inset-0 flex items-center justify-center bg-black/40 backdrop-blur-sm z-50 p-4">
        <div class="bg-white shadow-2xl w-[36rem] max-w-full rounded-lg border border-gray-200 p-5">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                    <i class="fa-solid fa-robot text-indigo-600"></i> Review Fitur AI
                </h3>
                <button wire:click="$set('showAiReview', false)" class="text-gray-400 hover:text-red-500 rounded-full p-1">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            {{-- Daftar fitur hasil AI --}}
            <ul class="space-y-2 mb-4 max-h-60 overflow-y-auto border border-gray-100 p-2 rounded">
                @foreach($aiFiturList as $fitur)
                    <li class="p-2 border border-gray-200 rounded text-gray-700 bg-gray-50">{{ $fitur }}</li>
                @endforeach
            </ul>

            {{-- Kolom revisi prompt --}}
            <div class="mb-3 space-y-3">
                <div>
                    <label class="text-xs font-semibold text-gray-600">Revisi atau perbaikan permintaan untuk AI</label>
                    <textarea wire:model.defer="revisi_deskripsi_ai" rows="3"
                            placeholder="Contoh: tambahkan fitur keamanan, hapus fitur laporan keuangan..."
                            class="border border-gray-300 rounded-lg p-2 w-full text-sm resize-y focus:ring-2 focus:ring-indigo-400"></textarea>
                </div>

                <div>
                    <div class="flex justify-between items-center">
                        <label class="text-xs font-semibold text-gray-600">Jumlah fitur yang ingin dihasilkan ulang</label>
                        <span class="text-[11px] text-gray-500 italic">Bisa berbeda dari permintaan awal</span>
                    </div>
                    <input type="number" min="1" max="10" wire:model.defer="jumlah_fitur_revisi"
                        placeholder="{{ $jumlah_fitur_ai }}" oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                        class="border border-gray-300 rounded-lg p-2 w-full text-sm focus:ring-2 focus:ring-indigo-400">
                </div>
            </div>


            {{-- Tombol aksi --}}
            <div class="flex justify-end gap-3">
                <button wire:click="$set('showAiReview', false)"
                        class="bg-gray-200 text-gray-700 px-4 py-1.5 rounded-lg hover:bg-gray-300 text-sm">
                    Batal
                </button>
                <button wire:click="regenerateAiFitur"
                        class="bg-purple-600 text-white px-4 py-1.5 rounded-lg hover:bg-purple-700 hover:scale-105 transition text-sm">
                    <i class="fa-solid fa-arrows-rotate"></i> Generate Ulang
                </button>
                <button wire:click="approveAiFitur"
                        class="bg-indigo-600 text-white px-4 py-1.5 rounded-lg hover:bg-indigo-700 hover:scale-105 transition text-sm">
                    Setuju & Tambahkan
                </button>
            </div>

            {{-- Loading indikator --}}
            <div wire:loading wire:target="regenerateAiFitur" class="mt-3 text-sm text-gray-600 italic">
                Menghubungi AI untuk menghasilkan fitur baru...
            </div>
        </div>
    </div>
    
@endif


</div>
