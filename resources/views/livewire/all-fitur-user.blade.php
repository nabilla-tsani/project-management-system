<div>

{{-- Header --}}
<div class="flex justify-between items-center mb-4">
    @if($isManager)
        <button wire:click="openModal"
            class="flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm shadow transition rounded-lg">
            <i class="fas fa-plus"></i> User
        </button>
    @endif
</div>

{{-- Table --}}
<div class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm">
    <table class="w-full text-sm text-left border-collapse">
        <thead>
            <tr class="bg-gray-100 text-gray-700 text-sm">
                <th class="px-4 py-2 border text-center w-3/8">Nama</th>
                <th class="px-4 py-2 border text-center w-3/8">Keterangan</th>
                @if($isManager)
                    <th class="px-4 py-2 border text-center w-1/8">Aksi</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @forelse($fiturUsers as $fu)
                <tr class="hover:bg-gray-50 transition">
                    {{-- Nama --}}
                    <td class="px-4 py-2 border font-medium text-gray-800">
                        {{ $fu->user?->name ?? '-' }}
                    </td>

                    {{-- Keterangan (dibatasi agar tidak pecah tabel) --}}
                    <td class="px-4 py-2 border text-gray-600 whitespace-normal break-words">
                        {{ $fu->keterangan ?? '-' }}
                    </td>


                    {{-- Aksi --}}
                    @if($isManager)
                        <td class="py-2 border text-center">
                            <div class="flex justify-center">
                                <button wire:click="edit({{ $fu->id }})"
                                    class="p-1 text-blue-600 hover:bg-blue-100 rounded-lg transition"
                                    title="Edit">
                                    <i class="fas fa-pen"></i>
                                </button>
                                <button wire:click="delete({{ $fu->id }})"
                                    class="p-1 text-red-600 hover:bg-red-100 rounded-lg transition"
                                    title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="{{ $isManager ? 3 : 2 }}" class="text-center p-4 text-gray-500">
                        <i class="fas fa-info-circle mr-1"></i> Belum ada user
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>


    {{-- Modal Tambah/Edit --}}
    @if($modalOpen && $isManager)
        <div class="fixed inset-0 bg-black/40 backdrop-blur-sm flex items-center justify-center z-50 px-4">
            <div class="bg-white shadow-2xl w-full max-w-xl p-6 border border-gray-100">
                {{-- Header --}}
                <h3 class="text-lg font-bold text-gray-800 mb-6 text-center flex items-center justify-center gap-2">
                    <i class="fas fa-user-plus text-blue-600"></i>
                    {{ $fiturUserId ? 'Edit User' : 'Tambah User' }}
                </h3>

                {{-- User select --}}
                <select wire:model="user_id"
                    class="border border-gray-300 rounded-lg p-3 w-full mb-3 focus:ring-2 focus:ring-blue-400 text-sm">
                    <option value="">-- Pilih User --</option>
                    @foreach($userList as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
                @error('user_id')
                    <div class="text-red-600 text-sm mb-4">{{ $message }}</div>
                @enderror

                {{-- Keterangan --}}
                <textarea wire:model="keterangan" placeholder="Keterangan"
                    rows="4"
                    class="border border-gray-300 rounded-lg p-3 w-full mb-4 focus:ring-2 focus:ring-blue-400 text-sm resize-y"></textarea>
                @error('keterangan')
                    <div class="text-red-600 text-sm mb-4">{{ $message }}</div>
                @enderror

                {{-- Footer --}}
                <div class="flex justify-end gap-3">
                    <button wire:click="closeModal"
                        class="flex items-center gap-2 px-5 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition text-sm">
                        Batal
                    </button>
                    <button wire:click="save"
                        class="flex items-center gap-2 px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 shadow transition text-sm">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
