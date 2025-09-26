<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Proyek;

class DetailProyek extends Component
{
    public $proyekId;
    public $proyek;

    // mount() dijalankan saat komponen di-load
    public function mount($id)
    {
        $this->proyekId = $id;
        $this->proyek = Proyek::findOrFail($id);
    }

    public function render()
    {
        return view('livewire.detail-proyek')
            ->layout('layouts.app'); // ✅ gunakan layout breeze
    }
}
