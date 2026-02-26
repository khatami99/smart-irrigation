<?php
// database/seeders/IrrigationDataSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\IrrigationData;
use Carbon\Carbon;

class IrrigationDataSeeder extends Seeder
{
    public function run(): void
    {
        // Kosongkan dulu kalau ada data lama
        IrrigationData::truncate();

        $startDate = Carbon::create(2025, 1, 1);
        $endDate   = Carbon::create(2026, 2, 28);
        $current   = $startDate->copy();

        $this->command->info('Generating data iklim Kalimantan Tengah...');

        while ($current <= $endDate) {
            $bulan = $current->month;

            // ── Profil iklim Kalimantan Tengah per bulan ──
            // Musim hujan: Nov–Apr | Musim kemarau: Mei–Okt
            $profil = $this->getProfilIklim($bulan);

            // Generate nilai dengan variasi acak realistis
            $suhu_max = round($profil['suhu_max'] + $this->randFloat(-1.5, 1.5), 1);
            $suhu_min = round($profil['suhu_min'] + $this->randFloat(-1.0, 1.0), 1);
            $kelembaban    = round(min(98, max(60, $profil['kelembaban'] + $this->randFloat(-5, 5))), 1);
            $kecepatan_angin = round(max(0.3, $profil['angin'] + $this->randFloat(-0.3, 0.3)), 1);
            $radiasi_matahari = round(max(8, $profil['radiasi'] + $this->randFloat(-2, 2)), 1);

            // Curah hujan — probabilitas hujan sesuai musim
            $curah_hujan = 0;
            if (rand(1, 100) <= $profil['prob_hujan']) {
                $curah_hujan = round($this->randFloat(2, $profil['max_hujan']), 1);
            }

            // Hitung ETo (Penman-Monteith FAO-56)
            $eto = $this->hitungETo($suhu_max, $suhu_min, $kelembaban, $kecepatan_angin, $radiasi_matahari);

            // Kc padi sesuai fase (rotasi MT per 4 bulan)
            $kc = $this->getKc($current);

            $etc = round($eto * $kc, 2);
            $kebutuhan_air = round(max(0, $etc - ($curah_hujan * 0.8)), 2);

            IrrigationData::create([
                'tanggal'          => $current->format('Y-m-d'),
                'suhu_max'         => $suhu_max,
                'suhu_min'         => $suhu_min,
                'kelembaban'       => $kelembaban,
                'kecepatan_angin'  => $kecepatan_angin,
                'radiasi_matahari' => $radiasi_matahari,
                'curah_hujan'      => $curah_hujan,
                'kc'               => $kc,
                'eto'              => $eto,
                'etc'              => $etc,
                'kebutuhan_air'    => $kebutuhan_air,
            ]);

            $current->addDay();
        }

        $total = IrrigationData::count();
        $this->command->info("✅ Berhasil generate {$total} data iklim harian!");
    }

    // ── Profil iklim Kalimantan Tengah per bulan ──
    private function getProfilIklim(int $bulan): array
    {
        $profil = [
            1  => ['suhu_max'=>32.0,'suhu_min'=>23.5,'kelembaban'=>87,'angin'=>1.2,'radiasi'=>14.5,'prob_hujan'=>80,'max_hujan'=>35],
            2  => ['suhu_max'=>32.2,'suhu_min'=>23.5,'kelembaban'=>86,'angin'=>1.2,'radiasi'=>15.0,'prob_hujan'=>78,'max_hujan'=>32],
            3  => ['suhu_max'=>32.8,'suhu_min'=>23.8,'kelembaban'=>85,'angin'=>1.3,'radiasi'=>16.5,'prob_hujan'=>72,'max_hujan'=>28],
            4  => ['suhu_max'=>33.5,'suhu_min'=>24.0,'kelembaban'=>83,'angin'=>1.4,'radiasi'=>17.5,'prob_hujan'=>65,'max_hujan'=>22],
            5  => ['suhu_max'=>34.2,'suhu_min'=>24.2,'kelembaban'=>79,'angin'=>1.5,'radiasi'=>18.5,'prob_hujan'=>50,'max_hujan'=>18],
            6  => ['suhu_max'=>34.8,'suhu_min'=>24.0,'kelembaban'=>75,'angin'=>1.6,'radiasi'=>19.5,'prob_hujan'=>35,'max_hujan'=>12],
            7  => ['suhu_max'=>35.0,'suhu_min'=>23.8,'kelembaban'=>73,'angin'=>1.7,'radiasi'=>20.0,'prob_hujan'=>25,'max_hujan'=>10],
            8  => ['suhu_max'=>35.2,'suhu_min'=>24.0,'kelembaban'=>72,'angin'=>1.7,'radiasi'=>20.5,'prob_hujan'=>22,'max_hujan'=>8],
            9  => ['suhu_max'=>34.8,'suhu_min'=>24.2,'kelembaban'=>74,'angin'=>1.6,'radiasi'=>19.8,'prob_hujan'=>30,'max_hujan'=>12],
            10 => ['suhu_max'=>33.8,'suhu_min'=>24.0,'kelembaban'=>79,'angin'=>1.4,'radiasi'=>18.0,'prob_hujan'=>50,'max_hujan'=>20],
            11 => ['suhu_max'=>32.5,'suhu_min'=>23.8,'kelembaban'=>84,'angin'=>1.3,'radiasi'=>15.5,'prob_hujan'=>72,'max_hujan'=>30],
            12 => ['suhu_max'=>31.8,'suhu_min'=>23.5,'kelembaban'=>88,'angin'=>1.2,'radiasi'=>14.0,'prob_hujan'=>82,'max_hujan'=>38],
        ];

        return $profil[$bulan];
    }

    // ── Koefisien tanaman padi sesuai fase ──
    private function getKc(Carbon $date): float
    {
        // Siklus padi ~120 hari, ada 3 fase:
        // Pengolahan + awal: Kc 1.05
        // Pertengahan: Kc 1.20
        // Akhir/panen: Kc 0.75
        $dayOfYear = $date->dayOfYear;
        $fase = $dayOfYear % 120;

        if ($fase < 30)       return 1.05; // pengolahan tanah & awal
        elseif ($fase < 80)   return 1.20; // vegetatif & generatif
        elseif ($fase < 110)  return 0.90; // pemasakan
        else                  return 0.75; // panen & bero
    }

    // ── Hitung ETo Penman-Monteith ──
    private function hitungETo($tmax, $tmin, $rh, $ws, $rs): float
    {
        $tmean = ($tmax + $tmin) / 2;
        $delta = (4098 * (0.6108 * exp((17.27 * $tmean) / ($tmean + 237.3))))
                 / pow($tmean + 237.3, 2);
        $gamma = 0.0665;
        $esTmax = 0.6108 * exp((17.27 * $tmax) / ($tmax + 237.3));
        $esTmin = 0.6108 * exp((17.27 * $tmin) / ($tmin + 237.3));
        $es  = ($esTmax + $esTmin) / 2;
        $ea  = ($rh / 100) * $es;
        $Rn  = 0.77 * $rs;
        $ETo = (0.408 * $delta * $Rn + $gamma * (900 / ($tmean + 273)) * $ws * ($es - $ea))
               / ($delta + $gamma * (1 + 0.34 * $ws));

        return round(max(0, $ETo), 2);
    }

    private function randFloat(float $min, float $max): float
    {
        return $min + mt_rand() / mt_getrandmax() * ($max - $min);
    }
}
