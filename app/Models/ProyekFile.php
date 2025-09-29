<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProyekFile extends Model
{
    use HasFactory;

    protected $table = 'proyek_file';

    protected $fillable = [
        'proyek_id',
        'keterangan',
        'nama_file',
        'path',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function proyek()
    {
        return $this->belongsTo(Proyek::class, 'proyek_id');
    }

}

