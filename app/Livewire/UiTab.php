<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Proyek;
use App\Models\ProyekUser;
use Illuminate\Support\Facades\Auth;

class UiTab extends Component
{
    protected $listeners = [
        'switchTab' => 'setTab',
    ];

    public $proyek;
    public $tab = 'informasi';
    public $isManajerProyek = false;

    public function mount($proyekId)
    {
        $this->proyek = Proyek::with('customer')->findOrFail($proyekId);

        // Cek role user pada proyek
        $pivot = ProyekUser::where('proyek_id', $proyekId)
            ->where('user_id', Auth::id())
            ->first();

        if ($pivot && strtolower($pivot->sebagai) === 'manajer proyek') {
            $this->isManajerProyek = true;
        }

        // Ambil tab terakhir dari session
        $sessionKey = "proyek_tab_{$proyekId}";
        $stored = session()->get($sessionKey);

        // ✅ Tambahkan 'timeline' ke daftar tab
        $allowedTabs = ['informasi', 'dashboard', 'team', 'fitur', 'tasks', 'file', 'timeline'];
        if ($this->isManajerProyek) {
            $allowedTabs = array_merge($allowedTabs, ['invoice', 'kwitansi']);
        }

        if ($stored && in_array($stored, $allowedTabs)) {
            $this->tab = $stored;
        }
    }

    public function setTab($tab)
    {
        // Validasi tab
        $allowedTabs = ['informasi', 'dashboard', 'team', 'fitur', 'tasks', 'file', 'timeline'];
        if ($this->isManajerProyek) {
            $allowedTabs = array_merge($allowedTabs, ['invoice', 'kwitansi']);
        }

        if (! in_array($tab, $allowedTabs)) {
            return;
        }

        $this->tab = $tab;

        // Simpan ke session agar tidak hilang saat refresh
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
