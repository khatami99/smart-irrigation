<?php
// app/Models/Petak.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Petak extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_petak',
        'nama_petak',
        'luas_area',
        'lokasi_wilayah',
        'pintu_air',
        'penanggung_jawab',
        'status',
        'keterangan',
    ];

    // Scope: hanya petak aktif
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    // Format luas area
    public function getLuasFormattedAttribute(): string
    {
        return number_format($this->luas_area, 2) . ' ha';
    }
}
