<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\ProyekFitur;
use App\Models\ProyekUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class AllProyekFitur extends Component
{
    public $proyekId;

    // Form fields
    public $fiturId;
    public $nama_fitur;
    public $keterangan;
    public $status_fitur;
    public $target;

    // Modal states
    public $modalOpen = false;

    // Dropdown & user modal
    public $statusList = ['Upcoming','In Progress', 'Done', 'Pending'];
    public $selectedFiturId;
    public $selectedFitur;

    public $showCatatan = []; // array untuk menyimpan state tiap fitur

    // Role flag
    public $isManajerProyek = false;

    // --- AI modal state ---
    public $aiModalOpen = false;
    public $jumlah_fitur_ai = 3;
    public $deskripsi_ai = '';
    public $aiFiturList = [];
    public $showAiReview = false;
    public $loadingAi = false;
    public $revisi_deskripsi_ai = '';
    public $jumlah_fitur_revisi = null;

    public $catatanModal = false;
    public $userFiturModal = false;

    public $search = '';

    public $showConfirmDelete = false; 
    public $deleteId;

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
    }

    // --- Modal tambah/edit ---
    public function openModal($id = null)
    {
        $this->resetValidation();
        $this->reset(['fiturId', 'nama_fitur', 'keterangan', 'status_fitur', 'target']);

        if ($id) {
            $fitur = ProyekFitur::findOrFail($id);
            $this->fiturId = $fitur->id;
            $this->nama_fitur = $fitur->nama_fitur;
            $this->keterangan = $fitur->keterangan;
            $this->status_fitur = $fitur->status_fitur;
            $this->target = $fitur->target;
        }

        $this->modalOpen = true;
    }

    public function save()
    {
        $this->validate([
            'nama_fitur'   => 'required|string|max:255',
            'keterangan'   => 'nullable|string|max:1000',
            'status_fitur' => 'required|string|in:Upcoming,Pending,In Progress,Done',
            'target'       => 'nullable|date',
        ]);

        ProyekFitur::updateOrCreate(
            ['id' => $this->fiturId],
            [
                'proyek_id'   => $this->proyekId,
                'nama_fitur'  => $this->nama_fitur,
                'keterangan'  => $this->keterangan,
                'status_fitur'=> $this->status_fitur,
                'target'      => $this->target,
            ]
        );

        $this->closeModal();

        session()->flash('message', $this->fiturId ? 'Feature successfully updated!' : 'New feature successfully added!');

        $this->dispatch('$refresh');
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $this->showConfirmDelete = true;
    }

    public function delete()
    {
        ProyekFitur::find($this->deleteId)->delete();

        $this->showConfirmDelete = false;
        $this->deleteId = null;

        session()->flash('message', 'Data deleted successfully.');

        $this->dispatch('$refresh');
    }


    public function closeModal()
    {
        $this->reset(['modalOpen', 'fiturId', 'nama_fitur', 'keterangan', 'status_fitur', 'target']);
        $this->resetValidation();
    }

    public function toggleCatatan($fiturId)
    {
        if (isset($this->showCatatan[$fiturId])) {
            $this->showCatatan[$fiturId] = !$this->showCatatan[$fiturId];
        } else {
            $this->showCatatan[$fiturId] = true;
        }
    }

    // --- Modal AI ---
    public function openAiModal()
    {
        $this->reset(['jumlah_fitur_ai', 'deskripsi_ai', 'aiFiturList', 'showAiReview', 'loadingAi']);
        $this->jumlah_fitur_ai = 3;
        $this->aiModalOpen = true;
    }

    public function closeAiModal()
    {
        $this->reset(['aiModalOpen', 'jumlah_fitur_ai', 'deskripsi_ai']);
        $this->resetValidation();
    }

    public function generateFiturAI()
    {
        $this->validate([
            'jumlah_fitur_ai' => 'required|integer|min:1|max:10',
            'deskripsi_ai' => 'required|string|min:10',
        ]);

        $this->loadingAi = true;

        $this->aiFiturList = $this->callGeminiApi($this->deskripsi_ai, $this->jumlah_fitur_ai);

        $this->loadingAi = false;

        if (empty($this->aiFiturList)) {
            session()->flash('message', 'AI generated feature failed. Please try again.');
            return;
        }
        $this->aiModalOpen = false;
        $this->showAiReview = true;
    }

    public function approveAiFitur()
    {
        foreach ($this->aiFiturList as $namaFitur) {
            ProyekFitur::create([
                'proyek_id' => $this->proyekId,
                'nama_fitur' => $namaFitur,
                'status_fitur' => 'Pending',
            ]);
        }

        $this->showAiReview = false;
        $this->aiFiturList = [];

        session()->flash('message', 'AI-generated features added successfully!');

        $this->dispatch('$refresh');
        $this->closeAiModal();
    }


    public function regenerateAiFitur()
    {
        $this->validate([
            'revisi_deskripsi_ai' => 'required|string|min:5',
        ], [
            'revisi_deskripsi_ai.required' => 'Mohon isi deskripsi revisi untuk AI.',
        ]);

        $this->loadingAi = true;

        // Tentukan jumlah fitur yang akan digunakan
        $jumlahBaru = $this->jumlah_fitur_revisi ?: $this->jumlah_fitur_ai;

        // Gabungkan deskripsi lama dan revisi
        $deskripsiGabungan = trim($this->deskripsi_ai . ' ' . $this->revisi_deskripsi_ai);

        // Panggil ulang AI dengan jumlah baru
        $this->aiFiturList = $this->callGeminiApi($deskripsiGabungan, $jumlahBaru);

        $this->loadingAi = false;

        if (empty($this->aiFiturList)) {
            session()->flash('message', 'AI failed to generate new features. Please try again.');
            return;
        }

        $this->revisi_deskripsi_ai = '';
        $this->jumlah_fitur_revisi = null;
        $this->resetValidation();
        $this->dispatch('clear-revisi');

    }
    

    public function reviseAiFitur()
    {
        $this->showAiReview = false;
        $this->aiFiturList = [];
    }

    private function callGeminiApi($deskripsi, $jumlahFitur)
    {
        try {
            // Gunakan service AI yang sama seperti fitur "Generate Proposal"
            $aiService = app(\App\Services\GeminiService::class);

            // Ambil data proyek dan fitur yang sudah ada
            $proyek = \App\Models\Proyek::with('customer')->find($this->proyekId);
            $fiturEksisting = \App\Models\ProyekFitur::where('proyek_id', $this->proyekId)
                ->pluck('nama_fitur')
                ->toArray();

            // Format daftar fitur yang sudah ada (untuk dikirim ke prompt)
            $daftarFitur = $fiturEksisting
                ? "- " . implode("\n- ", $fiturEksisting)
                : "(belum ada fitur yang terdaftar)";

            // Susun prompt yang lebih kaya konteks
            $prompt = <<<EOT
    Kamu adalah asisten pengembang perangkat lunak.
    Berikut informasi proyek yang sedang dikerjakan:

    Nama proyek: {$proyek->nama_proyek}
    Deskripsi proyek: {$proyek->deskripsi}

    Daftar fitur yang sudah ada:
    {$daftarFitur}

    Deskripsi tambahan mengenai fitur yang diminta: {$deskripsi}
    Tugasmu: buatkan daftar {$jumlahFitur} fitur baru yang relevan untuk proyek ini,
    tanpa mengulang fitur yang sudah ada di atas.
    Format jawaban hanya berupa daftar nama fitur (satu fitur per baris),
    tanpa penjelasan atau nomor urut, dan tanpa tanda -.
    EOT;

            // Minta hasil dari Gemini
            $aiResponse = $aiService->ask($prompt);

            // Log untuk debugging (opsional)
            \Log::info("AI response (fitur): " . $aiResponse);

            // Pecah hasil menjadi array per baris
            $fiturs = array_filter(array_map('trim', preg_split("/\r\n|\n|\r/", $aiResponse)));

            // Hilangkan nomor urut jika ada (misal "1. Login" -> "Login")
            $fiturs = array_map(fn($f) => preg_replace('/^\d+\.\s*/', '', $f), $fiturs);

            return $fiturs;
        } catch (\Exception $e) {
            \Log::error("Gemini API error: " . $e->getMessage());
            return [];
        }
    }

    public function openUserFitur($id)
    {
        $this->dispatch('openUserFiturModal', id: $id);
    }

    public function openUserFiturModal($id)
    {
        $this->selectedFiturId = $id;
        $this->userFiturModal = true;
    }

    public function closeUserFiturModal()
    {
        $this->userFiturModal = false;
        $this->selectedFiturId = null;
    }


    // Method untuk mengakses Catatan Pekerjaan
    public function openCatatan($id)
    {
        $this->dispatch('openCatatanModal', id: $id);
        $this->fiturList = ProyekFitur::withCount('catatan')
    ->where('proyek_id', $this->proyekId)
    ->get();

    }

    public function openCatatanModal($id)
    {
        $this->selectedFiturId = $id;
        $this->catatanModal = true;
    }

    public function closeCatatanModal()
    {
        $this->catatanModal = false;
        $this->selectedFiturId = null;
    }

    

    public function render()
    {
        \Log::info('Search term:', [$this->search]);

        $searchTerm = strtolower($this->search); // ubah pencarian jadi lowercase

        $fiturs = ProyekFitur::with(['anggota.user'])
            ->where('proyek_id', $this->proyekId)
            ->when($this->search, function ($query) use ($searchTerm) {
                $query->where(function ($q) use ($searchTerm) {
                    $q->whereRaw('LOWER(nama_fitur) LIKE ?', ['%' . $searchTerm . '%'])
                    ->orWhereHas('anggota.user', function ($subQuery) use ($searchTerm) {
                        $subQuery->whereRaw('LOWER(name) LIKE ?', ['%' . $searchTerm . '%']);
                    });
                });
            })
            ->orderBy('id', 'desc')
            ->get();

        return view('livewire.all-proyek-fitur', [
            'fiturs' => $fiturs,
            'isManajerProyek' => $this->isManajerProyek,
        ]);
    }

}
