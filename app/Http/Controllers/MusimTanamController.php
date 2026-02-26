<?php
// app/Http/Controllers/MusimTanamController.php

namespace App\Http\Controllers;

use App\Models\MusimTanam;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class MusimTanamController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view musim-tanam')->only(['index']);
        $this->middleware('permission:create musim-tanam')->only(['create', 'store']);
        $this->middleware('permission:edit musim-tanam')->only(['edit', 'update']);
        $this->middleware('permission:delete musim-tanam')->only(['destroy']);
    }

    public function index()
    {
        $musimTanams = MusimTanam::orderBy('tanggal_mulai', 'desc')->paginate(10);
        $mtBerjalan  = MusimTanam::berjalan()->first();
        return view('musim_tanam.index', compact('musimTanams', 'mtBerjalan'));
    }

    public function create()
    {
        return view('musim_tanam.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_mt'           => 'required|string|max:100',
            'jenis_mt'          => 'required|in:MT1,MT2,MT3,MK',
            'tanggal_mulai'     => 'required|date',
            'tanggal_selesai'   => 'required|date|after:tanggal_mulai',
            'target_luas_tanam' => 'required|numeric|min:0',
            'jenis_tanaman'     => 'required|string|max:100',
            'status'            => 'required|in:rencana,berjalan,selesai',
            'keterangan'        => 'nullable|string',
        ]);

        // Hanya boleh 1 MT berstatus berjalan
        if ($request->status === 'berjalan') {
            MusimTanam::where('status', 'berjalan')->update(['status' => 'selesai']);
        }

        MusimTanam::create($request->all());
        return redirect()->route('musim-tanam.index')->with('success', 'Musim tanam berhasil ditambahkan.');
    }

    public function edit(MusimTanam $musimTanam)
    {
        return view('musim_tanam.edit', compact('musimTanam'));
    }

    public function update(Request $request, MusimTanam $musimTanam)
    {
        $request->validate([
            'nama_mt'           => 'required|string|max:100',
            'jenis_mt'          => 'required|in:MT1,MT2,MT3,MK',
            'tanggal_mulai'     => 'required|date',
            'tanggal_selesai'   => 'required|date|after:tanggal_mulai',
            'target_luas_tanam' => 'required|numeric|min:0',
            'jenis_tanaman'     => 'required|string|max:100',
            'status'            => 'required|in:rencana,berjalan,selesai',
            'keterangan'        => 'nullable|string',
        ]);

        if ($request->status === 'berjalan') {
            MusimTanam::where('status', 'berjalan')
                ->where('id', '!=', $musimTanam->id)
                ->update(['status' => 'selesai']);
        }

        $musimTanam->update($request->all());
        return redirect()->route('musim-tanam.index')->with('success', 'Musim tanam berhasil diperbarui.');
    }

    public function destroy(MusimTanam $musimTanam)
    {
        $musimTanam->delete();
        return redirect()->route('musim-tanam.index')->with('success', 'Musim tanam berhasil dihapus.');
    }
}
