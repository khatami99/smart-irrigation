<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DaerahIrigasi extends Model
{
    use SoftDeletes;

    protected $table = 'daerah_irigasis';

    protected $fillable = [
        'map_feature_id', 'kode', 'nama', 'luas_total',
        'sumber_air', 'penanggung_jawab',
        'latitude', 'longitude', 'status', 'keterangan',
    ];

    public function mapFeature()
    {
        return $this->belongsTo(MapFeature::class);
    }

    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }
}
