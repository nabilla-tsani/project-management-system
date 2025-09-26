<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Proyek;

class GeminiService
{
    protected $base = 'https://generativelanguage.googleapis.com/v1beta/models';
    protected $key;

    public function __construct()
    {
        $this->key = config('services.gemini.key');
    }

    /**
     * Chat tanpa riwayat (single-turn)
     */
    public function chat(string $prompt, string $model = 'gemini-2.0-flash')
    {
        $intent = $this->detectIntent($prompt);
        Log::info('[Gemini] Intent Detected', $intent);

        if ($intent['type'] === 'database') {
            return $this->handleDatabaseIntent($intent);
        }

        return $this->callGemini([$this->formatMessage('user', $prompt)], $model);
    }

    /**
     * Chat dengan riwayat (multi-turn)
     */
    public function chatWithHistory(array $messages, string $model = 'gemini-2.0-flash')
    {
        $lastMessage = end($messages)['message'] ?? '';
        $intent = $this->detectIntent($lastMessage);
        Log::info('[Gemini] Intent Detected (History)', $intent);

        if ($intent['type'] === 'database') {
            return $this->handleDatabaseIntent($intent);
        }

        $contents = array_map(function ($msg) {
            return $this->formatMessage(
                $msg['role'] === 'ai' ? 'model' : 'user',
                $msg['message']
            );
        }, $messages);

        return $this->callGemini($contents, $model);
    }

    public function ask(string $prompt)
    {
        return $this->chat($prompt);
    }

    /**
     * Panggil Gemini API
     */
    protected function callGemini(array $contents, string $model)
    {
        $url = "{$this->base}/{$model}:generateContent?key={$this->key}";
        $payload = ["contents" => $contents];

        $response = Http::post($url, $payload);

        if ($response->failed()) {
            return "Terjadi error: " . $response->body();
        }

        $data = $response->json();
        return $data['candidates'][0]['content']['parts'][0]['text'] ?? 'Tidak ada respons dari AI';
    }

    protected function formatMessage(string $role, string $text): array
    {
        return [
            "role" => $role,
            "parts" => [["text" => $text]]
        ];
    }

    /**
     * Intent Detection → sekarang mendukung filter per kolom
     */
    protected function detectIntent(string $prompt): array
    {
        $url = "{$this->base}/gemini-2.0-flash:generateContent?key={$this->key}";

        $classifierPrompt = <<<PROMPT
Anda adalah sistem deteksi intent untuk query database proyek.
Analisis pesan berikut dan keluarkan JSON VALID tanpa penjelasan tambahan.

Pesan: "{$prompt}"

Format JSON wajib:
{
  "type": "database" | "general",
  "action": "count_projects" | "list_projects" | "filter_projects" | null,
  "filters": {
    "status": "belum_dimulai|sedang_berjalan|selesai" | null,
    "lokasi": "string atau null",
    "customer": "string atau null",
    "tanggal_mulai_after": "YYYY-MM-DD atau null",
    "tanggal_selesai_before": "YYYY-MM-DD atau null",
    "anggaran_min": number atau null,
    "anggaran_max": number atau null
  }
}

Aturan:
- Jika user hanya tanya jumlah proyek → action = "count_projects".
- Jika minta semua daftar proyek → action = "list_projects".
- Jika minta proyek berdasarkan filter (misal "yang sedang berjalan" atau "di Purwokerto") → action = "filter_projects" dan isi filters sesuai.
- Jika bukan pertanyaan database, kembalikan {"type":"general","action":null}.
PROMPT;

        $payload = ["contents" => [$this->formatMessage("user", $classifierPrompt)]];
        $response = Http::post($url, $payload);

        if ($response->failed()) {
            return ['type' => 'general', 'action' => null, 'filters' => []];
        }

        $data = $response->json();
        $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';

        if (preg_match('/\{.*\}/s', $text, $matches)) {
            $text = $matches[0];
        }

        $parsed = json_decode($text, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return ['type' => 'general', 'action' => null, 'filters' => []];
        }

        return $parsed;
    }

    /**
     * Handler query database → sekarang mendukung filter dinamis
     */
    protected function handleDatabaseIntent(array $intent): string
    {
        switch ($intent['action']) {
            case 'count_projects':
                $count = Proyek::count();
                return "📊 Saat ini ada **{$count} proyek** yang terdaftar.";

            case 'list_projects':
                $projects = Proyek::pluck('nama_proyek')->toArray();
                return empty($projects)
                    ? "Belum ada proyek yang terdaftar."
                    : "📋 Daftar proyek: " . implode(", ", $projects);

            case 'filter_projects':
                return $this->filterProjects($intent['filters'] ?? []);

            default:
                return "Saya mendeteksi ini pertanyaan database, tetapi belum ada action yang sesuai.";
        }
    }

    /**
     * Query dinamis berdasarkan filter
     */
    protected function filterProjects(array $filters): string
    {
        $query = Proyek::query();

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['lokasi'])) {
            $query->where('lokasi', 'like', '%' . $filters['lokasi'] . '%');
        }

        if (!empty($filters['customer'])) {
            $query->whereHas('customer', function ($q) use ($filters) {
                $q->where('nama', 'like', '%' . $filters['customer'] . '%');
            });
        }

        if (!empty($filters['tanggal_mulai_after'])) {
            $query->whereDate('tanggal_mulai', '>=', $filters['tanggal_mulai_after']);
        }

        if (!empty($filters['tanggal_selesai_before'])) {
            $query->whereDate('tanggal_selesai', '<=', $filters['tanggal_selesai_before']);
        }

        if (!empty($filters['anggaran_min'])) {
            $query->where('anggaran', '>=', $filters['anggaran_min']);
        }

        if (!empty($filters['anggaran_max'])) {
            $query->where('anggaran', '<=', $filters['anggaran_max']);
        }

        $projects = $query->pluck('nama_proyek')->toArray();

        if (empty($projects)) {
            return "Tidak ada proyek yang sesuai dengan filter.";
        }

        return "🔍 Proyek yang sesuai filter: " . implode(", ", $projects);
    }
}
