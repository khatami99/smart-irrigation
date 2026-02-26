@extends('layouts.app')

@section('title', 'Dashboard — Smart Irrigation')
@section('page-title', 'Dashboard Monitoring')

@section('content')

<style>
    :root {
        --soil:   #3d2b1f; --soil2: #5c3d2e; --earth: #8b5e3c;
        --straw:  #e8d5a3; --cream: #faf6ef; --cream2: #f5ede0;
        --water:  #4a7c6f; --water2: #6aab9a; --leaf: #5a7a47;
        --text:   #4a3728; --textlt: #7a6355; --border: rgba(139,94,60,.14);
    }
    .card { background: var(--cream); border: 1px solid var(--border); border-radius: 14px; }
    .card-dark { background: var(--soil); border-radius: 14px; }
    .badge-water { background: rgba(74,124,111,.1); border: 1px solid rgba(74,124,111,.2); color: var(--water); border-radius: 6px; font-size: .72rem; font-weight: 700; padding: .25rem .65rem; letter-spacing: .04em; text-transform: uppercase; }
    .badge-leaf  { background: rgba(90,122,71,.1);  border: 1px solid rgba(90,122,71,.2);  color: var(--leaf);  border-radius: 6px; font-size: .72rem; font-weight: 700; padding: .25rem .65rem; letter-spacing: .04em; text-transform: uppercase; }
</style>

{{-- Top bar --}}
<div style="display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:1.75rem;">
    <div>
        <p style="font-size:.68rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--textlt);margin-bottom:.3rem;">Ikhtisar Sistem</p>
        <h2 style="font-family:'Fraunces',serif;font-size:1.6rem;font-weight:700;color:var(--soil);letter-spacing:-.02em;">Ringkasan Irigasi</h2>
        <p style="font-size:.82rem;color:var(--textlt);font-weight:300;margin-top:.2rem;">Data diolah dengan algoritma Linear Regression · Metode FAO-56</p>
    </div>
    <a href="{{ route('irrigation.create') }}"
       style="background:var(--soil);color:var(--straw);padding:.65rem 1.4rem;border-radius:8px;font-size:.85rem;font-weight:600;text-decoration:none;transition:background .2s;display:inline-flex;align-items:center;gap:.4rem;"
       onmouseover="this.style.background='var(--soil2)'" onmouseout="this.style.background='var(--soil)'">
        + Tambah Data
    </a>
</div>

{{-- Stat Cards --}}
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1.25rem;margin-bottom:1.75rem;">

    {{-- Rerata --}}
    <div class="card" style="padding:1.5rem;">
        <p style="font-size:.68rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--textlt);margin-bottom:.75rem;">Rerata Kebutuhan</p>
        <div style="display:flex;align-items:flex-end;gap:.5rem;">
            <span style="font-family:'Fraunces',serif;font-size:2.5rem;font-weight:700;color:var(--soil);line-height:1;">{{ number_format($kebutuhan->avg(), 1) }}</span>
            <span style="font-size:.85rem;color:var(--textlt);margin-bottom:.4rem;">mm/hari</span>
        </div>
        <div style="margin-top:.75rem;"><span class="badge-water">Avg harian</span></div>
    </div>

    {{-- Status --}}
    <div class="card" style="padding:1.5rem;">
        <p style="font-size:.68rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--textlt);margin-bottom:.75rem;">Status Sistem</p>
        <div style="display:flex;align-items:center;gap:.5rem;">
            <div style="width:8px;height:8px;border-radius:50%;background:var(--leaf);animation:pulse 2s infinite;flex-shrink:0;"></div>
            <span style="font-family:'Fraunces',serif;font-size:1.6rem;font-weight:700;color:var(--leaf);">Optimal</span>
        </div>
        <div style="margin-top:.75rem;"><span class="badge-leaf">Sistem aktif</span></div>
    </div>

    {{-- AI Prediction --}}
    <div class="card-dark" style="padding:1.5rem;position:relative;overflow:hidden;">
        <div style="position:absolute;top:0;left:0;right:0;bottom:0;background:radial-gradient(ellipse 80% 80% at 80% 20%,rgba(106,171,154,.15),transparent);pointer-events:none;"></div>
        <p style="font-size:.65rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:rgba(232,213,163,.45);margin-bottom:.75rem;">Prediksi AI · 24 Jam</p>
        <div style="display:flex;align-items:flex-end;gap:.5rem;">
            <span style="font-family:'Fraunces',serif;font-size:2.5rem;font-weight:700;color:var(--straw);line-height:1;">{{ $forecast }}</span>
            <span style="font-size:.85rem;color:rgba(232,213,163,.5);margin-bottom:.4rem;">mm</span>
        </div>
        <p style="font-size:.72rem;color:rgba(232,213,163,.4);margin-top:.3rem;">Estimasi kebutuhan air esok</p>
        <div style="margin-top:.75rem;">
            <span style="background:rgba(106,171,154,.15);border:1px solid rgba(106,171,154,.25);color:var(--water2);border-radius:6px;font-size:.72rem;font-weight:700;padding:.25rem .65rem;letter-spacing:.04em;text-transform:uppercase;">
                🤖 Linear Regression
            </span>
        </div>
        {{-- Rekomendasi --}}
        <div style="margin-top:1rem;padding-top:.85rem;border-top:1px solid rgba(255,255,255,.07);">
            <p style="font-size:.75rem;font-weight:600;{{ str_contains($recommendation['color'], 'red') ? 'color:#e8a090' : (str_contains($recommendation['color'], 'emerald') ? 'color:var(--water2)' : 'color:var(--straw)') }}">
                {{ $recommendation['status'] }}</p>
            <p style="font-size:.72rem;color:rgba(232,213,163,.4);margin-top:.2rem;line-height:1.5;">{{ $recommendation['msg'] }}</p>
        </div>
    </div>

