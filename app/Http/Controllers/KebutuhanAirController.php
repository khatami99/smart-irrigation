<?php

namespace App\Http\Controllers;

use App\Models\DaerahIrigasi;
use App\Models\KebutuhanAirDi;
use App\Models\MusimTanam;
use Illuminate\Http\Request;

class KebutuhanAirController extends Controller
{
    public function index(Request $request)
    {
        $daerahIrigasis = DaerahIrigasi::aktif()->orderBy('kode')->get();
        $musimTanams    = MusimTanam::orderBy('tanggal_mulai', 'desc')->get();
        $mtAktif        = MusimTanam::berjalan()->first();

        $diId = $request->get('daerah_irigasi_id');
        $mtId = $request->get('musim_tanam_id', $mtAktif?->id);

        $data = collect();
        $di   = null;
        $mt   = null;

        if ($diId && $mtId) {
            $di   = DaerahIrigasi::find($diId);
            $mt   = MusimTanam::find($mtId);

            $data = KebutuhanAirDi::where('daerah_irigasi_id', $diId)
                ->where('musim_tanam_id', $mtId)
                ->orderBy('tahun')
                ->orderBy('bulan')
                ->orderByRaw("FIELD(dekade, 'I', 'II', 'III')")
                ->get();
        }

        $summary = $data->isNotEmpty() ? [
            'total_padi'     => $data->sum('kebutuhan_padi'),
            'total_palawija' => $data->sum('kebutuhan_palawija'),
            'total_tebu'     => $data->sum('kebutuhan_tebu'),
            'total_semua'    => $data->sum('kebutuhan_total'),
            'rata_eto'       => round($data->whereNotNull('eto_dekade')->avg('eto_dekade'), 2),
            'total_ch'       => round($data->whereNotNull('ch_dekade')->sum('ch_dekade'), 1),
        ] : null;

        return view('kebutuhan_air.index', compact(
            'daerahIrigasis', 'musimTanams', 'mtAktif',
            'diId', 'mtId', 'di', 'mt', 'data', 'summary'
        ));
    }
}
