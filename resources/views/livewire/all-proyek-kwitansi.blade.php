<div class="pt-0 p-2 space-y-2">

    {{-- Judul Halaman --}}
    <div class="flex items-center justify-between mb-4">
    <div class="flex items-center gap-3">
        <h2 class="text-md font-medium flex items-center gap-2 text-[#5ca9ff]">
            <i class="fa-solid fa-file-invoice"></i>
            List of Receipts ({{ $kwitansis->count() }})
        </h2>
        <input 
            type="text"
            wire:model.live="search" 
            placeholder="Find receipts..."
            class="text-xs px-3 py-1.5 border border-gray-500 rounded-3xl focus:ring-[#5ca9ff] focus:border-[#5ca9ff] outline-none w-96"
        />

    </div>
    </div>

    {{-- INFORMASI ANGGARAN PROYEK --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-center pb-4">

        {{-- Sudah Dibayar --}}
        <div class="space-y-1">
            <i class="fas fa-coins text-blue-500 text-base"></i>
            <p class="text-[11px] text-gray-500 uppercase tracking-wide">Paid</p>
            <p class="text-sm font-semibold text-blue-500">
                Rp {{ number_format($totalPaid, 0, ',', '.') }}
            </p>
        </div>

        {{-- Belum DIbayar --}}
        <div class="space-y-1">
            <i class="fas fa-file-invoice text-[#9c62ff] text-base"></i>
            <p class="text-[11px] text-gray-500 uppercase tracking-wide">Outstanding </p>
            <p class="text-sm font-semibold text-[#9c62ff]">
                Rp {{ number_format($totalUnpaid, 0, ',', '.') }}
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

    {{-- Daftar Kwitansi --}}
    <div>
        @forelse($kwitansis as $kwitansi)
            <div class="bg-white shadow-md border border-gray-300 p-3 text-sm text-gray-800 flex flex-col gap-1 mb-1">

    {{-- Baris 1: Judul & Harga --}}
    <div class="grid grid-cols-2">
        <div class="font-semibold text-gray-800 text-xs">
            {{ $kwitansi->judul_kwitansi }}
        </div>
        <div class="text-right font-bold text-green-700 text-xs block">
            <i class="fa-solid fa-money-bill-wave text-blue-500"></i>
            <span class="text-blue-600 font-semibold text-sm">
                Rp {{ number_format($kwitansi->jumlah, 0, ',', '.') }}
            </span>
        </div>
    </div>

    {{-- Baris 2: no.kwitansi, no.invoice, tanggal, pembuat --}}
    <div class="grid grid-cols-2">
        <div class="text-gray-700 text-xs font-base">
            {{ $kwitansi->nomor_kwitansi }}  <span class="text-gray-700 px-3">|</span>  
            {{ $kwitansi->nomor_invoice }}    <span class="text-gray-700 px-3">|</span> 
            <i class="fa-regular fa-calendar text-gray-500"></i>
            {{ \Carbon\Carbon::parse($kwitansi->tanggal_kwitansi)->format('d F Y') }}
        </div>
        <div class="text-right text-[10px] italic text-gray-500">
            <p>Created by: <span>{{ $kwitansi->user?->name ?? 'User' }} | </span>
            At: <span>{{ $kwitansi->created_at }}</span></p>
        </div>
    </div>

    {{-- Baris 3: desc, aksi --}}
   <div class="flex justify-between items-start">
            <div class="text-xs text-justify pr-3">
            @if(!empty($kwitansi->keterangan))
                <span class="text-gray-500">Description:</span>
                <span class="text-gray-500">{{ $kwitansi->keterangan }}</span>
            @else
                <span class="text-gray-400 italic">No description</span>
            @endif
        </div>

        <div class="flex items-center gap-4 text-xs text-gray-600">
            <a href="{{ route('proyek-kwitansi.print', $kwitansi->id) }}" target="_blank"
            class="text-[#9c62ff] hover:text-blue-800 flex items-center gap-1">
                <i class="fa-solid fa-file-pdf text-[#9c62ff]"></i> PDF
            </a>

            <button wire:click="openEditKwitansi({{ $kwitansi->id }})"
                    class="text-blue-500 hover:text-yellow-800 flex items-center gap-1">
                <i class="fa-solid fa-pen-to-square"></i>
            </button>

            <button wire:click="askDelete({{ $kwitansi->id }})"
                    class="text-red-600 hover:text-red-800 flex items-center gap-1">
                <i class="fa-solid fa-trash"></i>
            </button>
        </div>
    </div>

</div>


        @empty
            <div class="text-center py-5">
                <i class="fa-regular fa-file-lines text-gray-400 text-3xl mb-2"></i>
                <p class="text-gray-500 text-sm italic">No receipt yet</p>
            </div>
        @endforelse
    </div>

    {{-- Modal Edit Kwitansi --}}
    @if($showEditModal)
    <div class="fixed inset-0 flex items-center justify-center bg-black/40 backdrop-blur-sm z-50">
        <div class="bg-white shadow-lg w-full max-w-lg p-5">
                <h3 class="text-md font-medium text-[#9c62ff] mb-5 text-center">
                    Edit Receipt
                </h3>
            <label class="block text-xs text-gray-600 pb-1 pl-1">Receipt Title</label>
            <input type="text" wire:model="edit_judul_kwitansi" class="text-xs w-full border rounded-3xl p-2 mb-3" />
            @error('edit_judul_kwitansi') <div class="text-red-600 text-xs">{{ $message }}</div> @enderror
            <label class="block text-xs text-gray-600 pb-1 pl-1">Receipt Date</label>
            <input type="date" wire:model="edit_tanggal_kwitansi" class="text-xs w-full border rounded-3xl p-2 mb-3" />
            @error('edit_tanggal_kwitansi') <div class="text-red-600 text-xs">{{ $message }}</div> @enderror

            <label class="block text-xs text-gray-600 pb-1 pl-1">Description</label>
            <textarea wire:model="edit_keterangan" rows="7" class="text-xs w-full border rounded-xl p-2 mb-3"></textarea>
            @error('edit_keterangan') <div class="text-red-600 text-xs">{{ $message }}</div> @enderror

            <div class="flex justify-end gap-2">
                <button wire:click="$set('showEditModal', false)" type="button" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-3xl hover:bg-gray-300 
                    hover:scale-105 transition text-xs">Cancel</button>
                <button wire:click="updateKwitansi" type="button" class="bg-[#5ca9ff] text-white px-4 py-2 
                    rounded-3xl shadow hover:scale-105 transition text-xs">Update</button>
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

            <button wire:click="confirmDeleteKwitansi"
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
