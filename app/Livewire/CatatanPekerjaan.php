<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\Proyek;
use App\Models\ProyekFitur;
use App\Models\ProyekCatatanPekerjaan;

class CatatanPekerjaan extends Component
{
    public $proyekFiturId;
    public $catatan = [];
    public $users = [];
    public $catatanModal = false;
    public $catatanId = null;
    public $jenis = '';
    public $isiCatatan = '';
    public $user_id = '';
    public $namaFitur;
    public $formKey = 'form_default';

    protected $listeners = ['openCatatanModal' => 'showModal'];

    protected $rules = [
        'jenis' => 'required|string|max:50',
        'isiCatatan' => 'required|string|max:1000',
        'user_id' => 'required|exists:users,id',
    ];

    public function showModal($id)
    {
        $this->resetForm();
        $this->proyekFiturId = $id;

        $fitur = ProyekFitur::find($id);
        $this->namaFitur = $fitur?->nama_fitur ?? 'Fitur Tidak Dikenal';

        // ✅ Ambil hanya user yang terlibat dalam proyek fitur ini
        if ($fitur && $fitur->proyek_id) {
            $proyek = Proyek::with('users')->find($fitur->proyek_id);
            $this->users = $proyek?->users ?? collect();
        } else {
            $this->users = collect();
        }

        $this->loadCatatan();
        $this->catatanModal = true;
    }

    public function loadCatatan()
    {
        $this->catatan = ProyekCatatanPekerjaan::where('proyek_fitur_id', $this->proyekFiturId)
            ->with('user')
            ->orderByDesc('id')
            ->get();
    }

    public function closeModal()
    {
        $this->catatanModal = false;
        $this->resetForm();
    }

    public function resetForm($newKey = true)
    {
        $this->reset(['catatanId', 'jenis', 'isiCatatan', 'user_id']);
        $this->resetValidation();
        if ($newKey) {
            $this->formKey = uniqid('form_', true);
        }
    }

    public function save()
    {
        $this->validate();

        ProyekCatatanPekerjaan::updateOrCreate(
            ['id' => $this->catatanId],
            [
                'proyek_fitur_id' => $this->proyekFiturId,
                'user_id' => $this->user_id,
                'jenis' => $this->jenis,
                'catatan' => $this->isiCatatan,
            ]
        );

        $this->resetForm(true);
        $this->loadCatatan();
        $this->dispatch('catatan-saved');
    }

    public function edit($id)
    {
        $data = ProyekCatatanPekerjaan::findOrFail($id);
        $this->catatanId = $data->id;
        $this->jenis = $data->jenis;
        $this->isiCatatan = $data->catatan;
        $this->user_id = $data->user_id;
        $this->formKey = 'form_edit_' . $data->id;
    }

    public function cancelEdit()
    {
        $this->resetForm(true);
    }

    public function delete($id)
    {
        ProyekCatatanPekerjaan::findOrFail($id)->delete();
        $this->loadCatatan();
    }

    public function render()
    {
        return view('livewire.catatan-pekerjaan');
    }
}
