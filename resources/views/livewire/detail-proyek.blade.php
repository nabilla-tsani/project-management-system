<div>
    {{-- Card Utama --}}
    <div class="bg-white shadow-lg rounded-2xl p-6 transition-transform transform">
        <div class="flex items-center justify-between mb-5">
        <h2 class="text-xl font-bold text-gray-800 mb-5 flex items-center gap-2">
            Informasi Proyek
        </h2>
        <div class="flex justify-end">
            <a href="{{ route('proposal-proyek.pdf', $proyek->id) }}" target="_blank"
            class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-xl shadow hover:bg-blue-700 transition">
                <i class="fa-solid fa-file-export"></i> Generate Proposal
            </a>

             <button 
    wire:click="generateProposalWithAI"
    wire:loading.attr="disabled"
    class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-cyan-400 to-purple-600 text-white text-sm font-medium rounded-xl shadow hover:bg-green-700 transition cursor-pointer ml-3 disabled:opacity-70">

    {{-- Normal state (tidak loading) --}}
    <span wire:loading.remove wire:target="generateProposalWithAI" class="inline-flex items-center gap-2">
        <i class="fa-solid fa-wand-magic-sparkles"></i>
        Generate Proposal with AI
    </span>

    {{-- Loading state --}}
    <span wire:loading.flex wire:target="generateProposalWithAI" class="items-center gap-2">
        <i class="fa-solid fa-spinner fa-spin"></i>
        Proposal sedang diproses...
    </span>
</button>

        </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 text-gray-800 mx-6">

            {{-- Customer --}}
            <div class="flex items-center gap-3">
                <i class="fas fa-user-tie text-blue-500 text-xl"></i>
                <div>
                    <p class="text-gray-400 text-sm">Customer</p>
                    <p class="font-semibold">{{ $proyek->customer?->nama ?? '-' }}</p>
                </div>
            </div>

            {{-- Lokasi --}}
            <div class="flex items-center gap-3">
                <i class="fas fa-map-marker-alt text-red-500 text-xl"></i>
                <div>
                    <p class="text-gray-400 text-sm">Lokasi</p>
                    <p class="font-semibold">{{ $proyek->lokasi ?? '-' }}</p>
                </div>
            </div>

            {{-- Anggaran --}}
            <div class="flex items-center gap-3">
                <i class="fas fa-coins text-green-500 text-xl"></i>
                <div>
                    <p class="text-gray-400 text-sm">Anggaran</p>
                    <p class="font-semibold">Rp {{ number_format($proyek->anggaran,0,',','.') }}</p>
                </div>
            </div>

            {{-- Status --}}
            <div class="flex items-center gap-3">
                <i class="fas fa-flag text-yellow-500 text-xl"></i>
                <div>
                    <p class="text-gray-400 text-sm">Status</p>
                    <span class="inline-block px-3 py-1 text-sm font-medium rounded-full
                        @if($proyek->status === 'belum_dimulai') bg-blue-100 text-blue-800
                        @elseif($proyek->status === 'sedang_berjalan') bg-yellow-100 text-yellow-800
                        @elseif($proyek->status === 'selesai') bg-green-100 text-green-800
                        @else bg-gray-100 text-gray-700 @endif">
                        {{ ucfirst(str_replace('_',' ',$proyek->status)) }}
                    </span>
                </div>
            </div>

            {{-- Tanggal Mulai --}}
            <div class="flex items-center gap-3">
                <i class="fas fa-calendar-day text-indigo-500 text-xl"></i>
                <div>
                    <p class="text-gray-400 text-sm">Tanggal Mulai</p>
                    <p class="font-semibold">{{ $proyek->tanggal_mulai ?? '-' }}</p>
                </div>
            </div>

            {{-- Tanggal Selesai --}}
            <div class="flex items-center gap-3">
                <i class="fas fa-calendar-check text-green-500 text-xl"></i>
                <div>
                    <p class="text-gray-400 text-sm">Tanggal Selesai</p>
                    <p class="font-semibold">{{ $proyek->tanggal_selesai ?? '-' }}</p>
                </div>
            </div>

            {{-- Progress bar --}}
            <div class="md:col-span-3 mt-4">
                <div class="flex items-center gap-2 mb-1">
                    <i class="fas fa-tasks text-blue-500 text-lg"></i>
                    <p class="text-gray-400 text-sm">Progress Proyek</p>
                </div>
                @php
                    $progress = $proyek->status === 'belum_dimulai' ? 0 : ($proyek->status === 'sedang_berjalan' ? 50 : 100);
                @endphp
                <div class="w-full bg-gray-200 h-3 rounded-full">
                    <div class="h-3 rounded-full transition-all duration-500"
                         style="width: {{ $progress }}%; background: linear-gradient(to right, #4f46e5, #3b82f6);"></div>
                </div>
            </div>

            {{-- Deskripsi --}}
            <div class="md:col-span-3 mt-4">
                <div class="flex items-center gap-2 mb-1">
                    <i class="fas fa-file-alt text-gray-500 text-lg"></i>
                    <p class="text-gray-400 text-sm">Deskripsi</p>
                </div>
                <p class="font-medium text-gray-700">{{ $proyek->deskripsi ?? '-' }}</p>
            </div>
        </div>
    </div>
</div>
