<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\ProyekFitur;
use App\Models\ProyekUser;
use Illuminate\Support\Facades\Auth;

class AllProyekFitur extends Component
{
    public $proyekId;
    public $fiturs;

    // Form fields
    public $fiturId;
    public $nama_fitur;
    public $keterangan;
    public $status_fitur;

    // Modal states
    public $modalOpen = false;
    public $userModalOpen = false;

    // Dropdown & user modal
    public $statusList = ['Pending', 'In Progress', 'Done'];
    public $selectedFiturId;
    public $selectedFitur;

    public $showCatatan = []; // array untuk menyimpan state tiap fitur

    // Role flag
    public $isManajerProyek = false;

    public function mount($proyekId)
    {
        $this->proyekId = $proyekId;

        // Cek apakah user saat ini adalah manajer proyek
        $pivot = ProyekUser::where('proyek_id', $proyekId)
            ->where('user_id', Auth::id())
            ->first();

        if ($pivot && strtolower($pivot->sebagai) === 'manajer proyek') {
            $this->isManajerProyek = true;
        }

        $this->loadFitur();
    }

    public function loadFitur()
    {
        $this->fiturs = ProyekFitur::with(['anggota.user'])
            ->where('proyek_id', $this->proyekId)
            ->orderBy('id', 'desc')
            ->get();
    }

    public function openModal($id = null)
    {
        $this->resetValidation();
        $this->reset(['fiturId', 'nama_fitur', 'keterangan', 'status_fitur']);

        if ($id) {
            $fitur = ProyekFitur::findOrFail($id);
            $this->fiturId = $fitur->id;
            $this->nama_fitur = $fitur->nama_fitur;
            $this->keterangan = $fitur->keterangan;
            $this->status_fitur = $fitur->status_fitur;
        }

        $this->modalOpen = true;
    }

    public function save()
    {
        $this->validate([
            'nama_fitur'   => 'required|string|max:255',
            'keterangan'   => 'nullable|string|max:1000',
            'status_fitur' => 'required|string|in:Pending,In Progress,Done',
        ]);

        ProyekFitur::updateOrCreate(
            ['id' => $this->fiturId],
            [
                'proyek_id'   => $this->proyekId,
                'nama_fitur'  => $this->nama_fitur,
                'keterangan'  => $this->keterangan,
                'status_fitur'=> $this->status_fitur,
            ]
        );

        $this->closeModal();
        $this->loadFitur();
        
    }

    public function delete($id)
    {
        ProyekFitur::findOrFail($id)->delete();
        $this->loadFitur();
    }

    public function closeModal()
    {
        $this->reset(['modalOpen', 'fiturId', 'nama_fitur', 'keterangan', 'status_fitur']);
        $this->resetValidation();
    }

    public function openUserModal($fiturId)
    {
        $this->selectedFiturId = $fiturId;
        $this->selectedFitur   = ProyekFitur::findOrFail($fiturId);
        $this->userModalOpen   = true;
    }

    public function closeUserModal()
    {
        $this->reset(['userModalOpen', 'selectedFiturId', 'selectedFitur']);
    }

    public function toggleCatatan($fiturId)
    {
        if (isset($this->showCatatan[$fiturId])) {
            $this->showCatatan[$fiturId] = !$this->showCatatan[$fiturId];
        } else {
            $this->showCatatan[$fiturId] = true;
        }
    }

    public function render()
    {
        return view('livewire.all-proyek-fitur', [
            'isManajerProyek' => $this->isManajerProyek,
        ]);
    }
}
