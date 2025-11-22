<div class="pt-0 p-2 space-y-2">

    {{-- Header: Judul & Tombol Tambah --}}
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-3">
            <h2 class="text-md font-medium flex items-center gap-2 text-[#5ca9ff]">
                <i class="fa-solid fa-file-invoice-dollar"></i>
                Invoive List ({{ $invoices->count() }})
            </h2>
            <input 
                type="text"
                wire:model.live="search" 
                placeholder="Find invoive..."
                class="text-xs px-3 py-1.5 border border-gray-500 rounded-3xl focus:ring-[#5ca9ff] focus:border-[#5ca9ff] outline-none w-96"
            />
        </div>

        <div class="flex items-center gap-3">
            <button wire:click="$set('openModal', true)"
                class="px-4 py-1.5 rounded-3xl text-white shadow hover:shadow-md transition-all duration-200 text-xs"
                style="background-color: #5ca9ff;">
                <i class="fa-solid fa-plus mr-1"></i> Create Invoice
            </button>
        </div>
    </div>


{{-- INFORMASI ANGGARAN PROYEK --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-center pb-4">

    {{-- Total Anggaran --}}
    <div class="space-y-1">
        <i class="fas fa-coins text-gray-800 text-base"></i>
        <p class="text-[11px] text-gray-500 uppercase tracking-wide">Total Budget</p>
        <p class="text-sm font-semibold text-gray-800">
            Rp {{ number_format($proyek->anggaran, 0, ',', '.') }}
        </p>
    </div>

    {{-- Total Diinvoice --}}
    <div class="space-y-1">
        <i class="fas fa-file-invoice text-blue-500 text-base"></i>
        <p class="text-[11px] text-gray-500 uppercase tracking-wide">Total Invoiced Amount</p>
        <p class="text-sm font-semibold text-blue-700">
            Rp {{ number_format($totalInvoice, 0, ',', '.') }}
        </p>
    </div>

    {{-- Sisa Belum Diinvoice --}}
    <div class="space-y-1">
        <i class="fas fa-wallet text-[#9c62ff] text-base"></i>
        <p class="text-[11px] text-gray-500 uppercase tracking-wide">Unbilled Amount</p>
        <p class="text-sm font-semibold text-[#9c62ff]">
            Rp {{ number_format($sisaInvoice, 0, ',', '.') }}
        </p>
    </div>

</div>

{{-- Flash Message --}}
@if (session()->has('success'))
    <div 
       x-data="{ show: true }"
                x-init="setTimeout(() => show = false, 1000)"
                x-show="show"
                x-transition.duration.500ms
                class="text-xs p-2 rounded bg-green-100 text-green-700 border border-green-300"
    >
        {{ session('success') }}
    </div>
@endif

{{-- LIST INVOICE --}}
<div class="space-y-1"> 
    @forelse($invoices as $invoice)
        <div class="p-3 bg-white border border-gray-200 shadow hover:shadow-md transition">

            {{-- Baris 1: Judul & Harga --}}
            <div class="grid grid-cols-2">
                <div class="font-semibold text-gray-800 text-xs">
                    {{ $invoice->judul_invoice }}
                </div>
                <div class="text-right font-bold text-green-700 text-xs block">
                    <i class="fa-solid fa-money-bill-wave text-blue-500"></i>
                    <span class="text-blue-600 font-semibold text-sm">
                        Rp {{ number_format($invoice->jumlah, 0, ',', '.') }}
                    </span>
                </div>
            </div>

            {{-- Baris 2: No.inv, Tanggal, Status, Kwitansi, Tombol Aksi --}}
            <div class="flex flex-wrap justify-between items-center text-gray-700">
                <div class="flex flex-col sm:flex-row sm:items-center sm:gap-4">
                    <p class="text-xs text-gray-500">#{{ $invoice->nomor_invoice }}</p>
                    <p class="flex items-center gap-1.5 text-xs ">
                        <i class="fa-regular fa-calendar text-gray-500"></i>
                        {{ \Carbon\Carbon::parse($invoice->tanggal_invoice)->translatedFormat('j F Y') }}
                    </p>
                     <select wire:change="updateStatus({{ $invoice->id }}, $event.target.value)"
                    wire:key="status-{{ $invoice->id }}-{{ now()->timestamp }}"
                    class="text-[10px] font-semibold rounded-3xl px-6 py-1 border-0 focus:ring-2 focus:ring-blue-400 cursor-pointer
                        appearance-none transition-all duration-200
                        {{ $invoice->status === 'belum_dibayar' ? 'bg-red-100 text-red-700' : '' }}
                        {{ $invoice->status === 'diproses' ? 'bg-yellow-100 text-yellow-700' : '' }}
                        {{ $invoice->status === 'dibayar' ? 'bg-green-100 text-green-700' : '' }}">
                        <option value="belum_dibayar" {{ $invoice->status=='belum_dibayar'?'selected':'' }}>Unpaid</option>
                        <option value="diproses" {{ $invoice->status=='diproses'?'selected':'' }}>On Process</option>
                        <option value="dibayar" {{ $invoice->status=='dibayar'?'selected':'' }}>Paid</option>
                    </select>
                    <div class="flex flex-col sm:flex-row sm:items-center gap-2 rounded-3xl">
                    @if($invoice->status == 'dibayar')
                        <div>
                            @php
                                $hasReceipt = $invoice->kwitansi !== null;
                            @endphp

                            <button 
                                wire:click="createKwitansi({{ $invoice->id }})"
                                class="text-white text-[10px] px-3 py-1 rounded-3xl hover:scale-105 flex items-center gap-1
                                    {{ $hasReceipt ? 'bg-[#5ca9ff]' : 'bg-[#9c62ff]' }}">
                                    
                                <i class="fa-solid fa-file-invoice text-[11px]"></i>
                                {{ $hasReceipt ? 'Edit Receipt' : 'Create Receipt' }}
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
                </div>
                
                
            </div>

        {{-- Baris 3: Keterangan + Tombol --}}
        @if($invoice->keterangan)
        <div class="flex justify-between items-start mt-1">

            <p class="text-xs text-gray-500 text-justify flex-1 pr-3">
                <span class="font-semibold">Description:</span> 
                {{ $invoice->keterangan }}
            </p>

            <div class="flex items-center gap-3 shrink-0">
                <button wire:click="printInvoice({{ $invoice->id }})" 
                    class="text-[#9c62ff] hover:text-blue-800 text-xs">
                    <i class="fa-solid fa-print"></i>
                </button>

                <button wire:click="editInvoice({{ $invoice->id }})" 
                    class="text-blue-500 hover:text-blue-800 text-xs">
                    <i class="fa-solid fa-pen-to-square"></i>
                </button>

                <button wire:click="askDelete({{ $invoice->id }})" 
                    class="text-red-600 hover:text-red-800 text-xs">
                    <i class="fa-solid fa-trash"></i>
                </button>
            </div>

        </div>
        @endif


        </div>
    @empty
        <div class="text-center text-gray-500 p-4 text-xs italic">
            No invoices have been created yet
        </div>
    @endforelse
</div>


<!-- Modal Tambah / Edit -->
@if($openModal)
    <div class="fixed inset-0 flex items-center justify-center bg-black/40 backdrop-blur-sm z-50">
        <div class="bg-white shadow-2xl p-6 w-full max-w-xl border border-gray-200">
            <h3 class="text-lg font-md text-[#9c62ff] mb-5 text-center flex items-center justify-center gap-2">
                <i class="fa-solid fa-file-invoice text-[#9c62ff] text-sm"></i>
                {{ $isEdit ? 'Edit Invoice' : 'Create Invoice' }}
            </h3>

            <form wire:submit.prevent="{{ $isEdit ? 'updateInvoice' : 'store' }}" class="space-y-4">
                <input type="text" wire:model.defer="judul_invoice" placeholder="Judul Invoice"
                    class="border border-gray-300 rounded-3xl p-2.5 w-full text-xs focus:ring-2 focus:ring-blue-400">
                @error('judul_invoice') 
                    <div class="text-red-600 text-xs">{{ $message }}</div> 
                @enderror

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <input type="number" wire:model.defer="jumlah" placeholder="Jumlah"
                            class="border border-gray-300 rounded-3xl p-2.5 w-full text-xs focus:ring-2 focus:ring-blue-400">
                        @error('jumlah') 
                            <div class="text-red-600 text-xs">{{ $message }}</div> 
                        @enderror
                    </div>
                    <div>
                        <input type="date" wire:model.defer="tanggal_invoice"
                            class="border border-gray-300 rounded-3xl p-2.5 w-full text-xs focus:ring-2 focus:ring-blue-400">
                        @error('tanggal_invoice') 
                            <div class="text-red-600 text-xs">{{ $message }}</div> 
                        @enderror
                    </div>
                </div>

                <textarea wire:model.defer="keterangan" rows="8" placeholder="Keterangan"
                        class="border border-gray-300 rounded-lg p-2.5 w-full text-xs focus:ring-2 focus:ring-blue-400"></textarea>
                @error('keterangan') 
                    <div class="text-red-600 text-xs">{{ $message }}</div> 
                @enderror

                <div class="flex justify-end gap-2 mt-4">
                    <button wire:click="$set('openModal', false)" type="button"
                            class="bg-gray-200 text-gray-700 px-4 py-2 rounded-3xl hover:bg-gray-300 hover:scale-105 transition text-xs">
                        Cancel
                    </button>
                    <button type="submit"
                            class="bg-[#5ca9ff] text-white px-4 py-2 rounded-3xl shadow hover:scale-105 transition text-xs">
                        {{ $isEdit ? 'Update' : 'Save' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endif

    {{-- Modal Buat Kwitansi --}}
    @if($showKwitansiModal)
    <div class="fixed inset-0 flex items-center justify-center bg-black/40 backdrop-blur-sm z-50">
        <div class="bg-white shadow-lg w-1/3 p-5"> 
            <h3 class="text-sm font-semibold text-[#9c62ff] mb-3 text-center">{{ $isEditingKwitansi ? 'Edit Receipt' : 'Create Receipt' }}</h3>
            <h5 class="italic text-[12px] text-gray-500 text-center mb-1">{{ $isEditingKwitansi ? 'This receipt already exists. Would you like to edit it?' : '' }}</h5>

            <label class="block text-xs text-gray-500 mb-1">Receipt Title</label>
            <input type="text" 
                wire:model="judulKwitansi"
                class="w-full border-gray-300 rounded-3xl text-xs p-2 mb-3" />

            <label class="block text-xs text-gray-500 mb-1">Date</label>
            <input type="date"
                wire:model="tanggalKwitansi"
                class="w-full border-gray-300 rounded-3xl text-xs p-2 mb-3" />
           
            <label class="block text-xs text-gray-500 mb-1">Description</label>
            <textarea wire:model="keteranganKwitansi" rows="8"
                class="w-full border-gray-300 rounded-md text-xs focus:ring focus:ring-blue-200 p-2"></textarea>

            <div class="mt-4 flex justify-end gap-2">
                <button wire:click="closeKwitansiModal"
                    class="bg-gray-200 px-3 py-1 text-xs border rounded-3xl text-gray-600 hover:bg-gray-300">Cancel</button>
                <button wire:click="simpanKwitansi"
                    class="px-3 py-1 text-xs bg-[#5ca9ff] text-white rounded-3xl hover:bg-[#449bffff]">{{ $isEditingKwitansi ? 'Update' : 'Save' }}</button>
            </div>
        </div>
    </div>
    @endif

    {{-- Modal Konfirmasi Delete --}}
    @if($confirmDelete)
    <div class="fixed inset-0 bg-black/40 backdrop-blur-sm flex items-center justify-center z-50"
        x-data
        x-init="$el.classList.add('opacity-0'); setTimeout(()=> $el.classList.remove('opacity-0'), 10)"
    >
        <div class="bg-white p-6 shadow-xl w-full max-w-sm text-center animate-fadeIn">

            <h3 class="text-sm font-semibold text-red-500 mb-2">
                Delete Receipt?
            </h3>

            <p class="text-xs text-gray-600 mb-5 px-2 leading-relaxed">
                Deleting this receipt cannot be undone.  
                Are you sure you want to continue?
            </p>

            <div class="flex justify-center gap-3">
                <button wire:click="$set('confirmDelete', false)"
                    class="px-4 py-1.5 rounded-3xl bg-gray-300 text-gray-700 text-xs hover:bg-gray-400">
                    Cancel
                </button>

                <button wire:click="confirmDeleteInvoice({{ $invoice->id }})"
                    class="px-4 py-1.5 rounded-3xl bg-red-600 text-white text-xs hover:bg-red-700">
                    Yes, Delete
                </button>
            </div>
        </div>
    </div>

<style>
.animate-fadeIn {
    opacity: 0;
    transform: scale(0.95);
    animation: fadeInModal 0.2s forwards;
}
@keyframes fadeInModal {
    to {
        opacity: 1;
        transform: scale(1);
    }
}
</style>
@endif

</div>
