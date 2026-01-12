<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

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
    protected $base = 'https://openrouter.ai/api/v1/chat/completions';
    protected $key;

    public function __construct()
    {
        $this->key = config('services.openrouter.key');
    }

    /**
     * GENERIC FILTERING
     */
    private function applyFilters($query, array $filters)
    {
        foreach ($filters as $column => $value) {
            if (!$value) continue;

            // FILTER RELASI (customer.nama)
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

            if (in_array($column, ['id', 'user_id', 'proyek_id'])) {
                // PROTEKSI WAJIB
                if (!is_numeric($value)) {
                    continue;
                }
                $query->where($column, (int) $value);
                continue;
            }

            // FILTER KOLOM BIASA (CASE INSENSITIVE)
            $query->whereRaw(
                "LOWER($column) LIKE ?",
                ['%' . strtolower($value) . '%']
            );
        }

        return $query->get();
    }

    /**
     * STEP 3 — FINAL ANSWER (Updated)
     */
    private function askFinalAnswer(array $messages, $dbResults, $calculations, string $model)
    {
        $lastUserMessage = end($messages)['message'];
        $user = Auth::user();

        // 🔥 Format hasil perhitungan dengan lebih jelas
        $calculationText = '';
        if (!empty($calculations)) {
            $calculationText = "\n\n=== HASIL PERHITUNGAN BACKEND (GUNAKAN INI, JANGAN HITUNG ULANG) ===\n" 
                . json_encode($calculations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }

        $prompt = "
        User login saat ini:
        - user_id: {$user->id}
        - nama: {$user->name}

        User bertanya:
        \"$lastUserMessage\"

        Data dari database:
        " . json_encode($dbResults, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "
        $calculationText

        ATURAN WAJIB:
        1. Jawab HANYA dari data yang diberikan.
        2. 🔥 PRIORITAS UTAMA: Jika ada HASIL PERHITUNGAN, WAJIB gunakan angka dari sana.
        3. JANGAN PERNAH menghitung manual dari array data.
        4. Gunakan format yang sudah tersedia (xxx_formatted) untuk angka uang.
        5. Jika pertanyaan menyebut 'saya', maksudnya user dengan user_id {$user->id}.
        6. Jika pertanyaan menanyakan PERAN, gunakan kolom 'sebagai'.
        7. Jika pertanyaan menanyakan 'siapa', tampilkan nama user.
        8. JANGAN gunakan bullet points, numbering, atau format markdown.
        9. Gunakan teks biasa (plain text) saja.
        10. Jika lebih dari satu informasi, pisahkan dengan baris baru tanpa simbol.
        11. Format tanggal: contoh '28 Desember 2026'.
        12. Jangan tampilkan ID kecuali diminta.
        13. Jika data proyek kosong pada pertanyaan spesifik proyek: 'Anda tidak terdaftar di proyek tersebut.'
        14. Jika perhitungan menghasilkan 0 dan memang tidak ada data: 'Belum ada data untuk perhitungan ini.'
        
        CONTOH JAWABAN YANG BENAR:
        - User: 'berapa anggaran yang belum dibayar?'
        - AI: 'Total anggaran yang belum dibayar adalah Rp 50.000.000'
        
        CONTOH JAWABAN YANG SALAH:
        - 'Berdasarkan data yang saya hitung...' ❌
        - Menampilkan hasil perhitungan sendiri ❌
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
            'temperature' => 0.1,
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

    public function ask(string $prompt): string
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->key,
            'HTTP-Referer'  => config('app.url'),
            'X-Title'       => config('app.name'),
            'Content-Type'  => 'application/json',
        ])->post($this->base, [
            'model' => 'google/gemini-2.0-flash-001',
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $prompt,
                ],
            ],
            'temperature' => 0.6,
        ]);

        if (! $response->successful()) {
            \Log::error('OpenRouter error', $response->json());
            return '';
        }

        $content = $response->json('choices.0.message.content');

        // HANDLE ARRAY CONTENT (OpenRouter Gemini)
        if (is_array($content)) {
            return trim(collect($content)
                ->where('type', 'text')
                ->pluck('text')
                ->implode("\n"));
        }

        return trim($content ?? '');
    }

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
        // 🔥 AMBIL PESAN TERAKHIR USER SAJA (Fresh Start)
        $lastUserMessage = end($messages);
        
        // 🔥 Buat array pesan baru dengan hanya pesan terakhir
        $freshMessages = [$lastUserMessage];
        
        $raw = $this->classifyAndAskIntent($freshMessages, $model);

        if (str_starts_with($raw, 'Limit:')) {
            return $raw;
        }

        $json = $this->sanitizeJson($raw);
        $intent = json_decode($json, true);

        // FAILSAFE
        if (!$intent || !isset($intent['type'])) {
            return "Maaf, saya tidak dapat memahami pertanyaan Anda.";
        }

        // 🔹 GENERAL QUESTION
        if ($intent['type'] === 'general') {
            return $this->chat($lastUserMessage['message'], $model);
        }

        // 🔹 RAG QUESTION
        if ($intent['type'] === 'rag') {
            $dbResults = $this->runAiSearch(json_encode($intent));
            
            // 🔥 CEK APAKAH USER TERDAFTAR DI PROYEK YANG DITANYAKAN
            $accessCheck = $this->checkProjectAccess($intent, $dbResults);
            if ($accessCheck !== true) {
                return $accessCheck;
            }
            
            // 🔥 HITUNG STATISTIK
            $calculations = $this->calculateStatistics($intent, $dbResults);
            
            return $this->askFinalAnswer($freshMessages, $dbResults, $calculations, $model);
        }

        return "Maaf, saya tidak dapat memproses pertanyaan tersebut.";
    }

    /**
     * 🔥 FUNGSI CEK AKSES PROYEK
     */
    private function checkProjectAccess(array $intent, array $dbResults)
    {
        // Jika ada filter proyek yang spesifik
        if (!empty($intent['filters']['proyek'])) {
            // Cek apakah ada data proyek yang ditemukan
            if (empty($dbResults['proyek']) || count($dbResults['proyek']) === 0) {
                return "Anda tidak terdaftar di proyek tersebut.";
            }
        }
        
        return true;
    }

    /**
     * STEP 1 — ASK INTENT JSON
     */
    private function classifyAndAskIntent(array $messages, string $model)
    {
        // 🔥 AMBIL DAFTAR NAMA PROYEK USER untuk fuzzy matching
        $userProjects = Proyek::whereIn('id', ProyekUser::where('user_id', Auth::id())->pluck('proyek_id'))
            ->pluck('nama_proyek')
            ->toArray();
        
        $projectList = implode(', ', $userProjects);

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
            { "type": "general" }

            3. Jika RAG:
            - Gunakan SCHEMA di bawah
            - Tentukan tabel & filter
            - Tentukan apakah perlu perhitungan (calculation_type)
            - Jika user menyebut nama proyek atau fitur, WAJIB masukkan ke filter
            - Balas JSON dengan format:
            {
            "type": "rag",
            "required_tables": [...],
            "filters": {
                "proyek": {"nama_proyek": "..."},
                "proyek_fitur": {"nama_fitur": "..."}
            },
            "calculation_type": "count_proyek|count_anggota|sum_anggaran|sum_dibayar|sum_belum_dibayar|count_fitur|count_anggota_fitur|count_file|count_invoice|count_kwitansi|none",
            "calculation_scope": "specific|all"
            }

            SCHEMA DATABASE:
            $schema

            🔥 DAFTAR PROYEK USER (untuk fuzzy matching):
            $projectList

            🔥 ATURAN FUZZY MATCHING NAMA PROYEK:
            - Jika user menyebut nama proyek, cocokkan dengan daftar di atas
            - Abaikan perbedaan huruf besar/kecil
            - Toleransi typo (misal: "web ecomerce" → "Web Ecommerce PT. Pelita")
            - Jika user menyebut sebagian nama (misal: "proyek web ecommerce"), cocokkan dengan nama lengkap
            - Ambil nama proyek yang paling mendekati dari daftar

            Mapping status (case-insensitive):
            - belum mulai, not started, upcoming → belum_dimulai
            - progress, in progress, berjalan, ongoing → sedang_berjalan
            - done, finished, completed → selesai
            - pending, hold, paused, delay, ditunda, tertunda → ditunda

            Mapping calculation_type:
            - "berapa jumlah proyek" → count_proyek
            - "berapa anggota proyek [nama]" → count_anggota
            - "berapa total anggaran [proyek]" → sum_anggaran
            - "berapa yang sudah dibayar [proyek]" → sum_dibayar
            - "berapa yang belum dibayar [proyek]" → sum_belum_dibayar
            - "berapa jumlah fitur [proyek]" → count_fitur
            - "berapa anggota fitur [nama fitur]" → count_anggota_fitur
            - "berapa file" → count_file
            - "berapa invoice" → count_invoice
            - "berapa kwitansi" → count_kwitansi

            Mapping calculation_scope:
            - specific: jika pertanyaan menyebut nama proyek/fitur spesifik
            - all: jika pertanyaan umum untuk semua proyek user

            Aturan penting:
            - WAJIB ekstrak nama proyek/fitur dari pertanyaan dan masukkan ke filters
            - Gunakan fuzzy matching dengan daftar proyek di atas
            - Contoh: "berapa anggaran proyek web ecommerce yang belum dibayar" 
              → cari di daftar yang mirip "web ecommerce" → "Web Ecommerce PT. Pelita"
              → filters.proyek.nama_proyek = "Web Ecommerce PT. Pelita"
            - Contoh: "berapa anggota fitur home" → filters.proyek_fitur.nama_fitur = "home"
            - Mapping status berlaku untuk semua kolom status
            - Jika user bertanya perhitungan spesifik proyek, set calculation_scope = "specific"
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
     * 🔥 FUNGSI CALCULATE STATISTICS (DIPERBAIKI & DIANALISIS)
     */
    private function calculateStatistics(array $intent, array $dbResults)
    {
        $calculations = [];
        $calcType = $intent['calculation_type'] ?? 'none';
        $calcScope = $intent['calculation_scope'] ?? 'all';
        
        if ($calcType === 'none') {
            return $calculations;
        }

        $userId = Auth::id();
        
        // 🔥 TENTUKAN PROJECT IDS BERDASARKAN SCOPE
        if ($calcScope === 'specific' && !empty($dbResults['proyek'])) {
            // Gunakan proyek yang sudah difilter
            $filteredProjectIds = collect($dbResults['proyek'])->pluck('id')->toArray();
            $calculations['proyek_yang_dihitung'] = collect($dbResults['proyek'])->pluck('nama_proyek')->toArray();
        } else {
            // Gunakan semua proyek user
            $filteredProjectIds = $this->myProjectIds()->toArray();
            $calculations['proyek_yang_dihitung'] = 'Semua proyek Anda';
        }

        // 🔥 Jika tidak ada proyek yang valid, return kosong
        if (empty($filteredProjectIds)) {
            $calculations['error'] = 'Tidak ada proyek ditemukan';
            return $calculations;
        }

        switch ($calcType) {
            case 'count_proyek':
                $calculations['jumlah_proyek'] = count($filteredProjectIds);
                $calculations['keterangan'] = 'Total proyek yang Anda ikuti';
                break;

            case 'count_anggota':
                // 🔥 Hitung anggota UNIK di proyek
                $anggotaData = ProyekUser::with('user:id,name')
                    ->whereIn('proyek_id', $filteredProjectIds)
                    ->get();
                
                $anggotaUnik = $anggotaData->unique('user_id');
                
                $calculations['jumlah_anggota'] = $anggotaUnik->count();
                $calculations['detail_anggota'] = $anggotaUnik->pluck('user.name')->filter()->values()->toArray();
                $calculations['keterangan'] = 'Total anggota unik dalam proyek';
                break;

            case 'sum_anggaran':
                // 🔥 Total anggaran semua proyek yang difilter
                $proyekData = Proyek::whereIn('id', $filteredProjectIds)
                    ->select('id', 'nama_proyek', 'anggaran')
                    ->get();
                
                $totalAnggaran = $proyekData->sum('anggaran');
                
                $calculations['total_anggaran'] = $totalAnggaran;
                $calculations['total_anggaran_formatted'] = 'Rp ' . number_format($totalAnggaran, 0, ',', '.');
                $calculations['detail_per_proyek'] = $proyekData->map(function($p) {
                    return [
                        'nama' => $p->nama_proyek,
                        'anggaran' => $p->anggaran,
                        'anggaran_formatted' => 'Rp ' . number_format($p->anggaran, 0, ',', '.')
                    ];
                })->toArray();
                $calculations['keterangan'] = 'Total anggaran proyek';
                break;

            case 'sum_dibayar':
                // 🔥 Total yang SUDAH DIBAYAR (dari kwitansi)
                $kwitansiData = ProyekKwitansi::whereIn('proyek_id', $filteredProjectIds)
                    ->select('proyek_id', 'jumlah', 'judul_kwitansi')
                    ->get();
                
                $totalDibayar = $kwitansiData->sum('jumlah');
                
                $calculations['total_dibayar'] = $totalDibayar;
                $calculations['total_dibayar_formatted'] = 'Rp ' . number_format($totalDibayar, 0, ',', '.');
                $calculations['jumlah_kwitansi'] = $kwitansiData->count();
                $calculations['detail_kwitansi'] = $kwitansiData->map(function($k) {
                    return [
                        'judul' => $k->judul_kwitansi,
                        'jumlah' => $k->jumlah,
                        'jumlah_formatted' => 'Rp ' . number_format($k->jumlah, 0, ',', '.')
                    ];
                })->toArray();
                $calculations['keterangan'] = 'Total yang sudah dibayar (dari kwitansi)';
                break;

            case 'sum_belum_dibayar':
                // 🔥 ANALISIS: Anggaran - Kwitansi = Belum Dibayar
                $proyekData = Proyek::whereIn('id', $filteredProjectIds)
                    ->select('id', 'nama_proyek', 'anggaran')
                    ->get();
                
                $totalAnggaran = $proyekData->sum('anggaran');
                
                $kwitansiData = ProyekKwitansi::whereIn('proyek_id', $filteredProjectIds)
                    ->select('proyek_id', 'jumlah')
                    ->get();
                
                $totalDibayar = $kwitansiData->sum('jumlah');
                $totalBelumDibayar = $totalAnggaran - $totalDibayar;
                
                $calculations['total_anggaran'] = $totalAnggaran;
                $calculations['total_anggaran_formatted'] = 'Rp ' . number_format($totalAnggaran, 0, ',', '.');
                $calculations['total_dibayar'] = $totalDibayar;
                $calculations['total_dibayar_formatted'] = 'Rp ' . number_format($totalDibayar, 0, ',', '.');
                $calculations['total_belum_dibayar'] = $totalBelumDibayar;
                $calculations['total_belum_dibayar_formatted'] = 'Rp ' . number_format($totalBelumDibayar, 0, ',', '.');
                $calculations['jumlah_kwitansi'] = $kwitansiData->count();
                
                // 🔥 Detail per proyek
                $calculations['detail_per_proyek'] = $proyekData->map(function($p) use ($kwitansiData) {
                    $dibayarProyek = $kwitansiData->where('proyek_id', $p->id)->sum('jumlah');
                    $belumDibayar = $p->anggaran - $dibayarProyek;
                    
                    return [
                        'nama' => $p->nama_proyek,
                        'anggaran' => $p->anggaran,
                        'anggaran_formatted' => 'Rp ' . number_format($p->anggaran, 0, ',', '.'),
                        'dibayar' => $dibayarProyek,
                        'dibayar_formatted' => 'Rp ' . number_format($dibayarProyek, 0, ',', '.'),
                        'belum_dibayar' => $belumDibayar,
                        'belum_dibayar_formatted' => 'Rp ' . number_format($belumDibayar, 0, ',', '.')
                    ];
                })->toArray();
                
                $calculations['keterangan'] = 'Total yang belum dibayar (Anggaran - Kwitansi)';
                break;

            case 'count_fitur':
                // 🔥 Hitung fitur berdasarkan proyek yang sudah difilter
                $fiturData = ProyekFitur::whereIn('proyek_id', $filteredProjectIds)
                    ->select('id', 'proyek_id', 'nama_fitur', 'status_fitur')
                    ->get();
                
                $calculations['jumlah_fitur'] = $fiturData->count();
                $calculations['detail_fitur'] = $fiturData->map(function($f) {
                    return [
                        'nama' => $f->nama_fitur,
                        'status' => $f->status_fitur
                    ];
                })->toArray();
                $calculations['keterangan'] = 'Total fitur dalam proyek';
                break;

            case 'count_anggota_fitur':
                // 🔥 Jika ada filter fitur spesifik dari dbResults
                if (!empty($dbResults['proyek_fitur'])) {
                    $fiturIds = collect($dbResults['proyek_fitur'])->pluck('id')->toArray();
                    $namaFitur = collect($dbResults['proyek_fitur'])->pluck('nama_fitur')->toArray();
                    $calculations['fitur_yang_dihitung'] = $namaFitur;
                } else {
                    // Ambil semua fitur dari proyek yang difilter
                    $fiturIds = ProyekFitur::whereIn('proyek_id', $filteredProjectIds)
                        ->pluck('id')
                        ->toArray();
                    $calculations['fitur_yang_dihitung'] = 'Semua fitur dalam proyek';
                }
                
                if (!empty($fiturIds)) {
                    $anggotaFiturData = ProyekFiturUser::with('user:id,name')
                        ->whereIn('proyek_fitur_id', $fiturIds)
                        ->get();
                    
                    $anggotaUnik = $anggotaFiturData->unique('user_id');
                    
                    $calculations['jumlah_anggota_fitur'] = $anggotaUnik->count();
                    $calculations['detail_anggota'] = $anggotaUnik->pluck('user.name')->filter()->values()->toArray();
                    $calculations['keterangan'] = 'Total anggota yang ditugaskan ke fitur';
                } else {
                    $calculations['jumlah_anggota_fitur'] = 0;
                    $calculations['keterangan'] = 'Tidak ada fitur ditemukan';
                }
                break;

            case 'count_file':
                $fileData = ProyekFile::whereIn('proyek_id', $filteredProjectIds)
                    ->select('id', 'nama_file', 'keterangan')
                    ->get();
                
                $calculations['jumlah_file'] = $fileData->count();
                $calculations['keterangan'] = 'Total file yang diunggah';
                break;

            case 'count_invoice':
                $invoiceData = ProyekInvoice::whereIn('proyek_id', $filteredProjectIds)
                    ->select('id', 'nomor_invoice', 'judul_invoice', 'jumlah', 'status')
                    ->get();
                
                $calculations['jumlah_invoice'] = $invoiceData->count();
                $calculations['total_nilai_invoice'] = $invoiceData->sum('jumlah');
                $calculations['total_nilai_invoice_formatted'] = 'Rp ' . number_format($invoiceData->sum('jumlah'), 0, ',', '.');
                $calculations['keterangan'] = 'Total invoice yang dibuat';
                break;

            case 'count_kwitansi':
                $kwitansiData = ProyekKwitansi::whereIn('proyek_id', $filteredProjectIds)
                    ->select('id', 'nomor_kwitansi', 'judul_kwitansi', 'jumlah')
                    ->get();
                
                $calculations['jumlah_kwitansi'] = $kwitansiData->count();
                $calculations['total_nilai_kwitansi'] = $kwitansiData->sum('jumlah');
                $calculations['total_nilai_kwitansi_formatted'] = 'Rp ' . number_format($kwitansiData->sum('jumlah'), 0, ',', '.');
                $calculations['keterangan'] = 'Total kwitansi yang dibuat';
                break;
        }

        return $calculations;
    }

    /**
     * STEP 2 — JSON → FILTER DB
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
                    $filters = $ai['filters']['proyek_user'] ?? [];
                    $query = ProyekUser::with(['user:id,name', 'proyek:id,nama_proyek'])
                        ->whereIn('proyek_id', $this->myProjectIds());
                    
                    // 🔥 Jika ada filter proyek spesifik, terapkan
                    if (!empty($ai['filters']['proyek'])) {
                        $proyekFiltered = $this->applyProjectFilter(Proyek::query(), $ai['filters']['proyek']);
                        $proyekIds = $proyekFiltered->pluck('id')->toArray();
                        if (!empty($proyekIds)) {
                            $query->whereIn('proyek_id', $proyekIds);
                        }
                    }
                    
                    $results['proyek_user'] = $query->get();
                    break;

                case 'proyek_fitur':
                    $results['proyek_fitur'] = $this->applyProjectChildFilter(
                        ProyekFitur::query(), 
                        $ai['filters']['proyek_fitur'] ?? [],
                        'proyek_id',
                        $ai['filters']['proyek'] ?? []
                    );
                    break;

                case 'proyek_fitur_user':
                    $results['proyek_fitur_user'] = $this->applyProjectChildFilter(
                        ProyekFiturUser::query(), 
                        $ai['filters']['proyek_fitur_user'] ?? [], 
                        'proyek_fitur_id',
                        $ai['filters']['proyek'] ?? []
                    );
                    break;

                case 'proyek_file':
                    $results['proyek_file'] = $this->applyProjectChildFilter(
                        ProyekFile::query(), 
                        $ai['filters']['proyek_file'] ?? [],
                        'proyek_id',
                        $ai['filters']['proyek'] ?? []
                    );
                    break;

                case 'proyek_invoice':
                    $results['proyek_invoice'] = $this->applyProjectChildFilter(
                        ProyekInvoice::query(), 
                        $ai['filters']['proyek_invoice'] ?? [],
                        'proyek_id',
                        $ai['filters']['proyek'] ?? []
                    );
                    break;

                case 'proyek_kwitansi':
                    $results['proyek_kwitansi'] = $this->applyProjectChildFilter(
                        ProyekKwitansi::query(), 
                        $ai['filters']['proyek_kwitansi'] ?? [],
                        'proyek_id',
                        $ai['filters']['proyek'] ?? []
                    );
                    break;

                case 'proyek_catatan_pekerjaan':
                    $results['proyek_catatan_pekerjaan'] = $this->applyProjectChildFilter(
                        ProyekCatatanPekerjaan::query(), 
                        $ai['filters']['proyek_catatan_pekerjaan'] ?? [],
                        'proyek_id',
                        $ai['filters']['proyek'] ?? []
                    );
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
     * 🔥 FILTER CHILD TABLE (DIPERBAIKI)
     */
    private function applyProjectChildFilter($query, array $filters, string $field = 'proyek_id', array $proyekFilter = [])
    {
        $myProjectIds = $this->myProjectIds();
        
        // 🔥 Jika ada filter proyek spesifik, filter dulu proyeknya
        if (!empty($proyekFilter)) {
            $filteredProyek = $this->applyProjectFilter(Proyek::query(), $proyekFilter);
            $proyekIds = $filteredProyek->pluck('id');
        } else {
            $proyekIds = $myProjectIds;
        }
        
        // Apply ke child table
        if ($field === 'proyek_fitur_id') {
            // Untuk tabel yang referensi ke proyek_fitur
            $query->whereIn($field, function($q) use ($proyekIds) {
                $q->select('id')
                  ->from('proyek_fitur')
                  ->whereIn('proyek_id', $proyekIds);
            });
        } else {
            // Untuk tabel yang langsung punya proyek_id
            $query->whereIn($field, $proyekIds);
        }
        
        return $this->applyFilters($query, $filters);
    }

    /**
     * FILTER CUSTOMER
     */
    private function customerForUser(array $filters)
    {
        $query = Customer::query();
        return $this->applyFilters($query, $filters);
    }

    private function myProjectIds()
    {
        return ProyekUser::where('user_id', Auth::id())
            ->pluck('proyek_id');
    }
}