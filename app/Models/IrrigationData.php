<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IrrigationData extends Model
{
    protected $table = 'irrigation_data';

    protected $fillable = [
        'tanggal',
        'suhu_max',
        'suhu_min',
        'kelembaban',
        'kecepatan_angin',
        'radiasi_matahari',
        'kc',
        'eto',
        'etc',
        'curah_hujan',
        'kebutuhan_air',
    ];
}
