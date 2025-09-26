<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\GeminiService;
use Illuminate\Support\Facades\Log;

class Chatbot extends Component
{
    public $messages = [];
    public $input;
    public $isOpen = false;
    public $isLoading = false;

    public function toggleChat()
    {
        $this->isOpen = !$this->isOpen;
    }

    public function sendMessage()
    {
        $userMessage = trim($this->input);
        if (!$userMessage) return;

        // Tambahkan pesan user
        $this->messages[] = ['role' => 'user', 'message' => $userMessage];
        $this->isLoading = true;
        $this->input = '';

        // Jalankan AI setelah render UI
        $this->dispatch('fetch-ai-response');
    }

    public function fetchAiResponse()
    {
        try {
            $gemini = new GeminiService();
            $aiResponse = $gemini->chatWithHistory($this->messages);

            // Log untuk debugging hasil AI
            Log::info('[Chatbot] AI Response', ['response' => $aiResponse]);

            $this->messages[] = ['role' => 'ai', 'message' => $aiResponse];
        } catch (\Throwable $e) {
            Log::error('[Chatbot] Error', ['message' => $e->getMessage()]);
            $this->messages[] = [
                'role' => 'ai',
                'message' => "⚠️ Terjadi kesalahan saat memproses jawaban AI."
            ];
        } finally {
            $this->isLoading = false;
        }
    }

    public function resetChat()
    {
        $this->messages = [];
        $this->isLoading = false;
    }

    public function render()
    {
        return view('livewire.chatbot');
    }
}
