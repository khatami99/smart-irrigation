@extends('laporan.pdf.layout')

@section('doc-title', 'Laporan Data Iklim Harian')
@section('doc-meta')
    Tahun: {{ $tahun }}
    @if($bulan)
        · Bulan: {{ ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'][$bulan] }}
    @endif
    · Total: {{ $data->count() }} data
@endsection

@section('content')
{{-- Stat ringkasan --}}
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
        <div class="stat-box-val">{{ number_format($data->sum('curah_hujan'),1) }} <span class="stat-box-unit">mm</span></div>
    </div>
</div>

<table>
    <thead>
        <tr>
            <th>No</th><th>Tanggal</th><th>Suhu Max</th><th>Suhu Min</th>
            <th>Kelembaban</th><th>Angin</th><th>Radiasi</th>
            <th>Curah Hujan</th><th>Kc</th><th>ETo</th><th>ETc</th><th>Kebutuhan Air</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $i => $row)
        <tr>
            <td style="text-align:center;">{{ $i+1 }}</td>
            <td>{{ \Carbon\Carbon::parse($row->tanggal)->format('d/m/Y') }}</td>
            <td style="text-align:center;">{{ $row->suhu_max }}°C</td>
            <td style="text-align:center;">{{ $row->suhu_min }}°C</td>
            <td style="text-align:center;">{{ $row->kelembaban }}%</td>
            <td style="text-align:center;">{{ $row->kecepatan_angin }}</td>
            <td style="text-align:center;">{{ $row->radiasi_matahari }}</td>
            <td style="text-align:center;">{{ $row->curah_hujan }}</td>
            <td style="text-align:center;">{{ $row->kc }}</td>
            <td style="text-align:center;"><span class="badge badge-water">{{ $row->eto }}</span></td>
            <td style="text-align:center;"><span class="badge badge-water">{{ $row->etc }}</span></td>
            <td style="text-align:center;"><span class="badge {{ $row->kebutuhan_air > 5 ? 'badge-red' : ($row->kebutuhan_air > 3 ? 'badge-clay' : 'badge-leaf') }}">{{ $row->kebutuhan_air }} mm</span></td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
