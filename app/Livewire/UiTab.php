<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Proyek;
use App\Models\ProyekUser;
use Illuminate\Support\Facades\Auth;

class UiTab extends Component
{
    public $proyek;
    public $tab = 'informasi'; // default tab
    public $isManajerProyek = false; // penanda role user

    public function mount($proyekId)
    {
        $this->proyek = Proyek::with('customer')->findOrFail($proyekId);

        // Cek apakah user login memiliki role "manajer proyek"
        $pivot = ProyekUser::where('proyek_id', $proyekId)
            ->where('user_id', Auth::id())
            ->first();

        if ($pivot && strtolower($pivot->sebagai) === 'manajer proyek') {
            $this->isManajerProyek = true;
        }
    }

    public function setTab($tab)
    {
        $this->tab = $tab;
    }

    public function render()
    {
        return view('livewire.ui-tab', [
            'isManajerProyek' => $this->isManajerProyek,
        ])->layout('layouts.app'); 
    }
}
