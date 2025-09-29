<div x-data="{ openModal: false }"
     x-init="
        window.addEventListener('close-modal', () => {
            openModal = false;
        })
     ">


    {{-- Notifikasi sukses --}}
    @if (session()->has('message'))
        <div class="p-3 mb-4 text-green-800 bg-green-200 rounded">
            {{ session('message') }}
        </div>
    @endif

    {{-- Informasi Proyek --}}
    <div class="mb-6">
        <h2 class="text-xl font-bold mb-2">Detail Proyek</h2>
        <p><strong>Nama Proyek:</strong> {{ $proyek->nama_proyek }}</p>
        <p><strong>Total Harga Proyek:</strong> Rp {{ number_format($proyek->anggaran, 0, ',', '.') }}</p>
        <p><strong>Total Invoice:</strong> Rp {{ number_format($totalInvoice, 0, ',', '.') }}</p>
        <p><strong>Sisa Belum di-Invoice-kan:</strong> Rp {{ number_format($sisaInvoice, 0, ',', '.') }}</p>
    </div>

    {{-- Daftar Invoice --}}
    <h3 class="text-lg font-semibold mb-3">Daftar Invoice</h3>
    @if(count($invoices) > 0)
        <table class="w-full border-collapse border border-gray-300 mb-6">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border border-gray-300 px-2 py-1">No</th>
                    <th class="border border-gray-300 px-2 py-1">Nomor Invoice</th>
                    <th class="border border-gray-300 px-2 py-1">Judul</th>
                    <th class="border border-gray-300 px-2 py-1">Tanggal</th>
                    <th class="border border-gray-300 px-2 py-1">Jumlah</th>
                    <th class="border border-gray-300 px-2 py-1">Keterangan</th>
                    <th class="border border-gray-300 px-2 py-1">Status</th>
                    <th class="border border-gray-300 px-2 py-1 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoices as $index => $invoice)
    <tr>
        <td class="border border-gray-300 px-2 py-1 text-center">{{ $index + 1 }}</td>
        <td class="border border-gray-300 px-2 py-1">{{ $invoice->nomor_invoice }}</td>
        <td class="border border-gray-300 px-2 py-1">{{ $invoice->judul_invoice }}</td>
        <td class="border border-gray-300 px-2 py-1">{{ \Carbon\Carbon::parse($invoice->tanggal_invoice)->format('d/m/Y') }}</td>
        <td class="border border-gray-300 px-2 py-1 text-right">Rp {{ number_format($invoice->jumlah, 0, ',', '.') }}</td>
        <td class="border border-gray-300 px-2 py-1">{{ $invoice->keterangan }}</td>
        
        {{-- Dropdown Status --}}
        <td class="border border-gray-300 px-2 py-1 text-center">
            <select 
                wire:model.lazy="statuses.{{ $invoice->id }}" 
                wire:change="updateStatus({{ $invoice->id }}, $event.target.value)"
                class="w-full min-w-[100px] rounded px-2 pr-6 py-1 text-xs font-medium focus:outline-none
                    @if($invoice->status === 'belum_dibayar') border-red-400 text-red-700 bg-red-50
                    @elseif($invoice->status === 'diproses') border-yellow-400 text-yellow-700 bg-yellow-50
                    @elseif($invoice->status === 'dibayar') border-green-400 text-green-700 bg-green-50
                    @endif">
                <option value="belum_dibayar">Belum Dibayar</option>
                <option value="diproses">Diproses</option>
                <option value="dibayar">Dibayar</option>
            </select>
        </td>

        {{-- Tombol Buat Kwitansi --}}
        <td class="border px-3 py-2 text-center">
            @if($invoice->status === 'dibayar')
                <button wire:click="buatKwitansi({{ $invoice->id }})"
                        class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600">
                    Buat Kwitansi
                </button>
            @else
                <span class="text-gray-400 italic">-</span>
            @endif
        </td>



        {{-- Tombol Aksi --}}
        <td class="border border-gray-300 px-2 py-1 text-center flex justify-center space-x-2">
        {{-- Icon Print --}}
        <a href="{{ route('proyek-invoice.print', $invoice->id) }}" target="_blank"
        class="text-blue-600 hover:text-blue-800 cursor-pointer mr-2">
            <i class="fa-solid fa-print"></i>
        </a>

        {{-- Icon Hapus --}}
        <span x-data
        @click.prevent="if(confirm('Yakin ingin menghapus invoice ini?')) { $wire.deleteInvoice({{ $invoice->id }}) }"
        class="text-red-600 hover:text-red-800 cursor-pointer">
        <i class="fa-solid fa-trash"></i>
        </span>
    </td>

    </tr>
@endforeach

            </tbody>
        </table>
    @else
        <p class="text-gray-600 mb-6">Belum ada invoice untuk proyek ini.</p>
    @endif

    {{-- Tombol buka modal --}}
    <button @click="openModal = true" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
        + Tambah Invoice
    </button>

    {{-- Modal --}}
    <div x-show="openModal" x-cloak class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div @click.away="openModal = false" class="bg-white rounded shadow-lg w-full max-w-lg p-6">
            <h3 class="text-lg font-semibold mb-4">Tambah Invoice</h3>

            <form wire:submit.prevent="store" class="space-y-4">
                <div>
                    <label class="block mb-1 font-medium">Judul Invoice</label>
                    <input type="text" wire:model.defer="judul_invoice" class="w-full border rounded px-3 py-2">
                    @error('judul_invoice') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block mb-1 font-medium">Jumlah</label>
                    <input type="number" wire:model.defer="jumlah" class="w-full border rounded px-3 py-2">
                    @error('jumlah') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block mb-1 font-medium">Tanggal Invoice</label>
                    <input type="date" wire:model.defer="tanggal_invoice" class="w-full border rounded px-3 py-2">
                    @error('tanggal_invoice') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block mb-1 font-medium">Keterangan</label>
                    <textarea wire:model.defer="keterangan" class="w-full border rounded px-3 py-2"></textarea>
                    @error('keterangan') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="flex justify-end space-x-2">
                    <button type="button" @click="openModal = false" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">
                        Batal
                    </button>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
