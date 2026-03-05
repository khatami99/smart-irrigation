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
        $items = DaerahIrigasi::orderBy('kode')->paginate(15);
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
        $request->validate([
            'kode'              => 'required|string|max:20|unique:daerah_irigasis,kode',
            'nama'              => 'required|string|max:100',
            'luas_total'        => 'nullable|numeric|min:0',
            'sumber_air'        => 'nullable|string|max:100',
            'penanggung_jawab'  => 'nullable|string|max:100',
            'latitude'          => 'nullable|numeric|between:-90,90',
            'longitude'         => 'nullable|numeric|between:-180,180',
            'status'            => 'required|in:aktif,nonaktif',
            'keterangan'        => 'nullable|string',
        ]);

        DaerahIrigasi::create($request->all());
        return redirect()->route('daerah_irigasi.index')->with('success', 'Daerah irigasi berhasil ditambahkan.');
    }

    public function edit(DaerahIrigasi $daerahIrigasi)
    {
        return view('daerah_irigasi.edit', compact('daerahIrigasi'));
    }

    public function update(Request $request, DaerahIrigasi $daerahIrigasi)
    {
        $request->validate([
            'kode'              => 'required|string|max:20|unique:daerah_irigasis,kode,' . $daerahIrigasi->id,
            'nama'              => 'required|string|max:100',
            'luas_total'        => 'nullable|numeric|min:0',
            'sumber_air'        => 'nullable|string|max:100',
            'penanggung_jawab'  => 'nullable|string|max:100',
            'latitude'          => 'nullable|numeric|between:-90,90',
            'longitude'         => 'nullable|numeric|between:-180,180',
            'status'            => 'required|in:aktif,nonaktif',
            'keterangan'        => 'nullable|string',
        ]);

        $daerahIrigasi->update($request->all());
        return redirect()->route('daerah_irigasi.index')->with('success', 'Daerah irigasi berhasil diperbarui.');
    }

    public function destroy(DaerahIrigasi $daerahIrigasi)
    {
        $daerahIrigasi->delete();
        return redirect()->route('daerah_irigasi.index')->with('success', 'Daerah irigasi berhasil dihapus.');
    }
}
