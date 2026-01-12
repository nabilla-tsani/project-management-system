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
    public $confirmDeleteModal = false;
    public $showProjectModal = false;
    public $projectList = [];
    public $projectOwnerName = '';
    public $hasProject = false;

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
        $query = Customer::with('proyek');

        if ($this->search) {
            $query->whereRaw(
                'LOWER(nama) LIKE ?',
                ['%' . strtolower($this->search) . '%']
            );
        }


        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }


        $customers = $query
            ->orderBy('status', 'asc') // Active dulu (A < I)
            ->orderBy('id', 'desc')
            ->paginate(10);

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

        session()->flash('message', 'Customer berhasil ditambahkan');
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

        session()->flash('message', 'Customer berhasil diperbarui');
        $this->resetForm();
    }

    public function showProjects($customerId)
{
    $customer = Customer::with('proyek')->findOrFail($customerId);

    $this->projectList = $customer->proyek;
    $this->projectOwnerName = $customer->nama;

    $this->showProjectModal = true;
}


    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function confirmDelete($id)
    {
        $customer = Customer::with('proyek')->findOrFail($id);

        $this->customer_id = $customer->id;
        $this->nama = $customer->nama;

        // INI YANG PENTING
        $this->hasProject = $customer->proyek->isNotEmpty();

        $this->confirmDeleteModal = true;
    }

    public function deleteCustomer()
    {
        $customer = Customer::with('proyek')->findOrFail($this->customer_id);

        if ($customer->proyek->isNotEmpty()) {
            session()->flash(
                'error',
                'Klien masih memiliki proyek dan tidak dapat dihapus.'
            );
            return;
        }

        $customer->delete();

        session()->flash('message', 'Customer berhasil dihapus');

        $this->confirmDeleteModal = false;
        $this->resetForm();
    }


    public function cancelDelete()
    {
        $this->confirmDeleteModal = false;
        $this->customer_id = null;
    }

}
