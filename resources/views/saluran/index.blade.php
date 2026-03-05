@extends('layouts.app')
@section('title', 'Saluran — Smart Irrigation')
@section('page-title', 'Master Data Saluran')

@section('content')
<style>
    :root { --soil:#3d2b1f;--soil2:#5c3d2e;--earth:#8b5e3c;--straw:#e8d5a3;--cream:#faf6ef;--cream2:#f5ede0;--water:#4a7c6f;--water2:#6aab9a;--leaf:#5a7a47;--text:#4a3728;--textlt:#7a6355;--border:rgba(139,94,60,.14); }
    .card { background:var(--cream);border:1px solid var(--border);border-radius:14px; }
    .badge { display:inline-block;padding:.2rem .65rem;border-radius:5px;font-size:.72rem;font-weight:700; }
    .badge-baik    { background:rgba(90,122,71,.1);border:1px solid rgba(90,122,71,.2);color:var(--leaf); }
    .badge-sedang  { background:rgba(196,137,90,.1);border:1px solid rgba(196,137,90,.2);color:var(--earth); }
    .badge-rusak   { background:rgba(185,74,60,.08);border:1px solid rgba(185,74,60,.15);color:#a03828; }
    .badge-dipetakan { background:rgba(74,124,111,.1);border:1px solid rgba(74,124,111,.2);color:var(--water); }
    .badge-tipe { background:rgba(139,94,60,.08);border:1px solid rgba(139,94,60,.15);color:var(--earth);display:inline-block;padding:.2rem .65rem;border-radius:5px;font-size:.72rem;font-weight:700; }
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
        <h2 style="font-family:'Fraunces',serif;font-size:1.6rem;font-weight:700;color:var(--soil);letter-spacing:-.02em;">Saluran Irigasi</h2>
        <p style="font-size:.82rem;color:var(--textlt);font-weight:300;margin-top:.2rem;">Total <strong style="color:var(--soil);">{{ $totalSemua }}</strong> saluran · <strong style="color:var(--soil);">{{ number_format($totalKm, 2) }} km</strong> panjang total</p>
    </div>
    @can('create saluran')
    <a href="{{ route('saluran.create') }}"
       style="background:var(--soil);color:var(--straw);padding:.65rem 1.4rem;border-radius:8px;font-size:.85rem;font-weight:600;text-decoration:none;transition:background .2s;"
       onmouseover="this.style.background='var(--soil2)'" onmouseout="this.style.background='var(--soil)'">
        + Tambah Saluran
    </a>
    @endcan
</div>

{{-- Summary cards --}}
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;margin-bottom:1.5rem;">
    <div class="card" style="padding:1.25rem;">
        <p style="font-size:.65rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--textlt);margin-bottom:.4rem;">Kondisi Baik</p>
        <p style="font-family:'Fraunces',serif;font-size:2rem;font-weight:700;color:var(--leaf);">{{ $totalBaik }}</p>
    </div>
    <div class="card" style="padding:1.25rem;">
        <p style="font-size:.65rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--textlt);margin-bottom:.4rem;">Panjang Total</p>
        <p style="font-family:'Fraunces',serif;font-size:2rem;font-weight:700;color:var(--water);">{{ number_format($totalKm, 2) }} <span style="font-size:1rem;font-weight:400;color:var(--textlt);">km</span></p>
    </div>
    <div class="card" style="padding:1.25rem;">
        <p style="font-size:.65rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--textlt);margin-bottom:.4rem;">Total Saluran</p>
        <p style="font-family:'Fraunces',serif;font-size:2rem;font-weight:700;color:var(--earth);">{{ $totalSemua }}</p>
    </div>
</div>

{{-- Table --}}
<div class="card" style="overflow:hidden;">
    <div style="overflow-x:auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="text-align:left;">Nama</th>
                    <th style="text-align:center;">Tipe</th>
                    <th style="text-align:center;">Panjang (km)</th>
                    <th style="text-align:left;">Penanggung Jawab</th>
                    <th style="text-align:center;">Kondisi</th>
                    <th style="text-align:center;">Dipetakan</th>
                    @canany(['edit saluran','delete saluran'])
                    <th style="text-align:center;">Aksi</th>
                    @endcanany
                </tr>
            </thead>
            <tbody>
                @forelse($salurans as $saluran)
                <tr>
                    <td style="font-weight:600;color:var(--soil);font-family:'Fraunces',serif;">{{ $saluran->nama }}</td>
                    <td style="text-align:center;"><span class="badge-tipe">{{ ucfirst($saluran->tipe) }}</span></td>
                    <td style="text-align:center;font-weight:600;color:var(--water);">{{ $saluran->panjang_km ? number_format($saluran->panjang_km, 3) : '—' }}</td>
                    <td>{{ $saluran->penanggung_jawab ?? '—' }}</td>
                    <td style="text-align:center;">
                        <span class="badge badge-{{ $saluran->kondisi }}">{{ ucfirst($saluran->kondisi) }}</span>
                    </td>
                    <td style="text-align:center;">
                        @if($saluran->map_feature_id)
                            <span class="badge badge-dipetakan">✓ Dipetakan</span>
                        @else
                            <span style="color:var(--textlt);font-size:.8rem;">—</span>
                        @endif
                    </td>
                    @canany(['edit saluran','delete saluran'])
                    <td style="text-align:center;">
                        <div style="display:flex;gap:.4rem;justify-content:center;">
                            @can('edit saluran')
                            <a href="{{ route('saluran.edit', $saluran) }}" class="btn-sm btn-edit">Edit</a>
                            @endcan
                            @can('delete saluran')
                            <form method="POST" action="{{ route('saluran.destroy', $saluran) }}" style="margin:0;" onsubmit="return confirm('Hapus saluran {{ $saluran->nama }}?')">
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
                    <td colspan="7" style="text-align:center;padding:3rem;color:var(--textlt);">
                        <p style="font-size:2rem;margin-bottom:.75rem;">〰️</p>
                        <p style="font-family:'Fraunces',serif;font-size:1.1rem;font-weight:600;color:var(--soil);margin-bottom:.4rem;">Belum ada data saluran</p>
                        <p style="font-size:.85rem;">Mulai tambahkan saluran irigasi pertama.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding:1rem 1.25rem;border-top:1px solid var(--border);background:rgba(245,237,224,.4);">
        {{ $salurans->links() }}
    </div>
</div>
@endsection
