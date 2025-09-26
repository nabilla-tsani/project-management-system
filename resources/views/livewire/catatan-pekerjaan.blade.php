<div class="mt-2 border-t pt-2">
    @if(session()->has('message'))
        <div class="bg-green-100 text-green-700 p-2 rounded mb-2">
            {{ session('message') }}
        </div>
    @endif

    {{-- Daftar Catatan --}}
    <div class="space-y-3 mb-2">
        @forelse($catatan as $c)
            <table class="w-full border border-gray-300 rounded">
                <tbody>
                    <tr class="bg-gray-50">
                        {{-- Kolom 1 = Jenis --}}
                        <td class="w-1/6 border border-gray-300 font-semibold text-blue-700 p-2 align-top">
                            {{ $c->jenis }}
                        </td>

                        {{-- Kolom 2 = Catatan --}}
                        <td class="w-2/4 border border-gray-300 text-gray-700 p-2 align-top">
                            {{ $c->catatan }}
                        </td>

                        {{-- Kolom 3 = User --}}
                        <td class="w-1/6 border border-gray-300 text-gray-500 text-sm p-2 align-top">
                            {{ $c->user?->name ?? '-' }}
                        </td>

                        {{-- Kolom 4 = Aksi --}}
                        <td class="w-1/6 border border-gray-300 text-center p-2 align-top">
                            <div class="flex flex-row space-x-3 justify-center">
                                <button wire:click="openModal({{ $c->id }})"
                                    class="text-blue-600 hover:underline text-sm">Edit</button>
                                <button wire:click="delete({{ $c->id }})"
                                    class="text-red-600 hover:underline text-sm">Hapus</button>
                            </div>
                        </td>

                    </tr>
                </tbody>
            </table>
        @empty
            <div class="text-center text-gray-500 p-4 border rounded">Belum ada catatan</div>
        @endforelse
    </div>

    {{-- Tombol Tambah Catatan --}}
    <div class="mt-3">
        <button wire:click="openModal"
            class="px-3 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            Tambah Catatan
        </button>
    </div>

    {{-- Modal Tambah/Edit --}}
@if($modalOpen)
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded shadow w-full max-w-md">
            <h3 class="text-lg font-semibold mb-4">
                {{ $catatanId ? 'Edit Catatan' : 'Tambah Catatan' }}
            </h3>

            {{-- Dropdown Jenis --}}
            <select wire:model="jenis" class="border rounded p-2 w-full mb-3">
                <option value="">-- Pilih Jenis --</option>
                <option value="Bug">Bug</option>
                <option value="Pekerjaan">Pekerjaan</option>
            </select>
            @error('jenis')
                <div class="text-red-600 text-sm mb-2">{{ $message }}</div>
            @enderror

            <textarea wire:model="isiCatatan" placeholder="Catatan"
                class="border rounded p-2 w-full mb-3"></textarea>
            @error('isiCatatan')
                <div class="text-red-600 text-sm mb-2">{{ $message }}</div>
            @enderror

            <div class="flex justify-end">
                <button wire:click="$set('modalOpen', false)"
                    class="px-3 py-2 bg-gray-300 rounded mr-2">Batal</button>
                <button wire:click="save"
                    class="px-3 py-2 bg-blue-600 text-white rounded">Simpan</button>
            </div>
        </div>
    </div>
@endif

</div>
