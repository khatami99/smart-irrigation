<?php

namespace App\Http\Controllers;

use App\Models\IrrigationData;
use App\Services\IrrigationDataService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\MusimTanam;

class IrrigationController extends Controller
{
    public function index()
    {
        // ── Prediksi AI ─────────────────────────────────────────
        $pythonPath = base_path('predict.py');
        $output     = shell_exec("python $pythonPath");
        // dd($output);

        preg_match('/Prediksi Kebutuhan Air Besok: ([\d.]+)/', $output, $m1);
        preg_match('/Akurasi Model R2: ([\d.]+)/',             $output, $m2);
        preg_match('/RMSE: ([\d.]+)/',                         $output, $m3);

        $forecast  = (float) ($m1[1] ?? 0.0);

        Log::info('Prediksi AI', [
            'forecast' => $forecast,
            'r2'       => (float) ($m2[1] ?? 0.0),
            'rmse'     => (float) ($m3[1] ?? 0.0),
        ]);

        // ── Threshold adaptif berdasarkan distribusi data historis ──
        $stats  = IrrigationData::selectRaw('AVG(kebutuhan_air) as avg, STDDEV(kebutuhan_air) as stddev')->first();
        $avg    = (float) ($stats->avg    ?? 5);
        $stddev = (float) ($stats->stddev ?? 1.5);

        $thresholdTinggi = $avg + ($stddev * 0.5);
        $thresholdRendah = $avg - ($stddev * 0.5);

        if ($forecast > $thresholdTinggi) {
            $recommendation = [
                'status' => 'Tinggi',
                'color'  => 'text-red-500',
                'msg'    => 'Kebutuhan air di atas rata-rata historis. Pantau debit saluran lebih ketat.',
            ];
        } elseif ($forecast >= $thresholdRendah) {
            $recommendation = [
                'status' => 'Normal',
                'color'  => 'text-blue-400',
                'msg'    => 'Kebutuhan air dalam rentang normal. Jalankan jadwal irigasi seperti biasa.',
            ];
        } else {
            $recommendation = [
                'status' => 'Rendah',
                'color'  => 'text-emerald-400',
                'msg'    => 'Kebutuhan air di bawah rata-rata. Efisiensi penggunaan pompa bisa ditingkatkan.',
            ];
        }

        // ── Data chart 30 hari terakhir ──────────────────────────
        $allData   = IrrigationData::orderBy('tanggal', 'asc')
                        ->where('tanggal', '>=', now()->subDays(30)->format('Y-m-d'))
                        ->get();
        $labels    = $allData->pluck('tanggal');
        $kebutuhan = $allData->pluck('kebutuhan_air');

        // ── Rerata kebutuhan (30 hari) ───────────────────────────
        $rerata = round($allData->avg('kebutuhan_air'), 2);

        $tableData = IrrigationData::orderBy('tanggal', 'desc')->paginate(10);

        $musimTanams = MusimTanam::orderBy('tanggal_mulai', 'desc')->get();
        $mtAktif     = MusimTanam::where('status', 'berjalan')->first();

        // ── Data peta mini dashboard ─────────────────────────────
        $petaksPeta = \App\Models\Petak::with(['rtt' => function($q) use ($mtAktif) {
            if ($mtAktif) $q->where('musim_tanam_id', $mtAktif->id);
            $q->latest();
        }])
        ->whereNotNull('latitude')
        ->whereNotNull('longitude')
        ->where('status', 'aktif')
        ->get()
        ->map(function($petak) {
            $rtt = $petak->rtt->first();
            $warna = match($rtt?->status) {
                'berjalan'  => '#4a7c6f',
                'terlambat' => '#b94a3c',
                'rencana'   => '#c4895a',
                'selesai'   => '#5a7a47',
                default     => '#7a6355',
            };
            return [
                'id'       => $petak->id,
                'nama'     => $petak->nama_petak,
                'kode'     => $petak->kode_petak,
                'lat'      => (float) $petak->latitude,
                'lng'      => (float) $petak->longitude,
                'luas'     => $petak->luas_area,
                'status'   => $rtt?->status ?? 'belum ada RTT',
                'warna'    => $warna,
            ];
        });

        return view('irrigation.index', compact(
            'labels', 'kebutuhan', 'forecast',
            'recommendation', 'tableData', 'rerata',
            'avg', 'thresholdRendah', 'thresholdTinggi',
            'musimTanams', 'mtAktif',
            'petaksPeta'
        ));
    }

