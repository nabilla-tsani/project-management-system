<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProyekFitur extends Model
{
    use HasFactory;

    protected $table = 'proyek_fitur';

    protected $fillable = [
        'proyek_id',
        'nama_fitur',
        'keterangan',
        'status_fitur',
    ];

    public function proyek()
    {
        return $this->belongsTo(Proyek::class, 'proyek_id');
    }


    public function anggota()
    {
        return $this->hasMany(ProyekFiturUser::class, 'proyek_fitur_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'proyek_fitur_user', 'proyek_fitur_id', 'user_id');
    }

    public function catatan()
    {
        return $this->hasMany(ProyekCatatanPekerjaan::class, 'proyek_fitur_id');
    }
    

}

