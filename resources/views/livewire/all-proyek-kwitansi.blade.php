<div class="pt-0 p-2 space-y-2">

    {{-- Header: Judul & Tombol Tambah --}}
    <div class="flex items-center justify-between mb-3">
        <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <h2 class="text-sm font-semibold flex items-center gap-2 text-gray-700 pr-4">
                <i class="fa-solid fa-file-invoice text-blue-500 text-2xl"></i>
                Daftar Kwitansi ({{ $kwitansis->count() }})
            </h2>
        </div>
            <input 
                type="text"
                wire:model.live="search" 
                placeholder="Cari kwitansi..."
                class="text-xs px-3 py-1.5 border border-gray-300 rounded-full focus:ring-1 focus:ring-[#5ca9ff] focus:border-[#5ca9ff] outline-none w-72"
            />
        </div>
    </div>

    {{-- INFORMASI ANGGARAN PROYEK --}}
    <div class="grid grid-cols-2 gap-2 mb-3">

       {{-- Sudah Dibayar --}}
        <div class="bg-gradient-to-br from-cyan-500 to-teal-500 rounded-lg p-2.5 border border-gray-200">
            <div class="flex items-center gap-2 mb-1">
                {{-- Ikon dengan Glassmorphism effect --}}
                <div class="w-7 h-7 rounded-lg bg-white flex items-center justify-center backdrop-blur-sm">
                    <i class="fas fa-check-circle text-cyan-400 text-xs"></i>
                </div>
                <div class="flex-1">
                    <p class="text-[8px] text-white font-semibold uppercase tracking-wide">Sudah Dibayar</p>
                    <p class="text-xs font-bold text-white">
                        Rp {{ number_format($totalPaid, 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Belum Dibayar --}}
        <div class="bg-gradient-to-br from-purple-500 to-pink-600 rounded-lg p-2.5 border border-gray-200">
            <div class="flex items-center gap-2 mb-1">
                <div class="w-7 h-7 rounded-lg bg-white flex items-center justify-center">
                    <i class="fas fa-hourglass-half text-pink-500 text-xs"></i>
                </div>
                <div class="flex-1">
                    <p class="text-[8px] text-white font-semibold uppercase tracking-wide">Belum Dibayar</p>
                    <p class="text-xs font-bold text-white">
                        Rp {{ number_format($totalUnpaid, 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>

    </div>

    {{-- Flash Message --}}
    @if (session()->has('success'))
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
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    {{-- Daftar Kwitansi --}}
    <div class="space-y-2">
        @forelse($kwitansis as $kwitansi)
            <div class="group bg-white rounded-xl p-3 border-2 border-gray-200 shadow-sm hover:shadow-lg hover:border-blue-500 transition-all duration-300 hover:-translate-y-0.5">

                {{-- Header: Judul & Nominal --}}
                <div class="flex items-start justify-between mb-2 pb-2 border-b border-gray-100">
                    <div class="flex-1">
                        <h3 class="font-bold text-xs text-gray-800 mb-1 group-hover:text-blue-500 transition-colors">
                            {{ $kwitansi->judul_kwitansi }}
                        </h3>
                        <div class="flex items-center gap-2 text-[10px] text-gray-500">
                            <span class="flex items-center gap-1">
                                <i class="fa-solid fa-hashtag"></i>
                                {{ $kwitansi->nomor_kwitansi }}
                            </span>
                            <span class="text-gray-300">|</span>
                            <span class="flex items-center gap-1">
                                <i class="fa-solid fa-file-invoice"></i>
                                {{ $kwitansi->nomor_invoice }}
                            </span>
                            {{-- Tanggal --}}
                            <div class="flex items-center gap-1 text-[10px] text-gray-600">
                                <i class="fa-regular fa-calendar text-purple-500"></i>
                                <span>{{ \Carbon\Carbon::parse($kwitansi->tanggal_kwitansi)->translatedFormat('j M Y') }}</span>
                            </div>

                            {{-- Pembuat --}}
                            <div class="flex items-center gap-1 text-[9px] text-gray-500">
                                <i class="fa-solid fa-user text-indigo-500"></i>
                                <span>{{ $kwitansi->user?->name ?? 'User' }}</span>
                            </div>

                            {{-- Waktu Dibuat --}}
                            <div class="flex items-center gap-1 text-[9px] text-gray-400">
                                <i class="fa-solid fa-clock"></i>
                                <span>{{ $kwitansi->created_at->format('d M Y H:i') }}</span>
                            </div>
                            
                        </div>
                    </div>
                    <div class="text-right ml-3">
                        <div class="px-2 py-1 bg-gradient-to-r from-purple-50 to-pink-50 rounded-lg border border-purple-200 group-hover:from-purple-100 group-hover:to-pink-100 group-hover:border-purple-300 transition-all">
                            <p class="text-[8px] text-purple-600 font-semibold mb-0.5">Nominal</p>
                            <p class="text-xs font-bold text-purple-700">
                                Rp {{ number_format($kwitansi->jumlah, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Keterangan --}}
                @if(!empty($kwitansi->keterangan))
                    <div class="flex items-center justify-between gap-1">
                        
                        {{-- Teks Keterangan --}}
                        <p class="text-[10px] text-gray-700 leading-relaxed flex-1">
                            <span class="font-semibold text-blue-700">Keterangan:</span> 
                            {{ $kwitansi->keterangan }}
                        </p>

                        <div class="flex items-center gap-1 flex-shrink-0 transition-all duration-200">
                            <a href="{{ route('proyek-kwitansi.print', $kwitansi->id) }}" target="_blank"
                                class="w-6 h-6 flex items-center justify-center text-red-600 bg-red-50 hover:bg-red-100 rounded transition-all hover:scale-110"
                                title="Cetak PDF">
                                <i class="fa-solid fa-file-pdf text-[10px]"></i>
                            </a>

                            <button wire:click="openEditKwitansi({{ $kwitansi->id }})"
                                class="w-6 h-6 flex items-center justify-center text-blue-600 bg-blue-50 hover:bg-blue-100 rounded transition-all hover:scale-110"
                                title="Edit">
                                <i class="fa-solid fa-pen-to-square text-[10px]"></i>
                            </button>

                            <button wire:click="askDelete({{ $kwitansi->id }})"
                                class="w-6 h-6 flex items-center justify-center text-rose-600 bg-rose-50 hover:bg-rose-100 rounded transition-all hover:scale-110"
                                title="Hapus">
                                <i class="fa-solid fa-trash text-[10px]"></i>
                            </button>
                        </div>
                    </div>
                @endif

            </div>
        @empty
            <div class="flex flex-col items-center justify-center py-8">
                <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-purple-50 to-pink-50 flex items-center justify-center mb-2">
                    <i class="fa-solid fa-receipt text-purple-300 text-2xl"></i>
                </div>
                <p class="text-xs font-semibold text-gray-700 mb-0.5">Belum ada kwitansi</p>
                <p class="text-[10px] text-gray-500">Kwitansi akan muncul setelah tagihan dibayar</p>
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

    {{-- Modal Edit Kwitansi --}}
    @if($showEditModal)
    <div class="fixed inset-0 flex items-center justify-center bg-black/50 backdrop-blur-sm z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl p-5 w-full max-w-md border border-gray-100 animate-fade-in">

            {{-- Header Modal --}}
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-purple-500 to-pink-600 flex items-center justify-center shadow">
                        <i class="fa-solid fa-pen-to-square text-white text-xs"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-gray-800">
                            Edit Kwitansi
                        </h3>
                        <p class="text-[9px] text-gray-500">Ubah informasi kwitansi</p>
                    </div>
                </div>
                <button 
                    wire:click="$set('showEditModal', false)"
                    class="w-7 h-7 rounded-lg hover:bg-gray-100 flex items-center justify-center transition-colors"
                >
                    <i class="fa-solid fa-times text-gray-400 text-xs"></i>
                </button>
            </div>

            {{-- Form --}}
            <div class="space-y-3">
                
                {{-- Judul Kwitansi --}}
                <div>
                    <label class="text-[10px] font-semibold text-gray-700 mb-1 block flex items-center gap-1">
                        <i class="fa-solid fa-heading text-purple-500 text-[9px]"></i>
                        Judul Kwitansi
                    </label>
                    <input type="text" wire:model="edit_judul_kwitansi" placeholder="Masukkan judul kwitansi"
                        class="text-[10px] border border-gray-200 rounded-lg p-2 w-full bg-white text-gray-800
                            focus:ring-1 focus:ring-purple-400 focus:border-purple-400 outline-none transition-all">
                    @error('edit_judul_kwitansi') 
                        <div class="text-red-600 text-[9px] mt-1">{{ $message }}</div> 
                    @enderror
                </div>

                {{-- Tanggal --}}
                <div>
                    <label class="text-[10px] font-semibold text-gray-700 mb-1 block flex items-center gap-1">
                        <i class="fa-solid fa-calendar text-indigo-500 text-[9px]"></i>
                        Tanggal Kwitansi
                    </label>
                    <input type="date" wire:model="edit_tanggal_kwitansi"
                        class="text-[10px] border border-gray-200 rounded-lg p-2 w-full bg-white text-gray-800
                            focus:ring-1 focus:ring-indigo-400 focus:border-indigo-400 outline-none transition-all">
                    @error('edit_tanggal_kwitansi') 
                        <div class="text-red-600 text-[9px] mt-1">{{ $message }}</div> 
                    @enderror
                </div>

                {{-- Keterangan --}}
                <div>
                    <label class="text-[10px] font-semibold text-gray-700 mb-1 block flex items-center gap-1">
                        <i class="fa-solid fa-note-sticky text-amber-500 text-[9px]"></i>
                        Keterangan
                    </label>
                    <textarea wire:model="edit_keterangan" rows="5" placeholder="Tambahkan keterangan kwitansi..."
                        class="text-[10px] border border-gray-200 rounded-lg p-2 w-full bg-white text-gray-800 placeholder-gray-400
                            focus:ring-1 focus:ring-amber-400 focus:border-amber-400 outline-none resize-none transition-all"></textarea>
                    @error('edit_keterangan') 
                        <div class="text-red-600 text-[9px] mt-1">{{ $message }}</div> 
                    @enderror
                </div>

                {{-- Actions --}}
                <div class="flex justify-end gap-2 mt-4 pt-3 border-t border-gray-100">
                    <button 
                        wire:click="$set('showEditModal', false)"
                        type="button"
                        class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 
                            hover:scale-105 transition-all duration-200 text-[10px] font-semibold flex items-center gap-1"
                    >
                        <i class="fa-solid fa-times text-[9px]"></i>
                        Batal
                    </button>
                    <button 
                        wire:click="updateKwitansi"
                        type="button"
                        class="px-4 py-2 bg-gradient-to-r from-purple-500 to-pink-600 text-white rounded-lg shadow 
                            hover:shadow-lg hover:scale-105 transition-all duration-200 text-[10px] font-semibold flex items-center gap-1"
                    >
                        <i class="fa-solid fa-check text-[9px]"></i>
                        Perbarui
                    </button>
                </div>
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
                Hapus Kwitansi?
            </h3>

            <p class="text-[10px] text-gray-600 mb-4 text-center leading-relaxed">
                Menghapus kwitansi ini tidak dapat dibatalkan. Apakah Anda yakin ingin melanjutkan?
            </p>

            <div class="flex justify-center gap-2">
                <button 
                    wire:click="$set('confirmDelete', false)"
                    class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 
                        hover:scale-105 transition-all duration-200 text-[10px] font-semibold flex items-center gap-1"
                >
                    <i class="fa-solid fa-times text-[9px]"></i>
                    Batal
                </button>

                <button 
                    wire:click="confirmDeleteKwitansi"
                    class="px-4 py-2 bg-gradient-to-r from-red-500 to-rose-600 text-white rounded-lg shadow 
                        hover:shadow-lg hover:scale-105 transition-all duration-200 text-[10px] font-semibold flex items-center gap-1"
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
    </style>

</div>