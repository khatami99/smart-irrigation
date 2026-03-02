<?php
// app/Models/Rtt.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Rtt extends Model
{
    protected $fillable = [
        'petak_id', 'musim_tanam_id', 'user_id',
        'rencana_mulai_tanam', 'rencana_selesai_tanam',
        'realisasi_mulai_tanam', 'realisasi_selesai_tanam',
        'target_luas', 'realisasi_luas',
        'urutan_rotasi', 'durasi_rotasi_hari',
        'jadwal_fase', 'status', 'keterangan',
    ];

    protected $casts = [
        'rencana_mulai_tanam'      => 'date',
        'rencana_selesai_tanam'    => 'date',
        'realisasi_mulai_tanam'    => 'date',
        'realisasi_selesai_tanam'  => 'date',
        'jadwal_fase'              => 'array',
    ];

    // ── Relasi ──
    public function petak()      { return $this->belongsTo(Petak::class); }
    public function musimTanam() { return $this->belongsTo(MusimTanam::class); }
    public function user()       { return $this->belongsTo(User::class); }

    // ── Accessors ──

    // Durasi rencana tanam (hari)
    public function getDurasiRencanaAttribute(): int
    {
        return $this->rencana_mulai_tanam->diffInDays($this->rencana_selesai_tanam);
    }

    // Progress berdasarkan tanggal hari ini
    public function getProgressAttribute(): int
    {
        if ($this->status === 'selesai') return 100;
        if ($this->status === 'rencana') return 0;

        $mulai    = $this->realisasi_mulai_tanam ?? $this->rencana_mulai_tanam;
        $selesai  = $this->rencana_selesai_tanam;
        $today    = Carbon::today();

        if ($today < $mulai)   return 0;
        if ($today > $selesai) return 100;

        $total   = $mulai->diffInDays($selesai);
        $elapsed = $mulai->diffInDays($today);
        return $total > 0 ? round(($elapsed / $total) * 100) : 0;
    }

    // Efisiensi luas (%)
    public function getEfisiensiLuasAttribute(): ?float
    {
        if (!$this->realisasi_luas || !$this->target_luas) return null;
        return round(($this->realisasi_luas / $this->target_luas) * 100, 1);
    }

    // Warna status
    public function getStatusColorAttribute(): array
    {
        return match($this->status) {
            'rencana'  => ['bg' => 'rgba(196,137,90,.1)',  'border' => 'rgba(196,137,90,.3)',  'text' => '#8b5e3c'],
            'berjalan' => ['bg' => 'rgba(74,124,111,.1)',  'border' => 'rgba(74,124,111,.3)',  'text' => '#4a7c6f'],
            'selesai'  => ['bg' => 'rgba(90,122,71,.1)',   'border' => 'rgba(90,122,71,.3)',   'text' => '#5a7a47'],
            'batal'    => ['bg' => 'rgba(185,74,60,.08)',  'border' => 'rgba(185,74,60,.2)',   'text' => '#a03828'],
            default    => ['bg' => 'rgba(122,99,85,.08)',  'border' => 'rgba(122,99,85,.2)',   'text' => '#7a6355'],
        };
    }

    // Fase saat ini berdasarkan tanggal
    public function getFaseSekarangAttribute(): ?string
    {
        if (!$this->jadwal_fase) return null;
        $today = Carbon::today()->format('Y-m-d');
        foreach ($this->jadwal_fase as $fase) {
            if ($today >= $fase['mulai'] && $today <= $fase['selesai']) {
                return $fase['fase'];
            }
        }
        return null;
    }

    // Generate jadwal fase otomatis dari tanggal mulai
    public static function generateJadwalFase(string $mulai, string $selesai): array
    {
        $start  = Carbon::parse($mulai);
        $end    = Carbon::parse($selesai);
        $durasi = $start->diffInDays($end);

        // Proporsi fase padi standar FAO (total ~120 hari)
        $proporsi = [
            'pengolahan_tanah' => 0.08,  // ~10 hari
            'tanam'            => 0.08,  // ~10 hari
            'vegetatif'        => 0.33,  // ~40 hari
            'generatif'        => 0.25,  // ~30 hari
            'pemasakan'        => 0.17,  // ~20 hari
            'panen'            => 0.09,  // ~10 hari
        ];

        $jadwal = [];
        $current = $start->copy();

        foreach ($proporsi as $fase => $pct) {
            $hariF  = (int) round($durasi * $pct);
            $faseEnd = $current->copy()->addDays($hariF - 1);
            if ($faseEnd > $end) $faseEnd = $end->copy();

            $jadwal[] = [
                'fase'   => $fase,
                'mulai'  => $current->format('Y-m-d'),
                'selesai'=> $faseEnd->format('Y-m-d'),
                'hari'   => $hariF,
            ];
            $current->addDays($hariF);
            if ($current > $end) break;
        }

        return $jadwal;
    }

    // Scope
    public function scopeBerjalan($q)  { return $q->where('status', 'berjalan'); }
    public function scopeByMt($q, $id) { return $q->where('musim_tanam_id', $id); }
}
