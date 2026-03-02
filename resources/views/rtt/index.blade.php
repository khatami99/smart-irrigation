@extends('layouts.app')
@section('title', 'RTT — Smart Irrigation')
@section('page-title', 'Rencana Tata Tanam')

@section('content')
<style>
    :root { --soil:#3d2b1f;--soil2:#5c3d2e;--earth:#8b5e3c;--clay:#c4895a;--straw:#e8d5a3;--cream:#faf6ef;--cream2:#f5ede0;--water:#4a7c6f;--water2:#6aab9a;--leaf:#5a7a47;--text:#4a3728;--textlt:#7a6355;--border:rgba(139,94,60,.14); }
    .card { background:var(--cream);border:1px solid var(--border);border-radius:14px; }
    .filter-input { background:var(--cream);border:1.5px solid rgba(139,94,60,.18);color:var(--text);border-radius:8px;padding:.55rem .9rem;font-size:.85rem;font-family:'Karla',sans-serif;outline:none; }

    /* Stat cards */
    .stat-card { background:var(--cream2);border:1px solid var(--border);border-radius:10px;padding:1rem 1.25rem; }
    .stat-label { font-size:.63rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--textlt);margin-bottom:.3rem; }
    .stat-val { font-family:'Fraunces',serif;font-size:1.6rem;font-weight:700;color:var(--soil);line-height:1; }

    /* Gantt */
    .gantt-wrap { overflow-x:auto; }
    .gantt-table { border-collapse:collapse;min-width:900px;width:100%; }
    .gantt-table th { padding:.6rem 1rem;font-size:.65rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--textlt);border-bottom:2px solid var(--border);white-space:nowrap; }
    .gantt-row td { padding:.6rem 1rem;border-bottom:1px solid rgba(139,94,60,.06);vertical-align:middle; }
    .gantt-row:hover td { background:rgba(74,124,111,.03); }

    /* Progress bar */
    .progress-wrap { background:rgba(139,94,60,.08);border-radius:20px;height:8px;width:100%;min-width:80px;overflow:hidden; }
    .progress-bar { height:100%;border-radius:20px;transition:width .4s; }

    /* Fase badge */
    .fase-badge { display:inline-block;padding:.2rem .55rem;border-radius:5px;font-size:.7rem;font-weight:700; }

    /* Status badge */
    .badge-rencana  { background:rgba(196,137,90,.1);border:1px solid rgba(196,137,90,.25);color:#8b5e3c; }
    .badge-berjalan { background:rgba(74,124,111,.1);border:1px solid rgba(74,124,111,.25);color:#4a7c6f; }
    .badge-selesai  { background:rgba(90,122,71,.1); border:1px solid rgba(90,122,71,.25); color:#5a7a47; }
    .badge-batal    { background:rgba(185,74,60,.08);border:1px solid rgba(185,74,60,.2);  color:#a03828; }

    /* Gantt chart visual */
    .gantt-visual { position:relative;height:28px;border-radius:6px;overflow:hidden; }
    .gantt-bar { position:absolute;height:100%;border-radius:6px;display:flex;align-items:center;padding:0 .4rem;font-size:.68rem;font-weight:700;color:#fff;white-space:nowrap;overflow:hidden;transition:opacity .2s; }
    .gantt-bar:hover { opacity:.85; }
    .gantt-bar-plan { background:var(--water);opacity:.6; }
    .gantt-bar-real { background:var(--leaf); }

    /* Fase timeline */
    .fase-timeline { display:flex;height:12px;border-radius:4px;overflow:hidden;margin-top:4px; }
    .fase-seg { height:100%;transition:flex .3s; }

    /* Tab */
    .view-tab { padding:.5rem 1.1rem;border-radius:8px;border:none;font-size:.8rem;font-weight:600;cursor:pointer;font-family:'Karla',sans-serif;transition:all .2s; }
    .view-tab.active { background:var(--soil);color:var(--straw); }
    .view-tab:not(.active) { background:transparent;color:var(--textlt); }
    .view-tab:not(.active):hover { background:rgba(139,94,60,.08); }

    /* Rotasi badge */
    .rotasi-badge { display:inline-flex;align-items:center;justify-content:center;width:24px;height:24px;border-radius:50%;background:var(--soil);color:var(--straw);font-size:.75rem;font-weight:700; }

    /* Btn */
    .btn-sm { padding:.3rem .8rem;border-radius:6px;font-size:.75rem;font-weight:600;text-decoration:none;display:inline-block; }
    .btn-edit   { background:rgba(139,94,60,.08);border:1px solid rgba(139,94,60,.15);color:var(--earth); }
    .btn-delete { background:rgba(185,74,60,.07);border:1px solid rgba(185,74,60,.15);color:#a03828;cursor:pointer;font-family:'Karla',sans-serif; }
</style>

{{-- Top --}}
<div style="display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:1.5rem;flex-wrap:wrap;gap:1rem;">
    <div>
        <p style="font-size:.68rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--textlt);margin-bottom:.3rem;">Fase 4</p>
        <h2 style="font-family:'Fraunces',serif;font-size:1.6rem;font-weight:700;color:var(--soil);letter-spacing:-.02em;">Rencana Tata Tanam</h2>
        @if($mt)
        <p style="font-size:.82rem;color:var(--textlt);margin-top:.2rem;">
            Musim Tanam: <strong style="color:var(--soil);">{{ $mt->nama_mt }}</strong>
            <span style="margin:0 .4rem;">·</span>
            {{ $mt->tanggal_mulai->format('d M Y') }} — {{ $mt->tanggal_selesai->format('d M Y') }}
        </p>
        @endif
    </div>
    <div style="display:flex;gap:.75rem;align-items:center;">
        {{-- Filter MT --}}
        <form method="GET" action="{{ route('rtt.index') }}">
            <select name="musim_tanam_id" class="filter-input" onchange="this.form.submit()">
                @foreach($musimTanams as $mts)
                    <option value="{{ $mts->id }}" {{ $mtId == $mts->id ? 'selected' : '' }}>
                        {{ $mts->nama_mt }}
                    </option>
                @endforeach
            </select>
        </form>
        @can('create rtt')
        <a href="{{ route('rtt.create') }}"
           style="background:var(--soil);color:var(--straw);padding:.65rem 1.4rem;border-radius:8px;font-size:.85rem;font-weight:600;text-decoration:none;"
           onmouseover="this.style.background='var(--soil2)'" onmouseout="this.style.background='var(--soil)'">
            + Tambah RTT
        </a>
        @endcan
    </div>
</div>

{{-- Stats --}}
<div style="display:grid;grid-template-columns:repeat(5,1fr);gap:1rem;margin-bottom:1.25rem;">
    <div class="stat-card">
        <div class="stat-label">Total Petak</div>
        <div class="stat-val">{{ $stats['total'] }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Rencana</div>
        <div class="stat-val" style="color:var(--clay);">{{ $stats['rencana'] }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Berjalan</div>
        <div class="stat-val" style="color:var(--water);">{{ $stats['berjalan'] }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Selesai</div>
        <div class="stat-val" style="color:var(--leaf);">{{ $stats['selesai'] }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Target Luas</div>
        <div class="stat-val" style="font-size:1.2rem;">{{ number_format($stats['target_luas'],1) }}<span style="font-size:.75rem;font-weight:400;color:var(--textlt);margin-left:.2rem;">ha</span></div>
    </div>
</div>

{{-- View toggle --}}
<div style="display:flex;gap:.35rem;background:var(--cream2);border:1px solid var(--border);border-radius:10px;padding:.3rem;width:fit-content;margin-bottom:1.25rem;">
    <button class="view-tab active" id="tab-gantt" onclick="switchView('gantt')">📊 Gantt Chart</button>
    <button class="view-tab" id="tab-tabel" onclick="switchView('tabel')">📋 Tabel</button>
</div>

{{-- ═══ GANTT VIEW ═══ --}}
<div id="view-gantt">
    <div class="card" style="padding:1.5rem;">
        <div style="margin-bottom:1.25rem;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:.5rem;">
            <h3 style="font-family:'Fraunces',serif;font-size:1.1rem;font-weight:700;color:var(--soil);">Timeline Tanam per Petak</h3>
            <div style="display:flex;gap:1rem;font-size:.75rem;color:var(--textlt);">
                <span><span style="display:inline-block;width:12px;height:8px;background:var(--water);opacity:.6;border-radius:3px;margin-right:.3rem;"></span>Rencana</span>
                <span><span style="display:inline-block;width:12px;height:8px;background:var(--leaf);border-radius:3px;margin-right:.3rem;"></span>Realisasi</span>
            </div>
        </div>

        @if($rtts->isEmpty())
            <div style="text-align:center;padding:3rem;">
                <p style="font-size:2rem;margin-bottom:.75rem;">🌾</p>
                <p style="font-family:'Fraunces',serif;font-size:1.1rem;font-weight:600;color:var(--soil);">Belum ada RTT</p>
                <p style="font-size:.85rem;color:var(--textlt);margin-top:.4rem;">Tambah rencana tata tanam untuk musim ini.</p>
            </div>
        @else
            {{-- Gantt Chart Canvas --}}
            <div class="gantt-wrap">
                <div id="gantt-container" style="min-width:700px;">
                    {{-- Header bulan --}}
                    <div id="gantt-header" style="display:flex;margin-left:160px;margin-bottom:.4rem;"></div>
                    {{-- Rows --}}
                    @foreach($rtts as $rtt)
                    <div style="display:flex;align-items:center;margin-bottom:.5rem;" data-rtt="{{ json_encode(['mulai'=>$rtt->rencana_mulai_tanam->format('Y-m-d'),'selesai'=>$rtt->rencana_selesai_tanam->format('Y-m-d'),'mulai_real'=>$rtt->realisasi_mulai_tanam?->format('Y-m-d'),'selesai_real'=>$rtt->realisasi_selesai_tanam?->format('Y-m-d'),'fase'=>$rtt->jadwal_fase??[],'status'=>$rtt->status]) }}">
                        {{-- Label petak --}}
                        <div style="width:155px;flex-shrink:0;padding-right:.75rem;">
                            <p style="font-size:.82rem;font-weight:700;color:var(--soil);">{{ $rtt->petak->kode_petak }}</p>
                            <p style="font-size:.7rem;color:var(--textlt);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $rtt->petak->nama_petak }}</p>
                        </div>
                        {{-- Bar area --}}
                        <div style="flex:1;position:relative;">
                            <div class="gantt-visual" id="gantt-bar-{{ $rtt->id }}"></div>
                            <div class="fase-timeline" id="gantt-fase-{{ $rtt->id }}" style="margin-top:3px;"></div>
                        </div>
                    </div>
                    @endforeach

                    {{-- Legend fase --}}
                    <div style="margin-top:1rem;padding-top:1rem;border-top:1px solid var(--border);display:flex;flex-wrap:wrap;gap:.5rem;margin-left:160px;">
                        @foreach(['pengolahan_tanah'=>['#8b5e3c','Pengolahan'],'tanam'=>['#c4895a','Tanam'],'vegetatif'=>['#4a7c6f','Vegetatif'],'generatif'=>['#6aab9a','Generatif'],'pemasakan'=>['#5a7a47','Pemasakan'],'panen'=>['#3d2b1f','Panen']] as $key => $val)
                        <span style="font-size:.72rem;color:var(--textlt);display:flex;align-items:center;gap:.3rem;">
                            <span style="width:10px;height:10px;border-radius:2px;background:{{ $val[0] }};display:inline-block;"></span>
                            {{ $val[1] }}
                        </span>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

{{-- ═══ TABEL VIEW ═══ --}}
<div id="view-tabel" style="display:none;">
    <div class="card" style="overflow:hidden;">
        <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;">
                <thead>
                    <tr>
                        <th style="padding:.75rem 1.25rem;font-size:.65rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--textlt);border-bottom:2px solid var(--border);text-align:left;">Rotasi</th>
                        <th style="padding:.75rem 1.25rem;font-size:.65rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--textlt);border-bottom:2px solid var(--border);text-align:left;">Petak</th>
                        <th style="padding:.75rem 1.25rem;font-size:.65rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--textlt);border-bottom:2px solid var(--border);">Rencana Tanam</th>
                        <th style="padding:.75rem 1.25rem;font-size:.65rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--textlt);border-bottom:2px solid var(--border);">Realisasi Tanam</th>
                        <th style="padding:.75rem 1.25rem;font-size:.65rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--textlt);border-bottom:2px solid var(--border);text-align:center;">Target / Real<br><span style="font-weight:400;text-transform:none;">(ha)</span></th>
                        <th style="padding:.75rem 1.25rem;font-size:.65rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--textlt);border-bottom:2px solid var(--border);text-align:center;">Progress</th>
                        <th style="padding:.75rem 1.25rem;font-size:.65rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--textlt);border-bottom:2px solid var(--border);text-align:center;">Fase Sekarang</th>
                        <th style="padding:.75rem 1.25rem;font-size:.65rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--textlt);border-bottom:2px solid var(--border);text-align:center;">Status</th>
                        <th style="padding:.75rem 1.25rem;font-size:.65rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--textlt);border-bottom:2px solid var(--border);text-align:center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rtts as $rtt)
                    <tr style="border-bottom:1px solid rgba(139,94,60,.06);" onmouseover="this.style.background='rgba(74,124,111,.03)'" onmouseout="this.style.background=''">
                        <td style="padding:.85rem 1.25rem;text-align:center;">
                            <span class="rotasi-badge">{{ $rtt->urutan_rotasi }}</span>
                            <p style="font-size:.68rem;color:var(--textlt);margin-top:.2rem;">{{ $rtt->durasi_rotasi_hari }}h</p>
                        </td>
                        <td style="padding:.85rem 1.25rem;">
                            <p style="font-weight:700;color:var(--soil);font-size:.875rem;">{{ $rtt->petak->kode_petak }}</p>
                            <p style="font-size:.75rem;color:var(--textlt);">{{ $rtt->petak->nama_petak }}</p>
                            <p style="font-size:.7rem;color:var(--textlt);margin-top:.1rem;">{{ number_format($rtt->petak->luas_area,2) }} ha</p>
                        </td>
                        <td style="padding:.85rem 1.25rem;text-align:center;font-size:.82rem;">
                            <p style="color:var(--text);">{{ $rtt->rencana_mulai_tanam->format('d M Y') }}</p>
                            <p style="color:var(--textlt);">s/d {{ $rtt->rencana_selesai_tanam->format('d M Y') }}</p>
                            <p style="font-size:.7rem;color:var(--textlt);margin-top:.15rem;">{{ $rtt->durasi_rencana }} hari</p>
                        </td>
                        <td style="padding:.85rem 1.25rem;text-align:center;font-size:.82rem;">
                            @if($rtt->realisasi_mulai_tanam)
                                <p style="color:var(--leaf);">{{ $rtt->realisasi_mulai_tanam->format('d M Y') }}</p>
                                <p style="color:var(--textlt);">s/d {{ $rtt->realisasi_selesai_tanam?->format('d M Y') ?? '—' }}</p>
                            @else
                                <span style="color:var(--textlt);">—</span>
                            @endif
                        </td>
                        <td style="padding:.85rem 1.25rem;text-align:center;">
                            <p style="font-size:.875rem;color:var(--text);">{{ number_format($rtt->target_luas,2) }}</p>
                            @if($rtt->realisasi_luas)
                            <p style="font-size:.82rem;font-weight:700;color:{{ $rtt->efisiensi_luas >= 80 ? 'var(--leaf)' : 'var(--clay)' }};">
                                {{ number_format($rtt->realisasi_luas,2) }}
                                <span style="font-size:.68rem;font-weight:400;">({{ $rtt->efisiensi_luas }}%)</span>
                            </p>
                            @else
                            <p style="font-size:.75rem;color:var(--textlt);">— belum ada</p>
                            @endif
                        </td>
                        <td style="padding:.85rem 1.25rem;text-align:center;">
                            <div class="progress-wrap" style="margin:0 auto;">
                                <div class="progress-bar" style="width:{{ $rtt->progress }}%;background:{{ $rtt->progress >= 80 ? 'var(--leaf)' : ($rtt->progress >= 40 ? 'var(--water)' : 'var(--clay)') }};"></div>
                            </div>
                            <p style="font-size:.72rem;color:var(--textlt);margin-top:.3rem;">{{ $rtt->progress }}%</p>
                        </td>
                        <td style="padding:.85rem 1.25rem;text-align:center;">
                            @if($rtt->fase_sekarang)
                            @php
                                $faseColors = ['pengolahan_tanah'=>'#8b5e3c','tanam'=>'#c4895a','vegetatif'=>'#4a7c6f','generatif'=>'#6aab9a','pemasakan'=>'#5a7a47','panen'=>'#3d2b1f'];
                                $faseLabels = ['pengolahan_tanah'=>'Pengolahan','tanam'=>'Tanam','vegetatif'=>'Vegetatif','generatif'=>'Generatif','pemasakan'=>'Pemasakan','panen'=>'Panen'];
                            @endphp
                            <span class="fase-badge" style="background:{{ $faseColors[$rtt->fase_sekarang] ?? '#7a6355' }}20;border:1px solid {{ $faseColors[$rtt->fase_sekarang] ?? '#7a6355' }}40;color:{{ $faseColors[$rtt->fase_sekarang] ?? '#7a6355' }};">
                                {{ $faseLabels[$rtt->fase_sekarang] ?? $rtt->fase_sekarang }}
                            </span>
                            @else
                            <span style="color:var(--textlt);font-size:.82rem;">—</span>
                            @endif
                        </td>
                        <td style="padding:.85rem 1.25rem;text-align:center;">
                            <span class="fase-badge badge-{{ $rtt->status }}">{{ ucfirst($rtt->status) }}</span>
                        </td>
                        <td style="padding:.85rem 1.25rem;text-align:center;">
                            <div style="display:flex;gap:.4rem;justify-content:center;">
                                @can('edit rtt')
                                <a href="{{ route('rtt.edit', $rtt) }}" class="btn-sm btn-edit">Edit</a>
                                @endcan
                                @can('delete rtt')
                                <form method="POST" action="{{ route('rtt.destroy', $rtt) }}" style="margin:0;"
                                      onsubmit="return confirm('Hapus RTT {{ $rtt->petak->kode_petak }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-sm btn-delete">Hapus</button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" style="text-align:center;padding:3rem;color:var(--textlt);">
                            Belum ada RTT untuk musim tanam ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // ── View toggle ──
    function switchView(view) {
        document.getElementById('view-gantt').style.display = view === 'gantt' ? '' : 'none';
        document.getElementById('view-tabel').style.display = view === 'tabel' ? '' : 'none';
        document.getElementById('tab-gantt').className = 'view-tab' + (view === 'gantt' ? ' active' : '');
        document.getElementById('tab-tabel').className = 'view-tab' + (view === 'tabel' ? ' active' : '');
    }

    // ── Gantt Chart render ──
    const ganttData = @json($ganttData);
    const faseColors = {
        'pengolahan_tanah': '#8b5e3c',
        'tanam':            '#c4895a',
        'vegetatif':        '#4a7c6f',
        'generatif':        '#6aab9a',
        'pemasakan':        '#5a7a47',
        'panen':            '#3d2b1f',
        'bero':             '#e8d5a3',
    };

    function renderGantt() {
        if (!ganttData.length) return;

        // Cari range tanggal keseluruhan
        const allDates = ganttData.flatMap(r => [r.mulai, r.selesai, r.mulai_real, r.selesai_real].filter(Boolean));
        const minDate  = new Date(allDates.reduce((a,b) => a < b ? a : b));
        const maxDate  = new Date(allDates.reduce((a,b) => a > b ? a : b));
        const totalDays = Math.ceil((maxDate - minDate) / 86400000) + 1;

        // Header bulan
        const header = document.getElementById('gantt-header');
        if (header) {
            const bulanNama = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
            let bulanHTML = '';
            let dekadeHTML = '';

            // Mulai dari bulan pertama data, stop di bulan terakhir data
            let cur = new Date(minDate.getFullYear(), minDate.getMonth(), 1);
            const lastMonth = new Date(maxDate.getFullYear(), maxDate.getMonth(), 1);

            while (cur <= lastMonth) {
                const y = cur.getFullYear();
                const m = cur.getMonth();
                const daysInMonth = new Date(y, m+1, 0).getDate();

                const effStart = cur < minDate ? minDate : new Date(y, m, 1);
                const effEnd   = new Date(y, m, daysInMonth) > maxDate ? maxDate : new Date(y, m, daysInMonth);
                const effDays  = Math.round((effEnd - effStart) / 86400000) + 1;
                const pct      = (effDays / totalDays) * 100;

                bulanHTML += `<div style="flex:0 0 ${pct}%;min-width:0;font-size:.65rem;font-weight:700;color:var(--textlt);text-align:center;border-left:1px solid var(--border);padding:.25rem 0;overflow:hidden;">
                    ${bulanNama[m]} ${y}
                </div>`;

                // Dekade — hanya render yang masuk range
                const dekades = [
                    { label:'I',   start:1,  end:10 },
                    { label:'II',  start:11, end:20 },
                    { label:'III', start:21, end:daysInMonth },
                ];
                dekades.forEach(dek => {
                    const ds = new Date(y, m, dek.start);
                    const de = new Date(y, m, dek.end);
                    if (de < minDate || ds > maxDate) return; // skip kalau di luar range
                    const effDS = ds < minDate ? minDate : ds;
                    const effDE = de > maxDate ? maxDate : de;
                    const dDays = Math.round((effDE - effDS) / 86400000) + 1;
                    const dPct  = (dDays / totalDays) * 100;
                    dekadeHTML += `<div style="flex:0 0 ${dPct}%;min-width:0;font-size:.58rem;font-weight:600;color:var(--textlt);text-align:center;border-left:1px solid rgba(139,94,60,.08);padding:.15rem 0;overflow:hidden;">
                        ${dek.label}<br><span style="font-size:.52rem;opacity:.7;">${dek.start}–${dek.end}</span>
                    </div>`;
                });

                cur = new Date(y, m+1, 1);
            }

            header.style.flexDirection = 'column';
            header.innerHTML = `
                <div style="display:flex;width:100%;border-bottom:1px solid rgba(139,94,60,.1);">${bulanHTML}</div>
                <div style="display:flex;width:100%;border-bottom:2px solid var(--border);">${dekadeHTML}</div>
            `;
        }

        // Render tiap row
        ganttData.forEach((rtt, i) => {
            const barEl  = document.getElementById(`gantt-bar-${rtt.id}`);
            const faseEl = document.getElementById(`gantt-fase-${rtt.id}`);
            if (!barEl) return;

            const mulai  = new Date(rtt.mulai);
            const selesai = new Date(rtt.selesai);
            const leftPct  = ((mulai - minDate) / 86400000 / totalDays) * 100;
            const widthPct = ((selesai - mulai) / 86400000 / totalDays) * 100;

            // Bar rencana
            let html = `<div class="gantt-bar gantt-bar-plan" style="left:${leftPct}%;width:${widthPct}%;">
                ${widthPct > 8 ? rtt.petak : ''}
            </div>`;

            // Bar realisasi
            if (rtt.mulai_real) {
                const mulaiR  = new Date(rtt.mulai_real);
                const selesaiR = rtt.selesai_real ? new Date(rtt.selesai_real) : new Date();
                const lPct  = ((mulaiR - minDate) / 86400000 / totalDays) * 100;
                const wPct  = ((selesaiR - mulaiR) / 86400000 / totalDays) * 100;
                html += `<div class="gantt-bar gantt-bar-real" style="left:${lPct}%;width:${wPct}%;top:50%;transform:translateY(-50%)height:50%;"></div>`;
            }

            // Garis hari ini
            const today = new Date();
            if (today >= minDate && today <= maxDate) {
                const todayPct = ((today - minDate) / 86400000 / totalDays) * 100;
                html += `<div style="position:absolute;left:${todayPct}%;top:0;bottom:0;width:2px;background:#a03828;opacity:.5;z-index:5;"></div>`;
            }

            barEl.innerHTML = html;

            // Fase timeline
            if (faseEl && rtt.jadwal_fase.length) {
                let faseHtml = '';
                rtt.jadwal_fase.forEach(f => {
                    const fm = new Date(f.mulai);
                    const fs = new Date(f.selesai);
                    const fw = ((fs - fm) / 86400000 / totalDays) * 100;
                    const fl = ((fm - minDate) / 86400000 / totalDays) * 100;
                    faseHtml += `<div title="${f.fase}" style="position:absolute;left:${fl}%;width:${fw}%;height:100%;background:${faseColors[f.fase]||'#ccc'};border-radius:2px;"></div>`;
                });
                faseEl.style.position = 'relative';
                faseEl.innerHTML = faseHtml;
            }
        });
    }

    renderGantt();
</script>
@endpush
