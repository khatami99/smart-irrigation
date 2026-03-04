<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MapLayer extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nama', 'tipe', 'kategori', 'warna', 'opacity',
        'keterangan', 'is_active', 'urutan'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'opacity'   => 'float',
    ];

    public function features()
    {
        return $this->hasMany(MapFeature::class);
    }

    // Konversi seluruh layer ke GeoJSON FeatureCollection
    public function toGeoJson(): array
    {
        return [
            'type'     => 'FeatureCollection',
            'features' => $this->features->map(fn($f) => $f->toGeoJsonFeature())->values()->toArray(),
        ];
    }
}
