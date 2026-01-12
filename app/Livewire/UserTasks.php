<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\ProyekCatatanPekerjaan;
use Illuminate\Support\Facades\Auth;

class UserTasks extends Component
{
    public $tasks = [];
    public $search = '';
    public $filter = 'all'; // all | pekerjaan | bug | tambahan

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

    public function setFilter($filter)
    {
        $this->filter = $filter;
        $this->loadTasks();
    }

    public function loadTasks()
    {
        $search = strtolower($this->search);

        $query = ProyekCatatanPekerjaan::with(['proyek', 'fitur'])
            ->where('user_id', Auth::id())

            // 🔍 SEARCH: NAMA PROYEK ATAU ISI CATATAN
        ->when($this->search, function ($q) use ($search) {
            $q->where(function ($sub) use ($search) {

                // Cari di nama proyek
                $sub->whereHas('proyek', function ($p) use ($search) {
                    $p->whereRaw('LOWER(nama_proyek) LIKE ?', ['%' . $search . '%']);
                });

                // ATAU cari di isi catatan
                $sub->orWhereRaw('LOWER(catatan) LIKE ?', ['%' . $search . '%']);
            });
        })

            // 🧩 FILTER LOGIC
            ->when($this->filter === 'bug', function ($q) {
                $q->where('jenis', 'bug');
            })

            ->when($this->filter === 'pekerjaan', function ($q) {
                $q->where('jenis', 'pekerjaan')
                  ->whereNotNull('proyek_fitur_id');
            })

            ->when($this->filter === 'tambahan', function ($q) {
                $q->where('jenis', 'pekerjaan')
                  ->whereNull('proyek_fitur_id');
            })

            ->latest();

        $this->tasks = $query->get();
    }

    public function render()
    {
        return view('livewire.user-tasks');
    }
}
