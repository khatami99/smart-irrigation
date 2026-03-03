@extends('laporan.pdf.layout')

@section('doc-title', 'Rekapitulasi Kebutuhan Air')
@section('doc-meta')
    Tahun: {{ $tahun }} · Total Data: {{ $data->count() }} hari · {{ $rekapBulanan->count() }} bulan
@endsection

@section('content')
{{-- Stat tahunan --}}
<div class="stat-row">
    <div class="stat-box">
        <div class="stat-box-label">Rata-rata ETo</div>
        <div class="stat-box-val">{{ number_format($data->avg('eto'),2) }} <span class="stat-box-unit">mm/hari</span></div>
    </div>
    <div class="stat-box">
        <div class="stat-box-label">Rata-rata ETc</div>
        <div class="stat-box-val">{{ number_format($data->avg('etc'),2) }} <span class="stat-box-unit">mm/hari</span></div>
    </div>
    <div class="stat-box">
        <div class="stat-box-label">Rata-rata Kebutuhan Air</div>
        <div class="stat-box-val">{{ number_format($data->avg('kebutuhan_air'),2) }} <span class="stat-box-unit">mm/hari</span></div>
    </div>
    <div class="stat-box">
        <div class="stat-box-label">Total Curah Hujan</div>
        <div class="stat-box-val">{{ number_format($data->sum('curah_hujan'),0) }} <span class="stat-box-unit">mm/tahun</span></div>
    </div>
</div>

<table>
    <thead>
        <tr>
            <th>Bulan</th>
            <th>Rata-rata ETo (mm)</th>
            <th>Rata-rata ETc (mm)</th>
            <th>Rata-rata Kebutuhan (mm)</th>
            <th>Max Kebutuhan (mm)</th>
            <th>Min Kebutuhan (mm)</th>
            <th>Total Hujan (mm)</th>
            <th>Jumlah Data</th>
        </tr>
    </thead>
    <tbody>
        @foreach($rekapBulanan as $row)
        <tr>
            <td style="font-weight:bold;">{{ $row['bulan'] }}</td>
            <td style="text-align:center;"><span class="badge badge-water">{{ $row['avg_eto'] }}</span></td>
            <td style="text-align:center;"><span class="badge badge-water">{{ $row['avg_etc'] }}</span></td>
            <td style="text-align:center;"><span class="badge {{ $row['avg_kebutuhan'] > 5 ? 'badge-red' : ($row['avg_kebutuhan'] > 3 ? 'badge-clay' : 'badge-leaf') }}">{{ $row['avg_kebutuhan'] }}</span></td>
            <td style="text-align:center;">{{ $row['max_kebutuhan'] }}</td>
            <td style="text-align:center;">{{ $row['min_kebutuhan'] }}</td>
            <td style="text-align:center;">{{ $row['total_hujan'] }}</td>
            <td style="text-align:center;">{{ $row['jumlah_data'] }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr style="background:#f5ede0;">
            <td style="font-weight:bold;text-align:right;">TOTAL / RATA-RATA</td>
            <td style="text-align:center;font-weight:bold;">{{ number_format($rekapBulanan->avg('avg_eto'),2) }}</td>
            <td style="text-align:center;font-weight:bold;">{{ number_format($rekapBulanan->avg('avg_etc'),2) }}</td>
            <td style="text-align:center;font-weight:bold;">{{ number_format($rekapBulanan->avg('avg_kebutuhan'),2) }}</td>
            <td style="text-align:center;font-weight:bold;">{{ number_format($rekapBulanan->max('max_kebutuhan'),2) }}</td>
            <td style="text-align:center;font-weight:bold;">{{ number_format($rekapBulanan->min('min_kebutuhan'),2) }}</td>
            <td style="text-align:center;font-weight:bold;">{{ number_format($rekapBulanan->sum('total_hujan'),1) }}</td>
            <td style="text-align:center;font-weight:bold;">{{ $rekapBulanan->sum('jumlah_data') }}</td>
        </tr>
    </tfoot>
</table>
@endsection
