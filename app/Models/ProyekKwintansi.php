<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProyekKwintansi extends Model
{
    use HasFactory;

    protected $table = 'proyek_invoice';

    protected $fillable = [
        'nomor_kwitansi',
        'nomor_invoice',
        'proyek_id',
        'judul_kwitansi',
        'jumlah',
        'tanggal_kwitansi',
        'keterangan',
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

    // FK kolom nomor_invoice
    // FK kolom nomor_kwitansi

}

