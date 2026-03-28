<?php

namespace App\Services;

use App\Models\BlangkoO01;
use App\Models\DaerahIrigasi;
use App\Models\IrrigationData;
use App\Models\KcKoefisien;
use App\Models\KebutuhanAirDi;
use App\Models\MusimTanam;
use Carbon\Carbon;

class KpSatuService
{
    const PERKOLASI       = 2.0;
    const WLR_PER_DEKADE  = 3.3;
    const KONVERSI        = 8.64;
    const EFISIENSI_DIP   = 0.83;
    const EFISIENSI_DIR   = 0.65;
    const HUJAN_EFEKTIF_K = 0.70;

    /**
     * Hitung kebutuhan air untuk SEMUA dekade dalam MT sekaligus.
     * Dipanggil setiap kali O-01 disimpan/diupdate.
     */
    public function hitungDariO01(BlangkoO01 $o01): array
    {
        $di = DaerahIrigasi::find($o01->daerah_irigasi_id);
        $mt = MusimTanam::find($o01->musim_tanam_id);

        if (!$di || !$mt) return [];

        $efisiensi  = ($di->jenis_di === 'rawa') ? self::EFISIENSI_DIR : self::EFISIENSI_DIP;
        $varPadi    = $o01->varietas_padi ?? 'padi_unggul';
        $dekades    = $this->generateDekades($mt);
        $results    = [];

        foreach ($dekades as $index => $dekade) {
            $dekadeKe = $index + 1;

            [$etoRata, $chTotal] = $this->aggregateIklim(
                $dekade['tahun'],
                $dekade['bulan'],
                $dekade['dekade']
            );

            // Kc per fase
            $kcPadi     = KcKoefisien::getKc($varPadi,   KcKoefisien::getFaseKe($varPadi,    $dekadeKe));
            $kcPalawija = KcKoefisien::getKc('palawija', KcKoefisien::getFaseKe('palawija',  $dekadeKe));
            $kcTebu     = KcKoefisien::getKc('tebu',     KcKoefisien::getFaseKe('tebu',       $dekadeKe));

            // ETc = Kc × ETo
            $etcPadi     = $kcPadi     * ($etoRata ?? 0);
            $etcPalawija = $kcPalawija * ($etoRata ?? 0);
            $etcTebu     = $kcTebu     * ($etoRata ?? 0);

            // Re = 0.7 × CH/10 (mm/hari)
            $re  = self::HUJAN_EFEKTIF_K * (($chTotal ?? 0) / 10);
            $wlr = self::WLR_PER_DEKADE / 10;

            // NFR
            $nfrPadi     = max(0, $etcPadi     + self::PERKOLASI + $wlr - $re);
            $nfrPalawija = max(0, $etcPalawija + self::PERKOLASI + $wlr - $re);
            $nfrTebu     = max(0, $etcTebu     + self::PERKOLASI + $wlr - $re);

            // Kebutuhan total (lt/det)
            $kebutuhanPadi     = ($nfrPadi     / self::KONVERSI / $efisiensi) * ($o01->luas_padi_usulan     ?? 0);
            $kebutuhanPalawija = ($nfrPalawija / self::KONVERSI / $efisiensi) * ($o01->luas_palawija_usulan ?? 0);
            $kebutuhanTebu     = ($nfrTebu     / self::KONVERSI / $efisiensi) * ($o01->luas_tebu_usulan     ?? 0);
            $kebutuhanTotal    = $kebutuhanPadi + $kebutuhanPalawija + $kebutuhanTebu;

            $record = KebutuhanAirDi::updateOrCreate(
                [
                    'daerah_irigasi_id' => $di->id,
                    'musim_tanam_id'    => $mt->id,
                    'tahun'             => $dekade['tahun'],
                    'bulan'             => $dekade['bulan'],
                    'dekade'            => $dekade['dekade'],
                ],
                [
                    'blangko_o01_id'     => $o01->id,
                    'luas_padi'          => $o01->luas_padi_usulan     ?? 0,
                    'luas_palawija'      => $o01->luas_palawija_usulan ?? 0,
                    'luas_tebu'          => $o01->luas_tebu_usulan     ?? 0,
                    'varietas_padi'      => $varPadi,
                    'eto_dekade'         => $etoRata,
                    'ch_dekade'          => $chTotal,
                    'kc_padi'            => $kcPadi,
                    'kc_palawija'        => $kcPalawija,
                    'kc_tebu'            => $kcTebu,
                    'etc_padi'           => round($etcPadi, 3),
                    'etc_palawija'       => round($etcPalawija, 3),
                    'etc_tebu'           => round($etcTebu, 3),
                    're_dekade'          => round($re, 3),
                    'nfr_padi'           => round($nfrPadi, 3),
                    'nfr_palawija'       => round($nfrPalawija, 3),
                    'nfr_tebu'           => round($nfrTebu, 3),
                    'kebutuhan_padi'     => round($kebutuhanPadi, 3),
                    'kebutuhan_palawija' => round($kebutuhanPalawija, 3),
                    'kebutuhan_tebu'     => round($kebutuhanTebu, 3),
                    'kebutuhan_total'    => round($kebutuhanTotal, 3),
                    'efisiensi'          => $efisiensi,
                ]
            );

            $results[] = $record;
        }

        return $results;
    }

