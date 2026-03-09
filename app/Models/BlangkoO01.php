<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;

class BlangkoO01 extends Model
{
    // use SoftDeletes;

    protected $table = 'blangko_o01s';

    protected $fillable = [
        'daerah_irigasi_id',
        'musim_tanam_id',
        'user_id',
        'luas_padi_usulan',
        'luas_palawija_usulan',
        'luas_tebu_usulan',
        'luas_padi_disetujui',
        'luas_palawija_disetujui',
        'luas_tebu_disetujui',
        'status',
        'keterangan',
    ];

    protected $casts = [
        'luas_padi_usulan'        => 'decimal:2',
        'luas_palawija_usulan'    => 'decimal:2',
        'luas_tebu_usulan'        => 'decimal:2',
        'luas_padi_disetujui'     => 'decimal:2',
        'luas_palawija_disetujui' => 'decimal:2',
        'luas_tebu_disetujui'     => 'decimal:2',
    ];

    // ── Relasi ──────────────────────────────────────────────────
    public function daerahIrigasi()
    {
        return $this->belongsTo(DaerahIrigasi::class);
    }

    public function musimTanam()
    {
        return $this->belongsTo(MusimTanam::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ── Helper: luas total usulan ────────────────────────────────
    public function getTotalUsulanAttribute(): float
    {
        return (float) $this->luas_padi_usulan
             + (float) $this->luas_palawija_usulan
             + (float) $this->luas_tebu_usulan;
    }

    // ── Helper: luas total disetujui ────────────────────────────
    public function getTotalDisetujuiAttribute(): float
    {
        return (float) ($this->luas_padi_disetujui ?? $this->luas_padi_usulan)
             + (float) ($this->luas_palawija_disetujui ?? $this->luas_palawija_usulan)
             + (float) ($this->luas_tebu_disetujui ?? $this->luas_tebu_usulan);
    }

    // ── Helper: luas efektif (pakai disetujui kalau ada, fallback usulan) ──
    public function getLuasPadiEfektifAttribute(): float
    {
        return (float) ($this->luas_padi_disetujui ?? $this->luas_padi_usulan);
    }

    public function getLuasPalawijaEfektifAttribute(): float
    {
        return (float) ($this->luas_palawija_disetujui ?? $this->luas_palawija_usulan);
    }

    public function getLuasTebuEfektifAttribute(): float
    {
        return (float) ($this->luas_tebu_disetujui ?? $this->luas_tebu_usulan);
    }

    // ── Hitung kebutuhan air (l/det) berdasarkan SKA dari DI ────
    // Menggunakan nilai SKA dari tabel daerah_irigasi (O-05 standar)
    public function hitungKebutuhanAir(string $fase = 'pertumbuhan'): float
    {
        $di = $this->daerahIrigasi;
        if (!$di) return 0;

        if ($di->isRawa()) {
            // DIR: kehilangan air dalam persen dari luas
            $pct = ($di->pct_kehilangan_air ?? 35) / 100;
            return round($this->total_disetujui * $pct, 2);
        }

        // DIP: SKA × luas × Faktor Tersier
        $faktorTersier = (float) ($di->faktor_tersier ?? 0.83);

        $skaPadi = match($fase) {
            'pengolahan' => (float) ($di->ska_padi_pengolahan ?? 1.25),
            'panen'      => 0,
            default      => (float) ($di->ska_padi_pertumbuhan ?? 0.725),
        };

        $skaPalawija = (float) ($di->ska_palawija_banyak ?? 0.300);
        // Tebu pakai SKA palawija banyak air sebagai pendekatan
        $skaTebu     = (float) ($di->ska_padi_pertumbuhan ?? 0.725);

        $kebutuhanPadi     = $this->luas_padi_efektif     * $skaPadi     * $faktorTersier;
        $kebutuhanPalawija = $this->luas_palawija_efektif * $skaPalawija * $faktorTersier;
        $kebutuhanTebu     = $this->luas_tebu_efektif     * $skaTebu     * $faktorTersier;

        return round($kebutuhanPadi + $kebutuhanPalawija + $kebutuhanTebu, 2);
    }

    // ── Label status ─────────────────────────────────────────────
    public function getLabelStatusAttribute(): string
    {
        return match($this->status) {
            'usulan'   => 'Usulan',
            'disetujui'=> 'Disetujui',
            'revisi'   => 'Perlu Revisi',
            default    => $this->status,
        };
    }

    // ── Scopes ───────────────────────────────────────────────────
    public function scopeDisetujui($query)
    {
        return $query->where('status', 'disetujui');
    }

    public function scopeByMT($query, $musimTanamId)
    {
        return $query->where('musim_tanam_id', $musimTanamId);
    }
}
