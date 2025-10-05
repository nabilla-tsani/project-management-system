<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\ProyekKwitansi;
use App\Models\ProyekInvoice;
use Barryvdh\DomPDF\Facade\Pdf;

class AllProyekKwitansi extends Component
{
    public $kwitansis = [];
    public $proyekId;

    public $invoiceId;
    public $keterangan = '';
    public $openModalKwitansi = false;


    public function mount($proyekId = null)
    {
        $this->proyekId = $proyekId ?? request()->get('proyek_id');
        $this->loadData();
    }

    public function loadData()
    {
        $this->kwitansis = ProyekKwitansi::where('proyek_id', $this->proyekId)
            ->latest()
            ->get();
    }


    public function deleteKwitansi($id)
    {
        $kwitansi = ProyekKwitansi::findOrFail($id);
        $kwitansi->delete();
        $this->loadData();
    }

    public function printKwitansi($id)
    {
        $kwitansi = ProyekKwitansi::findOrFail($id);

        $pdf = Pdf::loadView('kwitansi-pdf', ['kwitansi' => $kwitansi])
            ->setPaper('A4', 'portrait');

        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="kwitansi-' . $kwitansi->nomor_kwitansi . '.pdf"',
        ]);
    }

    public function render()
    {
        return view('livewire.all-proyek-kwitansi', [
            'kwitansis' => $this->kwitansis
        ]);
    }
}
