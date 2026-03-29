<?php

namespace App\Http\Controllers;

use App\Models\DaerahIrigasi;
use App\Models\KebutuhanAirDi;
use App\Models\MusimTanam;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class BlangkoDipController extends Controller
{
    public function o05(Request $request)
    {
        $daerahIrigasis = DaerahIrigasi::where('jenis_di', 'permukaan')
            ->where('status', 'aktif')
            ->orderBy('kode')
            ->get();
        $musimTanams = MusimTanam::orderBy('tanggal_mulai', 'desc')->get();
        $mtAktif     = MusimTanam::berjalan()->first();

        $diId = $request->get('daerah_irigasi_id');
        $mtId = $request->get('musim_tanam_id', $mtAktif?->id);

        $data = collect();
        $di   = null;
        $mt   = null;

        if ($diId && $mtId) {
            $di = DaerahIrigasi::find($diId);
            $mt = MusimTanam::find($mtId);

            $data = KebutuhanAirDi::where('daerah_irigasi_id', $diId)
                ->where('musim_tanam_id', $mtId)
                ->orderBy('tahun')
                ->orderBy('bulan')
                ->orderByRaw("FIELD(dekade, 'I', 'II', 'III')")
                ->get();
        }

        return view('blangko_dip.o05', compact(
            'daerahIrigasis', 'musimTanams', 'mtAktif',
            'diId', 'mtId', 'di', 'mt', 'data'
        ));
    }

    public function o05Pdf(Request $request)
    {
        $diId = $request->get('daerah_irigasi_id');
        $mtId = $request->get('musim_tanam_id');

        $di = DaerahIrigasi::findOrFail($diId);
        $mt = MusimTanam::findOrFail($mtId);

        $data = KebutuhanAirDi::where('daerah_irigasi_id', $diId)
            ->where('musim_tanam_id', $mtId)
            ->orderBy('tahun')
            ->orderBy('bulan')
            ->orderByRaw("FIELD(dekade, 'I', 'II', 'III')")
            ->get();

        $pdf = Pdf::loadView('blangko_dip.o05_pdf', compact('di', 'mt', 'data'))
            ->setPaper('a4', 'landscape');

        $filename = 'O05-DIP-' . $di->kode . '-' . $mt->nama_mt . '.pdf';
        $filename = str_replace([' ', '/'], '-', $filename);

        return $pdf->download($filename);
    }
}
