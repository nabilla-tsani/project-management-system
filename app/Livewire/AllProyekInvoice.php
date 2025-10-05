<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\ProyekInvoice;
use App\Models\Proyek;
use App\Models\ProyekKwitansi;
use Barryvdh\DomPDF\Facade\Pdf;

class AllProyekInvoice extends Component
{
    public $proyekId;
    public $proyek;
    public $invoices = [];
    public $totalInvoice = 0;
    public $sisaInvoice = 0;

    public $judul_invoice;
    public $jumlah;
    public $tanggal_invoice;
    public $keterangan;

    public $statuses = [];
    public $openModal = false;

    // Properti untuk modal kwitansi
    public $showKwitansiModal = false;
    public $selectedInvoiceId;
    public $keteranganKwitansi = '';

    public function mount($proyekId)
    {
        $this->proyekId = $proyekId;
        $this->proyek = Proyek::findOrFail($proyekId);
        $this->loadInvoices();
    }

    public function loadInvoices()
    {
        $this->invoices = ProyekInvoice::where('proyek_id', $this->proyek->id)
            ->orderBy('tanggal_invoice', 'desc')
            ->get();

        $this->statuses = $this->invoices->pluck('status', 'id')->toArray();
        $this->totalInvoice = $this->invoices->sum('jumlah');
        $this->sisaInvoice = $this->proyek->anggaran - $this->totalInvoice;
    }

    /** 
     * Tampilkan modal input keterangan
     */
   public function konfirmasiKwitansi($invoiceId)
    {
        $this->resetErrorBag();
        $this->errorMessage = '';

        $invoice = ProyekInvoice::findOrFail($invoiceId);

        // cek apakah kwitansi sudah dibuat sebelumnya
        $existing = ProyekKwitansi::where('nomor_invoice', $invoice->nomor_invoice)->first();
        if ($existing) {
            // tampilkan pesan error tanpa buka modal
            $this->errorMessage = 'Kwitansi sudah dibuat dengan nomor: ' . $existing->nomor_kwitansi;
            $this->selectedInvoiceId = $invoiceId;
            return;
        }

        // buka modal jika belum ada kwitansi
        $this->selectedInvoiceId = $invoiceId;
        $this->keteranganKwitansi = '';
        $this->showKwitansiModal = true;
    }

    /**
     * Simpan kwitansi ke database
     */
    public $errorMessage = '';

    public function simpanKwitansi()
    {
        $this->errorMessage = '';

        $invoice = ProyekInvoice::findOrFail($this->selectedInvoiceId);

        // Buat kwitansi baru
        ProyekKwitansi::create([
            'nomor_kwitansi'   => 'KW-' . now()->format('YmdHis'),
            'nomor_invoice'    => $invoice->nomor_invoice,
            'proyek_id'        => $invoice->proyek_id,
            'judul_kwitansi'   => 'Kwitansi - ' . $invoice->judul_invoice,
            'jumlah'           => $invoice->jumlah,
            'tanggal_kwitansi' => now(),
            'keterangan'       => $this->keteranganKwitansi,
            'user_id'          => auth()->id(),
        ]);

        session()->flash('success', 'Kwitansi berhasil dibuat.');
        $this->showKwitansiModal = false;
        $this->loadInvoices();
    }


    public function store()
    {
        $this->validate([
            'judul_invoice' => 'required|string|max:255',
            'jumlah' => 'required|numeric|min:1|max:' . $this->sisaInvoice,
            'tanggal_invoice' => 'required|date',
        ]);

        ProyekInvoice::create([
            'nomor_invoice' => 'INV-' . time(),
            'proyek_id' => $this->proyek->id,
            'judul_invoice' => $this->judul_invoice,
            'jumlah' => $this->jumlah,
            'tanggal_invoice' => $this->tanggal_invoice,
            'keterangan' => $this->keterangan,
            'status' => 'belum_dibayar',
            'user_id' => auth()->id(),
        ]);

        $this->reset(['judul_invoice', 'jumlah', 'tanggal_invoice', 'keterangan']);
        $this->loadInvoices();
        $this->openModal = false;
    }

    public function updateStatus($invoiceId, $status)
    {
        $invoice = ProyekInvoice::findOrFail($invoiceId);
        $invoice->update(['status' => $status]);
        $this->loadInvoices();
    }

    public function deleteInvoice($id)
    {
        $invoice = ProyekInvoice::findOrFail($id);
        $invoice->delete();
        $this->loadInvoices();
    }

    public function printInvoice($id)
    {
        $invoice = ProyekInvoice::findOrFail($id);

        $invoice = json_decode(json_encode($invoice), true);
        array_walk_recursive($invoice, function (&$value) {
            if (is_string($value)) {
                $value = mb_convert_encoding($value, 'UTF-8', 'auto');
            }
        });

        $pdf = Pdf::loadView('invoice-pdf', [
            'invoice' => (object) $invoice,
            'proyek' => $this->proyek,
        ]);

        return response()->streamDownload(function () use ($pdf, $invoice) {
            echo $pdf->output();
        }, 'invoice-' . $invoice['nomor_invoice'] . '.pdf');
    }

    public function render()
    {
        return view('livewire.all-proyek-invoice', [
            'invoices' => $this->invoices,
            'totalInvoice' => $this->totalInvoice,
            'sisaInvoice' => $this->sisaInvoice,
            'statuses' => $this->statuses,
        ]);
    }
}
