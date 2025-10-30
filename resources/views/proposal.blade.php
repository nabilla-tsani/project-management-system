<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Proposal {{ $proyek->nama_proyek }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            line-height: 1.6;
            margin: 40px;
        }
        h1, h2, h3 {
            text-align: center;
            margin: 0;
            padding: 0;
        }
        h1 { font-size: 20px; text-transform: uppercase; margin-bottom: 10px; }
        h2 { font-size: 16px; margin-top: 20px; text-align: left; }
        p { text-align: justify; }
        .page-break { page-break-after: always; }
        .table-info {
            width: 100%;
            border-collapse: collapse;
        }
        .table-info th, .table-info td {
            border: 1px solid #000;
            padding: 5px;
        }
        .table-info th {
            background-color: #f0f0f0;
        }
        ul { margin: 0; padding-left: 20px; }
    </style>
</head>
<body>

    {{-- ================= HALAMAN 1 ================= --}}
    <h1>{{ $proyek->nama_proyek }}</h1>
    <p style="text-align:center;">Ditujukan Kepada:</p>
    <h3 style="text-align:center;">{{ $proyek->customer->nama }}</h3>

    <div class="page-break"></div>

    {{-- ================= HALAMAN 2: DAFTAR ISI ================= --}}
    <h1>Daftar Isi</h1>
    <ul>
        <li>Halaman 1: Judul Proyek</li>
        <li>Halaman 2: Daftar Isi</li>
        <li>Halaman 3: Surat Penawaran</li>
        <li>Halaman 4: Latar Belakang</li>
        <li>Halaman 5: Tentang Perusahaan</li>
        <li>Halaman 6: Visi & Misi</li>
        <li>Halaman 7: Legalitas Perusahaan</li>
        <li>Halaman 8: Modul & Fitur Sistem</li>
        <li>Halaman 9: Layanan</li>
        <li>Halaman 10: Kontak</li>
    </ul>

    <div class="page-break"></div>

    {{-- ================= HALAMAN 3: SURAT PENAWARAN ================= --}}
    <h2 style="text-align:left;">Kop Surat</h2>
    <p>Nomor : -<br> Lampiran : -<br> Perihal : Penawaran {{ $proyek->nama_proyek }}</p>

    <p>Kepada Yth,<br>
    {{ $proyek->customer->nama }}<br>
    Di tempat</p>

    <p>Dengan Hormat,</p>
    <p>
        Bersama ini kami CV. Jenderal Solusi Digital bermaksud mengajukan penawaran
        untuk pelaksanaan pekerjaan <strong>{{ $proyek->nama_proyek }}</strong>.
    </p>
    <p>
        Untuk pelaksanaan pekerjaan tersebut, kami ajukan opsi penawaran biaya dengan rincian sebagai berikut:
    </p>
    <p>
        1. Pengembangan <strong>{{ $proyek->nama_proyek }}</strong> dengan biaya sebesar 
        <strong>Rp {{ number_format($proyek->anggaran, 0, ',', '.') }}</strong>
    </p>
    <p>
        Sebagai bahan pertimbangan, bersama ini kami lampirkan komponen pengembangan.
        Demikian penawaran kami. Atas diterimanya penawaran ini kami sampaikan terima kasih.
    </p>

    <br><br>
    <p>
        CV. Jenderal Solusi Digital<br>
        Rian Kusdiono, S.E.<br>
        Direktur
    </p>

    <div class="page-break"></div>

    {{-- ================= HALAMAN 4: LATAR BELAKANG ================= --}}
    <h2>I. LATAR BELAKANG PENGEMBANGAN</h2>
    <p>{!! nl2br(e($aiDeskripsi)) !!}</p>


    <h2>II. TENTANG PERUSAHAAN</h2>
    <p>
        Jenderal Solusi Digital merupakan perusahaan teknologi informasi yang bergerak dalam bidang rekayasa perangkat lunak dan pengembangan sistem informasi yang bertempat di Purwokerto, Kabupaten Banyumas, Provinsi Jawa Tengah.
        <br>
        Jenderal Solusi Digital mengkhususkan pada pengembangan aplikasi web, aplikasi mobile, website dan aplikasi lainnya berbasis web.
        <br>
        Selain itu kami memberikan layanan konsultasi (Program Pelatihan dan Edukasi) untuk meningkatkan sumber daya manusia dalam bidang sistem administrasi.
    </p>

    <h2>III. PERNYATAAN VISI</h2>
    <p>
        Visi kami adalah untuk menghasilkan layanan berkualitas tinggi yang terjangkau dan fleksibel untuk klien kami.
        Kami ingin membuat klien kami senang dengan membuat aplikasi yang sesuai dengan kebutuhan klien sehingga
        meningkatkan kualitas produk dan jasa klien.
    </p>


    <h2>IV. PERNYATAAN MISI</h2>
    <p>
        Misi kami adalah membuat klien kami senang dengan membuat aplikasi yang akurat yang pasti akan membantu
        dalam pelayanan dan branding mereka. Kami akan menghasilkan layanan berkualitas tinggi yang terjangkau
        dan fleksibel untuk klien kami.
    </p>


    <h2>V. LEGALITAS PERUSAHAAN</h2>
    <p>
        a) NAMA LEGAL PERUSAHAAN <br> CV Jenderal Solusi Digital adalah nama legal perusahaan kami.<br>
        b) AKTA NOTARIS <br> Perusahaan kami terdaftar pada akta notaris MUHAMMAD DWI KUNCORO, S.H., M.Kn.<br>
        c) NOMOR INDUK BERUSAHA (NIB) <br> Nomor Induk Berusaha (NIB) kami adalah 2507720043689.
        d) Nomor AHU <br> Nomor AHU Perusahaan Kami adalah AHU-0046697-AH.01.14
        e) NOMOR PENGUSAHA KENA PAJAK (PKP) <br> Nomor PKP Perusahaan Kami adalah S-263/PKP/KPP.320103/2022
        f) NOMOR POKOK WAJIB PAJAK (NPWP) <br> NPWP Perusahaan Kami adalah 60.095.803.7-521.000
    </p>


    <h2>VI. MODUL ATAU FITUR SISTEM</h2>
    <p>1. Berikut merupakan komponen rencana {{ $proyek->nama_proyek }} dengan memperhatikan modul atau fitur :</p>
    <table class="table-info">
        <thead>
            <tr>
                <th>No</th>
                <th>Pengembangan Modul</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            {{-- Kosongkan tabel ini dulu --}}
            @for($i = 1; $i <= 5; $i++)
                <tr>
                    <td style="text-align:center;">{{ $i }}</td>
                    <td></td>
                    <td></td>
                </tr>
            @endfor
        </tbody>
    </table>

    <p>
        2. Timeline pengerjaan pengembangan sistem adalah 2 (dua) bulan dimulai setelah penandatanganan MoU atau perjanjian kerja sama.<br>
        3. Gratis sewa server selama 3 (tiga) bulan setelah masa pengembangan atau setelah implementasi.
    </p>


    <h2>VII. LAYANAN</h2>
    <ul>
        CV Jenderal Solusi Digital memberikan layanan terbaik untuk klien dari hulu ke hilir dalam pengembangan Sistem Informasi.
        <li>CV Jenderal Solusi Digital melakukan analisis sistem yang akan dikembangkan secara intens dengan berdiskusi bersama klien.</li>
        <li>CV Jenderal Solusi digital bekerja dalam timeline yang telah disepakati bersama klien</li>
        <li>Teknologi yang digunakan dalam pengembangan disesuaikan dengan kebutuhan fitur dan infrastruktur IT klien.</li>
        <li>CV Jenderal Solusi Digital memberikan maintenance secara gratis selama 3 bulan setelah sistem informasi diimplementasi</li>
        <li>CV Jenderal Solusi Digital memberikan pelatihan dan pendampingan pengunaan Sistem Informasi pada Pihak Klien</li>
        <li>CV Jenderal Solusi Digital memberikan user manual</li>
    </ul>


    <h2>VIII. KONTAK</h2>
    <p>
        1. Alamat: Jalan Menteri Supeno, Perum Griya Permata Residence No B9, Kecamatan Sokaraja, Banyumas, Jawa Tengah 53181<br>
        2. Nomor Telepon: 085172378297<br>
        3. Email: jenderalsolusidigital@gmail.com<br>
        4. Website: https://jenderalcorp.com
    </p>

</body>
</html>
