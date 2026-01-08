<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sesi extends Model
{
    use HasFactory;

    protected $table = 'sesi';
    public $timestamps = true;

    protected $fillable = [
        'nama_sesi',
        'kelas',
        'tanggal',
        'jam_mulai',
        'jam_selesai',
        'status',
        'created_by', // user_id admin yang membuat sesi
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jam_mulai' => 'datetime',
        'jam_selesai' => 'datetime',
    ];

    /**
     * Relasi ke User (admin yang membuat sesi)
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relasi ke Kehadiran
     */
    public function kehadiran()
    {
        return $this->hasMany(Kehadiran::class);
    }
}
