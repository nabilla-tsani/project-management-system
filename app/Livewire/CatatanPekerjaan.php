<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\ProyekCatatanPekerjaan;

class CatatanPekerjaan extends Component
{
    public $proyekFiturId; // ID fitur yang sedang dilihat
    public $catatan = [];  // data catatan pekerjaan
    public $modalOpen = false;
    public $catatanId = null;
    public $jenis;
    public $isiCatatan;

    protected $rules = [
        'jenis' => 'required|string|max:50',
        'isiCatatan' => 'required|string|max:1000',
    ];

    public function mount($proyekFiturId)
    {
        $this->proyekFiturId = $proyekFiturId;
        $this->loadCatatan();
    }

    public function loadCatatan()
    {
        $this->catatan = ProyekCatatanPekerjaan::where('proyek_fitur_id', $this->proyekFiturId)
            ->with('user')
            ->orderBy('id', 'desc')
            ->get();
    }

    public function openModal($id = null)
    {
        $this->resetValidation();
        $this->reset(['catatanId', 'jenis', 'isiCatatan']);

        if ($id) {
            $data = ProyekCatatanPekerjaan::findOrFail($id);
            $this->catatanId = $data->id;
            $this->jenis = $data->jenis;
            $this->isiCatatan = $data->catatan;
        }

        $this->modalOpen = true;
    }

    public function save()
    {
        $this->validate();

        ProyekCatatanPekerjaan::updateOrCreate(
            ['id' => $this->catatanId],
            [
                'proyek_fitur_id' => $this->proyekFiturId,
                'user_id' => auth()->id(),
                'jenis' => $this->jenis,
                'catatan' => $this->isiCatatan,
            ]
        );

        $this->modalOpen = false;
        $this->loadCatatan();
        session()->flash('message', $this->catatanId ? 'Catatan diperbarui' : 'Catatan ditambahkan');
    }

    public function delete($id)
    {
        ProyekCatatanPekerjaan::findOrFail($id)->delete();
        $this->loadCatatan();
        session()->flash('message', 'Catatan dihapus');
    }

    public function render()
    {
        return view('livewire.catatan-pekerjaan');
    }
}

