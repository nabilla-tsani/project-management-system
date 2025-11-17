<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\ProyekFiturUser;
use App\Models\ProyekFitur;
use App\Models\ProyekUser;
use App\Models\User;
use App\Models\Proyek;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;

class AllFiturUser extends Component
{
    public $proyekFiturId;
    public $fiturUsers;
    public $userList = [];
    public $modalOpen = false;
    public $fiturUserId = null;
    public $user_id = null;
    public $keterangan = null;
    public $isEdit = false;
    public $namaFitur;
    public $formKey;

    protected $listeners = [
        'openUserFiturModal' => 'showModal'
    ];

    protected $rules = [
        'user_id' => 'required|exists:users,id',
        'keterangan' => 'nullable|string|max:255',
    ];

    public function mount()
    {
        $this->fiturUsers = collect();
        $this->formKey = uniqid('form_', true);
    }

    public function loadFiturUsers()
    {
        $this->fiturUsers = ProyekFiturUser::where('proyek_fitur_id', $this->proyekFiturId)
            ->with('user')
            ->orderBy('id', 'asc')
            ->get();
    }

    private function loadAvailableUsers()
    {
        // Ambil ID user yang sudah menjadi anggota fitur ini
        $existingUserIds = ProyekFiturUser::where('proyek_fitur_id', $this->proyekFiturId)
            ->pluck('user_id')
            ->toArray();

        // Pada mode edit, user yang sedang diedit TIDAK boleh dihilangkan
        if ($this->isEdit && $this->user_id) {
            $existingUserIds = array_filter($existingUserIds, fn($id) => $id != $this->user_id);
        }

        // Ambil user yang BELUM ada di existingUserIds
        $this->userList = User::whereNotIn('id', $existingUserIds)
            ->orderBy('name')
            ->get();

        // Pada edit, jika user yang diedit hilang, tambahkan kembali ke list
        if ($this->isEdit && $this->user_id) {
            $editingUser = User::find($this->user_id);
            if ($editingUser) {
                $this->userList->prepend($editingUser);
            }
        }
    }


    public function showModal($id)
    {
        $this->resetForm();
        $this->resetErrorBag();
        $this->resetValidation();

        $this->proyekFiturId = $id;

        // Ambil fitur
        $fitur = ProyekFitur::find($id);

        $this->namaFitur = $fitur?->nama_fitur ?? 'Fitur Tidak Dikenal';

        // Ambil seluruh user
        $this->userList = User::orderBy('name')->get();

        // Ambil user-user anggota fitur
        $this->loadFiturUsers();
        $this->loadAvailableUsers();

        // Buka modal
        $this->modalOpen = true;
    }

    public function edit($id)
    {
        $data = ProyekFiturUser::findOrFail($id);

        // Pastikan kita tahu fitur & proyek dulu
        $this->proyekFiturId = $data->proyek_fitur_id;
        $fitur = ProyekFitur::find($this->proyekFiturId);
        $this->namaFitur = $fitur?->nama_fitur ?? $this->namaFitur;

        // Load user list dari proyek (biasanya returning User models)
        if ($fitur && $fitur->proyek_id) {
            $this->userList = User::select('id', 'name')
                ->orderBy('name')
                ->get();
        } else {
            $this->userList = collect();
        }

        // Isi form
        $this->fiturUserId = $data->id;
        $this->user_id = $data->user_id;
        $this->keterangan = $data->keterangan;
        $this->isEdit = true;

        // Jika user yang akan dipilih TIDAK ADA dalam userList, tambahkan user tersebut
        if (!$this->userList->contains('id', $this->user_id)) {
            $user = \App\Models\User::find($this->user_id);
            if ($user) {
                // prepend agar terlihat di atas
                $this->userList->prepend($user);
            }
        }
        $this->loadAvailableUsers();

        $this->resetErrorBag();
        $this->resetValidation();

        $this->modalOpen = true;
    }

    public function cancelEdit()
    {
        $this->resetForm();
        $this->isEdit = false;
    }

    public function save()
    {
        $this->validate();

        $fitur = ProyekFitur::findOrFail($this->proyekFiturId);
        $proyekId = $fitur->proyek_id;

        $cekProyekUser = ProyekUser::where('proyek_id', $proyekId)
            ->where('user_id', $this->user_id)
            ->first();

        if (!$cekProyekUser) {
            ProyekUser::create([
                'proyek_id' => $proyekId,
                'user_id' => $this->user_id,
                'sebagai' => 'programmer',
                'keterangan' => null,
            ]);
        }

        ProyekFiturUser::updateOrCreate(
            ['id' => $this->fiturUserId],
            [
                'proyek_fitur_id' => $this->proyekFiturId,
                'user_id' => $this->user_id,
                'keterangan' => $this->keterangan,
            ]
        );

        $this->loadFiturUsers();

        $this->resetForm();
        $this->loadAvailableUsers();

        session()->flash(
            'message',
            $this->isEdit 
                ? 'User updated successfully.' 
                : 'User added to feature successfully.'
        );

        $this->dispatch('$refresh');
    }

    public function delete($id)
    {
        ProyekFiturUser::findOrFail($id)->delete();

        session()->flash('message', 'User removed from feature successfully.');

        $this->loadFiturUsers();
        $this->loadAvailableUsers();
        $this->dispatch('$refresh');
    }



    public function resetForm($newKey = true)
    {
        $this->fiturUserId = null;
        $this->user_id = null;
        $this->keterangan = null;
        $this->isEdit = false;

        $this->resetValidation();
        $this->resetErrorBag();

        if ($newKey) {
            $this->formKey = uniqid('form_', true);
        }
    }

    public function closeModal()
    {
        $this->resetForm();
        $this->modalOpen = false;

        $this->dispatch('reloadPage');
    }

    public function render()
    {
        return view('livewire.all-fitur-user');
    }
}
