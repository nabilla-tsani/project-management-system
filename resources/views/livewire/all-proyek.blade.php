<div class="p-4">
    <h2 class="text-xl font-bold mb-4">Manajemen Proyek</h2>

    @if (session()->has('message'))
        <div class="bg-green-100 text-green-700 p-2 rounded mb-3">
            {{ session('message') }}
        </div>
    @endif

    {{-- Search --}}
    <input type="text" wire:model.live="search" placeholder="Cari proyek..."
        class="border rounded p-2 mb-4 w-full">

    {{-- Form --}}
    <div class="bg-white p-4 rounded shadow mb-4">
        <div class="grid grid-cols-2 gap-3">
            <input type="text" wire:model="nama_proyek" placeholder="Nama Proyek"
                class="border rounded p-2">

            {{-- Dropdown Customer --}}
            <select wire:model="customer_id" class="border rounded p-2">
                <option value="">-- Pilih Customer --</option>
                @foreach($customers as $c)
                    <option value="{{ $c->id }}">{{ $c->nama }}</option>
                @endforeach
            </select>

            <input type="text" wire:model="deskripsi" placeholder="Deskripsi"
                class="border rounded p-2">
            <input type="text" wire:model="lokasi" placeholder="Lokasi"
                class="border rounded p-2">
            <input type="date" wire:model="tanggal_mulai" class="border rounded p-2">
            <input type="date" wire:model="tanggal_selesai" class="border rounded p-2">
            <input type="number" wire:model="anggaran" placeholder="Anggaran"
                class="border rounded p-2">
            <select wire:model="status" class="border rounded p-2">
                <option value="">-- Status --</option>
                <option value="belum_dimulai">Belum Dimulai</option>
                <option value="sedang_berjalan">Sedang Berjalan</option>
                <option value="selesai">Selesai</option>
            </select>
        </div>

        <div class="mt-3">
            @if($isEdit)
                <button wire:click="update" class="bg-blue-500 text-white px-4 py-2 rounded">Update</button>
                <button wire:click="resetForm" class="bg-gray-400 text-white px-4 py-2 rounded">Batal</button>
            @else
                <button wire:click="store" class="bg-green-500 text-white px-4 py-2 rounded">Simpan</button>
            @endif
        </div>
    </div>

    {{-- Table --}}
    <table class="w-full border">
        <thead>
            <tr class="bg-gray-200">
                <th class="border px-2 py-1">Nama</th>
                <th class="border px-2 py-1">Customer</th>
                <th class="border px-2 py-1">Deskripsi</th>
                <th class="border px-2 py-1">Lokasi</th>
                <th class="border px-2 py-1">Tanggal Mulai</th>
                <th class="border px-2 py-1">Tanggal Selesai</th>
                <th class="border px-2 py-1">Anggaran</th>
                <th class="border px-2 py-1">Status</th>
                <th class="border px-2 py-1">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($proyek as $p)
                <tr>
                    <td class="border px-2 py-1">{{ $p->nama_proyek }}</td>
                    <td class="border px-2 py-1">{{ $p->customer?->nama }}</td>
                    <td class="border px-2 py-1">{{ $p->deskripsi }}</td>
                    <td class="border px-2 py-1">{{ $p->lokasi }}</td>
                    <td class="border px-2 py-1">{{ $p->tanggal_mulai }}</td>
                    <td class="border px-2 py-1">{{ $p->tanggal_selesai }}</td>
                    <td class="border px-2 py-1">{{ number_format($p->anggaran) }}</td>
                    <td class="border px-2 py-1">{{ $p->status }}</td>
                    <td class="border px-2 py-1 text-center">
                        <button wire:click="edit({{ $p->id }})" class="bg-yellow-400 px-2 py-1 rounded">Edit</button>
                        <button 
                                x-data
                                x-on:click="
                                    if (confirm('Yakin hapus proyek ini?')) { 
                                        $wire.delete({{ $p->id }}); 
                                    }
                                "
                                class="bg-red-500 text-white px-2 py-1 rounded">
                                Hapus
                            </button>
                            <a href="{{ route('proyek.detail', $p->id) }}"
                                class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600">
                                Detail
                            </a>
                            
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-3">
        {{ $proyek->links() }}
    </div>
</div>
