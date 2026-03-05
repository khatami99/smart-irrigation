<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Saluran extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'map_feature_id', 'nama', 'tipe',
        'panjang_km', 'kondisi', 'penanggung_jawab',
        'latitude', 'longitude', 'keterangan',
    ];

    public function mapFeature()
    {
        return $this->belongsTo(MapFeature::class);
    }

    // Warna kondisi untuk peta
    public function getWarnaKondisiAttribute(): string
    {
        return match($this->kondisi) {
            'baik'   => '#4a7c6f',
            'sedang' => '#c4895a',
            'rusak'  => '#b94a3c',
            default  => '#7a6355',
        };
    }
}
