@extends('layouts.app')
@section('title', 'Musim Tanam — Smart Irrigation')
@section('page-title', 'Master Musim Tanam')

@section('content')
<style>
    :root { --soil:#3d2b1f;--soil2:#5c3d2e;--earth:#8b5e3c;--clay:#c4895a;--straw:#e8d5a3;--cream:#faf6ef;--cream2:#f5ede0;--water:#4a7c6f;--water2:#6aab9a;--leaf:#5a7a47;--text:#4a3728;--textlt:#7a6355;--border:rgba(139,94,60,.14); }
    .card { background:var(--cream);border:1px solid var(--border);border-radius:14px; }
    .mt-card { background:var(--cream);border:1px solid var(--border);border-radius:12px;padding:1.5rem;transition:box-shadow .2s,transform .2s; }
    .mt-card:hover { box-shadow:0 8px 32px rgba(61,43,31,.1);transform:translateY(-2px); }
    .badge { display:inline-block;padding:.25rem .75rem;border-radius:20px;font-size:.72rem;font-weight:700; }
    .badge-rencana  { background:rgba(196,137,90,.1);border:1px solid rgba(196,137,90,.2);color:var(--clay); }
    .badge-berjalan { background:rgba(74,124,111,.1);border:1px solid rgba(74,124,111,.2);color:var(--water); }
    .badge-selesai  { background:rgba(90,122,71,.1);border:1px solid rgba(90,122,71,.2);color:var(--leaf); }
    .jenis-badge { display:inline-block;background:var(--cream2);border:1px solid var(--border);color:var(--textlt);border-radius:5px;padding:.15rem .55rem;font-size:.72rem;font-weight:700;font-family:'Courier New',monospace; }
    .progress-bar { height:6px;background:rgba(139,94,60,.1);border-radius:3px;margin-top:.5rem;overflow:hidden; }
    .progress-fill { height:100%;background:linear-gradient(90deg,var(--water),var(--water2));border-radius:3px;transition:width .5s ease; }
    .btn-sm { padding:.32rem .85rem;border-radius:6px;font-size:.75rem;font-weight:600;text-decoration:none;display:inline-block;transition:background .2s; }
    .btn-edit { background:rgba(139,94,60,.08);border:1px solid rgba(139,94,60,.15);color:var(--earth); }
    .btn-edit:hover { background:rgba(139,94,60,.16); }
    .btn-delete { background:rgba(185,74,60,.07);border:1px solid rgba(185,74,60,.15);color:#a03828;cursor:pointer;font-family:'Karla',sans-serif; }
    .btn-delete:hover { background:rgba(185,74,60,.15); }
</style>

{{-- Top --}}
<div style="display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:1.5rem;">
    <div>
        <p style="font-size:.68rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--textlt);margin-bottom:.3rem;">Master Data</p>
        <h2 style="font-family:'Fraunces',serif;font-size:1.6rem;font-weight:700;color:var(--soil);letter-spacing:-.02em;">Musim Tanam</h2>
        <p style="font-size:.82rem;color:var(--textlt);font-weight:300;margin-top:.2rem;">Kelola jadwal & rencana musim tanam</p>
    </div>
    @can('create musim-tanam')
    <a href="{{ route('musim-tanam.create') }}"
       style="background:var(--soil);color:var(--straw);padding:.65rem 1.4rem;border-radius:8px;font-size:.85rem;font-weight:600;text-decoration:none;"
       onmouseover="this.style.background='var(--soil2)'" onmouseout="this.style.background='var(--soil)'">
        + Tambah MT
    </a>
    @endcan
</div>

{{-- MT Berjalan banner --}}
@if($mtBerjalan)
<div style="background:var(--soil);border-radius:12px;padding:1.5rem;margin-bottom:1.5rem;position:relative;overflow:hidden;">
    <div style="position:absolute;inset:0;background:radial-gradient(ellipse 70% 80% at 80% 50%,rgba(106,171,154,.15),transparent);pointer-events:none;"></div>
    <div style="position:relative;display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:1rem;">
        <div>
            <p style="font-size:.65rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:rgba(232,213,163,.5);margin-bottom:.4rem;">🌱 Musim Tanam Aktif Sekarang</p>
            <h3 style="font-family:'Fraunces',serif;font-size:1.4rem;font-weight:700;color:var(--straw);letter-spacing:-.02em;">{{ $mtBerjalan->nama_mt }}</h3>
            <p style="font-size:.82rem;color:rgba(232,213,163,.6);margin-top:.3rem;">
                {{ $mtBerjalan->tanggal_mulai->format('d M Y') }} — {{ $mtBerjalan->tanggal_selesai->format('d M Y') }}
                &nbsp;·&nbsp; {{ $mtBerjalan->jenis_tanaman }}
                &nbsp;·&nbsp; Target: {{ number_format($mtBerjalan->target_luas_tanam, 1) }} ha
            </p>
            <div class="progress-bar" style="max-width:360px;margin-top:.85rem;">
                <div class="progress-fill" style="width:{{ $mtBerjalan->progress }}%;"></div>
            </div>
            <p style="font-size:.72rem;color:rgba(232,213,163,.45);margin-top:.3rem;">Progress: {{ $mtBerjalan->progress }}% · {{ $mtBerjalan->durasi_hari }} hari total</p>
        </div>
        <div style="text-align:right;">
            <p style="font-size:.65rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:rgba(232,213,163,.4);margin-bottom:.3rem;">Jenis</p>
            <span style="background:rgba(106,171,154,.2);border:1px solid rgba(106,171,154,.3);color:var(--water2);border-radius:6px;padding:.3rem .85rem;font-size:.85rem;font-weight:700;">
                {{ $mtBerjalan->jenis_mt }}
            </span>
        </div>
    </div>
</div>
@endif

{{-- Grid MT --}}
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:1.25rem;">
    @forelse($musimTanams as $mt)
    <div class="mt-card">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:1rem;">
            <div>
                <span class="jenis-badge">{{ $mt->jenis_mt }}</span>
                <h3 style="font-family:'Fraunces',serif;font-size:1.05rem;font-weight:700;color:var(--soil);margin-top:.4rem;">{{ $mt->nama_mt }}</h3>
            </div>
            <span class="badge badge-{{ $mt->status }}">{{ ucfirst($mt->status) }}</span>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem;margin-bottom:1rem;">
            <div>
                <p style="font-size:.65rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--textlt);margin-bottom:.2rem;">Mulai</p>
                <p style="font-size:.85rem;font-weight:600;color:var(--text);">{{ $mt->tanggal_mulai->format('d M Y') }}</p>
            </div>
            <div>
                <p style="font-size:.65rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--textlt);margin-bottom:.2rem;">Selesai</p>
                <p style="font-size:.85rem;font-weight:600;color:var(--text);">{{ $mt->tanggal_selesai->format('d M Y') }}</p>
            </div>
            <div>
                <p style="font-size:.65rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--textlt);margin-bottom:.2rem;">Tanaman</p>
                <p style="font-size:.85rem;color:var(--text);">{{ $mt->jenis_tanaman }}</p>
            </div>
            <div>
                <p style="font-size:.65rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--textlt);margin-bottom:.2rem;">Target Luas</p>
                <p style="font-size:.85rem;font-weight:700;color:var(--water);">{{ number_format($mt->target_luas_tanam, 1) }} ha</p>
            </div>
        </div>

        @if($mt->status === 'berjalan')
        <div class="progress-bar">
            <div class="progress-fill" style="width:{{ $mt->progress }}%;"></div>
        </div>
        <p style="font-size:.7rem;color:var(--textlt);margin-top:.3rem;margin-bottom:.75rem;">{{ $mt->progress }}% selesai · {{ $mt->durasi_hari }} hari</p>
        @else
        <p style="font-size:.75rem;color:var(--textlt);margin-bottom:.75rem;">Durasi: {{ $mt->durasi_hari }} hari</p>
        @endif

        @if($mt->keterangan)
        <p style="font-size:.78rem;color:var(--textlt);font-style:italic;margin-bottom:.75rem;padding:.5rem .75rem;background:var(--cream2);border-radius:6px;">{{ $mt->keterangan }}</p>
        @endif

        @canany(['edit musim-tanam','delete musim-tanam'])
        <div style="display:flex;gap:.4rem;padding-top:.75rem;border-top:1px solid var(--border);">
            @can('edit musim-tanam')
            <a href="{{ route('musim-tanam.edit', $mt) }}" class="btn-sm btn-edit">Edit</a>
            @endcan
            @can('delete musim-tanam')
            <form method="POST" action="{{ route('musim-tanam.destroy', $mt) }}" style="margin:0;" onsubmit="return confirm('Hapus musim tanam {{ $mt->nama_mt }}?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn-sm btn-delete">Hapus</button>
            </form>
            @endcan
        </div>
        @endcanany
    </div>
    @empty
    <div style="grid-column:1/-1;text-align:center;padding:3rem;color:var(--textlt);">
        <p style="font-size:2rem;margin-bottom:.75rem;">🌱</p>
        <p style="font-family:'Fraunces',serif;font-size:1.1rem;font-weight:600;color:var(--soil);margin-bottom:.4rem;">Belum ada musim tanam</p>
        <p style="font-size:.85rem;">Tambahkan musim tanam pertama untuk mulai mencatat.</p>
    </div>
    @endforelse
</div>

{{-- Pagination --}}
@if($musimTanams->hasPages())
<div style="margin-top:1.5rem;">{{ $musimTanams->links() }}</div>
@endif
@endsection
