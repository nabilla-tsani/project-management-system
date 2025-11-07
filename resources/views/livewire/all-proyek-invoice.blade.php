<div class="relative bg-gray-50 rounded-2xl shadow-lg border border-gray-100 p-4">

    {{-- HEADER --}}
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-xl font-bold text-gray-800 flex items-center gap-2">
            <i class="fa-solid fa-file-invoice text-blue-600"></i> Daftar Invoice
        </h3>
        <button wire:click="$set('openModal', true)"
                class="px-4 py-2 bg-blue-600 text-white rounded-xl shadow hover:bg-blue-700 hover:shadow-md 
                       transition-all duration-200 flex items-center gap-2 text-sm font-medium">
            <i class="fa-solid fa-plus"></i> Tambah Invoice
        </button>
    </div>


{{-- INFORMASI ANGGARAN PROYEK --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-4 text-center">

    {{-- Total Anggaran --}}
    <div class="space-y-1">
        <i class="fas fa-coins text-blue-500 text-base"></i>
        <p class="text-[11px] text-gray-500 uppercase tracking-wide">Total Anggaran</p>
        <p class="text-sm font-semibold text-gray-800">
            Rp {{ number_format($proyek->anggaran, 0, ',', '.') }}
        </p>
    </div>

    {{-- Total Diinvoice --}}
    <div class="space-y-1">
        <i class="fas fa-file-invoice text-blue-500 text-base"></i>
        <p class="text-[11px] text-gray-500 uppercase tracking-wide">Total Diinvoice</p>
        <p class="text-sm font-semibold text-blue-700">
            Rp {{ number_format($totalInvoice, 0, ',', '.') }}
        </p>
    </div>

    {{-- Sisa Belum Diinvoice --}}
    <div class="space-y-1">
        <i class="fas fa-wallet text-green-500 text-base"></i>
        <p class="text-[11px] text-gray-500 uppercase tracking-wide">Belum Diinvoice</p>
        <p class="text-sm font-semibold text-green-700">
            Rp {{ number_format($sisaInvoice, 0, ',', '.') }}
        </p>
    </div>

</div>



{{-- LIST INVOICE --}}
<div class="divide-y divide-gray-200 border border-gray-200 rounded-lg bg-white shadow-sm">
    @forelse($invoices as $invoice)
        <div class="p-4 hover:bg-gray-50 transition">

            {{-- Baris 1: Judul + Aksi --}}
            <div class="flex justify-between items-center">
                <div>
                    <h4 class="font-semibold text-gray-800 text-base">{{ $invoice->judul_invoice }}</h4>
                    <p class="text-sm text-gray-500">#{{ $invoice->nomor_invoice }}</p>
                </div>

                <div class="flex items-center gap-3">
                     <button wire:click="editInvoice({{ $invoice->id }})" 
                            class="text-yellow-600 hover:text-blue-800 text-sm">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </button>
                    <button wire:click="printInvoice({{ $invoice->id }})" 
                            class="text-blue-600 hover:text-blue-800 text-sm">
                        <i class="fa-solid fa-print"></i>
                    </button>
                    <button wire:click="deleteInvoice({{ $invoice->id }})" 
                            class="text-red-600 hover:text-red-800 text-sm">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </div>
            </div>

            {{-- Baris 2: Tanggal + Jumlah + Tombol + Status --}}
            <div class="flex flex-wrap justify-between items-center mt-2 text-gray-700">
                {{-- Info Tanggal & Jumlah --}}
                <div class="flex flex-col sm:flex-row sm:items-center sm:gap-4">
                    <p class="flex items-center gap-1.5 text-xs ">
                        <i class="fa-regular fa-calendar text-gray-500"></i>
                        {{ \Carbon\Carbon::parse($invoice->tanggal_invoice)->translatedFormat('j F Y') }}
                    </p>
                    <p class="flex items-center gap-1.5">
                        <i class="fa-solid fa-money-bill-wave text-blue-500"></i>
                        <span class="text-blue-600 font-semibold text-sm">
                            Rp {{ number_format($invoice->jumlah, 0, ',', '.') }}
                        </span>
                    </p>
                </div>

                {{-- Tombol Kwitansi & Status --}}
                <div class="flex items-center gap-2 mt-2 sm:mt-0">
                <div class="flex flex-col sm:flex-row sm:items-center gap-2 mt-2 sm:mt-0">
                    @if($invoice->status == 'dibayar')
                        <div>
                            <button 
                                wire:click="createKwitansi({{ $invoice->id }})"
                                class="bg-green-600 text-white text-xs px-3 py-1 rounded-lg hover:bg-green-700 transition flex items-center gap-1">
                                <i class="fa-solid fa-file-invoice"></i> Kwitansi
                            </button>

                            {{-- Tampilkan error khusus untuk invoice ini --}}
                            @if($errorMessage && $selectedInvoiceId == $invoice->id)
                                <div class="mt-1 bg-red-100 border border-red-300 text-red-700 text-xs p-2 rounded w-max">
                                    {{ $errorMessage }}
                                </div>
                            @endif
                        </div>
                    @endif
                </div>


                    <select wire:change="updateStatus({{ $invoice->id }}, $event.target.value)"
                    wire:key="status-{{ $invoice->id }}-{{ now()->timestamp }}"
                    class="text-xs font-semibold rounded-full pl-3 pr-6 py-1.5 border-0 focus:ring-2 focus:ring-blue-400 cursor-pointer
                        appearance-none transition-all duration-200
                        {{ $invoice->status === 'belum_dibayar' ? 'bg-red-100 text-red-700' : '' }}
                        {{ $invoice->status === 'diproses' ? 'bg-yellow-100 text-yellow-700' : '' }}
                        {{ $invoice->status === 'dibayar' ? 'bg-green-100 text-green-700' : '' }}">
                        <option value="belum_dibayar" {{ $invoice->status=='belum_dibayar'?'selected':'' }}>Belum Dibayar</option>
                        <option value="diproses" {{ $invoice->status=='diproses'?'selected':'' }}>Diproses</option>
                        <option value="dibayar" {{ $invoice->status=='dibayar'?'selected':'' }}>Dibayar</option>
                    </select>
                </div>
            </div>

            {{-- Baris 3: Keterangan --}}
            @if($invoice->keterangan)
                <p class="mt-2 text-xs text-gray-500 flex items-start gap-1.5">
                    <i class="fa-regular fa-note-sticky text-gray-400 mt-0.5"></i>
                    {{ $invoice->keterangan }}
                </p>
            @endif

        </div>
    @empty
        <div class="text-center text-gray-500 bg-gray-50 rounded-lg p-4 text-sm">
            Belum ada invoice
        </div>
    @endforelse
</div>


<!-- Modal Tambah / Edit -->
@if($openModal)
    <div class="fixed inset-0 flex items-center justify-center bg-black/40 backdrop-blur-sm z-50">
        <div class="bg-white shadow-2xl p-6 w-full max-w-2xl border border-gray-200">
            <h3 class="text-lg font-bold text-gray-800 mb-5 text-center flex items-center justify-center gap-2">
                <i class="fa-solid fa-file-invoice text-blue-600 text-xl"></i>
                {{ $isEdit ? 'Edit Invoice' : 'Buat Invoice' }}
            </h3>

            <form wire:submit.prevent="{{ $isEdit ? 'updateInvoice' : 'store' }}" class="space-y-4">
                <input type="text" wire:model.defer="judul_invoice" placeholder="Judul Invoice"
                    class="border border-gray-300 rounded-lg p-2.5 w-full text-sm focus:ring-2 focus:ring-blue-400">
                @error('judul_invoice') 
                    <div class="text-red-600 text-xs">{{ $message }}</div> 
                @enderror

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <input type="number" wire:model.defer="jumlah" placeholder="Jumlah"
                            class="border border-gray-300 rounded-lg p-2.5 w-full text-sm focus:ring-2 focus:ring-blue-400">
                        @error('jumlah') 
                            <div class="text-red-600 text-xs">{{ $message }}</div> 
                        @enderror
                    </div>
                    <div>
                        <input type="date" wire:model.defer="tanggal_invoice"
                            class="border border-gray-300 rounded-lg p-2.5 w-full text-sm focus:ring-2 focus:ring-blue-400">
                        @error('tanggal_invoice') 
                            <div class="text-red-600 text-xs">{{ $message }}</div> 
                        @enderror
                    </div>
                </div>

                <textarea wire:model.defer="keterangan" rows="4" placeholder="Keterangan"
                        class="border border-gray-300 rounded-lg p-2.5 w-full text-sm focus:ring-2 focus:ring-blue-400"></textarea>
                @error('keterangan') 
                    <div class="text-red-600 text-xs">{{ $message }}</div> 
                @enderror

                <div class="flex justify-end gap-2 mt-4">
                    <button wire:click="$set('openModal', false)" type="button"
                            class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 hover:scale-105 transition text-sm">
                        Batal
                    </button>
                    <button type="submit"
                            class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:scale-105 transition text-sm">
                        {{ $isEdit ? 'Update' : 'Simpan' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endif

    {{-- Modal Input Keterangan Kwitansi --}}
    @if($showKwitansiModal)
    <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-40 z-50">
        <div class="bg-white shadow-lg w-1/3 p-5"> {{-- w-96 = lebih lebar dari w-80 --}}
            <h3 class="text-sm font-semibold text-gray-800 mb-3 text-center">{{ $isEditingKwitansi ? 'Edit Kwitansi' : 'Buat Kwitansi' }}</h3>

            <label class="block text-xs text-gray-500 mb-1">Keterangan</label>
            <textarea wire:model="keteranganKwitansi" rows="4"
                class="w-full border-gray-300 rounded-md text-sm focus:ring focus:ring-blue-200"></textarea>

            <div class="mt-4 flex justify-end gap-2">
                <button wire:click="closeKwitansiModal"
                    class="px-3 py-1 text-xs border rounded-md text-gray-600 hover:bg-gray-100">Batal</button>
                <button wire:click="simpanKwitansi"
                    class="px-3 py-1 text-xs bg-blue-500 text-white rounded-md hover:bg-green-700">{{ $isEditingKwitansi ? 'Update' : 'Simpan' }}</button>
            </div>
        </div>
    </div>
    @endif


</div>
