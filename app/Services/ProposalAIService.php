<?php

namespace App\Services;

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\SimpleType\Jc;
use Illuminate\Support\Str;

class ProposalAIService
{

    private function addHeader($section)
    {
        $header = $section->addHeader();
        $header->addImage(
            storage_path('app/public/proyek_files/header-proposal.png'),
            [
                'width' => 700,
                'height' => 80,
                'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER,
            ]
        );
    }

    public function generate($proyek, $aiResponse)
    {
        $phpWord = new PhpWord();

        // Gaya umum
        $phpWord->setDefaultFontName('Times New Roman');
        $phpWord->setDefaultFontSize(12);

        // =======================================================
        // 📄 SECTION 1 - COVER
        // =======================================================
        $section1 = $phpWord->addSection();
        $this->addHeader($section1);
        $section1->addTextBreak(6);
        $section1->addText(
            strtoupper("PENGEMBANGAN " . $proyek->nama_proyek),
            ['bold' => true, 'size' => 18],
            ['alignment' => Jc::CENTER]
        );
        $section1->addTextBreak(8);
        $section1->addText("Ditujukan Kepada :", ['alignment' => Jc::CENTER]);
        $section1->addText(
            $proyek->customer->nama ?? '-',
            ['bold' => true, 'size' => 14],
            ['alignment' => Jc::CENTER]
        );

        // =======================================================
        // 📄 SECTION 2 - DAFTAR ISI
        // =======================================================
        $section2 = $phpWord->addSection();
        $this->addHeader($section2);
        $section2->addText("DAFTAR ISI", ['bold' => true, 'size' => 14], ['alignment' => Jc::CENTER]);
        $section2->addTextBreak(1);
        $daftarIsi = [
        ];
        foreach ($daftarIsi as $item) {
            $section2->addText($item);
        }

        // =======================================================
        // 📄 SECTION 3 - SURAT PENAWARAN
        // =======================================================
        $section3 = $phpWord->addSection();
        $this->addHeader($section3);
        $section3->addText("Nomor : 003/JSD/SPH/III/2025");
        $section3->addText("Lampiran : -");
        $section3->addText("Perihal : Penawaran " . $proyek->nama_proyek);
        $section3->addTextBreak(1);
        $section3->addText("Kepada Yth,");
        $section3->addText($proyek->customer->nama ?? '-');
        $section3->addText("Ditempat");
        $section3->addTextBreak(1);
        $section3->addText("Dengan Hormat,");
        $section3->addText(
            "Bersama ini kami CV. Jenderal Solusi Digital bermaksud mengajukan penawaran untuk pelaksanaan pekerjaan " .
            $proyek->nama_proyek . "."
        );
        $section3->addText(
            "Untuk pelaksanaan pekerjaan kami ajukan opsi penawaran biaya dengan rincian sebagai berikut:"
        );
        $section3->addText(
            "1. Pengembangan " . $proyek->nama_proyek .
            " dengan biaya sebesar Rp. " . number_format($proyek->anggaran, 0, ',', '.') .
            " + gratis server 3 bulan setelah implementasi dengan mekanisme pembayaran langsung atau termin."
        );
        $section3->addText(
            "dan sebagai bahan pertimbangan, bersama ini kami lampirkan komponen pengembangan."
        );
        $section3->addText(
            "Demikian penawaran kami atas diterima dan dikabulkannya kami sampaikan ucapan terima kasih."
        );
        $section3->addTextBreak(2);
        $section3->addText("CV Jenderal Solusi Digital", ['bold' => true]);
        $section3->addText("Rian Kusdiono, S.E.");
        $section3->addText("Direktur");

        // =======================================================
        // 📄 SECTION 4 - LATAR BELAKANG (AI)
        // =======================================================
        $section4 = $phpWord->addSection();
        $this->addHeader($section4);
        $section4->addText("I. LATAR BELAKANG PENGEMBANGAN", ['bold' => true, 'size' => 14]);
        $section4->addText($aiResponse, ['size' => 12]);

        $section4->addText("II. TENTANG PERUSAHAAN", ['bold' => true, 'size' => 14]);
        $section4->addText(
            "Jenderal Solusi Digital merupakan perusahaan teknologi informasi yang bergerak dalam bidang rekayasa perangkat lunak dan pengembangan sistem informasi yang bertempat di Purwokerto, Kabupaten Banyumas, Provinsi Jawa Tengah. " .
            "Jenderal Solusi Digital mengkhususkan pada pengembangan aplikasi web, aplikasi mobile, website dan aplikasi lainnya berbasis web. " .
            "Selain itu kami memberikan layanan konsultasi (Program Pelatihan dan Edukasi) untuk meningkatkan sumber daya manusia dalam bidang sistem administrasi."
        );

        $section4->addTextBreak(1);
        $section4->addText("III. PERNYATAAN VISI", ['bold' => true, 'size' => 14]);
        $section4->addText(
            "Visi kami adalah untuk menghasilkan layanan berkualitas tinggi yang terjangkau dan fleksibel untuk klien kami. " .
            "Kami ingin membuat klien kami senang dengan membuat aplikasi yang sesuai dengan kebutuhan klien sehingga meningkatkan kualitas produk dan jasa klien."
        );

        $section4->addTextBreak(1);
        $section4->addText("IV. PERNYATAAN MISI", ['bold' => true, 'size' => 14]);
        $section4->addText(
            "Misi kami adalah membuat klien kami senang dengan membuat aplikasi yang akurat yang pasti akan membantu dalam pelayanan dan branding mereka. " .
            "Kami akan menghasilkan layanan berkualitas tinggi yang terjangkau dan fleksibel untuk klien kami."
        );

        $section4->addText("V. LEGALITAS PERUSAHAAN", ['bold' => true, 'size' => 14]);
        $section4->addText("a) NAMA LEGAL PERUSAHAAN: CV Jenderal Solusi Digital");
        $section4->addText("b) AKTA NOTARIS: MUHAMMAD DWI KUNCORO, S.H., M.Kn.");
        $section4->addText("c) NIB: 2507720043689");
        $section4->addText("d) AHU: AHU-0046697-AH.01.14");
        $section4->addText("e) PKP: S-263/PKP/KPP.320103/2022");
        $section4->addText("f) NPWP: 60.095.803.7-521.000");

        $section4->addTextBreak(1);
        $section4->addText("VI. MODUL ATAU FITUR SISTEM", ['bold' => true, 'size' => 14]);
        $section4->addText(
            "1. Berikut merupakan komponen rencana pengembangan modul atau fitur " . $proyek->nama_proyek . " :"
        );

        $table = $section4->addTable(['borderSize' => 6, 'borderColor' => '000000']);
        $table->addRow();
        $table->addCell(800)->addText('No', ['bold' => true]);
        $table->addCell(5000)->addText('Pengembangan Modul', ['bold' => true]);
        $table->addCell(5000)->addText('Keterangan', ['bold' => true]);

        $section4->addTextBreak(1);
        $section4->addText("2. Timeline pengerjaan pengembangan sistem adalah 2 (dua) bulan dimulai setelah penandatanganan MoU atau Perjanjian Kerjasama.");
        $section4->addText("3. Gratis sewa server selama 3 (tiga) bulan setelah masa pengembangan atau setelah implementasi.");

        $section4->addText("VII. LAYANAN", ['bold' => true, 'size' => 14]);
        $section4->addText(
            "CV Jenderal Solusi Digital memberikan layanan terbaik untuk klien dari hulu ke hilir dalam pengembangan Sistem Informasi. " .
            "Kami melakukan analisis sistem bersama klien, bekerja sesuai timeline, menyesuaikan teknologi dengan kebutuhan, memberikan maintenance gratis 3 bulan, pelatihan pengguna, dan user manual sistem."
        );

        $section4->addTextBreak(1);
        $section4->addText("VIII. KONTAK", ['bold' => true, 'size' => 14]);
        $section4->addText("1. ALAMAT PERUSAHAAN");
        $section4->addText("   Jalan Menteri Supeno, Perum Griya Permata Residence No B9, Sokaraja, Banyumas, Jawa Tengah 53181");
        $section4->addText("2. NOMOR TELEPON");
        $section4->addText("   085172378297");
        $section4->addText("3. EMAIL");
        $section4->addText("   jenderalsolusidigital@gmail.com");
        $section4->addText("4. WEBSITE");
        $section4->addText("   https://jenderalcorp.com/");

        // =======================================================
        // 💾 SIMPAN FILE
        // =======================================================
        $fileName = 'Proposal-Proyek-' . Str::slug($proyek->nama_proyek) . '.docx';
        $filePath = storage_path('app/public/' . $fileName);
        $writer = IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($filePath);

        return $filePath;
    }
}
