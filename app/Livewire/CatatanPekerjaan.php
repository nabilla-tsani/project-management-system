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
    public $filterJenis = '';

    public $alert = null;

    protected $listeners = [
        'openCatatanModal' => 'showModal',
        'hide-alert' => 'hideAlert'
    ];


    protected $rules = [
        'jenis' => 'required|string|max:50',
        'isiCatatan' => 'required|string|max:1000',
        'user_id' => 'required|exists:users,id',
    ];

    public function showModal($id)
    {
        $this->resetForm();

        $this->proyekFiturId = $id;

        $fitur = ProyekFitur::with('users')->find($id);
        $this->namaFitur = $fitur?->nama_fitur ?? 'Fitur Tidak Dikenal';

        // Ambil hanya user yang terlibat pada fitur
        $this->users = $fitur?->users ?? collect();

        $this->loadCatatan();
        $this->catatanModal = true;
    }

    public function loadCatatan()
    {
        $this->catatan = ProyekCatatanPekerjaan::where('proyek_fitur_id', $this->proyekFiturId)
            ->when($this->filterJenis, function($query) {
                // case-insensitive filter to handle stored capitalization differences
                $query->whereRaw('LOWER(jenis) = ?', [strtolower($this->filterJenis)]);
            })
            ->with('user')
            // order by most recently updated first so edited notes jump to the top
            ->orderByDesc('updated_at')
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

        // normalize jenis to lowercase before saving to keep data consistent
        $normalizedJenis = strtolower(trim($this->jenis));

        ProyekCatatanPekerjaan::updateOrCreate(
            ['id' => $this->catatanId],
            [
                'proyek_fitur_id' => $this->proyekFiturId,
                'user_id' => $this->user_id,
                'jenis' => $normalizedJenis,
                'catatan' => $this->isiCatatan,
            ]
        );

        // reflect normalized value back to the component property
        $this->jenis = $normalizedJenis;

       $message = $this->catatanId
        ? 'Updated successfully!'
        : 'Added successfully!';

    $this->resetForm(true);
    $this->loadCatatan();

    $this->showAlert($message, 'success');
    }

    public function edit($id)
    {
        $data = ProyekCatatanPekerjaan::findOrFail($id);
        $this->catatanId = $data->id;
        // normalize to lowercase for consistency with the form options
        $this->jenis = strtolower($data->jenis);
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

        // tampilkan alert sukses delete
        $this->showAlert('Deleted successfully!', 'success');
    }

    public function updatedFilterJenis($value)
    {
        // normalize incoming value and reload
        $this->filterJenis = $value ? strtolower($value) : '';
        $this->loadCatatan();
    }

    public function showAlert($message, $type = 'success')
    {
        $this->alert = [
            'type' => $type,
            'message' => $message,
        ];

        // kirim event browser agar Alpine tahu kapan harus menghilangkan alert
        $this->dispatch('alert-shown');
    }


    public function hideAlert()
    {
        $this->alert = null;
    }

    public function render()
    {
        $catatan = ProyekCatatanPekerjaan::with('user')
            ->where('proyek_fitur_id', $this->proyekFiturId) // ✅ batasi berdasarkan fitur aktif
            ->when($this->filterJenis, function($query) {
                // case-insensitive filter
                $query->whereRaw('LOWER(jenis) = ?', [strtolower($this->filterJenis)]);
            })
            // order by most recently updated first so new/edited notes appear at top
            ->orderByDesc('updated_at')
            ->get();

        return view('livewire.catatan-pekerjaan', [
            'catatan' => $catatan,
            'users' => $this->users, // gunakan user dari proyek aktif
        ]);
    }
}