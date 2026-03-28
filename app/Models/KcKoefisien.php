<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KcKoefisien extends Model
{
    protected $table = 'kc_koefisien';

    protected $fillable = [
        'komoditas',
        'fase_ke',
        'nama_fase',
        'kc',
        'durasi_dekade',
    ];

    protected $casts = [
        'kc'            => 'float',
        'fase_ke'       => 'integer',
        'durasi_dekade' => 'integer',
    ];

    /**
     * Ambil nilai Kc berdasarkan komoditas dan fase ke-berapa.
     * Kalau fase melebihi data yang ada, pakai fase terakhir.
     */
    public static function getKc(string $komoditas, int $faseKe): float
    {
        $record = static::where('komoditas', $komoditas)
            ->where('fase_ke', $faseKe)
            ->first();

        if ($record) return $record->kc;

        // Fallback: pakai fase terakhir yang ada
        $last = static::where('komoditas', $komoditas)
            ->orderBy('fase_ke', 'desc')
            ->first();

        return $last ? $last->kc : 1.0;
    }

    /**
     * Hitung fase ke-berapa berdasarkan dekade ke-berapa sejak awal MT.
     * dekadeKe = urutan dekade sejak MT mulai (1, 2, 3, ...)
     */
    public static function getFaseKe(string $komoditas, int $dekadeKe): int
    {
        $records = static::where('komoditas', $komoditas)
            ->orderBy('fase_ke')
            ->get();

        $accumulated = 0;
        foreach ($records as $record) {
            $accumulated += $record->durasi_dekade;
            if ($dekadeKe <= $accumulated) {
                return $record->fase_ke;
            }
        }

        // Lewat panen — kembalikan fase terakhir
        return $records->last()?->fase_ke ?? 1;
    }
}
