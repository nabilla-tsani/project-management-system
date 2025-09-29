{{-- resources/views/proposal.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Proposal Proyek - {{ $proyek->customer?->nama }}</title>
    <style>
        body {
            font-family: 'Times New Roman', serif;
            font-size: 12pt;
            line-height: 1.5;
            margin: 50px;
            color: #000;
        }

        /* Kop surat */
        .kop-surat {
            text-align: center;
            margin-bottom: 20px;
        }
        .kop-surat h1 {
            margin: 0;
            font-size: 16pt;
            font-weight: bold;
        }
        .kop-surat h2 {
            margin: 0;
            font-size: 12pt;
            font-weight: normal;
        }
        .kop-surat hr {
            margin-top: 5px;
            border: 1px solid #000;
        }

        .tanggal {
            text-align: right;
            margin-bottom: 20px;
        }

        .section {
            margin-bottom: 15px;
        }

        .section h2 {
            font-size: 14pt;
            margin-bottom: 5px;
        }

        ul {
            margin-top: 0;
            padding-left: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
            margin-bottom: 10px;
        }
        table, th, td {
            border: 1px solid #000;
            padding: 5px;
        }

        .contact {
            font-size: 11pt;
            margin-top: 20px;
        }

        .signature {
            margin-top: 50px;
            text-align: left;
        }
    </style>
</head>
<body>

    <!-- Kop Surat -->
    <div class="kop-surat">
        <h1>CV. Jenderal Solusi Digital</h1>
        <h2>Jl. Amad I, Sokaraja Kidul, Kec. Sokaraja, Kabupaten Banyumas, Jawa Tengah 53181</h2>
        <h2>+62 851-7237-8297 | informasi@jenderalcorp.com</h2>
        <hr>
    </div>

    <!-- Tanggal -->
    <div class="tanggal">
        Sokaraja, {{ now()->format('d F Y') }}
    </div>

    <!-- Tujuan Surat -->
    <div class="section">
        <p>Kepada Yth:</p>
        <p><strong>{{ $proyek->customer?->nama }}</strong></p>
    </div>

    <!-- Salam Pembuka -->
    <div class="section">
        <p>Dengan hormat,</p>
        <p>Sehubungan dengan kebutuhan {{ $proyek->customer?->nama }} akan pengembangan website, kami mengajukan proposal proyek sebagai berikut:</p>
    </div>

    <!-- Deskripsi Proyek -->
    <div class="section">
        <h2>Deskripsi Proyek</h2>
        <p>{{ $proyek->deskripsi }}</p>
    </div>

    <div class="section">
        <h2>Lokasi Proyek</h2>
        <p>{{ $proyek->lokasi }}</p>
    </div>

    <!-- Lingkup Pekerjaan -->
    <div class="section">
        <h2>Lingkup Pekerjaan</h2>
        <ul>
            <li>Analisis kebutuhan & desain UI/UX.</li>
            <li>Pengembangan website sesuai desain.</li>
            <li>Integrasi fitur (form, gallery, payment gateway jika ada).</li>
            <li>Testing & QA.</li>
            <li>Deployment & Training penggunaan.</li>
        </ul>
    </div>

    <!-- Deliverables -->
    <div class="section">
        <h2>Deliverables</h2>
        <ul>
            <li>Website lengkap & akses admin.</li>
            <li>Panduan penggunaan.</li>
            <li>Hosting & domain (jika termasuk paket).</li>
        </ul>
    </div>

    <!-- Timeline -->
    <div class="section">
        <h2>Timeline</h2>
        <p>{{ \Carbon\Carbon::parse($proyek->tanggal_mulai)->format('d F Y') }} s.d. {{ \Carbon\Carbon::parse($proyek->tanggal_selesai)->format('d F Y') }}</p>
    </div>

    <!-- Anggaran -->
    <div class="section">
        <h2>Anggaran</h2>
        <p><strong>Rp {{ number_format($proyek->anggaran, 0, ',', '.') }}</strong></p>
    </div>

    <!-- Penutup -->
    <div class="section">
        <p>Demikian proposal ini kami ajukan, besar harapan kami dapat bekerja sama dengan {{ $proyek->customer?->nama }}. Atas perhatian dan kerjasamanya, kami ucapkan terima kasih.</p>
    </div>

    <!-- Tanda Tangan -->
    <div class="signature">
        <p>Hormat Kami,</p>
        <p>CV. Jenderal Solusi Digital</p>
        <br><br>
        <p>__________________________</p>
    </div>

</body>
</html>
