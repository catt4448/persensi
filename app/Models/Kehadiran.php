<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kehadiran extends Model
{
    use HasFactory;

    protected $table = 'kehadiran';
    public $timestamps = true;

    protected $fillable = [
        'sesi_id',
        'mahasiswa_id',
        'waktu_hadir',
        'waktu_keluar',
        'status', // hadir, terlambat, izin, dll
    ];

    protected $casts = [
        'waktu_hadir' => 'datetime',
        'waktu_keluar' => 'datetime',
    ];

    /**
     * Relasi ke Sesi
     */
    public function sesi()
    {
        return $this->belongsTo(Sesi::class);
    }

    /**
     * Relasi ke Mahasiswa
     */
    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class);
    }
}
