@extends('layouts.app')
@section('title', 'Blangko O-09 DIR')
@section('page-title', 'Blangko O-09 DIR')

@section('content')

{{-- Filter --}}
<div style="background:var(--cream);border:1px solid var(--border);border-radius:12px;padding:1.5rem;margin-bottom:1.5rem;">
    <form method="GET" action="{{ route('blangko-dir.o09.index') }}" style="display:flex;gap:1rem;align-items:flex-end;flex-wrap:wrap;">
        <div style="flex:1;min-width:200px;">
            <label style="font-size:.75rem;font-weight:600;color:var(--textlt);text-transform:uppercase;letter-spacing:.06em;display:block;margin-bottom:.4rem;">Daerah Irigasi (Rawa)</label>
            <select name="daerah_irigasi_id" style="width:100%;padding:.6rem .9rem;border:1px solid var(--border);border-radius:8px;background:var(--cream2);color:var(--text);font-family:'Karla',sans-serif;font-size:.875rem;">
                <option value="">— Pilih DI —</option>
                @foreach($daerahIrigasis as $item)
                    <option value="{{ $item->id }}" {{ $diId == $item->id ? 'selected' : '' }}>
                        {{ $item->kode }} — {{ $item->nama }}
                    </option>
                @endforeach
            </select>
        </div>
        <div style="flex:1;min-width:200px;">
            <label style="font-size:.75rem;font-weight:600;color:var(--textlt);text-transform:uppercase;letter-spacing:.06em;display:block;margin-bottom:.4rem;">Musim Tanam</label>
            <select name="musim_tanam_id" style="width:100%;padding:.6rem .9rem;border:1px solid var(--border);border-radius:8px;background:var(--cream2);color:var(--text);font-family:'Karla',sans-serif;font-size:.875rem;">
                <option value="">— Pilih MT —</option>
                @foreach($musimTanams as $item)
                    <option value="{{ $item->id }}" {{ $mtId == $item->id ? 'selected' : '' }}>
                        {{ $item->nama_mt }} ({{ $item->jenis_mt }})
                    </option>
                @endforeach
            </select>
        </div>
        <button type="submit" style="padding:.65rem 1.5rem;background:var(--water);color:white;border:none;border-radius:8px;font-family:'Karla',sans-serif;font-size:.875rem;font-weight:600;cursor:pointer;">
            Tampilkan
        </button>
        @if($petaks->isNotEmpty() && $data->isNotEmpty())
        <a href="{{ route('blangko-dir.o09.create', ['daerah_irigasi_id' => $diId, 'musim_tanam_id' => $mtId]) }}"
            style="padding:.65rem 1.5rem;background:var(--leaf);color:white;border:none;border-radius:8px;font-family:'Karla',sans-serif;font-size:.875rem;font-weight:600;text-decoration:none;">
            + Input Data
        </a>
        <a href="{{ route('blangko-dir.o09.pdf', ['daerah_irigasi_id' => $diId, 'musim_tanam_id' => $mtId]) }}"
            target="_blank"
            style="padding:.65rem 1.5rem;background:var(--soil);color:var(--straw);border:none;border-radius:8px;font-family:'Karla',sans-serif;font-size:.875rem;font-weight:600;text-decoration:none;">
            ⬇ Download PDF
        </a>
        @elseif($petaks->isNotEmpty())
        <a href="{{ route('blangko-dir.o09.create', ['daerah_irigasi_id' => $diId, 'musim_tanam_id' => $mtId]) }}"
            style="padding:.65rem 1.5rem;background:var(--leaf);color:white;border:none;border-radius:8px;font-family:'Karla',sans-serif;font-size:.875rem;font-weight:600;text-decoration:none;">
            + Input Data
        </a>
        @endif
    </form>
</div>

@if($petaks->isNotEmpty() && count($bulan) > 0)

