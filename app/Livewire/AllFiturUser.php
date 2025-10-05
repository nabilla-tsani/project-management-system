<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\ProyekFiturUser;
use App\Models\ProyekFitur;
use App\Models\ProyekUser;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AllFiturUser extends Component
{
    public $proyekFiturId;
    public $fiturUsers = [];
    public $userList = [];
    public $modalOpen = false;
    public $fiturUserId = null;
    public $user_id = null;
    public $keterangan = null;
    public $isEdit = false;
    public $isManager = false; // <-- Tambahkan flag

    protected $rules = [
        'user_id' => 'required|exists:users,id',
        'keterangan' => 'nullable|string|max:255',
    ];

    public function mount($proyekFiturId)
    {
        $this->proyekFiturId = $proyekFiturId;
        $this->checkIfManager();
        $this->loadData();
    }

    protected function checkIfManager()
    {
        $fitur = ProyekFitur::findOrFail($this->proyekFiturId);

        $this->isManager = ProyekUser::where('proyek_id', $fitur->proyek_id)
            ->where('user_id', Auth::id())
            ->where('sebagai', 'manajer proyek')
            ->exists();
    }

    public function loadData()
    {
        $this->fiturUsers = ProyekFiturUser::with('user')
            ->where('proyek_fitur_id', $this->proyekFiturId)
            ->orderBy('id', 'desc')
            ->get();

        $this->userList = User::select('id', 'name')
            ->orderBy('name')
            ->get();
    }

    public function openModal($id = null)
    {
        if (!$this->isManager) return; // Hanya manajer
        $this->resetForm();

        if ($id) {
            $this->edit($id);
            return;
        }

        $this->modalOpen = true;
    }

    public function edit($id)
    {
        if (!$this->isManager) return; // Hanya manajer

        $data = ProyekFiturUser::findOrFail($id);
        $this->fiturUserId = $data->id;
        $this->user_id = $data->user_id;
        $this->keterangan = $data->keterangan;
        $this->isEdit = true;
        $this->modalOpen = true;
    }

    public function save()
    {
        if (!$this->isManager) return; // Hanya manajer

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

        $this->closeModal();
        $this->loadData();
        $this->dispatch('refresh-fitur-users');
    }

    public function delete($id)
    {
        if (!$this->isManager) return; // Hanya manajer
        ProyekFiturUser::findOrFail($id)->delete();
        $this->loadData();
        $this->dispatch('refresh-fitur-users');
    }

    public function resetForm()
    {
        $this->fiturUserId = null;
        $this->user_id = null;
        $this->keterangan = null;
        $this->isEdit = false;
        $this->resetValidation();
    }

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
