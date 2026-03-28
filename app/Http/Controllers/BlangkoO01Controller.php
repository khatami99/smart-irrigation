<?php

namespace App\Http\Controllers;

use App\Models\BlangkoO01;
use App\Models\DaerahIrigasi;
use App\Models\MusimTanam;
use App\Services\KpSatuService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class BlangkoO01Controller extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view blangko-op')->only(['index', 'show']);
        $this->middleware('permission:create blangko-op')->only(['create', 'store']);
        $this->middleware('permission:edit blangko-op')->only(['edit', 'update']);
        $this->middleware('permission:delete blangko-op')->only(['destroy']);
    }

    public function index(Request $request)
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

        return view('blangko_o01.index', compact('items', 'musimTanams', 'daerahIrigasis', 'mt', 'mtId'));
    }

    public function create(Request $request)
    {
        $daerahIrigasis = DaerahIrigasi::aktif()->orderBy('kode')->get();
        $musimTanams    = MusimTanam::orderBy('tanggal_mulai', 'desc')->get();
        $mtAktif        = MusimTanam::berjalan()->first();
        $selectedDiId   = $request->get('daerah_irigasi_id');
        $selectedMtId   = $request->get('musim_tanam_id', $mtAktif?->id);

        return view('blangko_o01.create', compact(
            'daerahIrigasis', 'musimTanams', 'mtAktif', 'selectedDiId', 'selectedMtId'
        ));
    }

    public function store(Request $request)
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

        // ── Trigger kalkulasi KP-01 ──────────────────────
        app(KpSatuService::class)->hitungDariO01($o01);

        return redirect()->route('blangko-o01.index')
            ->with('success', 'Blangko O-01 berhasil disimpan. Kebutuhan air telah dihitung.');
    }

    public function show(BlangkoO01 $blangkoO01)
    {
        $blangkoO01->load(['daerahIrigasi', 'musimTanam', 'user']);
        return view('blangko_o01.show', compact('blangkoO01'));
    }

    public function edit(BlangkoO01 $blangkoO01)
    {
        $daerahIrigasis = DaerahIrigasi::aktif()->orderBy('kode')->get();
        $musimTanams    = MusimTanam::orderBy('tanggal_mulai', 'desc')->get();
        return view('blangko_o01.edit', compact('blangkoO01', 'daerahIrigasis', 'musimTanams'));
    }

    public function update(Request $request, BlangkoO01 $blangkoO01)
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

        $di          = $blangkoO01->daerahIrigasi;
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

        $blangkoO01->update($data);

        // ── Trigger recalculate KP-01 ────────────────────
        app(KpSatuService::class)->hitungDariO01($blangkoO01->fresh());

        return redirect()->route('blangko-o01.index')
            ->with('success', 'Blangko O-01 berhasil diperbarui. Kebutuhan air telah dihitung ulang.');
    }

    public function destroy(BlangkoO01 $blangkoO01)
    {
        $blangkoO01->delete();
        return redirect()->route('blangko-o01.index')
            ->with('success', 'Blangko O-01 berhasil dihapus.');
    }
}
