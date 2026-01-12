<div class="pt-0 p-2 space-y-2">

    {{-- Header: Judul & Tombol Tambah --}}
    <div class="flex items-center justify-between mb-3">
        <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <h2 class="text-sm font-semibold flex items-center gap-2 text-gray-700 pr-4">
                <i class="fa-solid fa-file-invoice-dollar text-blue-500 text-2xl"></i>
                Daftar Tagihan ({{ $invoices->count() }})
            </h2>
        </div>
            <input 
                type="text"
                wire:model.live="search" 
                placeholder="Cari tagihan..."
                class="text-xs px-3 py-1.5 border border-gray-300 rounded-full focus:ring-1 focus:ring-[#5ca9ff] focus:border-[#5ca9ff] outline-none w-72"
            />
        </div>
        <div class="flex items-center gap-2">
            <button wire:click="$set('openModal', true)"
                class="px-3 py-1.5 rounded-full text-white shadow 
                    transition-all duration-200 ease-out
                    text-xs font-medium
                    bg-gradient-to-r from-blue-500 to-indigo-600
                    transform hover:scale-105 hover:shadow-lg">
                <i class="fa-solid fa-plus mr-1 text-xs"></i>
                Buat Tagihan
            </button>
        </div>
    </div>



    {{-- INFORMASI ANGGARAN PROYEK --}}
    <div class="grid grid-cols-3 gap-2 mb-3">

        {{-- Total Anggaran --}}
        <div class="bg-gradient-to-br from-purple-500 to-pink-600 rounded-lg p-2.5 border border-gray-200">
            <div class="flex items-center gap-2 mb-1">
                <div class="w-7 h-7 rounded-lg bg-white flex items-center justify-center">
                    <i class="fas fa-coins text-pink-500 text-xs"></i>
                </div>
                <div class="flex-1">
                    <p class="text-[8px] text-white font-semibold uppercase tracking-wide">Total Anggaran</p>
                    <p class="text-xs font-bold text-white">
                        Rp {{ number_format($proyek->anggaran, 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Total Ditagihkan --}}
        <div class="bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg p-2.5 border border-blue-200">
            <div class="flex items-center gap-2 mb-1">
                <div class="w-7 h-7 rounded-lg bg-white flex items-center justify-center">
                    <i class="fas fa-file-invoice text-blue-700 text-xs"></i>
                </div>
                <div class="flex-1">
                    <p class="text-[8px] text-white font-semibold uppercase tracking-wide">Total Ditagihkan</p>
                    <p class="text-xs font-bold text-white">
                        Rp {{ number_format($totalInvoice, 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Sisa Belum Ditagihkan --}}
        <div class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg p-2.5 border border-purple-200">
            <div class="flex items-center gap-2 mb-1">
                <div class="w-7 h-7 rounded-lg bg-white flex items-center justify-center">
                    <i class="fas fa-wallet text-purple-700 text-xs"></i>
                </div>
                <div class="flex-1">
                    <p class="text-[8px] text-white font-semibold uppercase tracking-wide">Sisa Anggaran</p>
                    <p class="text-xs font-bold text-white">
                        Rp {{ number_format($sisaInvoice, 0, ',', '.') }}
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

    {{-- LIST INVOICE --}}
    <div class="space-y-2"> 
        @forelse($invoices as $invoice)
            <div class="group bg-white rounded-xl p-3 border-2 border-gray-200 shadow-sm hover:shadow-lg hover:border-blue-500 transition-all duration-300 hover:-translate-y-0.5">

                {{-- Header: Judul & Harga --}}
                <div class="flex items-start justify-between mb-2 pb-2 border-b border-gray-100">
                    <div>
                        <h3 class="font-bold text-xs text-gray-800 mb-1">
                            {{ $invoice->judul_invoice }}
                        </h3>
                        
                        <div class="flex items-center gap-3 text-[10px]">
                            <p class="text-gray-500 flex items-center gap-1">
                                <i class="fa-solid fa-hashtag text-[8px]"></i>
                                {{ $invoice->nomor_invoice }}
                            </p>
                            
                            {{-- Tanggal --}}
                            <div class="flex items-center gap-1 text-[10px] text-gray-600">
                                <i class="fa-regular fa-calendar text-blue-500"></i>
                                <span>{{ \Carbon\Carbon::parse($invoice->tanggal_invoice)->translatedFormat('j M Y') }}</span>
                            </div>

                            {{-- Status --}}
                            <select wire:change="updateStatus({{ $invoice->id }}, $event.target.value)"
                                wire:key="status-{{ $invoice->id }}-{{ now()->timestamp }}"
                                class="text-[9px] font-bold rounded-lg px-2 py-1 border-0 focus:ring-1 focus:ring-blue-400 cursor-pointer
                                    transition-all duration-200
                                    {{ $invoice->status === 'belum_dibayar' ? 'bg-red-100 text-red-700' : '' }}
                                    {{ $invoice->status === 'diproses' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                    {{ $invoice->status === 'dibayar' ? 'bg-emerald-100 text-emerald-700' : '' }}">
                                <option value="belum_dibayar" {{ $invoice->status=='belum_dibayar'?'selected':'' }}>Belum Dibayar</option>
                                <option value="diproses" {{ $invoice->status=='diproses'?'selected':'' }}>Diproses</option>
                                <option value="dibayar" {{ $invoice->status=='dibayar'?'selected':'' }}>Dibayar</option>
                            </select>

                            {{-- Button Kwitansi --}}
                            @if($invoice->status == 'dibayar')
                                @php
                                    $hasReceipt = $invoice->kwitansi !== null;
                                @endphp
                                <button 
                                    wire:click="createKwitansi({{ $invoice->id }})"
                                    class="text-white text-[9px] font-semibold px-2 py-1 rounded-lg hover:scale-105 transition-all flex items-center gap-1
                                        {{ $hasReceipt ? 'bg-blue-500 hover:bg-blue-600' : 'bg-purple-500 hover:bg-purple-600' }}">
                                    <i class="fa-solid fa-receipt text-[9px]"></i>
                                    {{ $hasReceipt ? 'Edit Kwitansi' : 'Buat Kwitansi' }}
                                </button>
                            @endif
                        </div>
                    </div>

                    <div class="text-right">
                        <div class="px-2 py-1 bg-gradient-to-r from-emerald-50 to-teal-50 rounded-lg border border-emerald-200 group-hover:from-emerald-100 group-hover:to-teal-100 group-hover:border-emerald-300 transition-all">
                            <p class="text-[8px] text-emerald-600 font-semibold mb-0.5">Nominal</p>
                            <p class="text-xs font-bold text-emerald-700">
                                Rp {{ number_format($invoice->jumlah, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>
                </div>


                {{-- Keterangan --}}
                @if($invoice->keterangan)
                    <div class="flex items-center justify-between gap-1">
                        {{-- Flex-1 memastikan teks memenuhi ruang yang tersedia --}}
                        <p class="text-[10px] text-gray-700 leading-relaxed flex-1">
                            <span class="font-semibold text-blue-500">Keterangan:</span> 
                            {{ $invoice->keterangan }}
                        </p>

                        <button wire:click="printInvoice({{ $invoice->id }})" 
                            class="w-6 h-6 flex-shrink-0 flex items-center justify-center text-purple-600 bg-purple-50 hover:bg-purple-100 rounded transition-all hover:scale-110"
                            title="Cetak">
                            <i class="fa-solid fa-print text-[10px]"></i>
                        </button>

                        <button wire:click="editInvoice({{ $invoice->id }})" 
                            class="w-6 h-6 flex items-center justify-center text-blue-600 bg-blue-50 hover:bg-blue-100 rounded transition-all hover:scale-110"
                            title="Edit">
                            <i class="fa-solid fa-pen-to-square text-[10px]"></i>
                        </button>

                        <button wire:click="askDelete({{ $invoice->id }})" 
                            class="w-6 h-6 flex items-center justify-center text-rose-600 bg-rose-50 hover:bg-rose-100 rounded transition-all hover:scale-110"
                            title="Hapus">
                            <i class="fa-solid fa-trash text-[10px]"></i>
                        </button>
                    </div>
                @endif

                {{-- Error Message --}}
                @if($errorMessage && $selectedInvoiceId == $invoice->id)
                    <div class="mt-2 bg-red-50 border border-red-200 text-red-700 text-[10px] p-2 rounded-lg">
                        <i class="fa-solid fa-exclamation-circle mr-1"></i>
                        {{ $errorMessage }}
                    </div>
                @endif

            </div>
        @empty
            <div class="flex flex-col items-center justify-center py-8">
                <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-blue-50 to-indigo-50 flex items-center justify-center mb-2">
                    <i class="fa-solid fa-file-invoice text-blue-300 text-2xl"></i>
                </div>
                <p class="text-xs font-semibold text-gray-700 mb-0.5">Belum ada tagihan</p>
                <p class="text-[10px] text-gray-500">Klik "Buat Tagihan" untuk menambahkan tagihan baru</p>
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

    {{-- Modal Tambah / Edit --}}
    @if($openModal)
    <div class="fixed inset-0 flex items-center justify-center bg-black/50 backdrop-blur-sm z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl p-5 w-full max-w-lg border border-gray-100 animate-fade-in">

            {{-- Header Modal --}}
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shadow">
                        <i class="fa-solid fa-file-invoice text-white text-xs"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-gray-800">
                            {{ $isEdit ? 'Edit Tagihan' : 'Buat Tagihan Baru' }}
                        </h3>
                        <p class="text-[9px] text-gray-500">Isi form di bawah</p>
                    </div>
                </div>
                <button 
                    wire:click="$set('openModal', false)"
                    class="w-7 h-7 rounded-lg hover:bg-gray-100 flex items-center justify-center transition-colors"
                >
                    <i class="fa-solid fa-times text-gray-400 text-xs"></i>
                </button>
            </div>

            {{-- Form --}}
            <form wire:submit.prevent="{{ $isEdit ? 'updateInvoice' : 'store' }}" class="space-y-3">
                
                {{-- Judul Invoice --}}
                <div>
                    <label class="text-[10px] font-semibold text-gray-700 mb-1 block flex items-center gap-1">
                        <i class="fa-solid fa-heading text-blue-500 text-[9px]"></i>
                        Judul Tagihan
                    </label>
                    <input type="text" wire:model.defer="judul_invoice" placeholder="Masukkan judul tagihan"
                        class="text-[10px] border border-gray-200 rounded-lg p-2 w-full bg-white text-gray-800
                            focus:ring-1 focus:ring-blue-400 focus:border-blue-400 outline-none transition-all">
                    @error('judul_invoice') 
                        <div class="text-red-600 text-[9px] mt-1">{{ $message }}</div> 
                    @enderror
                </div>

                {{-- Jumlah & Tanggal --}}
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="text-[10px] font-semibold text-gray-700 mb-1 block flex items-center gap-1">
                            <i class="fa-solid fa-money-bill text-emerald-500 text-[9px]"></i>
                            Nominal
                        </label>
                        <input type="number" wire:model.defer="jumlah" placeholder="0"
                            class="text-[10px] border border-gray-200 rounded-lg p-2 w-full bg-white text-gray-800
                                focus:ring-1 focus:ring-emerald-400 focus:border-emerald-400 outline-none transition-all">
                        @error('jumlah') 
                            <div class="text-red-600 text-[9px] mt-1">{{ $message }}</div> 
                        @enderror
                    </div>
                    <div>
                        <label class="text-[10px] font-semibold text-gray-700 mb-1 block flex items-center gap-1">
                            <i class="fa-solid fa-calendar text-indigo-500 text-[9px]"></i>
                            Tanggal
                        </label>
                        <input type="date" wire:model.defer="tanggal_invoice"
                            class="text-[10px] border border-gray-200 rounded-lg p-2 w-full bg-white text-gray-800
                                focus:ring-1 focus:ring-indigo-400 focus:border-indigo-400 outline-none transition-all">
                        @error('tanggal_invoice') 
                            <div class="text-red-600 text-[9px] mt-1">{{ $message }}</div> 
                        @enderror
                    </div>
                </div>

                {{-- Keterangan --}}
                <div>
                    <label class="text-[10px] font-semibold text-gray-700 mb-1 block flex items-center gap-1">
                        <i class="fa-solid fa-note-sticky text-purple-500 text-[9px]"></i>
                        Keterangan
                    </label>
                    <textarea wire:model.defer="keterangan" rows="5" placeholder="Tambahkan keterangan tagihan..."
                        class="text-[10px] border border-gray-200 rounded-lg p-2 w-full bg-white text-gray-800 placeholder-gray-400
                            focus:ring-1 focus:ring-purple-400 focus:border-purple-400 outline-none resize-none transition-all"></textarea>
                    @error('keterangan') 
                        <div class="text-red-600 text-[9px] mt-1">{{ $message }}</div> 
                    @enderror
                </div>

                {{-- Actions --}}
                <div class="flex justify-end gap-2 mt-4 pt-3 border-t border-gray-100">
                    <button 
                        wire:click="$set('openModal', false)" 
                        type="button"
                        class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 
                            hover:scale-105 transition-all duration-200 text-[10px] font-semibold flex items-center gap-1"
                    >
                        <i class="fa-solid fa-times text-[9px]"></i>
                        Batal
                    </button>
                    <button 
                        type="submit"
                        class="px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-lg shadow 
                            hover:shadow-lg hover:scale-105 transition-all duration-200 text-[10px] font-semibold flex items-center gap-1"
                    >
                        <i class="fa-solid fa-{{ $isEdit ? 'check' : 'save' }} text-[9px]"></i>
                        {{ $isEdit ? 'Perbarui' : 'Simpan' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- Modal Buat Kwitansi --}}
    @if($showKwitansiModal)
    <div class="fixed inset-0 flex items-center justify-center bg-black/50 backdrop-blur-sm z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl p-5 w-full max-w-md border border-gray-100 animate-fade-in">

            {{-- Header Modal --}}
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-purple-500 to-pink-600 flex items-center justify-center shadow">
                        <i class="fa-solid fa-receipt text-white text-xs"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-gray-800">
                            {{ $isEditingKwitansi ? 'Edit Kwitansi' : 'Buat Kwitansi' }}
                        </h3>
                        <p class="text-[9px] text-gray-500">
                            {{ $isEditingKwitansi ? 'Kwitansi sudah ada, edit jika perlu' : 'Buat kwitansi untuk tagihan ini' }}
                        </p>
                    </div>
                </div>
                <button 
                    wire:click="closeKwitansiModal"
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
                    <input type="text" wire:model="judulKwitansi" placeholder="Masukkan judul kwitansi"
                        class="text-[10px] border border-gray-200 rounded-lg p-2 w-full bg-white text-gray-800
                            focus:ring-1 focus:ring-purple-400 focus:border-purple-400 outline-none transition-all">
                </div>

                {{-- Tanggal --}}
                <div>
                    <label class="text-[10px] font-semibold text-gray-700 mb-1 block flex items-center gap-1">
                        <i class="fa-solid fa-calendar text-indigo-500 text-[9px]"></i>
                        Tanggal
                    </label>
                    <input type="date" wire:model="tanggalKwitansi"
                        class="text-[10px] border border-gray-200 rounded-lg p-2 w-full bg-white text-gray-800
                            focus:ring-1 focus:ring-indigo-400 focus:border-indigo-400 outline-none transition-all">
                </div>
           
                {{-- Keterangan --}}
                <div>
                    <label class="text-[10px] font-semibold text-gray-700 mb-1 block flex items-center gap-1">
                        <i class="fa-solid fa-note-sticky text-amber-500 text-[9px]"></i>
                        Keterangan
                    </label>
                    <textarea wire:model="keteranganKwitansi" rows="5" placeholder="Tambahkan keterangan kwitansi..."
                        class="text-[10px] border border-gray-200 rounded-lg p-2 w-full bg-white text-gray-800 placeholder-gray-400
                            focus:ring-1 focus:ring-amber-400 focus:border-amber-400 outline-none resize-none transition-all"></textarea>
                </div>

                {{-- Actions --}}
                <div class="flex justify-end gap-2 mt-4 pt-3 border-t border-gray-100">
                    <button 
                        wire:click="closeKwitansiModal"
                        class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 
                            hover:scale-105 transition-all duration-200 text-[10px] font-semibold flex items-center gap-1"
                    >
                        <i class="fa-solid fa-times text-[9px]"></i>
                        Batal
                    </button>
                    <button 
                        wire:click="simpanKwitansi"
                        class="px-4 py-2 bg-gradient-to-r from-purple-500 to-pink-600 text-white rounded-lg shadow 
                            hover:shadow-lg hover:scale-105 transition-all duration-200 text-[10px] font-semibold flex items-center gap-1"
                    >
                        <i class="fa-solid fa-{{ $isEditingKwitansi ? 'check' : 'save' }} text-[9px]"></i>
                        {{ $isEditingKwitansi ? 'Perbarui' : 'Simpan' }}
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
                Hapus Tagihan?
            </h3>

            <p class="text-[10px] text-gray-600 mb-4 text-center leading-relaxed">
                Menghapus tagihan ini tidak dapat dibatalkan. Apakah Anda yakin ingin melanjutkan?
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
                    wire:click="confirmDeleteInvoice({{ $invoice->id }})"
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

        /* Custom select arrow */
        select {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 0.3rem center;
            background-repeat: no-repeat;
            background-size: 1.2em 1.2em;
            padding-right: 1.5rem;
        }
    </style>

</div>

