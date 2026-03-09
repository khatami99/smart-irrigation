<?php

namespace App\Http\Controllers;

use App\Models\DaerahIrigasi;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class DaerahIrigasiController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view daerah_irigasi')->only(['index']);
        $this->middleware('permission:create daerah_irigasi')->only(['create', 'store']);
        $this->middleware('permission:edit daerah_irigasi')->only(['edit', 'update']);
        $this->middleware('permission:delete daerah_irigasi')->only(['destroy']);
    }

    public function index()
    {
        $items      = DaerahIrigasi::orderBy('kode')->paginate(15);
        $totalAktif = DaerahIrigasi::aktif()->count();
        $totalLuas  = DaerahIrigasi::aktif()->sum('luas_total');
        $totalSemua = DaerahIrigasi::count();

        return view('daerah_irigasi.index', compact('items', 'totalAktif', 'totalLuas', 'totalSemua'));
    }

    public function create()
    {
        return view('daerah_irigasi.create');
    }

    public function store(Request $request)
    {
        $request->validate($this->rules());

        DaerahIrigasi::create($request->all());

        return redirect()->route('daerah_irigasi.index')
            ->with('success', 'Daerah irigasi berhasil ditambahkan.');
    }

    public function edit(DaerahIrigasi $daerahIrigasi)
    {
        return view('daerah_irigasi.edit', compact('daerahIrigasi'));
    }

    public function update(Request $request, DaerahIrigasi $daerahIrigasi)
    {
        $request->validate($this->rules($daerahIrigasi->id));

        $daerahIrigasi->update($request->all());

        return redirect()->route('daerah_irigasi.index')
            ->with('success', 'Daerah irigasi berhasil diperbarui.');
    }

    public function destroy(DaerahIrigasi $daerahIrigasi)
    {
        $daerahIrigasi->delete();

        return redirect()->route('daerah_irigasi.index')
            ->with('success', 'Daerah irigasi berhasil dihapus.');
    }

    // ─── Validation rules ───────────────────────────────
    private function rules(?int $id = null): array
    {
        return [
            // Field
            'kode'                 => 'required|string|max:20|unique:daerah_irigasis,kode,' . ($id ?? 'NULL'),
            'nama'                 => 'required|string|max:100',
            'jenis'                => 'required|in:permukaan,rawa',   // ← BARU
            'luas_total'           => 'nullable|numeric|min:0',
            'sumber_air'           => 'nullable|string|max:100',
            'penanggung_jawab'     => 'nullable|string|max:100',
            'latitude'             => 'nullable|numeric|between:-90,90',
            'longitude'            => 'nullable|numeric|between:-180,180',
            'status'               => 'required|in:aktif,nonaktif',
            'keterangan'           => 'nullable|string',
            // Parameter SKA
            'ska_padi_pengolahan'  => 'required|numeric|min:0|max:10',
            'ska_padi_pertumbuhan' => 'required|numeric|min:0|max:10',
            'ska_palawija_banyak'  => 'required|numeric|min:0|max:10',
            'ska_palawija_sedikit' => 'required|numeric|min:0|max:10',
            'faktor_tersier'       => 'required|numeric|min:0|max:1',
            'pct_kehilangan_air'   => 'required|numeric|min:0|max:100',
        ];
    }
}
