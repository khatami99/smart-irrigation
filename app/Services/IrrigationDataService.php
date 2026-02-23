<?php

namespace App\Services;

class IrrigationDataService
{
    /**
     * Hitung ETo dengan metode Penman-Monteith (FAO-56)
     * Versi simplified menggunakan data yang tersedia
     */
    public static function hitungETo(
        float $suhuMax,
        float $suhuMin,
        float $kelembaban,
        float $kecepatanAngin,
        float $radiasiMatahari
    ): float {
        $suhuRata = ($suhuMax + $suhuMin) / 2;

        // Slope of saturation vapour pressure curve (kPa/°C)
        $delta = (4098 * (0.6108 * exp((17.27 * $suhuRata) / ($suhuRata + 237.3))))
                 / pow($suhuRata + 237.3, 2);

        // Psychrometric constant (kPa/°C) - asumsi ketinggian 0 mdpl
        $gamma = 0.0665;

        // Saturation vapour pressure (kPa)
        $es = (0.6108 * exp((17.27 * $suhuMax) / ($suhuMax + 237.3))
             + 0.6108 * exp((17.27 * $suhuMin) / ($suhuMin + 237.3))) / 2;

        // Actual vapour pressure (kPa)
        $ea = ($kelembaban / 100) * $es;

        // Net radiation (MJ/m²/day) - simplified: Rn ≈ 0.77 * Rs
        $Rn = 0.77 * $radiasiMatahari;

        // Soil heat flux (G) ≈ 0 untuk harian
        $G = 0;

        // ETo (mm/hari) - Penman-Monteith FAO-56
        $ETo = (0.408 * $delta * ($Rn - $G) + $gamma * (900 / ($suhuRata + 273)) * $kecepatanAngin * ($es - $ea))
               / ($delta + $gamma * (1 + 0.34 * $kecepatanAngin));

        return round($ETo, 2);
    }

    /**
     * Hitung ETc = ETo × Kc
     */
    public static function hitungETc(float $eto, float $kc): float
    {
        return round($eto * $kc, 2);
    }

    /**
     * Hitung Kebutuhan Air = ETc - Curah Hujan Efektif
     * Curah hujan efektif = 80% dari curah hujan (pendekatan umum irigasi)
     */
    public static function hitungKebutuhanAir(float $etc, float $curahHujan): float
    {
        $hujanEfektif = $curahHujan * 0.8;
        $kebutuhan = $etc - $hujanEfektif;
        return round(max($kebutuhan, 0), 2); // tidak boleh negatif
    }
}
