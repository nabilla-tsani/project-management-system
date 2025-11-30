<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Proyek;
use App\Models\Customer;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\ProposalAIService;
use Illuminate\Support\Facades\Auth;

class DetailProyek extends Component
{
    public $proyekId;
    public $proyek;
    public $proyeks;
    public $showModal = false;

    public $proyek_id;
    public $nama_proyek;
    public $customer_id;
    public $deskripsi;
    public $lokasi;
    public $tanggal_mulai;
    public $tanggal_selesai;
    public $anggaran;
    public $status;

    public $customers = [];
    public $isEdit = false;

    protected $rules = [
        'nama_proyek' => 'required|string|max:255',
        'customer_id' => 'required|exists:customer,id',
        'deskripsi' => 'nullable|string',
        'lokasi' => 'nullable|string|max:255',
        'tanggal_mulai' => 'required|date',
        'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
        'anggaran' => 'required|numeric',
        'status' => 'required|string|max:50',
    ];

    protected $messages = [
        'nama_proyek.max' => 'Maximum Project Name is 255 characters.',
        'customer_id.exists' => 'Invalid Customer.',
        'lokasi.max' => 'Maximum Location name is 255 characters.',
        'tanggal_selesai.after_or_equal' => 'The end date must be after or same as start date.',
        'anggaran.numeric' => 'The budget must be a number.',
    ];

    public function mount($id)
    {
        $this->proyekId = $id;
        $this->proyek = Proyek::findOrFail($id);

        $user = Auth::user();
        $this->proyeks = $user->proyeks()->with('customer')->get();

        $this->customers = Customer::orderBy('nama')->get();
    }

    private function loadData()
    {
        $this->proyek = Proyek::with('customer')->find($this->proyekId);
        $this->proyeks = Auth::user()->proyeks()->with('customer')->get();
    }

    public function openModal($id = null)
    {
        $this->resetForm();
        if ($id) {
            $this->edit($id);
        } else {
            $this->showModal = true;
        }
    }

    public function closeModal()
    {
        $this->resetForm();
        $this->showModal = false;
    }

    private function resetForm()
    {
        $this->reset([
            'proyek_id', 'nama_proyek', 'customer_id',
            'deskripsi', 'lokasi',
            'tanggal_mulai', 'tanggal_selesai',
            'anggaran', 'status', 'isEdit'
        ]);
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function edit($id)
    {
        $proyek = Proyek::findOrFail($id);

        $this->proyek_id = $proyek->id;
        $this->nama_proyek = $proyek->nama_proyek;
        $this->customer_id = $proyek->customer_id;
        $this->deskripsi = $proyek->deskripsi;
        $this->lokasi = $proyek->lokasi;
        $this->tanggal_mulai = $proyek->tanggal_mulai ? $proyek->tanggal_mulai->format('Y-m-d') : null;
        $this->tanggal_selesai = $proyek->tanggal_selesai ? $proyek->tanggal_selesai->format('Y-m-d') : null;

        $this->anggaran = $proyek->anggaran;
        $this->status = $proyek->status;

        $this->isEdit = true;
        $this->showModal = true;
    }

    public function update()
    {
        $this->validate();

        Proyek::findOrFail($this->proyek_id)->update([
            'nama_proyek' => $this->nama_proyek,
            'customer_id' => $this->customer_id,
            'deskripsi' => $this->deskripsi,
            'lokasi' => $this->lokasi,
            'tanggal_mulai' => $this->tanggal_mulai,
            'tanggal_selesai' => $this->tanggal_selesai,
            'anggaran' => $this->anggaran,
            'status' => $this->status,
        ]);

        $this->loadData();

        $this->closeModal();
        session()->flash('success', 'Project updated successfully!');
        $this->dispatch('$refresh');
    }

    public function render()
    {
        return view('livewire.detail-proyek')
            ->layout('layouts.app');
    }
}
