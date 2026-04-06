<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
    * { box-sizing:border-box;margin:0;padding:0; }
    body { font-family:'DejaVu Sans',sans-serif;font-size:7pt;color:#3d2b1f;background:white; }
    .header { text-align:center;margin-bottom:8px;border-bottom:2px solid #3d2b1f;padding-bottom:6px; }
    .header h1 { font-size:10pt;font-weight:bold; }
    .header h2 { font-size:8.5pt;font-weight:bold;margin-top:2px; }
    .header p  { font-size:7pt;margin-top:2px;color:#7a6355; }
    table { width:100%;border-collapse:collapse; }
    th, td { border:0.5pt solid #8b5e3c;padding:2.5pt 3pt;text-align:center; }
    th { background:#f5ede0;font-weight:bold;font-size:6.5pt; }
    td { font-size:6.5pt; }
    td.label { text-align:left;font-weight:600; }
    tr.total-row td { background:#e8f4f0;font-weight:bold; }
    tr.grand-total td { background:#4a7c6f;color:white;font-weight:bold; }
    .footer { margin-top:8px;font-size:6.5pt;color:#7a6355;border-top:0.5pt solid #8b5e3c;padding-top:5px; }
    .footer-left { float:left; }
    .footer-right { float:right; }
    .sign-box { margin-top:16px;width:100%; }
    .sign-box table { border:none;width:100%; }
    .sign-box td { border:none;text-align:center;width:33%;padding-top:60px;vertical-align:bottom; }
    .sign-line { border-top:1pt solid #3d2b1f;padding-top:3px;font-size:7pt;margin-top:60px; }
    .sign-line { border-top:1pt solid #3d2b1f;padding-top:3px;font-size:7pt; }
    .sign-item { text-align:center;width:30%; }
    .sign-space {height: 75px;}
    .sign-line {border-top: 1pt solid #3d2b1f;padding-top: 4px;font-size: 7pt;margin-top: 0;}
</style>
</head>
<body>

<div class="header">
    <h1>BLANGKO O-05 — RENCANA KEBUTUHAN AIR DI PINTU</h1>
    <h2>{{ strtoupper($di->nama) }} ({{ $di->kode }}) · {{ strtoupper($mt->nama_mt) }}</h2>
    <p>{{ $mt->tanggal_mulai->format('d M Y') }} s/d {{ $mt->tanggal_selesai->format('d M Y') }} · Luas: {{ number_format($di->luas_total, 0, ',', '.') }} ha · Efisiensi: {{ ($data->first()->efisiensi ?? 0.83) * 100 }}%</p>
</div>

<table>
    <thead>
        <tr>
            <th rowspan="2">Bulan</th>
            <th rowspan="2">Dek</th>
            <th rowspan="2">ETo<br>(mm/hr)</th>
            <th rowspan="2">CH<br>(mm)</th>
            <th rowspan="2">Re<br>(mm/hr)</th>
            <th colspan="3">Kc</th>
            <th colspan="3">NFR (mm/hr)</th>
            <th colspan="3">Q (lt/det)</th>
            <th rowspan="2">Total Q<br>(lt/det)</th>
        </tr>
        <tr>
            <th>Padi</th><th>Palawija</th><th>Tebu</th>
            <th>Padi</th><th>Palawija</th><th>Tebu</th>
            <th>Padi</th><th>Palawija</th><th>Tebu</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $row)
        @php
            $namaBulan = \Carbon\Carbon::createFromDate($row->tahun, $row->bulan, 1)->translatedFormat('M');
        @endphp
        <tr>
            <td class="label">{{ $namaBulan }} {{ $row->tahun }}</td>
            <td>{{ $row->dekade }}</td>
            <td>{{ $row->eto_dekade ? number_format($row->eto_dekade, 2) : '-' }}</td>
            <td>{{ $row->ch_dekade ? number_format($row->ch_dekade, 1) : '-' }}</td>
            <td>{{ number_format($row->re_dekade, 2) }}</td>
            <td>{{ number_format($row->kc_padi, 2) }}</td>
            <td>{{ number_format($row->kc_palawija, 2) }}</td>
            <td>{{ number_format($row->kc_tebu, 2) }}</td>
            <td>{{ number_format($row->nfr_padi, 2) }}</td>
            <td>{{ number_format($row->nfr_palawija, 2) }}</td>
            <td>{{ number_format($row->nfr_tebu, 2) }}</td>
            <td>{{ number_format($row->kebutuhan_padi, 2) }}</td>
            <td>{{ number_format($row->kebutuhan_palawija, 2) }}</td>
            <td>{{ number_format($row->kebutuhan_tebu, 2) }}</td>
            <td><strong>{{ number_format($row->kebutuhan_total, 2) }}</strong></td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr class="total-row">
            <td colspan="11" class="label">Total per Komoditas (lt/det)</td>
            <td>{{ number_format($data->sum('kebutuhan_padi'), 2) }}</td>
            <td>{{ number_format($data->sum('kebutuhan_palawija'), 2) }}</td>
            <td>{{ number_format($data->sum('kebutuhan_tebu'), 2) }}</td>
            <td></td>
        </tr>
        <tr class="grand-total">
            <td colspan="14" class="label">TOTAL KEBUTUHAN AIR KESELURUHAN</td>
            <td>{{ number_format($data->sum('kebutuhan_total'), 2) }} lt/det</td>
        </tr>
    </tfoot>
</table>

<div class="footer">
    <span class="footer-left">Perkolasi: 2.0 mm/hari · WLR: 3.3 mm/dekade · Re = 0.7 × CH · Metode KP-01/FAO-56</span>
    <span class="footer-right">Dicetak: {{ now()->translatedFormat('d M Y H:i') }}</span>
</div>

<div class="sign-box">
    <table>
        <tr>
            <td style="width:33%; text-align:center; vertical-align:bottom;">
                <p>Dibuat oleh,</p>
                <div class="sign-space"></div>
                <div class="sign-line">Petugas Lapangan</div>
            </td>
            <td style="width:33%; text-align:center; vertical-align:bottom;">
                <p>Diperiksa oleh,</p>
                <div class="sign-space"></div>
                <div class="sign-line">Juru Pengairan</div>
            </td>
            <td style="width:33%; text-align:center; vertical-align:bottom;">
                <p>Diketahui oleh,</p>
                <div class="sign-space"></div>
                <div class="sign-line">Kepala Pengairan</div>
            </td>
        </tr>
    </table>
</div>

</body>
</html>
