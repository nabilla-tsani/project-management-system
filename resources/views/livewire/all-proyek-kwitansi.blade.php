<div class="bg-white shadow rounded-xl p-4">
    <h2 class="text-xl font-bold mb-3">Daftar Kwitansi</h2>

    {{-- ALERT SUCCESS FLASH --}}
    @if(session('success'))
        <div class="mb-3 p-2 bg-green-100 text-green-700 rounded">
            {{ session('success') }}
        </div>
    @endif

    <table class="w-full border border-gray-300">
        <thead class="bg-gray-100">
            <tr>
                <th class="border px-3 py-2">Nomor Kwitansi</th>
                <th class="border px-3 py-2">Nomor Invoice</th>
                <th class="border px-3 py-2">Judul</th>
                <th class="border px-3 py-2">Jumlah</th>
                <th class="border px-3 py-2">Tanggal</th>
                <th class="border px-3 py-2">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($kwitansis as $kwitansi)
                <tr>
                    <td class="border px-3 py-2">{{ $kwitansi->nomor_kwitansi }}</td>
                    <td class="border px-3 py-2">{{ $kwitansi->nomor_invoice }}</td>
                    <td class="border px-3 py-2">{{ $kwitansi->judul_kwitansi }}</td>
                    <td class="border px-3 py-2 text-right">{{ number_format($kwitansi->jumlah, 0, ',', '.') }}</td>
                    <td class="border px-3 py-2">{{ \Carbon\Carbon::parse($kwitansi->tanggal_kwitansi)->format('d-m-Y') }}</td>
                    <td class="border px-3 py-2 text-center space-x-2">
                    <!-- Tombol Download -->
                    <a href="{{ route('proyek-kwitansi.print', $kwitansi->id) }}"
   target="_blank"
   class="text-blue-500 hover:text-blue-700"
   title="Preview Kwitansi">
    <i class="fa-solid fa-file-pdf"></i>
</a>


                    <!-- Tombol Hapus -->
                    <button 
                        @click="confirm('Yakin hapus kwitansi ini?') && $wire.deleteKwitansi({{ $kwitansi->id }})"
                        class="text-red-500 hover:text-red-700"
                        title="Hapus" >
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </td>

                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center py-3">Belum ada kwitansi</td>
                </tr>
            @endforelse
        </tbody>
    </table>

   {{-- POPUP INPUT KETERANGAN / PERINGATAN --}}
@if($showModal)
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg p-6 w-96">
            @if($kwitansiExisting)
                {{-- Jika sudah ada kwitansi --}}
                <h3 class="text-lg font-bold mb-4 text-red-600"> Peringatan</h3>
                <p class="mb-4">
                    Kwitansi untuk invoice ini sudah ada dengan nomor:
                    <span class="font-semibold">{{ $kwitansiExisting->nomor_kwitansi }}</span>
                    <br>judul :
                    <span class="font-semibold">{{ $kwitansiExisting->judul_kwitansi }}</span>
                </p>
                <div class="flex justify-end">
                    <button wire:click="$set('showModal', false)"
                        class="px-4 py-2 bg-gray-300 rounded">Tutup</button>
                </div>
            @else
                {{-- Form input keterangan --}}
                <h3 class="text-lg font-bold mb-4">Tambah Kwitansi</h3>
                <label class="block mb-2 text-sm font-semibold">Keterangan</label>
                <textarea wire:model="keterangan" class="w-full border rounded p-2 mb-4"></textarea>
                <div class="flex justify-end gap-2">
                    <button wire:click="$set('showModal', false)"
                        class="px-4 py-2 bg-gray-300 rounded">Batal</button>
                    <button wire:click="simpanKwitansi"
                        class="px-4 py-2 bg-blue-600 text-white rounded">Simpan</button>
                </div>
            @endif
        </div>
    </div>
@endif

</div>
