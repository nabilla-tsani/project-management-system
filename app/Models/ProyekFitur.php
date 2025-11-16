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
    

}

