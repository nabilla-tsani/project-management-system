<div class="min-h-screen w-full bg-white text-gray-900 py-3 px-11 relative overflow-hidden">

    <div class="max-w-7xl mx-auto">
        <!-- Header Section -->
        <div class="flex items-center justify-between mb-5">
            <div>
                <h1 class="text-2xl font-bold tracking-tight bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 bg-clip-text text-transparent">
                    Manajemen Klien
                </h1>
                <p class="text-xs text-gray-500 mt-1">
                    Kelola dan pantau seluruh klien Anda
                </p>
            </div>

            <!-- Tombol Add Project -->
            <button wire:click="$set('showModal', true)" 
                class="bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white px-4 py-2 rounded-lg shadow-md hover:shadow-lg flex items-center gap-2 text-xs font-semibold transition-all duration-300 transform hover:scale-105">
                <i class="fas fa-plus text-[10px]"></i> 
                <span>Tambah Klien</span>
            </button>
        </div>

        <!-- Search + Filter -->
        <div class="flex flex-col sm:flex-row gap-3">
            <!-- Search Bar -->
            <div class="relative w-full sm:flex-1">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                    <i class="fas fa-search text-xs"></i>
                </span>
                <input type="text" wire:model.live="search" placeholder="Cari klien..."
                    class="w-full pl-9 pr-4 py-2 rounded-lg bg-white border border-gray-200 text-gray-900 
                        focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none 
                        placeholder-gray-400 text-xs transition shadow-sm" />
            </div>

            <!-- Filter Buttons -->
            <div class="flex gap-2 flex-wrap">
                <button wire:click="$set('statusFilter', '')"
                    class="px-3 py-2 rounded-lg text-xs font-semibold transition-all duration-300 shadow-sm
                    {{ $statusFilter === '' 
                        ? 'bg-gradient-to-r from-blue-500 to-indigo-600 text-white shadow-md' 
                        : 'bg-white text-gray-600 hover:bg-gray-50 border border-gray-200' }}">
                    Semua Klien
                </button>

                <button wire:click="$set('statusFilter', 'aktif')"
                    class="px-3 py-2 rounded-lg text-xs font-semibold transition-all duration-300 shadow-sm
                    {{ $statusFilter === 'aktif' 
                        ? 'bg-gradient-to-r from-blue-500 to-indigo-600 text-white shadow-md' 
                        : 'bg-white text-gray-600 hover:bg-gray-50 border border-gray-200' }}">
                    Aktif
                </button>

                <button wire:click="$set('statusFilter', 'tidak_aktif')"
                    class="px-3 py-2 rounded-lg text-xs font-semibold transition-all duration-300 shadow-sm
                    {{ $statusFilter === 'tidak_aktif' 
                        ? 'bg-gradient-to-r from-blue-500 to-indigo-600 text-white shadow-md' 
                        : 'bg-white text-gray-600 hover:bg-gray-50 border border-gray-200' }}">
                    Tidak Aktif
                </button>
            </div>
        </div>

        <div class="pb-2 pt-2">
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

         @if (session()->has('error'))
            <div 
                x-data="{ show: true }"
                x-init="setTimeout(() => show = false, 3000)"
                x-show="show"
                x-transition.duration.500ms
                class="text-xs p-2 rounded bg-red-100 text-red-700 border border-red-300"
            >
                {{ session('error') }}
            </div>
        @endif
        </div>


        <!-- List Customer Modern (Aktif vs Tidak Aktif) -->
        @if($customers->isEmpty())
            <div class="text-center py-16 bg-white rounded-xl shadow-sm">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-blue-100 to-purple-100 rounded-full mb-4">
                    <i class="fas fa-folder-open text-2xl text-indigo-600"></i>
                </div>
                <p class="text-sm text-gray-500">Tidak ada klien</p>
                <p class="text-xs text-gray-400 mt-1">Silakan tambahkan klien baru.</p>
            </div>
        @else
            <div>
                @foreach($customers as $c)
                <div class="group relative bg-white border-b border-gray-300 p-4
                            transition-all duration-300 ease-out
                            transform hover:scale-[1.015] hover:shadow-md hover:border border-gray-400 hover:mb-2
                            {{ $c->status !== 'aktif' ? 'opacity-60 bg-gray-50' : '' }}">
                        
                        <div class="flex items-center justify-between gap-4">
                            
                            <!-- Left: Avatar + Info (2 baris) -->
                            <div class="flex items-center gap-3 flex-1 min-w-0">
                                <!-- Avatar -->
                                <div class="relative flex-shrink-0">
                                    <img src="{{ $c->foto ?? 'https://ui-avatars.com/api/?name='.urlencode($c->nama).'&background=667eea&color=fff&size=80' }}" 
                                        alt="{{ $c->nama }}" 
                                        class="w-10 h-10 rounded-full object-cover ring-2 ring-purple-100">
                                    
                                    <!-- Status badge di avatar -->
                                    <span class="absolute -bottom-1 -right-1 w-3.5 h-3.5 rounded-full border-2 border-white {{ $c->status === 'aktif' ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                                </div>

                                <!-- Customer Info -->
                                <div class="flex-1 min-w-0">
                                    <!-- Baris 1: Nama + Contact -->
                                    <div class="flex items-center gap-2 mb-1">
                                        <h3 class="text-sm font-semibold text-gray-800 truncate" title="{{ $c->nama }}">
                                            {{ $c->nama }}
                                        </h3>
                                        <span class="text-gray-400">•</span>
                                        <div class="flex items-center gap-2 text-xs text-gray-500">
                                            <span class="flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                                </svg>
                                                {{ $c->nomor_telepon }}
                                            </span>
                                            <span class="flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                                </svg>
                                                {{ $c->email }}
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Baris 2: Alamat + Projects -->
                                    <div class="flex items-center gap-2 text-xs">
                                        <span class="flex items-center gap-1 text-gray-500 truncate" title="{{ $c->alamat }}">
                                            <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                            <span class="truncate">{{ $c->alamat }}</span>
                                        </span>
                                        
                                        @if($c->proyek->count())
                                            <span class="text-gray-400">•</span>
                                            <div class="flex items-center gap-1 flex-shrink-0">
                                                <button
                                                    wire:click="showProjects({{ $c->id }})"
                                                    class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium
                                                        bg-gradient-to-r from-purple-50 to-pink-50 text-purple-700
                                                        border border-purple-200 hover:bg-purple-100 transition"
                                                >
                                                    <svg class="w-2.5 h-2.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                    </svg>
                                                    {{ $c->proyek->count() }} Proyek
                                                </button>

                                            </div>
                                        @else
                                            <span class="text-gray-400">•</span>
                                            <span class="text-[10px] italic text-gray-400">Tidak memiliki proyek</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Right: Status Badge + Actions -->
                            <div class="flex items-center gap-3 flex-shrink-0">
                                <!-- Action Buttons -->
                                <div class="flex items-center gap-3 flex-shrink-0
                                            opacity-0 translate-x-2
                                            group-hover:opacity-100 group-hover:translate-x-0
                                            transition-all duration-200 ease-out">
                                    
                                    <div class="flex gap-2">
                                        <button wire:click.stop="edit({{ $c->id }})" 
                                            class="w-7 h-7 flex items-center justify-center rounded-lg
                                                bg-blue-50 text-blue-600 hover:bg-blue-100
                                                transition text-xs"
                                            title="Perbarui">
                                            <i class="fas fa-edit"></i>
                                        </button>

                                        <button wire:click.stop="confirmDelete({{ $c->id }})"
                                            class="w-7 h-7 flex items-center justify-center rounded-lg
                                                bg-red-50 text-red-600 hover:bg-red-100
                                                transition text-xs"
                                            title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- Status Badge -->
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                            {{ $c->status === 'aktif' 
                                                ? 'bg-gradient-to-r from-green-400 to-emerald-500 text-white shadow-sm' 
                                                : 'bg-gray-200 text-gray-600' }}">
                                    @if($c->status === 'aktif')
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        Aktif
                                    @else
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                        </svg>
                                        Tidak Aktif
                                    @endif
                                </span>
                            </div>

                        </div>

                    </div>
                @endforeach

                <!-- Pagination -->
                <div class="mt-6 flex justify-center">
                    {{ $customers->links() }}
                </div>
        @endif



       {{-- Modal Tambah/Edit Customer --}}
        @if($showModal)
        <div class="fixed inset-0 flex items-center justify-center bg-black/50 backdrop-blur-sm z-50 p-4">
            <div class="bg-white w-full max-w-3xl rounded-xl shadow-2xl transform transition-all duration-300 ease-out animate-fadeIn overflow-hidden">

                {{-- Header --}}
                <div class="relative bg-white px-6 py-4">
                    <h3 class="text-md font-bold bg-gradient-to-r from-blue-500 to-indigo-600 bg-clip-text text-transparent text-center">
                        {{ $isEdit ? 'Perbarui Klien' : 'Tambah Klien Baru' }}
                    </h3>
                    <button wire:click="closeModal"
                        class="absolute top-1/2 right-4 -translate-y-1/2 w-8 h-8 flex items-center justify-center rounded-full bg-white/20 hover:bg-gray-300 text-black transition">
                        <i class="fas fa-times text-sm"></i>
                    </button>
                </div>

                {{-- Body --}}
                <div class="px-6 py-4">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        {{-- Nama --}}
                        <div class="flex flex-col">
                            <label class="text-xs font-semibold text-gray-700 mb-1.5">
                                Nama Klien
                            </label>
                            <input type="text" wire:model="nama"
                                class="border border-gray-200 rounded-lg px-3 py-2 text-xs
                                focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="Masukkan nama klien">
                        </div>

                        {{-- Nomor Telepon --}}
                        <div class="flex flex-col">
                            <label class="text-xs font-semibold text-gray-700 mb-1.5">
                                Nomor Telepon
                            </label>
                            <input type="text" wire:model="nomor_telepon"
                                class="border border-gray-200 rounded-lg px-3 py-2 text-xs
                                focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="Masukkan nomor telepon">
                        </div>

                        {{-- Email --}}
                        <div class="flex flex-col">
                            <label class="text-xs font-semibold text-gray-700 mb-1.5">
                                Email
                            </label>
                            <input type="email" wire:model="email"
                                class="border border-gray-200 rounded-lg px-3 py-2 text-xs
                                focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="email@contoh.com">
                        </div>

                        {{-- Status --}}
                        <div class="flex flex-col">
                            <label class="text-xs font-semibold text-gray-700 mb-1.5">
                                Status
                            </label>
                            <select wire:model="status"
                                class="border border-gray-200 rounded-lg px-3 py-2 text-xs
                                focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">-- Pilih Status --</option>
                                <option value="aktif">Aktif</option>
                                <option value="tidak_aktif">Tidak Aktif</option>
                            </select>
                        </div>

                        {{-- Alamat --}}
                        <div class="flex flex-col md:col-span-2">
                            <label class="text-xs font-semibold text-gray-700 mb-1.5">
                                Alamat
                            </label>
                            <input type="text" wire:model="alamat"
                                class="border border-gray-200 rounded-lg px-3 py-2 text-xs
                                focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="Alamat lengkap">
                        </div>

                        {{-- Catatan --}}
                        <div class="flex flex-col md:col-span-2">
                            <label class="text-xs font-semibold text-gray-700 mb-1.5">
                                Catatan
                            </label>
                            <textarea wire:model="catatan" rows="3"
                                class="border border-gray-200 rounded-lg px-3 py-2 text-xs resize-none
                                focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="Catatan tambahan..."></textarea>
                        </div>

                    </div>

                    {{-- Tombol Aksi --}}
                    <div class="pb-2 flex justify-end gap-3 pt-4 border-t border-gray-100 mt-4">
                        <button wire:click="closeModal"
                            class="px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold transition">
                            Batal
                        </button>

                        @if($isEdit)
                            <button wire:click="update"
                                class="px-4 py-2 rounded-lg bg-gradient-to-r from-blue-500 to-indigo-600
                                    text-white text-xs font-semibold shadow hover:shadow-lg transition">
                                Perbarui Klien
                            </button>
                        @else
                            <button wire:click="store"
                                class="px-4 py-2 rounded-lg bg-gradient-to-r from-blue-500 to-indigo-600
                                    text-white text-xs font-semibold shadow hover:shadow-lg transition">
                                Simpan Klien
                            </button>
                        @endif
                    </div>

                </div>
            </div>
        </div>

        <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.95); }
            to   { opacity: 1; transform: scale(1); }
        }
        .animate-fadeIn {
            animation: fadeIn 0.3s ease-out forwards;
        }
        </style>
        @endif

       {{-- Confirmation Delete Modal --}}
