@extends('layouts.app')
@section('title', 'Blangko OP — Smart Irrigation')
@section('page-title', 'Blangko OP Per Dekade')

@section('content')
<style>
    :root { --soil:#3d2b1f;--soil2:#5c3d2e;--earth:#8b5e3c;--clay:#c4895a;--straw:#e8d5a3;--cream:#faf6ef;--cream2:#f5ede0;--water:#4a7c6f;--water2:#6aab9a;--leaf:#5a7a47;--text:#4a3728;--textlt:#7a6355;--border:rgba(139,94,60,.14); }
    .card { background:var(--cream);border:1px solid var(--border);border-radius:14px; }
    .filter-input { background:var(--cream);border:1.5px solid rgba(139,94,60,.18);color:var(--text);border-radius:8px;padding:.55rem .9rem;font-size:.85rem;font-family:'Karla',sans-serif;outline:none;transition:border-color .2s; }
    .filter-input:focus { border-color:var(--water); }
    .data-table { width:100%;border-collapse:collapse; }
    .data-table th { padding:.75rem 1.25rem;font-size:.65rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--textlt);border-bottom:2px solid var(--border);white-space:nowrap; }
    .data-table td { padding:.85rem 1.25rem;font-size:.875rem;color:var(--textlt);border-bottom:1px solid rgba(139,94,60,.06);vertical-align:middle; }
    .data-table tbody tr:hover { background:rgba(74,124,111,.04); }
    .badge { display:inline-block;padding:.22rem .65rem;border-radius:5px;font-size:.72rem;font-weight:700; }
    .badge-baik         { background:rgba(90,122,71,.1);border:1px solid rgba(90,122,71,.2);color:var(--leaf); }
    .badge-rusak_ringan { background:rgba(196,137,90,.1);border:1px solid rgba(196,137,90,.2);color:var(--clay); }
    .badge-rusak_berat  { background:rgba(185,74,60,.08);border:1px solid rgba(185,74,60,.2);color:#a03828; }
    .btn-sm { padding:.32rem .85rem;border-radius:6px;font-size:.75rem;font-weight:600;text-decoration:none;display:inline-block;transition:background .2s; }
    .btn-edit   { background:rgba(139,94,60,.08);border:1px solid rgba(139,94,60,.15);color:var(--earth); }
    .btn-edit:hover { background:rgba(139,94,60,.16); }
    .btn-delete { background:rgba(185,74,60,.07);border:1px solid rgba(185,74,60,.15);color:#a03828;cursor:pointer;font-family:'Karla',sans-serif; }
    .btn-delete:hover { background:rgba(185,74,60,.15); }
    .efisiensi-ok   { color:var(--leaf);font-weight:700; }
    .efisiensi-warn { color:var(--clay);font-weight:700; }
    .efisiensi-bad  { color:#a03828;font-weight:700; }
</style>

{{-- Top --}}
<div style="display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:1.5rem;">
    <div>
        <p style="font-size:.68rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--textlt);margin-bottom:.3rem;">Blangko OP</p>
        <h2 style="font-family:'Fraunces',serif;font-size:1.6rem;font-weight:700;color:var(--soil);letter-spacing:-.02em;">Catatan Per Dekade</h2>
        <p style="font-size:.82rem;color:var(--textlt);font-weight:300;margin-top:.2rem;">Total <strong style="color:var(--soil);">{{ $blangkos->total() }}</strong> catatan tersimpan</p>
    </div>
    @can('create blangko-op')
    <a href="{{ route('blangko-op.create') }}"
       style="background:var(--soil);color:var(--straw);padding:.65rem 1.4rem;border-radius:8px;font-size:.85rem;font-weight:600;text-decoration:none;"
       onmouseover="this.style.background='var(--soil2)'" onmouseout="this.style.background='var(--soil)'">
        + Input Blangko OP
    </a>
    @endcan
</div>

{{-- Filter --}}
<div class="card" style="padding:1rem 1.25rem;margin-bottom:1.25rem;">
    <form method="GET" action="{{ route('blangko-op.index') }}" style="display:flex;gap:.75rem;flex-wrap:wrap;align-items:center;">

        <select name="petak_id" class="filter-input">
            <option value="">Semua Petak</option>
            @foreach($petaks as $petak)
                <option value="{{ $petak->id }}" {{ request('petak_id') == $petak->id ? 'selected' : '' }}>
                    {{ $petak->kode_petak }} — {{ $petak->nama_petak }}
                </option>
            @endforeach
        </select>

        <select name="musim_tanam_id" class="filter-input">
            <option value="">Semua MT</option>
            @foreach($musimTanams as $mt)
                <option value="{{ $mt->id }}" {{ request('musim_tanam_id') == $mt->id ? 'selected' : '' }}>
                    {{ $mt->nama_mt }}
                </option>
            @endforeach
        </select>

        <select name="bulan" class="filter-input">
            <option value="">Semua Bulan</option>
            @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $i => $bln)
                <option value="{{ $i+1 }}" {{ request('bulan') == $i+1 ? 'selected' : '' }}>{{ $bln }}</option>
            @endforeach
        </select>

        <select name="tahun" class="filter-input">
            <option value="">Semua Tahun</option>
            @foreach($years as $year)
                <option value="{{ $year }}" {{ request('tahun') == $year ? 'selected' : '' }}>{{ $year }}</option>
            @endforeach
        </select>

        <button type="submit" style="background:var(--water);color:#fff;padding:.55rem 1.1rem;border-radius:8px;border:none;font-size:.85rem;font-weight:600;font-family:'Karla',sans-serif;cursor:pointer;">
            Filter
        </button>
        <a href="{{ route('blangko-op.index') }}"
           style="padding:.55rem 1rem;background:rgba(139,94,60,.08);border:1px solid var(--border);border-radius:8px;font-size:.82rem;font-weight:600;color:var(--textlt);text-decoration:none;">
            Reset
        </a>
    </form>
</div>

{{-- Tabel --}}
<div class="card" style="overflow:hidden;">
    <div style="overflow-x:auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Periode</th>
                    <th>Petak</th>
                    <th>Musim Tanam</th>
                    <th>Fase Tanam</th>
                    <th style="text-align:center;">Debit R/A<br><span style="font-weight:400;text-transform:none;">(l/det)</span></th>
                    <th style="text-align:center;">Luas R/A<br><span style="font-weight:400;text-transform:none;">(ha)</span></th>
                    <th style="text-align:center;">TMA<br><span style="font-weight:400;text-transform:none;">(cm)</span></th>
                    <th style="text-align:center;">Kondisi</th>
                    @canany(['edit blangko-op','delete blangko-op'])
                    <th style="text-align:center;">Aksi</th>
                    @endcanany
                </tr>
            </thead>
            <tbody>
                @forelse($blangkos as $b)
                <tr>
                    {{-- Periode --}}
                    <td>
                        <span style="font-family:'Courier New',monospace;font-size:.78rem;font-weight:700;background:var(--cream2);border:1px solid var(--border);padding:.2rem .55rem;border-radius:5px;color:var(--soil);">
                            Dek.{{ $b->dekade }}
                        </span>
                        <p style="font-size:.75rem;color:var(--textlt);margin-top:.25rem;">
                            {{ ['','Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'][$b->bulan] }} {{ $b->tahun }}
                        </p>
                    </td>

                    {{-- Petak --}}
                    <td>
                        <p style="font-weight:600;color:var(--soil);font-size:.875rem;">{{ $b->petak->kode_petak }}</p>
                        <p style="font-size:.75rem;color:var(--textlt);">{{ $b->petak->nama_petak }}</p>
                    </td>

                    {{-- MT --}}
                    <td style="font-size:.82rem;">{{ $b->musimTanam->nama_mt }}</td>

                    {{-- Fase --}}
                    <td>
                        @if($b->fase_pertumbuhan)
                        <span style="background:rgba(74,124,111,.08);border:1px solid rgba(74,124,111,.15);color:var(--water);border-radius:5px;padding:.2rem .6rem;font-size:.75rem;font-weight:600;">
                            {{ $b->fase_label }}
                        </span>
                        @else
                        <span style="color:var(--textlt);">—</span>
                        @endif
                    </td>

                    {{-- Debit --}}
                    <td style="text-align:center;">
                        <p style="font-size:.82rem;">
                            {{ $b->debit_rencana ?? '—' }} /
                            <strong style="{{ $b->efisiensi_debit !== null ? ($b->efisiensi_debit >= 80 ? 'color:var(--leaf)' : ($b->efisiensi_debit >= 60 ? 'color:var(--clay)' : 'color:#a03828')) : '' }}">
                                {{ $b->debit_realisasi ?? '—' }}
                            </strong>
                        </p>
                        @if($b->efisiensi_debit !== null)
                        <p style="font-size:.7rem;color:var(--textlt);">{{ $b->efisiensi_debit }}%</p>
                        @endif
                    </td>

                    {{-- Luas --}}
                    <td style="text-align:center;">
                        <p style="font-size:.82rem;">
                            {{ $b->luas_rencana ?? '—' }} /
                            <strong style="{{ $b->efisiensi_luas !== null ? ($b->efisiensi_luas >= 80 ? 'color:var(--leaf)' : ($b->efisiensi_luas >= 60 ? 'color:var(--clay)' : 'color:#a03828')) : '' }}">
                                {{ $b->luas_realisasi ?? '—' }}
                            </strong>
                        </p>
                        @if($b->efisiensi_luas !== null)
                        <p style="font-size:.7rem;color:var(--textlt);">{{ $b->efisiensi_luas }}%</p>
                        @endif
                    </td>

                    {{-- TMA --}}
                    <td style="text-align:center;font-size:.875rem;">{{ $b->tinggi_muka_air ?? '—' }}</td>

                    {{-- Kondisi --}}
                    <td style="text-align:center;">
                        @if($b->kondisi_saluran)
                        <span class="badge badge-{{ $b->kondisi_saluran }}" style="display:block;margin-bottom:.25rem;">
                            Sal: {{ str_replace('_',' ', ucfirst($b->kondisi_saluran)) }}
                        </span>
                        @endif
                        @if($b->kondisi_bangunan)
                        <span class="badge badge-{{ $b->kondisi_bangunan }}">
                            Bang: {{ str_replace('_',' ', ucfirst($b->kondisi_bangunan)) }}
                        </span>
                        @endif
                    </td>

                    {{-- Aksi --}}
                    @canany(['edit blangko-op','delete blangko-op'])
                    <td style="text-align:center;">
                        <div style="display:flex;gap:.4rem;justify-content:center;">
                            @can('edit blangko-op')
                            <a href="{{ route('blangko-op.edit', $b) }}" class="btn-sm btn-edit">Edit</a>
                            @endcan
                            @can('delete blangko-op')
                            <form method="POST" action="{{ route('blangko-op.destroy', $b) }}" style="margin:0;"
                                  onsubmit="return confirm('Hapus data {{ $b->periode }}?')">
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
                        <p style="font-family:'Fraunces',serif;font-size:1.1rem;font-weight:600;color:var(--soil);margin-bottom:.4rem;">Belum ada data blangko OP</p>
                        <p style="font-size:.85rem;color:var(--textlt);">Mulai input data pengamatan per dekade.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding:1rem 1.25rem;border-top:1px solid var(--border);background:rgba(245,237,224,.4);display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:.5rem;">
        <p style="font-size:.78rem;color:var(--textlt);">
            Menampilkan {{ $blangkos->firstItem() }}–{{ $blangkos->lastItem() }} dari {{ $blangkos->total() }} catatan
        </p>
        {{ $blangkos->appends(request()->query())->links() }}
    </div>
</div>
@endsection
