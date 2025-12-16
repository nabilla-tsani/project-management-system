<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

// Import semua model
use App\Models\Customer;
use App\Models\Proyek;
use App\Models\ProyekCatatanPekerjaan;
use App\Models\ProyekFile;
use App\Models\ProyekFitur;
use App\Models\ProyekFiturUser;
use App\Models\ProyekInvoice;
use App\Models\ProyekKwitansi;
use App\Models\ProyekUser;
use App\Models\User;

class GeminiService
{
    protected $base = 'https://generativelanguage.googleapis.com/v1beta/models';
    protected $key;

    public function __construct()
    {
        $this->key = config('services.gemini.key');
    }


    /**
     * CHAT BASIC
     */
    public function chat(string $prompt, string $model = 'gemini-2.5-flash')
    {
        $url = "{$this->base}/{$model}:generateContent?key={$this->key}";

         $payload = [
            "contents" => [
                [
                    "role" => "user",
                    "parts" => [[
                        "text" => "Jawab secara ringkas, jelas, dan profesional:\n\n$prompt"
                    ]]
                ]
            ]
        ];

        $response = Http::post($url, $payload);

        if ($response->failed()) {
            return $this->handleGeminiError($response);
        }


        return $response->json()['candidates'][0]['content']['parts'][0]['text'] ?? "";
    }



    /**
     * CHAT → INTENT → FILTER DB → FINAL ANSWER
     */
    public function chatWithHistory(array $messages, string $model = 'gemini-2.5-flash')
{
    $raw = $this->classifyAndAskIntent($messages, $model);

    if (str_starts_with($raw, 'Limit:')) {
        return $raw;
    }

    $json = $this->sanitizeJson($raw);
    $intent = json_decode($json, true);

    // ❌ FAILSAFE
    if (!$intent || !isset($intent['type'])) {
        return "Maaf, saya tidak dapat memahami pertanyaan Anda.";
    }

    // 🔹 GENERAL QUESTION
    if ($intent['type'] === 'general') {
        return $this->chat(end($messages)['message'], $model);
    }

    // 🔹 RAG QUESTION
    if ($intent['type'] === 'rag') {
        $dbResults = $this->runAiSearch(json_encode($intent));
        return $this->askFinalAnswer($messages, $dbResults, $model);
    }

    return "Maaf, saya tidak dapat memproses pertanyaan tersebut.";
}




