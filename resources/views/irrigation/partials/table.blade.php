<style>
    :root {
        --soil:#3d2b1f;--soil2:#5c3d2e;--straw:#e8d5a3;--cream:#faf6ef;--cream2:#f5ede0;
        --water:#4a7c6f;--water2:#6aab9a;--leaf:#5a7a47;
        --text:#4a3728;--textlt:#7a6355;--border:rgba(139,94,60,.12);
    }
</style>

{{-- Table header --}}
<div style="padding:1.25rem 1.5rem;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;">
    <div>
        <p style="font-size:.65rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--textlt);margin-bottom:.2rem;">Histori</p>
        <h3 style="font-family:'Fraunces',serif;font-size:1rem;font-weight:600;color:var(--soil);">Dataset Pengamatan</h3>
    </div>
    <span style="background:var(--cream2);border:1px solid var(--border);border-radius:20px;padding:.3rem .9rem;font-size:.72rem;font-weight:700;color:var(--textlt);">
        {{ $tableData->total() }} Data
    </span>
</div>

{{-- Table --}}
<div style="overflow-x:auto;">
    <table style="width:100%;border-collapse:collapse;">
        <thead>
            <tr style="border-bottom:1px solid var(--border);">
                <th style="padding:.75rem 1.5rem;text-align:left;font-size:.65rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--textlt);">Tanggal</th>
                <th style="padding:.75rem 1rem;text-align:center;font-size:.65rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--textlt);">ETo</th>
                <th style="padding:.75rem 1rem;text-align:center;font-size:.65rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--textlt);">ETc</th>
                <th style="padding:.75rem 1rem;text-align:center;font-size:.65rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--textlt);">Hujan (mm)</th>
                <th style="padding:.75rem 1rem;text-align:center;font-size:.65rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--textlt);">Kebutuhan</th>
                <th style="padding:.75rem 1.5rem;text-align:center;font-size:.65rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--textlt);">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tableData as $item)
            <tr style="border-bottom:1px solid rgba(139,94,60,.06);transition:background .15s;"
                onmouseover="this.style.background='rgba(74,124,111,.04)'"
                onmouseout="this.style.background='transparent'">
                <td style="padding:.9rem 1.5rem;">
                    <span style="font-family:'Fraunces',serif;font-size:.9rem;font-weight:600;color:var(--soil);">{{ $item->tanggal }}</span>
                </td>
                <td style="padding:.9rem 1rem;text-align:center;font-size:.875rem;color:var(--textlt);">{{ number_format($item->eto, 1) }}</td>
                <td style="padding:.9rem 1rem;text-align:center;font-size:.875rem;color:var(--textlt);">{{ number_format($item->etc, 1) }}</td>
                <td style="padding:.9rem 1rem;text-align:center;">
                    <span style="background:rgba(74,124,111,.08);border:1px solid rgba(74,124,111,.15);color:var(--water);border-radius:5px;padding:.2rem .6rem;font-size:.75rem;font-weight:700;">
                        {{ $item->curah_hujan }}
                    </span>
                </td>
                <td style="padding:.9rem 1rem;text-align:center;">
                    <span style="font-family:'Fraunces',serif;font-weight:700;font-size:.95rem;color:var(--soil);">
                        {{ $item->kebutuhan_air }} <span style="font-size:.72rem;font-weight:400;color:var(--textlt);">mm</span>
                    </span>
                </td>
                <td style="padding:.9rem 1.5rem;text-align:center;">
                    <div style="display:flex;align-items:center;justify-content:center;gap:.5rem;">
                        <a href="{{ route('irrigation.edit', $item) }}"
                           style="padding:.35rem .9rem;background:rgba(139,94,60,.08);border:1px solid rgba(139,94,60,.15);color:var(--earth);border-radius:6px;font-size:.75rem;font-weight:600;text-decoration:none;transition:background .2s;"
                           onmouseover="this.style.background='rgba(139,94,60,.15)'"
                           onmouseout="this.style.background='rgba(139,94,60,.08)'">
                            Edit
                        </a>
                        <form method="POST" action="{{ route('irrigation.destroy', $item) }}"
                              onsubmit="return confirm('Hapus data tanggal {{ $item->tanggal }}?')" style="margin:0;">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                style="padding:.35rem .9rem;background:rgba(185,74,60,.07);border:1px solid rgba(185,74,60,.15);color:#a03828;border-radius:6px;font-size:.75rem;font-weight:600;cursor:pointer;font-family:'Karla',sans-serif;transition:background .2s;"
                                onmouseover="this.style.background='rgba(185,74,60,.15)'"
                                onmouseout="this.style.background='rgba(185,74,60,.07)'">
                                Hapus
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{-- Pagination --}}
<div style="padding:1rem 1.5rem;border-top:1px solid var(--border);background:rgba(245,237,224,.4);">
    {{ $tableData->links() }}
</div>
