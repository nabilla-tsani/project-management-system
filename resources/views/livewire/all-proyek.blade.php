<div class="min-h-screen w-full bg-white text-gray-900 py-3 px-11 relative overflow-hidden">

    <!-- Alert Notifikasi -->
    @if ($alert)
    <div
        class="fixed top-4 left-1/2 transform -translate-x-1/2 
            z-[9999] px-5 py-2.5 rounded-xl shadow-lg text-xs font-medium 
            transition-all duration-500 backdrop-blur-sm
            {{ $alert['type'] === 'error' 
                    ? 'bg-red-50/90 text-red-600 border border-red-200' 
                    : 'bg-green-50/90 text-green-600 border border-green-200' }}">
        <div class="flex items-center gap-2">
            @if($alert['type'] === 'error')
                <i class="fas fa-exclamation-circle"></i>
            @else
                <i class="fas fa-check-circle"></i>
            @endif
            <span>{{ $alert['message'] }}</span>
        </div>
    </div>
    @endif

    <script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('reset-alert', () => {
            const alertType = @this.get('alert')?.type;
            const delay = alertType === 'error' ? 5000 : 1500;
            setTimeout(() => {
                @this.call('clearAlert');
            }, delay);
        });
    });
    </script>

    <div class="max-w-7xl mx-auto">
        <!-- Header Section -->
        <div class="flex items-center justify-between mb-5">
            <div>
                <h1 class="text-2xl font-bold tracking-tight bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 bg-clip-text text-transparent">
                    Project Management
                </h1>
                <p class="text-xs text-gray-500 mt-1">Manage and track all your projects</p>
            </div>

            <!-- Tombol Add Project -->
            <button wire:click="openModal" 
                class="bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white px-4 py-2 rounded-lg shadow-md hover:shadow-lg flex items-center gap-2 text-xs font-semibold transition-all duration-300 transform hover:scale-105">
                <i class="fas fa-plus text-[10px]"></i> 
                <span>Add Project</span>
            </button>
        </div>

        <!-- Search + Filter -->
        <div class="flex flex-col sm:flex-row gap-3 mb-5">
            <!-- Search Bar -->
            <div class="relative w-full sm:flex-1">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                    <i class="fas fa-search text-xs"></i>
                </span>
                <input type="text" wire:model.live="search" placeholder="Search projects..."
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
                    All Projects
                </button>

                <button wire:click="$set('statusFilter', 'belum_dimulai')"
                    class="px-3 py-2 rounded-lg text-xs font-semibold transition-all duration-300 shadow-sm
                    {{ $statusFilter === 'belum_dimulai' 
                        ? 'bg-gradient-to-r from-blue-500 to-indigo-600 text-white shadow-md' 
                        : 'bg-white text-gray-600 hover:bg-gray-50 border border-gray-200' }}">
                    Upcoming
                </button>

                <button wire:click="$set('statusFilter', 'sedang_berjalan')"
                    class="px-3 py-2 rounded-lg text-xs font-semibold transition-all duration-300 shadow-sm
                    {{ $statusFilter === 'sedang_berjalan' 
                        ? 'bg-gradient-to-r from-blue-500 to-indigo-600 text-white shadow-md' 
                        : 'bg-white text-gray-600 hover:bg-gray-50 border border-gray-200' }}">
                    Ongoing
                </button>

                <button wire:click="$set('statusFilter', 'selesai')"
                    class="px-3 py-2 rounded-lg text-xs font-semibold transition-all duration-300 shadow-sm
                    {{ $statusFilter === 'selesai' 
                        ? 'bg-gradient-to-r from-blue-500 to-indigo-600 text-white shadow-md' 
                        : 'bg-white text-gray-600 hover:bg-gray-50 border border-gray-200' }}">
                    Done
                </button>
            </div>
        </div>

        <!-- Grid Cards -->
        @if ($proyek->isEmpty())
            <div class="text-center py-16 bg-white rounded-xl shadow-sm">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-blue-100 to-purple-100 rounded-full mb-4">
                    <i class="fas fa-folder-open text-2xl text-indigo-600"></i>
                </div>
                <p class="text-sm text-gray-500">No projects found</p>
                <p class="text-xs text-gray-400 mt-1">Create your project now.</p>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-2">
                @foreach($proyek as $p)
                    <div class="group relative bg-white rounded-xl p-4 flex flex-col justify-between shadow-sm border border-gray-300 hover:shadow-xl transition-all duration-300 cursor-pointer border border-gray-100 hover:border-indigo-200 transform hover:-translate-y-1"
                        onclick="window.location='{{ route('proyek.detail', $p->id) }}'">

                        <!-- Header Card -->
                        <div class="mb-3">
                            <div class="flex items-start justify-between mb-2">
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-sm font-bold text-indigo-600 mb-1 truncate group-hover:text-gray-900 transition-colors" title="{{ $p->nama_proyek }}">
                                        {{ $p->nama_proyek }}
                                    </h3>
                                    <p class="text-xs text-gray-500 truncate flex items-center gap-1" title="{{ $p->customer?->nama }}">
                                        <i class="fas fa-building text-[10px]"></i>
                                        <span>{{ $p->customer?->nama ?? 'No Customer' }}</span>
                                    </p>
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex gap-1.5 ml-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button wire:click.stop="edit({{ $p->id }})" 
                                        class="w-7 h-7 flex items-center justify-center rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition text-xs" 
                                        title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button wire:click.stop="confirmDeleteProyek({{ $p->id }})"
                                        class="w-7 h-7 flex items-center justify-center rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition text-xs" 
                                        title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Status Badge -->
                            <div class="flex items-center gap-2 mt-3">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-semibold
                                    @if ($p->status === 'belum_dimulai') bg-gray-100 text-gray-700
                                    @elseif ($p->status === 'sedang_berjalan') bg-gradient-to-r from-blue-100 to-indigo-100 text-indigo-700
                                    @elseif ($p->status === 'selesai') bg-gradient-to-r from-green-100 to-emerald-100 text-green-700
                                    @else bg-red-100 text-red-700
                                    @endif">
                                    <span class="w-1.5 h-1.5 rounded-full
                                        @if ($p->status === 'belum_dimulai') bg-gray-500
                                        @elseif ($p->status === 'sedang_berjalan') bg-indigo-500
                                        @elseif ($p->status === 'selesai') bg-green-500
                                        @else bg-red-500
                                        @endif">
                                    </span>
                                    @if ($p->status === 'belum_dimulai') Upcoming
                                    @elseif ($p->status === 'sedang_berjalan') Ongoing
                                    @elseif ($p->status === 'selesai') Done
                                    @elseif ($p->status === 'ditunda') Pending
                                    @else {{ $p->status }}
                                    @endif
                                </span>
                            </div>
                        </div>

                        <!-- Progress Bar -->
                            <div class="w-full bg-gray-100 rounded-full h-1.5 overflow-hidden">
                                <div class="h-full rounded-full transition-all duration-500
                                    @if($p->status == 'selesai') bg-gradient-to-r from-green-500 to-emerald-500
                                    @elseif($p->status == 'sedang_berjalan') bg-gradient-to-r from-blue-500 to-indigo-600
                                    @elseif($p->status == 'ditunda') bg-gray-300
                                    @else bg-gray-200
                                    @endif"
                                    style="width: 
                                        @if($p->status == 'selesai') 100%
                                        @elseif($p->status == 'sedang_berjalan') 45%
                                        @else 0%
                                        @endif">
                                </div>
                            </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $proyek->links() }}
            </div>
        @endif
    </div>

    {{-- Modal Add/Edit --}}
    @if($showModal)
    <div class="fixed inset-0 flex items-center justify-center bg-black/50 backdrop-blur-sm z-50 p-4">
        <div class="bg-white w-full max-w-3xl rounded-xl shadow-2xl transform transition-all duration-300 ease-out animate-fadeIn overflow-hidden">

            {{-- Header --}}
            <div class="relative bg-white px-6 py-4">
                <h3 class="text-md font-bold bg-gradient-to-r from-blue-500 to-indigo-600 bg-clip-text text-transparent text-center">
                    {{ $isEdit ? 'Update Project' : 'Create New Project' }}
                </h3>
                <button wire:click="closeModal"
                    class="absolute top-1/2 right-4 -translate-y-1/2 w-8 h-8 flex items-center justify-center rounded-full bg-white/20 hover:bg-gray-300 text-black transition">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>

            {{-- Body --}}
            <div class="px-6 py-2">

                {{-- Success Message --}}
                @if (session()->has('message'))
                    <div class="mb-4 flex items-center gap-2 text-xs text-green-700 bg-green-50 border border-green-200 rounded-lg px-4 py-2.5">
                        <span>{{ session('message') }}</span>
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    {{-- Project Name --}}
                    <div class="flex flex-col">
                        <label class="text-xs font-semibold text-gray-700 mb-1.5">
                            Project Name
                        </label>
                        <input type="text" wire:model="nama_proyek"
                            class="border border-gray-200 rounded-lg px-3 py-2 text-xs
                            focus:ring-2 focus:ring-blue-500 focus:border-transparent
                            @error('nama_proyek') border-red-400 @enderror"
                            placeholder="Enter project name">
                        @error('nama_proyek')
                            <span class="text-[10px] text-red-600 mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Customer --}}
                    <div class="flex flex-col">
                        <label class="text-xs font-semibold text-gray-700 mb-1.5">
                            Customer
                        </label>
                        <select wire:model="customer_id"
                            class="border border-gray-200 rounded-lg px-3 py-2 text-xs
                            focus:ring-2 focus:ring-blue-500 focus:border-transparent
                            @error('customer_id') border-red-400 @enderror">
                            <option value="">-- Select Customer --</option>
                            @foreach($customers as $c)
                                <option value="{{ $c->id }}">{{ $c->nama }}</option>
                            @endforeach
                        </select>
                        @error('customer_id')
                            <span class="text-[10px] text-red-600 mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Description --}}
                    <div class="flex flex-col md:col-span-2">
                        <label class="text-xs font-semibold text-gray-700 mb-1.5">
                            Description
                        </label>
                        <textarea wire:model="deskripsi" rows="3"
                            class="border border-gray-200 rounded-lg px-3 py-2 text-xs resize-none
                            focus:ring-2 focus:ring-blue-500 focus:border-transparent
                            @error('deskripsi') border-red-400 @enderror"
                            placeholder="Project description..."></textarea>
                        @error('deskripsi')
                            <span class="text-[10px] text-red-600 mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Location --}}
                    <div class="flex flex-col md:col-span-2">
                        <label class="text-xs font-semibold text-gray-700 mb-1.5">
                            Location
                        </label>
                        <input type="text" wire:model="lokasi"
                            class="border border-gray-200 rounded-lg px-3 py-2 text-xs
                            focus:ring-2 focus:ring-blue-500 focus:border-transparent
                            @error('lokasi') border-red-400 @enderror"
                            placeholder="Project location">
                        @error('lokasi')
                            <span class="text-[10px] text-red-600 mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Start Date --}}
                    <div class="flex flex-col">
                        <label class="text-xs font-semibold text-gray-700 mb-1.5">
                            Start Date
                        </label>
                        <input type="date" wire:model="tanggal_mulai"
                            class="border border-gray-200 rounded-lg px-3 py-2 text-xs
                            focus:ring-2 focus:ring-blue-500 focus:border-transparent
                            @error('tanggal_mulai') border-red-400 @enderror">
                        @error('tanggal_mulai')
                            <span class="text-[10px] text-red-600 mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- End Date --}}
                    <div class="flex flex-col">
                        <label class="text-xs font-semibold text-gray-700 mb-1.5">
                            End Date
                        </label>
                        <input type="date" wire:model="tanggal_selesai"
                            class="border border-gray-200 rounded-lg px-3 py-2 text-xs
                            focus:ring-2 focus:ring-blue-500 focus:border-transparent
                            @error('tanggal_selesai') border-red-400 @enderror">
                        @error('tanggal_selesai')
                            <span class="text-[10px] text-red-600 mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Budget --}}
                    <div class="flex flex-col">
                        <label class="text-xs font-semibold text-gray-700 mb-1.5">
                            Budget (Rp)
                        </label>
                        <input type="number" wire:model="anggaran"
                            class="border border-gray-200 rounded-lg px-3 py-2 text-xs
                            focus:ring-2 focus:ring-blue-500 focus:border-transparent
                            @error('anggaran') border-red-400 @enderror"
                            placeholder="0">
                        @error('anggaran')
                            <span class="text-[10px] text-red-600 mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Status --}}
                    <div class="flex flex-col">
                        <label class="text-xs font-semibold text-gray-700 mb-1.5">
                            Status
                        </label>
                        <select wire:model="status"
                            class="border border-gray-200 rounded-lg px-3 py-2 text-xs
                            focus:ring-2 focus:ring-blue-500 focus:border-transparent
                            @error('status') border-red-400 @enderror">
                            <option value="">-- Select Status --</option>
                            <option value="belum_dimulai">Upcoming</option>
                            <option value="sedang_berjalan">Ongoing</option>
                            <option value="selesai">Done</option>
                            <option value="ditunda">Pending</option>
                        </select>
                        @error('status')
                            <span class="text-[10px] text-red-600 mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                </div>

                {{-- Action Buttons --}}
                <div class="pb-2 flex justify-end gap-3 pt-4 border-t border-gray-100">
                    <button wire:click="closeModal"
                        class="px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold transition">
                        Cancel
                    </button>

                    @if($isEdit)
                        <button wire:click="update"
                            class="px-4 py-2 rounded-lg bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-xs font-semibold shadow hover:shadow-lg transition">
                            Update Project
                        </button>
                    @else
                        <button wire:click="store"
                            class="px-4 py-2 rounded-lg bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-xs font-semibold shadow hover:shadow-lg transition">
                            Save Project
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
    @if($confirmDelete)
    <div class="fixed inset-0 flex items-center justify-center bg-black/50 backdrop-blur-sm z-50 p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md p-6 text-center animate-fadeIn">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-red-100 rounded-full mb-4">
                <i class="fas fa-exclamation-triangle text-2xl text-red-600"></i>
            </div>
            
            <h3 class="text-base font-bold text-gray-900 mb-2">Delete Project?</h3>
            <p class="text-xs text-gray-600 mb-1">
                Are you sure you want to delete
            </p>
            <p class="text-xs font-bold text-gray-900 mb-4">
                "{{ $nama_proyek }}"?
            </p>
            <p class="text-xs text-red-600 mb-6">
                This action cannot be undone!
            </p>

            <div class="flex justify-center gap-3">
                <button wire:click="cancelDelete"
                    class="px-5 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold transition">
                    <i class="fas fa-times text-[10px] mr-1"></i> Cancel
                </button>
                <button wire:click="deleteProyek"
                    class="px-5 py-2 rounded-lg bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white text-xs font-semibold shadow-md hover:shadow-lg transition-all">
                    <i class="fas fa-trash text-[10px] mr-1"></i> Yes, Delete
                </button>
            </div>
        </div>
    </div>
    @endif

</div>