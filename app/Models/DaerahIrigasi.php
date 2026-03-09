<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DaerahIrigasi extends Model
{
    use SoftDeletes;

    protected $table = 'daerah_irigasis';

    protected $fillable = [
        'map_feature_id',
        'kode',
        'nama',
        'jenis',
        'luas_total',
        'sumber_air',
        'penanggung_jawab',
        'latitude',
        'longitude',
        'status',
        'keterangan',
        // Parameter SKA
        'ska_padi_pengolahan',
        'ska_padi_pertumbuhan',
        'ska_palawija_banyak',
        'ska_palawija_sedikit',
        // Parameter DIP
        'faktor_tersier',
        // Parameter DIR
        'pct_kehilangan_air',
    ];

    protected $casts = [
        'luas_total'           => 'decimal:2',
        'ska_padi_pengolahan'  => 'decimal:3',
        'ska_padi_pertumbuhan' => 'decimal:3',
        'ska_palawija_banyak'  => 'decimal:3',
        'ska_palawija_sedikit' => 'decimal:3',
        'faktor_tersier'       => 'decimal:3',
        'pct_kehilangan_air'   => 'decimal:2',
    ];

    // ─── Scopes ─────────────────────────────────────────

    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    // ─── Helper jenis DI ────────────────────────────────

    public function isPermukaan(): bool
    {
        return $this->jenis === 'permukaan';
    }

    public function isRawa(): bool
    {
        return $this->jenis === 'rawa';
    }

    public function getLabelJenisAttribute(): string
    {
        return $this->jenis === 'permukaan'
            ? 'DIP (Irigasi Permukaan)'
            : 'DIR (Irigasi Rawa)';
    }

    // ─── Status RTT

    public function getStatusRttAttribute(?int $musimTanamId = null): string
    {
        $query = Rtt::whereHas('petak', fn($q) => $q->where('daerah_irigasi_id', $this->id));

        if ($musimTanamId) {
            $query->where('musim_tanam_id', $musimTanamId);
        }

        $rtts = $query->get();

        if ($rtts->isEmpty()) return 'belum ada RTT';

        if ($rtts->where('status', 'terlambat')->count() > 0) return 'terlambat';
        if ($rtts->where('status', 'berjalan')->count() > 0)  return 'berjalan';
        if ($rtts->where('status', 'rencana')->count() > 0)   return 'rencana';
        if ($rtts->where('status', 'selesai')->count() > 0)   return 'selesai';

        return 'belum ada RTT';
    }

    // ─── Relasi ─────────────────────────────────────────

    public function petaks()
    {
        return $this->hasMany(Petak::class);
    }

    public function mapFeature()
    {
        return $this->belongsTo(MapFeature::class);
    }
}
