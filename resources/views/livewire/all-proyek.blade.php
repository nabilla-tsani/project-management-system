<div class="min-h-screen w-full bg-white text-gray-900 pt-0 p-8 relative overflow-hidden">
    <!-- Efek glow ungu  -->
    <div
        style="
        position: fixed;
        bottom: -350px;
        left: -230px;
        width: 500px;
        height: 400px;
        background: radial-gradient(circle, #ac7bff 100%);
        filter: blur(120px);
        opacity: 0.7;
        z-index: 0;
        pointer-events: none;
        "
    ></div>

    <!-- Alert Notifikasi -->
    @if ($alert)
    <div
        class="fixed top-5 left-1/2 transform -translate-x-1/2 
            z-[9999] px-6 py-3 rounded-3xl shadow-lg text-sm font-medium 
            transition-opacity duration-500
            {{ $alert['type'] === 'error' 
                    ? 'bg-red-50 text-red-600' 
                    : 'bg-green-100 text-green-600' }}">
        {{ $alert['message'] }}
    </div>
    @endif

    <script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('reset-alert', () => {
            // Ambil nilai type alert dari komponen Livewire
            const alertType = @this.get('alert')?.type;

            // Tentukan durasi berdasarkan tipe
            const delay = alertType === 'error' ? 5000 : 1500;

            setTimeout(() => {
                @this.call('clearAlert');
            }, delay);
        });
    });
    </script>


    <div class="max-w-7xl mx-auto">
        <!-- Title Halaman -->
        <div class="flex items-center justify-between mb-4 pl-1">
           <h1 class="text-3xl font-medium tracking-tight">
                    <span class="text-[#ac7bff]">Project</span>
                    <span class="text-[#77b6ff]">Management</span>
                </h1>

            <!-- Tombol Tambah Proyek Minimalis -->
             <div class="pt-3">
            <button wire:click="openModal" 
                class="text-white px-4 py-2 rounded-3xl shadow-sm flex items-center gap-2 text-xs font-medium hover:brightness-105 transition"
                style="background-color: #5ca9ff;">
                <i class="fas fa-plus"></i> Add Project
            </button>
            </div>
        </div>


     <!-- Search + Filter -->
    <div class="flex flex-col sm:flex-row gap-3 mb-4">
        <!-- Search Bar -->
    <div class="relative w-full sm:w-1/2">
        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
            <i class="fas fa-search"></i>
        </span>
        <input type="text" wire:model.live="search" placeholder="Find Project..."
            class="w-full pl-10 pr-3 py-2 rounded-3xl bg-white border border-gray-300 text-gray-900 
                focus:ring-1 focus:ring-[#5ca9ff] focus:border-transparent outline-none 
                placeholder-gray-400 text-sm transition" />
    </div>
    <!-- Filter berdasarkan status -->
    <div class="flex gap-2">
        <button wire:click="$set('statusFilter', '')"
            class="px-3 py-1 rounded-3xl text-xs font-medium transition"
            style="{{ $statusFilter === '' 
                ? 'background-color:#5ca9ff;color:white;' 
                : 'background-color:#e5e7eb;color:#374151;' }}">
            All Project
        </button>

        <button wire:click="$set('statusFilter', 'belum_dimulai')"
            class="px-3 py-1 rounded-3xl text-xs font-medium transition"
            style="{{ $statusFilter === 'belum_dimulai' 
                ? 'background-color:#5ca9ff;color:white;' 
                : 'background-color:#e5e7eb;color:#374151;' }}">
            Upcoming
        </button>

        <button wire:click="$set('statusFilter', 'sedang_berjalan')"
            class="px-3 py-1 rounded-3xl text-xs font-medium transition"
            style="{{ $statusFilter === 'sedang_berjalan' 
                ? 'background-color:#5ca9ff;color:white;' 
                : 'background-color:#e5e7eb;color:#374151;' }}">
            Ongoing
        </button>

        <button wire:click="$set('statusFilter', 'selesai')"
            class="px-4 py-1 rounded-3xl text-xs font-medium transition"
            style="{{ $statusFilter === 'selesai' 
                ? 'background-color:#5ca9ff;color:white;' 
                : 'background-color:#e5e7eb;color:#374151;' }}">
            Done
        </button>
    </div>
