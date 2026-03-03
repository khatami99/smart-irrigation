<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MapFeature extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'map_layer_id', 'petak_id', 'nama',
        'deskripsi', 'luas_manual', 'geojson', 'warna'
    ];

    protected $casts = [
        'geojson' => 'array',
    ];

    public function layer()
    {
        return $this->belongsTo(MapLayer::class);
    }

    public function petak()
    {
        return $this->belongsTo(Petak::class);
    }

    // Format sebagai GeoJSON Feature
    public function toGeoJsonFeature(): array
    {
        return [
            'type'       => 'Feature',
            'id'         => $this->id,
            'geometry'   => $this->geojson,
            'properties' => [
                'id'          => $this->id,
                'nama'        => $this->nama,
                'deskripsi'   => $this->deskripsi,
                'luas_manual' => $this->luas_manual,
                'warna'       => $this->warna ?? $this->layer?->warna ?? '#4a7c6f',
                'layer_id'    => $this->map_layer_id,
                'layer_nama'  => $this->layer?->nama,
                'layer_tipe'  => $this->layer?->tipe,
                'petak_kode'  => $this->petak?->kode_petak,
                'petak_nama'  => $this->petak?->nama_petak,
            ],
        ];
    }
}
