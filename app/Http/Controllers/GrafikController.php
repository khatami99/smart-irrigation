<?php
// app/Http/Controllers/GrafikController.php
// VERSI FIX — label chart blangko OP pakai label sendiri

namespace App\Http\Controllers;

use App\Models\IrrigationData;
use App\Models\BlangkoOp;
use App\Models\MusimTanam;
use Illuminate\Http\Request;

class GrafikController extends Controller
{
    public function index(Request $request)
    {
        $musimTanams = MusimTanam::orderBy('tanggal_mulai', 'desc')->get();
        $mtAktif     = MusimTanam::berjalan()->first();

        $mode  = $request->get('mode', 'dekade');
        $mtId  = $request->get('musim_tanam_id', $mtAktif?->id);
        $tahun = $request->get('tahun', date('Y'));
        $bulan = $request->get('bulan');

        $kebutuhanData = $this->getKebutuhanData($mode, $tahun, $bulan, $mtId);
        $blangkoData   = $this->getBlangkoData($mode, $tahun, $bulan, $mtId);

        $years = IrrigationData::selectRaw('YEAR(tanggal) as year')
            ->distinct()->orderBy('year', 'desc')->pluck('year');

        return view('grafik.index', compact(
            'musimTanams', 'mtAktif', 'mode', 'mtId', 'tahun', 'bulan',
            'kebutuhanData', 'blangkoData', 'years'
        ));
    }

    public function data(Request $request)
    {
        $mode  = $request->get('mode', 'dekade');
        $mtId  = $request->get('musim_tanam_id');
        $tahun = $request->get('tahun', date('Y'));
        $bulan = $request->get('bulan');
        $limit = $request->get('limit'); // tambah ini

        $kebutuhanData = $this->getKebutuhanData($mode, $tahun, $bulan, $mtId, $limit);
        $blangkoData   = $this->getBlangkoData($mode, $tahun, $bulan, $mtId);

        return response()->json([
            // Chart 1 — pakai label kebutuhan
            'labels'          => $kebutuhanData['labels'],
            'kebutuhan'       => $kebutuhanData['kebutuhan'],
            'eto'             => $kebutuhanData['eto'],
            'etc'             => $kebutuhanData['etc'],

            // Chart 2, 3, 4 — pakai label blangko (berbeda!)
            'blangko_labels'     => $blangkoData['labels'],
            'debit_rencana'      => $blangkoData['debit_rencana'],
            'debit_realisasi'    => $blangkoData['debit_realisasi'],
            'luas_rencana'       => $blangkoData['luas_rencana'],
            'luas_realisasi'     => $blangkoData['luas_realisasi'],
            'curah_hujan'        => $blangkoData['curah_hujan'],
        ]);
    }

