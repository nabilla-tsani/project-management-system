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

    // Untuk edit
    public $isEdit = false;
    public $editingId = null;

    // Properti untuk modal kwitansi
    public $showKwitansiModal = false;
    public $selectedInvoiceId;
    public $keteranganKwitansi = '';
    public $errorMessage = '';

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

    // buka modal tambah
    public function openCreateModal()
    {
        $this->resetForm();
        $this->isEdit = false;
        $this->openModal = true;
    }

    // buka modal edit
    public function editInvoice($id)
    {
        $invoice = ProyekInvoice::findOrFail($id);

        $this->editingId = $id;
        $this->judul_invoice = $invoice->judul_invoice;
        $this->jumlah = $invoice->jumlah;
        $this->tanggal_invoice = $invoice->tanggal_invoice;
        $this->keterangan = $invoice->keterangan;

        $this->isEdit = true;
        $this->openModal = true;
    }

    // simpan invoice baru
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

        session()->flash('success', 'Invoice berhasil ditambahkan.');
        $this->resetForm();
        $this->loadInvoices();
        $this->openModal = false;
    }

    // update invoice yang diedit
    public function updateInvoice()
    {
        $this->validate([
            'judul_invoice' => 'required|string|max:255',
            'jumlah' => 'required|numeric|min:1',
            'tanggal_invoice' => 'required|date',
        ]);

        $invoice = ProyekInvoice::findOrFail($this->editingId);
        $invoice->update([
            'judul_invoice' => $this->judul_invoice,
            'jumlah' => $this->jumlah,
            'tanggal_invoice' => $this->tanggal_invoice,
            'keterangan' => $this->keterangan,
        ]);

        session()->flash('success', 'Invoice berhasil diperbarui.');
        $this->resetForm();
        $this->loadInvoices();
        $this->openModal = false;
    }

    // reset form
    public function resetForm()
    {
        $this->reset(['judul_invoice', 'jumlah', 'tanggal_invoice', 'keterangan', 'editingId']);
        $this->resetErrorBag();
    }

    public function deleteInvoice($id)
    {
        $invoice = ProyekInvoice::findOrFail($id);
        $invoice->delete();
        $this->loadInvoices();
    }

    // Cetak invoice (sama seperti sebelumnya)
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
