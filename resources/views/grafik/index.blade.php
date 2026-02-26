@extends('layouts.app')
@section('title', 'Grafik & Visualisasi — Smart Irrigation')
@section('page-title', 'Grafik & Visualisasi')

@section('content')
<style>
    :root { --soil:#3d2b1f;--soil2:#5c3d2e;--earth:#8b5e3c;--clay:#c4895a;--straw:#e8d5a3;--cream:#faf6ef;--cream2:#f5ede0;--water:#4a7c6f;--water2:#6aab9a;--leaf:#5a7a47;--text:#4a3728;--textlt:#7a6355;--border:rgba(139,94,60,.14); }
    .card { background:var(--cream);border:1px solid var(--border);border-radius:14px; }
    .filter-input { background:var(--cream);border:1.5px solid rgba(139,94,60,.18);color:var(--text);border-radius:8px;padding:.55rem .9rem;font-size:.85rem;font-family:'Karla',sans-serif;outline:none;transition:border-color .2s; }
    .filter-input:focus { border-color:var(--water); }

    /* Mode tabs */
    .mode-tabs { display:flex;gap:.4rem;background:var(--cream2);border:1px solid var(--border);border-radius:10px;padding:.3rem; }
    .mode-tab {
        flex:1;text-align:center;padding:.5rem .75rem;border-radius:7px;
        font-size:.8rem;font-weight:600;cursor:pointer;border:none;
        background:transparent;color:var(--textlt);font-family:'Karla',sans-serif;
        transition:all .2s;
    }
    .mode-tab.active { background:var(--soil);color:var(--straw);box-shadow:0 2px 8px rgba(61,43,31,.2); }
    .mode-tab:hover:not(.active) { background:rgba(139,94,60,.08);color:var(--text); }

    /* Dataset toggle */
    .dataset-toggle { display:flex;flex-wrap:wrap;gap:.5rem; }
    .ds-btn {
        display:flex;align-items:center;gap:.4rem;padding:.4rem .85rem;
        border-radius:20px;border:1.5px solid;font-size:.78rem;font-weight:600;
        cursor:pointer;font-family:'Karla',sans-serif;transition:all .2s;background:transparent;
    }
    .ds-dot { width:10px;height:10px;border-radius:50%;flex-shrink:0; }
    .ds-btn.active { color:#fff; }
    .ds-btn.active .ds-dot { background:#fff; }

    /* Chart containers */
    .chart-wrap { position:relative;width:100%; }

    /* Stat cards */
    .stat-mini { background:var(--cream2);border:1px solid var(--border);border-radius:10px;padding:1rem 1.25rem; }
    .stat-mini-label { font-size:.63rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--textlt);margin-bottom:.3rem; }
    .stat-mini-val { font-family:'Fraunces',serif;font-size:1.4rem;font-weight:700;color:var(--soil);line-height:1; }
    .stat-mini-unit { font-size:.72rem;font-weight:400;color:var(--textlt);margin-left:.2rem; }
</style>

{{-- Top --}}
<div style="display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:1.5rem;">
    <div>
        <p style="font-size:.68rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--textlt);margin-bottom:.3rem;">Analisis Data</p>
        <h2 style="font-family:'Fraunces',serif;font-size:1.6rem;font-weight:700;color:var(--soil);letter-spacing:-.02em;">Grafik & Visualisasi</h2>
        <p style="font-size:.82rem;color:var(--textlt);font-weight:300;margin-top:.2rem;">Tren kebutuhan air, debit, curah hujan & luas areal</p>
    </div>
</div>

{{-- Filter Panel --}}
<div class="card" style="padding:1.25rem;margin-bottom:1.25rem;">
    <div style="display:flex;flex-wrap:wrap;gap:1rem;align-items:flex-end;">

        {{-- Mode tabs --}}
        <div style="flex:1;min-width:280px;">
            <p style="font-size:.68rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--textlt);margin-bottom:.5rem;">Periode</p>
            <div class="mode-tabs">
                <button class="mode-tab {{ $mode == 'harian'  ? 'active' : '' }}" onclick="gantiMode('harian')">Per Hari</button>
                <button class="mode-tab {{ $mode == 'dekade'  ? 'active' : '' }}" onclick="gantiMode('dekade')">Per Dekade</button>
                <button class="mode-tab {{ $mode == 'bulanan' ? 'active' : '' }}" onclick="gantiMode('bulanan')">Per Bulan</button>
                <button class="mode-tab {{ $mode == 'musim'   ? 'active' : '' }}" onclick="gantiMode('musim')">Per MT</button>
            </div>
        </div>

        {{-- Filter MT --}}
        <div id="filter-mt" style="{{ $mode == 'musim' ? 'display:none' : '' }}">
            <p style="font-size:.68rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--textlt);margin-bottom:.5rem;">Musim Tanam</p>
            <select id="sel-mt" class="filter-input" onchange="refreshChart()">
                <option value="">Semua MT</option>
                @foreach($musimTanams as $mt)
                    <option value="{{ $mt->id }}" {{ $mtId == $mt->id ? 'selected' : '' }}>
                        {{ $mt->nama_mt }} @if($mt->status=='berjalan')(Aktif)@endif
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Filter Tahun --}}
        <div id="filter-tahun" style="{{ $mode == 'musim' ? 'display:none' : '' }}">
            <p style="font-size:.68rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--textlt);margin-bottom:.5rem;">Tahun</p>
            <select id="sel-tahun" class="filter-input" onchange="refreshChart()">
                @foreach($years as $y)
                    <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>
        </div>

        {{-- Filter Bulan --}}
        <div id="filter-bulan" style="{{ in_array($mode, ['musim','bulanan']) ? 'display:none' : '' }}">
            <p style="font-size:.68rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--textlt);margin-bottom:.5rem;">Bulan</p>
            <select id="sel-bulan" class="filter-input" onchange="refreshChart()">
                <option value="">Semua Bulan</option>
                @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $i => $bln)
                    <option value="{{ $i+1 }}" {{ $bulan == $i+1 ? 'selected' : '' }}>{{ $bln }}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>

