<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProyekUser extends Model
{
    use HasFactory;

    protected $table = 'proyek_user';

    // Pastikan semua kolom yang akan diisi ada di $fillable
    protected $fillable = [
        'proyek_id',
        'user_id',
        'sebagai',
        'keterangan',
    ];

    public function proyek()
    {
        return $this->belongsTo(Proyek::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
