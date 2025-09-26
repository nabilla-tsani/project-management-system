<div class="mt-6">
    {{-- HEADER + BUTTON TAMBAH --}}
    <div class="flex justify-between items-center mb-3">
        <h3 class="text-xl font-semibold">Daftar Fitur</h3>
        <button wire:click="openModal" 
                class="px-3 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            + Tambah Fitur
        </button>
    </div>

    {{-- TABEL LIST FITUR --}}
    <table class="w-full border border-gray-300 rounded">
        <thead>
            <tr class="bg-gray-100">
                <th class="p-2 border">Nama Fitur</th>
                <th class="p-2 border">Keterangan</th>
                <th class="p-2 border">Status</th>
                <th class="p-2 border">User</th>
                <th class="p-2 border w-48">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($fiturs as $fitur)
                <tr>
                    <td class="p-2 border">{{ $fitur->nama_fitur }}</td>
                    <td class="p-2 border">{{ $fitur->keterangan }}</td>
                    <td class="p-2 border">{{ $fitur->status_fitur }}</td>
                    <td class="p-2 border">
                        @if($fitur->anggota->count())
                            <ul class="list-disc pl-4">
                                @foreach($fitur->anggota as $anggota)
                                    <li>{{ $anggota->user?->name ?? '-' }}</li>
                                @endforeach
                            </ul>
                        @else
                            <span class="text-gray-400 text-sm">Belum ada</span>
                        @endif
                    </td>
                    <td class="p-2 border text-center space-x-2">
                        {{-- Tombol Expand Catatan --}}
                        <button wire:click="toggleCatatan({{ $fitur->id }})"
                                class="px-2 py-1 bg-gray-200 rounded hover:bg-gray-300 text-sm">
                            {{ isset($showCatatan[$fitur->id]) && $showCatatan[$fitur->id] ? 'Tutup Catatan' : 'Lihat Catatan' }}
                        </button>

                        {{-- Tombol Edit --}}
                        <button wire:click="openModal({{ $fitur->id }})" 
                                class="text-blue-600 hover:underline">
                            Edit
                        </button>

                        {{-- Tombol Hapus --}}
                        <button wire:click="delete({{ $fitur->id }})" 
                                onclick="return confirm('Yakin hapus fitur ini?')"
                                class="text-red-600 hover:underline">
                            Hapus
                        </button>

                        {{-- Tombol Manage User --}}
                        <button wire:click="openUserModal({{ $fitur->id }})" 
                                class="text-green-600 hover:underline">
                            Manage User
                        </button>
                    </td>
                </tr>

                {{-- Row untuk catatan --}}
                @if(isset($showCatatan[$fitur->id]) && $showCatatan[$fitur->id])
                    <tr>
                        <td colspan="5" class="bg-gray-50 p-2">
                            @livewire('catatan-pekerjaan', ['proyekFiturId' => $fitur->id], key('catatan-'.$fitur->id))
                        </td>
                    </tr>
                @endif
            @empty
                <tr>
                    <td colspan="5" class="text-center p-3 text-gray-500">Belum ada fitur</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- MODAL TAMBAH/EDIT FITUR --}}
    @if($modalOpen)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white p-6 rounded shadow w-full max-w-md">
                <h3 class="text-lg font-semibold mb-4">
                    {{ $fiturId ? 'Edit Fitur' : 'Tambah Fitur' }}
                </h3>

                <input type="text" wire:model.defer="nama_fitur" 
                       placeholder="Nama Fitur"
                       class="border rounded p-2 w-full mb-3">

                <textarea wire:model.defer="keterangan" 
                          placeholder="Keterangan"
                          class="border rounded p-2 w-full mb-3"></textarea>

                <select wire:model.defer="status_fitur" 
                        class="border rounded p-2 w-full mb-3">
                    <option value="">-- Pilih Status --</option>
                    @foreach($statusList as $status)
                        <option value="{{ $status }}">{{ $status }}</option>
                    @endforeach
                </select>

                <div class="flex justify-end">
                    <button wire:click="closeModal"
                            class="px-3 py-2 bg-gray-300 rounded mr-2">
                        Batal
                    </button>
                    <button wire:click="save"
                            class="px-3 py-2 bg-blue-600 text-white rounded">
                        Simpan
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- MODAL USER UNTUK FITUR --}}
    @if($userModalOpen)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white p-6 rounded shadow w-full max-w-2xl">
                <h3 class="text-lg font-semibold mb-4">
                    User untuk Fitur: {{ $selectedFitur?->nama_fitur }}
                </h3>

                @livewire('all-fitur-user', 
                    ['proyekFiturId' => $selectedFiturId], 
                    key('fitur-user-'.$selectedFiturId)
                )

                <div class="flex justify-end mt-4">
                    <button wire:click="closeUserModal"
                            class="px-3 py-2 bg-gray-300 rounded">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
