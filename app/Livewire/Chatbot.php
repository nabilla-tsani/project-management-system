<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\GeminiService;
use Livewire\Attributes\On;

class Chatbot extends Component
{
    public $input;
    public $messages = [];
    public $isOpen = false;
    public $isLoading = false;

    public function mount()
    {
        $this->messages = session()->get('chatbot_messages', []);
    }
    


    public function toggleChat()
    {
        $this->isOpen = !$this->isOpen;
    }

    public function sendMessage()
    {
        if (!$this->input) return;

        // 1. Tampilkan pesan user LANGSUNG
        $this->messages[] = [
            'role' => 'user',
            'message' => $this->input
        ];

        $this->input = '';
        $this->isLoading = true;

        session()->put('chatbot_messages', $this->messages);
        $this->dispatch('scroll-to-bottom');

        // 2. Trigger AI di request TERPISAH
        $this->dispatch('fetch-ai-response');
    }


   #[On('fetch-ai-response')]
    public function fetchAiResponse()
    {
        $gemini = new GeminiService();
        $result = $gemini->chatWithHistory($this->messages);

        // 🔥 NORMALISASI NEWLINE (jaga-jaga)
        $result = str_replace(
            ["\\r\\n", "\\n", "\\r"],
            "\n",
            $result
        );

        $this->messages[] = [
            'role' => 'ai',
            'message' => trim($result) // ❌ TANPA json_encode
        ];

        $this->isLoading = false;

        session()->put('chatbot_messages', $this->messages);
        $this->dispatch('scroll-to-bottom');
    }



    public function resetChat()
    {
        $this->messages = [];
        $this->isLoading = false;

        session()->forget('chatbot_messages');
    }

    public function render()
    {
        return view('livewire.chatbot');
    }
}