@if($confirmDeleteModal)
<div class="fixed inset-0 flex items-center justify-center bg-black/50 backdrop-blur-sm z-50 p-4">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md p-6 text-center animate-fadeIn">

        <div class="inline-flex items-center justify-center w-16 h-16 
            {{ $hasProject ? 'bg-yellow-100' : 'bg-red-100' }} 
            rounded-full mb-4">
            <i class="fas 
                {{ $hasProject ? 'fa-info-circle text-yellow-600' : 'fa-exclamation-triangle text-red-600' }} 
                text-2xl"></i>
        </div>

        @if($hasProject)
            <h3 class="text-sm font-bold text-gray-800 mb-2">
                Tidak Dapat Menghapus
            </h3>
            <p class="text-xs text-gray-600 mb-4">
                Klien <span class="font-semibold">"{{ $nama }}"</span>
                masih memiliki proyek aktif.
            </p>

            <button wire:click="cancelDelete"
                class="px-5 py-2 rounded-lg bg-gray-100 hover:bg-gray-200
                text-xs font-semibold">
                Tutup
            </button>
        @else
            <h3 class="text-base font-bold text-gray-900 mb-2">
                Hapus Klien?
            </h3>
            <p class="text-xs text-gray-600 mb-1">
                Apakah Anda yakin ingin menghapus
            </p>
            <p class="text-xs font-bold text-gray-900 mb-4">
                "{{ $nama }}"?
            </p>
            <p class="text-xs text-red-600 mb-6">
                Tindakan ini tidak dapat dibatalkan!
            </p>

            <div class="flex justify-center gap-3">
                <button wire:click="cancelDelete"
                    class="px-5 py-2 rounded-lg bg-gray-100 hover:bg-gray-200
                    text-gray-700 text-xs font-semibold">
                    Batal
                </button>

                <button wire:click="deleteCustomer"
                    class="px-5 py-2 rounded-lg bg-gradient-to-r
                    from-red-500 to-red-600 text-white text-xs font-semibold">
                    Ya, Hapus
                </button>
            </div>
        @endif

    </div>
