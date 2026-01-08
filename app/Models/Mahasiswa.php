<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mahasiswa extends Model
{
    use HasFactory;

    protected $table = 'mahasiswa';
    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'no_kartu',
        'nim',
        'nama',
        'kelas',
    ];

    /**
     * Relasi ke User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke Kehadiran
     */
    public function kehadiran()
    {
        return $this->hasMany(Kehadiran::class);
    }
}
