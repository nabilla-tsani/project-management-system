<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\Proyek;
use App\Models\ProyekFitur;
use App\Models\ProyekCatatanPekerjaan;

class CatatanPekerjaan extends Component
{
    public $proyekFiturId;
    public $proyekId;
    public $catatan = [];
    public $users = [];
    public $catatanModal = false;
    public $catatanId = null;
    public $jenis = '';
    public $isiCatatan = '';
    public $feedback = '';
    public $user_id = '';
    public $namaFitur;
    public $formKey = 'form_default';
    public $filterJenis = '';
    public $tanggal_mulai;
    public $tanggal_selesai;
    public $isMember = false;
    
    public $roleUser;
    public $feedbackModal = false;
    public $feedbackText = '';
    public $feedbackId = null;


    public $alert = null;

    protected $listeners = [
        'openCatatanModal' => 'showModal',
        'hide-alert' => 'hideAlert'
    ];


    protected $rules = [
        'jenis' => 'required|string|max:50',
        'isiCatatan' => 'required|string|max:1000',
        'tanggal_mulai' => 'required|date',
        'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
        'feedback' => 'nullable|string',
    ];

    public function mount($proyekId)
{
    $this->proyekId = $proyekId;

    $this->roleUser = \DB::table('proyek_user')
        ->where('proyek_id', $proyekId)
        ->where('user_id', auth()->id())
        ->value('sebagai'); // ambil kolom 'sebagai'
}


    public function showModal($id)
    {
        $this->resetForm();

        $this->proyekFiturId = $id;

        $fitur = ProyekFitur::with('users')->find($id);
        $this->namaFitur = $fitur?->nama_fitur ?? 'Fitur Tidak Dikenal';

        // Ambil user terkait fitur
        $this->users = $fitur?->users ?? collect();
        $this->proyekId = $fitur?->proyek_id;

        // Cek apakah user login adalah anggota
        $this->isMember = $fitur?->users
            ->pluck('id')
            ->contains(auth()->id());

        $this->loadCatatan();
        $this->catatanModal = true;
    }


    public function loadCatatan()
    {
        $this->catatan = ProyekCatatanPekerjaan::where('proyek_fitur_id', $this->proyekFiturId)
            ->when($this->filterJenis, function($query) {
                // case-insensitive filter to handle stored capitalization differences
                $query->whereRaw('LOWER(jenis) = ?', [strtolower($this->filterJenis)]);
            })
            ->with('user')
            // order by most recently updated first so edited notes jump to the top
            ->orderByDesc('updated_at')
            ->get();
    }


    public function closeModal()
    {
        $this->catatanModal = false;
        $this->resetForm();
    }

    public function resetForm($newKey = true)
    {
        $this->reset(['catatanId', 'jenis', 'isiCatatan', 'tanggal_mulai', 'tanggal_selesai', 'feedback']);
        $this->resetValidation();
        if ($newKey) {
            $this->formKey = uniqid('form_', true);
        }
    }

    public function save()
    {
        $this->validate();

        // normalize jenis
        $normalizedJenis = strtolower(trim($this->jenis));

        // cek apakah ini update atau create
        $isUpdate = $this->catatanId ? true : false;

        ProyekCatatanPekerjaan::updateOrCreate(
            ['id' => $this->catatanId],
            [
                'proyek_fitur_id' => $this->proyekFiturId,
                'proyek_id' => $this->proyekId,
                'user_id' => auth()->id(),
                'jenis' => $normalizedJenis,
                'catatan' => $this->isiCatatan,
                'tanggal_mulai' => $this->tanggal_mulai,
                'tanggal_selesai' => $this->tanggal_selesai,
                'feedback' => $this->feedback,
            ]
        );

        $this->resetForm(true);
        $this->loadCatatan();

        // kirim message sesuai aksi
        session()->flash(
            'message',
            $isUpdate
                ? 'Catatan berhasil diperbarui.'
                : 'Catatan berhasil ditambahkan.'
        );
    }


    public function edit($id)
    {
        $data = ProyekCatatanPekerjaan::findOrFail($id);

        $this->catatanId = $data->id;
        $this->jenis = strtolower($data->jenis);
        $this->isiCatatan = $data->catatan;
        $this->tanggal_mulai = $data->tanggal_mulai ? $data->tanggal_mulai->format('Y-m-d') : null;
        $this->tanggal_selesai = $data->tanggal_selesai ? $data->tanggal_selesai->format('Y-m-d') : null;
        $this->feedback = $data->feedback;
    
        $this->formKey = 'form_edit_' . $data->id;
    }


    public function cancelEdit()
    {
        $this->resetForm(true);
    }

    public function delete($id)
    {
        ProyekCatatanPekerjaan::findOrFail($id)->delete();

        $this->loadCatatan();

        // tampilkan alert sukses delete
        session()->flash('message', 'Catatan berhasil dihapus.');
    }

    public function updatedFilterJenis($value)
    {
        // normalize incoming value and reload
        $this->filterJenis = $value ? strtolower($value) : '';
        $this->loadCatatan();
    }

    public function hideAlert()
    {
        $this->alert = null;
    }

    public function openFeedbackModal($id)
    {
        $data = ProyekCatatanPekerjaan::findOrFail($id);

        $this->feedbackId = $data->id;
        $this->feedbackText = $data->feedback; // isikan jika sudah ada

        $this->feedbackModal = true;
    }

    public function closeFeedbackModal()
    {
        $this->feedbackModal = false;
    }

    public function saveFeedback()
    {
        ProyekCatatanPekerjaan::where('id', $this->feedbackId)
            ->update([
                'feedback' => $this->feedbackText,
            ]);

        $this->feedbackModal = false;
        $this->feedbackId = null;
        $this->feedbackText = '';

        $this->loadCatatan();
        session()->flash('message', 'Umpan balik berhasil ditambahkan.');
    }

    public function deleteFeedback($id)
    {
        ProyekCatatanPekerjaan::where('id', $id)->update([
            'feedback' => null,
        ]);

        $this->loadCatatan();

        session()->flash('message', 'Umpan balik berhasil dihapus.');
    }


    public function render()
    {
        $catatan = ProyekCatatanPekerjaan::with('user')
            ->where('proyek_fitur_id', $this->proyekFiturId) // ✅ batasi berdasarkan fitur aktif
            ->when($this->filterJenis, function($query) {
                // case-insensitive filter
                $query->whereRaw('LOWER(jenis) = ?', [strtolower($this->filterJenis)]);
            })
            // order by most recently updated first so new/edited notes appear at top
            ->orderByDesc('updated_at')
            ->get();

        return view('livewire.catatan-pekerjaan', [
            'catatan' => $catatan,
            'users' => $this->users, // gunakan user dari proyek aktif
        ]);
    }
}