</div>
@endif


        @if($showProjectModal)
            <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4">
                <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg overflow-hidden">

                    {{-- Header --}}
                    <div class="px-6 py-4 bg-gradient-to-r from-indigo-500 to-purple-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-sm font-bold text-white">
                                    Daftar Proyek
                                </h3>
                                <p class="text-xs text-indigo-100">
                                    {{ $projectOwnerName }}
                                </p>
                            </div>

                            <button
                                wire:click="$set('showProjectModal', false)"
                                class="w-7 h-7 flex items-center justify-center rounded-full
                                    bg-white/20 hover:bg-white/30 text-white transition"
                            >
                                ✕
                            </button>
                        </div>
                    </div>

                    {{-- Table --}}
                    <div class="p-6">
                        <div class="max-h-[65vh] overflow-y-auto rounded-xl border border-gray-200">
                            <table class="w-full text-xs table-fixed">
                                <thead class="bg-gray-50 sticky top-0 z-10">
                                    <tr>
                                        <th class="w-10 px-2 py-2 text-center font-semibold text-gray-600">
                                            No
                                        </th>
                                        <th class="px-4 py-2 text-center font-semibold text-gray-600">
                                            Nama Proyek
                                        </th>
                                    </tr>
                                </thead>

                                <tbody class="divide-y">
                                    @forelse($projectList as $index => $proyek)
                                        <tr class="hover:bg-indigo-50 transition">
                                            <td class="px-2 py-2 text-center text-gray-500">
                                                {{ $index + 1 }}
                                            </td>
                                            <td class="px-4 py-2 font-medium text-gray-700">
                                                {{ $proyek->nama_proyek ?? 'Nama proyek tidak tersedia' }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2" class="px-4 py-6 text-center text-gray-400">
                                                Tidak ada proyek
                                            </td>
                                        </tr>
                                    @endforelse

                                    @for ($i = 1; $i <= 30; $i++)
                                        <tr class="hover:bg-indigo-50 transition">
                                            <td class="px-2 py-2 text-center text-gray-400">
                                                {{ count($projectList) + $i }}
                                            </td>
                                            <td class="px-4 py-2 font-medium text-gray-500">
                                                Proyek Pengembangan Sistem Informasi Manajemen Terintegrasi
                                                untuk Optimalisasi Proses Bisnis dan Monitoring Kinerja Organisasi Ke-{{ $i }}
                                            </td>
                                        </tr>
                                    @endfor
                                </tbody>
                            </table>
                        </div>
                    </div>


                </div>
            </div>
            @endif

    {{-- Chatbot --}}
    <div class="font-sans" wire:ignore>
        @livewire('chatbot')
    </div>
</div>
