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
    public $judulKwitansi = '';    
    public $tanggalKwitansi = '';
    public $errorMessage = '';
    // Track whether the kwitansi modal is in edit mode
    public $isEditingKwitansi = false;
    public $editingKwitansiId = null;

    public $confirmDelete = false;
    public $search = '';

    public function mount($proyekId = null)
    {
        $this->proyekId = $proyekId;
        $this->proyek = Proyek::findOrFail($proyekId);
        $this->loadInvoices();
    }

    public function loadInvoices()
    {
        $search = trim(strtolower($this->search));

        $query = ProyekInvoice::where('proyek_id', $this->proyek->id);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(judul_invoice) LIKE ?', ["%{$search}%"])
                ->orWhereRaw('CAST(nomor_invoice AS TEXT) LIKE ?', ["%{$search}%"])
                ->orWhereRaw('CAST(jumlah AS TEXT) LIKE ?', ["%{$search}%"])
                ->orWhereRaw('CAST(tanggal_invoice AS TEXT) LIKE ?', ["%{$search}%"]);
            });
        }

        $this->invoices = $query->orderBy('tanggal_invoice', 'desc')->get();

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

        session()->flash('success', 'Tagihan berhasil dibuat.');
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

        session()->flash('success', 'Tagihan berhasil diperbarui.');
        $this->resetForm();
        $this->loadInvoices();
        $this->isEdit = false;
        $this->openModal = false;
    }

    public function resetForm()
    {
        $this->reset(['judul_invoice', 'jumlah', 'tanggal_invoice', 'keterangan', 'editingId']);
        $this->resetErrorBag();
    }

    public function askDelete($id)
    {
        $this->deleteId = $id;
        $this->confirmDelete = true;
    }

    public function confirmDeleteInvoice($id)
    {
        $invoice = ProyekInvoice::findOrFail($id);
        $invoice->delete();
            
        session()->flash('success', 'Tagihan berhasil dihapus.');
        $this->loadInvoices();

        $this->confirmDelete = false;
        $this->deleteId = null;
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
        return;
    }

    if ($invoice->status !== 'dibayar') {
        $this->errorMessage = 'Kwitansi hanya dapat dibuat untuk invoice yang sudah dibayar.';
        return;
    }

    $this->selectedInvoiceId = $id;

    $existing = ProyekKwitansi::where('nomor_invoice', $invoice->nomor_invoice)->first();

    if ($existing) {
        // MODE EDIT
        $this->isEditingKwitansi = true;
        $this->editingKwitansiId = $existing->id;

        $this->judulKwitansi      = $existing->judul_kwitansi;
        $this->tanggalKwitansi    = $existing->tanggal_kwitansi;
        $this->keteranganKwitansi = $existing->keterangan;
    } 
    else {
        // MODE CREATE
        $this->isEditingKwitansi = false;
        $this->editingKwitansiId = null;

        // DEFAULT VALUES
        $this->judulKwitansi      = 'Kwitansi ' . $invoice->judul_invoice;
        $this->tanggalKwitansi    = now()->toDateString();
        $this->keteranganKwitansi = $invoice->keterangan ?? '';
    }

    $this->showKwitansiModal = true;
    }


    public function simpanKwitansi()
    {
    $this->validate([
        'judulKwitansi' => 'required|string|max:255',
        'tanggalKwitansi' => 'required|date',
        'keteranganKwitansi' => 'nullable|string|max:1000',
    ]);

    $invoice = ProyekInvoice::find($this->selectedInvoiceId);

    $existing = ProyekKwitansi::where('nomor_invoice', $invoice->nomor_invoice)->first();

    if ($existing) {
        // UPDATE
        $existing->update([
            'proyek_id' => $invoice->proyek_id,
            'judul_kwitansi' => $this->judulKwitansi,       // pakai input user
            'jumlah' => $invoice->jumlah,
            'tanggal_kwitansi' => $this->tanggalKwitansi,  // pakai input user
            'keterangan' => $this->keteranganKwitansi,
            'user_id' => auth()->id(),
        ]);

        session()->flash('success', 'Kwitansi berhasil diperbarui.');
    } 
    else {
        // CREATE
        $nomorKwitansi = 'KW-' . time();

        ProyekKwitansi::create([
            'nomor_kwitansi' => $nomorKwitansi,
            'nomor_invoice' => $invoice->nomor_invoice,
            'proyek_id' => $invoice->proyek_id,
            'judul_kwitansi' => $this->judulKwitansi,        // user input
            'jumlah' => $invoice->jumlah,
            'tanggal_kwitansi' => $this->tanggalKwitansi,    // user input
            'keterangan' => $this->keteranganKwitansi,
            'user_id' => auth()->id(),
        ]);

        session()->flash('success', 'Kwitansi berhasil dibuat.');
    }
        $this->closeKwitansiModal();
        $this->loadInvoices();
    }


    public function closeKwitansiModal()
    {
        $this->showKwitansiModal = false;
        $this->keteranganKwitansi = '';
        $this->selectedInvoiceId = null;
        $this->isEditingKwitansi = false;
        $this->editingKwitansiId = null;
        $this->errorMessage = '';
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

    public function updatedSearch()
{
    $this->loadInvoices();
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
