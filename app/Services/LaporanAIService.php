<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class LaporanAIService
{
    private function rupiah($nilai): string
    {
        return 'Rp ' . number_format($nilai, 0, ',', '.');
    }

    public function generate($proyek, string $analisisAI): string
    {
        $html = $this->buildHtml($proyek, $analisisAI);

        $pdf = Pdf::loadHTML($html)->setPaper('A4', 'portrait');

        $fileName = 'Laporan Proyek - ' . preg_replace('/[^A-Za-z0-9 ]/', '', $proyek->nama_proyek) . '.pdf';

        $filePath = storage_path('app/public/' . $fileName);

        $pdf->save($filePath);

        return $filePath;
    }

    private function buildHtml($proyek, string $analisisAI): string
    {
        $periode = Carbon::parse($proyek->tanggal_mulai)->translatedFormat('d F Y')
         . ' - ' .
         Carbon::parse($proyek->tanggal_selesai)->translatedFormat('d F Y');

        /* ===============================
         * ANGGOTA BERDASARKAN PERAN
         * =============================== */
        $groupedAnggota = $proyek->proyekUsers->groupBy('sebagai');

        $renderAnggota = function ($role) use ($groupedAnggota) {
            if (!isset($groupedAnggota[$role])) return '<li>-</li>';

            return $groupedAnggota[$role]->map(fn ($pu) =>
                "<li>{$pu->user->name}</li>"
            )->implode('');
        };

        /* ===============================
         * FITUR + PENANGGUNG JAWAB
         * =============================== */
        $fiturRows = $proyek->fitur->map(function ($f, $index) {

            $users = $f->users->map(fn ($u) => $u->name)->implode(', ') ?: '-';

            return "
                <tr>
                    <td align='center'>" . ($index + 1) . "</td>
                    <td>{$f->nama_fitur}</td>
                    <td>{$f->keterangan}</td>
                    <td>{$users}</td>
                </tr>
            ";
        })->implode('');


        /* ===============================
         * INVOICE TABLE
         * =============================== */
        $totalInvoice = $this->rupiah(
            $proyek->invoice->sum('jumlah')
        );
        $invoiceRows = $proyek->invoice->map(fn ($i) =>
            "<tr>
                <td>{$i->nomor_invoice}</td>
                <td align='right'>Rp " . number_format($i->jumlah, 0, ',', '.') . "</td>
            </tr>"
        )->implode('');

        /* ===============================
         * KWITANSI TABLE
         * =============================== */
        $totalKwitansi = $this->rupiah(
            $proyek->kwitansi->sum('jumlah')
        );
        $kwitansiRows = $proyek->kwitansi->map(fn ($k) =>
            "<tr>
                <td>{$k->nomor_kwitansi}</td>
                <td align='right'>Rp " . number_format($k->jumlah, 0, ',', '.') . "</td>
            </tr>"
        )->implode('');

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
body {
    font-family: "Times New Roman", Times, serif;
    font-size: 20px; /* ⬅️ diperbesar */
    line-height: 1.7;
    color: #2c2c2c;
}

/* ===============================
   JUDUL UTAMA
=============================== */
h1 {
    text-align: center;
    text-transform: uppercase;
    font-size: 22px; /* ⬅️ diperbesar */
    margin-bottom: 30px;
    color: #1f3a5f;
}

/* ===============================
   SUB JUDUL
=============================== */
h3 {
    margin-top: 28px;
    margin-bottom: 10px;
    padding-bottom: 6px;
    font-size: 16px; /* ⬅️ diperbesar */
    color: #1f3a5f;
    border-bottom: 2px solid #1f3a5f;
}

/* ===============================
   TABEL UMUM
=============================== */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 8px;
    font-size: 13px; /* ⬅️ tabel ikut besar */
}

th {
    background-color: #e9f0f7;
    color: #1f3a5f;
    font-weight: bold;
    text-align: center;
}

td, th {
    border: 1px solid #bfc9d4;
    padding: 8px 10px; /* ⬅️ padding lebih lega */
    vertical-align: top;
}

/* ===============================
   INFO TABLE (TANPA BORDER)
=============================== */
.info-table,
.info-table td {
    border: none !important;
    padding: 6px 8px;
    font-size: 13px;
}

.info-table td:first-child {
    font-weight: bold;
    width: 30%;
}

.info-table td:nth-child(2) {
    width: 5%;
}

/* ===============================
   LIST
=============================== */
ul {
    margin: 6px 0 14px 22px;
    padding: 0;
    font-size: 13px;
}

/* ===============================
   BOX ANALISIS AI
=============================== */
.ai-box {
    border-left: 5px solid #1f3a5f;
    padding: 16px;
    background: #f4f7fb;
    white-space: pre-line;
    font-size: 13.5px; /* ⬅️ sedikit lebih besar */
}

/* ===============================
   TEKS TEBAL
=============================== */
strong {
    font-size: 13.5px;
}
</style>

</head>

<body>

<h1>Laporan Proyek</h1>

<h3>Informasi Proyek</h3>
<table class="info-table">
    <tr>
        <td width="30%">Nama Proyek</td>
        <td width="5%" align="center">:</td>
        <td>{$proyek->nama_proyek}</td>
    </tr>
    <tr>
        <td>Customer</td>
        <td align="center">:</td>
        <td>{$proyek->customer->nama}</td>
    </tr>
    <tr>
        <td>Lokasi</td>
        <td align="center">:</td>
        <td>{$proyek->lokasi}</td>
    </tr>
    <tr>
        <td>Status</td>
        <td align="center">:</td>
        <td>{$proyek->status}</td>
    </tr>
    <tr>
        <td>Periode</td>
        <td align="center">:</td>
        <td>{$periode}</td>
    </tr>

    <tr>
        <td>Anggaran</td>
        <td align="center">:</td>
        <td>Rp {$proyek->anggaran}</td>
    </tr>
</table>


<h3>Anggota Proyek</h3>
<strong>Manajer Proyek</strong>
<ul>{$renderAnggota('manajer proyek')}</ul>

<strong>Programmer</strong>
<ul>{$renderAnggota('programmer')}</ul>

<strong>Tester</strong>
<ul>{$renderAnggota('tester')}</ul>

<h3>Daftar Fitur</h3>
<table>
    <thead>
        <tr>
            <th width="5%">No</th>
            <th width="25%">Nama Fitur</th>
            <th width="40%">Keterangan</th>
            <th width="30%">Penanggung Jawab</th>
        </tr>
    </thead>
    <tbody>
        {$fiturRows}
    </tbody>
</table>


<h3>Invoice</h3>
<table>
<tr><th>Nomor Invoice</th><th>Jumlah</th></tr>
{$invoiceRows}
<tr>
    <tr>
        <th>Total</th>
        <th align="right">{$totalInvoice}</th>
    </tr>

</tr>
</table>

<h3>Kwitansi</h3>
<table>
<tr><th>Nomor Kwitansi</th><th>Jumlah</th></tr>
{$kwitansiRows}
<tr>
   <tr>
        <th>Total</th>
        <th align="right">{$totalKwitansi}</th>
    </tr>

</tr>
</table>

<h3>Analisis AI</h3>
<div class="ai-box">
{$analisisAI}
</div>

</body>
</html>
HTML;
    }
}
