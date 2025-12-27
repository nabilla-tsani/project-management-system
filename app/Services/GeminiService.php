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
    // protected $base = 'https://generativelanguage.googleapis.com/v1beta/models';
    protected $base = 'https://openrouter.ai/api/v1/chat/completions';
    protected $key;

    public function __construct()
{
    $this->key = config('services.openrouter.key');
}



    /**
     * CHAT BASIC
     */
    public function chat(string $prompt, string $model = 'google/gemini-2.0-flash-001')
    {
        $response = Http::withHeaders([
            'Authorization' => "Bearer {$this->key}",
            'Content-Type'  => 'application/json',
            'Accept'        => 'application/json',
            'HTTP-Referer'  => config('app.url'),
            'X-Title'       => 'Manpro AI',
        ])->post($this->base, [
            'model' => $model,
            'messages' => [
                [
                    'role' => 'user',
                    'content' => "Jawab secara ringkas, jelas, dan profesional:\n\n$prompt"
                ]
            ],
            'temperature' => 0.3,
        ]);

        if ($response->failed()) {
            return "Terjadi error: " . $response->body();
        }

        return $response->json('choices.0.message.content');
    }


    /**
     * CHAT → INTENT → FILTER DB → FINAL ANSWER
     */
    public function chatWithHistory(array $messages, string $model = 'google/gemini-2.0-flash-001')
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

        $systemPrompt = <<<PROMPT
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
            PROMPT;

        $chatMessages = [
            [
                'role' => 'system',
                'content' => $systemPrompt
            ]
        ];

        foreach ($messages as $msg) {
            $chatMessages[] = [
                'role' => $msg['role'] === 'ai' ? 'assistant' : 'user',
                'content' => $msg['message']
            ];
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->key,
            'HTTP-Referer'  => config('app.url'),
            'X-Title'       => 'Manpro AI',
        ])->post('https://openrouter.ai/api/v1/chat/completions', [
            'model' => $model,
            'messages' => $chatMessages,
            'temperature' => 0
        ]);

        if ($response->failed()) {
            return $this->handleGeminiError($response);
        }

        return $response->json('choices.0.message.content') ?? '{}';
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
    $results['proyek_user'] = ProyekUser::with(['user:id,name', 'proyek:id,nama_proyek'])
        ->whereIn('proyek_id', $this->myProjectIds())
        ->get();
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
    $results['users'] = $this->applyFilters(
        User::whereIn('id', function ($q) {
            $q->select('user_id')
              ->from('proyek_user')
              ->whereIn('proyek_id', $this->myProjectIds());
        }),
        $ai['filters']['user'] ?? []
    );
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

    private function myProjectIds()
    {
        return ProyekUser::where('user_id', Auth::id())
            ->pluck('proyek_id');
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
            if (in_array($column, ['id', 'user_id', 'proyek_id'])) {
                $query->where($column, $value);
            } else {
                $query->whereRaw(
                    "LOWER($column) LIKE ?",
                    ['%' . strtolower($value) . '%']
                );
            }

        }

        return $query->get();
    }


    /**
     * STEP 3 — FINAL ANSWER
     */
    private function askFinalAnswer(array $messages, $dbResults, string $model)
    {
        $lastUserMessage = end($messages)['message'];

        $user = Auth::user();

$prompt = "
User login saat ini:
- user_id: {$user->id}
- nama: {$user->name}

Jika pertanyaan menggunakan kata 'saya', maka yang dimaksud adalah user dengan user_id {$user->id}.

User bertanya:
\"$lastUserMessage\"

Data berikut adalah DATA RESMI dari database sistem manajemen proyek (JSON):
" . json_encode($dbResults, JSON_PRETTY_PRINT) . "

ATURAN WAJIB:
1. Jawab HANYA dari data yang diberikan.
2. Jika pertanyaan menyebut 'saya', itu berarti user dengan user_id yang terlibat pada proyek.
3. Jika pertanyaan menanyakan PERAN, gunakan kolom 'sebagai' dari tabel proyek_user.
4. Jika pertanyaan menanyakan JUMLAH (berapa, ada berapa), hitung dari data yang ada.
5. Jika pertanyaan menanyakan 'siapa', tampilkan nama user.
6. Jika data terkait user lain di luar proyek user login, jawab:
   'Maaf, saya tidak memiliki akses ke data tersebut.'
7. Jangan tampilkan JSON, ID, atau format khusus.
8. Gunakan kalimat singkat dan jelas.
9. Gunakan baris baru nyata jika lebih dari satu informasi.
";


        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->key,
            'HTTP-Referer'  => config('app.url'),
            'X-Title'       => 'Manpro AI',
        ])->post('https://openrouter.ai/api/v1/chat/completions', [
            'model' => $model,
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $prompt,
                ]
            ],
            'temperature' => 0.2,
        ]);

        if ($response->failed()) {
            return $this->handleGeminiError($response);
        }

        return trim($response->json('choices.0.message.content') ?? '');
    }


   private function handleGeminiError($response)
    {
        if ($response->status() === 401) {
            return "Limit: API Key tidak valid atau tidak terbaca";
        }

        if ($response->status() === 402) {
            return "Limit: Saldo OpenRouter habis";
        }

        if ($response->status() === 429) {
            return "Limit: Terlalu banyak permintaan";
        }

        return "Terjadi error: " . $response->body();
    }

}
