<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RttDir extends Model
{
    protected $table = 'rtt_dirs';

    protected $fillable = [
        'petak_id',
        'musim_tanam_id',
        'daerah_irigasi_id',
        'user_id',
        'bulan',
        'tahun',
        'rencana_padi',
        'realisasi_padi',
        'rencana_palawija',
        'realisasi_palawija',
        'rencana_tanaman_keras',
        'realisasi_tanaman_keras',
        'rencana_bera',
        'realisasi_bera',
        'keterangan',
    ];

    protected $casts = [
        'rencana_padi'            => 'decimal:2',
        'realisasi_padi'          => 'decimal:2',
        'rencana_palawija'        => 'decimal:2',
        'realisasi_palawija'      => 'decimal:2',
        'rencana_tanaman_keras'   => 'decimal:2',
        'realisasi_tanaman_keras' => 'decimal:2',
        'rencana_bera'            => 'decimal:2',
        'realisasi_bera'          => 'decimal:2',
    ];

    public function petak()        { return $this->belongsTo(Petak::class); }
    public function musimTanam()   { return $this->belongsTo(MusimTanam::class); }
    public function daerahIrigasi(){ return $this->belongsTo(DaerahIrigasi::class); }
    public function user()         { return $this->belongsTo(User::class); }

    public function getNamaBulanAttribute(): string
    {
        return \Carbon\Carbon::createFromDate($this->tahun, $this->bulan, 1)
            ->translatedFormat('F');
    }
}
