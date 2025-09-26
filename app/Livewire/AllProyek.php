<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Proyek;
use App\Models\Customer;
use Livewire\WithPagination;

class AllProyek extends Component
{
    use WithPagination;

    public $proyek_id;
    public $nama_proyek, $customer_id, $deskripsi, $lokasi, $tanggal_mulai, $tanggal_selesai, $anggaran, $status;
    public $isEdit = false;
    public $search = '';

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
        $proyek = Proyek::with('customer')
            ->where('nama_proyek', 'like', '%'.$this->search.'%')
            ->orderBy('id', 'desc')
            ->paginate(5);

        $customers = Customer::orderBy('nama')->get();

        return view('livewire.all-proyek', [
            'proyek' => $proyek,
            'customers' => $customers
        ]);
    }

    public function resetForm()
    {
        $this->proyek_id = null;
        $this->nama_proyek = '';
        $this->customer_id = '';
        $this->deskripsi = '';
        $this->lokasi = '';
        $this->tanggal_mulai = '';
        $this->tanggal_selesai = '';
        $this->anggaran = '';
        $this->status = '';
        $this->isEdit = false;
    }

    public function store()
    {
        $this->validate();

        Proyek::create([
            'nama_proyek' => $this->nama_proyek,
            'customer_id' => $this->customer_id,
            'deskripsi' => $this->deskripsi,
            'lokasi' => $this->lokasi,
            'tanggal_mulai' => $this->tanggal_mulai,
            'tanggal_selesai' => $this->tanggal_selesai,
            'anggaran' => $this->anggaran,
            'status' => $this->status,
        ]);

        session()->flash('message', 'Proyek berhasil ditambahkan.');
        $this->resetForm();
    }

    public function edit($id)
{
    $this->resetValidation(); // bersihkan error validasi lama
    $proyek = Proyek::findOrFail($id);

    $this->proyek_id      = $proyek->id;
    $this->nama_proyek    = $proyek->nama_proyek;
    $this->customer_id    = $proyek->customer_id;
    $this->deskripsi      = $proyek->deskripsi;
    $this->lokasi         = $proyek->lokasi;
    $this->tanggal_mulai  = \Carbon\Carbon::parse($proyek->tanggal_mulai)->format('Y-m-d');
    $this->tanggal_selesai= \Carbon\Carbon::parse($proyek->tanggal_selesai)->format('Y-m-d');
    $this->anggaran       = $proyek->anggaran;
    $this->status         = $proyek->status;

    $this->isEdit = true;
}


    public function update()
    {
        $this->validate();

        Proyek::where('id', $this->proyek_id)->update([
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
        $this->resetForm();
    }

    public function delete($id)
    {
        Proyek::findOrFail($id)->delete();
        session()->flash('message', 'Proyek berhasil dihapus.');
    }

}