    // ──────────────────────────────────────────────
    private function getKebutuhanData($mode, $tahun, $bulan, $mtId, $limit = null)
    {
        $query = IrrigationData::query();

        if ($mtId) {
            $mt = MusimTanam::find($mtId);
            if ($mt) {
                $query->whereBetween('tanggal', [
                    $mt->tanggal_mulai->format('Y-m-d'),
                    $mt->tanggal_selesai->format('Y-m-d'),
                ]);
            }
        } else {
            $query->whereYear('tanggal', $tahun);
            if ($bulan) $query->whereMonth('tanggal', $bulan);
        }

        $labels = []; $kebutuhan = []; $eto = []; $etc = [];
        $namaBulan = ['','Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
        $namaBulanPanjang = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];

        switch ($mode) {
            case 'harian':
                if ($limit) $query->orderBy('tanggal', 'desc')->limit($limit);
                else $query->orderBy('tanggal', 'asc');

                // $data = $query->orderBy('tanggal')->get();
                $data = $limit ? $query->get()->reverse() : $query->get();
                foreach ($data as $d) {
                    $labels[]    = $d->tanggal;
                    $kebutuhan[] = round($d->kebutuhan_air, 2);
                    $eto[]       = round($d->eto, 2);
                    $etc[]       = round($d->etc, 2);
                }
                break;

            case 'dekade':
                $data = $query->selectRaw("
                    YEAR(tanggal) as tahun, MONTH(tanggal) as bulan,
                    CASE WHEN DAY(tanggal)<=10 THEN 'I' WHEN DAY(tanggal)<=20 THEN 'II' ELSE 'III' END as dekade,
                    AVG(kebutuhan_air) as avg_kebutuhan, AVG(eto) as avg_eto, AVG(etc) as avg_etc
                ")->groupByRaw("tahun, bulan, dekade")
                  ->orderByRaw("tahun, bulan, FIELD(dekade,'I','II','III')")->get();

                foreach ($data as $d) {
                    $labels[]    = "Dek.{$d->dekade} {$namaBulan[$d->bulan]} {$d->tahun}";
                    $kebutuhan[] = round($d->avg_kebutuhan, 2);
                    $eto[]       = round($d->avg_eto, 2);
                    $etc[]       = round($d->avg_etc, 2);
                }
                break;

            case 'bulanan':
                $data = $query->selectRaw("
                    YEAR(tanggal) as tahun, MONTH(tanggal) as bulan,
                    AVG(kebutuhan_air) as avg_kebutuhan, AVG(eto) as avg_eto, AVG(etc) as avg_etc
                ")->groupByRaw("tahun, bulan")->orderByRaw("tahun, bulan")->get();

                foreach ($data as $d) {
                    $labels[]    = "{$namaBulanPanjang[$d->bulan]} {$d->tahun}";
                    $kebutuhan[] = round($d->avg_kebutuhan, 2);
                    $eto[]       = round($d->avg_eto, 2);
                    $etc[]       = round($d->avg_etc, 2);
                }
                break;

            case 'musim':
                $musims = MusimTanam::orderBy('tanggal_mulai')->get();
                foreach ($musims as $mt) {
                    $avg = IrrigationData::whereBetween('tanggal', [
                        $mt->tanggal_mulai->format('Y-m-d'),
                        $mt->tanggal_selesai->format('Y-m-d'),
                    ])->selectRaw("AVG(kebutuhan_air) as k, AVG(eto) as e, AVG(etc) as c")->first();
                    if ($avg && $avg->k) {
                        $labels[]    = $mt->nama_mt;
                        $kebutuhan[] = round($avg->k, 2);
                        $eto[]       = round($avg->e, 2);
                        $etc[]       = round($avg->c, 2);
                    }
                }
                break;
        }

        return compact('labels', 'kebutuhan', 'eto', 'etc');
    }

    private function getBlangkoData($mode, $tahun, $bulan, $mtId)
    {
        $labels = []; $debitR = []; $debitA = []; $luasR = []; $luasA = []; $ch = [];
        $namaBulan = ['','Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
        $namaBulanPanjang = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];

        $query = BlangkoOp::query();
        if ($mtId) {
            $query->where('musim_tanam_id', $mtId);
        } else {
            $query->where('tahun', $tahun);
            if ($bulan) $query->where('bulan', $bulan);
        }

        switch ($mode) {
            case 'harian':
            case 'dekade':
                $data = $query->selectRaw("
                    tahun, bulan, dekade,
                    AVG(debit_rencana) as dr, AVG(debit_realisasi) as da,
                    AVG(luas_rencana) as lr, AVG(luas_realisasi) as la,
                    AVG(curah_hujan) as ch
                ")->groupBy('tahun', 'bulan', 'dekade')
                  ->orderByRaw("tahun, bulan, FIELD(dekade,'I','II','III')")->get();

                foreach ($data as $d) {
                    $labels[] = "Dek.{$d->dekade} {$namaBulan[$d->bulan]} {$d->tahun}";
                    $debitR[] = round($d->dr ?? 0, 2);
                    $debitA[] = round($d->da ?? 0, 2);
                    $luasR[]  = round($d->lr ?? 0, 2);
                    $luasA[]  = round($d->la ?? 0, 2);
                    $ch[]     = round($d->ch ?? 0, 1);
                }
                break;

            case 'bulanan':
                $data = $query->selectRaw("
                    tahun, bulan,
                    AVG(debit_rencana) as dr, AVG(debit_realisasi) as da,
                    AVG(luas_rencana) as lr, AVG(luas_realisasi) as la,
                    AVG(curah_hujan) as ch
                ")->groupBy('tahun', 'bulan')->orderByRaw("tahun, bulan")->get();

                foreach ($data as $d) {
                    $labels[] = "{$namaBulanPanjang[$d->bulan]} {$d->tahun}";
                    $debitR[] = round($d->dr ?? 0, 2);
                    $debitA[] = round($d->da ?? 0, 2);
                    $luasR[]  = round($d->lr ?? 0, 2);
                    $luasA[]  = round($d->la ?? 0, 2);
                    $ch[]     = round($d->ch ?? 0, 1);
                }
                break;

            case 'musim':
                $musims = MusimTanam::orderBy('tanggal_mulai')->get();
                foreach ($musims as $mt) {
                    $avg = BlangkoOp::where('musim_tanam_id', $mt->id)
                        ->selectRaw("AVG(debit_rencana) as dr, AVG(debit_realisasi) as da, AVG(luas_rencana) as lr, AVG(luas_realisasi) as la, AVG(curah_hujan) as ch")
                        ->first();
                    $labels[] = $mt->nama_mt;
                    $debitR[] = round($avg->dr ?? 0, 2);
                    $debitA[] = round($avg->da ?? 0, 2);
                    $luasR[]  = round($avg->lr ?? 0, 2);
                    $luasA[]  = round($avg->la ?? 0, 2);
                    $ch[]     = round($avg->ch ?? 0, 1);
                }
                break;
        }

        return [
            'labels'          => $labels,
            'debit_rencana'   => $debitR,
            'debit_realisasi' => $debitA,
            'luas_rencana'    => $luasR,
            'luas_realisasi'  => $luasA,
            'curah_hujan'     => $ch,
        ];
    }
}
