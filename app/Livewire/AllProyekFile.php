<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\ProyekFile;

class AllProyekFile extends Component
{
    use WithFileUploads;

    public $proyekId; // ID proyek yang sedang dilihat
    public $files = [];

    // Form fields
    public $fileId;
    public $file; // file upload
    public $namaFile;
    public $keterangan;

    // Modal state
    public $modalOpen = false;

    public function mount($proyekId)
    {
        $this->proyekId = $proyekId;
        $this->loadFiles();
    }

    public function loadFiles()
    {
        $this->files = ProyekFile::where('proyek_id', $this->proyekId)
                        ->orderBy('id', 'desc')
                        ->get();
    }

    public function openModal($id = null)
    {
        $this->resetValidation();
        $this->reset(['fileId', 'file', 'namaFile', 'keterangan']);

        if ($id) {
            $fileData = ProyekFile::findOrFail($id);
            $this->fileId = $fileData->id;
            $this->namaFile = $fileData->{'nama_file'};
            $this->keterangan = $fileData->keterangan;
        }

        $this->modalOpen = true;
    }

    public function save()
{
    $this->validate([
        'file' => $this->fileId ? 'nullable|file|max:10240' : 'required|file|max:10240', // max 10MB
        'namaFile' => 'required|string|max:255',
        'keterangan' => 'nullable|string|max:1000',
    ]);

    // Hanya upload file baru jika ini create (fileId = null)
    if (!$this->fileId && $this->file) {
        $originalName = $this->file->getClientOriginalName(); // nama file asli
        $path = $this->file->storeAs('proyek_files', $originalName, 'public');
    }

    // Update atau create
    ProyekFile::updateOrCreate(
        ['id' => $this->fileId],
        [
            'proyek_id' => $this->proyekId,
            'user_id' => auth()->id(),
            'keterangan' => $this->keterangan,
            'nama_file' => $this->namaFile,
            'path' => $this->fileId 
                        ? ProyekFile::find($this->fileId)->path // pakai path lama jika edit
                        : ($this->file ? $path : null),        // pakai path baru jika create
        ]
    );

    $this->modalOpen = false;
    $this->loadFiles();

    session()->flash('message', $this->fileId ? 'File diperbarui' : 'File ditambahkan');
}


    public function delete($id)
    {
        $file = ProyekFile::findOrFail($id);
        if ($file->path && \Storage::disk('public')->exists($file->path)) {
            \Storage::disk('public')->delete($file->path);
        }
        $file->delete();
        $this->loadFiles();
        session()->flash('message', 'File dihapus');
    }

    public function render()
    {
        return view('livewire.all-proyek-file');
    }
}