{{-- Header --}}
<div style="background:var(--cream);border:1px solid var(--border);border-radius:12px;overflow:hidden;">
    <div style="background:var(--soil);padding:1rem 1.5rem;display:flex;justify-content:space-between;align-items:center;">
        <div>
            <p style="font-size:.65rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:rgba(232,213,163,.5);margin-bottom:.2rem;">Blangko OP · DIR</p>
            <h3 style="font-family:'Fraunces',serif;font-size:1.2rem;font-weight:700;color:var(--straw);">O-09 — Rencana/Realisasi Tanaman per Petak Tersier</h3>
        </div>
        <div style="text-align:right;">
            <p style="font-size:.75rem;color:rgba(232,213,163,.6);">{{ $di->kode }} · {{ $di->nama }}</p>
            <p style="font-size:.75rem;color:rgba(232,213,163,.6);">{{ $mt->nama_mt }} · {{ $mt->jenis_mt }}</p>
        </div>
    </div>

    <div style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;font-size:.78rem;">
            <thead>
                <tr style="background:rgba(61,43,31,.06);border-bottom:2px solid var(--border);">
                    <th rowspan="3" style="padding:.75rem;text-align:left;font-weight:700;color:var(--textlt);font-size:.68rem;text-transform:uppercase;border-right:1px solid var(--border);white-space:nowrap;min-width:120px;">Petak Tersier</th>
                    @foreach($bulan as $b)
                    <th colspan="8" style="padding:.5rem;text-align:center;font-weight:700;color:var(--textlt);font-size:.68rem;text-transform:uppercase;border-right:1px solid var(--border);border-bottom:1px solid var(--border);">{{ $b['label'] }}</th>
                    @endforeach
                </tr>
                <tr style="background:rgba(61,43,31,.04);border-bottom:1px solid var(--border);">
                    @foreach($bulan as $b)
                    <th colspan="2" style="padding:.4rem;text-align:center;font-size:.65rem;color:var(--water);border-right:1px solid rgba(139,94,60,.08);border-bottom:1px solid var(--border);">Padi</th>
                    <th colspan="2" style="padding:.4rem;text-align:center;font-size:.65rem;color:var(--leaf);border-right:1px solid rgba(139,94,60,.08);border-bottom:1px solid var(--border);">Palawija</th>
                    <th colspan="2" style="padding:.4rem;text-align:center;font-size:.65rem;color:var(--earth);border-right:1px solid rgba(139,94,60,.08);border-bottom:1px solid var(--border);">T. Keras</th>
                    <th colspan="2" style="padding:.4rem;text-align:center;font-size:.65rem;color:var(--textlt);border-right:1px solid var(--border);border-bottom:1px solid var(--border);">Bera</th>
                    @endforeach
                </tr>
                <tr style="background:rgba(61,43,31,.02);border-bottom:1px solid var(--border);">
                    @foreach($bulan as $b)
                        @foreach(['Ren','Real','Ren','Real','Ren','Real','Ren','Real'] as $label)
                        <th style="padding:.35rem .4rem;text-align:center;font-size:.62rem;color:var(--textlt);border-right:1px solid rgba(139,94,60,.06);">{{ $label }}</th>
                        @endforeach
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($petaks as $petak)
                <tr style="border-bottom:1px solid rgba(139,94,60,.07);" onmouseover="this.style.background='rgba(139,94,60,.02)'" onmouseout="this.style.background='none'">
                    <td style="padding:.6rem .75rem;font-weight:600;color:var(--soil);border-right:1px solid var(--border);white-space:nowrap;">
                        <span style="font-size:.72rem;color:var(--textlt);">{{ $petak->kode_petak }}</span><br>
                        {{ $petak->nama_petak ?? $petak->kode_petak }}
                    </td>
                    @foreach($bulan as $b)
                    @php
                        $row = $data->get($petak->id)?->first(fn($r) => $r->bulan == $b['bulan'] && $r->tahun == $b['tahun']);
                    @endphp
                    <td style="padding:.4rem;text-align:center;font-size:.75rem;border-right:1px solid rgba(139,94,60,.06);">{{ $row ? number_format($row->rencana_padi, 1) : '—' }}</td>
                    <td style="padding:.4rem;text-align:center;font-size:.75rem;color:var(--water);border-right:1px solid rgba(139,94,60,.06);">{{ $row ? number_format($row->realisasi_padi, 1) : '—' }}</td>
                    <td style="padding:.4rem;text-align:center;font-size:.75rem;border-right:1px solid rgba(139,94,60,.06);">{{ $row ? number_format($row->rencana_palawija, 1) : '—' }}</td>
                    <td style="padding:.4rem;text-align:center;font-size:.75rem;color:var(--leaf);border-right:1px solid rgba(139,94,60,.06);">{{ $row ? number_format($row->realisasi_palawija, 1) : '—' }}</td>
                    <td style="padding:.4rem;text-align:center;font-size:.75rem;border-right:1px solid rgba(139,94,60,.06);">{{ $row ? number_format($row->rencana_tanaman_keras, 1) : '—' }}</td>
                    <td style="padding:.4rem;text-align:center;font-size:.75rem;color:var(--earth);border-right:1px solid rgba(139,94,60,.06);">{{ $row ? number_format($row->realisasi_tanaman_keras, 1) : '—' }}</td>
                    <td style="padding:.4rem;text-align:center;font-size:.75rem;border-right:1px solid rgba(139,94,60,.06);">{{ $row ? number_format($row->rencana_bera, 1) : '—' }}</td>
                    <td style="padding:.4rem;text-align:center;font-size:.75rem;color:var(--textlt);border-right:1px solid var(--border);">{{ $row ? number_format($row->realisasi_bera, 1) : '—' }}</td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@elseif($diId && $mtId)
<div style="text-align:center;padding:4rem 2rem;color:var(--textlt);">
    <p style="font-size:2rem;">📋</p>
    <p style="font-size:.95rem;margin-top:.5rem;">Belum ada petak tersier untuk DI ini.</p>
</div>
@else
<div style="text-align:center;padding:4rem 2rem;color:var(--textlt);">
    <p style="font-size:2rem;">📋</p>
    <p style="font-size:.95rem;margin-top:.5rem;">Pilih Daerah Irigasi Rawa dan Musim Tanam untuk menampilkan O-09.</p>
</div>
@endif

@endsection
