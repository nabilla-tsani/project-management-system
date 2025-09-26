<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\ProyekUser;
use App\Models\User;

class AllProyekUser extends Component
{
    public $proyekId;
    public $proyekUsers;
    public $users;
    public $user_id, $sebagai, $keterangan, $editId;
    public $showModal = false;

    protected $rules = [
        'user_id' => 'required|exists:users,id',
        'sebagai' => 'required|string|max:50',
        'keterangan' => 'nullable|string',
    ];

    public function mount($proyekId)
    {
        $this->proyekId = $proyekId;
        $this->users = \App\Models\User::all(); // untuk dropdown
        $this->loadProyekUsers();
    }

    public function loadProyekUsers()
    {
        $this->proyekUsers = ProyekUser::with('user')
            ->where('proyek_id', $this->proyekId)
            ->get();
    }

    public function openModal($id = null)
    {
        $this->resetForm();
        if ($id) {
            $proyekUser = ProyekUser::findOrFail($id);
            $this->editId = $id;
            $this->user_id = $proyekUser->user_id;
            $this->sebagai = $proyekUser->sebagai;
            $this->keterangan = $proyekUser->keterangan;
        }
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        ProyekUser::updateOrCreate(
            ['id' => $this->editId],
            [
                'proyek_id' => $this->proyekId,
                'user_id' => $this->user_id,
                'sebagai' => $this->sebagai,
                'keterangan' => $this->keterangan,
            ]
        );

        $this->showModal = false;
        $this->loadProyekUsers();
        session()->flash('message', $this->editId ? 'Data diperbarui.' : 'Data ditambahkan.');
        $this->resetForm();
    }

    public function delete($id)
    {
        ProyekUser::findOrFail($id)->delete();
        $this->loadProyekUsers();
        session()->flash('message', 'Data dihapus.');
    }

    public function resetForm()
    {
        $this->editId = null;
        $this->user_id = '';
        $this->sebagai = '';
        $this->keterangan = '';
    }

    public function render()
    {
        return view('livewire.all-proyek-user');
    }
}
