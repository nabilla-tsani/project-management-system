<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoice->nomor_invoice }}</title>
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

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 8px 12px;
            text-align: left;
        }

        th {
            background-color: #3498db;
            color: white;
        }

        .info p {
            margin: 4px 0;
        }

        .highlight {
            font-weight: bold;
        }

        .status {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 12px;
            color: white;
        }

        .belum_dibayar { background-color: #e74c3c; }
        .diproses { background-color: #f39c12; }
        .dibayar { background-color: #27ae60; }
    </style>
</head>
<body>
    <h2>Invoice: {{ $invoice->nomor_invoice }}</h2>

    <div class="section info">
        <h3>Informasi Proyek</h3>
        <p><span class="highlight">Nama Proyek:</span> {{ $invoice->proyek->nama_proyek ?? '-' }}</p>
        <p><span class="highlight">Customer:</span> {{ $invoice->proyek->customer?->nama ?? '-' }}</p>
        <p><span class="highlight">Lokasi:</span> {{ $invoice->proyek->lokasi ?? '-' }}</p>
        <p><span class="highlight">Total Anggaran:</span> Rp {{ number_format($invoice->proyek->anggaran ?? 0,0,',','.') }}</p>
    </div>

    <div class="section info">
        <h3>Detail Invoice</h3>
        <p><span class="highlight">Judul:</span> {{ $invoice->judul_invoice }}</p>
        <p><span class="highlight">Jumlah:</span> Rp {{ number_format($invoice->jumlah,0,',','.') }}</p>
        <p><span class="highlight">Tanggal:</span> {{ \Carbon\Carbon::parse($invoice->tanggal_invoice)->format('d/m/Y') }}</p>
        <p><span class="highlight">Keterangan:</span> {{ $invoice->keterangan ?: '-' }}</p>
        <p><span class="highlight">Status:</span>
            <span class="status {{ $invoice->status }}">
                {{ $invoice->status === 'belum_dibayar' ? 'Belum Dibayar' : ucfirst($invoice->status) }}
            </span>
        </p>
    </div>
</body>
</html>
