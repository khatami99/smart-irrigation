<?php
// app/Http/Controllers/RttController.php

namespace App\Http\Controllers;

use App\Models\Rtt;
use App\Models\Petak;
use App\Models\MusimTanam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RttController extends Controller
{
    public function index(Request $request)
    {
        $musimTanams = MusimTanam::orderBy('tanggal_mulai', 'desc')->get();
        $mtAktif     = MusimTanam::berjalan()->first();
        $mtId        = $request->get('musim_tanam_id', $mtAktif?->id);
        $mt          = $mtId ? MusimTanam::find($mtId) : $mtAktif;

        $rtts = Rtt::with(['petak', 'musimTanam'])
            ->when($mtId, fn($q) => $q->where('musim_tanam_id', $mtId))
            ->orderBy('urutan_rotasi')
            ->get();

        // Data untuk Gantt chart
        $ganttData = $rtts->map(fn($r) => [
            'id'            => $r->id,
            'petak'         => $r->petak->kode_petak,
            'nama_petak'    => $r->petak->nama_petak,
            'mulai'         => $r->rencana_mulai_tanam->format('Y-m-d'),
            'selesai'       => $r->rencana_selesai_tanam->format('Y-m-d'),
            'mulai_real'    => $r->realisasi_mulai_tanam?->format('Y-m-d'),
            'selesai_real'  => $r->realisasi_selesai_tanam?->format('Y-m-d'),
            'status'        => $r->status,
            'progress'      => $r->progress,
            'urutan_rotasi' => $r->urutan_rotasi,
            'jadwal_fase'   => $r->jadwal_fase ?? [],
        ])->values()->toArray();

        // Summary stats
        $stats = [
            'total'       => $rtts->count(),
            'rencana'     => $rtts->where('status', 'rencana')->count(),
            'berjalan'    => $rtts->where('status', 'berjalan')->count(),
            'selesai'     => $rtts->where('status', 'selesai')->count(),
            'target_luas' => $rtts->sum('target_luas'),
            'real_luas'   => $rtts->sum('realisasi_luas'),
        ];

        return view('rtt.index', compact('rtts', 'musimTanams', 'mt', 'mtId', 'ganttData', 'stats'));
    }

    public function create()
    {
        $petaks      = Petak::aktif()->orderBy('kode_petak')->get();
        $musimTanams = MusimTanam::orderBy('tanggal_mulai', 'desc')->get();
        $mtAktif     = MusimTanam::berjalan()->first();

        // Petak yang belum punya RTT di MT aktif
        $petakSudahAda = $mtAktif
            ? Rtt::where('musim_tanam_id', $mtAktif->id)->pluck('petak_id')->toArray()
            : [];

        return view('rtt.create', compact('petaks', 'musimTanams', 'mtAktif', 'petakSudahAda'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'petak_id'           => 'required|exists:petaks,id',
            'musim_tanam_id'     => 'required|exists:musim_tanams,id',
            'rencana_mulai_tanam'   => 'required|date',
            'rencana_selesai_tanam' => 'required|date|after:rencana_mulai_tanam',
            'target_luas'        => 'required|numeric|min:0.01',
            'urutan_rotasi'      => 'required|integer|min:1',
            'durasi_rotasi_hari' => 'required|integer|min:1',
        ]);

        // Cek duplikat
        $exists = Rtt::where('petak_id', $request->petak_id)
            ->where('musim_tanam_id', $request->musim_tanam_id)
            ->exists();

        if ($exists) {
            return back()->withInput()->withErrors([
                'petak_id' => 'Petak ini sudah memiliki RTT di musim tanam tersebut!'
            ]);
        }

        // Generate jadwal fase otomatis
        $jadwalFase = Rtt::generateJadwalFase(
            $request->rencana_mulai_tanam,
            $request->rencana_selesai_tanam
        );

        Rtt::create(array_merge($request->all(), [
            'user_id'     => Auth::id(),
            'jadwal_fase' => $jadwalFase,
        ]));

        return redirect()->route('rtt.index')
            ->with('success', 'RTT berhasil ditambahkan!');
    }

    public function edit(Rtt $rtt)
    {
        $petaks      = Petak::aktif()->orderBy('kode_petak')->get();
        $musimTanams = MusimTanam::orderBy('tanggal_mulai', 'desc')->get();
        return view('rtt.edit', compact('rtt', 'petaks', 'musimTanams'));
    }

    public function update(Request $request, Rtt $rtt)
    {
        $request->validate([
            'rencana_mulai_tanam'    => 'required|date',
            'rencana_selesai_tanam'  => 'required|date|after:rencana_mulai_tanam',
            'target_luas'            => 'required|numeric|min:0.01',
            'realisasi_luas'         => 'nullable|numeric|min:0',
            'realisasi_mulai_tanam'  => 'nullable|date',
            'realisasi_selesai_tanam'=> 'nullable|date',
            'urutan_rotasi'          => 'required|integer|min:1',
            'durasi_rotasi_hari'     => 'required|integer|min:1',
            'status'                 => 'required|in:rencana,berjalan,selesai,batal',
        ]);

        // Regenerate jadwal fase kalau tanggal berubah
        if ($request->rencana_mulai_tanam != $rtt->rencana_mulai_tanam->format('Y-m-d') ||
            $request->rencana_selesai_tanam != $rtt->rencana_selesai_tanam->format('Y-m-d')) {
            $jadwalFase = Rtt::generateJadwalFase(
                $request->rencana_mulai_tanam,
                $request->rencana_selesai_tanam
            );
            $request->merge(['jadwal_fase' => $jadwalFase]);
        }

        $rtt->update($request->all());
        return redirect()->route('rtt.index')
            ->with('success', 'RTT berhasil diperbarui!');
    }

    public function destroy(Rtt $rtt)
    {
        $rtt->delete();
        return redirect()->route('rtt.index')
            ->with('success', 'RTT berhasil dihapus.');
    }
}