    /**
     * Generate semua dekade dari tanggal_mulai sampai tanggal_selesai MT.
     * Return array of ['tahun' => x, 'bulan' => x, 'dekade' => 'I'|'II'|'III']
     */
    private function generateDekades(MusimTanam $mt): array
    {
        $dekades = [];
        $current = Carbon::parse($mt->tanggal_mulai)->startOfDay();
        $akhir   = Carbon::parse($mt->tanggal_selesai)->startOfDay();

        while ($current->lte($akhir)) {
            $tahun = $current->year;
            $bulan = $current->month;
            $hari  = $current->day;

            if ($hari <= 10) {
                $label = 'I';
                $next  = Carbon::createFromDate($tahun, $bulan, 11);
            } elseif ($hari <= 20) {
                $label = 'II';
                $next  = Carbon::createFromDate($tahun, $bulan, 21);
            } else {
                $label = 'III';
                $next  = Carbon::createFromDate($tahun, $bulan, 1)->addMonth();
            }

            $dekades[] = [
                'tahun'  => $tahun,
                'bulan'  => $bulan,
                'dekade' => $label,
            ];

            $current = $next;
        }

        return $dekades;
    }

    /**
     * Aggregate ETo rata-rata dan CH total untuk satu dekade.
     */
    private function aggregateIklim(int $tahun, int $bulan, string $dekade): array
    {
        [$tglMulai, $tglAkhir] = $this->rentangTanggal($tahun, $bulan, $dekade);

        $result = IrrigationData::whereBetween('tanggal', [$tglMulai, $tglAkhir])
            ->selectRaw('AVG(eto) as eto_rata, SUM(curah_hujan) as ch_total')
            ->first();

        $eto = $result?->eto_rata ? round((float) $result->eto_rata, 3) : null;
        $ch  = $result?->ch_total ? round((float) $result->ch_total, 2) : null;

        return [$eto, $ch];
    }

    /**
     * Rentang tanggal untuk satu dekade.
     */
    private function rentangTanggal(int $tahun, int $bulan, string $dekade): array
    {
        $lastDay = Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth()->day;

        return match ($dekade) {
            'I'     => ["$tahun-$bulan-01", "$tahun-$bulan-10"],
            'II'    => ["$tahun-$bulan-11", "$tahun-$bulan-20"],
            'III'   => ["$tahun-$bulan-21", "$tahun-$bulan-$lastDay"],
            default => ["$tahun-$bulan-01", "$tahun-$bulan-10"],
        };
    }
}