</div>

{{-- Chart --}}
{{-- <div class="card" style="padding:1.75rem;margin-bottom:1.75rem;">
    <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:1.5rem;">
        <div>
            <p style="font-size:.68rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--textlt);margin-bottom:.3rem;">Visualisasi Data</p>
            <h3 style="font-family:'Fraunces',serif;font-size:1.15rem;font-weight:600;color:var(--soil);">Tren Kebutuhan Air Per 15 Hari</h3>
        </div>
        <div style="display:flex;align-items:center;gap:.4rem;font-size:.75rem;color:var(--textlt);">
            <div style="width:10px;height:10px;border-radius:50%;background:var(--water);"></div>
            Kebutuhan Air (mm)
        </div>
    </div>
    <div style="position:relative;height:280px;">
        <canvas id="chartKebutuhan"></canvas>
    </div>
</div> --}}

{{-- Chart dengan mode toggle --}}
<div class="card" style="padding:1.75rem;margin-bottom:1.75rem;">
    <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:1.25rem;flex-wrap:wrap;gap:.75rem;">
        <div>
            <p style="font-size:.68rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--textlt);margin-bottom:.3rem;">Visualisasi Data</p>
            <h3 style="font-family:'Fraunces',serif;font-size:1.15rem;font-weight:600;color:var(--soil);">Tren Kebutuhan Air</h3>
        </div>
        <div style="display:flex;align-items:center;gap:.5rem;flex-wrap:wrap;">
            <div style="display:flex;gap:.25rem;background:var(--cream2);border:1px solid var(--border);border-radius:8px;padding:.2rem;">
                <button onclick="gantiModeDashboard('harian', this)"  class="dash-mode-btn"
                    style="padding:.3rem .7rem;border-radius:6px;border:none;font-size:.75rem;font-weight:600;cursor:pointer;font-family:'Karla',sans-serif;background:var(--soil);color:var(--straw);">
                    Harian
                </button>
                <button onclick="gantiModeDashboard('dekade', this)"  class="dash-mode-btn"
                    style="padding:.3rem .7rem;border-radius:6px;border:none;font-size:.75rem;font-weight:600;cursor:pointer;font-family:'Karla',sans-serif;background:transparent;color:var(--textlt);">
                    Dekade
                </button>
                <button onclick="gantiModeDashboard('bulanan', this)" class="dash-mode-btn"
                    style="padding:.3rem .7rem;border-radius:6px;border:none;font-size:.75rem;font-weight:600;cursor:pointer;font-family:'Karla',sans-serif;background:transparent;color:var(--textlt);">
                    Bulan
                </button>
            </div>
            <a href="{{ route('grafik.index') }}"
               style="font-size:.75rem;color:var(--water);text-decoration:none;font-weight:600;">
                Lihat lengkap →
            </a>
        </div>
    </div>
    <div style="position:relative;height:280px;">
        <canvas id="chartKebutuhan"></canvas>
    </div>
</div>

