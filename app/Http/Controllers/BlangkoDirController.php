<?php

namespace App\Http\Controllers;

use App\Models\DaerahIrigasi;
use App\Models\MusimTanam;
use App\Models\Petak;
use App\Models\RttDir;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BlangkoDirController extends Controller
{
    // ── O-09: Rencana/Realisasi Tanaman per Petak Tersier ────

    public function o09Index(Request $request)
    {
        $daerahIrigasis = DaerahIrigasi::where('jenis', 'rawa')
            ->where('status', 'aktif')
            ->orderBy('kode')
            ->get();
        $musimTanams = MusimTanam::orderBy('tanggal_mulai', 'desc')->get();
        $mtAktif     = MusimTanam::berjalan()->first();

        $diId = $request->get('daerah_irigasi_id');
        $mtId = $request->get('musim_tanam_id', $mtAktif?->id);

        $data   = collect();
        $petaks = collect();
        $bulan  = [];
        $di     = null;
        $mt     = null;

        if ($diId && $mtId) {
            $di = DaerahIrigasi::find($diId);
            $mt = MusimTanam::find($mtId);

            // Generate daftar bulan dalam MT
            $current = Carbon::parse($mt->tanggal_mulai)->startOfMonth();
            $akhir   = Carbon::parse($mt->tanggal_selesai)->startOfMonth();
            while ($current->lte($akhir)) {
                $bulan[] = [
                    'bulan' => $current->month,
                    'tahun' => $current->year,
                    'label' => $current->translatedFormat('M Y'),
                ];
                $current->addMonth();
            }

            $petaks = Petak::where('daerah_irigasi_id', $diId)
                ->orderBy('kode_petak')
                ->get();

            $data = RttDir::where('daerah_irigasi_id', $diId)
                ->where('musim_tanam_id', $mtId)
                ->get()
                ->groupBy('petak_id');
        }

        return view('blangko_dir.o09_index', compact(
            'daerahIrigasis', 'musimTanams', 'mtAktif',
            'diId', 'mtId', 'di', 'mt', 'data', 'petaks', 'bulan'
        ));
    }

    public function o09Create(Request $request)
    {
        $daerahIrigasis = DaerahIrigasi::where('jenis', 'rawa')
            ->where('status', 'aktif')
            ->orderBy('kode')
            ->get();
        $musimTanams = MusimTanam::orderBy('tanggal_mulai', 'desc')->get();
        $mtAktif     = MusimTanam::berjalan()->first();
        $diId        = $request->get('daerah_irigasi_id');
        $mtId        = $request->get('musim_tanam_id', $mtAktif?->id);

        $petaks = $diId
            ? Petak::where('daerah_irigasi_id', $diId)->orderBy('kode_petak')->get()
            : collect();

        $bulan = [];
        if ($mtId) {
            $mt      = MusimTanam::find($mtId);
            $current = Carbon::parse($mt->tanggal_mulai)->startOfMonth();
            $akhir   = Carbon::parse($mt->tanggal_selesai)->startOfMonth();
            while ($current->lte($akhir)) {
                $bulan[] = [
                    'bulan' => $current->month,
                    'tahun' => $current->year,
                    'label' => $current->translatedFormat('M Y'),
                ];
                $current->addMonth();
            }
        }

        return view('blangko_dir.o09_create', compact(
            'daerahIrigasis', 'musimTanams', 'mtAktif',
            'diId', 'mtId', 'petaks', 'bulan'
        ));
    }

    public function o09Store(Request $request)
    {
        $request->validate([
            'daerah_irigasi_id' => 'required|exists:daerah_irigasis,id',
            'musim_tanam_id'    => 'required|exists:musim_tanams,id',
            'petak_id'          => 'required|exists:petaks,id',
            'bulan'             => 'required|integer|min:1|max:12',
            'tahun'             => 'required|integer|min:2000|max:2100',
            'rencana_padi'      => 'nullable|numeric|min:0',
            'realisasi_padi'    => 'nullable|numeric|min:0',
            'rencana_palawija'      => 'nullable|numeric|min:0',
            'realisasi_palawija'    => 'nullable|numeric|min:0',
            'rencana_tanaman_keras' => 'nullable|numeric|min:0',
            'realisasi_tanaman_keras' => 'nullable|numeric|min:0',
            'rencana_bera'      => 'nullable|numeric|min:0',
            'realisasi_bera'    => 'nullable|numeric|min:0',
        ]);

        RttDir::updateOrCreate(
            [
                'petak_id'          => $request->petak_id,
                'musim_tanam_id'    => $request->musim_tanam_id,
                'bulan'             => $request->bulan,
                'tahun'             => $request->tahun,
            ],
            array_merge($request->only([
                'daerah_irigasi_id',
                'rencana_padi', 'realisasi_padi',
                'rencana_palawija', 'realisasi_palawija',
                'rencana_tanaman_keras', 'realisasi_tanaman_keras',
                'rencana_bera', 'realisasi_bera',
                'keterangan',
            ]), ['user_id' => Auth::id()])
        );

        return redirect()->route('blangko-dir.o09.index', [
            'daerah_irigasi_id' => $request->daerah_irigasi_id,
            'musim_tanam_id'    => $request->musim_tanam_id,
        ])->with('success', 'Data O-09 berhasil disimpan.');
    }

    public function o09Pdf(Request $request)
    {
        $diId = $request->get('daerah_irigasi_id');
        $mtId = $request->get('musim_tanam_id');

        $di = DaerahIrigasi::findOrFail($diId);
        $mt = MusimTanam::findOrFail($mtId);

        $petaks = Petak::where('daerah_irigasi_id', $diId)->orderBy('kode_petak')->get();

        $bulan = [];
        $current = Carbon::parse($mt->tanggal_mulai)->startOfMonth();
        $akhir   = Carbon::parse($mt->tanggal_selesai)->startOfMonth();
        while ($current->lte($akhir)) {
            $bulan[] = [
                'bulan' => $current->month,
                'tahun' => $current->year,
                'label' => $current->translatedFormat('M Y'),
            ];
            $current->addMonth();
        }

        $data = RttDir::where('daerah_irigasi_id', $diId)
            ->where('musim_tanam_id', $mtId)
            ->get()
            ->groupBy('petak_id');

        $pdf = Pdf::loadView('blangko_dir.o09_pdf', compact('di', 'mt', 'petaks', 'bulan', 'data'))
            ->setPaper('a4', 'landscape');

        $filename = 'O09-DIR-' . $di->kode . '-' . $mt->nama_mt . '.pdf';
        $filename = str_replace([' ', '/'], '-', $filename);

        return $pdf->download($filename);
    }
}
