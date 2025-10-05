<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProyekUser extends Model
{
    use HasFactory;

    protected $table = 'proyek_user';

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

    //  Relasi ke ProyekFitur melalui pivot proyek_fitur_user
    public function fitur()
    {
        return $this->belongsToMany(ProyekFitur::class, 'proyek_fitur_user', 'user_id', 'proyek_fitur_id', 'user_id', 'id')
            ->withPivot('keterangan')
            ->withTimestamps();
    }
}
