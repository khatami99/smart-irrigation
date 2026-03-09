@extends('layouts.app')
@section('title', 'Daerah Irigasi — Smart Irrigation')
@section('page-title', 'Master Data Daerah Irigasi')

@section('content')
<style>
    :root { --soil:#3d2b1f;--soil2:#5c3d2e;--earth:#8b5e3c;--straw:#e8d5a3;--cream:#faf6ef;--cream2:#f5ede0;--water:#4a7c6f;--water2:#6aab9a;--leaf:#5a7a47;--text:#4a3728;--textlt:#7a6355;--border:rgba(139,94,60,.14); }
    .card { background:var(--cream);border:1px solid var(--border);border-radius:14px; }
    .badge { display:inline-block;padding:.2rem .65rem;border-radius:5px;font-size:.72rem;font-weight:700; }
    .badge-aktif    { background:rgba(90,122,71,.1);border:1px solid rgba(90,122,71,.2);color:var(--leaf); }
    .badge-nonaktif { background:rgba(139,94,60,.08);border:1px solid rgba(139,94,60,.15);color:var(--textlt); }
    .badge-dipetakan { background:rgba(74,124,111,.1);border:1px solid rgba(74,124,111,.2);color:var(--water); }
    .btn-sm { padding:.32rem .85rem;border-radius:6px;font-size:.75rem;font-weight:600;text-decoration:none;display:inline-block;transition:background .2s; }
    .btn-edit   { background:rgba(139,94,60,.08);border:1px solid rgba(139,94,60,.15);color:var(--earth); }
    .btn-edit:hover { background:rgba(139,94,60,.16); }
    .btn-delete { background:rgba(185,74,60,.07);border:1px solid rgba(185,74,60,.15);color:#a03828;cursor:pointer;font-family:'Karla',sans-serif; }
    .btn-delete:hover { background:rgba(185,74,60,.15); }
    .data-table { width:100%;border-collapse:collapse; }
    .data-table th { padding:.75rem 1.25rem;font-size:.65rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--textlt);border-bottom:2px solid var(--border); }
    .data-table td { padding:.85rem 1.25rem;font-size:.875rem;color:var(--textlt);border-bottom:1px solid rgba(139,94,60,.06); }
    .data-table tbody tr:hover { background:rgba(74,124,111,.04); }
</style>

<div style="display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:1.5rem;">
    <div>
        <p style="font-size:.68rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--textlt);margin-bottom:.3rem;">Master Data</p>
        <h2 style="font-family:'Fraunces',serif;font-size:1.6rem;font-weight:700;color:var(--soil);letter-spacing:-.02em;">Daerah Irigasi</h2>
        <p style="font-size:.82rem;color:var(--textlt);font-weight:300;margin-top:.2rem;">Total <strong style="color:var(--soil);">{{ $totalAktif }}</strong> daerah irigasi aktif · <strong style="color:var(--soil);">{{ number_format($totalLuas, 2) }} ha</strong> luas total</p>
    </div>
    @can('create daerah_irigasi')
    <a href="{{ route('daerah_irigasi.create') }}"
       style="background:var(--soil);color:var(--straw);padding:.65rem 1.4rem;border-radius:8px;font-size:.85rem;font-weight:600;text-decoration:none;transition:background .2s;"
       onmouseover="this.style.background='var(--soil2)'" onmouseout="this.style.background='var(--soil)'">
        + Tambah Daerah Irigasi
    </a>
    @endcan
</div>

{{-- Summary cards --}}
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;margin-bottom:1.5rem;">
    <div class="card" style="padding:1.25rem;">
        <p style="font-size:.65rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--textlt);margin-bottom:.4rem;">Total Aktif</p>
        <p style="font-family:'Fraunces',serif;font-size:2rem;font-weight:700;color:var(--soil);">{{ $totalAktif }}</p>
    </div>
    <div class="card" style="padding:1.25rem;">
        <p style="font-size:.65rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--textlt);margin-bottom:.4rem;">Total Luas</p>
        <p style="font-family:'Fraunces',serif;font-size:2rem;font-weight:700;color:var(--water);">{{ number_format($totalLuas, 2) }} <span style="font-size:1rem;font-weight:400;color:var(--textlt);">ha</span></p>
    </div>
    <div class="card" style="padding:1.25rem;">
        <p style="font-size:.65rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--textlt);margin-bottom:.4rem;">Total Semua</p>
        <p style="font-family:'Fraunces',serif;font-size:2rem;font-weight:700;color:var(--earth);">{{ $totalSemua }}</p>
    </div>
</div>

{{-- Table --}}
<div class="card" style="overflow:hidden;">
    <div style="overflow-x:auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="text-align:left;">Kode</th>
                    <th style="text-align:left;">Nama</th>
                    <th style="text-align:center;">Jenis</th>
                    <th style="text-align:center;">Luas (ha)</th>
                    <th style="text-align:left;">Sumber Air</th>
                    <th style="text-align:left;">Penanggung Jawab</th>
                    <th style="text-align:center;">Dipetakan</th>
                    <th style="text-align:center;">Status</th>
                    @canany(['edit daerah_irigasi','delete daerah_irigasi'])
                    <th style="text-align:center;">Aksi</th>
                    @endcanany
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                <tr>
                    <td><span style="font-family:'Courier New',monospace;font-size:.82rem;font-weight:700;color:var(--soil);background:var(--cream2);padding:.2rem .6rem;border-radius:5px;">{{ $item->kode }}</span></td>
                    <td style="font-weight:600;color:var(--soil);font-family:'Fraunces',serif;">{{ $item->nama }}</td>
                    <td style="text-align:center;">
                        @if($item->jenis === 'permukaan')
                            <span style="display:inline-block;font-size:.72rem;font-weight:700;padding:.2rem .65rem;border-radius:20px;background:rgba(74,124,111,.12);color:var(--water);border:1px solid rgba(74,124,111,.25);">
                                Permukaan
                            </span>
                        @else
                            <span style="display:inline-block;font-size:.72rem;font-weight:700;padding:.2rem .65rem;border-radius:20px;background:rgba(90,122,71,.12);color:var(--leaf);border:1px solid rgba(90,122,71,.25);">
                                Rawa
                            </span>
                        @endif
                    </td>
                    <td style="text-align:center;font-weight:600;color:var(--water);">{{ $item->luas_total ? number_format($item->luas_total, 2) : '—' }}</td>
                    <td>{{ $item->sumber_air ?? '—' }}</td>
                    <td>{{ $item->penanggung_jawab ?? '—' }}</td>
                    <td style="text-align:center;">
                        @if($item->map_feature_id)
                            <span class="badge badge-dipetakan">✓ Dipetakan</span>
                        @else
                            <span style="color:var(--textlt);font-size:.8rem;">—</span>
                        @endif
                    </td>
                    <td style="text-align:center;">
                        <span class="badge {{ $item->status === 'aktif' ? 'badge-aktif' : 'badge-nonaktif' }}">
                            {{ ucfirst($item->status) }}
                        </span>
                    </td>
                    @canany(['edit daerah_irigasi','delete daerah_irigasi'])
                    <td style="text-align:center;">
                        <div style="display:flex;gap:.4rem;justify-content:center;">
                            @can('edit daerah_irigasi')
                            <a href="{{ route('daerah_irigasi.edit', $item) }}" class="btn-sm btn-edit">Edit</a>
                            @endcan
                            @can('delete daerah_irigasi')
                            <form method="POST" action="{{ route('daerah_irigasi.destroy', $item) }}" style="margin:0;" onsubmit="return confirm('Hapus daerah irigasi {{ $item->kode }}?')">
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
                    <td colspan="8" style="text-align:center;padding:3rem;color:var(--textlt);">
                        <p style="font-size:2rem;margin-bottom:.75rem;">🏞️</p>
                        <p style="font-family:'Fraunces',serif;font-size:1.1rem;font-weight:600;color:var(--soil);margin-bottom:.4rem;">Belum ada data daerah irigasi</p>
                        <p style="font-size:.85rem;">Mulai tambahkan daerah irigasi pertama.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding:1rem 1.25rem;border-top:1px solid var(--border);background:rgba(245,237,224,.4);">
        {{ $items->links() }}
    </div>
</div>
@endsection
