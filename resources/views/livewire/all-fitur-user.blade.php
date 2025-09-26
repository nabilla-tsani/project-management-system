<div>
    {{-- Flash message --}}
    @if (session()->has('message'))
        <div class="bg-green-100 text-green-700 p-2 rounded mb-3">
            {{ session('message') }}
        </div>
    @endif

    {{-- Tombol tambah --}}
    <div class="flex justify-between items-center mb-3">
        <h4 class="text-md font-semibold">Daftar User</h4>
        <button wire:click="openModal" class="px-3 py-2 bg-blue-600 text-white rounded">
            + Tambah User
        </button>
    </div>

    {{-- Table --}}
    <table class="w-full border border-gray-300 rounded mb-3">
        <thead>
            <tr class="bg-gray-100">
                <th class="p-2 border">Nama</th>
                <th class="p-2 border">Keterangan</th>
                <th class="p-2 border w-20">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($fiturUsers as $fu)
                <tr>
                    <td class="p-2 border">{{ $fu->user?->name ?? '-' }}</td>
                    <td class="p-2 border">{{ $fu->keterangan ?? '-' }}</td>
                    <td class="p-2 border text-center">
                        <button wire:click="edit({{ $fu->id }})" class="text-blue-600">Edit</button>
                        <button onclick="return confirm('Yakin hapus?')"
                                wire:click="delete({{ $fu->id }})"
                                class="text-red-600 ml-2">Hapus</button>
                    </td>
                </tr>
            @empty
                <tr><td colspan="3" class="text-center p-3">Belum ada user</td></tr>
            @endforelse
        </tbody>
    </table>

    {{-- Modal Tambah/Edit --}}
    @if($modalOpen)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white p-6 rounded shadow w-full max-w-md">
                <h3 class="text-lg font-semibold mb-4">{{ $fiturUserId ? 'Edit User' : 'Tambah User' }}</h3>

                <select wire:model="user_id" class="border rounded p-2 w-full mb-3">
                    <option value="">-- Pilih User --</option>
                    @foreach($userList as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
                @error('user_id') <div class="text-red-600 text-sm mb-2">{{ $message }}</div> @enderror

                <textarea wire:model="keterangan" placeholder="Keterangan"
                          class="border rounded p-2 w-full mb-3"></textarea>
                @error('keterangan') <div class="text-red-600 text-sm mb-2">{{ $message }}</div> @enderror

                <div class="flex justify-end">
                    <button wire:click="closeModal" class="px-3 py-2 bg-gray-300 rounded mr-2">
                        Batal
                    </button>
                    <button wire:click="save" class="px-3 py-2 bg-blue-600 text-white rounded">
                        Simpan
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
