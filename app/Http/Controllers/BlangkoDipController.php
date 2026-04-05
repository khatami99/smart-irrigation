<?php

namespace App\Http\Controllers;

use App\Models\BlangkoO01;
use App\Models\DaerahIrigasi;
use App\Models\KebutuhanAirDi;
use App\Models\MusimTanam;
use App\Services\KpSatuService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BlangkoDipController extends Controller
{
    // ── O-01: Usulan Luas Tanam ──────────────────────────────

    public function o01Index(Request $request)
    {
        $musimTanams = MusimTanam::orderBy('tanggal_mulai', 'desc')->get();
        $mtAktif     = MusimTanam::berjalan()->first();
        $mtId        = $request->get('musim_tanam_id', $mtAktif?->id);

        $query = BlangkoO01::with(['daerahIrigasi', 'musimTanam', 'user'])
            ->orderBy('created_at', 'desc');

        if ($mtId) $query->where('musim_tanam_id', $mtId);
        if ($request->filled('daerah_irigasi_id')) $query->where('daerah_irigasi_id', $request->daerah_irigasi_id);
        if ($request->filled('status')) $query->where('status', $request->status);

        $items          = $query->paginate(15);
        $daerahIrigasis = DaerahIrigasi::aktif()->orderBy('kode')->get();
        $mt             = $mtId ? MusimTanam::find($mtId) : $mtAktif;

        return view('blangko_dip.o01_index', compact('items', 'musimTanams', 'daerahIrigasis', 'mt', 'mtId'));
    }

    public function o01Create(Request $request)
    {
        $daerahIrigasis = DaerahIrigasi::aktif()->orderBy('kode')->get();
        $musimTanams    = MusimTanam::orderBy('tanggal_mulai', 'desc')->get();
        $mtAktif        = MusimTanam::berjalan()->first();
        $selectedDiId   = $request->get('daerah_irigasi_id');
        $selectedMtId   = $request->get('musim_tanam_id', $mtAktif?->id);

        return view('blangko_dip.o01_create', compact(
            'daerahIrigasis', 'musimTanams', 'mtAktif', 'selectedDiId', 'selectedMtId'
        ));
    }

    public function o01Store(Request $request)
    {
        $request->validate([
            'daerah_irigasi_id'    => 'required|exists:daerah_irigasis,id',
            'musim_tanam_id'       => 'required|exists:musim_tanams,id',
            'luas_padi_usulan'     => 'required|numeric|min:0',
            'luas_palawija_usulan' => 'required|numeric|min:0',
            'luas_tebu_usulan'     => 'required|numeric|min:0',
            'keterangan'           => 'nullable|string',
        ]);

        $di          = DaerahIrigasi::find($request->daerah_irigasi_id);
        $totalUsulan = $request->luas_padi_usulan + $request->luas_palawija_usulan + $request->luas_tebu_usulan;

        if ($di->luas_total && $totalUsulan > $di->luas_total) {
            return back()->withInput()->withErrors([
                'luas_padi_usulan' => "Total luas tanam ({$totalUsulan} ha) melebihi luas total DI {$di->nama} ({$di->luas_total} ha).",
            ]);
        }

        $exists = BlangkoO01::where('daerah_irigasi_id', $request->daerah_irigasi_id)
            ->where('musim_tanam_id', $request->musim_tanam_id)
            ->exists();

        if ($exists) {
            return back()->withInput()->withErrors([
                'daerah_irigasi_id' => 'O-01 untuk DI dan Musim Tanam ini sudah ada! Gunakan menu Edit untuk mengubahnya.',
            ]);
        }

        $o01 = BlangkoO01::create(array_merge($request->only([
            'daerah_irigasi_id',
            'musim_tanam_id',
            'luas_padi_usulan',
            'luas_palawija_usulan',
            'luas_tebu_usulan',
            'keterangan',
        ]), [
            'user_id' => Auth::id(),
            'status'  => 'usulan',
        ]));

        app(KpSatuService::class)->hitungDariO01($o01);

        return redirect()->route('blangko-dip.o01.index')
            ->with('success', 'Blangko O-01 berhasil disimpan. Kebutuhan air telah dihitung.');
    }

    public function o01Show(BlangkoO01 $o01)
    {
        $o01->load(['daerahIrigasi', 'musimTanam', 'user']);
        $blangkoO01 = $o01;
        return view('blangko_dip.o01_show', compact('blangkoO01'));
    }

    public function o01Edit(BlangkoO01 $o01)
    {
        $daerahIrigasis = DaerahIrigasi::aktif()->orderBy('kode')->get();
        $musimTanams    = MusimTanam::orderBy('tanggal_mulai', 'desc')->get();
        $blangkoO01 = $o01;
        return view('blangko_dip.o01_edit', compact('blangkoO01', 'daerahIrigasis', 'musimTanams'));
    }

    public function o01Update(Request $request, BlangkoO01 $o01)
    {
        /** @var \App\Models\User $user */
        $user    = Auth::user();
        $isAdmin = $user->can('delete blangko-op');

        $rules = [
            'luas_padi_usulan'     => 'required|numeric|min:0',
            'luas_palawija_usulan' => 'required|numeric|min:0',
            'luas_tebu_usulan'     => 'required|numeric|min:0',
            'keterangan'           => 'nullable|string',
        ];

        if ($isAdmin) {
            $rules['luas_padi_disetujui']     = 'nullable|numeric|min:0';
            $rules['luas_palawija_disetujui'] = 'nullable|numeric|min:0';
            $rules['luas_tebu_disetujui']     = 'nullable|numeric|min:0';
            $rules['status']                  = 'required|in:usulan,disetujui,revisi';
        }

        $request->validate($rules);

        $di          = $o01->daerahIrigasi;
        $totalUsulan = $request->luas_padi_usulan + $request->luas_palawija_usulan + $request->luas_tebu_usulan;

        if ($di->luas_total && $totalUsulan > $di->luas_total) {
            return back()->withInput()->withErrors([
                'luas_padi_usulan' => "Total luas tanam ({$totalUsulan} ha) melebihi luas total DI {$di->nama} ({$di->luas_total} ha).",
            ]);
        }

        $data = $request->only([
            'luas_padi_usulan',
            'luas_palawija_usulan',
            'luas_tebu_usulan',
            'keterangan',
        ]);

        if ($isAdmin) {
            $data = array_merge($data, $request->only([
                'luas_padi_disetujui',
                'luas_palawija_disetujui',
                'luas_tebu_disetujui',
                'status',
            ]));
        }

        $o01->update($data);

        app(KpSatuService::class)->hitungDariO01($o01->fresh());

        return redirect()->route('blangko-dip.o01.index')
            ->with('success', 'Blangko O-01 berhasil diperbarui. Kebutuhan air telah dihitung ulang.');
    }

    public function o01Destroy(BlangkoO01 $o01)
    {
        $o01->delete();
        return redirect()->route('blangko-dip.o01.index')
            ->with('success', 'Blangko O-01 berhasil dihapus.');
    }

    // ── O-05: Rencana Kebutuhan Air di Pintu ────────────────

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
