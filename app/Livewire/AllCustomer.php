<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Customer;
use Livewire\WithPagination;

class AllCustomer extends Component
{
    use WithPagination;

    public $customer_id;
    public $nama, $alamat, $nomor_telepon, $email, $catatan, $status;
    public $isEdit = false;
    public $search = '';
    public $statusFilter = '';
    public $showModal = false;

    protected $rules = [
        'nama' => 'required|string|max:255',
        'alamat' => 'nullable|string|max:255',
        'nomor_telepon' => 'nullable|string|max:20',
        'email' => 'nullable|email|max:255',
        'catatan' => 'nullable|string',
        'status' => 'required|string|max:50',
    ];

    public function render()
    {
        $query = Customer::query();

        // Search
        if ($this->search) {
            $query->where('nama', 'like', '%'.$this->search.'%');
        }

        // Filter status
        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        $customers = $query->orderBy('id', 'desc')->paginate(10); // sesuaikan jumlah grid

        return view('livewire.all-customer', ['customers' => $customers]);
    }

    public function resetForm()
    {
        $this->customer_id = null;
        $this->nama = '';
        $this->alamat = '';
        $this->nomor_telepon = '';
        $this->email = '';
        $this->catatan = '';
        $this->status = '';
        $this->isEdit = false;
        $this->showModal = false;
    }

    public function store()
    {
        $this->validate();

        Customer::create([
            'nama' => $this->nama,
            'alamat' => $this->alamat,
            'nomor_telepon' => $this->nomor_telepon,
            'email' => $this->email,
            'catatan' => $this->catatan,
            'status' => $this->status,
        ]);

        session()->flash('message', 'Customer berhasil ditambahkan.');
        $this->resetForm();
    }

    public function edit($id)
    {
        $customer = Customer::findOrFail($id);
        $this->customer_id = $customer->id;
        $this->nama = $customer->nama;
        $this->alamat = $customer->alamat;
        $this->nomor_telepon = $customer->nomor_telepon;
        $this->email = $customer->email;
        $this->catatan = $customer->catatan;
        $this->status = $customer->status;
        $this->isEdit = true;
        $this->showModal = true;
    }

    public function update()
    {
        $this->validate();

        Customer::where('id', $this->customer_id)->update([
            'nama' => $this->nama,
            'alamat' => $this->alamat,
            'nomor_telepon' => $this->nomor_telepon,
            'email' => $this->email,
            'catatan' => $this->catatan,
            'status' => $this->status,
        ]);

        session()->flash('message', 'Customer berhasil diperbarui.');
        $this->resetForm();
    }

    public function closeModal()
{
    $this->showModal = false;
    $this->resetForm();
}


    public function delete($id)
    {
        Customer::findOrFail($id)->delete();
        session()->flash('message', 'Customer berhasil dihapus.');
    }
}
