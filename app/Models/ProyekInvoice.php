<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProyekInvoice extends Model
{
    use HasFactory;

    protected $table = 'proyek_invoice';

    protected $fillable = [
        'nomor_invoice',
        'proyek_id',
        'judul_invoice',
        'jumlah',
        'tanggal_invoice',
        'keterangan',
        'status',
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