{{-- Tabel --}}
<div id="table-wrapper" class="card" style="overflow:hidden;">
    @include('irrigation.partials.table', ['tableData' => $tableData])
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let dashChart;

function initDashChart(labels, data) {
    if (dashChart) dashChart.destroy();
    dashChart = new Chart(document.getElementById('chartKebutuhan'), {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Kebutuhan Air',
                data: data,
                borderColor: '#4a7c6f',
                backgroundColor: 'rgba(74,124,111,0.06)',
                fill: true, tension: 0.4, pointRadius: 3,
                pointBackgroundColor: '#faf6ef',
                pointBorderColor: '#4a7c6f',
                pointBorderWidth: 2, borderWidth: 2.5
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { display: false }, ticks: { font: { size: 10, family: 'Karla' }, color: '#7a6355', maxTicksLimit: 10 } },
                y: { beginAtZero: true, grid: { color: 'rgba(139,94,60,.07)' }, ticks: { font: { size: 10, family: 'Karla' }, color: '#7a6355' } }
            }
        }
    });
}

// Load awal pakai data dari PHP
initDashChart(@json($labels), @json($kebutuhan));

function gantiModeDashboard(mode, btn) {
    document.querySelectorAll('.dash-mode-btn').forEach(b => {
        b.style.background = 'transparent';
        b.style.color = 'var(--textlt)';
    });
    btn.style.background = 'var(--soil)';
    btn.style.color = 'var(--straw)';

    fetch(`{{ route('grafik.data') }}?mode=${mode}`)
    .then(r => r.json())
    .then(d => initDashChart(d.labels, d.kebutuhan));
}

    // AJAX Pagination
    document.addEventListener('click', function(e) {
        const link = e.target.closest('#table-wrapper a');
        if (!link) return;
        e.preventDefault();
        const url = link.href;
        startLoading();

        document.getElementById('table-wrapper').innerHTML = `
            <div style="padding:1.25rem 1.5rem;border-bottom:1px solid rgba(139,94,60,.1);display:flex;justify-content:space-between;align-items:center;">
                <div style="height:16px;width:180px;background:rgba(139,94,60,.1);border-radius:6px;animation:shimmer 1.5s ease-in-out infinite;"></div>
                <div style="height:16px;width:80px;background:rgba(139,94,60,.08);border-radius:20px;animation:shimmer 1.5s .1s ease-in-out infinite;"></div>
            </div>
            <div style="padding:1rem 0;">
                ${Array(8).fill('').map((_, i) => `
                <div style="display:flex;gap:1rem;padding:.85rem 1.5rem;border-bottom:1px solid rgba(139,94,60,.06);">
                    <div style="height:14px;width:90px;background:rgba(139,94,60,.09);border-radius:4px;animation:shimmer 1.5s ${i*.1}s ease-in-out infinite;"></div>
                    <div style="height:14px;width:40px;background:rgba(139,94,60,.07);border-radius:4px;margin:0 auto;animation:shimmer 1.5s ${i*.1+.1}s ease-in-out infinite;"></div>
                    <div style="height:14px;width:40px;background:rgba(139,94,60,.07);border-radius:4px;margin:0 auto;animation:shimmer 1.5s ${i*.1+.2}s ease-in-out infinite;"></div>
                    <div style="height:14px;width:50px;background:rgba(139,94,60,.07);border-radius:4px;margin:0 auto;animation:shimmer 1.5s ${i*.1+.3}s ease-in-out infinite;"></div>
                    <div style="height:14px;width:60px;background:rgba(139,94,60,.07);border-radius:4px;margin:0 auto;animation:shimmer 1.5s ${i*.1+.4}s ease-in-out infinite;"></div>
                    <div style="height:14px;width:80px;background:rgba(139,94,60,.07);border-radius:4px;margin:0 auto;animation:shimmer 1.5s ${i*.1+.5}s ease-in-out infinite;"></div>
                </div>`).join('')}
            </div>
            <style>
                @keyframes shimmer {
                    0%,100%{opacity:.5} 50%{opacity:1}
                }
            </style>
        `;

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.text())
        .then(html => {
            const doc = new DOMParser().parseFromString(html, 'text/html');
            const newTable = doc.querySelector('#table-wrapper');
            if (newTable) {
                document.getElementById('table-wrapper').innerHTML = newTable.innerHTML;
                window.history.pushState({}, '', url);
            }
            finishLoading();
        });
    });
</script>
@endpush
