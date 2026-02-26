<?php
// app/Models/MusimTanam.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class MusimTanam extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_mt',
        'jenis_mt',
        'tanggal_mulai',
        'tanggal_selesai',
        'target_luas_tanam',
        'jenis_tanaman',
        'status',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_mulai'   => 'date',
        'tanggal_selesai' => 'date',
    ];

    // Durasi dalam hari
    public function getDurasiHariAttribute(): int
    {
        return $this->tanggal_mulai->diffInDays($this->tanggal_selesai);
    }

    // Progress persentase (kalau status berjalan)
    public function getProgressAttribute(): int
    {
        if ($this->status !== 'berjalan') return 0;
        $total   = $this->tanggal_mulai->diffInDays($this->tanggal_selesai);
        $elapsed = $this->tanggal_mulai->diffInDays(Carbon::today());
        if ($total == 0) return 0;
        return min(100, (int) round(($elapsed / $total) * 100));
    }

    // Scope MT yang sedang berjalan
    public function scopeBerjalan($query)
    {
        return $query->where('status', 'berjalan');
    }

    // Badge warna status
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'rencana'  => 'clay',
            'berjalan' => 'water',
            'selesai'  => 'leaf',
            default    => 'textlt',
        };
    }
}
