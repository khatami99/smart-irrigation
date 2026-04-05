{{-- resources/views/blangko_o01/show.blade.php --}}
@extends('layouts.app')
@section('title', 'Detail O-01 — Smart Irrigation')
@section('page-title', 'Blangko O-01 Usulan Luas Tanam')

@section('content')
<style>
    :root { --soil:#3d2b1f;--soil2:#5c3d2e;--earth:#8b5e3c;--straw:#e8d5a3;--cream:#faf6ef;--cream2:#f5ede0;--water:#4a7c6f;--water2:#6aab9a;--leaf:#5a7a47;--text:#4a3728;--textlt:#7a6355;--border:rgba(139,94,60,.14); }
    .card { background:var(--cream);border:1px solid var(--border);border-radius:14px;padding:1.75rem; }
    .section-box { background:var(--cream2);border:1px solid var(--border);border-radius:10px;padding:1.25rem;margin-bottom:1.25rem; }
    .section-title { font-size:.75rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:var(--textlt);margin-bottom:1rem; }
    .detail-row { display:flex;justify-content:space-between;align-items:baseline;padding:.5rem 0;border-bottom:1px solid rgba(139,94,60,.07); }
    .detail-row:last-child { border-bottom:none; }
    .detail-label { font-size:.8rem;color:var(--textlt); }
    .detail-val { font-size:.9rem;font-weight:600;color:var(--soil); }
    .badge { display:inline-block;padding:.22rem .65rem;border-radius:20px;font-size:.72rem;font-weight:700; }
    .badge-usulan   { background:rgba(196,137,90,.12);border:1px solid rgba(196,137,90,.25);color:var(--earth); }
    .badge-disetujui{ background:rgba(90,122,71,.12);border:1px solid rgba(90,122,71,.25);color:var(--leaf); }
    .badge-revisi   { background:rgba(185,74,60,.08);border:1px solid rgba(185,74,60,.2);color:#a03828; }
    .ska-table { width:100%;border-collapse:collapse;font-size:.85rem; }
    .ska-table th { padding:.6rem .9rem;font-size:.65rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--textlt);border-bottom:2px solid var(--border);text-align:left; }
    .ska-table td { padding:.65rem .9rem;border-bottom:1px solid rgba(139,94,60,.07);color:var(--text); }
    .ska-table tr:last-child td { border-bottom:none; }
    .total-row td { font-weight:700;color:var(--soil);background:rgba(74,124,111,.06); }
</style>

<div style="margin-bottom:1.5rem;">
    <a href="{{ route('blangko-dip.o01.index') }}" style="font-size:.82rem;color:var(--textlt);text-decoration:none;">← Kembali ke O-01</a>
</div>

<div style="max-width:800px;">
    {{-- Header --}}
    <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:1.5rem;">
        <div>
            <h2 style="font-family:'Fraunces',serif;font-size:1.5rem;font-weight:700;color:var(--soil);">
                {{ $blangkoO01->daerahIrigasi->nama }}
            </h2>
            <p style="font-size:.85rem;color:var(--textlt);margin-top:.25rem;">
                {{ $blangkoO01->musimTanam->nama_mt }} · Diinput oleh {{ $blangkoO01->user->name }}
            </p>
        </div>
        <span class="badge badge-{{ $blangkoO01->status }}">{{ $blangkoO01->label_status }}</span>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;">

        {{-- Kolom Kiri --}}
        <div>
            {{-- Identitas DI --}}
            <div class="section-box">
                <p class="section-title">📍 Identitas DI</p>
                <div class="detail-row">
                    <span class="detail-label">Kode</span>
                    <span class="detail-val">{{ $blangkoO01->daerahIrigasi->kode }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Jenis</span>
                    <span class="detail-val">{{ $blangkoO01->daerahIrigasi->jenis === 'permukaan' ? 'DIP (Permukaan)' : 'DIR (Rawa)' }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Luas Total</span>
                    <span class="detail-val">{{ number_format($blangkoO01->daerahIrigasi->luas_total, 2) }} ha</span>
                </div>
            </div>

            {{-- Luas Tanam --}}
            <div class="section-box">
                <p class="section-title">🌾 Luas Tanam (ha)</p>
                <table class="ska-table">
                    <thead>
                        <tr>
                            <th>Komoditas</th>
                            <th style="text-align:right;">Usulan</th>
                            <th style="text-align:right;">Disetujui</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Padi</td>
                            <td style="text-align:right;">{{ number_format($blangkoO01->luas_padi_usulan, 2) }}</td>
                            <td style="text-align:right;font-weight:600;color:var(--water);">{{ number_format($blangkoO01->luas_padi_disetujui ?? $blangkoO01->luas_padi_usulan, 2) }}</td>
                        </tr>
                        <tr>
                            <td>Palawija</td>
                            <td style="text-align:right;">{{ number_format($blangkoO01->luas_palawija_usulan, 2) }}</td>
                            <td style="text-align:right;font-weight:600;color:var(--leaf);">{{ number_format($blangkoO01->luas_palawija_disetujui ?? $blangkoO01->luas_palawija_usulan, 2) }}</td>
                        </tr>
                        <tr>
                            <td>Tebu</td>
                            <td style="text-align:right;">{{ number_format($blangkoO01->luas_tebu_usulan, 2) }}</td>
                            <td style="text-align:right;font-weight:600;color:var(--earth);">{{ number_format($blangkoO01->luas_tebu_disetujui ?? $blangkoO01->luas_tebu_usulan, 2) }}</td>
                        </tr>
                        <tr class="total-row">
                            <td>Total</td>
                            <td style="text-align:right;">{{ number_format($blangkoO01->total_usulan, 2) }}</td>
                            <td style="text-align:right;">{{ number_format($blangkoO01->total_disetujui, 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Kolom Kanan: Perhitungan Kebutuhan Air --}}
        <div>
            <div class="section-box" style="border-color:rgba(74,124,111,.3);">
                <p class="section-title" style="color:var(--water);">💧 Kebutuhan Air (O-05)</p>
                @php
                    $di = $blangkoO01->daerahIrigasi;
                    $isRawa = $di->isRawa();
                @endphp

                @if($isRawa)
                <p style="font-size:.8rem;color:var(--textlt);margin-bottom:.75rem;">
                    Metode DIR — kehilangan air {{ $di->pct_kehilangan_air ?? 35 }}% dari total luas
                </p>
                <div class="detail-row">
                    <span class="detail-label">Kebutuhan Air</span>
                    <span class="detail-val" style="color:var(--water);font-size:1.1rem;">{{ number_format($blangkoO01->hitungKebutuhanAir(), 2) }} l/det</span>
                </div>
                @else
                <p style="font-size:.8rem;color:var(--textlt);margin-bottom:.75rem;">
                    Metode DIP · Faktor Tersier: {{ $di->faktor_tersier ?? 0.83 }}
                </p>
                <table class="ska-table">
                    <thead>
                        <tr>
                            <th>Fase</th>
                            <th style="text-align:right;">Keb. Air (l/det)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Pengolahan Tanah</td>
                            <td style="text-align:right;font-weight:600;">{{ number_format($blangkoO01->hitungKebutuhanAir('pengolahan'), 2) }}</td>
                        </tr>
                        <tr>
                            <td>Pertumbuhan</td>
                            <td style="text-align:right;font-weight:600;color:var(--water);">{{ number_format($blangkoO01->hitungKebutuhanAir('pertumbuhan'), 2) }}</td>
                        </tr>
                        <tr>
                            <td>Panen</td>
                            <td style="text-align:right;font-weight:600;">0.00</td>
                        </tr>
                    </tbody>
                </table>
                @endif

                <div style="margin-top:1rem;padding:.75rem;background:rgba(74,124,111,.06);border-radius:8px;">
                    <p style="font-size:.72rem;color:var(--textlt);">
                        Nilai SKA berdasarkan <strong>Permen PU No. 32/PRT/M/2007</strong><br>
                        SKA Padi Pengolahan: {{ $di->ska_padi_pengolahan ?? 1.250 }} l/det/ha<br>
                        SKA Padi Pertumbuhan: {{ $di->ska_padi_pertumbuhan ?? 0.725 }} l/det/ha<br>
                        SKA Palawija: {{ $di->ska_palawija_banyak ?? 0.300 }} l/det/ha
                    </p>
                </div>
            </div>

            @if($blangkoO01->keterangan)
            <div class="section-box">
                <p class="section-title">📝 Keterangan</p>
                <p style="font-size:.875rem;color:var(--text);">{{ $blangkoO01->keterangan }}</p>
            </div>
            @endif
        </div>
    </div>

    <div style="display:flex;gap:.75rem;margin-top:1rem;">
        @can('edit blangko-op')
        <a href="{{ route('blangko-dip.o01.edit', $blangkoO01) }}"
            style="background:var(--soil);color:var(--straw);padding:.65rem 1.5rem;border-radius:8px;font-size:.875rem;font-weight:600;text-decoration:none;">
            Edit O-01
        </a>
        @endcan
        <a href="{{ route('blangko-dip.o01.index') }}"
            style="background:rgba(139,94,60,.08);border:1.5px solid var(--border);color:var(--textlt);padding:.65rem 1.25rem;border-radius:8px;font-size:.875rem;font-weight:600;text-decoration:none;">
            Kembali
        </a>
    </div>
</div>
@endsection
