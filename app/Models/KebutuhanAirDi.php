<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KebutuhanAirDi extends Model
{
    protected $table = 'kebutuhan_air_di';

    protected $fillable = [
        'daerah_irigasi_id',
        'musim_tanam_id',
        'blangko_o01_id',
        'tahun',
        'bulan',
        'dekade',
        'luas_padi',
        'luas_palawija',
        'luas_tebu',
        'varietas_padi',
        'eto_dekade',
        'ch_dekade',
        'kc_padi',
        'kc_palawija',
        'kc_tebu',
        'etc_padi',
        'etc_palawija',
        'etc_tebu',
        're_dekade',
        'nfr_padi',
        'nfr_palawija',
        'nfr_tebu',
        'kebutuhan_padi',
        'kebutuhan_palawija',
        'kebutuhan_tebu',
        'kebutuhan_total',
        'efisiensi',
    ];

    protected $casts = [
        'luas_padi'         => 'float',
        'luas_palawija'     => 'float',
        'luas_tebu'         => 'float',
        'eto_dekade'        => 'float',
        'ch_dekade'         => 'float',
        'kc_padi'           => 'float',
        'kc_palawija'       => 'float',
        'kc_tebu'           => 'float',
        'etc_padi'          => 'float',
        'etc_palawija'      => 'float',
        'etc_tebu'          => 'float',
        're_dekade'         => 'float',
        'nfr_padi'          => 'float',
        'nfr_palawija'      => 'float',
        'nfr_tebu'          => 'float',
        'kebutuhan_padi'    => 'float',
        'kebutuhan_palawija'=> 'float',
        'kebutuhan_tebu'    => 'float',
        'kebutuhan_total'   => 'float',
        'efisiensi'         => 'float',
    ];

    // ── Relasi ──────────────────────────────────────────
    public function daerahIrigasi()
    {
        return $this->belongsTo(DaerahIrigasi::class);
    }

    public function musimTanam()
    {
        return $this->belongsTo(MusimTanam::class);
    }

    public function blangkoO01()
    {
        return $this->belongsTo(BlangkoO01::class);
    }

    // ── Helper: label dekade ─────────────────────────────
    public function getLabelDekadeAttribute(): string
    {
        $bulanNama = [
            1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'Mei',6=>'Jun',
            7=>'Jul',8=>'Ags',9=>'Sep',10=>'Okt',11=>'Nov',12=>'Des'
        ];
        return ($bulanNama[$this->bulan] ?? $this->bulan) . ' ' . $this->tahun . ' Dek-' . $this->dekade;
    }
}
