<div class="pt-0 p-2 space-y-2">
<div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <h2 class="text-md font-medium flex items-center gap-2 text-[#5ca9ff]">
                <i class="fa-solid fa-circle-info"></i>
                Project Information
            </h2>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('proposal-proyek.pdf', $proyek->id) }}" target="_blank"
            class="inline-flex items-center gap-2 px-4 py-1.5 bg-[#5ca9ff] text-white text-xs font-medium rounded-3xl shadow hover:scale-105 transition">
                <i class="fa-solid fa-file-export"></i> Generate Proposal
            </a>
            <button 
                wire:click="generateProposalWithAI"
                wire:loading.attr="disabled"
                class="inline-flex items-center gap-2 px-4 py-1.5 bg-gradient-to-r from-cyan-400 to-purple-600 text-white text-xs font-medium rounded-3xl shadow hover:scale-105 transition cursor-pointer ml-3 disabled:opacity-70">

                {{-- Normal state (tidak loading) --}}
                <span wire:loading.remove wire:target="generateProposalWithAI" class="inline-flex items-center gap-2">
                    <i class="fa-solid fa-wand-magic-sparkles"></i>
                    Generate Proposal with AI
                </span>

                {{-- Loading state --}}
                <span wire:loading.flex wire:target="generateProposalWithAI" class="items-center gap-2">
                    <i class="fa-solid fa-spinner fa-spin"></i>
                    Generating proposal with AI...
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
                class="text-xs p-2 rounded bg-green-100 text-green-700 border border-green-300"
            >
                {{ session('success') }}
            </div>
        @endif

    {{-- Card Utama --}}
    <div class="bg-white border shadow-xl p-4 transition-transform transform">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 text-gray-800 mx-6 text-xs">

            {{-- Customer --}}
            <div class="flex items-center gap-3">
                <i class="fas fa-user-tie text-blue-500"></i>
                <div>
                    <p class="text-gray-400">Customer</p>
                    <p class="font-semibold">{{ $proyek->customer?->nama ?? '-' }}</p>
                </div>
            </div>

            {{-- Lokasi --}}
            <div class="flex items-center gap-3">
                <i class="fas fa-map-marker-alt text-red-500"></i>
                <div>
                    <p class="text-gray-400">Location</p>
                    <p class="font-semibold">{{ $proyek->lokasi ?? '-' }}</p>
                </div>
            </div>

            {{-- Anggaran --}}
            <div class="flex items-center gap-3">
                <i class="fas fa-coins text-green-500"></i>

                <div>
                    <p class="text-gray-400">Budget</p>
                    <p class="font-semibold">Rp {{ number_format($proyek->anggaran,0,',','.') }}</p>
                </div>

                <div class="ml-auto">
                    <button  
                        wire:click="edit({{ $proyek->id }})"
                        class="px-2 py-1.5 rounded-3xl text-white shadow hover:shadow-md transition-all duration-200 text-xs hover:scale-105"
                        style="background-color: #5ca9ff;">
                        <i class="fas fa-pen"></i>
                    </button>
                </div>
            </div>


            {{-- Status --}}
            <div class="flex items-center gap-3">
                <i class="fas fa-flag text-yellow-500"></i>
                <div>
                    <p class="text-gray-400">Status</p>
                    @php
                        $statusMap = [
                            'belum_dimulai'   => 'Upcoming',
                            'sedang_berjalan' => 'Ongoing',
                            'selesai'         => 'Done',
                            'ditunda'         => 'Pending',
                        ];
                    @endphp

                    <span class="inline-block px-3 py-1 font-medium rounded-full
                        @if($proyek->status === 'belum_dimulai') bg-blue-100 text-blue-800
                        @elseif($proyek->status === 'sedang_berjalan') bg-yellow-100 text-yellow-800
                        @elseif($proyek->status === 'selesai') bg-green-100 text-green-800
                        @elseif($proyek->status === 'ditunda') bg-red-100 text-red-800
                        @else bg-gray-100 text-gray-700 @endif">

                        {{ $statusMap[$proyek->status] ?? '-' }}

                    </span>

                </div>
            </div>

            {{-- Tanggal Mulai --}}
            <div class="flex items-center gap-3">
                <i class="fas fa-calendar-day text-indigo-500"></i>
                <div>
                    <p class="text-gray-400">Start Date</p>
                    <p class="font-semibold">{{ \Carbon\Carbon::parse($proyek->tanggal_mulai)->format('d M Y') ?? '-' }}</p>
                </div>
            </div>

            {{-- Tanggal Selesai --}}
            <div class="flex items-center gap-3">
                <i class="fas fa-calendar-check text-green-500"></i>
                <div>
                    <p class="text-gray-400">End Date</p>
                    <p class="font-semibold">{{ \Carbon\Carbon::parse($proyek->tanggal_selesai)->format('d M Y') ?? '-' }}</p>
                </div>
            </div>
            

            {{-- Progress bar --}}
            <div class="md:col-span-3 mt-2">
                <div class="flex items-center gap-2 mb-1">
                    <i class="fas fa-tasks text-blue-500 text-xs"></i>
                    <p class="text-gray-400 text-xs">Status Bar</p>
                </div>
                @php
                    $progress = match ($proyek->status) {
                        'sedang_berjalan' => 50,
                        'selesai' => 100,
                        'belum_dimulai', 'ditunda' => 0,
                        default => 0,
                    };
                @endphp

                <div class="w-full bg-gray-200 h-3 rounded-full">
                    <div class="h-3 rounded-full transition-all duration-500 bg-[#5ca9ff]"
                         style="width: {{ $progress }}%"></div>
                </div>
            </div>

            {{-- Deskripsi --}}
            <div class="md:col-span-3 mt-1">
                <div class="flex items-center gap-2 mb-1">
                    <i class="fas fa-file-alt text-gray-500 text-xs"></i>
                    <p class="text-gray-400 text-xs">Decription</p>
                </div>
                <p class="text-gray-700">{{ $proyek->deskripsi ?? '-' }}</p>
            </div>
        </div>
    </div>


    {{-- Customer Info --}}
    <div class="bg-white border shadow-xl p-4 transition-transform transform">
        <div class="flex items-center justify-between text-xs mx-6 mb-5">
            <h3 class="text-sm font-semibold text-gray-800 flex items-center gap-2">
                <i class="fas fa-user-tie text-blue-500"></i>
                Customer Information
            </h3>

            @if($proyek->customer->status === 'aktif')
                <span class="text-xs px-2 py-1 bg-green-100 text-green-600 rounded-3xl">
                Active
            </span>
            @elseif($proyek->customer->status === 'tidak_aktif')
            <span class="text-xs px-2 py-1 bg-red-100 text-red-600 rounded-3xl">
                Inactive
            </span>
            @endif
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-y-4 gap-x-8 mx-6 my-4 text-xs">

            <div>
                <p class="text-gray-500 mb-1">Full Name</p>
                <p class="font-medium text-gray-800">
                    {{ $proyek->customer->nama ?? '-' }}
                </p>
            </div>

            <div>
                <p class="text-gray-500 mb-1">Notes</p>
                <p class="font-medium text-gray-800">
                    {{ $proyek->customer->catatan ?? '-' }}
                </p>
            </div>

            <div>
                <p class="text-gray-500 mb-1">Email</p>
                <p class="font-medium text-gray-800 break-all">
                    {{ $proyek->customer->email ?? '-' }}
                </p>
            </div>

            <div>
                <p class="text-gray-500 mb-1">Phone Number</p>
                <p class="font-medium text-gray-800">
                    {{ $proyek->customer->nomor_telepon ?? '-' }}
                </p>
            </div>

            <div class="md:col-span-2">
                <p class="text-gray-500 mb-1">Address</p>
                <p class="font-medium text-gray-800">
                    {{ $proyek->customer->alamat ?? '-' }}
                </p>
            </div>

        </div>
    </div>
    {{-- Footer Tombol Kembali --}}
    <div class="flex justify-start pt-4">
        <a href="{{ route('proyek') }}"
           class="px-4 py-2 bg-[#5ca9ff] text-white text-[10px] rounded-3xl shadow hover:bg-[#884fd9] transition">
            Back to Project List
        </a>
    </div>


    {{-- Modal Edit --}}
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
                    Update Project
                </h3>
            </div>

            {{-- Body --}}
            <div class="p-5">
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
    
</div>
