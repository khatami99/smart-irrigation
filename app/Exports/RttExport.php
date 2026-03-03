<?php
// app/Exports/RttExport.php

namespace App\Exports;

use App\Models\Rtt;
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

class RttExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize
{
    public function __construct(private ?int $mtId = null) {}

    public function collection()
    {
        $mt = $this->mtId ? MusimTanam::find($this->mtId) : MusimTanam::berjalan()->first();
        return Rtt::with(['petak', 'musimTanam'])
            ->when($mt, fn($q) => $q->where('musim_tanam_id', $mt->id))
            ->orderBy('urutan_rotasi')->get();
    }

    public function headings(): array
    {
        return ['Rotasi', 'Kode Petak', 'Nama Petak', 'Luas Area (ha)',
                'Rencana Mulai', 'Rencana Selesai', 'Durasi (hari)',
                'Realisasi Mulai', 'Realisasi Selesai',
                'Target Luas (ha)', 'Realisasi Luas (ha)', 'Efisiensi (%)',
                'Durasi Air (hari)', 'Status', 'Keterangan'];
    }

    public function map($row): array
    {
        return [
            $row->urutan_rotasi,
            $row->petak->kode_petak ?? '-',
            $row->petak->nama_petak ?? '-',
            $row->petak->luas_area ?? '-',
            $row->rencana_mulai_tanam->format('d/m/Y'),
            $row->rencana_selesai_tanam->format('d/m/Y'),
            $row->durasi_rencana,
            $row->realisasi_mulai_tanam?->format('d/m/Y') ?? '-',
            $row->realisasi_selesai_tanam?->format('d/m/Y') ?? '-',
            $row->target_luas,
            $row->realisasi_luas ?? '-',
            $row->efisiensi_luas ? $row->efisiensi_luas . '%' : '-',
            $row->durasi_pemberian_air,
            ucfirst($row->status),
            $row->keterangan ?? '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                  'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '5a7a47']],
                  'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]],
        ];
    }

    public function title(): string { return 'RTT'; }
}