{{-- Stat Summary --}}
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:1rem;margin-bottom:1.25rem;" id="stat-row">
    <div class="stat-mini">
        <div class="stat-mini-label">Rata-rata ETo</div>
        <div class="stat-mini-val" id="stat-eto">—<span class="stat-mini-unit">mm</span></div>
    </div>
    <div class="stat-mini">
        <div class="stat-mini-label">Rata-rata Kebutuhan</div>
        <div class="stat-mini-val" id="stat-kebutuhan">—<span class="stat-mini-unit">mm</span></div>
    </div>
    <div class="stat-mini">
        <div class="stat-mini-label">Avg Debit Realisasi</div>
        <div class="stat-mini-val" id="stat-debit">—<span class="stat-mini-unit">l/det</span></div>
    </div>
    <div class="stat-mini">
        <div class="stat-mini-label">Avg Luas Realisasi</div>
        <div class="stat-mini-val" id="stat-luas">—<span class="stat-mini-unit">ha</span></div>
    </div>
</div>

{{-- Chart 1: Kebutuhan Air --}}
<div class="card" style="padding:1.5rem;margin-bottom:1.25rem;">
    <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:1.25rem;flex-wrap:wrap;gap:.75rem;">
        <div>
            <p style="font-size:.65rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--textlt);margin-bottom:.25rem;">Chart 1</p>
            <h3 style="font-family:'Fraunces',serif;font-size:1.1rem;font-weight:700;color:var(--soil);">Kebutuhan Air & Evapotranspirasi</h3>
        </div>
        <div class="dataset-toggle" id="toggle-chart1">
            <button class="ds-btn active" data-chart="1" data-idx="0" onclick="toggleDataset(this)"
                style="border-color:#4a7c6f;background:#4a7c6f;">
                <span class="ds-dot"></span> Kebutuhan Air
            </button>
            <button class="ds-btn active" data-chart="1" data-idx="1" onclick="toggleDataset(this)"
                style="border-color:#8b5e3c;background:#8b5e3c;">
                <span class="ds-dot"></span> ETo
            </button>
            <button class="ds-btn active" data-chart="1" data-idx="2" onclick="toggleDataset(this)"
                style="border-color:#6aab9a;background:#6aab9a;">
                <span class="ds-dot"></span> ETc
            </button>
        </div>
    </div>
    <div class="chart-wrap" style="height:280px;">
        <canvas id="chart1"></canvas>
    </div>
</div>

