<div class="min-h-screen bg-gray-100 text-gray-900 p-8 font-sans">
    <div class="max-w-7xl mx-auto">
        <!-- Title Halaman -->
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-4xl font-extrabold text-gray-800 flex items-center gap-3">
                <i class="fas fa-project-diagram text-blue-500"></i>
                <span>Manajemen Proyek</span>
            </h2>

            <!-- Tombol Tambah Proyek Minimalis -->
            <button wire:click="openModal" 
                class="bg-blue-500 text-white px-4 py-2 rounded-lg shadow-sm flex items-center gap-2 text-sm font-medium hover:brightness-105 transition">
                <i class="fas fa-plus"></i> Tambah
            </button>
        </div>


       <!-- Search + Icon -->
        <div class="relative w-full mb-6">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                <i class="fas fa-search"></i>
            </span>
            <input type="text" wire:model.live="search" placeholder="Cari proyek..."
                class="w-full pl-10 pr-3 py-2 rounded-lg bg-white border border-gray-300 text-gray-900 
                    focus:ring-1 focus:ring-blue-500 focus:border-transparent outline-none 
                    placeholder-gray-400 text-sm transition" />
        </div>


        <!-- Filter berdasarkan status -->
            <div class="flex gap-2 mb-4">
    <button wire:click="$set('statusFilter', '')"
        class="px-3 py-1 rounded-lg text-sm font-medium 
               {{ $statusFilter === '' ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
        Semua
    </button>
    <button wire:click="$set('statusFilter', 'belum_dimulai')"
        class="px-3 py-1 rounded-lg text-sm font-medium 
               {{ $statusFilter === 'belum_dimulai' ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
        Belum Dimulai
    </button>
    <button wire:click="$set('statusFilter', 'sedang_berjalan')"
        class="px-3 py-1 rounded-lg text-sm font-medium 
               {{ $statusFilter === 'sedang_berjalan' ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
        Sedang Berjalan
    </button>
    <button wire:click="$set('statusFilter', 'selesai')"
        class="px-3 py-1 rounded-lg text-sm font-medium 
               {{ $statusFilter === 'selesai' ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
        Selesai
    </button>
</div>


    <!-- Grid Card -->
    @if ($proyek->isEmpty())
        <div class="text-center py-10 text-gray-500 italic">
            Tidak ada Proyek.
        </div>
    @else
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-3 gap-3">
        @foreach($proyek as $p)
            <div class="relative bg-white rounded-xl p-4 flex flex-col justify-between shadow hover:shadow-lg transition transform hover:scale-105 duration-300 cursor-pointer"
                onclick="window.location='{{ route('proyek.detail', $p->id) }}'">

                <div>
                    <h3 class="text-base font-bold mb-1 truncate" title="{{ $p->nama_proyek }}">
                        {{ $p->nama_proyek }}
                    </h3>
                    <p class="text-sm text-gray-500 mb-1 truncate" title="{{ $p->customer?->nama }}">
                        {{ $p->customer?->nama }}
                    </p>
                </div>

                <div class="flex justify-between items-center text-sm mb-1 font-medium">
                    <!-- Status kiri -->
                    <span class="
                        @if ($p->status === 'belum_dimulai') text-blue-500
                        @elseif ($p->status === 'sedang_berjalan') text-yellow-500
                        @elseif ($p->status === 'selesai') text-green-500
                        @else text-gray-500
                        @endif
                    ">
                        @if ($p->status === 'belum_dimulai')
                            Belum Dimulai
                        @elseif ($p->status === 'sedang_berjalan')
                            Sedang Berjalan
                        @elseif ($p->status === 'selesai')
                            Selesai
                        @else
                            {{ $p->status }}
                        @endif
                    </span>

                    <!-- Tombol aksi kanan -->
                    <div class="flex gap-2">
                        <button wire:click.stop="edit({{ $p->id }})" class="text-gray-500 hover:text-gray-700 transition text-[12px]" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button wire:click.stop="deleteProyek({{ $p->id }})" class="text-gray-500 hover:text-gray-700 transition text-[12px]" title="Hapus">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>

                <!-- Status Bar -->
                <div class="mt-4 h-1 rounded-b-xl 
                    {{ $p->status == 'belum_dimulai' ? 'bg-gradient-to-r from-blue-400 to-blue-600' : 
                    ($p->status == 'sedang_berjalan' ? 'bg-gradient-to-r from-yellow-400 to-orange-500' : 
                    'bg-gradient-to-r from-green-400 to-teal-500') }}">
                </div>
            </div>
        @endforeach
    </div>


        <!-- Pagination -->
        <div class="mt-6 flex justify-center">
            {{ $proyek->links() }}
        </div>
        @endif
    </div>

{{-- Modal --}}
@if($showModal)
    <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-40 z-50
    bg-black/40 backdrop-blur-sm"> {{-- Tambahkan backdrop-blur-sm --}}
        <div class="bg-white w-2/3 max-w-2xl shadow-2xl transform transition-transform duration-300 ease-out animate-fadeIn
                    flex flex-col">
            
            {{-- Header --}}
            <div class="p-5 border-b border-gray-200">
                <h3 class="text-xl font-semibold text-gray-900 text-center">
                    {{ $isEdit ? 'Edit Proyek' : 'Tambah Proyek' }}
                </h3>
            </div>

            {{-- Body --}}
            <div class="p-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    {{-- Nama Proyek --}}
                    <div class="flex flex-col">
                        <label class="text-sm font-medium text-gray-700">Nama Proyek</label>
                        <input type="text" wire:model="nama_proyek"
                            class="border border-gray-300 rounded-md px-3 py-2 text-gray-900 focus:ring-1 focus:ring-blue-500 focus:outline-none text-sm" />
                    </div>

                    {{-- Customer --}}
                    <div class="flex flex-col">
                        <label class="text-sm font-medium text-gray-700">Customer</label>
                        <select wire:model="customer_id"
                            class="border border-gray-300 rounded-md px-3 py-2 text-gray-900 focus:ring-1 focus:ring-blue-500 focus:outline-none text-sm">
                            <option value="">-- Pilih Customer --</option>
                            @foreach($customers as $c)
                                <option value="{{ $c->id }}">{{ $c->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Deskripsi --}}
                    <div class="flex flex-col md:col-span-2">
                        <label class="text-sm font-medium text-gray-700">Deskripsi</label>
                        <textarea wire:model="deskripsi"
                            class="border border-gray-300 rounded-md px-3 py-2 text-gray-900 focus:ring-1 focus:ring-blue-500 focus:outline-none text-sm"
                            rows="2"></textarea>
                    </div>

                    {{-- Lokasi --}}
                    <div class="flex flex-col md:col-span-2">
                        <label class="text-sm font-medium text-gray-700">Lokasi</label>
                        <input type="text" wire:model="lokasi"
                            class="border border-gray-300 rounded-md px-3 py-2 text-gray-900 focus:ring-1 focus:ring-blue-500 focus:outline-none text-sm" />
                    </div>

                    {{-- Tanggal Mulai --}}
                    <div class="flex flex-col">
                        <label class="text-sm font-medium text-gray-700">Tanggal Mulai</label>
                        <input type="date" wire:model="tanggal_mulai"
                            class="border border-gray-300 rounded-md px-3 py-2 text-gray-900 focus:ring-1 focus:ring-blue-500 focus:outline-none text-sm" />
                    </div>

                    {{-- Tanggal Selesai --}}
                    <div class="flex flex-col">
                        <label class="text-sm font-medium text-gray-700">Tanggal Selesai</label>
                        <input type="date" wire:model="tanggal_selesai"
                            class="border border-gray-300 rounded-md px-3 py-2 text-gray-900 focus:ring-1 focus:ring-blue-500 focus:outline-none text-sm" />
                    </div>

                    {{-- Anggaran --}}
                    <div class="flex flex-col">
                        <label class="text-sm font-medium text-gray-700">Anggaran</label>
                        <input type="number" wire:model="anggaran"
                            class="border border-gray-300 rounded-md px-3 py-2 text-gray-900 focus:ring-1 focus:ring-blue-500 focus:outline-none text-sm" />
                    </div>

                    {{-- Status --}}
                    <div class="flex flex-col">
                        <label class="text-sm font-medium text-gray-700">Anggaran</label>
                       <select wire:model="status"
                                class="border border-gray-300 rounded-md px-3 py-2 text-gray-900 focus:ring-1 focus:ring-blue-500 focus:outline-none text-sm">
                                <option value="">-- Pilih Status --</option>
                                <option value="belum_dimulai">Belum Dimulai</option>
                                <option value="sedang_berjalan">Sedang Berjalan</option>
                                <option value="selesai">Selesai</option>
                            </select>
                    </div>

                    {{-- Tombol --}}
                    <div class="mt-5 flex justify-end gap-3 md:col-span-2">                            
                        @if($isEdit)
                                <button wire:click="update"
                                    class="bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-md shadow text-white text-sm font-medium transition">
                                    Update
                                </button>
                            @else
                                <button wire:click="store"
                                    class="bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-md shadow text-white text-sm font-medium transition">
                                    Simpan
                                </button>
                            @endif
                            <button wire:click="closeModal"
                                class="bg-gray-200 hover:bg-gray-300 px-4 py-2 rounded-md shadow text-gray-800 text-sm font-medium transition">
                                Batal
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        @keyframes fadeIn {
            0% { opacity: 0; transform: scale(0.95); }
            100% { opacity: 1; transform: scale(1); }
        }
        .animate-fadeIn {
            animation: fadeIn 0.3s forwards;
        }
    </style>
@endif

</div>
