<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\ProyekCatatanPekerjaan;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AllProyekTasks extends Component
{
    public $proyekId;
    public $catatan = [];
    public $openModal = false;
    public $editId = null; // <--- ID untuk mode edit
    public $jenis = 'pekerjaan';
    public $catatanText = '';
    public $tanggal_mulai;
    public $tanggal_selesai;
    public $search = '';
    public $activeTab = 'fitur';
    public $isCreator = false;

    public function mount($proyekId)
    {
        $this->proyekId = $proyekId;
        $this->loadCatatan();
    }
    

    public function updatedSearch()
    {
        $this->loadCatatan();
    }

    public function loadCatatan()
    {
        $search = strtolower($this->search);

        $this->catatan = ProyekCatatanPekerjaan::with(['user', 'fitur'])
            
            // Filter proyek (TIDAK BOLEH KENA OR SEARCH)
            ->where(function ($q) {
                $q->whereHas('fitur', function($f) {
                    $f->where('proyek_id', $this->proyekId);
                })
                ->orWhere(function ($g) {
                    $g->whereNull('proyek_fitur_id')
                    ->where('proyek_id', $this->proyekId);
                });
            })

            // 🔍 SEARCH: harus tetap di dalam proyek yang sama
            ->when($this->search, function ($q) use ($search) {

                $q->where(function($q2) use ($search) {

                    // Nama fitur
                    $q2->whereHas('fitur', function($f) use ($search) {
                        $f->whereRaw('LOWER(nama_fitur) LIKE ?', ["%$search%"]);
                    })

                    // Nama user
                    ->orWhereHas('user', function($u) use ($search) {
                        $u->whereRaw('LOWER(name) LIKE ?', ["%$search%"]);
                    })

                    // Isi catatan
                    ->orWhereRaw('LOWER(catatan) LIKE ?', ["%$search%"]);
                });
            })

            ->orderByDesc('updated_at')
            ->get();
}

    public function showModal()
    {
        $this->resetForm();
        $this->openModal = true;
    }

    public function resetForm()
    {
        $this->reset(['editId', 'jenis', 'catatanText', 'tanggal_mulai', 'tanggal_selesai']);
    }


    public function save()
    {
        $this->validate([
            'jenis' => 'required',
            'catatanText' => 'required|string',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
        ]);

        ProyekCatatanPekerjaan::create([
            'proyek_id' => $this->proyekId,
            'user_id' => Auth::id(),
            'jenis' => $this->jenis,
            'catatan' => $this->catatanText,
            'tanggal_mulai' => $this->tanggal_mulai,
            'tanggal_selesai' => $this->tanggal_selesai,
        ]);

        session()->flash('success', 'Catatan berhasil ditambahkan.');
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
        $this->tanggal_mulai = $task->tanggal_mulai ? $task->tanggal_mulai->format('Y-m-d') : null;
        $this->tanggal_selesai = $task->tanggal_selesai ? $task->tanggal_selesai->format('Y-m-d') : null;
    }


    public function update()
    {
        $this->validate([
            'jenis' => 'required',
            'catatanText' => 'required|string',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
        ]);

        ProyekCatatanPekerjaan::where('id', $this->editId)->update([
            'proyek_id' => $this->proyekId,
            'user_id' => Auth::id(),
            'jenis'   => $this->jenis,
            'catatan' => $this->catatanText,
            'tanggal_mulai' => $this->tanggal_mulai,
            'tanggal_selesai' => $this->tanggal_selesai,
        ]);

        session()->flash('success', 'Catatan berhasil diperbarui.');
        $this->openModal = false;
        $this->loadCatatan();
    }

    public function delete($id)
    {
        ProyekCatatanPekerjaan::findOrFail($id)->delete();

        session()->flash('success', 'Catatan berhasil dihapus.');
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
