<?php
namespace App\Http\Controllers;

use App\Models\Saluran;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class SaluranController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view saluran')->only(['index']);
        $this->middleware('permission:create saluran')->only(['create', 'store']);
        $this->middleware('permission:edit saluran')->only(['edit', 'update']);
        $this->middleware('permission:delete saluran')->only(['destroy']);
    }

    public function index()
    {
        $salurans   = Saluran::orderBy('nama')->paginate(15);
        $totalBaik  = Saluran::where('kondisi', 'baik')->count();
        $totalSemua = Saluran::count();
        $totalKm    = Saluran::sum('panjang_km');
        return view('saluran.index', compact('salurans', 'totalBaik', 'totalSemua', 'totalKm'));
    }

    public function create()
    {
        return view('saluran.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'              => 'required|string|max:100',
            'tipe'              => 'required|in:primer,sekunder,tersier',
            'panjang_km'        => 'nullable|numeric|min:0',
            'kondisi'           => 'required|in:baik,sedang,rusak',
            'penanggung_jawab'  => 'nullable|string|max:100',
            'latitude'          => 'nullable|numeric|between:-90,90',
            'longitude'         => 'nullable|numeric|between:-180,180',
            'keterangan'        => 'nullable|string',
        ]);

        Saluran::create($request->all());
        return redirect()->route('saluran.index')->with('success', 'Saluran berhasil ditambahkan.');
    }

    public function edit(Saluran $saluran)
    {
        return view('saluran.edit', compact('saluran'));
    }

    public function update(Request $request, Saluran $saluran)
    {
        $request->validate([
            'nama'              => 'required|string|max:100',
            'tipe'              => 'required|in:primer,sekunder,tersier',
            'panjang_km'        => 'nullable|numeric|min:0',
            'kondisi'           => 'required|in:baik,sedang,rusak',
            'penanggung_jawab'  => 'nullable|string|max:100',
            'latitude'          => 'nullable|numeric|between:-90,90',
            'longitude'         => 'nullable|numeric|between:-180,180',
            'keterangan'        => 'nullable|string',
        ]);

        $saluran->update($request->all());
        return redirect()->route('saluran.index')->with('success', 'Saluran berhasil diperbarui.');
    }

    public function destroy(Saluran $saluran)
    {
        $saluran->delete();
        return redirect()->route('saluran.index')->with('success', 'Saluran berhasil dihapus.');
    }
}
