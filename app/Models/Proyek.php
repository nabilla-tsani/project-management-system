<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proyek extends Model
{
    use HasFactory;

    protected $table = 'proyek';

    protected $fillable = [
        'nama_proyek',
        'customer_id',
        'deskripsi',
        'lokasi',
        'tanggal_mulai',
        'tanggal_selesai',
        'anggaran',
        'status',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'proyek_user', 'proyek_id', 'user_id')
                    ->withPivot('sebagai', 'keterangan');
    }

    public function proyekUsers()
    {
        return $this->hasMany(ProyekUser::class, 'proyek_id');
    }

    public function fitur()
    {
        return $this->hasMany(ProyekFitur::class, 'proyek_id');
    }

    public function fiturUser()
    {
        return $this->hasMany(ProyekFiturUser::class, 'proyek_fitur_id');
    }

    public function file()
    {
        return $this->hasMany(ProyekFile::class, 'proyek_id');
    }

    public function invoice()
    {
        return $this->hasMany(ProyekInvoice::class, 'proyek_id');
    }

    public function kwitansi()
    {
        return $this->hasMany(ProyekKwitansi::class, 'proyek_id');
    }

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];


}
