<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Kwitansi {{ $kwitansi->nomor_kwitansi }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 14px;
            color: #333;
            line-height: 1.5;
            margin: 0;
            padding: 20px;
        }

        h2, h3 {
            margin: 0 0 10px 0;
            color: #2c3e50;
        }

        h2 {
            font-size: 24px;
            border-bottom: 2px solid #3498db;
            padding-bottom: 5px;
        }

        h3 {
            font-size: 18px;
            margin-top: 20px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }

        .section {
            margin-bottom: 20px;
        }

        .info p {
            margin: 4px 0;
        }

        .highlight {
            font-weight: bold;
        }

        .jumlah {
            font-size: 18px;
            font-weight: bold;
            color: #27ae60;
        }

        .stamp {
        color: #e74c3c;
        font-size: 20px;       /* sebelumnya 40px */
        font-weight: bold;
        border: 2px solid #e74c3c;  /* sebelumnya 4px */
        display: inline-block;
        padding: 5px 15px;     /* sebelumnya 10px 30px */
        text-transform: uppercase;
        border-radius: 6px;    /* lebih kecil */
        transform: rotate(-15deg);
        opacity: 0.8;
        margin-top: 15px;
        }


    </style>
</head>
<body>
    <h2>Kwitansi: {{ $kwitansi->nomor_kwitansi }}</h2>

    <div class="section info">
        <h3>Informasi Proyek</h3>
        <p><span class="highlight">Nama Proyek:</span> {{ $kwitansi->proyek->nama_proyek ?? '-' }}</p>
        <p><span class="highlight">Customer:</span> {{ $kwitansi->proyek->customer?->nama ?? '-' }}</p>
        <p><span class="highlight">Lokasi:</span> {{ $kwitansi->proyek->lokasi ?? '-' }}</p>
        <p><span class="highlight">Total Anggaran:</span> Rp {{ number_format($kwitansi->proyek->anggaran ?? 0,0,',','.') }}</p>
    </div>

    <div class="section info">
    <h3>Detail Kwitansi</h3>
    <p><span class="highlight">Judul:</span> {{ $kwitansi->judul_kwitansi }}</p>
    <p><span class="highlight">Nomor Invoice:</span> {{ $kwitansi->nomor_invoice }}</p>
    <p><span class="highlight">Jumlah:</span> <span class="jumlah">Rp {{ number_format($kwitansi->jumlah,0,',','.') }}</span></p>
    <p><span class="highlight">Tanggal Kwitansi:</span> {{ \Carbon\Carbon::parse($kwitansi->tanggal_kwitansi)->format('d/m/Y') }}</p>
    <p><span class="highlight">Keterangan:</span> {{ $kwitansi->keterangan ?: '-' }}</p>
    <p><span class="highlight">Status:</span> 
<div class="stamp">
    LUNAS
</div>    </p>
</div>

</body>
</html>
