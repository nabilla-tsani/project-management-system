<div class="p-6 bg-gray-50 min-h-screen">

    {{-- Judul Halaman --}}
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-xl font-bold text-gray-800 flex items-center gap-2">
            <i class="fa-solid fa-receipt text-blue-600"></i> Daftar Kwitansi
        </h3>
        <button wire:click="$set('openModal', true)"
                class="px-4 py-2 bg-blue-600 text-white rounded-xl shadow hover:bg-blue-700 hover:shadow-md 
                       transition-all duration-200 flex items-center gap-2 text-sm font-medium">
            <i class="fa-solid fa-plus"></i> Tambah Kwitansi
        </button>
    </div>

    {{-- Daftar Kwitansi --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        @forelse($kwitansis as $kwitansi)
            <div class="bg-white shadow-md border border-gray-300 relative overflow-hidden
                        hover:shadow-lg transition-transform duration-300 transform hover:-translate-y-1
                        flex flex-col h-full"> {{-- flex-col & h-full agar footer menempel di bawah --}}

                {{-- Header --}}
                <div class="bg-gradient-to-r from-blue-100 to-blue-50 border-b border-dashed border-gray-400 px-5 py-3">
                    <div class="flex flex-col">
                        <h3 class="font-bold text-gray-800 text-lg tracking-wide leading-tight">
                            {{ $kwitansi->judul_kwitansi }}
                        </h3>
                        <span class="text-sm font-mono text-gray-600 mt-1">
                            #{{ $kwitansi->nomor_kwitansi }}
                        </span>
                    </div>
                </div>

                {{-- Isi Kwitansi --}}
                <div class="p-5 text-gray-800 text-sm space-y-2 flex-1"> {{-- flex-1 agar mengisi ruang --}}
                    <p><span class="font-semibold">No Invoice:</span> {{ $kwitansi->nomor_invoice }}</p>
                    <p><span class="font-semibold">Tanggal:</span> 
                        {{ \Carbon\Carbon::parse($kwitansi->tanggal_kwitansi)->format('d M Y') }}
                    </p>
                    
                    @if(!empty($kwitansi->keterangan))
                        <p><span class="font-semibold">Keterangan:</span> {{ $kwitansi->keterangan }}</p>
                    @endif

                    <div class="border-t border-dashed border-gray-300 my-3"></div>

                    <div class="font-mono text-sm">
                        <span class="font-semibold text-gray-700">Jumlah Dibayarkan:</span>
                        <span class="font-bold text-green-700 text-base block mt-1">
                            Rp {{ number_format($kwitansi->jumlah, 0, ',', '.') }}
                        </span>
                    </div>

                    <div class="border-t border-dashed border-gray-300 my-3"></div>

                    <div class="text-xs text-gray-500">
                        Dibuat oleh: <span class="font-medium">{{ $kwitansi->user?->name ?? 'User' }}</span>
                    </div>
                </div>

                {{-- Footer Nota --}}
                <div class="bg-gray-50 border-t border-dashed border-gray-300 px-5 py-3 flex justify-between items-center text-sm mt-auto">
                    <div class="flex items-center gap-3">
                        <a href="{{ route('proyek-kwitansi.print', $kwitansi->id) }}" target="_blank"
                           class="text-blue-600 hover:text-blue-800 flex items-center gap-1" title="Lihat Kwitansi">
                            <i class="fa-solid fa-file-pdf"></i> <span>PDF</span>
                        </a>

                        <button 
                            x-data 
                            @click="if(confirm('Yakin ingin menghapus kwitansi ini?')) { $wire.deleteKwitansi({{ $kwitansi->id }}) }"
                            class="text-red-600 hover:text-red-800 flex items-center gap-1"
                            title="Hapus Kwitansi">
                            <i class="fa-solid fa-trash"></i> <span>Hapus</span>
                        </button>
                    </div>
                </div>

                {{-- Efek "sobekan" --}}
                <div class="absolute bottom-0 left-0 right-0 h-3 bg-gray-50 flex justify-between px-3">
                    @for ($i = 0; $i < 10; $i++)
                        <div class="w-2 h-2 bg-white rounded-full border border-gray-300"></div>
                    @endfor
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-10 bg-white rounded-xl border border-gray-200 shadow-sm">
                <i class="fa-regular fa-file-lines text-gray-400 text-4xl mb-2"></i>
                <p class="text-gray-500 text-sm">Belum ada kwitansi</p>
            </div>
        @endforelse
    </div>
</div>
