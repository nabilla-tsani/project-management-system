<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\ProyekCatatanPekerjaan;
use App\Models\User;

class AllProyekTasks extends Component
{
    public $proyekId;
    public $catatan = [];

    public $openModal = false;

    public $editId = null; // <--- ID untuk mode edit

    public $jenis = 'pekerjaan';
    public $catatanText = '';

    public $selectedUser = null;
    public $users = [];

    public function mount($proyekId)
    {
        $this->proyekId = $proyekId;
        $this->loadCatatan();
    }

    public function loadCatatan()
    {
        $this->catatan = ProyekCatatanPekerjaan::where(function($q) {

                // Catatan yang punya fitur → filter berdasarkan proyek fitur
                $q->whereHas('fitur', function($f) {
                    $f->where('proyek_id', $this->proyekId);
                })

                // General notes → harus sesuai proyek_id
                ->orWhere(function ($g) {
                    $g->whereNull('proyek_fitur_id')
                    ->where('proyek_id', $this->proyekId);
                });

            })
            ->with(['user', 'fitur'])
            ->orderByDesc('updated_at')
            ->get();
    }

    public function showModal()
    {
        $this->resetForm();
        $this->openModal = true;

        // load user
        $this->users = User::whereHas('proyeks', function($q) {
            $q->where('proyek_id', $this->proyekId);
        })->get();
    }

    public function resetForm()
    {
        $this->reset(['editId', 'jenis', 'catatanText', 'selectedUser']);
    }


    public function save()
    {
        $this->validate([
            'selectedUser' => 'required',
            'jenis' => 'required',
            'catatanText' => 'required|string',
        ]);

        ProyekCatatanPekerjaan::create([
            'proyek_id' => $this->proyekId,
            'user_id' => $this->selectedUser,
            'jenis' => $this->jenis,
            'catatan' => $this->catatanText,
        ]);

        session()->flash('success', 'Task successfully created.');
        $this->openModal = false;
        $this->loadCatatan();
    }

    public function edit($id)
    {
        $this->resetForm();
        $this->openModal = true;

        $task = ProyekCatatanPekerjaan::findOrFail($id);

        $this->editId = $id;
        $this->jenis = $task->jenis;
        $this->catatanText = $task->catatan;
        $this->selectedUser = $task->user_id;

        // Load user
        $this->users = User::whereHas('proyeks', function($q) {
            $q->where('proyek_id', $this->proyekId);
        })->get();
    }


    public function update()
    {
        $this->validate([
            'selectedUser' => 'required',
            'jenis' => 'required',
            'catatanText' => 'required|string',
        ]);

        ProyekCatatanPekerjaan::where('id', $this->editId)->update([
            'proyek_id' => $this->proyekId,
            'user_id' => $this->selectedUser,
            'jenis'   => $this->jenis,
            'catatan' => $this->catatanText,
        ]);

        session()->flash('success', 'Task successfully updated.');
        $this->openModal = false;
        $this->loadCatatan();
    }

    public function delete($id)
    {
        ProyekCatatanPekerjaan::findOrFail($id)->delete();
        
        session()->flash('success', 'Task successfully deleted.');
        $this->loadCatatan();
    }


    public function render()
    {
        return view('livewire.all-proyek-tasks', [
            'catatanNonFitur' => $this->catatan->whereNull('proyek_fitur_id'),
            'catatanFitur' => $this->catatan->whereNotNull('proyek_fitur_id'),
        ]);
    }
}
