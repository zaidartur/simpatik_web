<?php

namespace App\Exports;

use App\Models\Outbox;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AgendaKeluarExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithTitle
{
    protected ?string $startDate;
    protected ?string $endDate;

    public function __construct(?string $startDate = null, ?string $endDate = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        $query = Outbox::with(['sifat', 'klasifikasi', 'pengolah'])
            ->whereNull('on_delete');

        if ($this->startDate && $this->endDate) {
            $query->whereBetween('tgl_surat', [
                Carbon::parse($this->startDate)->format('Y-m-d'),
                Carbon::parse($this->endDate)->format('Y-m-d'),
            ]);
        }

        return $query->orderBy('no_agenda', 'asc')->get();
    }

    public function headings(): array
    {
        return [
            'No. Agenda',
            'Tahun',
            'No. Surat',
            'Tgl. Surat',
            'Tgl. Naik',
            'Tgl. Diteruskan',
            'Kepada (Tujuan)',
            'Wilayah',
            'Perihal',
            'Unit Pengolah',
            'Sifat Surat',
            'Klasifikasi',
        ];
    }

    public function map($outbox): array
    {
        return [
            $outbox->no_agenda,
            $outbox->year,
            $outbox->no_surat ?: '-',
            $outbox->tgl_surat ? Carbon::parse($outbox->tgl_surat)->format('d/m/Y') : '-',
            $outbox->tgl_naik ? Carbon::parse($outbox->tgl_naik)->format('d/m/Y') : '-',
            $outbox->tgl_diteruskan ? Carbon::parse($outbox->tgl_diteruskan)->format('d/m/Y') : '-',
            $outbox->kepada,
            $outbox->wilayah ?: '-',
            $outbox->perihal,
            $outbox->pengolah->nama ?? ($outbox->unit ?: '-'),
            $outbox->sifat->nama_sifat ?? '-',
            $outbox->klasifikasi->klas3 ?? '-',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $sheet->getStyle('A1:L1')->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('A1:L1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('00AB55');
        $sheet->getStyle('A1:L1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function title(): string
    {
        return 'Agenda Surat Keluar';
    }
}
