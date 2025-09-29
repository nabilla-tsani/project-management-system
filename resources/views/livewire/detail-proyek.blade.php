<div class="p-8">
    {{-- Header Judul + Tombol PDF --}}
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
            {{ $proyek->nama_proyek }}
        </h1>
        <a href="{{ route('proposal-proyek.pdf', $proyek->id) }}" target="_blank"
        class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md shadow hover:bg-green-700 transition">
            <i class="fa-solid fa-file-export"></i>
            Generate Proposal
        </a>
    </div>

    {{-- Card Detail --}}
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-base text-gray-800">
                <div>
                    <p class="text-gray-500 text-sm">Customer</p>
                    <p class="font-semibold">{{ $proyek->customer?->nama }}</p>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">Lokasi</p>
                    <p class="font-semibold">{{ $proyek->lokasi ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">Anggaran</p>
                    <p class="font-semibold">
                        Rp {{ number_format($proyek->anggaran, 0, ',', '.') }}
                    </p>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">Status</p>
                    <span class="inline-block px-2 py-1 text-sm font-medium rounded 
                        @if($proyek->status === 'belum_dimulai') bg-yellow-100 text-yellow-800
                        @elseif($proyek->status === 'sedang_berjalan') bg-blue-100 text-blue-800
                        @elseif($proyek->status === 'selesai') bg-green-100 text-green-800
                        @else bg-gray-100 text-gray-700 @endif">
                        {{ ucfirst(str_replace('_',' ',$proyek->status)) }}
                    </span>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">Tanggal Mulai</p>
                    <p class="font-semibold">{{ $proyek->tanggal_mulai }}</p>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">Tanggal Selesai</p>
                    <p class="font-semibold">{{ $proyek->tanggal_selesai }}</p>
                </div>
                <div class="md:col-span-2">
                    <p class="text-gray-500 text-sm">Deskripsi</p>
                    <p class="font-semibold">{{ $proyek->deskripsi ?? '-' }}</p>
                </div>
            </div>
        </div>

        
    </div>

    {{-- Livewire Bagian --}}
    <div class="mt-8 space-y-6">
        @livewire('all-proyek-user', ['proyekId' => $proyek->id])
        @livewire('all-proyek-fitur', ['proyekId' => $proyek->id])
        @livewire('all-proyek-file', ['proyekId' => $proyek->id])
        @livewire('all-proyek-invoice', ['proyekId' => $proyek->id])
        @livewire('all-proyek-kwitansi', ['proyekId' => $proyek->id])
    </div>

    {{-- Footer --}}
        <div class="flex items-center justify-start py-4 bg-gray-50 border-t">
            <a href="{{ route('proyek') }}"
               class="px-4 py-2 bg-blue-600 text-white text-sm rounded-md shadow-sm hover:bg-gray-200 transition">
                ← Kembali
            </a>
        </div>
</div>
