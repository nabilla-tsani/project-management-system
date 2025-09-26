<div>
    {{-- Flash message --}}
    @if (session()->has('message'))
        <div class="bg-green-100 text-green-700 p-2 rounded mb-3">
            {{ session('message') }}
        </div>
    @endif

    {{-- Tombol tambah --}}
    <button wire:click="openModal" class="mb-3 bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
        + Tambah User Proyek
    </button>

    {{-- Table --}}
    <table class="w-full border">
        <thead>
            <tr class="bg-gray-200">
                <th class="border px-2 py-1">Nama User</th>
                <th class="border px-2 py-1">Sebagai</th>
                <th class="border px-2 py-1">Keterangan</th>
                <th class="border px-2 py-1">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($proyekUsers as $pu)
                <tr>
                    <td class="border px-2 py-1">{{ $pu->user->name }}</td>
                    <td class="border px-2 py-1">{{ $pu->sebagai }}</td>
                    <td class="border px-2 py-1">{{ $pu->keterangan }}</td>
                    <td class="border px-2 py-1 text-center">
                        <button wire:click="openModal({{ $pu->id }})" class="bg-yellow-400 px-2 py-1 rounded">Edit</button>
                        <button wire:click="delete({{ $pu->id }})" 
                            onclick="return confirm('Yakin hapus?')"
                            class="bg-red-500 text-white px-2 py-1 rounded">Hapus</button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center py-2">Belum ada user pada proyek ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Modal --}}
    @if($showModal)
        <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white rounded shadow p-6 w-96">
                <h3 class="text-lg font-semibold mb-4">{{ $editId ? 'Edit User Proyek' : 'Tambah User Proyek' }}</h3>

                <select wire:model="user_id" class="border rounded p-2 w-full mb-3">
                    <option value="">-- Pilih User --</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>

                <select wire:model="sebagai" class="border rounded p-2 w-full mb-3">
                    <option value="">-- Sebagai --</option>
                    <option value="manajer proyek">Manajer Proyek</option>
                    <option value="programmer">Programmer</option>
                    <option value="tester">Tester</option>
                </select>

                <textarea wire:model="keterangan" placeholder="Keterangan"
                          class="border rounded p-2 w-full mb-3"></textarea>

                <div class="flex justify-end gap-2">
                    <button wire:click="save" class="bg-blue-500 text-white px-4 py-2 rounded">
                        Simpan
                    </button>
                    <button wire:click="$set('showModal', false)" class="bg-gray-400 text-white px-4 py-2 rounded">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
