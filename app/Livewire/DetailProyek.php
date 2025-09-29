<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Proyek;
use Barryvdh\DomPDF\Facade\Pdf;

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

    public function render()
    {
        return view('livewire.detail-proyek')
            ->layout('layouts.app'); 
    }
}
