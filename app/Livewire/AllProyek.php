<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Proyek;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;

class AllProyek extends Component
{
    use WithPagination;

    public $showModal = false;
    public $isEdit = false;
    public $proyek_id;
    public $nama_proyek;
    public $customer_id;
    public $deskripsi;
    public $lokasi;
    public $tanggal_mulai;
    public $tanggal_selesai;
    public $anggaran;
    public $status;

    public $search = '';
    public $statusFilter = '';
    public $proyeks;

    public $alert = null; 
    public $confirmDelete = false;
    public $deleteId = null;

    protected $rules = [
        'nama_proyek' => 'required|string|max:255',
        'customer_id' => 'required|exists:customer,id',
        'deskripsi' => 'nullable|string',
        'lokasi' => 'nullable|string|max:255',
        'tanggal_mulai' => 'required|date',
        'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
        'anggaran' => 'required|numeric',
        'status' => 'required|string|max:50',
    ];

    protected $messages = [
    'nama_proyek.max' => 'Maximum Project Name is 255 characters.',
    'customer_id.exists' => 'Invalid Customer.',
    'lokasi.max' => 'Maximum Location name is 255 characters.',
    'tanggal_selesai.after_or_equal' => 'The end date field must be a date after or equal to the start date.',
    'anggaran.numeric' => 'The budget must be a number.',
    ];

    public function mount()
    {
        $user = Auth::user();
        $this->proyeks = $user->proyeks()->with('customer')->get();
    }

    public function render()
    {
        $user = Auth::user();

        $query = $user->proyeks()->with(['customer', 'proyekUsers']) 
            ->when($this->search, fn($q) => 
                $q->whereRaw('LOWER(nama_proyek) LIKE ?', ['%' . strtolower(trim($this->search)) . '%'])
            )
            ->when($this->statusFilter, fn($q) =>
                $q->where('status', $this->statusFilter)
            )
            ->orderBy('id', 'desc');

        $proyek = $query->paginate(20);
        $customers = Customer::orderBy('nama')->get();

        return view('livewire.all-proyek', compact('proyek', 'customers'));
    }

    private function showAlert($message, $type = 'success')
    {
        $this->alert = ['message' => $message, 'type' => $type];
        $this->dispatch('reset-alert')->self();
    }

    public function openModal($id = null)
    {
        $this->resetForm();
        if ($id) {
            $this->edit($id);
        } else {
            $this->showModal = true;
        }
    }

    public function closeModal()
    {
        $this->resetForm();
        $this->showModal = false;
    }

    private function resetForm()
    {
        $this->reset(['proyek_id', 'nama_proyek', 'customer_id', 'deskripsi', 'lokasi', 'tanggal_mulai', 'tanggal_selesai', 'anggaran', 'status', 'isEdit']);
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function store()
    {
        try {
            $this->validate();

            $proyek = Proyek::create([
                'nama_proyek' => $this->nama_proyek,
                'customer_id' => $this->customer_id,
                'deskripsi' => $this->deskripsi,
                'lokasi' => $this->lokasi,
                'tanggal_mulai' => $this->tanggal_mulai,
                'tanggal_selesai' => $this->tanggal_selesai,
                'anggaran' => $this->anggaran,
                'status' => $this->status,
            ]);

            $user = Auth::user();
            $proyek->users()->attach($user->id, [
                'sebagai' => 'manajer proyek',
                'keterangan' => 'Pembuat proyek',
            ]);

            $this->closeModal();
            session()->flash('message', 'Proyek berhasil ditambahkan');



        } catch (\Exception $e) {
            $this->alert = [
                'type' => 'error',
                'message' => 'Failed to add project: ' . $e->getMessage(),
            ];
        }
         $this->dispatch('reset-alert');
    }

    public function edit($id)
    {
        $this->resetValidation();
        $proyek = Proyek::findOrFail($id);

        $this->proyek_id = $proyek->id;
        $this->nama_proyek = $proyek->nama_proyek;
        $this->customer_id = $proyek->customer_id;
        $this->deskripsi = $proyek->deskripsi;
        $this->lokasi = $proyek->lokasi;
        $this->tanggal_mulai = $proyek->tanggal_mulai;
        $this->tanggal_selesai = $proyek->tanggal_selesai;
        $this->anggaran = $proyek->anggaran;
        $this->status = $proyek->status;

        $this->isEdit = true;
        $this->showModal = true;
    }

    public function update()
    {
        try {
            $this->validate();

            Proyek::findOrFail($this->proyek_id)->update([
                'nama_proyek' => $this->nama_proyek,
                'customer_id' => $this->customer_id,
                'deskripsi' => $this->deskripsi,
                'lokasi' => $this->lokasi,
                'tanggal_mulai' => $this->tanggal_mulai,
                'tanggal_selesai' => $this->tanggal_selesai,
                'anggaran' => $this->anggaran,
                'status' => $this->status,
            ]);

            $this->closeModal();
            session()->flash('message', 'Proyek berhasil diperbarui');

        } catch (\Exception $e) {
            $this->alert = [
                'type' => 'error',
                'message' => 'Failed to update project: ' . $e->getMessage(),
            ];
        }
        $this->dispatch('reset-alert');
    }


    public function deleteProyek()
    {
        try {
            $proyek = Proyek::findOrFail($this->deleteId);
            $proyek->delete();

            $this->confirmDelete = false;
            $this->deleteId = null;

            session()->flash('message', 'Proyek berhasil dihapus');


        } catch (\Illuminate\Database\QueryException $e) {
            $this->alert = [
                'type' => 'error',
                'message' => $e->getCode() === '23503'
                    ? 'Cannot delete this project — it’s being used.'
                    : 'Failed to delete: ' . $e->getMessage(),
            ];
            $this->dispatch('reset-alert');
        }
    }


    public function clearAlert()
    {
        $this->alert = null;
    }

    public function confirmDeleteProyek($id)
    {
        $proyek = Proyek::findOrFail($id);

        $this->deleteId = $id;
        $this->nama_proyek = $proyek->nama_proyek; // ✅ tampilkan nama proyek di modal
        $this->confirmDelete = true;
    }

    public function cancelDelete()
    {
        $this->confirmDelete = false;
        $this->deleteId = null;
    }

    public function updatedSearch() { $this->resetPage(); }
    public function updatedStatusFilter() { $this->resetPage(); }
}
