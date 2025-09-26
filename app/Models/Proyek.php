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

    public function proyekUsers()
    {
        return $this->hasMany(ProyekUser::class);
    }

    public function users()
{
    return $this->belongsToMany(User::class, 'proyek_user', 'proyek_id', 'user_id')
                ->withPivot('sebagai', 'keterangan');
}

}
