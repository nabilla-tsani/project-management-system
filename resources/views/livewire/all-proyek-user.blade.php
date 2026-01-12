<div class="pt-0 p-2 space-y-2">
    @if (session()->has('message'))
        <div 
            x-data="{ show: true }"
            x-init="setTimeout(() => show = false, 1000)"
            x-show="show"
            x-transition.duration.500ms
            class="text-xs p-2 rounded bg-green-100 text-green-700 border border-green-300"
        >
            {{ session('message') }}
        </div>
    @endif

    {{-- Header: Judul & Tombol Tambah --}}
    <div class="flex items-center justify-between mb-3">
        <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <h2 class="text-sm font-semibold flex items-center gap-2 text-gray-700 pr-4">
                <i class="fa-solid fa-users text-blue-500 text-2xl"></i>
                Anggota Proyek ({{ $proyekUsers->count() }})
            </h2>
        </div>
            <input 
                type="text"
                wire:model.live="search" 
                placeholder="Cari anggota..."
                class="text-xs px-3 py-1.5 border border-gray-300 rounded-full focus:ring-1 focus:ring-[#5ca9ff] focus:border-[#5ca9ff] outline-none w-72"
            />
        </div>
        @if($this->isManajerProyek())
        <div class="flex items-center gap-2">
            <button wire:click="openModal"
                class="px-3 py-1.5 rounded-full text-white shadow 
                    transition-all duration-200 ease-out
                    text-xs font-medium
                    bg-gradient-to-r from-blue-500 to-indigo-600
                    transform hover:scale-105 hover:shadow-lg">
                <i class="fa-solid fa-plus mr-1 text-xs"></i>
                Tambah Anggota
            </button>
        </div>
        @endif
    </div>


    {{-- Tabel Modern --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        @if($proyekUsers->count() > 0)
            <table class="w-full table-fixed border border-gray-200 rounded-lg overflow-hidden">
    <thead>
        <tr class="bg-gradient-to-r from-blue-50 to-purple-50 border-b border-gray-200">
            <th class="px-3 py-2.5 text-center text-xs font-semibold text-gray-700 uppercase tracking-wide w-[220px]">
                Nama Anggota
            </th>
            <th class="px-3 py-2.5 text-center text-xs font-semibold text-gray-700 uppercase tracking-wide w-[140px]">
                Peran
            </th>
            <th class="px-3 py-2.5 text-center text-xs font-semibold text-gray-700 uppercase tracking-wide w-[280px]">
                Fitur Terlibat
            </th>
            <th class="px-3 py-2.5 text-center text-xs font-semibold text-gray-700 uppercase tracking-wide w-[360px]">
                Keterangan
            </th>
            @if($this->isManajerProyek())
            <th class="px-3 py-2.5 text-center text-xs font-semibold text-gray-700 uppercase tracking-wide w-[80px]">
                Aksi
            </th>
            @endif
        </tr>
    </thead>

    <tbody class="divide-y divide-gray-100">
        @foreach($proyekUsers as $pu)
            <tr class="hover:bg-gradient-to-r hover:from-blue-50/30 hover:to-purple-50/30 transition-colors duration-150">

                {{-- Nama Anggota --}}
                <td class="px-3 py-3 w-[220px] break-words">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-full bg-cyan-500 flex items-center justify-center text-white text-[10px] font-semibold shrink-0">
                            {{ strtoupper(substr($pu->user->name, 0, 1)) }}
                        </div>
                        <span class="text-xs font-semibold text-gray-800 break-words">
                            {{ $pu->user->name }}
                        </span>
                    </div>
                </td>

                {{-- Peran --}}
                <td class="px-3 py-3 w-[140px] break-words text-center">
                    <span
                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-medium text-white shadow-sm whitespace-normal
                        @if($pu->sebagai === 'manajer proyek') bg-gradient-to-br from-blue-400 to-purple-500
                        @elseif($pu->sebagai === 'programmer') bg-gradient-to-r from-blue-500 to-indigo-600
                        @elseif($pu->sebagai === 'tester') bg-gradient-to-r from-orange-500 to-yellow-400
                        @else bg-gradient-to-r from-gray-500 to-gray-600 @endif">

                        @if($pu->sebagai === 'manajer proyek')
                            <i class="fa-solid fa-user-tie text-[9px]"></i>
                        @elseif($pu->sebagai === 'programmer')
                            <i class="fa-solid fa-laptop-code text-[9px]"></i>
                        @elseif($pu->sebagai === 'tester')
                            <i class="fa-solid fa-vial-circle-check text-[9px]"></i>
                        @else
                            <i class="fa-solid fa-user text-[9px]"></i>
                        @endif

                        {{
                            $pu->sebagai === 'manajer proyek' ? 'Manajer' :
                            ($pu->sebagai === 'programmer' ? 'Programmer' :
                            ($pu->sebagai === 'tester' ? 'Tester' : ucfirst($pu->sebagai)))
                        }}
                    </span>
                </td>

                {{-- Fitur Terlibat --}}
                <td class="px-3 py-3 w-[280px]">
                    <div class="flex flex-wrap gap-1 break-words">
                        @forelse($pu->fitur as $fitur)
                            <span class="px-2 py-0.5 bg-gradient-to-r from-blue-50 to-purple-50 text-gray-700 text-[10px] rounded-full border border-blue-100 font-medium">
                                {{ $fitur->nama_fitur }}
                            </span>
                        @empty
                            <span class="text-[10px] text-gray-400 italic">Belum ada fitur</span>
                        @endforelse
                    </div>
                </td>

                {{-- Keterangan --}}
                <td class="px-3 py-3 w-[360px]">
                    @if($pu->keterangan)
                        <p class="text-[10px] text-gray-600 leading-relaxed break-words whitespace-normal text-justify">
                            {{ $pu->keterangan }}
                        </p>
                    @else
                        <span class="text-[10px] text-gray-800 italic">-</span>
                    @endif
                </td>

                {{-- Aksi --}}
                @if($this->isManajerProyek())
                <td class="px-3 py-3 w-[80px]">
                    <div class="flex items-center justify-center gap-1">
                        <button
                            wire:click="openModal({{ $pu->id }})"
                            class="text-blue-500 hover:text-blue-700 hover:bg-blue-50 p-1.5 rounded transition"
                            title="Edit">
                            <i class="fas fa-edit text-xs"></i>
                        </button>
                        <button
                            wire:click="delete({{ $pu->id }})"
                            class="text-red-500 hover:text-red-700 hover:bg-red-50 p-1.5 rounded transition"
                            title="Hapus">
                            <i class="fas fa-trash text-xs"></i>
                        </button>
                    </div>
                </td>
                @endif

            </tr>
        @endforeach
    </tbody>
</table>

        @else
            <div class="text-center text-gray-500 bg-gradient-to-r from-blue-50 to-purple-50 p-8">
                <i class="fa-solid fa-users text-3xl text-gray-300 mb-3"></i>
                <p class="font-medium text-xs">Proyek ini belum memiliki anggota.</p>
                <p class="text-[10px] text-gray-400 mt-1">Silakan tambahkan anggota proyek.</p>
            </div>
        @endif
    </div>

    {{-- Footer Tombol Kembali --}}
    <div class="flex justify-start pt-3">
        <a href="{{ route('proyek') }}"
           class="px-3 py-1.5 bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-xs rounded-full shadow hover:shadow-md transition font-medium">
            <i class="fa-solid fa-arrow-left mr-1 text-[10px]"></i> Kembali ke Daftar Proyek
        </a>
    </div>

    {{-- Modal --}}
    @if($showModal)
        <div class="fixed inset-0 flex items-center justify-center bg-black/40 backdrop-blur-sm z-50">
            <div class="bg-white rounded-2xl shadow-2xl p-5 w-[28rem] max-w-[90%] border border-gray-100">
                <h3 class="text-sm font-semibold text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600 mb-4 text-center">
                    {{ $editId ? 'Edit Anggota' : 'Tambah Anggota' }}
                </h3>

                <div class="space-y-3">
                    <div>
                        <label class="text-xs font-medium text-gray-700 mb-1 block">Pilih Anggota</label>
                        <select wire:model="user_id"
                            class="text-xs border border-gray-300 rounded-lg p-2 w-full bg-white text-gray-800 
                                focus:ring-2 focus:ring-blue-400 focus:border-blue-400 outline-none">
                            <option value="">-- Pilih Anggota --</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="text-xs font-medium text-gray-700 mb-1 block">Peran</label>
                        <select wire:model="sebagai"
                            class="text-xs border border-gray-300 rounded-lg p-2 w-full bg-white text-gray-800 
                                focus:ring-2 focus:ring-blue-400 focus:border-blue-400 outline-none">
                            <option value="">-- Pilih Peran --</option>
                            <option value="manajer proyek">Manajer Proyek</option>
                            <option value="programmer">Programmer</option>
                            <option value="tester">Tester</option>
                        </select>
                    </div>

                    <div>
                        <label class="text-xs font-medium text-gray-700 mb-1 block">Keterangan</label>
                        <textarea wire:model="keterangan" placeholder="Tambahkan keterangan (opsional)"
                            rows="4"
                            class="text-xs border border-gray-300 rounded-lg p-2.5 w-full bg-white text-gray-800 
                                placeholder-gray-400 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 outline-none resize-y"></textarea>
                    </div>
                </div>

                <div class="flex justify-end gap-2 mt-4">
                    <button wire:click="save"
                        class="bg-gradient-to-r from-blue-500 to-indigo-600 text-white px-4 py-1.5 
                            rounded-full shadow hover:shadow-md hover:scale-105 transition text-xs font-medium">
                        <i class="fa-solid fa-check mr-1 text-[10px]"></i>
                        {{ $editId ? 'Perbarui' : 'Simpan' }}
                    </button>
                    <button wire:click="$set('showModal', false)"
                        class="bg-gray-200 text-gray-700 px-4 py-1.5 rounded-full hover:bg-gray-300 
                            hover:scale-105 transition text-xs font-medium">
                        <i class="fa-solid fa-times mr-1 text-[10px]"></i>
                        Batal
                    </button>
                </div>
            </div>
        </div>
    @endif


    {{-- Konfirmasi Delete --}}
    @if($confirmDelete)
        <div class="fixed inset-0 flex items-center justify-center bg-black/40 backdrop-blur-sm z-50">
            <div class="bg-white rounded-2xl shadow-2xl p-5 w-[26rem] border border-gray-100">

                <div class="text-center mb-4">
                    <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-exclamation-triangle text-red-500 text-lg"></i>
                    </div>
                    <h3 class="text-sm font-semibold text-red-600">
                        Konfirmasi Hapus Anggota
                    </h3>
                </div>

                @if(count($fiturTerlibat) > 0)
                    <p class="text-xs text-gray-700 mb-2 text-center">
                        Anggota ini masih terlibat dalam fitur berikut:
                    </p>

                    <ul class="mb-3 text-xs text-gray-800 list-disc pl-5 space-y-0.5 max-h-32 overflow-y-auto bg-gray-50 p-2.5 rounded-lg">
                        @foreach($fiturTerlibat as $f)
                            <li>{{ $f }}</li>
                        @endforeach
                    </ul>

                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-2.5 mb-4">
                        <p class="text-xs text-gray-700 leading-relaxed">
                            <i class="fas fa-exclamation-triangle text-yellow-500 mr-1"></i>
                            Menghapus anggota ini akan menghapusnya dari semua fitur dalam proyek. Tindakan ini tidak dapat dibatalkan.
                        </p>
                    </div>

                @else
                    <p class="text-xs text-gray-600 mb-4 text-center">
                        Apakah Anda yakin ingin menghapus anggota ini? Tindakan ini tidak dapat dibatalkan.
                    </p>
                @endif

                <div class="flex justify-center gap-2">
                    <button wire:click="$set('confirmDelete', false)"
                        class="bg-gray-200 text-xs text-gray-700 px-4 py-1.5 rounded-full hover:bg-gray-300 hover:scale-105 transition font-medium">
                        <i class="fa-solid fa-times mr-1 text-[10px]"></i>
                        Batal
                    </button>
                    <button wire:click="confirmDeleteAction"
                        class="bg-gradient-to-r from-red-500 to-red-600 text-xs text-white px-4 py-1.5 rounded-full shadow hover:shadow-md hover:scale-105 transition font-medium">
                        <i class="fa-solid fa-trash mr-1 text-[10px]"></i>
                        Ya, Hapus
                    </button>
                </div>

            </div>
        </div>
    @endif

</div>