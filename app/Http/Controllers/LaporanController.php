<?php

namespace App\Http\Controllers;

use App\Models\IrrigationData;
use App\Models\BlangkoOp;
use App\Models\Rtt;
use App\Models\MusimTanam;
use App\Models\Petak;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\DataIklimExport;
use App\Exports\BlangkoOpExport;
use App\Exports\RttExport;
use App\Exports\RekapKebutuhanExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class LaporanController extends Controller
{
    // ── Index ──
    public function index(Request $request)
    {
        $musimTanams = MusimTanam::orderBy('tanggal_mulai', 'desc')->get();
        $mtAktif     = MusimTanam::berjalan()->first();
        $tahunList   = IrrigationData::selectRaw('YEAR(tanggal) as tahun')
                        ->distinct()->orderBy('tahun', 'desc')->pluck('tahun');

        return view('laporan.index', compact('musimTanams', 'mtAktif', 'tahunList'));
    }

    // ══════════════════════════════
    //  PDF EXPORTS
    // ══════════════════════════════

    public function pdfDataIklim(Request $request)
    {
        $tahun  = $request->get('tahun', date('Y'));
        $bulan  = $request->get('bulan');
        $query  = IrrigationData::orderBy('tanggal', 'asc')->whereYear('tanggal', $tahun);
        if ($bulan) $query->whereMonth('tanggal', $bulan);
        $data   = $query->get();

        $pdf = Pdf::loadView('laporan.pdf.data-iklim', compact('data', 'tahun', 'bulan'))
                  ->setPaper('a4', 'landscape');

        $filename = 'laporan-data-iklim-' . $tahun . ($bulan ? '-'.str_pad($bulan,2,'0',STR_PAD_LEFT) : '') . '.pdf';
        return $pdf->download($filename);
    }

    public function pdfBlangkoOp(Request $request)
    {
        $mtId = $request->get('musim_tanam_id');
        $mt   = $mtId ? MusimTanam::find($mtId) : MusimTanam::berjalan()->first();
        $data = BlangkoOp::with(['petak', 'musimTanam'])
                    ->when($mt, fn($q) => $q->where('musim_tanam_id', $mt->id))
                    ->orderBy('tahun')->orderBy('bulan')->orderBy('dekade')
                    ->get();

        $pdf = Pdf::loadView('laporan.pdf.blangko-op', compact('data', 'mt'))
                  ->setPaper('a4', 'landscape');

        $filename = 'laporan-blangko-op-' . ($mt?->nama_mt ?? 'semua') . '.pdf';
        return $pdf->download(str_replace('/', '-', $filename));
    }

    public function pdfRtt(Request $request)
    {
        $mtId = $request->get('musim_tanam_id');
        $mt   = $mtId ? MusimTanam::find($mtId) : MusimTanam::berjalan()->first();
        $data = Rtt::with(['petak', 'musimTanam'])
                    ->when($mt, fn($q) => $q->where('musim_tanam_id', $mt->id))
                    ->orderBy('urutan_rotasi')->get();

        $minDate = $data->min('rencana_mulai_tanam');
        $maxDate = $data->max('rencana_selesai_tanam');
        $totalDays = Carbon::parse($minDate)->diffInDays(Carbon::parse($maxDate)) + 1;

        $pdf = Pdf::loadView('laporan.pdf.rtt', compact('data', 'mt', 'minDate', 'maxDate', 'totalDays'))
                  ->setPaper('a4', 'portrait');

        $filename = 'laporan-rtt-' . ($mt?->nama_mt ?? 'semua') . '.pdf';
        return $pdf->download(str_replace('/', '-', $filename));
    }

    public function pdfRekap(Request $request)
    {
        $tahun = $request->get('tahun', date('Y'));
        $data  = IrrigationData::whereYear('tanggal', $tahun)
                    ->orderBy('tanggal')->get();

        // Rekap per bulan
        $rekapBulanan = $data->groupBy(fn($d) => Carbon::parse($d->tanggal)->format('Y-m'))
            ->map(fn($group, $key) => [
                'bulan'          => Carbon::parse($key . '-01')->locale('id')->isoFormat('MMMM YYYY'),
                'avg_eto'        => round($group->avg('eto'), 2),
                'avg_etc'        => round($group->avg('etc'), 2),
                'avg_kebutuhan'  => round($group->avg('kebutuhan_air'), 2),
                'max_kebutuhan'  => round($group->max('kebutuhan_air'), 2),
                'min_kebutuhan'  => round($group->min('kebutuhan_air'), 2),
                'total_hujan'    => round($group->sum('curah_hujan'), 1),
                'jumlah_data'    => $group->count(),
            ]);

        $pdf = Pdf::loadView('laporan.pdf.rekap', compact('data', 'rekapBulanan', 'tahun'))
                  ->setPaper('a4', 'portrait');

        return $pdf->download('laporan-rekap-kebutuhan-air-' . $tahun . '.pdf');
    }

    // ══════════════════════════════
    //  EXCEL EXPORTS
    // ══════════════════════════════

    public function excelDataIklim(Request $request)
    {
        $tahun   = $request->get('tahun', date('Y'));
        $bulan   = $request->get('bulan');
        $filename = 'data-iklim-' . $tahun . ($bulan ? '-'.str_pad($bulan,2,'0',STR_PAD_LEFT) : '') . '.xlsx';
        return Excel::download(new DataIklimExport($tahun, $bulan), $filename);
    }

    public function excelBlangkoOp(Request $request)
    {
        $mtId = $request->get('musim_tanam_id');
        $mt   = $mtId ? MusimTanam::find($mtId) : MusimTanam::berjalan()->first();
        $filename = 'blangko-op-' . str_replace('/', '-', $mt?->nama_mt ?? 'semua') . '.xlsx';
        return Excel::download(new BlangkoOpExport($mtId), $filename);
    }

    public function excelRtt(Request $request)
    {
        $mtId = $request->get('musim_tanam_id');
        $mt   = $mtId ? MusimTanam::find($mtId) : MusimTanam::berjalan()->first();
        $filename = 'rtt-' . str_replace('/', '-', $mt?->nama_mt ?? 'semua') . '.xlsx';
        return Excel::download(new RttExport($mtId), $filename);
    }

    public function excelRekap(Request $request)
    {
        $tahun = $request->get('tahun', date('Y'));
        return Excel::download(new RekapKebutuhanExport($tahun), 'rekap-kebutuhan-air-' . $tahun . '.xlsx');
    }
}
