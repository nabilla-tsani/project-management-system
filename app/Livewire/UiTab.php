<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Proyek;
use App\Models\ProyekUser;
use Illuminate\Support\Facades\Auth;

class UiTab extends Component
{
    protected $listeners = [
        // allow child components to ask UiTab to switch active tab
        'switchTab' => 'setTab',
    ];
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
        
        // Restore tab from session (per-project) when available and allowed.
        $sessionKey = "proyek_tab_{$proyekId}";
        $stored = session()->get($sessionKey);

        // Build allowed tabs list depending on role
        $allowedTabs = ['informasi', 'team', 'fitur', 'file'];
        if ($this->isManajerProyek) {
            $allowedTabs = array_merge($allowedTabs, ['invoice', 'kwitansi']);
        }

        if ($stored && in_array($stored, $allowedTabs)) {
            $this->tab = $stored;
        }
    }

    public function setTab($tab)
    {
        // Validate requested tab against allowed tabs to avoid storing invalid/privileged tabs
        $allowedTabs = ['informasi', 'team', 'fitur', 'file'];
        if ($this->isManajerProyek) {
            $allowedTabs = array_merge($allowedTabs, ['invoice', 'kwitansi']);
        }

        if (! in_array($tab, $allowedTabs)) {
            return; // ignore invalid tab requests
        }

        $this->tab = $tab;

        // Persist to session per-project so refresh keeps the active tab
        if ($this->proyek && isset($this->proyek->id)) {
            session()->put("proyek_tab_{$this->proyek->id}", $tab);
        }
    }

    public function render()
    {
        return view('livewire.ui-tab', [
            'isManajerProyek' => $this->isManajerProyek,
        ])->layout('layouts.app'); 
    }
}
