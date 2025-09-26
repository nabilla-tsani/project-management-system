<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProyekFiturUser extends Model
{
    use HasFactory;

    protected $table = 'proyek_fitur_user';

    protected $fillable = [
        'proyek_fitur_id',
        'user_id',
        'keterangan',
    ];

    public function fitur()
    {
        return $this->belongsTo(ProyekFitur::class, 'proyek_fitur_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}
