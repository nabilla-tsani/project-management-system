<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class AIGenerateProposalService
{
    public function generateLatarBelakang($proyek): string
    {
        $prompt = <<<PROMPT
Buatkan latar belakang pengembangan sistem untuk proposal resmi dengan ketentuan:
- Nama proyek: {$proyek->nama_proyek}
- Jenis sistem: Sistem Informasi
- Bahasa formal dan profesional
- Panjang 1–2 paragraf
- Fokus pada permasalahan bisnis dan solusi sistem
- JANGAN GUNAKAN HURUF TEBAL ATAU MIRING, ATAU PEMBEDA APAPUN ITU, JADI KIRIMKAN PLAIN TEKS SAJA
PROMPT;

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('services.openrouter.key'),
            'Content-Type'  => 'application/json',
            'HTTP-Referer'  => config('app.url'), // wajib OpenRouter
            'X-Title'       => config('app.name'), // opsional tapi disarankan
        ])->post('https://openrouter.ai/api/v1/chat/completions', [
            'model' => 'google/gemini-2.0-flash-001',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'Kamu adalah penulis proposal proyek sistem informasi yang profesional.'
                ],
                [
                    'role' => 'user',
                    'content' => $prompt
                ],
            ],
            'temperature' => 0.6,
            'max_tokens' => 400,
        ]);

        if (!$response->successful()) {
            logger()->error('AI OpenRouter Error', [
                'response' => $response->body()
            ]);
            return 'Latar belakang belum tersedia.';
        }

        return trim($response->json('choices.0.message.content') ?? '');
    }
}
