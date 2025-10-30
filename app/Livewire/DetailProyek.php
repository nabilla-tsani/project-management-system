<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Proyek;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\ProposalAIService;

class DetailProyek extends Component
{
    public $proyekId;
    public $proyek;

    // mount() dijalankan saat komponen di-load
    public function mount($id)
    {
        $this->proyekId = $id;
        $this->proyek = Proyek::findOrFail($id);
    }

    public function generateProposal($id)
    {
        $proyek = Proyek::with(['customer'])->findOrFail($id);

        // Load template PDF
        $pdf = Pdf::loadView('proposal-pdf', compact('proyek'))
                  ->setPaper('a4', 'portrait'); // bisa ganti landscape

        return $pdf->stream("Proposal-Proyek-{$proyek->nama_proyek}.pdf");
    }

    public function generateProposalWithAI()
    {
        $proyek = Proyek::with('customer')->findOrFail($this->proyekId);
        $prompt = "Buatkan latar belakang proposal proyek untuk " . $proyek->nama_proyek . " di " . $proyek->lokasi;
        $aiService = app(\App\Services\GeminiService::class);
        $aiResponse = $aiService->ask($prompt);

        $proposalService = new ProposalAIService();
        $filePath = $proposalService->generate($proyek, $aiResponse);

        return response()->download($filePath)->deleteFileAfterSend(true);
    }

    public function render()
    {
        return view('livewire.detail-proyek')
            ->layout('layouts.app'); 
    }
}
