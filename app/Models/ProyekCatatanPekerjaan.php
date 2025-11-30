<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProyekCatatanPekerjaan extends Model
{
    use HasFactory;

    protected $table = 'proyek_catatan_pekerjaan';

    protected $fillable = [
        'proyek_id',
        'proyek_fitur_id',
        'user_id',
        'jenis',
        'catatan',
        'tanggal_mulai',
        'tanggal_selesai',  
    ];

    public function proyek()
    {
        return $this->belongsTo(Proyek::class, 'proyek_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function fitur()
    {
        return $this->belongsTo(ProyekFitur::class, 'proyek_fitur_id');
    }

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

}
