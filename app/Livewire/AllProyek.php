<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Proyek;
use App\Models\Customer;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class AllProyek extends Component
{
    use WithPagination;

    public $showModal = false;
    public $isEdit = false;
    public $proyek_id;
    public $nama_proyek, $customer_id, $deskripsi, $lokasi, $tanggal_mulai, $tanggal_selesai, $anggaran, $status;
    public $search = '';
    public $statusFilter = '';
    public $proyeks;

    public function mount()
    {
        // Ambil user yang login
        $user = Auth::user();

        // Ambil semua proyek yang terkait dengan user ini
        $this->proyeks = $user->proyeks()->with('customer')->get();
    }


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

    public function render()
    {
        $user = Auth::user();

        // Ambil proyek yang hanya terkait user login
        $query = $user->proyeks()->with('customer')
            ->where('nama_proyek', 'like', '%'.$this->search.'%')
            ->orderBy('proyek.id', 'desc'); // hati-hati pakai nama tabel pivot

        // Filter status kalau ada
        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        $proyek = $query->paginate(20);

        $customers = Customer::orderBy('nama')->get();

        return view('livewire.all-proyek', compact('proyek','customers'));
        
    }


    public function openModal($id = null)
    {
        $this->resetForm();
        if($id) {
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
        $this->reset(['proyek_id','nama_proyek','customer_id','deskripsi','lokasi','tanggal_mulai','tanggal_selesai','anggaran','status','isEdit']);
    }

    public function store()
    {
        $this->validate();

        // Buat proyek baru
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

        // Ambil user yang sedang login
        $user = Auth::user();

        // Tambahkan ke pivot table proyek_user sebagai "manajer proyek"
        $proyek->users()->attach($user->id, [
            'sebagai' => 'manajer proyek',
            'keterangan' => 'Pembuat proyek',
        ]);

        session()->flash('message', 'Proyek berhasil ditambahkan.');
        $this->closeModal();
    }


    public function edit($id)
    {
        $this->resetValidation();
        $proyek = Proyek::findOrFail($id);

        $this->proyek_id       = $proyek->id;
        $this->nama_proyek     = $proyek->nama_proyek;
        $this->customer_id     = $proyek->customer_id;
        $this->deskripsi       = $proyek->deskripsi;
        $this->lokasi          = $proyek->lokasi;
        $this->tanggal_mulai   = $proyek->tanggal_mulai;
        $this->tanggal_selesai = $proyek->tanggal_selesai;
        $this->anggaran        = $proyek->anggaran;
        $this->status          = $proyek->status;

        $this->isEdit = true;
        $this->showModal = true;
    }

    public function update()
    {
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

        session()->flash('message', 'Proyek berhasil diperbarui.');
        $this->closeModal();
    }

public function deleteProyek($id)
{
    $proyek = Proyek::findOrFail($id);

    try {
        $proyek->delete();
        session()->flash('message', [
            'text' => 'Proyek berhasil dihapus.',
            'type' => 'success',  // success, error, warning
            'duration' => 1000    // durasi dalam milidetik
        ]);
    } catch (\Illuminate\Database\QueryException $e) {
        if ($e->getCode() === '23503') { 
            session()->flash('message', [
                'text' => 'Proyek sedang digunakan dan tidak bisa dihapus.',
                'type' => 'error',
                'duration' => 4000
            ]);
        } else {
            session()->flash('message', [
                'text' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'type' => 'error',
                'duration' => 5000
            ]);
        }
    }

    // Refresh data Livewire
    $this->proyeks = Auth::user()->proyeks()->with('customer')->get();
}


}
