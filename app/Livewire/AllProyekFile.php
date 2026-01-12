<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\ProyekFile;
use Illuminate\Support\Facades\Storage;


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

    public $search = '';
    public $modalOpen = false;
    public $confirmDelete;
    public $deleteId;
    public $deleteName;

    public function mount($proyekId)
    {
        $this->proyekId = $proyekId;
        $this->loadFiles();
    }

    public function updatedSearch()
    {
        $this->loadFiles();
    }

    public function loadFiles()
    {
        $query = ProyekFile::where('proyek_id', $this->proyekId);

        if (!empty($this->search)) {
            $searchTerm = strtolower($this->search);

            $fileTypeMap = [
                'image' => ['jpg','jpeg','png','gif'],
                'img'   => ['jpg','jpeg','png','gif'],
                'pdf'   => ['pdf'],
                'word'  => ['doc','docx'],
                'doc'   => ['doc','docx'],
                'excel' => ['xls','xlsx'],
                'xls'   => ['xls','xlsx'],
                'ppt'   => ['ppt','pptx'],
                'powerpoint' => ['ppt','pptx'],
                'zip'   => ['zip','rar'],
                'rar'   => ['zip','rar'],
            ];

            $query->where(function($q) use ($searchTerm, $fileTypeMap) {

                $q->whereRaw('LOWER(nama_file) LIKE ?', ["%{$searchTerm}%"])
                ->orWhereRaw('LOWER(keterangan) LIKE ?', ["%{$searchTerm}%"]);

                // Cari berdasarkan extension dari path
                $q->orWhereRaw("LOWER(SUBSTRING(path from '\\.([^.]+)$')) LIKE ?", ["%{$searchTerm}%"]);

                // Cari berdasarkan kategori file
                if (array_key_exists($searchTerm, $fileTypeMap)) {
                    $extList = $fileTypeMap[$searchTerm];
                    $q->orWhere(function($q2) use ($extList) {
                        foreach ($extList as $ext) {
                            $q2->orWhereRaw("LOWER(path) LIKE ?", ["%.{$ext}"]);
                        }
                    });
                }
            });

        }
        $this->files = $query->orderBy('id', 'desc')->get();
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
            'file' => $this->fileId ? 'nullable|file|max:10240' : 'required|file|max:10240',
            'namaFile' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string|max:1000',
        ]);

        $path = null;

        // -----------------------
        // CREATE (fileId null)
        // -----------------------
        if (!$this->fileId && $this->file) {
            $path = $this->generateFileNameAndUpload($this->file, $this->namaFile);
        }

        // -----------------------
        // UPDATE (fileId tidak null)
        // -----------------------
        if ($this->fileId) {

            $fileRecord = ProyekFile::find($this->fileId);
            $oldPath = $fileRecord->path;

            // Jika user mengubah nama file tetapi tidak upload file baru
            if ($this->namaFile && $this->namaFile !== $fileRecord->nama_file && !$this->file) {

                $extension = pathinfo($oldPath, PATHINFO_EXTENSION);
                $newBaseName = str()->slug($this->namaFile);

                // Rename file fisik
                $path = $this->renameExistingFile($oldPath, $newBaseName, $extension);
            } 
            else {
                // Kalau tidak rename (nama sama), gunakan path lama
                $path = $oldPath;
            }
        }

        // -----------------------
        // SIMPAN KE DATABASE
        // -----------------------
        ProyekFile::updateOrCreate(
            ['id' => $this->fileId],
            [
                'proyek_id'  => $this->proyekId,
                'user_id'    => auth()->id(),
                'keterangan' => $this->keterangan,
                'nama_file'  => $this->namaFile ?: ($this->file?->getClientOriginalName()),
                'path'       => $path,
            ]
        );

        // Feedback UI
        session()->flash(
            'message',
            $this->fileId ? 'Berkas berhasil diperbarui.' : 'Berkas berhasil diunggah.'
        );

        $this->modalOpen = false;
        $this->loadFiles();
    }


    private function generateFileNameAndUpload($file, $namaFile)
    {
        $folder = 'proyek_files';

        $extension = $file->getClientOriginalExtension();

        // Jika nama file user kosong → pakai nama asli
        if (empty($namaFile)) {
            $baseName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        } else {
            $baseName = str()->slug($namaFile);
        }

        // Buat nama file awal
        $fileName = $baseName . '.' . $extension;

        // Anti duplicate → tambah (1), (2), dst
        $counter = 1;
        while (Storage::disk('public')->exists("$folder/$fileName")) {
            $fileName = $baseName . "($counter)." . $extension;
            $counter++;
        }

        // Upload
        return $file->storeAs($folder, $fileName, 'public');
    }

    private function renameExistingFile($oldPath, $newBaseName, $extension)
    {
        $folder = 'proyek_files';

        // Nama awal
        $fileName = $newBaseName . '.' . $extension;

        // Cek duplicate
        $counter = 1;
        while (Storage::disk('public')->exists("$folder/$fileName")) {
            $fileName = $newBaseName . "($counter)." . $extension;
            $counter++;
        }

        // Path baru
        $newPath = $folder . '/' . $fileName;

        // Rename file fisik
        Storage::disk('public')->move($oldPath, $newPath);

        // Return path baru
        return $newPath;
    }


    public function askDelete($id)
    {
        $file = ProyekFile::find($id);

        $this->deleteId = $id;
        $this->deleteName = $file->nama_file; // simpan nama file
        $this->confirmDelete = true;
    }

    public function delete($id)
    {
        $file = ProyekFile::findOrFail($id);
        if ($file->path && \Storage::disk('public')->exists($file->path)) {
            \Storage::disk('public')->delete($file->path);
        }
        $this->confirmDelete = false;
        $file->delete();
        session()->flash('message', 'Berkas berhasil dihapus.');
        $this->loadFiles();
    }

    public function render()
    {
        return view('livewire.all-proyek-file');
    }
}

