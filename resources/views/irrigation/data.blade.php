@extends('layouts.app')
@section('title', 'Data Iklim — Smart Irrigation')

@section('content')
<style>
    :root { --soil:#3d2b1f;--soil2:#5c3d2e;--earth:#8b5e3c;--clay:#c4895a;--straw:#e8d5a3;--cream:#faf6ef;--cream2:#f5ede0;--water:#4a7c6f;--water2:#6aab9a;--leaf:#5a7a47;--textlt:#7a6355;--border:rgba(139,94,60,.14); }
    .card { background:var(--cream);border:1px solid var(--border);border-radius:14px; }
    .data-table { width:100%;border-collapse:collapse; }
    .data-table th { padding:.75rem 1rem;font-size:.65rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--textlt);border-bottom:2px solid var(--border);white-space:nowrap;text-align:center; }
    .data-table th:first-child { text-align:left; }
    .data-table td { padding:.85rem 1rem;font-size:.875rem;color:var(--textlt);border-bottom:1px solid rgba(139,94,60,.06);text-align:center;vertical-align:middle; }
    .data-table td:first-child { text-align:left; }
    .data-table tbody tr:hover { background:rgba(74,124,111,.04); }
    .btn-sm { padding:.32rem .85rem;border-radius:6px;font-size:.75rem;font-weight:600;text-decoration:none;display:inline-block; }
    .btn-edit { background:rgba(139,94,60,.08);border:1px solid rgba(139,94,60,.15);color:#8b5e3c; }
    .btn-delete { background:rgba(185,74,60,.07);border:1px solid rgba(185,74,60,.15);color:#a03828;cursor:pointer;font-family:'Karla',sans-serif; }
    .bv { display:inline-block;padding:.2rem .6rem;border-radius:5px;font-size:.78rem;font-weight:700; }
</style>

<div style="display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:1.75rem;">
    <div>
        <p style="font-size:.68rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--textlt);margin-bottom:.3rem;">Manajemen Data</p>
        <h2 style="font-family:'Fraunces',serif;font-size:1.6rem;font-weight:700;color:var(--soil);">Data Iklim Harian</h2>
        <p style="font-size:.82rem;color:var(--textlt);margin-top:.2rem;">Suhu, kelembaban, radiasi, dan kebutuhan air per hari</p>
    </div>
    <a href="{{ route('irrigation.create') }}"
       style="background:var(--soil);color:var(--straw);padding:.65rem 1.4rem;border-radius:8px;font-size:.85rem;font-weight:600;text-decoration:none;"
       onmouseover="this.style.background='var(--soil2)'" onmouseout="this.style.background='var(--soil)'">
        + Tambah Data
    </a>
</div>

@if(session('success'))
<div style="background:rgba(90,122,71,.1);border:1px solid rgba(90,122,71,.2);color:var(--leaf);border-radius:8px;padding:.8rem 1rem;font-size:.85rem;margin-bottom:1.25rem;">
    {{ session('success') }}
</div>
@endif

<div class="card" style="overflow:hidden;">
    <div style="padding:1.25rem 1.5rem;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;">
        <h3 style="font-family:'Fraunces',serif;font-size:1.05rem;font-weight:700;color:var(--soil);">Daftar Data Iklim</h3>
        <span style="font-size:.78rem;color:var(--textlt);">Total: {{ $tableData->total() }} data</span>
    </div>
    <div style="overflow-x:auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Tanggal</th><th>Suhu Max</th><th>Suhu Min</th><th>Kelembaban</th>
                    <th>Angin</th><th>Radiasi</th><th>Curah Hujan</th><th>Kc</th>
                    <th>ETo</th><th>ETc</th><th>Kebutuhan Air</th><th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tableData as $row)
                <tr>
                    <td><strong style="color:var(--soil);">{{ \Carbon\Carbon::parse($row->tanggal)->format('d M Y') }}</strong></td>
                    <td>{{ $row->suhu_max }}°C</td>
                    <td>{{ $row->suhu_min }}°C</td>
                    <td>{{ $row->kelembaban }}%</td>
                    <td>{{ $row->kecepatan_angin }}</td>
                    <td>{{ $row->radiasi_matahari }}</td>
                    <td>{{ $row->curah_hujan }} mm</td>
                    <td>{{ $row->kc }}</td>
                    <td><span class="bv" style="background:rgba(74,124,111,.1);color:var(--water);">{{ $row->eto }}</span></td>
                    <td><span class="bv" style="background:rgba(106,171,154,.1);color:var(--water2);">{{ $row->etc }}</span></td>
                    <td>
                        @php $k = $row->kebutuhan_air; @endphp
                        <span class="bv" style="background:{{ $k>5?'rgba(185,74,60,.08)':($k>3?'rgba(196,137,90,.1)':'rgba(90,122,71,.1)') }};color:{{ $k>5?'#a03828':($k>3?'var(--clay)':'var(--leaf)') }};">
                            {{ $k }} mm
                        </span>
                    </td>
                    <td>
                        <div style="display:flex;gap:.4rem;justify-content:center;">
                            <a href="{{ route('irrigation.edit', $row) }}" class="btn-sm btn-edit">Edit</a>
                            <form method="POST" action="{{ route('irrigation.destroy', $row) }}" style="margin:0;" onsubmit="return confirm('Hapus data ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-sm btn-delete">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="12" style="text-align:center;padding:3rem;color:var(--textlt);">🌤️ Belum ada data iklim.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($tableData->hasPages())
    <div style="padding:1rem 1.5rem;border-top:1px solid var(--border);">{{ $tableData->links() }}</div>
    @endif
</div>
@endsection
