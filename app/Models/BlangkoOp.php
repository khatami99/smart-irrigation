<?php
// app/Models/BlangkoOp.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BlangkoOp extends Model
{
    use HasFactory;

    protected $fillable = [
        'petak_id', 'musim_tanam_id', 'user_id',
        'tahun', 'bulan', 'dekade',
        'debit_rencana', 'debit_realisasi',
        'tinggi_muka_air',
        'luas_rencana', 'luas_realisasi',
        'fase_pertumbuhan',
        'kondisi_saluran', 'kondisi_bangunan', 'catatan_kondisi',
        'curah_hujan', 'keterangan',
    ];

    // ── Relasi ──
    public function petak()      { return $this->belongsTo(Petak::class); }
    public function musimTanam() { return $this->belongsTo(MusimTanam::class); }
    public function user()       { return $this->belongsTo(User::class); }

    // ── Helpers ──

    // Label dekade yang mudah dibaca
    public function getPeriodeAttribute(): string
    {
        $namaBulan = [
            1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'Mei',6=>'Jun',
            7=>'Jul',8=>'Agu',9=>'Sep',10=>'Okt',11=>'Nov',12=>'Des'
        ];
        return "Dekade {$this->dekade} — {$namaBulan[$this->bulan]} {$this->tahun}";
    }

    // Rentang tanggal dekade
    public function getRentangTanggalAttribute(): string
    {
        $awal = match($this->dekade) {
            'I'   => 1,
            'II'  => 11,
            'III' => 21,
        };
        $akhir = match($this->dekade) {
            'I'   => 10,
            'II'  => 20,
            'III' => cal_days_in_month(CAL_GREGORIAN, $this->bulan, $this->tahun),
        };
        return "{$awal}–{$akhir} " . $this->periode;
    }

    // Label fase pertumbuhan
    public function getFaseLabelAttribute(): string
    {
        return match($this->fase_pertumbuhan) {
            'pengolahan_tanah' => 'Pengolahan Tanah',
            'tanam'            => 'Tanam',
            'vegetatif'        => 'Vegetatif',
            'generatif'        => 'Generatif',
            'panen'            => 'Panen',
            'bero'             => 'Bero',
            default            => '—',
        };
    }

    // Efisiensi debit (realisasi/rencana %)
    public function getEfisiensiDebitAttribute(): ?float
    {
        if (!$this->debit_rencana || $this->debit_rencana == 0) return null;
        return round(($this->debit_realisasi / $this->debit_rencana) * 100, 1);
    }

    // Efisiensi luas (realisasi/rencana %)
    public function getEfisiensiLuasAttribute(): ?float
    {
        if (!$this->luas_rencana || $this->luas_rencana == 0) return null;
        return round(($this->luas_realisasi / $this->luas_rencana) * 100, 1);
    }

    // Warna kondisi
    public static function warnaKondisi(string $kondisi): string
    {
        return match($kondisi) {
            'baik'         => 'leaf',
            'rusak_ringan' => 'clay',
            'rusak_berat'  => 'red',
            default        => 'textlt',
        };
    }

    // Scope filter
    public function scopeByDekade($query, $tahun, $bulan, $dekade)
    {
        return $query->where('tahun', $tahun)->where('bulan', $bulan)->where('dekade', $dekade);
    }
}
