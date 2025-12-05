<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Proyek;
use App\Models\ProyekCatatanPekerjaan;

class TimelineProyek extends Component
{
    public $proyekId;
    public $proyek;
    public $catatan = [];
    public $allDays = [];
    public $showModal = false;
    public $selectedCatatan = null;

    public function mount($proyekId)
    {
        $this->proyekId = $proyekId;
        $this->proyek   = Proyek::findOrFail($proyekId);

        $this->loadCatatan();
        $this->generateColumns();
    }

    /**
     * Ambil catatan dan isi tanggal_selesai jika null
     */
    public function loadCatatan()
    {
        $tanggalSelesaiProyek = $this->proyek->tanggal_selesai;

       $this->catatan = ProyekCatatanPekerjaan::with('fitur')
    ->where('proyek_id', $this->proyekId)
    ->orderBy('tanggal_mulai')
    ->get()
    ->map(function ($c) use ($tanggalSelesaiProyek) {
        if (!$c->tanggal_selesai) {
            $c->tanggal_selesai = $tanggalSelesaiProyek;
        }
        return $c;
    });

    }

    /**
     * Generate kolom HANYA antara tanggal_mulai → tanggal_selesai proyek
     */
    public function generateColumns()
    {
        $mulai   = \Carbon\Carbon::parse($this->proyek->tanggal_mulai)->startOfDay();
        $selesai = \Carbon\Carbon::parse($this->proyek->tanggal_selesai)->endOfDay();

        $period = new \DatePeriod(
            $mulai,
            new \DateInterval('P1D'),
            $selesai->copy()->addDay() // supaya termasuk tanggal_selesai
        );

        $this->allDays = collect();
        foreach ($period as $date) {
            $this->allDays->push($date->format('Y-m-d'));
        }
    }

    public function openModal($id)
    {
        $this->selectedCatatan = ProyekCatatanPekerjaan::with('fitur')->find($id);
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
    }


    public function render()
    {
        return view('livewire.timeline-proyek');
    }
}