{{-- Chart 2: Debit Air --}}
<div class="card" style="padding:1.5rem;margin-bottom:1.25rem;">
    <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:1.25rem;flex-wrap:wrap;gap:.75rem;">
        <div>
            <p style="font-size:.65rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--textlt);margin-bottom:.25rem;">Chart 2</p>
            <h3 style="font-family:'Fraunces',serif;font-size:1.1rem;font-weight:700;color:var(--soil);">Debit Air Rencana vs Realisasi</h3>
        </div>
        <div class="dataset-toggle">
            <button class="ds-btn active" data-chart="2" data-idx="0" onclick="toggleDataset(this)"
                style="border-color:#4a7c6f;background:#4a7c6f;">
                <span class="ds-dot"></span> Rencana
            </button>
            <button class="ds-btn active" data-chart="2" data-idx="1" onclick="toggleDataset(this)"
                style="border-color:#c4895a;background:#c4895a;">
                <span class="ds-dot"></span> Realisasi
            </button>
        </div>
    </div>
    <div class="chart-wrap" style="height:240px;">
        <canvas id="chart2"></canvas>
    </div>
</div>

{{-- Chart 3: Luas + Curah Hujan --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;">
    <div class="card" style="padding:1.5rem;">
        <div style="margin-bottom:1.25rem;">
            <p style="font-size:.65rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--textlt);margin-bottom:.25rem;">Chart 3</p>
            <h3 style="font-family:'Fraunces',serif;font-size:1.05rem;font-weight:700;color:var(--soil);">Luas Areal Rencana vs Realisasi</h3>
        </div>
        <div class="chart-wrap" style="height:220px;">
            <canvas id="chart3"></canvas>
        </div>
    </div>
    <div class="card" style="padding:1.5rem;">
        <div style="margin-bottom:1.25rem;">
            <p style="font-size:.65rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--textlt);margin-bottom:.25rem;">Chart 4</p>
            <h3 style="font-family:'Fraunces',serif;font-size:1.05rem;font-weight:700;color:var(--soil);">Curah Hujan</h3>
        </div>
        <div class="chart-wrap" style="height:220px;">
            <canvas id="chart4"></canvas>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // ── State ──
    let currentMode = '{{ $mode }}';
    let charts = {};

    const chartDefaults = {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            x: { grid: { display: false }, ticks: { font: { size: 10, family: 'Karla' }, color: '#7a6355', maxTicksLimit: 12 } },
            y: { beginAtZero: true, grid: { color: 'rgba(139,94,60,.07)' }, ticks: { font: { size: 10, family: 'Karla' }, color: '#7a6355' } }
        },
        animation: { duration: 400 }
    };

    function makeChart(id, datasets, type = 'line') {
        if (charts[id]) charts[id].destroy();
        charts[id] = new Chart(document.getElementById(id), {
            type,
            data: { labels: [], datasets },
            options: JSON.parse(JSON.stringify(chartDefaults))
        });
        return charts[id];
    }

    function lineDs(label, color, fill = false) {
        return {
            label, data: [],
            borderColor: color,
            backgroundColor: fill ? color.replace(')', ',.06)').replace('rgb','rgba') : 'transparent',
            fill, tension: 0.4, pointRadius: 3,
            pointBackgroundColor: '#faf6ef', pointBorderColor: color,
            pointBorderWidth: 2, borderWidth: 2
        };
    }

    function barDs(label, color) {
        return {
            label, data: [],
            backgroundColor: color + '80',
            borderColor: color,
            borderWidth: 1.5, borderRadius: 4
        };
    }

    // ── Init charts ──
    charts['1'] = makeChart('chart1', [
        lineDs('Kebutuhan Air', '#4a7c6f', true),
        lineDs('ETo', '#8b5e3c'),
        lineDs('ETc', '#6aab9a'),
    ]);
    charts['2'] = makeChart('chart2', [
        barDs('Rencana', '#4a7c6f'),
        barDs('Realisasi', '#c4895a'),
    ], 'bar');
    charts['3'] = makeChart('chart3', [
        barDs('Rencana', '#5a7a47'),
        barDs('Realisasi', '#6aab9a'),
    ], 'bar');
    charts['4'] = makeChart('chart4', [
        { ...barDs('Curah Hujan', '#4a7c6f'), backgroundColor: 'rgba(74,124,111,.25)', borderColor: '#4a7c6f' }
    ], 'bar');

    // ── Fetch data & update ──
    function refreshChart() {
        const mt    = document.getElementById('sel-mt')?.value || '';
        const tahun = document.getElementById('sel-tahun')?.value || '';
        const bulan = document.getElementById('sel-bulan')?.value || '';

        const params = new URLSearchParams({ mode: currentMode });
        if (mt)    params.set('musim_tanam_id', mt);
        if (tahun) params.set('tahun', tahun);
        if (bulan) params.set('bulan', bulan);

        fetch(`{{ route('grafik.data') }}?${params}`)
        .then(r => r.json())
        .then(d => {
            const labels = d.labels;

            // Chart 1
            charts['1'].data.labels = labels;
            charts['1'].data.datasets[0].data = d.kebutuhan;
            charts['1'].data.datasets[1].data = d.eto;
            charts['1'].data.datasets[2].data = d.etc;
            charts['1'].update();

            // Chart 2 — pakai label blangko (per dekade)
            charts['2'].data.labels = d.blangko_labels;
            charts['2'].data.datasets[0].data = d.debit_rencana;
            charts['2'].data.datasets[1].data = d.debit_realisasi;
            charts['2'].update();

            // Chart 3 — pakai label blangko
            charts['3'].data.labels = d.blangko_labels;
            charts['3'].data.datasets[0].data = d.luas_rencana;
            charts['3'].data.datasets[1].data = d.luas_realisasi;
            charts['3'].update();

            // Chart 4 — pakai label blangko
            charts['4'].data.labels = d.blangko_labels;
            charts['4'].data.datasets[0].data = d.curah_hujan;
            charts['4'].update();

            // Update stats
            const avg = arr => arr.length ? (arr.reduce((a,b)=>a+b,0)/arr.length).toFixed(2) : '—';
            document.getElementById('stat-eto').innerHTML       = avg(d.eto) + '<span style="font-size:.72rem;font-weight:400;color:var(--textlt);margin-left:.2rem;">mm</span>';
            document.getElementById('stat-kebutuhan').innerHTML = avg(d.kebutuhan) + '<span style="font-size:.72rem;font-weight:400;color:var(--textlt);margin-left:.2rem;">mm</span>';
            document.getElementById('stat-debit').innerHTML     = avg(d.debit_realisasi) + '<span style="font-size:.72rem;font-weight:400;color:var(--textlt);margin-left:.2rem;">l/det</span>';
            document.getElementById('stat-luas').innerHTML      = avg(d.luas_realisasi) + '<span style="font-size:.72rem;font-weight:400;color:var(--textlt);margin-left:.2rem;">ha</span>';
        });
    }

    // ── Mode switching ──
    function gantiMode(mode) {
        currentMode = mode;
        document.querySelectorAll('.mode-tab').forEach(t => t.classList.remove('active'));
        event.target.classList.add('active');

        // Tampilkan/sembunyikan filter sesuai mode
        const filterMt    = document.getElementById('filter-mt');
        const filterTahun = document.getElementById('filter-tahun');
        const filterBulan = document.getElementById('filter-bulan');

        if (mode === 'musim') {
            filterMt.style.display = 'none';
            filterTahun.style.display = 'none';
            filterBulan.style.display = 'none';
        } else if (mode === 'bulanan') {
            filterMt.style.display = '';
            filterTahun.style.display = '';
            filterBulan.style.display = 'none';
        } else {
            filterMt.style.display = '';
            filterTahun.style.display = '';
            filterBulan.style.display = '';
        }

        refreshChart();
    }

    // ── Toggle dataset visibility ──
    function toggleDataset(btn) {
        const chartId = btn.dataset.chart;
        const idx     = parseInt(btn.dataset.idx);
        const chart   = charts[chartId];
        const meta    = chart.getDatasetMeta(idx);

        meta.hidden = !meta.hidden;
        chart.update();

        if (meta.hidden) {
            btn.classList.remove('active');
            btn.style.background = 'transparent';
            btn.style.color = btn.style.borderColor;
        } else {
            btn.classList.add('active');
            btn.style.background = btn.style.borderColor;
            btn.style.color = '#fff';
        }
    }

    // Load awal
    refreshChart();
</script>
@endpush
