<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Proyek;
use App\Models\ProyekUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class UiTab extends Component
{
    protected $listeners = ['switchTab' => 'setTab'];

    public $proyek;
    public $tab;
    public $isManajerProyek = false;

    public function mount($proyekId)
    {
        $this->proyek = Proyek::with('customer')->findOrFail($proyekId);

        // 🔹 Cek role user
        $pivot = ProyekUser::where('proyek_id', $proyekId)
            ->where('user_id', Auth::id())
            ->first();

        if ($pivot && strtolower($pivot->sebagai) === 'manajer proyek') {
            $this->isManajerProyek = true;
        }

        // 🔹 Gunakan session key yang unik per proyek
        $sessionKey = "proyek_tab_{$proyekId}";
        $lastVisitedProject = session('last_project_opened');

        /**
         * 🔥 LOGIKA UTAMA:
         * Jika user membuka proyek yang berbeda (atau dari luar halaman proyek),
         * maka reset tab ke "dashboard".
         * Tapi jika masih proyek yang sama (misal refresh),
         * maka gunakan tab terakhir dari session.
         */
        if ($lastVisitedProject != $proyekId) {
            // buka proyek baru → mulai dari dashboard
            $this->tab = 'dashboard';
            session()->put($sessionKey, 'dashboard');
        } else {
            // proyek yang sama → tetap gunakan tab terakhir
            $this->tab = session($sessionKey, 'dashboard');
        }

        // simpan proyek ini sebagai proyek terakhir yang dikunjungi
        session()->put('last_project_opened', $proyekId);
    }

    public function setTab($tab)
    {
        $allowedTabs = ['dashboard', 'informasi', 'team', 'fitur', 'tasks', 'timeline', 'file'];
        if ($this->isManajerProyek) {
            $allowedTabs = array_merge($allowedTabs, ['invoice', 'kwitansi']);
        }

        if (!in_array($tab, $allowedTabs)) return;

        $this->tab = $tab;

        // Simpan tab terakhir ke session
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
