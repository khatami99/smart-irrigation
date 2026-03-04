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
        return $this->belongsTo(MapLayer::class, 'map_layer_id');
    }

    public function petak()
    {
        return $this->hasOne(Petak::class, 'map_feature_id');
    }

    public function saluran()
    {
        return $this->hasOne(Saluran::class, 'map_feature_id');
    }

    // Deteksi apakah feature ini adalah petak atau saluran
    public function getTipeDataAttribute(): string
    {
        if ($this->layer->tipe === 'polyline') return 'saluran';
        return 'petak';
    }

    // Format sebagai GeoJSON Feature
    public function toGeoJsonFeature(): array
    {
        $petak   = $this->petak;
        $saluran = $this->saluran;

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
                'layer_kategori' => $this->layer?->kategori,

                // Data petak
                'petak_kode'     => $petak?->kode_petak,
                'petak_nama'     => $petak?->nama_petak,
                'petak_pintu_air'=> $petak?->pintu_air,
                'petak_pj'       => $petak?->penanggung_jawab,
                'petak_status'   => $petak?->status,

                // Data saluran
                'saluran_tipe'    => $saluran?->tipe,
                'saluran_panjang' => $saluran?->panjang_km,
                'saluran_kondisi' => $saluran?->kondisi,
                'saluran_pj'      => $saluran?->penanggung_jawab,
            ],
        ];
    }
}
