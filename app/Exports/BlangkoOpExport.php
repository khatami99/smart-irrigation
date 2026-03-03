<?php
// app/Exports/BlangkoOpExport.php

namespace App\Exports;

use App\Models\BlangkoOp;
use App\Models\MusimTanam;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class BlangkoOpExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize
{
    public function __construct(private ?int $mtId = null) {}

    public function collection()
    {
        $mt = $this->mtId ? MusimTanam::find($this->mtId) : MusimTanam::berjalan()->first();
        return BlangkoOp::with(['petak', 'musimTanam'])
            ->when($mt, fn($q) => $q->where('musim_tanam_id', $mt->id))
            ->orderBy('tahun')->orderBy('bulan')->orderBy('dekade')->get();
    }

    public function headings(): array
    {
        return ['No', 'Petak', 'Nama Petak', 'Tahun', 'Bulan', 'Dekade',
                'Fase Pertumbuhan', 'Debit Rencana (l/det)', 'Debit Realisasi (l/det)',
                'Luas Rencana (ha)', 'Luas Realisasi (ha)', 'TMA (cm)',
                'Curah Hujan (mm)', 'Kondisi Saluran', 'Keterangan'];
    }

    public function map($row): array
    {
        static $no = 0;
        $no++;
        $bulanNama = ['','Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
        $dekadeLabel = ['I (1-10)', 'II (11-20)', 'III (21-31)'];
        return [
            $no,
            $row->petak->kode_petak ?? '-',
            $row->petak->nama_petak ?? '-',
            $row->tahun,
            $bulanNama[(int)$row->bulan] ?? $row->bulan,
            $row->dekade,
            ucfirst(str_replace('_', ' ', $row->fase_pertumbuhan ?? '-')),
            $row->debit_rencana,
            $row->debit_realisasi,
            $row->luas_rencana,
            $row->luas_realisasi,
            $row->tinggi_muka_air,
            $row->curah_hujan,
            ucfirst(str_replace('_', ' ', $row->kondisi_saluran ?? '-')),
            $row->keterangan ?? '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                  'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4a7c6f']],
                  'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]],
        ];
    }

    public function title(): string { return 'Blangko OP'; }
}