    /**
     * STEP 1 — ASK INTENT JSON
     */
    /**
 * ADVANCED — CLASSIFY + ASK INTENT (1 CALL)
 */
private function classifyAndAskIntent(array $messages, string $model)
{
    $url = "{$this->base}/{$model}:generateContent?key={$this->key}";

    // Format riwayat chat
    $contents = [];
    foreach ($messages as $msg) {
        $contents[] = [
            "role"  => $msg['role'] === 'ai' ? 'model' : 'user',
            "parts" => [["text" => $msg['message']]]
        ];
    }

    // Schema database
    $schema = "Tabel tersedia:
1. customer(nama, alamat, nomor_telepon, email, catatan, status)
2. proyek(nama_proyek, customer_id, deskripsi, lokasi, tanggal_mulai, tanggal_selesai, anggaran, status)
3. proyek_catatan_pekerjaan(proyek_id, proyek_fitur_id, user_id, jenis, catatan, tanggal_mulai, tanggal_selesai, feedback)
4. proyek_file(proyek_id, keterangan, nama_file, path, user_id)
5. proyek_fitur(proyek_id, nama_fitur, keterangan, target, status_fitur)
6. proyek_fitur_user(proyek_fitur_id, user_id, keterangan)
7. proyek_invoice(nomor_invoice, proyek_id, judul_invoice, jumlah, tanggal_invoice, keterangan, status, user_id)
8. proyek_kwitansi(nomor_kwitansi, nomor_invoice, proyek_id, judul_kwitansi, jumlah, tanggal_kwitansi, keterangan, user_id)
9. proyek_user(proyek_id, user_id, sebagai, keterangan)
10. users(name, email, password)
";

    // 🔥 PROMPT UTAMA
    $contents[] = [
        "role" => "user",
        "parts" => [[
            "text" => "
Tugasmu:
1. Tentukan apakah pertanyaan user membutuhkan DATA INTERNAL (RAG) atau PENGETAHUAN UMUM (GENERAL).
2. Jika GENERAL → balas JSON:
   { \"type\": \"general\" }

3. Jika RAG:
   - Gunakan SCHEMA di bawah
   - Tentukan tabel & filter
   - Balas JSON dengan format:
{
  \"type\": \"rag\",
  \"required_tables\": [...],
  \"filters\": {...}
}

SCHEMA DATABASE:
$schema

Mapping status (case-insensitive):
- belum mulai, not started, upcoming → belum_dimulai
- progress, in progress, berjalan, ongoing → sedang_berjalan
- done, finished, completed → selesai
- pending, hold, paused, delay, ditunda, tertunda → ditunda

Aturan penting:
- Mapping status berlaku untuk semua kolom status
- Istilah boleh tidak persis, pilih makna terdekat
- Jika user menyebut nama (meskipun satu kata), anggap sebagai pencarian
- JANGAN tambahkan teks atau penjelasan di luar JSON
"
        ]]
    ];

    $response = Http::post($url, ["contents" => $contents]);

    if ($response->failed()) {
        return $this->handleGeminiError($response);
    }

    return $response->json()['candidates'][0]['content']['parts'][0]['text'] ?? "{}";
}


    /**
     * SANITIZE JSON
     */
    private function sanitizeJson(string $raw)
    {
        $clean = preg_replace('/```json|```/i', '', $raw);
        preg_match('/\{(.|\n)*\}/', $clean, $match);
        if ($match) $clean = $match[0];
        $clean = preg_replace('/,\s*}/', '}', $clean);
        $clean = preg_replace('/,\s*]/', ']', $clean);
        return trim($clean);
    }



    /**
     * STEP 2 — JSON → FILTER DB
     * Sudah ditambah pembatasan berdasarkan proyek milik user login
     */
    public function runAiSearch(string $json)
    {
        $ai = json_decode($json, true);

        if (!$ai || !isset($ai['required_tables'])) {
            return [];
        }

        $results = [];

        foreach ($ai['required_tables'] as $table) {
            $table = strtolower($table);

            switch ($table) {
                case 'customer':
                    $results['customer'] = $this->customerForUser($ai['filters']['customer'] ?? []);
                    break;

                case 'proyek':
                    $results['proyek'] = $this->applyProjectFilter(Proyek::query(), $ai['filters']['proyek'] ?? []);
                    break;

                case 'proyek_user':
                    $results['proyek_user'] = ProyekUser::where('user_id', Auth::id())->get();
                    break;

                case 'proyek_fitur':
                    $results['proyek_fitur'] = $this->applyProjectChildFilter(ProyekFitur::query(), $ai['filters']['proyek_fitur'] ?? []);
                    break;

                case 'proyek_fitur_user':
                    $results['proyek_fitur_user'] = $this->applyProjectChildFilter(ProyekFiturUser::query(), $ai['filters']['proyek_fitur_user'] ?? [], 'proyek_fitur_id');
                    break;

                case 'proyek_file':
                    $results['proyek_file'] = $this->applyProjectChildFilter(ProyekFile::query(), $ai['filters']['proyek_file'] ?? []);
                    break;

                case 'proyek_invoice':
                    $results['proyek_invoice'] = $this->applyProjectChildFilter(ProyekInvoice::query(), $ai['filters']['proyek_invoice'] ?? []);
                    break;

                case 'proyek_kwitansi':
                    $results['proyek_kwitansi'] = $this->applyProjectChildFilter(ProyekKwitansi::query(), $ai['filters']['proyek_kwitansi'] ?? []);
                    break;

                case 'proyek_catatan_pekerjaan':
                    $results['proyek_catatan_pekerjaan'] = $this->applyProjectChildFilter(ProyekCatatanPekerjaan::query(), $ai['filters']['proyek_catatan_pekerjaan'] ?? []);
                    break;

                case 'users':
                case 'user':
                    $results['users'] = $this->applyFilters(User::query(), $ai['filters']['user'] ?? []);
                    break;
            }
        }

        return $results;
    }



    /**
     * FILTER UTAMA: HANYA PROYEK MILIK USER
     */
    private function applyProjectFilter($query, array $filters)
    {
        $query->whereIn('id', ProyekUser::where('user_id', Auth::id())->pluck('proyek_id'));

        return $this->applyFilters($query, $filters);
    }

    /**
     * FILTER CHILD TABLE: Semua tabel anak yang punya proyek_id
     */
    private function applyProjectChildFilter($query, array $filters, string $field = 'proyek_id')
    {
        $query->whereIn($field, ProyekUser::where('user_id', Auth::id())->pluck('proyek_id'));
        return $this->applyFilters($query, $filters);
    }

    /**
     * FILTER CUSTOMER: Hanya customer yang punya proyek milik user
     */
    private function customerForUser(array $filters)
    {
        // Semua customer bisa dilihat oleh semua user
        $query = Customer::query();

        return $this->applyFilters($query, $filters);
    }



    /**
     * GENERIC FILTERING
     */

    private function applyFilters($query, array $filters)
    {
        foreach ($filters as $column => $value) {
            if (!$value) continue;

            // ===============================
            // 🔥 FILTER RELASI (customer.nama)
            // ===============================
            if (Str::contains($column, '.')) {
                [$relation, $field] = explode('.', $column, 2);

                $query->whereHas($relation, function ($q) use ($field, $value) {
                    $q->whereRaw(
                        "LOWER($field) LIKE ?",
                        ['%' . strtolower($value) . '%']
                    );
                });

                continue;
            }

            // ===============================
            // FILTER KOLOM BIASA
            // ===============================
            $query->whereRaw(
                "LOWER($column) LIKE ?",
                ['%' . strtolower($value) . '%']
            );
        }

        return $query->get();
    }




    /**
     * STEP 3 — FINAL ANSWER
     */
        private function askFinalAnswer(array $messages, $dbResults, string $model)
    {
        $url = "{$this->base}/{$model}:generateContent?key={$this->key}";

        $lastUserMessage = end($messages)['message'];

        $prompt = "
    User bertanya: \"$lastUserMessage\"

    Berikut hasil pencarian database (JSON):
    " . json_encode($dbResults, JSON_PRETTY_PRINT) . "

    Instruksi sangat penting:
    1. Jawab HANYA berdasarkan data di atas.
    2. Jangan menambahkan data lain yang tidak ada dalam hasil pencarian.
    3. Jangan membuat asumsi atau menebak-nebak.
    4. Jangan tampilkan JSON.
    5. Jika data kosong atau tidak ditemukan, jawab singkat:
    \"Maaf, saya tidak menemukan data terkait pertanyaan tersebut.\"
    6. Buat jawaban singkat, jelas, dan tidak bertele-tele.
    7. Gunakan baris baru asli untuk setiap informasi.
    8. Jangan gunakan karakter '\\n'. Tulis baris baru langsung menggunakan ENTER.
    ";

        $contents = [
            [
                "role" => "user",
                "parts" => [["text" => $prompt]]
            ]
        ];

        $response = Http::post($url, ["contents" => $contents]);

        if ($response->failed()) {
            return $this->handleGeminiError($response);
        }

        // Ambil output
        $answer = $response->json()['candidates'][0]['content']['parts'][0]['text'] ?? "";

        // 🔥 FIX: Ubah teks '\n' menjadi baris baru asli
        $answer = str_replace("\\n", "\n", $answer);

        // 🔥 FIX: Hapus tanda kutip di awal & akhir jika Gemini mengirim string quoted
        $answer = trim($answer, "\"");

        return $answer;
    }

   private function handleGeminiError($response)
{
    // HTTP 429
    if ($response->status() === 429) {
        return "Limit: Kuota Harian Habis";
    }

    if ($response->status() === 503) {
        return "Limit: Model AI sedang sibuk. Silakan coba beberapa saat lagi.";
    }


    $error = $response->json();

    // Gemini quota exhausted
    if (
        isset($error['error']['status']) &&
        $error['error']['status'] === 'RESOURCE_EXHAUSTED'
    ) {
        return "Limit: Kuota Harian Habis";
    }

    return "Terjadi error: " . $response->body();
}




}
