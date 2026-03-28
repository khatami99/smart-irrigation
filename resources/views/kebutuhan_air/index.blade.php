@extends('layouts.app')

@section('title', 'Kebutuhan Air Irigasi')
@section('page-title', 'Kebutuhan Air Irigasi')

@section('content')

{{-- Filter --}}
<div style="background:var(--cream);border:1px solid var(--border);border-radius:12px;padding:1.5rem;margin-bottom:1.5rem;">
    <form method="GET" action="{{ route('kebutuhan-air.index') }}" style="display:flex;gap:1rem;align-items:flex-end;flex-wrap:wrap;">
        <div style="flex:1;min-width:200px;">
            <label style="font-size:.75rem;font-weight:600;color:var(--textlt);text-transform:uppercase;letter-spacing:.06em;display:block;margin-bottom:.4rem;">Daerah Irigasi</label>
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
    </form>
</div>

@if($data->isNotEmpty())

    {{-- Info DI + MT --}}
    <div style="display:flex;gap:1rem;margin-bottom:1.5rem;flex-wrap:wrap;">
        <div style="flex:1;min-width:200px;background:var(--cream);border:1px solid var(--border);border-radius:10px;padding:1rem 1.25rem;">
            <p style="font-size:.7rem;color:var(--textlt);text-transform:uppercase;letter-spacing:.06em;font-weight:700;">Daerah Irigasi</p>
            <p style="font-size:1rem;font-weight:700;color:var(--soil);font-family:'Fraunces',serif;">{{ $di->nama }}</p>
            <p style="font-size:.8rem;color:var(--textlt);">{{ $di->kode }} · {{ number_format($di->luas_total, 0, ',', '.') }} ha</p>
        </div>
        <div style="flex:1;min-width:200px;background:var(--cream);border:1px solid var(--border);border-radius:10px;padding:1rem 1.25rem;">
            <p style="font-size:.7rem;color:var(--textlt);text-transform:uppercase;letter-spacing:.06em;font-weight:700;">Musim Tanam</p>
            <p style="font-size:1rem;font-weight:700;color:var(--soil);font-family:'Fraunces',serif;">{{ $mt->nama_mt }}</p>
            <p style="font-size:.8rem;color:var(--textlt);">{{ $mt->jenis_mt }} · {{ $mt->tanggal_mulai->format('d M Y') }} – {{ $mt->tanggal_selesai->format('d M Y') }}</p>
        </div>

        {{-- Summary cards --}}
        <div style="flex:1;min-width:160px;background:rgba(74,124,111,.08);border:1px solid rgba(74,124,111,.2);border-radius:10px;padding:1rem 1.25rem;">
            <p style="font-size:.7rem;color:var(--water);text-transform:uppercase;letter-spacing:.06em;font-weight:700;">Total Kebutuhan</p>
            <p style="font-size:1.25rem;font-weight:700;color:var(--water);font-family:'Fraunces',serif;">{{ number_format($summary['total_semua'], 2) }}</p>
            <p style="font-size:.75rem;color:var(--textlt);">lt/det (rerata per dekade)</p>
        </div>
        <div style="flex:1;min-width:160px;background:rgba(90,122,71,.08);border:1px solid rgba(90,122,71,.2);border-radius:10px;padding:1rem 1.25rem;">
            <p style="font-size:.7rem;color:var(--leaf);text-transform:uppercase;letter-spacing:.06em;font-weight:700;">Rata ETo</p>
            <p style="font-size:1.25rem;font-weight:700;color:var(--leaf);font-family:'Fraunces',serif;">{{ $summary['rata_eto'] ?? '—' }}</p>
            <p style="font-size:.75rem;color:var(--textlt);">mm/hari</p>
        </div>
        <div style="flex:1;min-width:160px;background:rgba(196,137,90,.08);border:1px solid rgba(196,137,90,.2);border-radius:10px;padding:1rem 1.25rem;">
            <p style="font-size:.7rem;color:var(--clay);text-transform:uppercase;letter-spacing:.06em;font-weight:700;">Total CH</p>
            <p style="font-size:1.25rem;font-weight:700;color:var(--clay);font-family:'Fraunces',serif;">{{ $summary['total_ch'] ?? '—' }}</p>
            <p style="font-size:.75rem;color:var(--textlt);">mm</p>
        </div>
    </div>

    {{-- Tabel --}}
    <div style="background:var(--cream);border:1px solid var(--border);border-radius:12px;overflow:hidden;">
        <div style="padding:1rem 1.5rem;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;">
            <h3 style="font-family:'Fraunces',serif;font-size:1rem;font-weight:700;color:var(--soil);">Kebutuhan Air per Dekade</h3>
            <span style="font-size:.8rem;color:var(--textlt);">{{ $data->count() }} dekade</span>
        </div>
        <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;font-size:.8rem;">
                <thead>
                    <tr style="background:rgba(61,43,31,.04);">
                        <th style="padding:.75rem 1rem;text-align:left;font-weight:700;color:var(--textlt);font-size:.7rem;text-transform:uppercase;letter-spacing:.06em;white-space:nowrap;">Dekade</th>
                        <th style="padding:.75rem 1rem;text-align:right;font-weight:700;color:var(--textlt);font-size:.7rem;text-transform:uppercase;letter-spacing:.06em;">ETo</th>
                        <th style="padding:.75rem 1rem;text-align:right;font-weight:700;color:var(--textlt);font-size:.7rem;text-transform:uppercase;letter-spacing:.06em;">CH</th>
                        <th style="padding:.75rem 1rem;text-align:right;font-weight:700;color:var(--textlt);font-size:.7rem;text-transform:uppercase;letter-spacing:.06em;">Re</th>
                        <th style="padding:.75rem 1rem;text-align:right;font-weight:700;color:var(--textlt);font-size:.7rem;text-transform:uppercase;letter-spacing:.06em;">NFR Padi</th>
                        <th style="padding:.75rem 1rem;text-align:right;font-weight:700;color:var(--textlt);font-size:.7rem;text-transform:uppercase;letter-spacing:.06em;">NFR Palawija</th>
                        <th style="padding:.75rem 1rem;text-align:right;font-weight:700;color:var(--textlt);font-size:.7rem;text-transform:uppercase;letter-spacing:.06em;">NFR Tebu</th>
                        <th style="padding:.75rem 1rem;text-align:right;font-weight:700;color:var(--water);font-size:.7rem;text-transform:uppercase;letter-spacing:.06em;">Total (lt/det)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $row)
                    @php
                        $namaBulan = \Carbon\Carbon::createFromDate($row->tahun, $row->bulan, 1)->translatedFormat('M');
                    @endphp
                    <tr style="border-top:1px solid var(--border);" onmouseover="this.style.background='rgba(139,94,60,.03)'" onmouseout="this.style.background='none'">
                        <td style="padding:.75rem 1rem;font-weight:600;color:var(--soil);">
                            {{ $namaBulan }}-{{ $row->dekade }} {{ $row->tahun }}
                        </td>
                        <td style="padding:.75rem 1rem;text-align:right;color:var(--textlt);">
                            {{ $row->eto_dekade ? number_format($row->eto_dekade, 2) : '—' }}
                        </td>
                        <td style="padding:.75rem 1rem;text-align:right;color:var(--textlt);">
                            {{ $row->ch_dekade ? number_format($row->ch_dekade, 1) : '—' }}
                        </td>
                        <td style="padding:.75rem 1rem;text-align:right;color:var(--textlt);">
                            {{ number_format($row->re_dekade, 2) }}
                        </td>
                        <td style="padding:.75rem 1rem;text-align:right;">{{ number_format($row->nfr_padi, 2) }}</td>
                        <td style="padding:.75rem 1rem;text-align:right;">{{ number_format($row->nfr_palawija, 2) }}</td>
                        <td style="padding:.75rem 1rem;text-align:right;">{{ number_format($row->nfr_tebu, 2) }}</td>
                        <td style="padding:.75rem 1rem;text-align:right;font-weight:700;color:var(--water);">
                            {{ number_format($row->kebutuhan_total, 2) }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="border-top:2px solid var(--border);background:rgba(74,124,111,.06);">
                        <td colspan="7" style="padding:.75rem 1rem;font-weight:700;color:var(--soil);">Total</td>
                        <td style="padding:.75rem 1rem;text-align:right;font-weight:700;color:var(--water);">
                            {{ number_format($summary['total_semua'], 2) }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

@elseif(request()->hasAny(['daerah_irigasi_id', 'musim_tanam_id']))
    <div style="text-align:center;padding:4rem 2rem;color:var(--textlt);">
        <p style="font-size:2rem;">💧</p>
        <p style="font-size:.95rem;margin-top:.5rem;">Belum ada data kebutuhan air untuk pilihan ini.</p>
        <p style="font-size:.8rem;margin-top:.25rem;">Pastikan Blangko O-01 sudah diisi untuk DI dan MT yang dipilih.</p>
    </div>
@else
    <div style="text-align:center;padding:4rem 2rem;color:var(--textlt);">
        <p style="font-size:2rem;">💧</p>
        <p style="font-size:.95rem;margin-top:.5rem;">Pilih Daerah Irigasi dan Musim Tanam untuk melihat kebutuhan air.</p>
    </div>
@endif

@endsection
