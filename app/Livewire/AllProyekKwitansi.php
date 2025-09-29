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

    public $invoiceId;           // simpan id invoice sementara
    public $keterangan = '';     // input dari popup
    public $showModal = false;   // kontrol popup
    public $kwitansiExisting;    // simpan jika sudah ada kwitansi

    public function mount($proyekId = null)
    {
        $this->proyekId = $proyekId ?? request()->get('proyek_id'); // bisa dari route, query, atau parameter
        $this->loadData();
    }

    public function loadData()
    {
        $this->kwitansis = ProyekKwitansi::where('proyek_id', $this->proyekId)
            ->latest()
            ->get();
    }


    #[\Livewire\Attributes\On('buat-kwitansi')]
    public function createKwitansi($invoiceId)
    {
        $invoice = ProyekInvoice::findOrFail($invoiceId);

        $this->invoiceId = $invoiceId;
        $this->kwitansiExisting = ProyekKwitansi::where('nomor_invoice', $invoice->nomor_invoice)->first();
        $this->showModal = true;
    }

    public function simpanKwitansi()
    {
        $invoice = ProyekInvoice::findOrFail($this->invoiceId);

        // Jika kwitansi sudah ada, jangan buat baru
        if ($this->kwitansiExisting) {
            session()->flash('success', ' Kwitansi sudah ada dengan nomor ' . $this->kwitansiExisting->nomor_kwitansi);
            $this->reset(['invoiceId', 'keterangan', 'showModal', 'kwitansiExisting']);
            return;
        }

        ProyekKwitansi::create([
            'nomor_kwitansi'   => 'KW-' . now()->format('YmdHis'),
            'nomor_invoice'    => $invoice->nomor_invoice,
            'proyek_id'        => $invoice->proyek_id,
            'judul_kwitansi'   => 'Kwitansi - ' . $invoice->judul_invoice,
            'jumlah'           => $invoice->jumlah,
            'tanggal_kwitansi' => now(),
            'keterangan'       => $this->keterangan,
            'user_id'          => auth()->id(),
        ]);

        $this->reset(['invoiceId', 'keterangan', 'showModal', 'kwitansiExisting']);
        session()->flash('success', ' Kwitansi berhasil dibuat.');
        $this->loadData();
    }

    public function deleteKwitansi($id)
    {
        $kwitansi = ProyekKwitansi::findOrFail($id);
        $kwitansi->delete();

        session()->flash('success', ' Kwitansi berhasil dihapus.');
        $this->loadData();
    }

    public function closeModal()
    {
        $this->reset(['invoiceId', 'keterangan', 'showModal', 'kwitansiExisting']);
    }

    public function printKwitansi($id)
{
    $kwitansi = ProyekKwitansi::findOrFail($id);

    $pdf = Pdf::loadView('kwitansi-pdf', [
        'kwitansi' => $kwitansi
    ])->setPaper('A4', 'portrait');

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
