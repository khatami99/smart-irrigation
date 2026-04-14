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
        // $pythonPath = base_path('predict.py');
        // $output     = shell_exec("python $pythonPath");
        // // dd($output);

        // preg_match('/Prediksi Kebutuhan Air Besok: ([\d.]+)/', $output, $m1);
        // preg_match('/Akurasi Model R2: ([\d.]+)/',             $output, $m2);
        // preg_match('/RMSE: ([\d.]+)/',                         $output, $m3);

        // $forecast  = (float) ($m1[1] ?? 0.0);

        // Log::info('Prediksi AI', [
        //     'forecast' => $forecast,
        //     'r2'       => (float) ($m2[1] ?? 0.0),
        //     'rmse'     => (float) ($m3[1] ?? 0.0),
        // ]);

        // Prediksi Kebutuhan Air Besok
        $aiPrediction = \App\Models\AiPrediction::latest('trained_at')->first();
        $forecast     = (float) ($aiPrediction?->prediksi ?? 0);

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

        //data KP-01
        $summaryKp = null;
        if ($mtAktif) {
            $summaryKp = [
                'total_kebutuhan' => \App\Models\KebutuhanAirDi::where('musim_tanam_id', $mtAktif->id)
                    ->avg('kebutuhan_total'),
                'jumlah_di'       => \App\Models\DaerahIrigasi::where('status', 'aktif')->count(),
                'total_luas' => \App\Models\BlangkoO01::where('musim_tanam_id', $mtAktif->id)
                    ->selectRaw('SUM(luas_padi_usulan + luas_palawija_usulan + luas_tebu_usulan) as total')
                    ->value('total'),
                'total_data_iklim' => \App\Models\IrrigationData::count(),
            ];
        }
        // ── Data peta mini dashboard (per Daerah Irigasi) ────────
        $mtId = $mtAktif?->id;
        $daerahIrigasiPeta = \App\Models\DaerahIrigasi::with([
            'mapFeature',
            'petaks.rtt' => function($q) use ($mtId) {
                if ($mtId) $q->where('musim_tanam_id', $mtId);
            }
        ])
        ->whereNotNull('map_feature_id')
        ->where('status', 'aktif')
        ->get()
        ->map(function($di) use ($mtId) {
            $statusRtt = $di->getStatusRttAttribute($mtId);
            $warna = match($statusRtt) {
                'berjalan'     => '#4a7c6f',
                'terlambat'    => '#b94a3c',
                'rencana'      => '#c4895a',
                'selesai'      => '#5a7a47',
                default        => '#7a6355',
            };
            return [
                'nama'      => $di->nama,
                'kode'      => $di->kode,
                'luas'      => $di->luas_total ?? '—',
                'status'    => $statusRtt,
                'warna'     => $warna,
                'geojson'   => $di->mapFeature?->geojson,
            ];
        })->filter(fn($di) => !is_null($di['geojson']))->values();

        // ── Data kartu per DI ────────────────────────────────────
        $diKartu = \App\Models\DaerahIrigasi::with([
            'petaks.rtt' => function($q) use ($mtId) {
                if ($mtId) $q->where('musim_tanam_id', $mtId);
            }
        ])
        ->where('status', 'aktif')
        ->get()
        ->map(function($di) use ($mtId) {
            $rtts = $di->petaks->flatMap->rtt;
            $statusRtt = $di->getStatusRttAttribute($mtId);
            $warna = match($statusRtt) {
                'berjalan'  => '#4a7c6f',
                'terlambat' => '#b94a3c',
                'rencana'   => '#c4895a',
                'selesai'   => '#5a7a47',
                default     => '#7a6355',
            };
            $progressAvg = $rtts->count() > 0
                ? (int) round($rtts->avg(fn($r) => $r->progress))
                : 0;

            return [
                'nama'        => $di->nama,
                'kode'        => $di->kode,
                'total_petak' => $di->petaks->count(),
                'total_luas'  => $di->petaks->sum('luas_area'),
                'status'      => $statusRtt,
                'warna'       => $warna,
                'progress'    => $progressAvg,
            ];
        });
        return view('irrigation.index', compact(
            'labels', 'kebutuhan', 'forecast',
            'recommendation', 'tableData', 'rerata',
            'avg', 'thresholdRendah', 'thresholdTinggi',
            'musimTanams', 'mtAktif',
            'daerahIrigasiPeta',
            'diKartu',
            'summaryKp',
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
            'curah_hujan'      => 'required|numeric|min:0',
        ], [
            'tanggal.unique' => 'Data untuk tanggal ini sudah ada.',
        ]);

        $eto          = IrrigationDataService::hitungETo(
            $request->suhu_max, $request->suhu_min,
            $request->kelembaban, $request->kecepatan_angin,
            $request->radiasi_matahari
        );
        $etc          = IrrigationDataService::hitungETc($eto, 1.0); // Kc default, KpSatuService yang hitung per fase
        $kebutuhanAir = IrrigationDataService::hitungKebutuhanAir($etc, $request->curah_hujan);

        IrrigationData::create([
            'tanggal'          => $request->tanggal,
            'suhu_max'         => $request->suhu_max,
            'suhu_min'         => $request->suhu_min,
            'kelembaban'       => $request->kelembaban,
            'kecepatan_angin'  => $request->kecepatan_angin,
            'radiasi_matahari' => $request->radiasi_matahari,
            'kc'               => 1.0,
            'curah_hujan'      => $request->curah_hujan,
            'eto'              => $eto,
            'etc'              => $etc,
            'kebutuhan_air'    => $kebutuhanAir,
        ]);

        return redirect()->route('irrigation.index')
            ->with('success', 'Data iklim berhasil disimpan.');
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
            'curah_hujan'      => 'required|numeric|min:0',
        ], [
            'tanggal.unique' => 'Data untuk tanggal ini sudah ada.',
        ]);

        $eto          = IrrigationDataService::hitungETo(
            $request->suhu_max, $request->suhu_min,
            $request->kelembaban, $request->kecepatan_angin,
            $request->radiasi_matahari
        );
        $etc          = IrrigationDataService::hitungETc($eto, 1.0);
        $kebutuhanAir = IrrigationDataService::hitungKebutuhanAir($etc, $request->curah_hujan);

        $irrigationData->update([
            'tanggal'          => $request->tanggal,
            'suhu_max'         => $request->suhu_max,
            'suhu_min'         => $request->suhu_min,
            'kelembaban'       => $request->kelembaban,
            'kecepatan_angin'  => $request->kecepatan_angin,
            'radiasi_matahari' => $request->radiasi_matahari,
            'kc'               => 1.0,
            'curah_hujan'      => $request->curah_hujan,
            'eto'              => $eto,
            'etc'              => $etc,
            'kebutuhan_air'    => $kebutuhanAir,
        ]);

        return redirect()->route('irrigation.index')
            ->with('success', 'Data iklim berhasil diperbarui.');
    }

    public function destroy(IrrigationData $irrigationData)
    {
        $irrigationData->delete();
        return redirect()->route('irrigation.index')
            ->with('success', 'Data iklim berhasil dihapus.');
    }

    // ── CSV Import ──────────────────────────────────────────────
    public function importForm()
    {
        return view('irrigation.import');
    }

    public function importCsv(Request $request)
    {
        $request->validate([
            'file_csv' => 'required|file|mimes:csv,txt|max:2048',
        ], [
            'file_csv.required' => 'File CSV wajib dipilih.',
            'file_csv.mimes'    => 'File harus berformat CSV.',
            'file_csv.max'      => 'Ukuran file maksimal 2MB.',
        ]);

        $file  = $request->file('file_csv');
        $lines = file($file->getRealPath(), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        if (count($lines) < 2) {
            return back()->withErrors(['file_csv' => 'File CSV kosong atau tidak valid.']);
        }

        // Deteksi separator (comma atau semicolon)
        $separator = str_contains($lines[0], ';') ? ';' : ',';
        $header    = str_getcsv(array_shift($lines), $separator);
        $header    = array_map('trim', array_map('strtolower', $header));

        // Mapping kolom BMKG → field kita
        $kolom = [
            'tanggal'          => $this->cariKolom($header, ['tanggal', 'date', 'tn']),
            'suhu_max'         => $this->cariKolom($header, ['tx', 'suhu_max', 'tmax', 'suhu max']),
            'suhu_min'         => $this->cariKolom($header, ['tn', 'suhu_min', 'tmin', 'suhu min']),
            'kelembaban'       => $this->cariKolom($header, ['rh_avg', 'kelembaban', 'rh', 'humidity']),
            'kecepatan_angin'  => $this->cariKolom($header, ['ff_avg', 'kecepatan_angin', 'wind', 'ff_x']),
            'radiasi_matahari' => $this->cariKolom($header, ['ss', 'radiasi', 'radiasi_matahari', 'sunshine']),
            'curah_hujan'      => $this->cariKolom($header, ['rr', 'curah_hujan', 'rain', 'rainfall', 'ch']),
        ];

        // Cek kolom wajib
        $missing = array_filter($kolom, fn($v) => $v === null);
        if (!empty($missing)) {
            return back()->withErrors([
                'file_csv' => 'Kolom tidak ditemukan: ' . implode(', ', array_keys($missing)) . '. Pastikan format CSV sesuai template.'
            ]);
        }

        $berhasil = 0;
        $skip     = 0;
        $errors   = [];

        foreach ($lines as $i => $line) {
            $row = str_getcsv($line, $separator);
            $row = array_map('trim', $row);

            if (count($row) < count($header)) continue;

            $data = [];
            foreach ($kolom as $field => $idx) {
                $data[$field] = $row[$idx] ?? null;
            }

            // Parse tanggal — support berbagai format
            try {
                $tanggal = \Carbon\Carbon::parse($data['tanggal'])->format('Y-m-d');
            } catch (\Exception $e) {
                $errors[] = "Baris " . ($i + 2) . ": format tanggal tidak valid ({$data['tanggal']})";
                $skip++;
                continue;
            }

            // Skip jika tanggal sudah ada
            if (IrrigationData::where('tanggal', $tanggal)->exists()) {
                $skip++;
                continue;
            }

            // Konversi nilai — ganti koma desimal ke titik, handle 8888/9999 (kode missing BMKG)
            $suhuMax  = $this->parseAngka($data['suhu_max']);
            $suhuMin  = $this->parseAngka($data['suhu_min']);
            $rh       = $this->parseAngka($data['kelembaban']);
            $ws       = $this->parseAngka($data['kecepatan_angin']);
            $rs       = $this->parseAngka($data['radiasi_matahari']);
            $ch       = $this->parseAngka($data['curah_hujan']) ?? 0;

            // Skip baris dengan data missing kritis
            if (is_null($suhuMax) || is_null($suhuMin) || is_null($rh) || is_null($ws) || is_null($rs)) {
                $skip++;
                continue;
            }

            $eto          = IrrigationDataService::hitungETo($suhuMax, $suhuMin, $rh, $ws, $rs);
            $etc          = IrrigationDataService::hitungETc($eto, 1.0);
            $kebutuhanAir = IrrigationDataService::hitungKebutuhanAir($etc, $ch);

            IrrigationData::create([
                'tanggal'          => $tanggal,
                'suhu_max'         => $suhuMax,
                'suhu_min'         => $suhuMin,
                'kelembaban'       => $rh,
                'kecepatan_angin'  => $ws,
                'radiasi_matahari' => $rs,
                'kc'               => 1.0,
                'curah_hujan'      => $ch,
                'eto'              => $eto,
                'etc'              => $etc,
                'kebutuhan_air'    => $kebutuhanAir,
            ]);

            $berhasil++;
        }

        $msg = "Import selesai: {$berhasil} data berhasil diimpor.";
        if ($skip > 0) $msg .= " {$skip} baris dilewati (duplikat/data kosong).";

        return redirect()->route('irrigation.index')->with('success', $msg);
    }

    // Helper: cari index kolom dari beberapa kemungkinan nama
    private function cariKolom(array $header, array $candidates): ?int
    {
        foreach ($candidates as $name) {
            $idx = array_search($name, $header);
            if ($idx !== false) return (int) $idx;
        }
        return null;
    }

    // Helper: parse angka, return null jika missing (8888, 9999, -)
    private function parseAngka($val): ?float
    {
        if (is_null($val) || $val === '' || $val === '-' || $val === '8888' || $val === '9999') {
            return null;
        }
        return (float) str_replace(',', '.', $val);
    }
}
