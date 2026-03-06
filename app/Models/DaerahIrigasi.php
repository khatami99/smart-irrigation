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

    public function petaks()
    {
        return $this->hasMany(Petak::class);
    }

    public function mapFeature()
    {
        return $this->belongsTo(MapFeature::class);
    }

    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    // Agregat status RTT dari semua petak dalam DI ini
    public function getStatusRttAttribute(?int $musimTanamId = null): string
    {
        $query = Rtt::whereHas('petak', fn($q) => $q->where('daerah_irigasi_id', $this->id));

        if ($musimTanamId) {
            $query->where('musim_tanam_id', $musimTanamId);
        }

        $rtts = $query->get();

        if ($rtts->isEmpty()) return 'belum ada RTT';

        // Prioritas: terlambat > berjalan > rencana > selesai
        if ($rtts->where('status', 'terlambat')->count() > 0) return 'terlambat';
        if ($rtts->where('status', 'berjalan')->count() > 0)  return 'berjalan';
        if ($rtts->where('status', 'rencana')->count() > 0)   return 'rencana';
        if ($rtts->where('status', 'selesai')->count() > 0)   return 'selesai';

        return 'belum ada RTT';
    }
}
