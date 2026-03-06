<?php
// app/Http/Controllers/PetakController.php

namespace App\Http\Controllers;

use App\Models\Petak;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class PetakController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view petak')->only(['index']);
        $this->middleware('permission:create petak')->only(['create', 'store']);
        $this->middleware('permission:edit petak')->only(['edit', 'update']);
        $this->middleware('permission:delete petak')->only(['destroy']);
    }

    public function index()
    {
        $petaks = Petak::orderBy('kode_petak')->paginate(15);
        $totalLuas = Petak::aktif()->sum('luas_area');
        $totalPetak = Petak::aktif()->count();
        return view('petak.index', compact('petaks', 'totalLuas', 'totalPetak'));
    }

    public function create()
    {
        return view('petak.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_petak'        => 'required|string|max:20|unique:petaks,kode_petak',
            'nama_petak'        => 'required|string|max:100',
            'luas_area'         => 'required|numeric|min:0.01',
            'lokasi_wilayah'    => 'required|string|max:100',
            'pintu_air'         => 'nullable|string|max:100',
            'penanggung_jawab'  => 'nullable|string|max:100',
            'status'            => 'required|in:aktif,nonaktif',
            'keterangan'        => 'nullable|string',
            'latitude'  => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        Petak::create($request->all());
        return redirect()->route('petak.index')->with('success', 'Petak berhasil ditambahkan.');
    }

    public function edit(Petak $petak)
    {
        $daerahIrigasis = \App\Models\DaerahIrigasi::where('status', 'aktif')->orderBy('kode')->get();
        return view('petak.edit', compact('petak', 'daerahIrigasis'));
    }

    public function update(Request $request, Petak $petak)
    {
        $request->validate([
            'kode_petak'        => 'required|string|max:20|unique:petaks,kode_petak,' . $petak->id,
            'nama_petak'        => 'required|string|max:100',
            'luas_area'         => 'required|numeric|min:0.01',
            'lokasi_wilayah'    => 'required|string|max:100',
            'pintu_air'         => 'nullable|string|max:100',
            'penanggung_jawab'  => 'nullable|string|max:100',
            'status'            => 'required|in:aktif,nonaktif',
            'keterangan'        => 'nullable|string',
            'latitude'  => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'daerah_irigasi_id' => 'nullable|exists:daerah_irigasis,id',
        ]);

        $petak->update($request->all());
        return redirect()->route('petak.index')->with('success', 'Petak berhasil diperbarui.');
    }

    public function destroy(Petak $petak)
    {
        $petak->delete();
        return redirect()->route('petak.index')->with('success', 'Petak berhasil dihapus.');
    }
}
