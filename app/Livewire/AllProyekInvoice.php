<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\ProyekInvoice;
use App\Models\Proyek;
use Barryvdh\DomPDF\Facade\Pdf;

class AllProyekInvoice extends Component
{
    public $proyekId;
    public $proyek;
    public $invoices = [];
    public $totalInvoice = 0;
    public $sisaInvoice = 0;

    // Form fields
    public $judul_invoice;
    public $jumlah;
    public $tanggal_invoice;
    public $keterangan;

    // Untuk dropdown status
    public $statuses = [];

    public function mount($proyekId)
    {
        $this->proyekId = $proyekId;
        $this->proyek = Proyek::findOrFail($proyekId);
        $this->loadInvoices();
    }

    public function loadInvoices()
    {
        $this->invoices = ProyekInvoice::where('proyek_id', $this->proyek->id)->get();

        // Simpan semua status dalam array agar bisa di-bind di dropdown
        $this->statuses = $this->invoices->pluck('status', 'id')->toArray();

        $this->totalInvoice = $this->invoices->sum('jumlah');
        $this->sisaInvoice = $this->proyek->anggaran - $this->totalInvoice;
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

        // Reset form fields
        $this->reset(['judul_invoice', 'jumlah', 'tanggal_invoice', 'keterangan']);

        // Reload invoice list
        $this->loadInvoices();

        session()->flash('message', 'Invoice berhasil ditambahkan.');

        // Tutup modal di Alpine
        $this->dispatch('close-modal');
    }

    public function updateStatus($invoiceId, $status)
    {
        $invoice = ProyekInvoice::findOrFail($invoiceId);
        $invoice->update(['status' => $status]);

        // Refresh daftar invoice agar warna & value berubah otomatis
        $this->loadInvoices();

        session()->flash('message', 'Status invoice berhasil diperbarui.');
    }


    public function deleteInvoice($id)
    {
        $invoice = ProyekInvoice::findOrFail($id);
        $invoice->delete();

        $this->loadInvoices();
        session()->flash('message', 'Invoice berhasil dihapus.');
    }

    public function printInvoice($id)
    {
        $invoice = ProyekInvoice::findOrFail($id);

        // Kirim juga data proyek
        $pdf = Pdf::loadView('invoice-pdf', [
            'invoice' => $invoice,
            'proyek' => $this->proyek, // Ambil dari model Proyek yang sudah dimuat
        ]);

        return $pdf->stream('invoice-'.$invoice->nomor_invoice.'.pdf');
    }

    public function buatKwitansi($invoiceId)
    {
        $this->dispatch('buat-kwitansi', $invoiceId);
        session()->flash('message', 'Proses pembuatan kwitansi sedang dilakukan...');
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