    public function dataIklim()
    {
        $tableData = IrrigationData::orderBy('tanggal', 'desc')->paginate(15);
        return view('irrigation.data', compact('tableData'));
    }

    public function create()
    {
        return view('irrigation.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal'          => 'required|date|unique:irrigation_data,tanggal',
            'suhu_max'         => 'required|numeric|min:0|max:60',
            'suhu_min'         => 'required|numeric|min:0|max:60',
            'kelembaban'       => 'required|numeric|min:0|max:100',
            'kecepatan_angin'  => 'required|numeric|min:0',
            'radiasi_matahari' => 'required|numeric|min:0',
            'kc'               => 'required|numeric|min:0|max:2',
            'curah_hujan'      => 'required|numeric|min:0',
        ], [
            'tanggal.unique' => 'Data untuk tanggal ini sudah ada.',
        ]);

        $eto          = IrrigationDataService::hitungETo($request->suhu_max, $request->suhu_min, $request->kelembaban, $request->kecepatan_angin, $request->radiasi_matahari);
        $etc          = IrrigationDataService::hitungETc($eto, $request->kc);
        $kebutuhanAir = IrrigationDataService::hitungKebutuhanAir($etc, $request->curah_hujan);

        IrrigationData::create([
            'tanggal'          => $request->tanggal,
            'suhu_max'         => $request->suhu_max,
            'suhu_min'         => $request->suhu_min,
            'kelembaban'       => $request->kelembaban,
            'kecepatan_angin'  => $request->kecepatan_angin,
            'radiasi_matahari' => $request->radiasi_matahari,
            'kc'               => $request->kc,
            'curah_hujan'      => $request->curah_hujan,
            'eto'              => $eto,
            'etc'              => $etc,
            'kebutuhan_air'    => $kebutuhanAir,
        ]);

        return redirect()->route('irrigation.index')->with('success', 'Data berhasil ditambahkan!');
    }

    public function edit(IrrigationData $irrigationData)
    {
        return view('irrigation.edit', compact('irrigationData'));
    }

    public function update(Request $request, IrrigationData $irrigationData)
    {
        $request->validate([
            'tanggal'          => 'required|date|unique:irrigation_data,tanggal,' . $irrigationData->id,
            'suhu_max'         => 'required|numeric|min:0|max:60',
            'suhu_min'         => 'required|numeric|min:0|max:60',
            'kelembaban'       => 'required|numeric|min:0|max:100',
            'kecepatan_angin'  => 'required|numeric|min:0',
            'radiasi_matahari' => 'required|numeric|min:0',
            'kc'               => 'required|numeric|min:0|max:2',
            'curah_hujan'      => 'required|numeric|min:0',
        ], [
            'tanggal.unique' => 'Data untuk tanggal ini sudah ada.',
        ]);

        $eto          = IrrigationDataService::hitungETo($request->suhu_max, $request->suhu_min, $request->kelembaban, $request->kecepatan_angin, $request->radiasi_matahari);
        $etc          = IrrigationDataService::hitungETc($eto, $request->kc);
        $kebutuhanAir = IrrigationDataService::hitungKebutuhanAir($etc, $request->curah_hujan);

        $irrigationData->update([
            'tanggal'          => $request->tanggal,
            'suhu_max'         => $request->suhu_max,
            'suhu_min'         => $request->suhu_min,
            'kelembaban'       => $request->kelembaban,
            'kecepatan_angin'  => $request->kecepatan_angin,
            'radiasi_matahari' => $request->radiasi_matahari,
            'kc'               => $request->kc,
            'curah_hujan'      => $request->curah_hujan,
            'eto'              => $eto,
            'etc'              => $etc,
            'kebutuhan_air'    => $kebutuhanAir,
        ]);

        return redirect()->route('irrigation.index')->with('success', 'Data berhasil diperbarui!');
    }

    public function destroy(IrrigationData $irrigationData)
    {
        $irrigationData->delete();
        return redirect()->route('irrigation.index')->with('success', 'Data berhasil dihapus!');
    }
}
