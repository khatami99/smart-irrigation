@extends('layouts.app')

@section('title', 'Blangko O-05 DIP')
@section('page-title', 'Blangko O-05 DIP')

@section('content')

{{-- Filter --}}
<div style="background:var(--cream);border:1px solid var(--border);border-radius:12px;padding:1.5rem;margin-bottom:1.5rem;">
    <form method="GET" action="{{ route('blangko-dip.o05') }}" style="display:flex;gap:1rem;align-items:flex-end;flex-wrap:wrap;">
        <div style="flex:1;min-width:200px;">
            <label style="font-size:.75rem;font-weight:600;color:var(--textlt);text-transform:uppercase;letter-spacing:.06em;display:block;margin-bottom:.4rem;">Daerah Irigasi (Permukaan)</label>
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
        @if($data->isNotEmpty())
        <a href="{{ route('blangko-dip.o05.pdf', ['daerah_irigasi_id' => $diId, 'musim_tanam_id' => $mtId]) }}"
            target="_blank"
            style="padding:.65rem 1.5rem;background:var(--soil);color:var(--straw);border:none;border-radius:8px;font-family:'Karla',sans-serif;font-size:.875rem;font-weight:600;text-decoration:none;">
            ⬇ Download PDF
        </a>
        @endif
    </form>
</div>

@if($data->isNotEmpty())

