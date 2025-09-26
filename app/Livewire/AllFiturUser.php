<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\ProyekFiturUser;
use App\Models\User;
use App\Models\ProyekFitur;

class AllFiturUser extends Component
{
    // id fitur yang sedang dilihat
    public $proyekFiturId;

    // data
    public $fiturUsers = [];
    public $userList = [];

    // modal + form
    public $modalOpen = false;
    public $fiturUserId = null; // id dari tabel proyek_fitur_user (edit)
    public $user_id = null;
    public $keterangan = null;
    public $isEdit = false;

    protected $rules = [
        'user_id' => 'required|exists:users,id',
        'keterangan' => 'nullable|string|max:255',
    ];

    public function mount($proyekFiturId)
    {
        $this->proyekFiturId = $proyekFiturId;
        $this->loadData();
    }

    // load daftar user yang terkait dan daftar user untuk dropdown
    // public function loadData()
    // {
    //     $this->fiturUsers = ProyekFiturUser::with('user')
    //         ->where('proyek_fitur_id', $this->proyekFiturId)
    //         ->orderBy('id', 'desc')
    //         ->get();

    //     $this->userList = User::select('id', 'name')
    //         ->orderBy('name')
    //         ->get();
    // }

    public function loadData()
{
    // Ambil semua user yang sudah diassign ke fitur ini
    $this->fiturUsers = ProyekFiturUser::with('user')
        ->where('proyek_fitur_id', $this->proyekFiturId)
        ->orderBy('id', 'desc')
        ->get();

    // Ambil fitur + proyek untuk tahu proyek_id
    $fitur = ProyekFitur::with('proyek')->find($this->proyekFiturId);

    if ($fitur && $fitur->proyek) {
        // Ambil user yang terdaftar di proyek ini via tabel proyek_user
        $this->userList = $fitur->proyek->users()
            ->whereNotIn('users.id', ProyekFiturUser::where('proyek_fitur_id', $this->proyekFiturId)
                ->pluck('user_id'))
            ->select('users.id', 'users.name')
            ->orderBy('users.name')
            ->get();
    } else {
        $this->userList = collect();
    }
}


    // reset input form
    public function resetForm()
    {
        $this->fiturUserId = null;
        $this->user_id = null;
        $this->keterangan = null;
        $this->isEdit = false;
        $this->resetValidation();
    }

    // buka modal (tambah). Kalau param id diberikan, langsung panggil edit()
    public function openModal($id = null)
    {
        $this->resetForm();

        if ($id) {
            $this->edit($id);
            return;
        }

        $this->modalOpen = true;
    }

    // edit (isi form lalu buka modal)
    public function edit($id)
    {
        $data = ProyekFiturUser::findOrFail($id);
        $this->fiturUserId = $data->id;
        $this->user_id = $data->user_id;
        $this->keterangan = $data->keterangan;
        $this->isEdit = true;
        $this->modalOpen = true;
    }

    // simpan (create atau update)
    public function save()
    {
        $this->validate();

        ProyekFiturUser::updateOrCreate(
            ['id' => $this->fiturUserId],
            [
                'proyek_fitur_id' => $this->proyekFiturId,
                'user_id' => $this->user_id,
                'keterangan' => $this->keterangan,
            ]
        );

        session()->flash('message', $this->fiturUserId ? 'User fitur diperbarui.' : 'User fitur ditambahkan.');

        $this->closeModal();
        $this->loadData();

        // beri tahu parent kalau diperlukan (opsional)
        $this->dispatch('refresh-fitur-users');    }

    // hapus
    public function delete($id)
    {
        ProyekFiturUser::findOrFail($id)->delete();
        session()->flash('message', 'User fitur dihapus.');
        $this->loadData();
        $this->dispatch('refresh-fitur-users');    }

    public function closeModal()
    {
        $this->resetForm();
        $this->modalOpen = false;
    }

    public function render()
    {
        return view('livewire.all-fitur-user');
    }
}
