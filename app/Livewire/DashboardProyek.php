<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Proyek;
use App\Models\ProyekFitur;
use Carbon\Carbon;

class DashboardProyek extends Component
{
    public $proyekId;
    public $proyek;

    public $sisaHari;
    public $sisaBulan;
    public $sisaHariDetail;

    // === Member Counts ===
    public $totalProgrammer = 0;
    public $totalTester = 0;
    public $totalManajer = 0;

    // === Fitur Progress ===
    public $totalFitur = 0;
    public $fiturSelesai = 0;
    public $fiturBerjalan = 0;
    public $fiturPending = 0;
    public $fiturUpcoming = 0;
    public $fiturOverdue = 0;
    public $fiturTerbaru = [];
    public $progressComplete = 0;
    public $progressUncomplete = 0;

    // === Catatan Terbaru ===
    public $catatanTerbaru = [];


    public function mount($proyekId)
    {
        $this->proyek = Proyek::with(['customer', 'proyekUsers'])->findOrFail($proyekId);

        $this->hitungSisaWaktu();
        $this->hitungMember();
        $this->hitungProgressFitur();
        $this->ambilCatatanTerbaru();
    }

    /**
     * Hitung jumlah member berdasarkan role
     */
    private function hitungMember()
    {
        $users = $this->proyek->proyekUsers;

        $this->totalProgrammer = $users->where('sebagai', 'programmer')->count();
        $this->totalTester     = $users->where('sebagai', 'tester')->count();
        $this->totalManajer    = $users->where('sebagai', 'manajer proyek')->count();
    }

    /**
     * Hitung progress fitur pada proyek
     */
    private function hitungProgressFitur()
    {
        $fitur = ProyekFitur::where('proyek_id', $this->proyek->id)->get();
        $today = Carbon::now('Asia/Jakarta')->startOfDay();

        $this->totalFitur = $fitur->count();

        $this->fiturSelesai  = $fitur->where('status_fitur', 'Done')->count();
        $this->fiturBerjalan = $fitur->where('status_fitur', 'In Progress')->count();
        $this->fiturPending  = $fitur->where('status_fitur', 'Pending')->count();
        $this->fiturUpcoming = $fitur->where('status_fitur', 'Upcoming')->count();

        // === OVERDUE ===
        $this->fiturOverdue = $fitur->filter(function ($f) use ($today) {

            if (!$f->target) return false;

            try {
                $target = Carbon::createFromFormat('Y-m-d', trim($f->target), 'Asia/Jakarta')
                                ->startOfDay();
            } catch (\Exception $e) {
                return false;
            }

            return strtolower($f->status_fitur) === 'in progress'
                && $target->lt($today);
        })->count();

        // === Hilangkan overdue dari In Progress ===
        $this->fiturBerjalan -= $this->fiturOverdue;
        if ($this->fiturBerjalan < 0) $this->fiturBerjalan = 0;

        // Grafik progress ring
        $this->progressComplete   = $this->fiturSelesai;
        $this->progressUncomplete = max(0, $this->totalFitur - $this->fiturSelesai);

        $this->fiturTerbaru = ProyekFitur::where('proyek_id', $this->proyek->id)
        ->orderBy('created_at', 'desc')
        ->take(3)
        ->get();

    }

    /**
     * Hitung sisa waktu
     */
    private function hitungSisaWaktu()
    {
        if (!$this->proyek->tanggal_selesai) {
            $this->sisaHari = null;
            return;
        }

        $today = Carbon::now();
        $deadline = Carbon::parse($this->proyek->tanggal_selesai);

        $this->sisaHari = (int) $today->startOfDay()->diffInDays($deadline->startOfDay(), false);

        if ($this->sisaHari > 30) {
            $diff = $today->diff($deadline);
            $this->sisaBulan = $diff->m;
            $this->sisaHariDetail = $diff->d;
        } else {
            $this->sisaBulan = null;
            $this->sisaHariDetail = null;
        }
    }

    private function ambilCatatanTerbaru()
    {
        $this->catatanTerbaru = \App\Models\ProyekCatatanPekerjaan::where('proyek_id', $this->proyek->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
    }

    public function render()
    {
        return view('livewire.dashboard-proyek');
    }
}