</div>


    <!-- Grid Card -->
    @if ($proyek->isEmpty())
        <div class="text-center py-10 text-gray-500 italic">
            Tidak ada Proyek.
        </div>
    @else
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-3 gap-3">
        @foreach($proyek as $p)
            <div class="relative bg-white rounded-xl p-4 flex flex-col justify-between shadow-xl hover:border hover:border-gray-300 hover:shadow-lg transition transform hover:scale-105 duration-300 cursor-pointer"
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
                        @if ($p->status === 'belum_dimulai') text-gray-500
                        @elseif ($p->status === 'sedang_berjalan') text-[#9c62ff]
                        @elseif ($p->status === 'selesai') text-[#5ca9ff]
                        @else text-gray-500
                        @endif
                    ">
                        @if ($p->status === 'belum_dimulai')
                            Upcoming
                        @elseif ($p->status === 'sedang_berjalan')
                            Ongoing
                        @elseif ($p->status === 'selesai')
                            Done
                        @else
                            {{ $p->status }}
                        @endif
                    </span>

                    <!-- Tombol aksi kanan -->
                    <div class="flex gap-2">
                        <button wire:click.stop="edit({{ $p->id }})" class="text-gray-500 hover:text-gray-700 transition text-[12px]" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button 
                            wire:click.stop="confirmDeleteProyek({{ $p->id }})"
                            class="text-gray-500 hover:text-gray-700 transition text-[12px]" 
                            title="Hapus">
                            <i class="fas fa-trash"></i>
                        </button>

                    </div>
                </div>

                <!-- Status Bar -->
                <div
                    class="mt-4 h-1 rounded-b-xl
                        @if($p->status == 'belum_dimulai') bg-gray-300
                        @elseif($p->status == 'selesai') bg-[#5ca9ff]
                        @endif"
                    @if($p->status == 'sedang_berjalan')
                        style="background: linear-gradient(to right, #5ca9ff 30%, #d1d5db 30%);"
                    @endif>
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
<div class="fixed inset-0 flex items-center justify-center bg-black/40 backdrop-blur-sm z-50">
    <div class="bg-white w-2/3 max-w-2xl shadow-2xl transform transition-transform duration-300 ease-out animate-fadeIn flex flex-col">

    {{-- Tombol Close (icon X di kanan atas) --}}
        <button wire:click="closeModal"
            class="absolute top-3 right-3 text-gray-400 hover:text-gray-600 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        {{-- Header --}}
        <div class="p-5 border-b border-gray-200">
            <h3 class="text-sm font-semibold text-center" style="color: #9c62ff;">
                {{ $isEdit ? 'Update Project' : 'Add New Project' }}
            </h3>
        </div>

        {{-- Body --}}
        <div class="p-5">
            {{-- Pesan sukses --}}
            @if (session()->has('message'))
                <div class="mb-4 text-xs text-green-600 bg-green-50 border border-green-300 rounded-lg px-4 py-2">
                    {{ session('message') }}
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                {{-- Nama Proyek --}}
                <div class="flex flex-col">
                    <label class="text-xs font-medium text-gray-700 mb-1">Project Name</label>
                    <input type="text" wire:model="nama_proyek"
                        class="border rounded-3xl px-3 py-2 text-gray-900 focus:ring-1 focus:ring-[#5ca9ff] focus:outline-none text-xs 
                        @error('nama_proyek') border-red-400 @enderror">
                    @error('nama_proyek')
                        <span class="text-red-800"></span>
                    @enderror
                </div>

                {{-- Customer --}} 
                <div class="flex flex-col"> 
                    <label class="text-xs font-medium text-gray-700 mb-1">Customer</label> 
                    <select wire:model="customer_id" 
                        class="border rounded-3xl px-3 py-2 text-gray-900 focus:ring-1 focus:ring-[#5ca9ff] 
                        focus:outline-none text-xs @error('customer_id') border-red-400 @enderror"> 
                    <option value="">-- Select Customer --</option> 
                    @foreach($customers as $c) 
                        <option value="{{ $c->id }}">{{ $c->nama }}</option> 
                    @endforeach
                    </select> @error('customer_id') 
                        <span class="text-red-500"></span> 
                    @enderror 
                </div>


                {{-- Deskripsi --}}
                <div class="flex flex-col md:col-span-2">
                    <label class="text-xs font-medium text-gray-700 mb-1">Description</label>
                    <textarea wire:model="deskripsi" rows="4"
                        class="border rounded-md px-3 py-2 text-gray-900 focus:ring-1 focus:ring-[#5ca9ff] focus:outline-none text-xs
                        @error('deskripsi') border-red-400 @enderror"></textarea>
                    @error('deskripsi')
                        <span class="text-red-500"></span>
                    @enderror
                </div>

                {{-- Lokasi --}}
                <div class="flex flex-col md:col-span-2">
                    <label class="text-xs font-medium text-gray-700 mb-1">Location</label>
                    <input type="text" wire:model="lokasi"
                        class="border rounded-3xl px-3 py-2 text-gray-900 focus:ring-1 focus:ring-[#5ca9ff] focus:outline-none text-xs
                        @error('lokasi') border-red-400 @enderror">
                    @error('lokasi')
                        <span class="text-red-500"></span>
                    @enderror
                </div>

                {{-- Tanggal Mulai --}}
                <div class="flex flex-col">
                    <label class="text-xs font-medium text-gray-700 mb-1">Start Date</label>
                    <input type="date" wire:model="tanggal_mulai"
                        class="border rounded-3xl px-3 py-2 text-gray-900 focus:ring-1 focus:ring-[#5ca9ff] focus:outline-none text-xs
                        @error('tanggal_mulai') border-red-400 @enderror">
                    @error('tanggal_mulai')
                        <span class="text-red-500"></span>
                    @enderror
                </div>

                {{-- Tanggal Selesai --}}
                <div class="flex flex-col">
                    <label class="text-xs font-medium text-gray-700 mb-1">End Date</label>
                    <input type="date" wire:model="tanggal_selesai"
                        class="border rounded-3xl px-3 py-2 text-gray-900 focus:ring-1 focus:ring-[#5ca9ff] focus:outline-none text-xs
                        @error('tanggal_selesai') border-red-400 @enderror">
                    @error('tanggal_selesai')
                        <span class="text-red-500"></span>
                    @enderror
                </div>

                {{-- Anggaran --}}
                <div class="flex flex-col">
                    <label class="text-xs font-medium text-gray-700 mb-1">Budget</label>
                    <input type="number" wire:model="anggaran"
                        step="1"
                        oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                        class="border rounded-3xl px-3 py-2 text-gray-900 focus:ring-1 focus:ring-[#5ca9ff] focus:outline-none text-xs
                        @error('anggaran') border-red-400 @enderror">
                    @error('anggaran')
                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>


                {{-- Status --}}
                <div class="flex flex-col">
                    <label class="text-xs font-medium text-gray-700 mb-1">Status</label>
                    <select wire:model="status"
                        class="border rounded-3xl px-3 py-2 text-gray-900 focus:ring-1 focus:ring-[#5ca9ff] focus:outline-none text-xs
                        @error('status') border-red-400 @enderror">
                        <option value="">-- Select Status --</option>
                        <option value="belum_dimulai">Upcoming</option>
                        <option value="sedang_berjalan">Ongoing</option>
                        <option value="selesai">Done</option>
                        <option value="ditunda">Pending</option>
                    </select>
                    @error('status')
                        <span class="text-red-500"></span>
                    @enderror
                </div>

                {{-- Tombol --}}
                <div class="mt-5 flex justify-end gap-3 md:col-span-2">
                    @if($isEdit)
                        <button wire:click="update"
                            class="px-4 py-2 rounded-3xl shadow text-white text-xs font-medium transition"
                            style="background-color: #5ca9ff;">
                            Update
                        </button>
                    @else
                        <button wire:click="store"
                            class="px-4 py-2 rounded-3xl shadow text-white text-xs font-medium transition"
                            style="background-color: #5ca9ff;">
                            Save
                        </button>
                    @endif
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


@if($confirmDelete)
<div class="fixed inset-0 flex items-center justify-center bg-black/40 backdrop-blur-sm z-50">
    <div class="bg-white shadow-2xl w-96 p-6 text-center animate-fadeIn">
        <div class="text-red-600 text-3xl mb-3">
            <i class="fa-solid fa-circle-exclamation"></i>
        </div>
        <h3 class="text-lg font-semibold text-gray-800 mb-3">Confirm Delete Project</h3>
        <p class="text-sm text-gray-600 mb-6">
            Are you sure you want to delete 
            <span class="font-semibold text-gray-900">“{{ $nama_proyek }}”</span>? 
            This action cannot be undone!
        </p>

        <div class="flex justify-center gap-3">
            <button wire:click="cancelDelete"
                class="px-4 py-2 rounded-3xl bg-gray-200 border border-gray-300 text-gray-700 text-xs hover:bg-gray-400 transition">
                Cancel
            </button>
            <button wire:click="deleteProyek"
                class="px-4 py-2 rounded-3xl bg-red-500 text-white text-xs hover:bg-red-600 transition">
                Yes, Delete
            </button>
        </div>
    </div>
</div>
@endif

</div>
</body>
