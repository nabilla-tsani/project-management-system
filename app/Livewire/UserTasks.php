<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\ProyekCatatanPekerjaan;
use Illuminate\Support\Facades\Auth;

class UserTasks extends Component
{
    public $tasks = [];
    public $search = '';
    public $filter = 'nearest'; // default: deadline terdekat

    public function mount()
    {
        $this->loadTasks();
    }

    public function updatedSearch()
    {
        $this->loadTasks();
    }

    public function updatedFilter()
    {
        $this->loadTasks();
    }

    public function loadTasks()
    {
        $search = strtolower($this->search);

        $query = ProyekCatatanPekerjaan::with(['proyek', 'fitur'])
            ->where('user_id', Auth::id())
            ->when($this->search, function ($q) use ($search) {
                $q->whereHas('proyek', function ($p) use ($search) {
                    $p->whereRaw('LOWER(nama_proyek) LIKE ?', ['%' . $search . '%']);
                });
            });

        // ⬇️ Tambahkan filter sorting
        switch ($this->filter) {
            case 'nearest':
                $query->orderBy('tanggal_selesai', 'asc');
                break;

            case 'farthest':
                $query->orderBy('tanggal_selesai', 'desc');
                break;

            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;

            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;

            default:
                $query->orderBy('tanggal_selesai', 'asc');
                break;
        }

        $this->tasks = $query->get();
    }

    public function render()
    {
        return view('livewire.user-tasks');
    }
}
