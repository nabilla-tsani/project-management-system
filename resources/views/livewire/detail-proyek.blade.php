<div class="p-6 bg-white shadow rounded">
    <h2 class="text-2xl font-semibold mb-4">Detail Proyek</h2>

    <div class="mb-4">
        <p><strong>Nama Proyek:</strong> {{ $proyek->nama_proyek }}</p>
        <p><strong>Customer:</strong> {{ $proyek->customer?->nama }}</p>
        <p><strong>Deskripsi:</strong> {{ $proyek->deskripsi }}</p>
        <p><strong>Lokasi:</strong> {{ $proyek->lokasi }}</p>
        <p><strong>Anggaran:</strong> {{ $proyek->anggaran }}</p>
        <p><strong>Tanggal Mulai:</strong> {{ $proyek->tanggal_mulai }}</p>
        <p><strong>Tanggal Selesai:</strong> {{ $proyek->tanggal_selesai }}</p>
        <p><strong>Status:</strong> {{ $proyek->status }}</p>
    </div>
    @livewire('all-proyek-user', ['proyekId' => $proyek->id])
    @livewire('all-proyek-fitur', ['proyekId' => $proyek->id])



    <a href="{{ route('proyek') }}"
       class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">
       ← Kembali ke Daftar Proyek
    </a>
</div>
