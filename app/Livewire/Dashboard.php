<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Customer;
use App\Models\ProyekCatatanPekerjaan;

class Dashboard extends Component
{
    public int $totalProyek = 0;
    public int $totalCustomer = 0;
    public int $totalNotes = 0;

    public array $statusChart = [];
    public array $budgetLabels = [];
    public array $budgetValues = [];

    public array $customerStatusLabels = [];
    public array $customerStatusValues = [];

    //  LIST PROYEK
    public $latestProjects = [];
    public $upcomingDeadlines = [];

    //  CUSTOMER BY JUMLAH PROYEK
    public array $customerProjectLabels = [];
    public array $customerProjectValues = [];


    public function mount()
    {
        $user = Auth::user();

        // TOTAL
        $this->totalProyek   = $user->proyeks()->count();
        $this->totalCustomer = Customer::count();
        $this->totalNotes    = ProyekCatatanPekerjaan::where('user_id', $user->id)->count();

        // STATUS PROYEK
        $this->statusChart = [
            'belum_dimulai'   => $user->proyeks()->where('status', 'belum_dimulai')->count(),
            'sedang_berjalan' => $user->proyeks()->where('status', 'sedang_berjalan')->count(),
            'selesai'         => $user->proyeks()->where('status', 'selesai')->count(),
            'ditunda'         => $user->proyeks()->where('status', 'ditunda')->count(),
        ];

        // BUDGET
        $projects = $user->proyeks()->orderBy('anggaran', 'asc')->get();
        $this->budgetLabels = $projects->pluck('nama_proyek')->toArray();
        $this->budgetValues = $projects->pluck('anggaran')->toArray();

        // CUSTOMER STATUS
        $this->customerStatusLabels = ['Aktif', 'Tidak Aktif'];
        $this->customerStatusValues = [
            Customer::where('status', 'aktif')->count(),
            Customer::where('status', 'tidak_aktif')->count(),
        ];

        // 🔥 3 PROYEK TERBARU
        $this->latestProjects = $user->proyeks()
            ->latest('created_at')
            ->take(3)
            ->get();

        // ===== DEADLINE TERDEKAT (SEMUA PROYEK DENGAN TANGGAL SAMA) =====
        // 1️⃣ Ambil tanggal deadline terdekat
        $nearestDeadline = $user->proyeks()
            ->whereNotNull('tanggal_selesai')
            ->orderBy('tanggal_selesai', 'asc')
            ->value('tanggal_selesai');
        // 2️⃣ Ambil semua proyek dengan deadline tersebut
        $this->upcomingDeadlines = $nearestDeadline
            ? $user->proyeks()
                ->whereDate('tanggal_selesai', $nearestDeadline)
                ->orderBy('created_at', 'asc')
                ->get()
            : collect();

        $customerProjects = Customer::from('customer')
            ->select('customer.id', 'customer.nama')
            ->leftJoin('proyek', 'proyek.customer_id', '=', 'customer.id')
            ->selectRaw('COUNT(proyek.id) as total_proyek')
            ->groupBy('customer.id', 'customer.nama')
            ->orderByDesc('total_proyek')
            ->get();

        $this->customerProjectLabels = $customerProjects
            ->pluck('nama')
            ->take(5)
            ->toArray();

        $this->customerProjectValues = $customerProjects
            ->pluck('total_proyek')
            ->take(5)
            ->toArray();

    }

    public function render()
    {
        return view('livewire.dashboard')
            ->layout('layouts.app');
    }
}

