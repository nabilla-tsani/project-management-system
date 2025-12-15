<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\GeminiService;

class Chatbot extends Component
{
    public $input;
    public $messages = [];
    public $isOpen = false;
    public $isLoading = false;

    public function toggleChat()
    {
        $this->isOpen = !$this->isOpen;
    }

    public function sendMessage()
    {
        if (!$this->input) return;

        // Tambah pesan user
        $this->messages[] = [
            'role' => 'user',
            'message' => $this->input
        ];

        $this->isLoading = true;
        $this->input = '';

        // Ambil respon AI
        $this->fetchAiResponse();
    }

    public function fetchAiResponse()
    {
        $gemini = new GeminiService();
        $result = $gemini->chatWithHistory($this->messages);

        // Tambah pesan AI
        $this->messages[] = [
            'role' => 'ai',
            'message' => json_encode($result, JSON_PRETTY_PRINT)
        ];

        $this->isLoading = false;
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
