<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
    * { box-sizing:border-box;margin:0;padding:0; }
    body { font-family:'DejaVu Sans',sans-serif;font-size:6.5pt;color:#3d2b1f;background:white; }
    .header { text-align:center;margin-bottom:8px;border-bottom:2px solid #3d2b1f;padding-bottom:6px; }
    .header h1 { font-size:10pt;font-weight:bold; }
    .header h2 { font-size:8pt;font-weight:bold;margin-top:2px; }
    .header p  { font-size:7pt;margin-top:2px;color:#7a6355; }
    table { width:100%;border-collapse:collapse; }
    th, td { border:0.5pt solid #8b5e3c;padding:2pt 3pt;text-align:center;font-size:6pt; }
    th { background:#f5ede0;font-weight:bold; }
    td.label { text-align:left;font-weight:600; }
    .footer { margin-top:8px;font-size:6pt;color:#7a6355;border-top:0.5pt solid #8b5e3c;padding-top:4px; }
    .footer-left { float:left; }
    .footer-right { float:right; }
    .sign-box { margin-top:16px;width:100%; }
    .sign-box table { border:none; }
    .sign-box td { border:none;text-align:center;width:33%;padding-top:0;vertical-align:bottom; }
    .sign-line { border-top:1pt solid #3d2b1f;margin-top:50px;padding-top:3px;font-size:6.5pt; }
</style>
</head>
<body>

<div class="header">
    <h1>BLANGKO O-09 — RENCANA/REALISASI TANAMAN PER PETAK TERSIER</h1>
    <h2>{{ strtoupper($di->nama) }} ({{ $di->kode }}) · {{ strtoupper($mt->nama_mt) }}</h2>
    <p>{{ $mt->tanggal_mulai->format('d M Y') }} s/d {{ $mt->tanggal_selesai->format('d M Y') }}</p>
</div>

<table>
    <thead>
        <tr>
            <th rowspan="3" style="min-width:60pt;">Petak Tersier</th>
            @foreach($bulan as $b)
            <th colspan="8">{{ $b['label'] }}</th>
            @endforeach
        </tr>
        <tr>
            @foreach($bulan as $b)
            <th colspan="2" style="color:#4a7c6f;">Padi</th>
            <th colspan="2" style="color:#5a7a47;">Palawija</th>
            <th colspan="2" style="color:#8b5e3c;">T.Keras</th>
            <th colspan="2">Bera</th>
            @endforeach
        </tr>
        <tr>
            @foreach($bulan as $b)
                @foreach(['Ren','Real','Ren','Real','Ren','Real','Ren','Real'] as $l)
                <th>{{ $l }}</th>
                @endforeach
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach($petaks as $petak)
        <tr>
            <td class="label">{{ $petak->kode_petak }}</td>
            @foreach($bulan as $b)
            @php
                $row = $data->get($petak->id)?->first(fn($r) => $r->bulan == $b['bulan'] && $r->tahun == $b['tahun']);
            @endphp
            <td>{{ $row ? number_format($row->rencana_padi, 1) : '-' }}</td>
            <td>{{ $row ? number_format($row->realisasi_padi, 1) : '-' }}</td>
            <td>{{ $row ? number_format($row->rencana_palawija, 1) : '-' }}</td>
            <td>{{ $row ? number_format($row->realisasi_palawija, 1) : '-' }}</td>
            <td>{{ $row ? number_format($row->rencana_tanaman_keras, 1) : '-' }}</td>
            <td>{{ $row ? number_format($row->realisasi_tanaman_keras, 1) : '-' }}</td>
            <td>{{ $row ? number_format($row->rencana_bera, 1) : '-' }}</td>
            <td>{{ $row ? number_format($row->realisasi_bera, 1) : '-' }}</td>
            @endforeach
        </tr>
        @endforeach
    </tbody>
</table>

<div class="footer">
    <span class="footer-left">Satuan: Hektar (ha) · Ren = Rencana · Real = Realisasi</span>
    <span class="footer-right">Dicetak: {{ now()->translatedFormat('d M Y H:i') }}</span>
</div>

<div class="sign-box">
    <table>
        <tr>
            <td><p>Dibuat oleh,</p><div class="sign-line">Petugas Lapangan</div></td>
            <td><p>Diperiksa oleh,</p><div class="sign-line">Juru Pengairan</div></td>
            <td><p>Diketahui oleh,</p><div class="sign-line">Kepala Pengairan</div></td>
        </tr>
    </table>
</div>

</body>
</html>
