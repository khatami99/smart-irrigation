@extends('layouts.app')
@section('title', 'RTT — Smart Irrigation')
@section('page-title', 'Rencana Tata Tanam')

@section('content')
<style>
    :root { --soil:#3d2b1f;--soil2:#5c3d2e;--earth:#8b5e3c;--clay:#c4895a;--straw:#e8d5a3;--cream:#faf6ef;--cream2:#f5ede0;--water:#4a7c6f;--water2:#6aab9a;--leaf:#5a7a47;--text:#4a3728;--textlt:#7a6355;--border:rgba(139,94,60,.14); }
    .card { background:var(--cream);border:1px solid var(--border);border-radius:14px; }
    .di-card { background:var(--cream);border:1px solid var(--border);border-radius:14px;padding:1.5rem;transition:all .2s;cursor:pointer;text-decoration:none;display:block;color:inherit; }
    .di-card:hover { border-color:var(--water);box-shadow:0 4px 20px rgba(74,124,111,.12);transform:translateY(-2px); }
    .stat-mini { background:var(--cream2);border-radius:8px;padding:.6rem .9rem;text-align:center; }
</style>

{{-- Top --}}
<div style="display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:1.5rem;flex-wrap:wrap;gap:1rem;">
    <div>
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
        <form method="GET" action="{{ route('rtt.index') }}">
            <select name="musim_tanam_id"
                style="background:var(--cream);border:1.5px solid rgba(139,94,60,.18);color:var(--text);border-radius:8px;padding:.55rem .9rem;font-size:.85rem;font-family:'Karla',sans-serif;outline:none;"
                onchange="this.form.submit()">
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

{{-- Card per DI --}}
@if($daerahIrigasis->isEmpty())
<div class="card" style="padding:3rem;text-align:center;">
    <p style="font-size:2rem;margin-bottom:.75rem;">🌾</p>
    <p style="font-family:'Fraunces',serif;font-size:1.1rem;font-weight:600;color:var(--soil);">Belum ada Daerah Irigasi</p>
    <p style="font-size:.85rem;color:var(--textlt);margin-top:.4rem;">Tambah daerah irigasi di menu <a href="{{ route('daerah_irigasi.index') }}" style="color:var(--water);font-weight:600;">Master Data</a>.</p>
</div>
@else
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(320px,1fr));gap:1.25rem;">
    @foreach($daerahIrigasis as $di)
    <a href="{{ route('rtt.by-di', ['daerahIrigasi' => $di['id'], 'musim_tanam_id' => $mtId]) }}" class="di-card">

        {{-- Header DI --}}
        <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:1rem;">
            <div>
                <p style="font-size:.65rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--textlt);margin-bottom:.2rem;">{{ $di['kode'] }}</p>
                <h3 style="font-family:'Fraunces',serif;font-size:1.1rem;font-weight:700;color:var(--soil);">{{ $di['nama'] }}</h3>
            </div>
            <span style="background:{{ $di['warna'] }}20;border:1px solid {{ $di['warna'] }}40;color:{{ $di['warna'] }};border-radius:6px;font-size:.68rem;font-weight:700;padding:.25rem .7rem;text-transform:uppercase;white-space:nowrap;">
                {{ $di['status'] }}
            </span>
        </div>

        {{-- Progress bar --}}
        <div style="margin-bottom:1rem;">
            <div style="display:flex;justify-content:space-between;margin-bottom:.35rem;">
                <span style="font-size:.72rem;color:var(--textlt);">Progress RTT</span>
                <span style="font-size:.72rem;font-weight:700;color:{{ $di['warna'] }};">{{ $di['progress'] }}%</span>
            </div>
            <div style="background:rgba(139,94,60,.1);border-radius:99px;height:7px;overflow:hidden;">
                <div style="height:100%;border-radius:99px;background:{{ $di['warna'] }};width:{{ $di['progress'] }}%;transition:width .5s ease;"></div>
            </div>
        </div>

        {{-- Stats mini --}}
        <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:.5rem;margin-bottom:1rem;">
            <div class="stat-mini">
                <p style="font-size:.6rem;color:var(--textlt);margin-bottom:.2rem;">Rencana</p>
                <p style="font-size:.95rem;font-weight:700;color:var(--clay);">{{ $di['rencana'] }}</p>
            </div>
            <div class="stat-mini">
                <p style="font-size:.6rem;color:var(--textlt);margin-bottom:.2rem;">Berjalan</p>
                <p style="font-size:.95rem;font-weight:700;color:var(--water);">{{ $di['berjalan'] }}</p>
            </div>
            <div class="stat-mini">
                <p style="font-size:.6rem;color:var(--textlt);margin-bottom:.2rem;">Selesai</p>
                <p style="font-size:.95rem;font-weight:700;color:var(--leaf);">{{ $di['selesai'] }}</p>
            </div>
            <div class="stat-mini">
                <p style="font-size:.6rem;color:var(--textlt);margin-bottom:.2rem;">Terlambat</p>
                <p style="font-size:.95rem;font-weight:700;color:#b94a3c;">{{ $di['terlambat'] }}</p>
            </div>
        </div>

        {{-- Footer --}}
        <div style="display:flex;justify-content:space-between;align-items:center;padding-top:.85rem;border-top:1px solid var(--border);">
            <div style="display:flex;gap:1rem;">
                <span style="font-size:.75rem;color:var(--textlt);">
                    <strong style="color:var(--soil);">{{ $di['total_petak'] }}</strong> petak
                </span>
                <span style="font-size:.75rem;color:var(--textlt);">
                    <strong style="color:var(--water);">{{ number_format($di['target_luas'], 1) }}</strong> ha target
                </span>
            </div>
            <span style="font-size:.75rem;color:var(--water);font-weight:600;">Lihat detail →</span>
        </div>
    </a>
    @endforeach
</div>

{{-- Warning petak tanpa DI --}}
@if($petakTanpaDI > 0)
<div style="margin-top:1.25rem;background:rgba(196,137,90,.08);border:1px solid rgba(196,137,90,.2);border-radius:10px;padding:1rem 1.25rem;display:flex;align-items:center;gap:.75rem;">
    <span style="font-size:1.2rem;">⚠️</span>
    <p style="font-size:.82rem;color:#8b5e3c;">
        Ada <strong>{{ $petakTanpaDI }} petak</strong> yang punya RTT tapi belum di-assign ke Daerah Irigasi.
        <a href="{{ route('petak.index') }}" style="color:var(--water);font-weight:600;">Assign sekarang →</a>
    </p>
</div>
@endif
@endif

@endsection
