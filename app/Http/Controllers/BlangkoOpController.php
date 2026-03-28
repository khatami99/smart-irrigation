<?php
// app/Http/Controllers/BlangkoOpController.php

namespace App\Http\Controllers;

use App\Models\BlangkoOp;
use App\Models\Petak;
use App\Models\MusimTanam;
use App\Models\DaerahIrigasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller as BaseController;

class BlangkoOpController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view blangko-op')->only(['index']);
        $this->middleware('permission:create blangko-op')->only(['create', 'store']);
        $this->middleware('permission:edit blangko-op')->only(['edit', 'update']);
        $this->middleware('permission:delete blangko-op')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $query = BlangkoOp::with(['petak', 'musimTanam', 'user'])
            ->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc')
            ->orderBy('dekade', 'desc');

        if ($request->filled('petak_id'))      $query->where('petak_id', $request->petak_id);
        if ($request->filled('musim_tanam_id')) $query->where('musim_tanam_id', $request->musim_tanam_id);
        if ($request->filled('tahun'))          $query->where('tahun', $request->tahun);
        if ($request->filled('bulan'))          $query->where('bulan', $request->bulan);

        $blangkos       = $query->paginate(15);
        $petaks         = Petak::aktif()->orderBy('kode_petak')->get();
        $musimTanams    = MusimTanam::orderBy('tanggal_mulai', 'desc')->get();
        $years          = BlangkoOp::selectRaw('tahun')->distinct()->orderBy('tahun', 'desc')->pluck('tahun');

        return view('blangko_op.index', compact('blangkos', 'petaks', 'musimTanams', 'years'));
    }

    public function create()
    {
        $daerahIrigasis = DaerahIrigasi::aktif()->orderBy('kode')->get();
        $musimTanams    = MusimTanam::orderBy('tanggal_mulai', 'desc')->get();
        $mtAktif        = MusimTanam::berjalan()->first();

        return view('blangko_op.create', compact('daerahIrigasis', 'musimTanams', 'mtAktif'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'petak_id'          => 'required|exists:petaks,id',
            'musim_tanam_id'    => 'required|exists:musim_tanams,id',
            'tahun'             => 'required|integer|min:2000|max:2100',
            'bulan'             => 'required|integer|min:1|max:12',
            'dekade'            => 'required|in:I,II,III',
            'debit_rencana'     => 'nullable|numeric|min:0',
            'debit_realisasi'   => 'nullable|numeric|min:0',
            'tinggi_muka_air'   => 'nullable|numeric|min:0',
            'luas_rencana'      => 'nullable|numeric|min:0',
            'luas_realisasi'    => 'nullable|numeric|min:0',
            'fase_pertumbuhan'  => 'nullable|in:pengolahan_tanah,tanam,vegetatif,generatif,panen,bero',
            'kondisi_saluran'   => 'nullable|in:baik,rusak_ringan,rusak_berat',
            'kondisi_bangunan'  => 'nullable|in:baik,rusak_ringan,rusak_berat',
            'catatan_kondisi'   => 'nullable|string',
            'curah_hujan'       => 'nullable|numeric|min:0',
            'keterangan'        => 'nullable|string',
        ]);

        $exists = BlangkoOp::where('petak_id', $request->petak_id)
            ->where('musim_tanam_id', $request->musim_tanam_id)
            ->where('tahun', $request->tahun)
            ->where('bulan', $request->bulan)
            ->where('dekade', $request->dekade)
            ->exists();

        if ($exists) {
            return back()->withInput()->withErrors([
                'dekade' => 'Data blangko untuk petak, dekade, dan musim tanam ini sudah ada!'
            ]);
        }

        BlangkoOp::create(array_merge($request->all(), ['user_id' => Auth::id()]));

        return redirect()->route('blangko-op.index')
            ->with('success', 'Blangko OP berhasil disimpan.');
    }

    public function edit(BlangkoOp $blangkoOp)
    {
        $daerahIrigasis = DaerahIrigasi::aktif()->orderBy('kode')->get();
        $musimTanams    = MusimTanam::orderBy('tanggal_mulai', 'desc')->get();

        return view('blangko_op.edit', compact('blangkoOp', 'daerahIrigasis', 'musimTanams'));
    }

    public function update(Request $request, BlangkoOp $blangkoOp)
    {
        $request->validate([
            'debit_rencana'    => 'nullable|numeric|min:0',
            'debit_realisasi'  => 'nullable|numeric|min:0',
            'tinggi_muka_air'  => 'nullable|numeric|min:0',
            'luas_rencana'     => 'nullable|numeric|min:0',
            'luas_realisasi'   => 'nullable|numeric|min:0',
            'fase_pertumbuhan' => 'nullable|in:pengolahan_tanah,tanam,vegetatif,generatif,panen,bero',
            'kondisi_saluran'  => 'nullable|in:baik,rusak_ringan,rusak_berat',
            'kondisi_bangunan' => 'nullable|in:baik,rusak_ringan,rusak_berat',
            'catatan_kondisi'  => 'nullable|string',
            'curah_hujan'      => 'nullable|numeric|min:0',
            'keterangan'       => 'nullable|string',
        ]);

        $blangkoOp->update($request->all());
        return redirect()->route('blangko-op.index')
            ->with('success', 'Blangko OP berhasil diperbarui.');
    }

    public function destroy(BlangkoOp $blangkoOp)
    {
        $blangkoOp->delete();
        return redirect()->route('blangko-op.index')
            ->with('success', 'Blangko OP berhasil dihapus.');
    }
}
