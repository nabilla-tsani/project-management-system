<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class AIGenerateReportService
{
    public function generateAnalisis($proyek): string
    {
        // ===============================
        // DATA CUSTOMER
        // ===============================
        $customer = $proyek->customer
            ? $proyek->customer->nama
            : '-';

        // ===============================
        // ANGGOTA PROYEK & PERAN
        // ===============================
        $anggota = $proyek->proyekUsers->map(function ($pu) {
            return "- {$pu->user->name} ({$pu->role})";
        })->implode("\n") ?: '-';

        // ===============================
        // FITUR SISTEM
        // ===============================
        $fitur = $proyek->fitur->map(function ($f) {
            return "- {$f->nama_fitur}: {$f->keterangan}";
        })->implode("\n") ?: '-';

        // ===============================
        // PENUGASAN FITUR KE USER
        // ===============================
        $fiturUser = $proyek->fiturUser->map(function ($fu) {
            return "- {$fu->user->name} mengerjakan fitur {$fu->fitur->nama_fitur}";
        })->implode("\n") ?: '-';

        // ===============================
        // FILE PROYEK
        // ===============================
        $file = $proyek->file->map(function ($f) {
            return "- {$f->nama_file}";
        })->implode("\n") ?: '-';

        // ===============================
        // INVOICE
        // ===============================
        $invoice = $proyek->invoice->map(function ($i) {
            return "- {$i->nomor_invoice} (Rp " . number_format($i->total, 0, ',', '.') . ")";
        })->implode("\n") ?: '-';

        // ===============================
        // KWITANSI
        // ===============================
        $kwitansi = $proyek->kwitansi->map(function ($k) {
            return "- {$k->nomor_kwitansi} (Rp " . number_format($k->total, 0, ',', '.') . ")";
        })->implode("\n") ?: '-';

        // ===============================
        // PROMPT KE AI
        // ===============================
        $prompt = <<<PROMPT
Buatkan analisis laporan proyek secara profesional dan formal berdasarkan data berikut:

Nama Proyek: {$proyek->nama_proyek}
Customer: {$customer}
Status Proyek: {$proyek->status}
Periode: {$proyek->tanggal_mulai} s/d {$proyek->tanggal_selesai}
Anggaran: Rp {$proyek->anggaran}

Anggota Proyek:
{$anggota}

Daftar Fitur:
{$fitur}

Penugasan Fitur:
{$fiturUser}

Dokumen Proyek:
{$file}

Invoice:
{$invoice}

Kwitansi:
{$kwitansi}

Hasilkan:
1. Ringkasan proyek
2. Analisis pelaksanaan
3. Evaluasi fitur & tim
4. Evaluasi keuangan
5. Kesimpulan dan rekomendasi

Aturan Tambahan:
- Gunakan bahasa Indonesia yang baik dan benar.
- Gunakan format paragraf yang rapi dan mudah dibaca.
- Panjang analisis sekitar 300-500 kata.
- Jangan gunakan huruf tebal, miring, atau styling lainnya.
PROMPT;

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('services.openrouter.key'),
            'Content-Type' => 'application/json',
        ])->post('https://openrouter.ai/api/v1/chat/completions', [
            'model' => 'google/gemini-2.0-flash-001',
            'messages' => [
                ['role' => 'system', 'content' => 'Kamu adalah analis proyek profesional.'],
                ['role' => 'user', 'content' => $prompt],
            ],
            'temperature' => 0.4,
        ]);

        return $response['choices'][0]['message']['content'] ?? 'Analisis tidak tersedia.';
    }
}
