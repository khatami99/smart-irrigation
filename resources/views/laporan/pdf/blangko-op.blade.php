@extends('laporan.pdf.layout')

@section('doc-title', 'Laporan Blangko OP Per Dekade')
@section('doc-meta')
    Musim Tanam: {{ $mt?->nama_mt ?? 'Semua' }}
    @if($mt) · {{ $mt->tanggal_mulai->format('d M Y') }} — {{ $mt->tanggal_selesai->format('d M Y') }} @endif
    · Total: {{ $data->count() }} catatan
@endsection

@section('content')
@php
    $bulanNama = ['','Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
    $dekadeLabel = ['I','II','III'];
@endphp
<table>
    <thead>
        <tr>
            <th>No</th><th>Petak</th><th>Periode</th><th>Fase</th>
            <th>Debit Rencana</th><th>Debit Realisasi</th><th>Efisiensi</th>
            <th>Luas Rencana</th><th>Luas Realisasi</th>
            <th>TMA (cm)</th><th>Hujan (mm)</th><th>Kondisi</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $i => $row)
        <tr>
            <td style="text-align:center;">{{ $i+1 }}</td>
            <td><strong>{{ $row->petak->kode_petak }}</strong><br><small>{{ $row->petak->nama_petak }}</small></td>
            <td style="text-align:center;">Dek.{{ $row->dekade }}<br>{{ $bulanNama[(int)$row->bulan] }} {{ $row->tahun }}</td>
            <td>{{ ucfirst(str_replace('_',' ',$row->fase_pertumbuhan ?? '-')) }}</td>
            <td style="text-align:center;">{{ $row->debit_rencana }}</td>
            <td style="text-align:center;">{{ $row->debit_realisasi }}</td>
            <td style="text-align:center;">
                @if($row->debit_rencana > 0)
                @php $ef = round((float)$row->debit_realisasi/(float)$row->debit_rencana*100,1); @endphp
                <span class="badge {{ $ef >= 80 ? 'badge-leaf' : 'badge-red' }}">{{ $ef }}%</span>
                @endif
            </td>
            <td style="text-align:center;">{{ $row->luas_rencana }}</td>
            <td style="text-align:center;">{{ $row->luas_realisasi }}</td>
            <td style="text-align:center;">{{ $row->tinggi_muka_air }}</td>
            <td style="text-align:center;">{{ $row->curah_hujan }}</td>
            <td style="text-align:center;"><span class="badge {{ $row->kondisi_saluran === 'baik' ? 'badge-leaf' : 'badge-clay' }}">{{ ucfirst(str_replace('_',' ',$row->kondisi_saluran??'-')) }}</span></td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
