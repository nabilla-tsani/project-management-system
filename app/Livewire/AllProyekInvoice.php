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

    public function updateStatus($id, $status)
    {
        $invoice = ProyekInvoice::find($id);
        $invoice->status = $status;
        $invoice->save();

        // Refresh local lists and clear any previous error for this invoice
        $this->loadInvoices();
        if ($this->selectedInvoiceId == $id) {
            $this->errorMessage = '';
            $this->selectedInvoiceId = null;
        }
    }

    public function createKwitansi($id)
    {
        $invoice = ProyekInvoice::find($id);
        if (! $invoice) {
            $this->errorMessage = 'Invoice tidak ditemukan.';
            $this->selectedInvoiceId = $id;
            return;
        }

        // only allow creating kwitansi when invoice is marked as paid
        if ($invoice->status !== 'dibayar') {
            $this->errorMessage = 'Kwitansi hanya dapat dibuat untuk invoice yang sudah dibayar.';
            $this->selectedInvoiceId = $id;
            return;
        }

        $this->selectedInvoiceId = $id;
        $this->keteranganKwitansi = $invoice->keterangan ?? '';
        $this->errorMessage = '';
        $this->showKwitansiModal = true;
    }

    public function simpanKwitansi()
    {
        $this->validate([
            'keteranganKwitansi' => 'nullable|string|max:1000',
        ]);

        if (! $this->selectedInvoiceId) {
            $this->errorMessage = 'Tidak ada invoice yang dipilih.';
            return;
        }

        $invoice = ProyekInvoice::find($this->selectedInvoiceId);
        if (! $invoice) {
            $this->errorMessage = 'Invoice tidak ditemukan.';
            return;
        }

        // Cek apakah sudah ada kwitansi dengan nomor_invoice yang sama
        $existing = ProyekKwitansi::where('nomor_invoice', $invoice->nomor_invoice)->first();

        if ($existing) {
            // Update kwitansi yang sudah ada dengan data baru
            $existing->update([
                'proyek_id' => $invoice->proyek_id,
                'judul_kwitansi' => 'Kwitansi ' . $invoice->judul_invoice,
                'jumlah' => $invoice->jumlah,
                'tanggal_kwitansi' => now()->toDateString(),
                'keterangan' => $this->keteranganKwitansi,
                'user_id' => auth()->id(),
            ]);

            session()->flash('success', 'Kwitansi berhasil diperbarui.');
        } else {
            // Buat kwitansi baru
            $nomorKwitansi = 'KW-' . time();
            ProyekKwitansi::create([
                'nomor_kwitansi' => $nomorKwitansi,
                'nomor_invoice' => $invoice->nomor_invoice,
                'proyek_id' => $invoice->proyek_id,
                'judul_kwitansi' => 'Kwitansi ' . $invoice->judul_invoice,
                'jumlah' => $invoice->jumlah,
                'tanggal_kwitansi' => now()->toDateString(),
                'keterangan' => $this->keteranganKwitansi,
                'user_id' => auth()->id(),
            ]);

            session()->flash('success', 'Kwitansi berhasil dibuat.');
        }

        // close modal and reset related props
        $this->showKwitansiModal = false;
        $this->keteranganKwitansi = '';
        $this->selectedInvoiceId = null;

        // reload invoices in case any derived values change
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
