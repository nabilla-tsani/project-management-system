<div class="pt-0 p-2 space-y-2">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <h2 class="text-sm font-semibold flex items-center gap-2 text-gray-700">
                <i class="fa-solid fa-circle-info text-blue-500 text-2xl"></i>
                Informasi Proyek
            </h2>
        </div>

        <div class="flex items-center gap-2">
            @if($proyek->status === 'selesai')
               @if(
                    $proyek->proyekUsers
                        ->where('user_id', auth()->id())
                        ->where('sebagai', 'manajer proyek')
                        ->count()
                )
                <button 
                    wire:click="generateReportWithAI({{ $proyek->id }})"
                    wire:loading.attr="disabled"
                    class="inline-flex items-center gap-2 px-3 py-1.5 bg-gradient-to-r from-purple-600 to-cyan-400 text-white text-xs font-medium rounded-lg shadow-sm hover:shadow-md hover:scale-105 transition-all disabled:opacity-70">

                    <span wire:loading.remove wire:target="generateReportWithAI({{ $proyek->id }})" class="inline-flex items-center gap-2">
                        <i class="fa-solid fa-file-export"></i>
                        Buat Laporan dengan AI
                    </span>

                    <span wire:loading.flex wire:target="generateReportWithAI({{ $proyek->id }})" class="items-center gap-2">
                        <i class="fa-solid fa-spinner fa-spin"></i>
                        Membuat...
                    </span>
                </button>
                @endif
            @endif
            <button 
                wire:click="generateProposalWithAI({{ $proyek->id }})"
                wire:loading.attr="disabled"
                class="inline-flex items-center gap-2 px-3 py-1.5 bg-gradient-to-r from-cyan-400 to-purple-600 text-white text-white text-xs font-medium rounded-lg shadow-sm hover:shadow-md hover:scale-105 transition-all disabled:opacity-70">

                <span wire:loading.remove wire:target="generateProposalWithAI({{ $proyek->id }})" class="inline-flex items-center gap-2">
                    <i class="fa-solid fa-wand-magic-sparkles"></i>
                    Buat Proposal dengan AI
                </span>

                <span wire:loading.flex wire:target="generateProposalWithAI({{ $proyek->id }})" class="items-center gap-2">
                    <i class="fa-solid fa-spinner fa-spin"></i>
                    Membuat...
                </span>
            </button>
        </div>
    </div>

    @if (session()->has('success'))
        <div 
            x-data="{ show: true }"
            x-init="setTimeout(() => show = false, 1000)"
            x-show="show"
            x-transition.duration.500ms
            class="text-xs p-2 rounded-lg bg-green-50 text-green-700 border border-green-200"
        >
            {{ session('success') }}
        </div>
    @endif

    {{-- Card Utama --}}
    <div class="bg-white rounded-xl border border-gray-300 shadow-xl p-4 hover:shadow-md transition-shadow">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 text-gray-800 text-xs">

            {{-- Customer --}}
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-user-tie text-blue-500 text-sm"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-gray-500 text-xs mb-0.5">Klien</p>
                    <p class="font-semibold text-gray-800 truncate">{{ $proyek->customer?->nama ?? '-' }}</p>
                </div>
            </div>

            {{-- Lokasi --}}
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-lg bg-red-50 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-map-marker-alt text-red-500 text-sm"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-gray-500 text-xs mb-0.5">Lokasi</p>
                    <p class="font-semibold text-gray-800 truncate">{{ $proyek->lokasi ?? '-' }}</p>
                </div>
            </div>

            {{-- Anggaran --}}
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-lg bg-green-50 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-coins text-green-500 text-sm"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-gray-500 text-xs mb-0.5">Anggaran</p>
                    <p class="font-semibold text-gray-800">Rp {{ number_format($proyek->anggaran,0,',','.') }}</p>
                </div>
                @if(
                    $proyek->proyekUsers
                        ->where('user_id', auth()->id())
                        ->where('sebagai', 'manajer proyek')
                        ->count()
                )
                    <button  
                        wire:click="edit({{ $proyek->id }})"
                        class="w-7 h-7 rounded-lg bg-gradient-to-r from-blue-500 to-indigo-600 text-white shadow-sm hover:shadow-md transition-all hover:scale-105 flex items-center justify-center">
                        <i class="fas fa-pen text-xs"></i>
                    </button>
                @endif

            </div>

            {{-- Status --}}
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-lg bg-yellow-50 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-flag text-yellow-500 text-sm"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-gray-500 text-xs mb-0.5">Status</p>
                    @php
                        $statusMap = [
                            'belum_dimulai'   => 'Belum Dimulai',
                            'sedang_berjalan' => 'Sedang Berjalan',
                            'selesai'         => 'Selesai',
                            'ditunda'         => 'Ditunda',
                        ];
                    @endphp

                    <span class="inline-block px-2.5 py-1 text-xs font-medium rounded-lg
                        @if($proyek->status === 'belum_dimulai') bg-blue-50 text-blue-600
                        @elseif($proyek->status === 'sedang_berjalan') bg-yellow-50 text-yellow-600
                        @elseif($proyek->status === 'selesai') bg-green-50 text-green-600
                        @elseif($proyek->status === 'ditunda') bg-red-50 text-red-600
                        @else bg-gray-50 text-gray-600 @endif">
                        {{ $statusMap[$proyek->status] ?? '-' }}
                    </span>
                </div>
            </div>

            {{-- Tanggal Mulai --}}
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-calendar-day text-blue-500 text-sm"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-gray-500 text-xs mb-0.5">Tanggal Mulai</p>
                    <p class="font-semibold text-gray-800">{{ \Carbon\Carbon::parse($proyek->tanggal_mulai)->format('d M Y') ?? '-' }}</p>
                </div>
            </div>

            {{-- Tanggal Selesai --}}
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-lg bg-green-50 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-calendar-check text-green-500 text-sm"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-gray-500 text-xs mb-0.5">Tanggal Selesai</p>
                    <p class="font-semibold text-gray-800">{{ \Carbon\Carbon::parse($proyek->tanggal_selesai)->format('d M Y') ?? '-' }}</p>
                </div>
            </div>

            {{-- Progress bar --}}
            <div class="md:col-span-3 mt-2">
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center">
                        <i class="fas fa-tasks text-blue-500 text-sm"></i>
                    </div>
                    <p class="text-gray-700 text-xs font-medium">Status Progres</p>
                </div>
                @php
                    $progress = match ($proyek->status) {
                        'sedang_berjalan' => 50,
                        'selesai' => 100,
                        'belum_dimulai', 'ditunda' => 0,
                        default => 0,
                    };
                @endphp

                <div class="w-full bg-gray-100 h-2.5 rounded-full overflow-hidden">
                    <div class="h-2.5 rounded-full transition-all duration-500 bg-gradient-to-r from-blue-500 to-purple-500"
                         style="width: {{ $progress }}%"></div>
                </div>
                <p class="text-xs text-gray-500 mt-1">{{ $progress }}% Selesai</p>
            </div>

            {{-- Deskripsi --}}
            <div class="md:col-span-3 mt-2">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center">
                        <i class="fas fa-file-alt text-gray-500 text-sm"></i>
                    </div>
                    <p class="text-gray-700 text-xs font-medium">Deskripsi</p>
                </div>
                <p class="text-gray-600 text-xs leading-relaxed pl-10 text-justify pr-4">{{ $proyek->deskripsi ?? '-' }}</p>
            </div>
        </div>
    </div>

    {{-- Customer Info --}}
    <div class="bg-white rounded-xl border border-gray-300 shadow-xl p-4 hover:shadow-md transition-shadow mt-4">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center">
                    <i class="fas fa-user-tie text-blue-500 text-sm"></i>
                </div>
                Informasi Klien
            </h3>

            @if($proyek->customer->status === 'aktif')
                <span class="text-xs px-3 py-1 bg-green-50 text-green-600 rounded-lg font-medium">
                    Aktif
                </span>
            @elseif($proyek->customer->status === 'tidak_aktif')
                <span class="text-xs px-3 py-1 bg-red-50 text-red-600 rounded-lg font-medium">
                    Tidak Aktif
                </span>
            @endif
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
            <div class="bg-gray-50 rounded-lg p-3">
                <p class="text-gray-500 mb-1 text-xs">Nama Lengkap</p>
                <p class="font-semibold text-gray-800">
                    {{ $proyek->customer->nama ?? '-' }}
                </p>
            </div>

            <div class="bg-gray-50 rounded-lg p-3">
                <p class="text-gray-500 mb-1 text-xs">Catatan</p>
                <p class="font-semibold text-gray-800">
                    {{ $proyek->customer->catatan ?? '-' }}
                </p>
            </div>

            <div class="bg-gray-50 rounded-lg p-3">
                <p class="text-gray-500 mb-1 text-xs">Email</p>
                <p class="font-semibold text-gray-800 break-all">
                    {{ $proyek->customer->email ?? '-' }}
                </p>
            </div>

            <div class="bg-gray-50 rounded-lg p-3">
                <p class="text-gray-500 mb-1 text-xs">Nomor Telepon</p>
                <p class="font-semibold text-gray-800">
                    {{ $proyek->customer->nomor_telepon ?? '-' }}
                </p>
            </div>

            <div class="md:col-span-2 bg-gray-50 rounded-lg p-3">
                <p class="text-gray-500 mb-1 text-xs">Alamat</p>
                <p class="font-semibold text-gray-800">
                    {{ $proyek->customer->alamat ?? '-' }}
                </p>
            </div>
        </div>
    </div>

    {{-- Footer Tombol Kembali --}}
    <div class="flex justify-start pt-2">
        <a href="{{ route('proyek') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-xs font-medium rounded-lg shadow-sm hover:shadow-md hover:scale-105 transition-all">
            <i class="fas fa-arrow-left"></i>
            Kembali ke Daftar Proyek
        </a>
    </div>

    {{-- Modal Edit --}}
    @if($showModal)
    <div class="fixed inset-0 flex items-center justify-center bg-black/50 backdrop-blur-sm z-50">
        <div class="bg-white w-full max-w-2xl mx-4 rounded-xl shadow-2xl transform transition-transform duration-300 ease-out animate-fadeIn">

            {{-- Header --}}
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-sm font-semibold text-gray-800 flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-gradient-to-r from-blue-500 to-indigo-600 flex items-center justify-center">
                        <i class="fas fa-edit text-white text-xs"></i>
                    </div>
                    Perbarui Proyek
                </h3>
                <button wire:click="closeModal"
                    class="w-8 h-8 rounded-lg hover:bg-gray-100 flex items-center justify-center transition-colors">
                    <i class="fas fa-times text-gray-400 hover:text-gray-600"></i>
                </button>
            </div>

            {{-- Body --}}
            <div class="p-5 max-h-[70vh] overflow-y-auto">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    {{-- Nama Proyek --}}
                    <div class="flex flex-col">
                        <label class="text-xs font-medium text-gray-700 mb-1.5">Nama Proyek</label>
                        <input type="text" wire:model="nama_proyek"
                            class="border border-gray-200 rounded-lg px-3 py-2 text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none text-xs
                            @error('nama_proyek') border-red-300 @enderror">
                        @error('nama_proyek')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Customer --}}
                    <div class="flex flex-col">
                        <label class="text-xs font-medium text-gray-700 mb-1.5">Klien</label>
                        <select wire:model="customer_id"
                            class="border border-gray-200 rounded-lg px-3 py-2 text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none text-xs
                            @error('customer_id') border-red-300 @enderror">
                            <option value="">-- Pilih Klien --</option>
                            @foreach($customers as $c)
                                <option value="{{ $c->id }}">{{ $c->nama }}</option>
                            @endforeach
                        </select>
                        @error('customer_id')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Deskripsi --}}
                    <div class="flex flex-col md:col-span-2">
                        <label class="text-xs font-medium text-gray-700 mb-1.5">Deskripsi</label>
                        <textarea wire:model="deskripsi" rows="3"
                            class="border border-gray-200 rounded-lg px-3 py-2 text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none text-xs resize-none
                            @error('deskripsi') border-red-300 @enderror"></textarea>
                        @error('deskripsi')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Lokasi --}}
                    <div class="flex flex-col md:col-span-2">
                        <label class="text-xs font-medium text-gray-700 mb-1.5">Lokasi</label>
                        <input type="text" wire:model="lokasi"
                            class="border border-gray-200 rounded-lg px-3 py-2 text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none text-xs
                            @error('lokasi') border-red-300 @enderror">
                        @error('lokasi')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Tanggal Mulai --}}
                    <div class="flex flex-col">
                        <label class="text-xs font-medium text-gray-700 mb-1.5">Tanggal Mulai</label>
                        <input type="date" wire:model="tanggal_mulai"
                            class="border border-gray-200 rounded-lg px-3 py-2 text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none text-xs
                            @error('tanggal_mulai') border-red-300 @enderror">
                        @error('tanggal_mulai')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Tanggal Selesai --}}
                    <div class="flex flex-col">
                        <label class="text-xs font-medium text-gray-700 mb-1.5">Tanggal Selesai</label>
                        <input type="date" wire:model="tanggal_selesai"
                            class="border border-gray-200 rounded-lg px-3 py-2 text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none text-xs
                            @error('tanggal_selesai') border-red-300 @enderror">
                        @error('tanggal_selesai')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Anggaran --}}
                    <div class="flex flex-col">
                        <label class="text-xs font-medium text-gray-700 mb-1.5">Anggaran</label>
                        <input type="number" wire:model="anggaran"
                            step="1"
                            oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                            class="border border-gray-200 rounded-lg px-3 py-2 text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none text-xs
                            @error('anggaran') border-red-300 @enderror">
                        @error('anggaran')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Status --}}
                    <div class="flex flex-col">
                        <label class="text-xs font-medium text-gray-700 mb-1.5">Status</label>
                        <select wire:model="status"
                            class="border border-gray-200 rounded-lg px-3 py-2 text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none text-xs
                            @error('status') border-red-300 @enderror">
                            <option value="">-- Pilih Status --</option>
                            <option value="belum_dimulai">Belum Dimulai</option>
                            <option value="sedang_berjalan">Sedang Berjalan</option>
                            <option value="selesai">Selesai</option>
                            <option value="ditunda">Ditunda</option>
                        </select>
                        @error('status')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="px-5 py-4 border-t border-gray-100 flex justify-end gap-2">
                <button wire:click="closeModal"
                    class="px-4 py-2 rounded-lg text-gray-700 text-xs bg-gray-100 font-medium hover:bg-gray-100 transition-colors">
                    Batal
                </button>
                @if($isEdit)
                    <button wire:click="update"
                        class="px-4 py-2 rounded-lg bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-xs font-medium shadow-sm hover:shadow-md hover:scale-105 transition-all">
                        Perbarui Proyek
                    </button>
                @else
                    <button wire:click="store"
                        class="px-4 py-2 rounded-lg bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-xs font-medium shadow-sm hover:shadow-md hover:scale-105 transition-all">
                        Simpan Proyek
                    </button>
                @endif
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