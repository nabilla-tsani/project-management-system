<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\ProyekUser;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AllProyekUser extends Component
{
    public $proyekId;
    public $proyekUsers;
    public $users;
    public $user_id, $sebagai, $keterangan, $editId;
    public $showModal = false;
    public $search='';

    public $confirmDelete = false;
    public $deleteId = null;
    public $fiturTerlibat = [];


    protected $rules = [
        'user_id' => 'required|exists:users,id',
        'sebagai' => 'required|string|max:50',
        'keterangan' => 'nullable|string',
    ];

    public function mount($proyekId)
    {
        $this->proyekId = $proyekId;
        $this->users = User::whereNotNull('email_verified_at')
            ->orderBy('name')
            ->get();
                $this->loadProyekUsers();
    }

    public function loadProyekUsers()
    {
        $query = ProyekUser::with([
            'user',
            'fitur' => function ($q) {
                $q->select('proyek_fitur.id', 'proyek_fitur.nama_fitur', 'proyek_fitur.proyek_id')
                  ->where('proyek_fitur.proyek_id', $this->proyekId);
            }
        ])->where('proyek_id', $this->proyekId);

    }

    public function isManajerProyek(): bool
    {
        if (!Auth::check()) {
            return false;
        }

        return ProyekUser::where('proyek_id', $this->proyekId)
            ->where('user_id', Auth::id())
            ->whereRaw('LOWER(sebagai) = ?', ['manajer proyek'])
            ->exists();
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

        // Flash message
        session()->flash(
            'message',
            $this->editId ? 'Anggota berhasil diperbarui.' : 'Anggota berhasil ditambahkan.'
        );

        $this->showModal = false;
        $this->loadProyekUsers();
        $this->resetForm();
    }


    public function delete($id)
    {
        // Ambil data user & fitur terkait
        $proyekUser = ProyekUser::with('fitur')->findOrFail($id);

        $this->deleteId = $id;
        $this->fiturTerlibat = $proyekUser->fitur()
            ->where('proyek_id', $this->proyekId)
            ->pluck('nama_fitur')
            ->toArray();

        $this->confirmDelete = true;
    }


    public function confirmDeleteAction()
    {
        $proyekUser = ProyekUser::findOrFail($this->deleteId);

        $userId = $proyekUser->user_id;

        // Ambil semua fitur di proyek ini
        $fiturIds = \App\Models\ProyekFitur::where('proyek_id', $this->proyekId)
            ->pluck('id');

        // Hapus dependensi pivot fitur-user
        \DB::table('proyek_fitur_user')
            ->whereIn('proyek_fitur_id', $fiturIds)
            ->where('user_id', $userId)
            ->delete();

        // Hapus user dari proyek
        $proyekUser->delete();

        $this->confirmDelete = false;
        $this->deleteId = null;

        $this->loadProyekUsers();

        session()->flash('message', 'Anggota berhasil dihapus.');
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
        $search = strtolower($this->search);

        // 🔍 Mapping kata kunci agar "manager" mencocokkan "manajer"
        $keywordMap = [
            'manager' => 'manajer',
            'menejer' => 'manajer',
        ];

        if (array_key_exists($search, $keywordMap)) {
            $search = $keywordMap[$search];
        }

        $this->proyekUsers = ProyekUser::with([
            'user',
            'fitur' => function($q) {
                $q->where('proyek_id', $this->proyekId);
            }
        ])
            ->where('proyek_id', $this->proyekId)
            ->where(function ($query) use ($search) {
                $query
                    ->whereHas('user', function ($q) use ($search) {
                        $q->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"]);
                    })
                    ->orWhereRaw('LOWER(sebagai) LIKE ?', ["%{$search}%"]);
            })
            ->orderByRaw("
                CASE 
                    WHEN sebagai = 'manajer proyek' THEN 1
                    WHEN sebagai = 'programmer' THEN 2
                    WHEN sebagai = 'tester' THEN 3
                    ELSE 4
                END
            ")
            ->get();

        return view('livewire.all-proyek-user');
    }
}
