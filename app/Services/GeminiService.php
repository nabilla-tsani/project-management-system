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


    public function chat(string $prompt, string $model = 'gemini-2.0-flash')
    {
        $intent = $this->detectIntent($prompt);
        Log::info('[Gemini] Intent Detected', $intent);

        if ($intent['type'] === 'database') {
            return $this->handleDatabaseIntent($intent, $prompt, $model);
        }

        return $this->callGemini([$this->formatMessage('user', $prompt)], $model);
    }


    public function chatWithHistory(array $messages, string $model = 'gemini-2.0-flash')
    {
        $lastMessage = end($messages)['message'] ?? '';
        $intent = $this->detectIntent($lastMessage);
        Log::info('[Gemini] Intent Detected (History)', $intent);

        if ($intent['type'] === 'database') {
            return $this->handleDatabaseIntent($intent, $lastMessage, $model);
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
     * Intent Detection (gunakan Gemini untuk klasifikasi)
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
        - Jika minta proyek berdasarkan filter → action = "filter_projects".
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
     * 🔹 Hasil DB dikirim ke Gemini sebagai konteks
     */
    protected function handleDatabaseIntent(array $intent, string $userPrompt, string $model): string
    {
        $resultText = '';

        switch ($intent['action']) {
            case 'count_projects':
                $count = Proyek::count();
                $resultText = "Jumlah proyek dalam database saat ini adalah {$count}.";
                break;

            case 'list_projects':
                $projects = Proyek::select('nama_proyek', 'status', 'lokasi')->get();
                if ($projects->isEmpty()) {
                    return "Belum ada proyek yang terdaftar.";
                }

                $resultText = "Berikut daftar proyek:\n";
                foreach ($projects as $p) {
                    $resultText .= "- {$p->nama_proyek} ({$p->status}, {$p->lokasi})\n";
                }
                break;

            case 'filter_projects':
                $filtered = $this->filterProjects($intent['filters'] ?? []);
                $resultText = $filtered ?: "Tidak ada proyek yang sesuai filter.";
                break;

            default:
                return "Saya mendeteksi ini pertanyaan database, tapi belum ada aksi yang cocok.";
        }

        // ✅ Tahap Augmentation — hasil query dijadikan konteks untuk Gemini
        $contextPrompt = <<<PROMPT
        Anda adalah asisten proyek. Gunakan data berikut untuk menjawab pertanyaan user dengan konteks yang akurat dan bahasa natural.

        Data dari database:
        {$resultText}

        Pertanyaan user:
        "{$userPrompt}"

        Jawablah berdasarkan data di atas tanpa menebak, dan tulis dengan bahasa profesional.
        PROMPT;

        return $this->callGemini([$this->formatMessage('user', $contextPrompt)], $model);
    }

    /**
     * Dynamic filter untuk data proyek
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

        $projects = $query->select('nama_proyek', 'status', 'lokasi', 'anggaran')->get();

        if ($projects->isEmpty()) {
            return '';
        }

        $text = "Data proyek hasil filter:\n";
        foreach ($projects as $p) {
            $text .= "- {$p->nama_proyek} ({$p->status}, {$p->lokasi}) anggaran Rp" . number_format($p->anggaran, 0, ',', '.') . "\n";
        }
        return $text;
    }
}
