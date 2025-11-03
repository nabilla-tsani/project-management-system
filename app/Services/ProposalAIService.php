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
                'width' => 700, // atur sesuai lebar halaman
                'height' => 80,
                'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::LEFT,
                'marginTop' => 0,
                'marginLeft' => 0,
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
        $section1->addText(
            strtoupper("PENGEMBANGAN " . $proyek->nama_proyek),
            ['bold' => true, 'size' => 18],
            ['alignment' => Jc::CENTER]
        );
        $section1->addTextBreak(3);
        $section1->addImage(
            storage_path('app/public/proyek_files/logo.png'), // path file logo
            [
                'width' => 200,
                'height' => 200,
                'alignment' => Jc::CENTER,
            ]
        );
        $section1->addTextBreak(6);
        $textBox = $section1->addTextBox([
            'width' => 300, // lebar kotak (dalam twips, 1 cm = ±567 twips)
            'height' => 100, // tinggi kotak
            'borderSize' => 1, // ketebalan garis border
            'borderColor' => '000000', // warna border hitam
            'alignment' => Jc::LEFT, // posisi kotak di tengah halaman
        ]);
        $textBox->addTextBreak(1);
        $textBox->addText(
            "   Ditujukan Kepada :",
            ['size' => 12],
            ['alignment' => Jc::LEFT],
            ['indent' => 100]
        );
       $textBox->addText(
            "   " . ($proyek->customer->nama ?? '-'),
            ['bold' => true, 'size' => 14],
            ['alignment' => Jc::LEFT]
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
        $header = $section3->addHeader();
        $table = $header->addTable();
        $table->addRow();

        $cellLogo = $table->addCell(1200, ['valign' => 'center']);
        $cellLogo->addImage(
            storage_path('app/public/proyek_files/kop.png'), 
            [
                'width' => 70,
                'height' => 70,
                'alignment' => Jc::LEFT,
            ]
        );

        $cellText = $table->addCell(8000, ['valign' => 'center']);
        $cellText->addText(
            "JENDERAL SOLUSI DIGITAL",
            ['bold' => true, 'size' => 18],
            ['alignment' => Jc::CENTER]
        );
        $cellText->addText(
            "Jl Menteri Supeno, Perum Graha Residence Blok D3, Sokaraja, Banyumas 53181",
            ['size' => 11],
            ['alignment' => Jc::CENTER]
        );
        $cellText->addText(
            "jenderalcorp.com, 085172378297, jenderalsolusidigital@gmail.com",
            ['size' => 11],
            ['alignment' => Jc::CENTER]
        );
        $lineStyle = ['weight' => 3, 'width' => 460, 'height' => 0, 'color' => '000000'];
        $header->addLine($lineStyle);

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
            "Bersama ini kami CV. Jenderal Solusi Digital bermaksud mengajukan penawaran untuk pelaksanaan pekerjaan Pengembangan" .
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
        $table = $section3->addTable([
            'alignment' => Jc::LEFT, // posisi tabel di kiri halaman
            'cellMargin' => 100,
        ]);
        $cell = $table->addRow()->addCell(3000, ['valign' => 'top']); // lebar area tanda tangan
        $cell->addText("CV Jenderal Solusi Digital", [], ['alignment' => Jc::CENTER]);
        $cell->addTextBreak(2);
        $cell->addText("Rian Kusdiono, S.E.", ['underline' => 'single'], ['alignment' => Jc::CENTER]);
        $cell->addText("Direktur", [], ['alignment' => Jc::CENTER]);

        // =======================================================
        // 📄 SECTION 4 - LATAR BELAKANG (AI)
        // =======================================================
        $section4 = $phpWord->addSection();
        $this->addHeader($section4);
        $section4->addText("I. LATAR BELAKANG PENGEMBANGAN", ['bold' => true, 'size' => 12]);
        $section4->addText($aiResponse, ['size' => 12]);

        $section4->addTextBreak(1);
        $section4->addText("II. TENTANG PERUSAHAAN", ['bold' => true, 'size' => 12]);
        $section4->addText(
            "   Jenderal Solusi Digital merupakan perusahaan teknologi informasi yang bergerak dalam bidang rekayasa perangkat lunak dan pengembangan sistem informasi yang bertempat di Purwokerto, Kabupaten Banyumas, Provinsi Jawa Tengah.",
            ['size' => 12],                  
            ['alignment' => Jc::BOTH]
        );
        $section4->addText(
            "   Jenderal Solusi Digital mengkhususkan pada pengembangan aplikasi web, aplikasi mobile, website dan aplikasi lainnya berbasis web. ",
            ['size' => 12],                  
            ['alignment' => Jc::BOTH]  
        );
        $section4->addText(
            "   Selain itu kami memberikan layanan konsultasi (Program Pelatihan dan Edukasi) untuk meningkatkan sumber daya manusia dalam bidang sistem administrasi.",
            ['size' => 12],                  
            ['alignment' => Jc::BOTH]  
        );

        $section4->addTextBreak(1);
        $section4->addText("III. PERNYATAAN VISI", ['bold' => true, 'size' => 12]);
        $section4->addText(
            "   Visi kami adalah untuk menghasilkan layanan berkualitas tinggi yang terjangkau dan fleksibel untuk klien kami. Kami ingin membuat klien kami senang dengan membuat aplikasi yang sesuai dengan kebutuhan klien sehingga meningkatkan kualitas produk dan jasa klien.", 
            ['size' => 12],                  
            ['alignment' => Jc::BOTH]  
        );

        $section4->addTextBreak(1);
        $section4->addText("IV. PERNYATAAN MISI", ['bold' => true, 'size' => 12]);
        $section4->addText(
            "   Misi kami adalah membuat klien kami senang dengan membuat aplikasi yang akurat yang pasti akan membantu dalam pelayanan dan branding mereka. Kami akan menghasilkan layanan berkualitas tinggi yang terjangkau dan fleksibel untuk klien kami.",
            ['size' => 12],                  
            ['alignment' => Jc::BOTH]  
        );

        $section4->addTextBreak(1);
        $section4->addText("V. LEGALITAS PERUSAHAAN", ['bold' => true, 'size' => 12]);

        $textrun = $section4->addTextRun(['alignment' => Jc::LEFT]);
        $textrun->addText("     a) ");
        $textrun->addText("NAMA LEGAL PERUSAHAAN", ['underline' => 'single']);
        $section4->addText("         CV Jenderal Solusi Digital adalah nama legel perusahaan kami.");

        $textrun = $section4->addTextRun(['alignment' => Jc::LEFT]);
        $textrun->addText("     b) ");
        $textrun->addText("AKTA NOTARIS", ['underline' => 'single']);
        $section4->addText("         Perusahaan kami terdaftar pada akta notaris Muhammad Dwi Kuncoro, S.H., M.Kn.");

        $textrun = $section4->addTextRun(['alignment' => Jc::LEFT]);
        $textrun->addText("     c) ");
        $textrun->addText("NOMOR INDUK BERUSAHA (NIB)", ['underline' => 'single']);
        $section4->addText("         Nomor Induk Berusaha (NIB) kami adalah 2507720043689.");

        $textrun = $section4->addTextRun(['alignment' => Jc::LEFT]);
        $textrun->addText("     d) ");
        $textrun->addText("Nomor AHU", ['underline' => 'single']);
        $section4->addText("         Nomor AHU Perusahaan Kami adalah AHU-0046697-AH.01.14");

        $textrun = $section4->addTextRun(['alignment' => Jc::LEFT]);
        $textrun->addText("     e) ");
        $textrun->addText("NOMOR PENGUSAHA KENA PAJAK (PKP)", ['underline' => 'single']);
        $section4->addText("         Nomor PKP Perusahaan Kami adalah S-263/PKP/KPP.320103/2022");

        $textrun = $section4->addTextRun(['alignment' => Jc::LEFT]);
        $textrun->addText("     f) ");
        $textrun->addText("NOMOR POKOK WAJIB PAJAK (NPWP)", ['underline' => 'single']);
        $section4->addText("         NPWP Perusahaan Kami adalah 60.095.803.7-521.000");

        $section4->addTextBreak(1);
        $section4->addText("VI. MODUL ATAU FITUR SISTEM", ['bold' => true, 'size' => 12]);
        $section4->addText(
            "1. Berikut merupakan komponen rencana Pengembangan Modul " . $proyek->nama_proyek . " dengan memperhatikan modul atau fitur :",
            ['size' => 12],
            [
                'alignment' => Jc::BOTH,
                'indentation' => ['left' => 400], 
            ]
        );
        $table = $section4->addTable([
            'borderSize' => 6,
            'borderColor' => '000000',
        ]);
        $table->addRow();
        $styleHeader = [
            'bgColor' => 'FFC107', 
            'valign' => 'center', 
        ];
        $styleTextHeader = [
            'bold' => true,
        ];
        $styleParagraphHeader = [
            'alignment' => Jc::CENTER, 
        ];
        $table->addCell(800, $styleHeader)->addText('No', $styleTextHeader, $styleParagraphHeader);
        $table->addCell(5000, $styleHeader)->addText('Pengembangan Modul', $styleTextHeader, $styleParagraphHeader);
        $table->addCell(5000, $styleHeader)->addText('Keterangan', $styleTextHeader, $styleParagraphHeader);
        $table->addRow();
        $table->addCell(800)->addText('1', [], ['alignment' => Jc::CENTER]);
        $table->addCell(5000)->addText('Tambahkan Modul Disini');
        $table->addCell(5000)->addText('Tambahkan Keterangan Disini');

        $section4->addTextBreak(1);
        $section4->addText(
            "2. Timeline pengerjaan Pengembangan Sistem adalah 2 (dua) bulan dimulai setelah penandatanganan MoU atau Perjanjian Kerjasama.",
            ['size' => 12],
            [
                'alignment' => Jc::BOTH,
                'indentation' => ['left' => 400], 
            ]
        );
        $section4->addText(
            "3. Gratis sewa server selama 3 (tiga) bulan setelah masa pengembangan atau setelah implementasi.",
            ['size' => 12],
            [
                'alignment' => Jc::BOTH,
                'indentation' => ['left' => 400], 
            ]
        );

        $section4->addTextBreak(1);
        $section4->addText("VII. LAYANAN", ['bold' => true, 'size' => 12]);
        $section4->addText(
            "CV Jenderal Solusi Digital memberikan layanan terbaik untuk klien dari hulu ke hilir dalam pengembangan Sistem Informasi. " 
        );
        $section4->addText(
            "✓ CV Jenderal Solusi Digital melakukan analisis sistem yang akan dikembangkan secara intens dengan berdiskusi bersama klien.",
            ['size' => 12],
            [
                'alignment' => Jc::BOTH,
                'indentation' => ['left' => 500], 
            ]
        );
        $section4->addText(
            "✓ CV Jenderal Solusi digital bekerja dalam timeline yang telah disepakati bersama klien.",
            ['size' => 12],
            [
                'alignment' => Jc::BOTH,
                'indentation' => ['left' => 500], 
            ]
        );
        $section4->addText(
            "✓ Teknologi yang digunakan dalam pengembangan disesuaikan dengan kebutuhan fitur dan infrastruktur IT klien.",
            ['size' => 12],
            [
                'alignment' => Jc::BOTH,
                'indentation' => ['left' => 500], 
            ]
        );
        $section4->addText(
            "✓ CV Jenderal Solusi Digital memberikan maintenance secara gratis selama 3 bulan setelah sistem informasi diimplementasi.",
            ['size' => 12],
            [
                'alignment' => Jc::BOTH,
                'indentation' => ['left' => 500], 
            ]
        );
        $section4->addText(
            "✓ CV Jenderal Solusi Digital memberikan pelatihan dan pendampingan pengunaan Sistem Informasi pada Pihak Klien.",
            ['size' => 12],
            [
                'alignment' => Jc::BOTH,
                'indentation' => ['left' => 500], 
            ]
        );
        $section4->addText(
            "✓ CV Jenderal Solusi Digital memberikan user manual atau buku panduan penggunaan sistem baik dalam bentuk dokumen maupun video.",
            ['size' => 12],
            [
                'alignment' => Jc::BOTH,
                'indentation' => ['left' => 500], 
            ]
        );

        $section4->addTextBreak(1);
        $section4->addText("VIII. KONTAK", ['bold' => true, 'size' => 12]);
        $section4->addText("1. ALAMAT PERUSAHAAN", ['bold' => true, 'size' => 12]);
        $section4->addText(
            "Jalan Menteri Supeno, Perum Griya Permata Residence No B9 Kecamatan Sokaraja Kabupaten Banyumas Jawa Tengah 53181",
            ['size' => 12],
            [
                'alignment' => Jc::BOTH,
                'indentation' => ['left' => 200], 
            ]
        );
        $section4->addText("2. NOMOR TELEPON", ['bold' => true, 'size' => 12]);
        $section4->addText("    085172378297");
        $section4->addText("3. EMAIL:", ['bold' => true, 'size' => 12]);
        $section4->addText("    jenderalsolusidigital@gmail.com");
        $section4->addText("4. WEBSITE", ['bold' => true, 'size' => 12]);
        $section4->addText("    https://jenderalcorp.com/");

        // =======================================================
        // 📄 SECTION 5 - CLOSING
        // =======================================================
        $section5 = $phpWord->addSection();
        $this->addHeader($section5);
        $section5->addTextBreak(5);
        $section5->addImage(
            storage_path('app/public/proyek_files/logo.png'), // path file logo
            [
                'width' => 200,
                'height' => 200,
                'alignment' => Jc::CENTER,
            ]
        );

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
