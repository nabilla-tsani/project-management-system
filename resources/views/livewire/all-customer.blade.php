<div class="p-4">

    <h2 class="text-xl font-bold mb-4">Manajemen Customer</h2>

    @if (session()->has('message'))
        <div class="bg-green-100 text-green-700 p-2 rounded mb-3">
            {{ session('message') }}
        </div>
    @endif

    {{-- Search --}}
    <input type="text" wire:model.live="search" placeholder="Cari customer..."
        class="border rounded p-2 mb-4 w-full">

    {{-- Form --}}
    <div class="bg-white p-4 rounded shadow mb-4">
        <div class="grid grid-cols-2 gap-3">
            <input type="text" wire:model="nama" placeholder="Nama"
                class="border rounded p-2">
            <input type="text" wire:model="alamat" placeholder="Alamat"
                class="border rounded p-2">
            <input type="text" wire:model="nomor_telepon" placeholder="Nomor Telepon"
                class="border rounded p-2">
            <input type="email" wire:model="email" placeholder="Email"
                class="border rounded p-2">
            <input type="text" wire:model="catatan" placeholder="Catatan"
                class="border rounded p-2">
            <select wire:model="status" class="border rounded p-2">
                <option value="">-- Status --</option>
                <option value="aktif">Aktif</option>
                <option value="tidak_aktif">Tidak Aktif</option>
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
                <th class="border px-2 py-1">Alamat</th>
                <th class="border px-2 py-1">Telepon</th>
                <th class="border px-2 py-1">Email</th>
                <th class="border px-2 py-1">Catatan</th>
                <th class="border px-2 py-1">Status</th>
                <th class="border px-2 py-1">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($customers as $c)
                <tr>
                    <td class="border px-2 py-1">{{ $c->nama }}</td>
                    <td class="border px-2 py-1">{{ $c->alamat }}</td>
                    <td class="border px-2 py-1">{{ $c->nomor_telepon }}</td>
                    <td class="border px-2 py-1">{{ $c->email }}</td>
                    <td class="border px-2 py-1">{{ $c->catatan }}</td>
                    <td class="border px-2 py-1">{{ $c->status }}</td>
                    <td class="border px-2 py-1 text-center">
                        <button wire:click="edit({{ $c->id }})"
                            class="bg-yellow-400 px-2 py-1 rounded">Edit</button>
                        <button 
                                x-data
                                x-on:click="
                                    if (confirm('Yakin hapus proyek ini?')) { 
                                        $wire.delete({{ $c->id }}); 
                                    }
                                "
                                class="bg-red-500 text-white px-2 py-1 rounded">
                                Hapus
                            </button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-3">
        {{ $customers->links() }}
    </div>
</div>