{{-- Header Blangko --}}
<div style="background:var(--cream);border:1px solid var(--border);border-radius:12px;overflow:hidden;margin-bottom:1.5rem;">
    <div style="background:var(--soil);padding:1rem 1.5rem;display:flex;justify-content:space-between;align-items:center;">
        <div>
            <p style="font-size:.65rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:rgba(232,213,163,.5);margin-bottom:.2rem;">Blangko OP · DIP</p>
            <h3 style="font-family:'Fraunces',serif;font-size:1.2rem;font-weight:700;color:var(--straw);">O-05 — Rencana Kebutuhan Air di Pintu</h3>
        </div>
        <div style="text-align:right;">
            <p style="font-size:.75rem;color:rgba(232,213,163,.6);">{{ $di->kode }} · {{ $di->nama }}</p>
            <p style="font-size:.75rem;color:rgba(232,213,163,.6);">{{ $mt->nama_mt }} · {{ $mt->jenis_mt }}</p>
            <p style="font-size:.7rem;color:rgba(232,213,163,.4);">{{ $mt->tanggal_mulai->format('d M Y') }} — {{ $mt->tanggal_selesai->format('d M Y') }}</p>
        </div>
    </div>

    {{-- Tabel --}}
    <div style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;font-size:.78rem;">
            <thead>
                <tr style="background:rgba(61,43,31,.06);border-bottom:2px solid var(--border);">
                    <th rowspan="2" style="padding:.75rem;text-align:center;font-weight:700;color:var(--textlt);font-size:.68rem;text-transform:uppercase;border-right:1px solid var(--border);white-space:nowrap;">Bulan</th>
                    <th rowspan="2" style="padding:.75rem .5rem;text-align:center;font-weight:700;color:var(--textlt);font-size:.68rem;text-transform:uppercase;border-right:1px solid var(--border);">Dek</th>
                    <th rowspan="2" style="padding:.75rem .5rem;text-align:center;font-weight:700;color:var(--textlt);font-size:.68rem;text-transform:uppercase;border-right:1px solid var(--border);">ETo<br><span style="font-weight:400;">(mm/hr)</span></th>
                    <th rowspan="2" style="padding:.75rem .5rem;text-align:center;font-weight:700;color:var(--textlt);font-size:.68rem;text-transform:uppercase;border-right:1px solid var(--border);">CH<br><span style="font-weight:400;">(mm)</span></th>
                    <th rowspan="2" style="padding:.75rem .5rem;text-align:center;font-weight:700;color:var(--textlt);font-size:.68rem;text-transform:uppercase;border-right:1px solid var(--border);">Re<br><span style="font-weight:400;">(mm/hr)</span></th>
                    <th colspan="3" style="padding:.5rem;text-align:center;font-weight:700;color:var(--textlt);font-size:.68rem;text-transform:uppercase;border-right:1px solid var(--border);border-bottom:1px solid var(--border);">Kc</th>
                    <th colspan="3" style="padding:.5rem;text-align:center;font-weight:700;color:var(--textlt);font-size:.68rem;text-transform:uppercase;border-right:1px solid var(--border);border-bottom:1px solid var(--border);">NFR (mm/hr)</th>
                    <th colspan="3" style="padding:.5rem;text-align:center;font-weight:700;color:var(--textlt);font-size:.68rem;text-transform:uppercase;border-right:1px solid var(--border);border-bottom:1px solid var(--border);">DR (lt/det/ha)</th>
                    <th colspan="3" style="padding:.5rem;text-align:center;font-weight:700;color:var(--textlt);font-size:.68rem;text-transform:uppercase;border-bottom:1px solid var(--border);">Q (lt/det)</th>
                </tr>
                <tr style="background:rgba(61,43,31,.04);border-bottom:1px solid var(--border);">
                    <th style="padding:.5rem;text-align:center;font-size:.65rem;color:var(--textlt);border-right:1px solid rgba(139,94,60,.08);">Padi</th>
                    <th style="padding:.5rem;text-align:center;font-size:.65rem;color:var(--textlt);border-right:1px solid rgba(139,94,60,.08);">Palawija</th>
                    <th style="padding:.5rem;text-align:center;font-size:.65rem;color:var(--textlt);border-right:1px solid var(--border);">Tebu</th>
                    <th style="padding:.5rem;text-align:center;font-size:.65rem;color:var(--textlt);border-right:1px solid rgba(139,94,60,.08);">Padi</th>
                    <th style="padding:.5rem;text-align:center;font-size:.65rem;color:var(--textlt);border-right:1px solid rgba(139,94,60,.08);">Palawija</th>
                    <th style="padding:.5rem;text-align:center;font-size:.65rem;color:var(--textlt);border-right:1px solid var(--border);">Tebu</th>
                    <th style="padding:.5rem;text-align:center;font-size:.65rem;color:var(--textlt);border-right:1px solid rgba(139,94,60,.08);">Padi</th>
                    <th style="padding:.5rem;text-align:center;font-size:.65rem;color:var(--textlt);border-right:1px solid rgba(139,94,60,.08);">Palawija</th>
                    <th style="padding:.5rem;text-align:center;font-size:.65rem;color:var(--textlt);border-right:1px solid var(--border);">Tebu</th>
                    <th style="padding:.5rem;text-align:center;font-size:.65rem;color:var(--textlt);border-right:1px solid rgba(139,94,60,.08);">Padi</th>
                    <th style="padding:.5rem;text-align:center;font-size:.65rem;color:var(--textlt);border-right:1px solid rgba(139,94,60,.08);">Palawija</th>
                    <th style="padding:.5rem;text-align:center;font-size:.65rem;color:var(--textlt);">Tebu</th>
                </tr>
            </thead>
            <tbody>
                @php $efisiensi = $data->first()->efisiensi ?? 0.83; @endphp
                @foreach($data as $row)
                @php
                    $namaBulan  = \Carbon\Carbon::createFromDate($row->tahun, $row->bulan, 1)->translatedFormat('M');
                    $drPadi     = $row->nfr_padi     > 0 ? round($row->nfr_padi     / 8.64, 3) : 0;
                    $drPalawija = $row->nfr_palawija > 0 ? round($row->nfr_palawija / 8.64, 3) : 0;
                    $drTebu     = $row->nfr_tebu     > 0 ? round($row->nfr_tebu     / 8.64, 3) : 0;
                @endphp
                <tr style="border-bottom:1px solid rgba(139,94,60,.07);" onmouseover="this.style.background='rgba(139,94,60,.02)'" onmouseout="this.style.background='none'">
                    <td style="padding:.6rem .75rem;font-weight:600;color:var(--soil);border-right:1px solid var(--border);white-space:nowrap;">{{ $namaBulan }} {{ $row->tahun }}</td>
                    <td style="padding:.6rem .5rem;text-align:center;font-weight:600;color:var(--textlt);border-right:1px solid var(--border);">{{ $row->dekade }}</td>
                    <td style="padding:.6rem .5rem;text-align:center;border-right:1px solid var(--border);">{{ $row->eto_dekade ? number_format($row->eto_dekade, 2) : '—' }}</td>
                    <td style="padding:.6rem .5rem;text-align:center;border-right:1px solid var(--border);">{{ $row->ch_dekade ? number_format($row->ch_dekade, 1) : '—' }}</td>
                    <td style="padding:.6rem .5rem;text-align:center;border-right:1px solid var(--border);">{{ number_format($row->re_dekade, 2) }}</td>
                    <td style="padding:.6rem .5rem;text-align:center;border-right:1px solid rgba(139,94,60,.08);">{{ number_format($row->kc_padi, 2) }}</td>
                    <td style="padding:.6rem .5rem;text-align:center;border-right:1px solid rgba(139,94,60,.08);">{{ number_format($row->kc_palawija, 2) }}</td>
                    <td style="padding:.6rem .5rem;text-align:center;border-right:1px solid var(--border);">{{ number_format($row->kc_tebu, 2) }}</td>
                    <td style="padding:.6rem .5rem;text-align:center;border-right:1px solid rgba(139,94,60,.08);">{{ number_format($row->nfr_padi, 2) }}</td>
                    <td style="padding:.6rem .5rem;text-align:center;border-right:1px solid rgba(139,94,60,.08);">{{ number_format($row->nfr_palawija, 2) }}</td>
                    <td style="padding:.6rem .5rem;text-align:center;border-right:1px solid var(--border);">{{ number_format($row->nfr_tebu, 2) }}</td>
                    <td style="padding:.6rem .5rem;text-align:center;border-right:1px solid rgba(139,94,60,.08);">{{ number_format($drPadi, 3) }}</td>
                    <td style="padding:.6rem .5rem;text-align:center;border-right:1px solid rgba(139,94,60,.08);">{{ number_format($drPalawija, 3) }}</td>
                    <td style="padding:.6rem .5rem;text-align:center;border-right:1px solid var(--border);">{{ number_format($drTebu, 3) }}</td>
                    <td style="padding:.6rem .5rem;text-align:center;font-weight:600;color:var(--water);border-right:1px solid rgba(139,94,60,.08);">{{ number_format($row->kebutuhan_padi, 2) }}</td>
                    <td style="padding:.6rem .5rem;text-align:center;font-weight:600;color:var(--water);border-right:1px solid rgba(139,94,60,.08);">{{ number_format($row->kebutuhan_palawija, 2) }}</td>
                    <td style="padding:.6rem .5rem;text-align:center;font-weight:600;color:var(--water);">{{ number_format($row->kebutuhan_tebu, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="border-top:2px solid var(--border);background:rgba(74,124,111,.05);">
                    <td colspan="14" style="padding:.75rem;font-weight:700;color:var(--soil);">Total Kebutuhan Air</td>
                    <td style="padding:.75rem .5rem;text-align:center;font-weight:700;color:var(--water);">{{ number_format($data->sum('kebutuhan_padi'), 2) }}</td>
                    <td style="padding:.75rem .5rem;text-align:center;font-weight:700;color:var(--water);">{{ number_format($data->sum('kebutuhan_palawija'), 2) }}</td>
                    <td style="padding:.75rem .5rem;text-align:center;font-weight:700;color:var(--water);">{{ number_format($data->sum('kebutuhan_tebu'), 2) }}</td>
                </tr>
                <tr style="background:rgba(74,124,111,.08);">
                    <td colspan="14" style="padding:.75rem;font-weight:700;color:var(--soil);">Total Keseluruhan</td>
                    <td colspan="3" style="padding:.75rem .5rem;text-align:center;font-weight:700;font-size:1rem;color:var(--water);">
                        {{ number_format($data->sum('kebutuhan_total'), 2) }} lt/det
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    {{-- Footer info --}}
    <div style="padding:1rem 1.5rem;border-top:1px solid var(--border);display:flex;gap:2rem;flex-wrap:wrap;">
        <div style="font-size:.75rem;color:var(--textlt);">Efisiensi: <strong style="color:var(--soil);">{{ ($efisiensi * 100) }}%</strong></div>
        <div style="font-size:.75rem;color:var(--textlt);">Perkolasi: <strong style="color:var(--soil);">2.0 mm/hari</strong></div>
        <div style="font-size:.75rem;color:var(--textlt);">WLR: <strong style="color:var(--soil);">3.3 mm/dekade</strong></div>
        <div style="font-size:.75rem;color:var(--textlt);">Re: <strong style="color:var(--soil);">0.7 × CH</strong></div>
        <div style="font-size:.75rem;color:var(--textlt);">Metode: <strong style="color:var(--soil);">KP-01 / FAO-56</strong></div>
    </div>
</div>

@elseif(request()->hasAny(['daerah_irigasi_id', 'musim_tanam_id']))
<div style="text-align:center;padding:4rem 2rem;color:var(--textlt);">
    <p style="font-size:2rem;">📋</p>
    <p style="font-size:.95rem;margin-top:.5rem;">Belum ada data untuk pilihan ini.</p>
    <p style="font-size:.8rem;margin-top:.25rem;">Pastikan Blangko O-01 sudah diisi untuk DI dan MT yang dipilih.</p>
</div>
@else
<div style="text-align:center;padding:4rem 2rem;color:var(--textlt);">
    <p style="font-size:2rem;">📋</p>
    <p style="font-size:.95rem;margin-top:.5rem;">Pilih Daerah Irigasi dan Musim Tanam untuk menampilkan Blangko O-05.</p>
</div>
@endif

@endsection
