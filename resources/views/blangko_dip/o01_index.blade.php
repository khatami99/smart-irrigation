{{-- resources/views/blangko_o01/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Blangko O-01 — Smart Irrigation')
@section('page-title', 'Blangko O-01 Usulan Luas Tanam')

@section('content')
<style>
    :root { --soil:#3d2b1f;--soil2:#5c3d2e;--earth:#8b5e3c;--straw:#e8d5a3;--cream:#faf6ef;--cream2:#f5ede0;--water:#4a7c6f;--water2:#6aab9a;--leaf:#5a7a47;--text:#4a3728;--textlt:#7a6355;--border:rgba(139,94,60,.14); }
    .card { background:var(--cream);border:1px solid var(--border);border-radius:14px; }
    .filter-input { background:var(--cream);border:1.5px solid rgba(139,94,60,.18);color:var(--text);border-radius:8px;padding:.55rem .9rem;font-size:.85rem;font-family:'Karla',sans-serif;outline:none; }
    .data-table { width:100%;border-collapse:collapse; }
    .data-table th { padding:.75rem 1.25rem;font-size:.65rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--textlt);border-bottom:2px solid var(--border);white-space:nowrap; }
    .data-table td { padding:.85rem 1.25rem;font-size:.875rem;color:var(--textlt);border-bottom:1px solid rgba(139,94,60,.06);vertical-align:middle; }
    .data-table tbody tr:hover { background:rgba(74,124,111,.04); }
    .badge { display:inline-block;padding:.22rem .65rem;border-radius:20px;font-size:.72rem;font-weight:700;letter-spacing:.03em; }
    .badge-usulan   { background:rgba(196,137,90,.12);border:1px solid rgba(196,137,90,.25);color:var(--earth); }
    .badge-disetujui{ background:rgba(90,122,71,.12);border:1px solid rgba(90,122,71,.25);color:var(--leaf); }
    .badge-revisi   { background:rgba(185,74,60,.08);border:1px solid rgba(185,74,60,.2);color:#a03828; }
    .badge-dip { background:rgba(74,124,111,.12);border:1px solid rgba(74,124,111,.25);color:var(--water); }
    .badge-dir { background:rgba(90,122,71,.12);border:1px solid rgba(90,122,71,.25);color:var(--leaf); }
    .btn-sm { padding:.32rem .85rem;border-radius:6px;font-size:.75rem;font-weight:600;text-decoration:none;display:inline-block; }
    .btn-edit   { background:rgba(139,94,60,.08);border:1px solid rgba(139,94,60,.15);color:var(--earth); }
    .btn-delete { background:rgba(185,74,60,.07);border:1px solid rgba(185,74,60,.15);color:#a03828;cursor:pointer;font-family:'Karla',sans-serif; }
    .btn-show   { background:rgba(74,124,111,.08);border:1px solid rgba(74,124,111,.15);color:var(--water); }
</style>

<div style="display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:1.5rem;">
    <div>
        <p style="font-size:.68rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--textlt);margin-bottom:.3rem;">Blangko OP</p>
        <h2 style="font-family:'Fraunces',serif;font-size:1.6rem;font-weight:700;color:var(--soil);">O-01 Usulan Luas Tanam</h2>
        <p style="font-size:.82rem;color:var(--textlt);margin-top:.2rem;">Usulan & keputusan luas tanam per Daerah Irigasi per Musim Tanam</p>
    </div>
    @can('create blangko-op')
    <a href="{{ route('blangko-dip.o01.create') }}"
       style="background:var(--soil);color:var(--straw);padding:.65rem 1.4rem;border-radius:8px;font-size:.85rem;font-weight:600;text-decoration:none;"
       onmouseover="this.style.background='var(--soil2)'" onmouseout="this.style.background='var(--soil)'">
        + Input O-01
    </a>
    @endcan
</div>

@if(session('success'))
<div style="background:rgba(90,122,71,.1);border:1px solid rgba(90,122,71,.25);color:var(--leaf);border-radius:8px;padding:.8rem 1rem;font-size:.875rem;margin-bottom:1.25rem;">
    {{ session('success') }}
</div>
@endif

{{-- Filter --}}
<div class="card" style="padding:1rem 1.25rem;margin-bottom:1.25rem;">
    <form method="GET" style="display:flex;gap:.75rem;flex-wrap:wrap;align-items:center;">
        <select name="musim_tanam_id" class="filter-input" onchange="this.form.submit()">
            <option value="">Semua MT</option>
            @foreach($musimTanams as $mt)
                <option value="{{ $mt->id }}" {{ request('musim_tanam_id', $mtId) == $mt->id ? 'selected' : '' }}>
                    {{ $mt->nama_mt }}
                </option>
            @endforeach
        </select>
        <select name="daerah_irigasi_id" class="filter-input">
            <option value="">Semua DI</option>
            @foreach($daerahIrigasis as $di)
                <option value="{{ $di->id }}" {{ request('daerah_irigasi_id') == $di->id ? 'selected' : '' }}>
                    {{ $di->kode }} — {{ $di->nama }}
                </option>
            @endforeach
        </select>
        <select name="status" class="filter-input">
            <option value="">Semua Status</option>
            <option value="usulan"    {{ request('status') === 'usulan'    ? 'selected' : '' }}>Usulan</option>
            <option value="disetujui" {{ request('status') === 'disetujui' ? 'selected' : '' }}>Disetujui</option>
            <option value="revisi"    {{ request('status') === 'revisi'    ? 'selected' : '' }}>Perlu Revisi</option>
        </select>
        <button type="submit" style="background:var(--water);color:#fff;padding:.55rem 1.1rem;border-radius:8px;border:none;font-size:.85rem;font-weight:600;font-family:'Karla',sans-serif;cursor:pointer;">Filter</button>
        <a href="{{ route('blangko-dip.o01.index') }}" style="padding:.55rem 1rem;background:rgba(139,94,60,.08);border:1px solid var(--border);border-radius:8px;font-size:.82rem;font-weight:600;color:var(--textlt);text-decoration:none;">Reset</a>
    </form>
</div>

{{-- Tabel --}}
<div class="card" style="overflow:hidden;">
    <div style="overflow-x:auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>DI</th>
                    <th>Jenis</th>
                    <th>Musim Tanam</th>
                    <th style="text-align:right;">Luas Padi<br><span style="font-weight:400;text-transform:none;">(ha)</span></th>
                    <th style="text-align:right;">Luas Palawija<br><span style="font-weight:400;text-transform:none;">(ha)</span></th>
                    <th style="text-align:right;">Luas Tebu<br><span style="font-weight:400;text-transform:none;">(ha)</span></th>
                    <th style="text-align:right;">Kebutuhan Air<br><span style="font-weight:400;text-transform:none;">(l/det)</span></th>
                    <th style="text-align:center;">Status</th>
                    @canany(['edit blangko-op','delete blangko-op'])
                    <th style="text-align:center;">Aksi</th>
                    @endcanany
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                <tr>
                    <td>
                        <span style="font-family:'Courier New',monospace;font-size:.78rem;font-weight:700;background:var(--cream2);border:1px solid var(--border);padding:.2rem .55rem;border-radius:5px;color:var(--soil);">{{ $item->daerahIrigasi->kode }}</span>
                        <div style="font-weight:600;color:var(--soil);font-family:'Fraunces',serif;margin-top:.2rem;">{{ $item->daerahIrigasi->nama }}</div>
                    </td>
                    <td>
                        @if($item->daerahIrigasi->jenis === 'permukaan')
                            <span class="badge badge-dip">DIP</span>
                        @else
                            <span class="badge badge-dir">DIR</span>
                        @endif
                    </td>
                    <td>{{ $item->musimTanam->nama_mt }}</td>
                    <td style="text-align:right;font-weight:600;color:var(--water);">
                        {{ number_format($item->luas_padi_disetujui ?? $item->luas_padi_usulan, 2) }}
                        @if($item->luas_padi_disetujui && $item->luas_padi_disetujui != $item->luas_padi_usulan)
                            <div style="font-size:.7rem;color:var(--textlt);font-weight:400;">usulan: {{ number_format($item->luas_padi_usulan, 2) }}</div>
                        @endif
                    </td>
                    <td style="text-align:right;font-weight:600;color:var(--leaf);">
                        {{ number_format($item->luas_palawija_disetujui ?? $item->luas_palawija_usulan, 2) }}
                    </td>
                    <td style="text-align:right;font-weight:600;color:var(--earth);">
                        {{ number_format($item->luas_tebu_disetujui ?? $item->luas_tebu_usulan, 2) }}
                    </td>
                    <td style="text-align:right;font-weight:700;color:var(--soil);">
                        {{ number_format($item->hitungKebutuhanAir(), 2) }}
                        <div style="font-size:.7rem;color:var(--textlt);font-weight:400;">fase pertumbuhan</div>
                    </td>
                    <td style="text-align:center;">
                        <span class="badge badge-{{ $item->status }}">{{ $item->label_status }}</span>
                    </td>
                    @canany(['edit blangko-op','delete blangko-op'])
                    <td style="text-align:center;">
                        <div style="display:flex;gap:.4rem;justify-content:center;">
                            <a href="{{ route('blangko-dip.o01.show', $item) }}" class="btn-sm btn-show">Detail</a>
                            @can('edit blangko-op')
                            <a href="{{ route('blangko-dip.o01.edit', $item) }}" class="btn-sm btn-edit">Edit</a>
                            @endcan
                            @can('delete blangko-op')
                            <form method="POST" action="{{ route('blangko-dip.o01.destroy', $item) }}" onsubmit="return confirm('Hapus O-01 ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-sm btn-delete">Hapus</button>
                            </form>
                            @endcan
                        </div>
                    </td>
                    @endcanany
                </tr>
                @empty
                <tr>
                    <td colspan="9" style="text-align:center;padding:3rem;">
                        <p style="font-size:2rem;margin-bottom:.75rem;">📋</p>
                        <p style="font-family:'Fraunces',serif;font-size:1.1rem;font-weight:600;color:var(--soil);margin-bottom:.4rem;">Belum ada data O-01</p>
                        <p style="font-size:.85rem;color:var(--textlt);">Mulai input usulan luas tanam per Daerah Irigasi.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($items->hasPages())
    <div style="padding:1rem 1.25rem;border-top:1px solid var(--border);">
        {{ $items->appends(request()->query())->links() }}
    </div>
    @endif
</div>
@endsection
