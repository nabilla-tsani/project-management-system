<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\ProyekKwitansi;
use App\Models\ProyekInvoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class AllProyekKwitansi extends Component
{
    public $kwitansis = [];
    public $proyekId;

    public $invoiceId;
    public $keterangan = '';
    public $openModalKwitansi = false;
    // Edit kwitansi
    public $editingKwitansiId = null;
    public $edit_judul_kwitansi = null;
    public $edit_tanggal_kwitansi = null;
    public $edit_keterangan = '';
    public $showEditModal = false;
    public $confirmDelete = false;
    public $deleteId = null;
    public $search='';


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

    public function askDelete($id)
    {
        $this->deleteId = $id;
        $this->confirmDelete = true;
    }

    public function confirmDeleteKwitansi()
    {
        $kwitansi = ProyekKwitansi::find($this->deleteId);

        if ($kwitansi) {
            $kwitansi->delete();
        }

        $this->loadData();
        session()->flash('success', 'Receipt successfully deleted.');

        $this->confirmDelete = false;
        $this->deleteId = null;
    }

    public function openEditKwitansi($id)
    {
        $kwitansi = ProyekKwitansi::find($id);
        if (! $kwitansi) {
            session()->flash('error', 'Receipt not found.');
            return;
        }

    $this->editingKwitansiId = $kwitansi->id;
    $this->edit_judul_kwitansi = $kwitansi->judul_kwitansi;
    $this->edit_tanggal_kwitansi = $kwitansi->tanggal_kwitansi ? Carbon::parse($kwitansi->tanggal_kwitansi)->toDateString() : Carbon::now()->toDateString();
    $this->edit_keterangan = $kwitansi->keterangan;
    $this->showEditModal = true;
    }

    public function updateKwitansi()
    {
        $this->validate([
            'edit_judul_kwitansi' => 'required|string',
            'edit_tanggal_kwitansi' => 'required|date',
            'edit_keterangan' => 'nullable|string|max:1000',
        ]);

        $kwitansi = ProyekKwitansi::find($this->editingKwitansiId);
        if (! $kwitansi) {
            session()->flash('error', 'Receipt not found.');
            $this->showEditModal = false;
            return;
        }

        $kwitansi->judul_kwitansi = $this->edit_judul_kwitansi;
        $kwitansi->tanggal_kwitansi = $this->edit_tanggal_kwitansi;
        $kwitansi->keterangan = $this->edit_keterangan;
        $kwitansi->save();

        session()->flash('success', 'Receipt successfully updated.');

        $this->showEditModal = false;
        $this->editingKwitansiId = null;
        $this->edit_judul_kwitansi = null;
        $this->edit_tanggal_kwitansi = null;
        $this->edit_keterangan = '';

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
