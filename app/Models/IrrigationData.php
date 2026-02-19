<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IrrigationData extends Model
{
    protected $table = 'irrigation_data';

    protected $fillable = [
        'tanggal',
        'eto',
        'etc',
        'curah_hujan',
        'kebutuhan_air'
    ];
}
